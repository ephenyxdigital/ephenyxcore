<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistant
 *
 * Orchestrateur central de l'assistant IA back-office (cf. project_instructions :
 * un ChatBot BO natif au code source, avec une couche par plugin à forte
 * présence BO, nouvelles classes ajoutables dans vendor/ephenyxdigital/quantumcore/).
 *
 * Rôle : recueillir la question de l'employé + le contexte BO courant,
 * interroger toutes les couches de plugins disponibles (deux mécanismes
 * possibles, cf. PhenyxAssistantLayer), les agréger via un provider
 * remplaçable (cf. PhenyxAssistantProviderInterface — c'est ici qu'un LLM se
 * branchera plus tard), journaliser l'échange, et renvoyer la réponse.
 *
 * Volontairement calqué sur le style des autres singletons du framework
 * (Configuration::getInstance(), CacheApi::getInstance(), Hook::getInstance())
 * pour rester familier à quiconque connaît déjà QuantumCore.
 *
 * Usage typique côté widget BO (à appeler depuis une méthode
 * displayAjaxAssistantAsk() d'un futur AdminAssistantController, en suivant le
 * pattern JSON déjà en place dans PhenyxController) :
 *
 *   $answer = PhenyxAssistant::getInstance()->ask($questionText);
 *   die(Tools::jsonEncode([
 *       'success' => true,
 *       'text' => $answer->text,
 *       'suggestedAction' => $answer->suggestedAction,
 *       'quickReplies' => $answer->quickReplies,
 *   ]));
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistant {

    /** Nom du hook interrogé pour collecter les réponses candidates des plugins. */
    const HOOK_ANSWER = 'actionAssistantAnswer';

    /** Nom du hook interrogé pour enrichir le contexte (grounding) avant génération. */
    const HOOK_CONTEXT = 'actionAssistantContextBuild';

    /**
     * Hook d'ENRICHISSEMENT d'une réponse déjà retenue.
     *
     * Troisième mécanisme, distinct des deux précédents et complémentaire :
     *
     *  - HOOK_ANSWER  : le plugin propose une réponse CONCURRENTE, qui entre en
     *                   arbitrage avec celles des autres couches ;
     *  - HOOK_CONTEXT : le plugin enrichit le contexte donné au provider
     *                   (grounding), rien n'est affiché tel quel ;
     *  - HOOK_ANSWER_AUGMENT : le plugin AJOUTE à une réponse du cœur déjà
     *                   choisie, sans la remplacer ni la concurrencer.
     *
     * Pourquoi il manquait (retour Jeff 2026-07-25) : certains plugins ne
     * répondent pas « à la place » du cœur, ils MODIFIENT un écran du cœur. Cas
     * réel, ph_ecommerce sur AdminPreferences :
     *  - il injecte 11 champs dans l'onglet « Généralités » via le hook
     *    actionAddPreferenceFields ;
     *  - et son contrôleur override ajoute un TROISIÈME onglet « Recherche »
     *    (18 champs AJS_*), là où le cœur n'en déclare que deux.
     *
     * Aucun des deux mécanismes existants ne convenait. Faire concurrence au
     * topic d'accueil du cœur avec HOOK_ANSWER serait absurde (les deux
     * décrivent le même écran, et l'un des deux perdrait), et l'étage 0 n'admet
     * de toute façon qu'UN topic lié par écran (clé UNIQUE
     * (controller_name, source_type)) : le plugin ne peut pas revendiquer le
     * même écran. Il doit donc compléter, pas remplacer.
     *
     * Bénéfice de forme : la conditionnalité est gratuite. Un hook ne se
     * déclenche que pour les plugins installés et actifs — le cœur n'a aucun
     * `if (plugin installé)` à écrire, et sa réponse reste exacte sur les sites
     * sans le plugin.
     *
     * CONTRAT — le plugin implémente :
     *
     *   public function hookActionAssistantAnswerAugment($params) {
     *       if ($params['topicCode'] !== 'core.preferences.list') {
     *           return null;
     *       }
     *       return [
     *           'text'         => '<br /><br />' . $this->l("..."),
     *           'quickReplies' => [$this->l("How Search Works?")],
     *       ];
     *   }
     *
     * Il RETOURNE ses ajouts au lieu de muter l'objet réponse : le contrat ne
     * dépend alors pas de la façon dont Hook::exec() transmet ses paramètres, et
     * un plugin ne peut pas effacer par accident le texte du cœur.
     */
    const HOOK_ANSWER_AUGMENT = 'actionAssistantAnswerAugment';

    /** @var self */
    protected static $instance;

    /**
     * Couches enregistrées explicitement en mémoire pour la requête courante
     * (cf. PhenyxAssistantLayer, mode 1 : enregistrement direct).
     * Indexé par nom de plugin : ['ph_ecommerce' => PhenyxAssistantLayer, ...]
     *
     * @var PhenyxAssistantLayer[]
     */
    protected $layers = [];

    /** @var PhenyxAssistantProviderInterface|null */
    protected $provider;

    /**
     * Meilleur "presque-match" de la requête courante : métadonnées de la couche
     * qui a le plus approché sans atteindre le seuil (cf.
     * PhenyxAssistantLayer::getLastMatchMeta()). Transmis au provider dans le
     * snapshot de contexte pour être journalisé avec le repli.
     *
     * @var array|null
     */
    protected $nearMissMeta;

    protected function __construct() {

        $this->registerCoreLayers();
    }

    /**
     * Enregistre les couches "core" de l'assistant : celles qui ne dépendent
     * d'aucun plugin et documentent une fonctionnalité native du framework
     * (ex: gestion des clients via AdminUsers). Contrairement aux couches de
     * plugin (cf. PhenyxAssistantLayer, mode 1/2), elles n'ont pas besoin
     * qu'un plugin s'installe pour exister : elles sont toujours disponibles,
     * au même titre que les contrôleurs natifs de includes/controllers/backend/.
     *
     * @return void
     */
    protected function registerCoreLayers() {

        $this->registerLayer(new PhenyxAssistantCoreLayer());
    }

    /**
     * @return self
     */
    public static function getInstance() {

        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Permet à un plugin de s'enregistrer explicitement (mode 1, cf.
     * PhenyxAssistantLayer). Idempotent par nom de plugin : un second appel
     * pour le même plugin remplace le précédent plutôt que de l'empiler.
     *
     * @param PhenyxAssistantLayer $layer
     * @return void
     */
    public function registerLayer(PhenyxAssistantLayer $layer) {

        $key = $layer->pluginName ?: get_class($layer);
        $this->layers[$key] = $layer;
    }

    /**
     * @return PhenyxAssistantLayer[]
     */
    public function getRegisteredLayers() {

        return $this->layers;
    }

    /**
     * Permet de remplacer le moteur de génération (ex: brancher un LLM local
     * une fois disponible) sans toucher au reste de l'orchestration.
     *
     * @param PhenyxAssistantProviderInterface $provider
     * @return void
     */
    public function setProvider(PhenyxAssistantProviderInterface $provider) {

        $this->provider = $provider;
    }

    /**
     * @return PhenyxAssistantProviderInterface
     */
    public function getProvider() {

        if (!isset($this->provider)) {
            $this->provider = new PhenyxAssistantRuleProvider();
        }

        return $this->provider;
    }

    /**
     * Point d'entrée principal. Construit la query depuis le Context courant,
     * interroge toutes les couches disponibles, fait générer la réponse
     * finale par le provider actif, journalise, puis retourne la réponse.
     *
     * @param string $questionText
     * @param array  $extraContext Contexte additionnel fourni par l'appelant (cf. PhenyxAssistantQuery::$extra)
     * @return PhenyxAssistantAnswer
     */
    public function ask($questionText, array $extraContext = []) {

        $query = PhenyxAssistantQuery::fromContext($questionText, $extraContext);

        $candidates = $this->collectCandidates($query);
        $context = $this->buildContextSnapshot($query);
        $provider = $this->getProvider();

        try {
            $answer = $provider->generate($query, $candidates, $context);
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistant: le provider "%s" a levé une exception (%s)', $provider->getName(), $e->getMessage()),
                3,
                null,
                static::class
            );
            $answer = PhenyxAssistantAnswer::text(
                'Une erreur interne empêche de répondre pour le moment.',
                null,
                0.0
            );
        }

        // Enrichissement par les plugins, APRÈS l'arbitrage : on ne complète que
        // la réponse effectivement retenue, jamais une candidate qui a perdu.
        $this->augmentAnswer($query, $answer);

        // Best effort : un échec de log ne doit jamais empêcher de renvoyer la réponse.
        PhenyxAssistantLog::record($query, $answer, $provider->getName(), count($candidates));

        return $answer;
    }

    /**
     * Laisse les plugins installés compléter la réponse retenue (cf.
     * HOOK_ANSWER_AUGMENT).
     *
     * Deux garde-fous, parce qu'un plugin tiers ne doit jamais pouvoir dégrader
     * une réponse du cœur :
     *  - le texte d'origine n'est jamais remplacé, seulement suffixé ;
     *  - une exception dans un plugin est journalisée puis ignorée, la réponse
     *    part quand même.
     *
     * @param PhenyxAssistantQuery  $query
     * @param PhenyxAssistantAnswer $answer Complété sur place
     * @return void
     */
    protected function augmentAnswer(PhenyxAssistantQuery $query, PhenyxAssistantAnswer $answer) {

        // Rien à enrichir sur un repli « je ne sais pas » : aucun topic n'a été
        // retenu, un plugin n'a donc rien à quoi se raccrocher.
        if ($answer->isEmpty()) {
            return;
        }

        // Filet de sécurité contre un déploiement PARTIEL : $topicCode est posé
        // par PhenyxAssistantLayer::buildTopicAnswer(). Si ce fichier-là n'est pas
        // à jour alors que celui-ci l'est, le code manque et l'enrichissement
        // était silencieusement abandonné — panne invisible et pénible à
        // diagnostiquer. On le retrouve depuis l'id du topic, qui lui est posé
        // depuis bien plus longtemps.
        if (!$answer->topicCode && $answer->idTopic) {

            try {
                $answer->topicCode = PhenyxAssistantTopic::getCrmDb()->getValue(
                    (new DbQuery())
                        ->select('code')
                        ->from('phenyx_assistant_topic')
                        ->where('id_phenyx_assistant_topic = ' . (int) $answer->idTopic)
                );
                PhenyxLogger::addLog(
                    'PhenyxAssistant::augmentAnswer : topicCode absent de la réponse, retrouvé depuis idTopic — PhenyxAssistantLayer.php n\'est probablement pas à jour sur ce site.',
                    2,
                    null,
                    static::class
                );
            } catch (\Throwable $e) {
                // Sans code, on ne peut pas enrichir : les plugins l'utilisent
                // pour reconnaître le topic.
            }

        }

        if (!$answer->topicCode) {
            return;
        }

        $context = Context::getContext();

        if (!isset($context->_hook)) {
            return;
        }

        try {
            $results = $context->_hook->exec(
                self::HOOK_ANSWER_AUGMENT,
                [
                    'query'     => $query,
                    'answer'    => $answer,
                    'topicCode' => $answer->topicCode,
                    'idTopic'   => $answer->idTopic,
                ],
                null,
                true
            );
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistant: le hook %s a levé une exception (%s)', self::HOOK_ANSWER_AUGMENT, $e->getMessage()),
                2,
                null,
                static::class
            );

            return;
        }

        if (!is_array($results)) {
            return;
        }

        foreach ($results as $pluginName => $result) {

            if (!is_array($result)) {
                continue;
            }

            /*
             * ⚠️ Normalisation de forme — corrigé le 2026-07-25 après une panne
             * dont le diagnostic était trompeur.
             *
             * La version initiale faisait `foreach ((array) $result as $item)`,
             * pattern recopié de collectCandidates(). Là-bas il est correct : les
             * items sont des OBJETS PhenyxAssistantAnswer, et un plugin peut en
             * renvoyer un seul ou une liste.
             *
             * Ici la charge utile est un tableau ASSOCIATIF
             * ['text' => ..., 'quickReplies' => [...]]. Itérer dessus parcourait
             * donc ses CHAMPS au lieu de le traiter comme un item :
             *   - 1re itération : $item = la chaîne de texte -> !is_array -> ignorée ;
             *   - 2e itération  : $item = le tableau des puces -> is_array OK,
             *     mais ni clé 'text' ni clé 'quickReplies' dedans -> rien fusionné,
             *     et pourtant $answer->sourcePlugin renseigné en fin de boucle.
             *
             * D'où le symptôme déroutant : le hook renvoyait bien ses 308
             * caractères (vérifié isolément), sourcePlugin valait 'ph_ecommerce',
             * et malgré ça ni le texte ni les puces n'arrivaient dans la réponse.
             *
             * On détecte donc explicitement la forme au lieu de « rester
             * tolérant » : la présence d'une des deux clés attendues signe un item
             * unique, sinon on considère une liste d'items.
             */
            /*
             * ⚠️ La liste des clés reconnues est DÉLIBÉRÉMENT centralisée ici.
             *
             * Cette détection a été écrite en ne testant que 'text' et
             * 'quickReplies', puis 'topicChips' a été ajouté comme troisième
             * forme de contribution — sans mettre la détection à jour. Résultat,
             * un plugin ne renvoyant QUE des topicChips (ph_einvoicing sur
             * core.tour.company) retombait dans la branche « liste d'items », on
             * reparcourait ses champs, et rien n'était fusionné. Exactement la
             * même panne que celle corrigée quelques heures plus tôt, au même
             * endroit, faute d'avoir rendu la détection générique du premier coup.
             *
             * Toute nouvelle forme de contribution doit être ajoutée à ce
             * tableau, et à un seul endroit.
             */
            $payloadKeys = ['text', 'quickReplies', 'topicChips'];
            $isSingleItem = false;

            foreach ($payloadKeys as $payloadKey) {

                if (isset($result[$payloadKey])) {
                    $isSingleItem = true;
                    break;
                }

            }

            $items = $isSingleItem ? [$result] : $result;

            foreach ($items as $item) {

                if (!is_array($item)) {
                    continue;
                }

                if (!empty($item['text'])) {
                    // Concaténation, jamais affectation : le texte du cœur reste
                    // intact quoi que fasse le plugin.
                    $answer->text .= (string) $item['text'];
                }

                if (!empty($item['quickReplies']) && is_array($item['quickReplies'])) {
                    $answer->quickReplies = array_values(
                        array_unique(
                            array_merge((array) $answer->quickReplies, $item['quickReplies'])
                        )
                    );
                }

                /*
                 * Puces de NAVIGATION contribuées par le plugin (cf.
                 * PhenyxAssistantAnswer::$topicChips).
                 *
                 * C'est le moyen propre pour un plugin de rattacher SES écrans à
                 * une réponse du cœur : le topic core.tour.company décrit la
                 * famille « Société » telle que le cœur la connaît et ne peut pas
                 * mentionner les écrans qu'un plugin y ajoute (constaté le
                 * 2026-07-25 : la réponse ne disait rien de la facturation
                 * électronique de ph_einvoicing).
                 *
                 * Et comme une puce de navigation porte un id de topic, son
                 * libellé peut venir du champ `label` — déjà traduit dans les 11
                 * langues. Le plugin n'a donc AUCUN texte à rédiger pour se
                 * rendre visible, contrairement à une contribution par 'text'.
                 *
                 * Dédoublonnage par idTopic : deux plugins qui pointeraient le
                 * même topic ne produiraient qu'une puce.
                 */
                if (!empty($item['topicChips']) && is_array($item['topicChips'])) {
                    $existing = [];

                    foreach ((array) $answer->topicChips as $chip) {

                        if (!empty($chip['idTopic'])) {
                            $existing[(int) $chip['idTopic']] = true;
                        }

                    }

                    foreach ($item['topicChips'] as $chip) {

                        if (empty($chip['idTopic']) || isset($existing[(int) $chip['idTopic']])) {
                            continue;
                        }

                        $existing[(int) $chip['idTopic']] = true;
                        $answer->topicChips[] = $chip;
                    }

                }

                // Le plugin peut signaler quel plugin a enrichi, à des fins de
                // log — sans écraser la source si le cœur en avait déjà une.
                if (!$answer->sourcePlugin && is_string($pluginName)) {
                    $answer->sourcePlugin = $pluginName;
                }

            }

        }

    }

    /**
     * L'employé a désigné le bon topic parmi plusieurs candidats ambigus
     * (ÉTAGE 4). On mémorise son choix, puis on lui répond.
     *
     * L'ordre importe : on écrit l'alias AVANT de produire la réponse, de sorte
     * que celle-ci soit servie par l'étage 1 (STAGE_ALIAS) et que le log porte
     * la trace de ce nouveau chemin. Si l'écriture échoue (base CRM
     * indisponible), on répond quand même — l'employé attend une réponse, pas un
     * message d'erreur sur un mécanisme d'apprentissage dont il ignore
     * l'existence.
     *
     * @param string $questionText Question d'origine, telle que tapée
     * @param int    $idTopic      Topic choisi par l'employé
     * @param array  $extraContext Cf. PhenyxAssistantQuery::$extra
     * @return PhenyxAssistantAnswer
     */
    public function resolve($questionText, $idTopic, array $extraContext = []) {

        $query = PhenyxAssistantQuery::fromContext($questionText, $extraContext);
        $idLang = $this->resolveQueryLangId($query);

        PhenyxAssistantAlias::learn(
            $this->buildQuestionSignature($questionText),
            $questionText,
            (int) $idTopic,
            $idLang,
            PhenyxAssistantAlias::ORIGIN_EMPLOYEE
        );

        // Volontairement un ask() complet plutôt qu'une lecture directe du
        // topic : on veut exactement le même pipeline (filtrage des quick
        // replies selon l'écran, arbitrage entre couches, journalisation) qu'une
        // question ordinaire. L'alias fraîchement écrit fait que l'étage 1
        // répondra du premier coup.
        return $this->ask($questionText, $extraContext);
    }

    /**
     * Réponse d'un topic DÉSIGNÉ PAR SON ID, sans aucun matching.
     *
     * Sert aux puces de PhenyxAssistantAnswer::$topicChips — typiquement le tour
     * dérivé du menu, dont les libellés sont des noms de familles
     * (« Nos produits ») qui n'ont aucune raison de recouper les mots-clés du
     * topic visé. Renvoyer ce libellé comme une question l'aurait fait repasser
     * par le scoring, avec tous les risques de collision qu'on a passé la journée
     * à éliminer.
     *
     * Volontairement SANS écriture d'alias, contrairement à resolve() : cliquer
     * une puce de navigation n'est pas trancher une ambiguïté, il n'y a rien à
     * apprendre.
     *
     * @param int   $idTopic
     * @param array $extraContext Cf. PhenyxAssistantQuery::$extra
     * @return PhenyxAssistantAnswer
     */
    public function answerForTopic($idTopic, array $extraContext = []) {

        $query = PhenyxAssistantQuery::fromContext('', $extraContext);
        $idLang = $this->resolveQueryLangId($query);
        $row = PhenyxAssistantTopic::getRowById((int) $idTopic, $idLang);

        if (!$row || empty($row['answer'])) {
            return PhenyxAssistantAnswer::text(
                'Ce sujet n\'est plus disponible.',
                null,
                0.0
            );
        }

        /*
         * DROITS — un id de topic arrive ici depuis le NAVIGATEUR (clic sur une
         * puce). Les puces sont déjà filtrées à la construction, mais rien
         * n'empêche de rejouer la requête avec un autre id : c'est une entrée
         * utilisateur, elle se vérifie côté serveur.
         *
         * Même règle que dans PhenyxAssistantLayer::matchBestTopic() — un topic
         * sans liage d'écran reste accessible à tous.
         */
        if (!PhenyxAssistantTopic::isTopicAllowed((int) $row['id_phenyx_assistant_topic'])) {
            $restricted = PhenyxAssistantTopic::getRowByCode(
                PhenyxAssistantLayer::RESTRICTED_TOPIC_CODE,
                $idLang
            );

            return PhenyxAssistantAnswer::text(
                ($restricted && !empty($restricted['answer']))
                ? (string) $restricted['answer']
                : 'Ce sujet ne fait pas partie de ton périmètre.',
                null,
                0.9
            );
        }

        $answer = PhenyxAssistantAnswer::text((string) $row['answer'], null, (float) $row['confidence']);
        $answer->idTopic = (int) $row['id_phenyx_assistant_topic'];
        $answer->topicCode = (string) $row['code'];
        $answer->suggestedAction = !empty($row['suggested_action']) ? json_decode($row['suggested_action'], true) : null;
        $answer->quickReplies = !empty($row['quick_replies']) ? (array) json_decode($row['quick_replies'], true) : [];

        /*
         * Faute d'action déclarée, on propose d'OUVRIR l'écran que ce topic
         * documente — déduit du liage, cf.
         * PhenyxAssistantTopic::getBoundControllerForTopic().
         *
         * Retour Jeff 2026-07-25 : les puces de la famille « Société » répondaient
         * correctement mais laissaient l'employé chercher l'écran lui-même. Côté
         * navigateur, 'openTargetController' passe par simulateBackTabNavigation()
         * qui déroule visuellement le menu jusqu'à l'entrée avant de cliquer — il
         * MONTRE le chemin au lieu de téléporter, ce qui est exactement ce qu'on
         * veut d'un assistant.
         *
         * Jamais d'écrasement d'une action existante : un topic qui déclare
         * openScreenTab ou highlightElement garde la sienne.
         */
        if (empty($answer->suggestedAction)) {
            $screenAction = PhenyxAssistantTopic::getScreenActionForTopic(
                (int) $row['id_phenyx_assistant_topic'],
                $idLang
            );

            if ($screenAction) {
                $answer->suggestedAction = $screenAction;
            }

        }

        // Le bouton porte le nom du MENU, jamais le nom de classe PHP. Vaut pour
        // l'action déclarée par le topic comme pour celle déduite ci-dessus.
        $answer->suggestedAction = PhenyxAssistantTopic::decorateActionLabels(
            $answer->suggestedAction,
            $idLang
        );

        /*
         * ─── QUICK REPLIES : MUETTES TANT QUE L'ÉCRAN N'EST PAS OUVERT ───
         * (retour Jeff, 2026-07-27)
         *
         * En descendant l'arbre du menu depuis le tour — Listes, puis Clients,
         * puis « Gestion des groupes de clients » — l'employé n'a encore RIEN
         * ouvert. Lui proposer « Comment créer un groupe ? » ou « À quoi servent
         * les autorisations de plugins ? » revient à l'inviter à agir sur un écran
         * qu'il ne voit pas. Une seule proposition a du sens à cet instant :
         * ouvrir l'écran. Les quick replies reprendront tout leur sens une fois
         * qu'il y sera, l'ÉTAGE 0 les lui servant à l'ouverture.
         *
         * La règle ne s'applique qu'aux topics LIÉS À UN ÉCRAN : un topic
         * transverse (tour, rubrique de menu, notion générale) n'a pas d'écran de
         * référence et garde ses propositions.
         *
         * Complémentaire de PhenyxAssistantLayer::filterScreenDependentQuickReplies(),
         * qui traite un cas plus étroit — les quick replies dont l'ACTION exige une
         * liste ouverte. Ici c'est le SUJET tout entier qui est hors de portée.
         */
        $boundController = PhenyxAssistantTopic::getBoundControllerForTopic(
            (int) $row['id_phenyx_assistant_topic']
        );
        $currentScreen = isset($query->extra['sourceController'])
        ? (string) $query->extra['sourceController']
        : '';

        if ($boundController && $boundController !== $currentScreen) {
            $answer->quickReplies = [];
        }

        $answer->raw = [
            // Résolution exacte, au même titre qu'un alias : aucune approximation
            // n'est intervenue. Le log doit le refléter.
            'stage'         => PhenyxAssistantLayer::STAGE_ALIAS,
            'score'         => null,
            'idTopic'       => (int) $row['id_phenyx_assistant_topic'],
            'runnerUpTopic' => null,
            'runnerUpScore' => null,
        ];

        /*
         * FAMILLE DE MENU : puces dérivées des onglets enfants.
         *
         * Pendant de ce que fait PhenyxAssistantLayer::buildTopicAnswer() sur le
         * chemin du matching — ici c'est le chemin du CLIC sur une puce, qui ne
         * passe pas par la couche. Les deux doivent se comporter pareil, sinon le
         * tour montrerait des écrans différents selon qu'on y arrive en tapant une
         * question ou en cliquant : c'est précisément le genre d'incohérence qui
         * fait douter l'employé de l'assistant.
         *
         * ⚠️ L'ORDRE COMPTE. Cette dérivation doit précéder augmentAnswer(), qui
         * dédoublonne les puces contribuées par les plugins contre celles déjà
         * présentes (cf. le bloc 'topicChips' dans augmentAnswer()). Placée après,
         * elle ferait apparaître deux fois les écrans que ph_einvoicing et
         * ph_ecommerce contribuent explicitement au tour de « Société » — ils sont
         * aussi des enfants d'AdminParentCompany, donc dérivés d'office.
         */
        $parent = PhenyxAssistantTopic::getMenuBindingForTopic((int) $row['id_phenyx_assistant_topic']);

        if ($parent) {
            $children = PhenyxAssistantTopic::getMenuChildrenTopics($parent, $idLang);

            if ($children) {
                $answer->topicChips = PhenyxAssistantTopic::mergeTopicChips(
                    $children,
                    (array) $answer->topicChips
                );
                $answer->quickReplies = [];

                /*
                 * ⚠️ Une famille n'expose AUCUN bouton d'ouverture (règle du
                 * 2026-07-26). Pendant exact du même traitement dans
                 * PhenyxAssistantLayer::buildTopicAnswer() — les deux chemins,
                 * question tapée et clic sur une puce, doivent donner la même chose.
                 *
                 * La mise à null est ici indispensable et pas seulement défensive :
                 * le bloc getScreenActionForTopic() plus haut a pu poser une action,
                 * et surtout core.tour.company déclarait trois actions rédigées
                 * jusqu'à cette date. Un site dont le wiki n'a pas encore été
                 * re-seedé continuerait sinon d'afficher ses boutons.
                 */
                $answer->suggestedAction = null;
            }

        }

        // Les plugins peuvent enrichir cette réponse comme n'importe quelle autre.
        $this->augmentAnswer($query, $answer);

        // Journalisé avec le libellé du topic comme « question » : sans texte
        // saisi, c'est la seule trace lisible de ce que l'employé a demandé.
        $query->text = !empty($row['label']) ? (string) $row['label'] : (string) $row['code'];
        PhenyxAssistantLog::record($query, $answer, 'topic', 1);

        return $answer;
    }

    /**
     * Réponse pour un écran PRÉSENT dans le menu mais pas encore documenté.
     *
     * ⚠️ Contrepartie du renversement du 2026-07-26 (cf.
     * PhenyxAssistantTopic::getMenuFamilyTopics()) : puisque toute entrée visible
     * du menu produit désormais une puce, il faut savoir répondre quand on clique
     * sur l'une de celles qui n'ont pas de topic. Une puce muette serait pire que
     * l'absence de puce.
     *
     * La réponse assume l'ignorance et reste utile : elle NOMME l'écran tel qu'il
     * s'appelle dans le menu, et propose quand même de l'ouvrir. L'assistant ne
     * sait pas l'expliquer, mais il sait y conduire — ce qui est déjà l'essentiel
     * de ce qu'un employé perdu cherche.
     *
     * @param string $controller Nom de classe, tel que porté par la puce
     * @param array  $extraContext
     * @return PhenyxAssistantAnswer
     */
    public function answerForController($controller, array $extraContext = []) {

        $query = PhenyxAssistantQuery::fromContext('', $extraContext);
        $idLang = $this->resolveQueryLangId($query);
        $controller = (string) $controller;

        /*
         * Vérification de droits, pour la même raison que dans answerForTopic() :
         * le nom de contrôleur vient du navigateur. Sans ce contrôle, il suffirait
         * de rejouer la requête avec un autre nom pour apprendre l'existence d'un
         * écran interdit et se le faire ouvrir.
         */
        if (!PhenyxAssistantTopic::isControllerVisible($controller)) {
            $restricted = PhenyxAssistantTopic::getRowByCode(
                PhenyxAssistantLayer::RESTRICTED_TOPIC_CODE,
                $idLang
            );

            return PhenyxAssistantAnswer::text(
                ($restricted && !empty($restricted['answer'])) ? (string) $restricted['answer'] : '',
                null,
                0.9
            );
        }

        // Libellé du menu, pas nom de classe : « Gestion des Partenaires », pas
        // « AdminParentPart ». Même source que les puces (back_tab_lang).
        $label = $controller;
        $idTab = (int) BackTab::getIdFromClassName($controller);

        if ($idTab) {
            $tab = BackTab::getTab($idLang, $idTab);

            if (is_array($tab) && !empty($tab['name'])) {
                $label = (string) $tab['name'];
            }

        }

        /*
         * Un PARENT de menu n'a pas d'écran à ouvrir : proposer « Ouvrir Gestion
         * des Partenaires » sur une famille mènerait à un clic sans effet. On ne
         * pose l'action que si le contrôleur a réellement un écran — heuristique :
         * il n'est pas dans la liste des parents connus du menu.
         */
        $action = PhenyxAssistantTopic::buildOpenActionForController($controller, $idLang);

        /*
         * ─── REGROUPEMENT DE MENU ou ÉCRAN NON DOCUMENTÉ ? (2026-07-27) ───
         *
         * Retour de Jeff : cliquer « Clients » ou « Données locales » depuis la
         * famille Listes donnait « Je n'ai pas encore d'information sur Clients.
         * L'écran existe bien dans ton menu — je peux t'y conduire ». Deux
         * affirmations fausses d'un coup : ce n'est pas un écran, et il n'y a nulle
         * part où conduire. Ces onglets intermédiaires (AdminParentCustomers,
         * AdminParentLocalData) n'ont pas de contrôleur, seulement des enfants.
         *
         * La distinction est faite par la DONNÉE, pas par une convention de nommage
         * sur « AdminParent… » : est un regroupement ce qui n'a aucun écran à ouvrir
         * ($action null) mais porte des enfants visibles. Un plugin qui nommerait
         * autrement ses parents est traité correctement sans rien déclarer.
         *
         * L'intérêt est qu'il n'y a AUCUN contenu à écrire : la réponse est dérivée
         * du menu de cet employé. Tout regroupement, présent ou futur, du cœur ou
         * d'un plugin, oriente désormais vers ses enfants.
         */
        $children = PhenyxAssistantTopic::getMenuChildrenTopics($controller, $idLang);
        $isMenuGroup = (!$action && !empty($children));

        $row = PhenyxAssistantTopic::getRowByCode(
            $isMenuGroup
            ? PhenyxAssistantLayer::MENU_GROUP_TOPIC_CODE
            : PhenyxAssistantLayer::UNDOCUMENTED_TOPIC_CODE,
            $idLang
        );
        $text = ($row && !empty($row['answer'])) ? (string) $row['answer'] : '';
        // Jeton nommé, même raison que FAMILY_COUNT_TOKEN : pas de sprintf sur du
        // texte de wiki.
        $text = str_replace('{screen}', '<strong>' . $label . '</strong>', $text);

        // Un regroupement répond de façon sûre — il énumère ce que le menu dit —
        // là où « écran non documenté » est un aveu d'ignorance.
        $answer = PhenyxAssistantAnswer::text($text, null, $isMenuGroup ? 0.85 : 0.7);

        if ($isMenuGroup) {
            $answer->topicChips = PhenyxAssistantTopic::mergeTopicChips(
                $children,
                (array) $answer->topicChips
            );
        }

        if ($action) {
            $answer->suggestedAction = $action;
        }

        $query->text = $label;
        PhenyxAssistantLog::record($query, $answer, 'screen', 1);

        return $answer;
    }

    /**
     * Signature d'une question, pour l'écriture d'un alias depuis
     * l'orchestrateur.
     *
     * questionSignature() est `protected` sur PhenyxAssistantLayer (c'est un
     * détail d'implémentation du matching, pas une API publique) : on passe donc
     * par la couche core, qui est toujours enregistrée, plutôt que de dupliquer
     * l'algorithme ici — deux implémentations de la normalisation qui
     * divergeraient produiraient des alias jamais retrouvés, le pire des bugs
     * possibles sur ce mécanisme.
     *
     * @param string $questionText
     * @return string
     */
    protected function buildQuestionSignature($questionText) {

        foreach ($this->layers as $layer) {

            if ($layer instanceof PhenyxAssistantCoreLayer) {
                return $layer->publicQuestionSignature($questionText);
            }

        }

        // Aucune couche core (cas théorique : registerCoreLayers() l'enregistre
        // toujours) — on prend la première disponible.
        foreach ($this->layers as $layer) {
            return $layer->publicQuestionSignature($questionText);
        }

        return '';
    }

    /**
     * Résout l'id_lang d'une query, avec le même repli défensif que
     * PhenyxAssistantLayer::resolveLangId().
     *
     * @param PhenyxAssistantQuery $query
     * @return int
     */
    protected function resolveQueryLangId(PhenyxAssistantQuery $query) {

        if ($query->isoCode && Validate::isLanguageIsoCode($query->isoCode)) {
            $idLang = (int) Language::getIdByIso($query->isoCode);

            if ($idLang) {
                return $idLang;
            }

        }

        return (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');
    }

    /**
     * Rassemble les réponses candidates des deux sources possibles :
     * les couches enregistrées explicitement en mémoire, et les plugins
     * abonnés au hook HOOK_ANSWER (qui n'ont pas besoin d'instancier une
     * PhenyxAssistantLayer pour participer — juste implémenter
     * hookActionAssistantAnswer($params) et retourner une PhenyxAssistantAnswer).
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer[]
     */
    protected function collectCandidates(PhenyxAssistantQuery $query) {

        $candidates = [];
        $this->nearMissMeta = null;

        foreach ($this->layers as $layer) {

            try {
                $result = $layer->answer($query);
            } catch (\Throwable $e) {
                PhenyxLogger::addLog(
                    sprintf('PhenyxAssistantLayer "%s" a levé une exception (%s)', get_class($layer), $e->getMessage()),
                    2,
                    null,
                    static::class
                );
                continue;
            }

            if ($result instanceof PhenyxAssistantAnswer && !$result->isEmpty()) {
                $candidates[] = $result;
                continue;
            }

            // La couche n'a rien répondu : on retient quand même de combien elle
            // a manqué le seuil. Sans ça, une question sans réponse
            // n'apparaîtrait dans le log qu'avec un score vide, alors que
            // "0.6 sur deux mots génériques" et "aucun mot-clé commun" appellent
            // deux corrections très différentes (revoir un seuil vs écrire un
            // topic manquant).
            $this->rememberNearMiss($layer->getLastMatchMeta());
        }

        $context = Context::getContext();

        if (isset($context->_hook)) {
            $hookResults = $context->_hook->exec(self::HOOK_ANSWER, ['query' => $query], null, true);

            if (is_array($hookResults)) {

                foreach ($hookResults as $pluginName => $result) {

                    // arrayReturn=true peut donner directement l'objet retourné par le
                    // plugin, ou un tableau si le plugin a plusieurs hooks matchant —
                    // on reste tolérant sur la forme exacte.
                    foreach ((array) $result as $item) {

                        if ($item instanceof PhenyxAssistantAnswer && !$item->isEmpty()) {

                            if (!$item->sourcePlugin) {
                                $item->sourcePlugin = is_string($pluginName) ? $pluginName : null;
                            }

                            $candidates[] = $item;
                        }

                    }

                }

            }

        }

        return $candidates;
    }

    /**
     * Retient le meilleur presque-match rencontré sur la requête courante.
     * "Meilleur" = plus haut 'score' : avec plusieurs couches qui échouent
     * toutes, celle qui a le plus approché est la plus instructive.
     *
     * @param array|null $meta Cf. PhenyxAssistantLayer::getLastMatchMeta()
     * @return void
     */
    protected function rememberNearMiss($meta) {

        if (!is_array($meta) || !isset($meta['score'])) {
            return;
        }

        if ($this->nearMissMeta === null || (float) $meta['score'] > (float) $this->nearMissMeta['score']) {
            $this->nearMissMeta = $meta;
        }

    }

    /**
     * Construit l'instantané de contexte BO donné au provider (grounding).
     * Reste une structure de données simple (array), pas un objet dédié, pour
     * rester facile à sérialiser tel quel dans un futur prompt LLM.
     *
     * @param PhenyxAssistantQuery $query
     * @return array
     */
    public function buildContextSnapshot(PhenyxAssistantQuery $query) {

        /*
         * ⚠️ LE CONTROLEUR DE L'INSTANTANE EST CELUI DE L'EMPLOYE, PAS LE NOTRE.
         *
         * $query->controllerName vient de PhenyxAssistantQuery::fromContext(),
         * qui lit $context->controller — c'est-a-dire le controleur qui TRAITE
         * la requete ajax, donc AdminAssistant lui-meme. Jamais l'ecran depuis
         * lequel l'employe a pose sa question.
         *
         * Constate le 2026-08-04 sur la fiche utilisateur : le message de repli
         * annoncait « aucune reponse pour "Visualiser ou modifier Jeff Hunger"
         * sur l'ecran AdminAssistant » — un ecran ou l'employe n'a jamais mis
         * les pieds. Le vrai controleur voyage pourtant depuis le navigateur
         * dans extra['sourceController'] ; l'instantane l'ignorait.
         *
         * On prefere donc la source declaree, et on retombe sur le controleur
         * courant seulement quand elle manque (appel serveur a serveur, par
         * exemple). Meme regle pour le type d'ecran.
         *
         * ⚠️ Ce champ ne sert PAS a resoudre le topic — l'etage 0 lit
         * extra['sourceController'] directement. Il sert au GROUNDING : le
         * message de repli, l'onglet de menu associe (getBackTabInfo), et
         * demain le prompt d'un LLM. Un contexte faux y est plus nuisible
         * qu'un contexte absent : il raconte une histoire cohérente et fausse.
         */
        $nomControleur = !empty($query->extra['sourceController'])
        ? (string) $query->extra['sourceController']
        : $query->controllerName;

        $typeControleur = !empty($query->extra['sourceType'])
        ? (string) $query->extra['sourceType']
        : $query->controllerType;

        $snapshot = [
            'controller' => [
                'name'   => $nomControleur,
                'type'   => $typeControleur,
                'plugin' => $query->pluginName,
                /*
                 * Onglet courant (dataTab). Present pour le grounding, au meme
                 * titre que le nom et le type : un repli qui sait sur quel
                 * onglet se trouve l'employe peut le dire.
                 */
                'tab'    => isset($query->extra['sourceTab']) ? (string) $query->extra['sourceTab'] : null,
            ],
            'employee' => [
                'id'         => $query->idEmployee,
                'id_profile' => $query->idProfile,
            ],
            /*
             * Meme correction : l'onglet de menu a decrire est celui de
             * l'ecran de l'employe, pas celui d'AdminAssistant — qui n'a
             * d'ailleurs aucune entree de menu, d'ou un backTab vide.
             */
            'backTab' => $this->getBackTabInfo($nomControleur),
            'entity'  => null,
            'layers'  => $this->describeLayers(),
            // Renseigné uniquement si collectCandidates() a déjà tourné pour
            // cette requête (c'est l'ordre dans ask()) — permet au provider de
            // journaliser de combien le meilleur candidat a manqué le seuil
            // quand il doit produire un repli.
            'nearMissMeta' => $this->nearMissMeta,
        ];

        if ($query->entityClass && class_exists($query->entityClass)) {

            try {
                $definition = PhenyxObjectModel::getDefinition($query->entityClass);
                $snapshot['entity'] = [
                    'class' => $query->entityClass,
                    'id'    => $query->entityId,
                    'table' => isset($definition['table']) ? $definition['table'] : null,
                ];
            } catch (\Throwable $e) {
                // Une entité inconnue/mal déclarée ne doit pas empêcher de répondre.
            }

        }

        $context = Context::getContext();

        if (isset($context->_hook)) {
            $extra = $context->_hook->exec(self::HOOK_CONTEXT, ['query' => $query, 'snapshot' => $snapshot], null, true);

            if (is_array($extra)) {
                $snapshot['plugins'] = $extra;
            }

        }

        return $snapshot;
    }

    /**
     * Résout la ligne back_tab correspondant au contrôleur courant, pour
     * savoir de quel plugin il dépend et où il se situe dans le menu BO.
     * Requête directe (pas de dépendance à une éventuelle API BackTab non
     * confirmée) — à simplifier si BackTab expose un jour un getter dédié.
     *
     * @param string|null $controllerName
     * @return array|null
     */
    protected function getBackTabInfo($controllerName) {

        if (empty($controllerName)) {
            return null;
        }

        try {
            $row = Db::getInstance()->getRow(
                (new DbQuery())
                    ->select('id_back_tab, class_name, plugin, id_parent')
                    ->from('back_tab')
                    ->where('class_name = \'' . pSQL($controllerName) . '\'')
            );
        } catch (\Throwable $e) {
            return null;
        }

        return $row ?: null;
    }

    /**
     * @return array
     */
    protected function describeLayers() {

        $descriptions = [];

        foreach ($this->layers as $layer) {
            $descriptions[] = $layer->describe();
        }

        return $descriptions;
    }

}
