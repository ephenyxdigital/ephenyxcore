<?php

namespace EphenyxDigital\EphenyxCore;

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

    /**
     * Arbitre entre les réponses candidates des différentes couches.
     *
     * ⚠️ Cette méthode choisissait à l'origine le candidat au `confidence` le
     * plus élevé. C'était un bug latent, invisible tant qu'une seule couche
     * était enregistrée (PhenyxAssistantCoreLayer), mais qui se serait déclenché
     * dès la première couche de plugin : `confidence` est la colonne STATIQUE du
     * topic (`decimal(3,2)`, défaut 0.80), saisie à la main dans
     * install_assistant_topics.php ou l'écran CRUD — donc totalement
     * indépendante de la qualité du match. Un topic ph_ecommerce saisi à 0.90
     * recoupant UN seul mot-clé faible aurait battu un topic core à 0.80
     * recoupant QUATRE mots-clés distincts, et chaque plugin aurait été incité à
     * surenchérir sur son `confidence` pour être entendu.
     *
     * Ordre d'arbitrage retenu :
     *  1. l'étage de résolution (cf. PhenyxAssistantAnswer::stageRank()) — une
     *     résolution exacte bat toujours une approximation, quel que soit le
     *     plugin qui la produit ;
     *  2. à étage égal, le score de matching réel ($raw['score']) ;
     *  3. à score égal, la couche du plugin propriétaire de l'écran courant —
     *     c'est le seul endroit où l'ancrage sur l'écran doit jouer : en
     *     TIE-BREAK, jamais en bonus de score, sinon on masquerait une bonne
     *     réponse générale au profit d'une réponse locale médiocre ;
     *  4. en dernier recours seulement, `confidence`.
     *
     * @param PhenyxAssistantQuery $query
     * @param PhenyxAssistantAnswer[] $candidates
     * @param array $context Cf. PhenyxAssistant::buildContextSnapshot()
     * @return PhenyxAssistantAnswer
     */
    public function generate(PhenyxAssistantQuery $query, array $candidates, array $context) {

        $currentPlugin = isset($context['controller']['plugin']) ? $context['controller']['plugin'] : null;

        $best = null;
        $bestRank = null;

        foreach ($candidates as $candidate) {

            if (!($candidate instanceof PhenyxAssistantAnswer) || $candidate->isEmpty()) {
                continue;
            }

            $rank = $this->rankOf($candidate, $currentPlugin);

            if ($best === null || $this->outranks($rank, $bestRank)) {
                $best = $candidate;
                $bestRank = $rank;
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

        // Le repli est lui aussi tracé : sans ça, une question sans réponse
        // n'apparaîtrait nulle part dans le log alors que c'est précisément la
        // catégorie la plus intéressante à relire (manques du wiki). Le score du
        // meilleur "presque-match" est repris de la couche qui a le plus
        // approché (cf. PhenyxAssistant::collectCandidates()), s'il y en a un.
        $nearMiss = isset($context['nearMissMeta']) && is_array($context['nearMissMeta'])
        ? $context['nearMissMeta']
        : [];

        $fallback->raw = [
            // Toujours 'none' : même si une couche a frôlé le seuil, aucune
            // réponse n'a été retenue — c'est bien un échec, pas un match faible.
            'stage'         => PhenyxAssistantLayer::STAGE_NONE,
            'score'         => isset($nearMiss['score']) ? $nearMiss['score'] : null,
            'idTopic'       => null,
            'runnerUpTopic' => isset($nearMiss['runnerUpTopic']) ? $nearMiss['runnerUpTopic'] : null,
            'runnerUpScore' => isset($nearMiss['runnerUpScore']) ? $nearMiss['runnerUpScore'] : null,
        ];

        return $fallback;
    }

    /**
     * Compare deux tuples de rang produits par rankOf() : le premier critère qui
     * diffère tranche, dans l'ordre 1..4 documenté sur generate().
     *
     * Écrit explicitement plutôt qu'en s'appuyant sur l'opérateur `>` appliqué à
     * deux tableaux PHP : le résultat serait le même, mais la sémantique de
     * comparaison de tableaux est un coin obscur du langage (et devient
     * "non comparable" dès que les clés diffèrent). Une boucle explicite reste
     * lisible et ne réserve aucune surprise à qui ajoutera un cinquième critère.
     *
     * @param array $rank
     * @param array $against
     * @return bool true si $rank est strictement meilleur que $against
     */
    protected function outranks(array $rank, array $against) {

        foreach ($rank as $i => $value) {

            $other = isset($against[$i]) ? $against[$i] : 0;

            if ($value == $other) {
                continue;
            }

            return $value > $other;
        }

        // Égalité parfaite sur tous les critères : on garde le candidat déjà
        // retenu (premier arrivé). Stable, et identique au comportement d'avant
        // ce correctif.
        return false;
    }

    /**
     * Tuple de rang d'un candidat, du critère le plus fort au plus faible.
     * Cf. outranks() pour la comparaison.
     *
     * @param PhenyxAssistantAnswer $candidate
     * @param string|null           $currentPlugin Plugin propriétaire de l'écran courant
     * @return array
     */
    protected function rankOf(PhenyxAssistantAnswer $candidate, $currentPlugin) {

        $stage = $candidate->getMatchMeta('stage', PhenyxAssistantLayer::STAGE_NONE);
        $score = (float) $candidate->getMatchMeta('score', 0.0);

        // Couche du plugin qui possède l'écran courant : 1, sinon 0. Le coeur
        // (sourcePlugin null) n'est jamais "propriétaire" d'un écran de plugin.
        $ownsScreen = ($currentPlugin && $candidate->sourcePlugin === $currentPlugin) ? 1 : 0;

        return [
            PhenyxAssistantAnswer::stageRank($stage),
            $score,
            $ownsScreen,
            (float) $candidate->confidence,
        ];
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
