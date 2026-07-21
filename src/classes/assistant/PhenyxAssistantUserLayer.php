<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Class PhenyxAssistantUserLayer
 *
 * Première couche "core" de l'assistant BO : contrairement à une couche de
 * plugin (cf. PhEcommerceAssistantLayer en exemple dans le README du
 * dossier), celle-ci ne dépend d'aucun plugin. Elle documente une
 * fonctionnalité native du framework : la gestion des clients, portée par
 * la classe UserCore (table `user`) et le contrôleur natif AdminUsers
 * (includes/controllers/backend/AdminUsersController.php).
 *
 * Le contenu (questions/réponses/quick replies) vit en base — table
 * eph_phenyx_assistant_topic(_lang), cf. sql/phenyx_assistant_topic.sql et
 * install_assistant_topics.php pour le jeu de données initial (FR/EN/DE) —
 * plutôt qu'en constantes PHP : ça permet de répondre dans la langue de
 * l'employé (cf. PhenyxAssistantLayer::matchBestTopic()) sans dupliquer la
 * logique de matching pour chaque langue, et de traduire/enrichir le
 * contenu sans redéploiement.
 *
 * Enregistrée automatiquement au démarrage par
 * PhenyxAssistant::registerCoreLayers() — pas besoin qu'un plugin appelle
 * registerLayer() pour l'activer.
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantUserLayer extends PhenyxAssistantLayer {

    /** @see PhenyxAssistantLayer::$entities */
    protected $entities = ['User'];

    /** @see PhenyxAssistantLayer::$sampleQuestions */
    protected $sampleQuestions = [
        'Comment créer un client ?',
        'How do I create a customer?',
        'Wie lege ich einen Kunden an?',
    ];

    /**
     * Délègue entièrement au moteur générique de matching par topics — cf.
     * PhenyxAssistantLayer::matchBestTopic(). Les topics 'core.user.*'
     * (create/list/fields/clarify) sont scorés dans la langue de l'employé,
     * avec repli sur la langue par défaut du shop si pas encore traduits.
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer|null
     */
    public function answer(PhenyxAssistantQuery $query) {

        return $this->matchBestTopic($query, 'User');
    }

}
