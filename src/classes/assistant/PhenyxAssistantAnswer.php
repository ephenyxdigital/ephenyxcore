<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistantAnswer
 *
 * Value-object représentant une réponse candidate (produite par une couche de
 * plugin) ou finale (produite par le provider) de l'assistant back-office.
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantAnswer {

    /** @var string Texte de la réponse, prêt à afficher (HTML simple autorisé) */
    public $text;

    /**
     * Score de confiance 0..1. Sert au provider par défaut (sans LLM) à choisir
     * la meilleure réponse candidate ; sera aussi utile à un futur LLM pour
     * pondérer les sources qu'on lui donne en contexte (RAG).
     *
     * @var float
     */
    public $confidence = 0.0;

    /** @var string|null Nom du plugin ayant produit la réponse (null = coeur/provider) */
    public $sourcePlugin;

    /**
     * Action suggérée, exploitable par le widget front du chatbot pour proposer
     * un bouton d'action plutôt qu'un simple texte. Format libre mais la
     * convention recommandée est :
     * ['type' => 'openTargetController', 'controller' => 'AdminProducts', 'params' => [...]]
     * qui correspond directement à ce que consomme déjà
     * PhenyxController::ajaxProcessOpenTargetController côté BO.
     *
     * @var array|null
     */
    public $suggestedAction;

    /** @var string[] Suggestions de questions de suivi (chips/quick replies) */
    public $quickReplies = [];

    /**
     * Candidats en lice quand l'assistant préfère demander une précision plutôt
     * que de trancher au hasard (ÉTAGE 4, cf.
     * PhenyxAssistantLayer::buildAmbiguityAnswer()).
     *
     * Forme : [['idTopic' => int, 'label' => string], ...]
     *
     * Le widget en fait des puces "Did you mean ... ?" ; le clic renvoie
     * l'idTopic choisi à AdminAssistantController::ajaxProcessResolve(), qui
     * écrit l'alias correspondant — le geste de désambiguïsation EST le signal
     * d'apprentissage.
     *
     * @var array
     */
    public $ambiguousChoices = [];

    /**
     * Puces qui désignent un topic PAR SON ID, et non par un libellé à
     * re-matcher.
     *
     * Forme : [['idTopic' => int, 'label' => string], ...]
     *
     * ⚠️ Différence essentielle avec $quickReplies : une quick reply est un
     * LIBELLÉ renvoyé au serveur comme une nouvelle question, qui repasse donc
     * par le scoring. Ça convient à une question rédigée exprès pour matcher son
     * topic, mais pas à une puce GÉNÉRÉE — le tour dérivé du menu affiche le nom
     * de la famille (« Nos produits »), qui n'a aucune raison de recouper les
     * mots-clés du topic correspondant. Toute la fragilité qu'on cherche à
     * éliminer reviendrait par cette porte.
     *
     * Ici le clic renvoie l'id : la résolution est exacte, sans matching. C'est
     * la primitive à utiliser pour toute liste de puces construite par le code
     * plutôt que rédigée à la main.
     *
     * Distinct aussi de $ambiguousChoices, qui a la même forme mais une autre
     * sémantique : celui-là APPREND un alias au clic (l'employé tranche une
     * ambiguïté), celui-ci navigue simplement.
     *
     * @var array
     */
    public $topicChips = [];

    /**
     * Code du topic ayant produit cette réponse (ex. 'core.preferences.list').
     *
     * C'est le CONTRAT offert aux plugins qui veulent enrichir une réponse du
     * cœur via le hook actionAssistantAnswerAugment : ils testent ce code, jamais
     * $idTopic. Un `code` est stable, lisible et versionnable ; un id dépend de
     * l'ordre d'insertion en base.
     *
     * @var string|null
     */
    public $topicCode;

    /**
     * Topic ayant produit cette réponse (null pour un repli du provider, qui ne
     * vient d'aucun topic). Nécessaire au log (eph_phenyx_assistant_log.id_topic)
     * et, plus tard, à l'écriture d'un alias appris depuis le clic de l'employé.
     *
     * @var int|null
     */
    public $idTopic;

    /**
     * Métadonnées de matching, JAMAIS affichées telles quelles.
     *
     * Convention (renseignée par PhenyxAssistantLayer::findBestTopic(), lue par
     * PhenyxAssistantRuleProvider::generate() pour arbitrer et par
     * PhenyxAssistantLog::record() pour tracer) :
     *
     *   [
     *     'stage'         => PhenyxAssistantLayer::STAGE_* ('screen'|'alias'|'keyword'|'ambiguous'|'none'),
     *     'score'         => float, // score de recoupement réel, PAS $confidence
     *     'idTopic'       => int|null,
     *     'runnerUpTopic' => int|null, // second candidat, pour mesurer l'écart
     *     'runnerUpScore' => float|null,
     *   ]
     *
     * Distinction importante avec $confidence : $confidence est une constante
     * saisie par l'auteur du topic (colonne `confidence`), 'score' est ce que
     * cette question-là a réellement produit. C'est 'score' qui doit arbitrer
     * entre couches — cf. le docblock de generate() dans
     * PhenyxAssistantRuleProvider.
     *
     * @var mixed
     */
    public $raw;

    /**
     * Rang d'un étage de résolution : plus haut = plus exact. Sert à l'arbitrage
     * inter-couches (une résolution exacte bat toujours une approximation, quel
     * que soit le plugin qui la produit et quel que soit son `confidence`).
     *
     * Une réponse sans métadonnées (couche qui n'a pas renseigné $raw, ou repli
     * du provider) retombe au rang le plus bas plutôt que de bénéficier du
     * doute : c'est volontaire, sinon une couche pourrait gagner en ne
     * renseignant rien.
     *
     * @param string|null $stage
     * @return int
     */
    public static function stageRank($stage) {

        $ranks = [
            PhenyxAssistantLayer::STAGE_SCREEN    => 40,
            PhenyxAssistantLayer::STAGE_ALIAS     => 30,
            PhenyxAssistantLayer::STAGE_KEYWORD   => 20,
            PhenyxAssistantLayer::STAGE_AMBIGUOUS => 10,
            PhenyxAssistantLayer::STAGE_NONE      => 0,
        ];

        return isset($ranks[$stage]) ? $ranks[$stage] : 0;
    }

    /**
     * Accès défensif aux métadonnées : $raw est de type mixte par contrat
     * historique (une couche tierce peut y avoir mis n'importe quoi), donc
     * jamais déréférencé directement.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getMatchMeta($key, $default = null) {

        if (!is_array($this->raw) || !array_key_exists($key, $this->raw)) {
            return $default;
        }

        return $this->raw[$key];
    }

    public function __construct($text = '', $confidence = 0.0, $sourcePlugin = null) {

        $this->text = $text;
        $this->confidence = (float) $confidence;
        $this->sourcePlugin = $sourcePlugin;
    }

    /**
     * Réponse "je ne sais pas" standard, utilisée comme filet de sécurité par
     * le provider par défaut quand aucune couche de plugin n'a répondu.
     *
     * @return static
     */
    public static function none() {

        $answer = new static();
        $answer->text = '';
        $answer->confidence = 0.0;

        return $answer;
    }

    public static function text($text, $sourcePlugin = null, $confidence = 1.0) {

        return new static($text, $confidence, $sourcePlugin);
    }

    public function isEmpty() {

        return $this->text === null || trim((string) $this->text) === '';
    }

}
