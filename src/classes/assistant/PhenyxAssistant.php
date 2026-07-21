<?php

namespace EphenyxDigital\QuantumCore;

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

        // Best effort : un échec de log ne doit jamais empêcher de renvoyer la réponse.
        PhenyxAssistantLog::record($query, $answer, $provider->getName(), count($candidates));

        return $answer;
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
            }

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
     * Construit l'instantané de contexte BO donné au provider (grounding).
     * Reste une structure de données simple (array), pas un objet dédié, pour
     * rester facile à sérialiser tel quel dans un futur prompt LLM.
     *
     * @param PhenyxAssistantQuery $query
     * @return array
     */
    public function buildContextSnapshot(PhenyxAssistantQuery $query) {

        $snapshot = [
            'controller' => [
                'name'   => $query->controllerName,
                'type'   => $query->controllerType,
                'plugin' => $query->pluginName,
            ],
            'employee' => [
                'id'         => $query->idEmployee,
                'id_profile' => $query->idProfile,
            ],
            'backTab' => $this->getBackTabInfo($query->controllerName),
            'entity'  => null,
            'layers'  => $this->describeLayers(),
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
