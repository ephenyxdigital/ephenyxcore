<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Class PhenyxAssistantRuleProvider
 *
 * Provider par défaut, sans aucune dépendance externe : il se contente de
 * choisir la réponse candidate la plus confiante parmi celles remontées par
 * les couches de plugins (PhenyxAssistantLayer::answer() / hook
 * actionAssistantAnswer). Aucune génération de texte, aucun appel réseau.
 *
 * Sert de filet de sécurité permanent (même une fois un LLM branché, on peut
 * garder ce provider comme fallback si le LLM est indisponible) et de
 * baseline pour évaluer objectivement l'apport d'un futur LLM.
 *
 * @since 1.0.0 (assistant BO)
 */
class PhenyxAssistantRuleProvider implements PhenyxAssistantProviderInterface {

    public function generate(PhenyxAssistantQuery $query, array $candidates, array $context) {

        $best = null;

        foreach ($candidates as $candidate) {

            if (!($candidate instanceof PhenyxAssistantAnswer) || $candidate->isEmpty()) {
                continue;
            }

            if ($best === null || $candidate->confidence > $best->confidence) {
                $best = $candidate;
            }

        }

        if ($best !== null) {
            return $best;
        }

        $fallback = new PhenyxAssistantAnswer(
            $this->getFallbackMessage($query, $context),
            0.0,
            null
        );

        return $fallback;
    }

    public function getName() {

        return 'rule';
    }

    /**
     * Message par défaut quand aucune couche de plugin n'a su répondre.
     * Volontairement transparent sur les limites actuelles plutôt que
     * d'inventer une réponse (pas de LLM = pas d'hallucination possible ici).
     *
     * @param PhenyxAssistantQuery $query
     * @param array                $context
     * @return string
     */
    protected function getFallbackMessage(PhenyxAssistantQuery $query, array $context) {

        if (!empty($context['controller']['name'])) {
            return sprintf(
                'Je ne trouve pas encore de réponse pour "%s" sur l\'écran %s. Aucun plugin n\'a de couche assistant branchée dessus pour l\'instant.',
                $query->text,
                $context['controller']['name']
            );
        }

        return 'Je ne sais pas encore répondre à cette question — aucune couche d\'assistant n\'est encore branchée sur ce contrôleur.';
    }

}
