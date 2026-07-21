<?php

namespace EphenyxDigital\QuantumCore;

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

    /** @var mixed Donnée brute de debug (ex: match trouvé, score détaillé) — jamais affichée telle quelle */
    public $raw;

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
