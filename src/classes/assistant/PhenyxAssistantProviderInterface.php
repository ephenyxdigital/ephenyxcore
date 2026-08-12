<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Interface PhenyxAssistantProviderInterface
 *
 * C'est LE point de bascule du projet : tout ce qui précède (PhenyxAssistant,
 * PhenyxAssistantLayer, les hooks actionAssistantAnswer) reste identique qu'on
 * réponde avec un moteur de règles basique ou avec un LLM auto-hébergé sur une
 * machine dédiée. Seule l'implémentation de generate() change.
 *
 * Roadmap envisagée (à affiner au fur et à mesure, cf. discussion 2026-07) :
 *  - v0 : PhenyxAssistantRuleProvider (ci-joint), zéro dépendance, répond avec
 *    la meilleure candidate remontée par les couches de plugins.
 *  - v1 : un provider HTTP appelant un LLM local (Ollama / vLLM / llama.cpp)
 *    tournant sur une machine avec GPU dédiée, prompté avec $context (RAG léger
 *    basé sur PhenyxObjectModel::getDefinition() + back_tab).
 *  - v2 : fine-tuning / distillation sur les logs réels de
 *    eph_phenyx_assistant_log une fois qu'on a assez de volume.
 *
 * @since 1.0.0 (assistant BO)
 */
interface PhenyxAssistantProviderInterface {

    /**
     * Produit la réponse finale à partir de la question, des réponses
     * candidates remontées par les couches de plugins (via hooks ou registre
     * en mémoire) et d'un instantané de contexte BO (cf.
     * PhenyxAssistant::buildContextSnapshot()).
     *
     * @param PhenyxAssistantQuery   $query      Question + contexte de l'employé
     * @param PhenyxAssistantAnswer[] $candidates Réponses candidates (peut être vide)
     * @param array                  $context    Instantané de contexte BO (grounding)
     * @return PhenyxAssistantAnswer
     */
    public function generate(PhenyxAssistantQuery $query, array $candidates, array $context);

    /**
     * Identifiant court du provider, journalisé dans eph_phenyx_assistant_log
     * (colonne `provider`) pour pouvoir comparer les générations de moteurs
     * entre elles a posteriori.
     *
     * @return string
     */
    public function getName();

}
