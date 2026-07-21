<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Class PhenyxAssistantLayer
 *
 * Classe abstraite que chaque plugin ayant une forte présence BO (un
 * contrôleur admin, cf. project_instructions) peut étendre pour exposer sa
 * propre "couche" dans l'assistant — un peu comme PluginAdminControllerCore
 * sert de base aux contrôleurs admin de plugin, en repartant du même principe
 * d'extension par convention plutôt que par configuration XML.
 *
 * Deux façons de brancher une couche, au choix de l'auteur du plugin :
 *
 * 1) Enregistrement explicite (recommandé si le plugin a un état/contexte
 *    riche à réutiliser) : dans le constructeur du plugin ou d'un de ses
 *    contrôleurs, appeler
 *      PhenyxAssistant::getInstance()->registerLayer(new MyPluginAssistantLayer());
 *
 * 2) Hook générique (recommandé pour un plugin plus simple, cohérent avec le
 *    reste du framework) : le plugin appelle $this->registerHook('actionAssistantAnswer')
 *    à l'install, puis implémente hookActionAssistantAnswer($params) qui
 *    instancie sa Layer et lui délègue la réponse. PhenyxAssistant agrège les
 *    deux sources de la même façon.
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
abstract class PhenyxAssistantLayer {

    /** @var string Nom du plugin propriétaire (doit correspondre à eph_plugin.name) */
    public $pluginName;

    /**
     * Classes PhenyxObjectModel gérées par ce plugin, à déclarer par les
     * sous-classes. Sert à l'auto-description (describe()) : plutôt que de
     * maintenir une doc statique, on relit PhenyxObjectModel::getDefinition()
     * pour chaque classe et on en tire une liste de champs/capacités.
     *
     * Exemple dans une sous-classe :
     *   protected $entities = ['Product', 'Category'];
     *
     * @var string[]
     */
    protected $entities = [];

    /**
     * Exemples de questions que cette couche sait traiter, affichés dans le
     * widget du chatbot pour amorcer la conversation (utile tant qu'il n'y a
     * pas de LLM pour deviner l'intention à partir de rien).
     *
     * @var string[]
     */
    protected $sampleQuestions = [];

    public function __construct($pluginName = null) {

        $this->pluginName = $pluginName;
    }

    /**
     * Tente de répondre à la question. Retourne null si cette couche n'a rien
     * de pertinent à dire (PhenyxAssistant continuera d'interroger les autres
     * couches / le provider).
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer|null
     */
    abstract public function answer(PhenyxAssistantQuery $query);

    /**
     * Auto-description de la couche, utilisée pour construire le contexte
     * (grounding) donné au provider — et plus tard au LLM. Les sous-classes
     * peuvent surcharger pour ajouter des capacités au-delà des entités
     * déclarées dans $this->entities.
     *
     * @return array
     */
    public function describe() {

        return [
            'plugin'          => $this->pluginName,
            'entities'        => $this->describeEntities(),
            'sampleQuestions' => $this->sampleQuestions,
        ];
    }

    /**
     * Construit la description des entités déclarées dans $this->entities en
     * s'appuyant sur PhenyxObjectModel::getDefinition() — aucune doc à
     * maintenir à la main, la définition du framework fait foi.
     *
     * @return array
     */
    protected function describeEntities() {

        $entities = [];

        foreach ($this->entities as $className) {

            if (!class_exists($className)) {
                continue;
            }

            try {
                $definition = PhenyxObjectModel::getDefinition($className);
            } catch (\Throwable $e) {
                // Une entité mal déclarée ne doit jamais faire planter l'assistant.
                PhenyxLogger::addLog(
                    sprintf('PhenyxAssistantLayer: impossible de décrire %s (%s)', $className, $e->getMessage()),
                    2,
                    null,
                    static::class
                );
                continue;
            }

            $fields = [];

            if (!empty($definition['fields']) && is_array($definition['fields'])) {

                foreach ($definition['fields'] as $field => $data) {
                    $fields[] = [
                        'name'     => $field,
                        'type'     => isset($data['type']) ? $data['type'] : null,
                        'required' => !empty($data['required']),
                        'lang'     => !empty($data['lang']),
                    ];
                }

            }

            $entities[] = [
                'class'     => $className,
                'table'     => isset($definition['table']) ? $definition['table'] : null,
                'multilang' => !empty($definition['multilang']),
                'fields'    => $fields,
            ];
        }

        return $entities;
    }

    /**
     * Découpe et normalise un texte libre en tokens comparables : accents
     * retirés, minuscule, ponctuation neutralisée. Sert de base à
     * matchesConcept() pour un matching tolérant à l'ordre des mots — cf.
     * retour utilisateur du 2026-07-21 : "marque créer un client" doit
     * matcher aussi bien que "comment créer un client", la structure de la
     * phrase varie plus d'un employé à l'autre que le vocabulaire clé.
     *
     * @param string $text
     * @return string[]
     */
    protected function tokenize($text) {

        $normalized = Tools::strtolower(Tools::replaceAccentedChars((string) $text));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized);
        $tokens = array_filter(explode(' ', trim((string) $normalized)), function ($token) {
            return $token !== '';
        });

        return array_values($tokens);
    }

    /**
     * Est-ce que l'un des tokens correspond — exactement ou à une faute de
     * frappe près — à l'une des variantes d'un même concept ? Permet à une
     * couche de reconnaître "creer"/"cree"/"creation"/"creaer" (typo) comme
     * la même intention sans devoir lister toutes les phrases possibles.
     *
     * @param string[] $tokens   Résultat de tokenize()
     * @param string[] $variants Formes déjà normalisées (minuscule, sans accent)
     * @return bool
     */
    protected function matchesConcept(array $tokens, array $variants) {

        foreach ($tokens as $token) {

            foreach ($variants as $variant) {

                if ($token === $variant || $this->isCloseEnough($token, $variant)) {
                    return true;
                }

            }

        }

        return false;
    }

    /**
     * Tolérance aux fautes de frappe via la distance de Levenshtein, avec un
     * seuil proportionnel à la longueur du mot : un seuil fixe serait soit
     * trop laxiste sur les mots courts (faux positifs), soit trop strict sur
     * les longs.
     *
     * @param string $token
     * @param string $variant
     * @return bool
     */
    protected function isCloseEnough($token, $variant) {

        $shortest = min(strlen($token), strlen($variant));
        $lenDiff = abs(strlen($token) - strlen($variant));

        if ($shortest < 4 || $lenDiff > 2) {
            return false;
        }

        $threshold = $shortest <= 6 ? 1 : 2;

        return levenshtein($token, $variant) <= $threshold;
    }

}
