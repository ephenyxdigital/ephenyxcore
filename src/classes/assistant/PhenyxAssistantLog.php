<?php

namespace EphenyxDigital\QuantumCore;

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
            $log->controller_name = $query->controllerName;
            $log->entity_class = $query->entityClass;
            $log->question = $query->text;
            $log->answer = $answer->text;
            $log->source_plugin = $answer->sourcePlugin;
            $log->provider = $providerName;
            $log->confidence = $answer->confidence;
            $log->candidate_count = (int) $candidateCount;
            $log->date_add = date('Y-m-d H:i:s');

            return (bool) $log->add(false);
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantLog::record a échoué (%s)', $e->getMessage()),
                2,
                null,
                static::class
            );

            return false;
        }

    }

}
