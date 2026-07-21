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
 * Volontairement le premier maillon, le plus simple possible, avant
 * d'attaquer une couche plus riche comme celle de ph_ecommerce.
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
        'Quels champs sont obligatoires pour créer un client ?',
        'Où voir la liste des clients ?',
    ];

    /** Variantes reconnues pour l'entité "client" (cf. matchesConcept()). */
    const CONCEPT_ENTITY = ['client', 'clients', 'utilisateur', 'utilisateurs', 'customer', 'customers', 'compte', 'comptes', 'user', 'users'];

    /** Variantes reconnues pour l'intention "créer". */
    const CONCEPT_CREATE = ['creer', 'cree', 'crees', 'creation', 'ajouter', 'ajoute', 'ajout', 'nouveau', 'nouvelle', 'nouvel', 'inscrire', 'inscription', 'faire'];

    /** Variantes reconnues pour l'intention "lister/voir". */
    const CONCEPT_LIST = ['liste', 'lister', 'listing', 'voir', 'afficher', 'chercher', 'rechercher', 'trouver', 'consulter'];

    /** Variantes reconnues pour l'intention "champs obligatoires". */
    const CONCEPT_FIELDS = ['champ', 'champs', 'obligatoire', 'obligatoires', 'requis', 'necessaire', 'necessaires', 'remplir'];

    /**
     * Matching par tokens + tolérance aux fautes de frappe (cf.
     * PhenyxAssistantLayer::tokenize()/matchesConcept()) plutôt que par
     * phrase exacte : "marque créer un client" doit être reconnu au même
     * titre que "comment créer un client ?" — seul le vocabulaire clé
     * (entité + intention) compte, pas la structure de la phrase.
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer|null
     */
    public function answer(PhenyxAssistantQuery $query) {

        $tokens = $this->tokenize($query->text);

        if (!$tokens || !$this->matchesConcept($tokens, self::CONCEPT_ENTITY)) {
            return null; // cette couche ne parle que des clients/utilisateurs
        }

        if ($this->matchesConcept($tokens, self::CONCEPT_CREATE)) {
            return $this->createAnswer();
        }

        if ($this->matchesConcept($tokens, self::CONCEPT_FIELDS)) {
            return $this->requiredFieldsAnswer();
        }

        if ($this->matchesConcept($tokens, self::CONCEPT_LIST)) {
            return $this->listAnswer();
        }

        // "client" est mentionné mais l'intention n'est pas claire : plutôt
        // que de laisser tomber, on propose les trois pistes connues.
        return $this->clarifyAnswer();
    }

    /**
     * @return PhenyxAssistantAnswer
     */
    protected function createAnswer() {

        $answer = PhenyxAssistantAnswer::text(
            'Pour créer un client : ouvre <strong>Clients</strong> (contrôleur <code>AdminUsers</code>) puis clique sur '
            . '"Ajouter". Les champs obligatoires sont le prénom, le nom, l\'email et le mot de passe — le reste '
            . '(société, SIRET, téléphone, newsletter, etc.) est optionnel.',
            null,
            0.85
        );

        $answer->suggestedAction = [
            'type'       => 'openTargetController',
            'controller' => 'AdminUsers',
            'params'     => ['action' => 'add'],
        ];

        $answer->quickReplies = [
            'Où voir la liste des clients ?',
            'Quels champs sont obligatoires pour un client ?',
        ];

        return $answer;
    }

    /**
     * @return PhenyxAssistantAnswer
     */
    protected function listAnswer() {

        $answer = PhenyxAssistantAnswer::text(
            'La liste des clients se trouve dans <strong>Clients</strong> (contrôleur <code>AdminUsers</code>), '
            . 'avec recherche et filtres intégrés.',
            null,
            0.8
        );

        $answer->suggestedAction = [
            'type'       => 'openTargetController',
            'controller' => 'AdminUsers',
        ];

        $answer->quickReplies = ['Comment créer un client ?'];

        return $answer;
    }

    /**
     * @return PhenyxAssistantAnswer
     */
    protected function requiredFieldsAnswer() {

        return PhenyxAssistantAnswer::text(
            'Pour un client (table <code>user</code>), les champs obligatoires sont : prénom, nom, email et mot de '
            . 'passe. Tout le reste (société, SIRET, APE, TVA, site web, newsletter, opt-in, etc.) est optionnel.',
            null,
            0.75
        );
    }

    /**
     * "Client" mentionné mais intention ambiguë (ni créer, ni lister, ni
     * champs) : on prend l'initiative plutôt que de renvoyer null, avec des
     * quickReplies qui couvrent les trois cas gérés par cette couche.
     *
     * @return PhenyxAssistantAnswer
     */
    protected function clarifyAnswer() {

        $answer = PhenyxAssistantAnswer::text(
            'Tu parles des clients : tu veux plutôt en <strong>créer un</strong>, en <strong>voir la liste</strong>, '
            . 'ou connaître les <strong>champs obligatoires</strong> ?',
            null,
            0.55
        );

        $answer->quickReplies = [
            'Créer un client',
            'Voir la liste des clients',
            'Champs obligatoires pour un client',
        ];

        return $answer;
    }

}
