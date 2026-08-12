<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistantLog
 *
 * PhenyxObjectModel classique pour la table eph_phenyx_assistant_log (cf.
 * sql/phenyx_assistant.sql). Journalise chaque échange question/réponse de
 * l'assistant BO : sert au débogage immédiat, et deviendra la matière première
 * (paires Q/R réelles + contexte) pour évaluer les futurs providers LLM.
 *
 * Suit exactement le pattern $definition des autres classes du framework
 * (cf. PhenyxObjectModel::getDefinition(), lu par Reflection) — pas de
 * particularité ici, volontairement, pour rester un citoyen de première
 * classe de l'ORM (utilisable avec add()/update()/getFields() etc. sans rien
 * de spécial).
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantLog extends PhenyxObjectModel {

    public $id_employee;

    public $controller_name;

    public $entity_class;

    public $question;

    public $answer;

    public $source_plugin;

    public $provider;

    public $confidence;

    public $candidate_count;

    /** @var int|null Topic retenu (cf. PhenyxAssistantAnswer::$idTopic) */
    public $id_topic;

    /** @var string|null Étage qui a répondu, cf. PhenyxAssistantLayer::STAGE_* */
    public $match_stage;

    /** @var float|null Score de recoupement réel — à ne pas confondre avec $confidence */
    public $match_score;

    /** @var int|null Second meilleur topic, pour mesurer l'écart */
    public $runner_up_topic;

    /** @var float|null */
    public $runner_up_score;

    /** @var bool Round-trip automatique à l'ouverture d'écran, ou vraie question tapée */
    public $is_contextual = false;

    public $date_add;

    public static $definition = [
        'table'      => 'phenyx_assistant_log',
        'primary'    => 'id_phenyx_assistant_log',
        'multilang'  => false,
        'have_meta'  => false,
        'fields'     => [
            'id_employee'     => ['type' => self::TYPE_INT],
            'controller_name' => ['type' => self::TYPE_STRING, 'size' => 64],
            'entity_class'    => ['type' => self::TYPE_STRING, 'size' => 64],
            'question'        => ['type' => self::TYPE_STRING, 'size' => 2000, 'required' => true],
            'answer'          => ['type' => self::TYPE_HTML],
            'source_plugin'   => ['type' => self::TYPE_STRING, 'size' => 64],
            'provider'        => ['type' => self::TYPE_STRING, 'size' => 64],
            'confidence'      => ['type' => self::TYPE_FLOAT],
            'candidate_count' => ['type' => self::TYPE_INT],

            /*
             * Observabilité du matching (ajouté le 2026-07-25). Sans ces
             * colonnes, diagnostiquer un faux-positif imposait de rejouer le
             * scoring à la main depuis une capture d'écran — cf. les trois
             * correctifs des 22, 23 et 24/07/2026.
             * REQUIERT sql/phenyx_assistant_log_upgrade.sql sur la base
             * BOUTIQUE de chaque site (pas la base CRM).
             */
            'id_topic'        => ['type' => self::TYPE_INT],
            'match_stage'     => ['type' => self::TYPE_STRING, 'size' => 16],
            'match_score'     => ['type' => self::TYPE_FLOAT],
            'runner_up_topic' => ['type' => self::TYPE_INT],
            'runner_up_score' => ['type' => self::TYPE_FLOAT],
            'is_contextual'   => ['type' => self::TYPE_BOOL],

            'date_add'        => ['type' => self::TYPE_DATE],
        ],
    ];

    /**
     * Petit raccourci pour journaliser un échange sans exposer le détail de
     * l'ORM aux appelants (PhenyxAssistant::ask() principalement).
     * Volontairement "best effort" : un échec de log ne doit jamais faire
     * planter une réponse déjà produite pour l'employé.
     *
     * @param PhenyxAssistantQuery  $query
     * @param PhenyxAssistantAnswer $answer
     * @param string                $providerName
     * @param int                   $candidateCount
     * @return bool
     */
    public static function record(PhenyxAssistantQuery $query, PhenyxAssistantAnswer $answer, $providerName, $candidateCount = 0) {

        try {
            $log = new static();
            $log->id_employee = $query->idEmployee ?: null;
            /*
             * ⚠️ L'ECRAN DE L'EMPLOYE, PAS LE NOTRE.
             *
             * $query->controllerName vaut « AdminAssistant » — le controleur
             * qui traite la requete ajax. Journaliser cela remplissait la
             * colonne d'une seule et meme valeur sur TOUTES les lignes, ce qui
             * rendait inexploitable la question la plus utile qu'on puisse
             * poser a ce journal : « depuis quels ecrans les employes
             * demandent-ils de l'aide, et pour quoi ? ».
             *
             * Corrige le 2026-08-04, en meme temps que l'instantane de contexte
             * de PhenyxAssistant::buildContextSnapshot().
             *
             * ⚠️ Les lignes deja ecrites gardent l'ancienne valeur : une
             * statistique qui remonte avant cette date melange les deux.
             */
            $log->controller_name = !empty($query->extra['sourceController'])
            ? (string) $query->extra['sourceController']
            : $query->controllerName;
            $log->entity_class = $query->entityClass;
            $log->question = $query->text;
            $log->answer = $answer->text;
            $log->source_plugin = $answer->sourcePlugin;
            $log->provider = $providerName;
            $log->confidence = $answer->confidence;
            $log->candidate_count = (int) $candidateCount;

            // Métadonnées de matching : cf. la convention de
            // PhenyxAssistantAnswer::$raw. Lues via getMatchMeta() plutôt qu'en
            // déréférençant $raw, qui reste de type mixte par contrat (une
            // couche tierce peut y avoir mis autre chose).
            $log->id_topic = $answer->idTopic ?: $answer->getMatchMeta('idTopic');
            $log->match_stage = $answer->getMatchMeta('stage');
            $log->match_score = $answer->getMatchMeta('score');
            $log->runner_up_topic = $answer->getMatchMeta('runnerUpTopic');
            $log->runner_up_score = $answer->getMatchMeta('runnerUpScore');
            $log->is_contextual = !empty($query->extra['isContextual']);

            $log->date_add = date('Y-m-d H:i:s');

            return (bool) $log->add(false);
        } catch (\Throwable $e) {
            // Message volontairement explicite sur la cause la plus probable :
            // le paquet vendor est partagé entre plusieurs sites, donc déployer
            // ce fichier sans avoir passé l'ALTER TABLE sur la base boutique du
            // site concerné fait échouer TOUS les inserts de log (silencieusement
            // du point de vue de l'employé, ce log étant "best effort").
            PhenyxLogger::addLog(
                sprintf(
                    'PhenyxAssistantLog::record a échoué (%s) — vérifier que sql/phenyx_assistant_log_upgrade.sql a bien été exécuté sur la base boutique de ce site.',
                    $e->getMessage()
                ),
                2,
                null,
                static::class
            );

            return false;
        }

    }

}
