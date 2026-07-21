<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Class PhenyxAssistantCoreLayer
 *
 * Couche "core" unique de l'assistant BO : contrairement à une couche de
 * plugin (cf. README du dossier), celle-ci ne dépend d'aucun plugin et
 * couvre TOUTES les fonctionnalités natives du framework documentées dans
 * le wiki — clients (UserCore/AdminUsers), pages CMS (CMSCore/AdminCms),
 * blocs Content Anywhere (ContentAnyWhereCore/AdminContentAnyWhere),
 * gestion des plugins (PluginCore/AdminPlugins), etc.
 *
 * S'appelait à l'origine PhenyxAssistantUserLayer et ne couvrait que les
 * clients — renommée le 2026-07-21 en ajoutant les topics core.cms.*,
 * core.contentanywhere.* et core.plugin.* : plutôt que d'enregistrer une
 * couche PHP par entité (ce qui obligerait à toucher ce fichier à chaque
 * nouveau topic core), matchBestTopic() est appelé SANS filtre entity_class
 * (cf. PhenyxAssistantLayer::matchBestTopic(), 2e paramètre à null) — il
 * score alors tous les topics actifs de la langue de l'employé, quel que
 * soit leur entity_class. Ajouter un nouveau pan du core au wiki ne
 * nécessite donc qu'un nouveau topic en base (cf.
 * install_assistant_topics.php), jamais de nouvelle classe PHP ici.
 *
 * Le contenu (questions/réponses/quick replies) vit en base — table
 * eph_phenyx_assistant_topic(_lang), cf. sql/phenyx_assistant_topic.sql et
 * install_assistant_topics.php pour le jeu de données initial — plutôt
 * qu'en constantes PHP : ça permet de répondre dans la langue de l'employé
 * (cf. PhenyxAssistantLayer::matchBestTopic()) sans dupliquer la logique de
 * matching pour chaque langue, et de traduire/enrichir le contenu sans
 * redéploiement.
 *
 * Enregistrée automatiquement au démarrage par
 * PhenyxAssistant::registerCoreLayers() — pas besoin qu'un plugin appelle
 * registerLayer() pour l'activer.
 *
 * @since 1.0.0 (assistant BO)
 * @since 2026-07-21 renommée de PhenyxAssistantUserLayer, plus limitée à 'User'
 */
#[\AllowDynamicProperties]
class PhenyxAssistantCoreLayer extends PhenyxAssistantLayer {

    /** @see PhenyxAssistantLayer::$entities */
    protected $entities = ['User', 'CMS', 'ContentAnyWhere', 'Plugin'];

    /** @see PhenyxAssistantLayer::$sampleQuestions */
    protected $sampleQuestions = [
        'Comment créer un client ?',
        'How do I create a customer?',
        'Wie lege ich einen Kunden an?',
        'Comment créer une page CMS ?',
        'How do I install a plugin?',
    ];

    /**
     * Délègue entièrement au moteur générique de matching par topics — cf.
     * PhenyxAssistantLayer::matchBestTopic(). Pas de filtre entity_class
     * (2e argument à null) : cette couche répond pour tous les topics core
     * ('core.user.*', 'core.cms.*', 'core.contentanywhere.*',
     * 'core.plugin.*', ...), scorés dans la langue de l'employé, avec repli
     * sur la langue par défaut du shop si pas encore traduits.
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer|null
     */
    public function answer(PhenyxAssistantQuery $query) {

        return $this->matchBestTopic($query, null);
    }

}
