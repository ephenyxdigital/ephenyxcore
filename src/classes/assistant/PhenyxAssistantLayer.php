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
     * Normalise un texte libre (minuscule, accents retirés) sans le découper
     * — sert de base à tokenize() et au repli "sous-chaîne" de
     * variantMatches() pour les écritures sans espaces entre les mots
     * (chinois...).
     *
     * @param string $text
     * @return string
     */
    protected function normalizeText($text) {

        return Tools::strtolower(Tools::replaceAccentedChars((string) $text));
    }

    /**
     * Découpe un texte libre normalisé en tokens comparables. Sert de base à
     * matchesConcept() pour un matching tolérant à l'ordre des mots — cf.
     * retour utilisateur du 2026-07-21 : "marque créer un client" doit
     * matcher aussi bien que "comment créer un client", la structure de la
     * phrase varie plus d'un employé à l'autre que le vocabulaire clé.
     *
     * Important : `\p{L}\p{N}` (Unicode, modificateur /u) plutôt que
     * `a-z0-9` — sinon le russe, l'arabe, l'hébreu, le grec ou le chinois
     * seraient entièrement vidés avant même la comparaison (cf. retour du
     * 2026-07-21 : la boutique a onze langues installées, pas seulement des
     * langues latines).
     *
     * @param string $text
     * @return string[]
     */
    protected function tokenize($text) {

        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $this->normalizeText($text));
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

    /**
     * Est-ce qu'un mot-clé de topic (déjà normalisé) est présent dans la
     * question, soit comme token (exact ou à une faute de frappe près, cf.
     * isCloseEnough()), soit comme simple sous-chaîne du texte normalisé
     * complet. Le repli en sous-chaîne sert à deux cas que le découpage par
     * tokens ne couvre pas :
     *  - le chinois (et les écritures sans espaces entre les mots), où
     *    tokenize() renvoie un seul token pour toute une phrase ;
     *  - les mots composés allemands ("Kundenliste" contient "kunde").
     *
     * @param string   $variant        Mot-clé normalisé (une "unité" du champ keywords)
     * @param string[] $tokens         Résultat de tokenize() sur la question
     * @param string   $normalizedText Résultat de normalizeText() sur la question (non découpé)
     * @return bool
     */
    protected function variantMatches($variant, array $tokens, $normalizedText) {

        foreach ($tokens as $token) {

            if ($token === $variant || $this->isCloseEnough($token, $variant)) {
                return true;
            }

        }

        return mb_strlen($variant) >= 2 && mb_stripos($normalizedText, $variant) !== false;
    }

    /**
     * Moteur générique pour les couches qui préfèrent stocker leur contenu
     * en base (cf. PhenyxAssistantTopic) plutôt qu'en constantes PHP — seule
     * façon réaliste de couvrir plusieurs langues sans dupliquer toute la
     * logique de matching par langue dans chaque couche. Cf. retour
     * utilisateur du 2026-07-21 : un employé anglophone ou germanophone doit
     * pouvoir poser sa question dans sa langue.
     *
     * Chaque token de la question est comparé (cf. matchesConcept()) aux
     * mots-clés de chaque topic actif dans la langue de l'employé ; le topic
     * qui recoupe le plus de tokens gagne. Si aucun topic n'existe pour
     * cette langue (traduction pas encore faite), on retombe sur la langue
     * par défaut du shop plutôt que de ne rien répondre.
     *
     * @param PhenyxAssistantQuery $query
     * @param string               $entityClass Cf. PhenyxAssistantTopic::$entity_class
     * @return PhenyxAssistantAnswer|null
     */
    protected function matchBestTopic(PhenyxAssistantQuery $query, $entityClass) {

        $tokens = $this->tokenize($query->text);

        if (!$tokens) {
            return null;
        }

        $idLang = $this->resolveLangId($query->isoCode);
        $topics = PhenyxAssistantTopic::getActiveForLang($idLang, $entityClass);

        if (!$topics) {
            $fallbackLang = (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');

            if ($fallbackLang && $fallbackLang !== $idLang) {
                $topics = PhenyxAssistantTopic::getActiveForLang($fallbackLang, $entityClass);
            }

        }

        if (!$topics) {
            return null;
        }

        $normalizedText = $this->normalizeText($query->text);
        $best = null;
        $bestScore = 0;

        foreach ($topics as $topic) {

            $variants = preg_split('/\s+/u', trim((string) $topic['keywords']));
            $score = 0;

            foreach ($variants as $variant) {

                if ($variant !== '' && $this->variantMatches($variant, $tokens, $normalizedText)) {
                    $score++;
                }

            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $topic;
            }

        }

        if ($best === null || $bestScore < 1) {
            return null;
        }

        $answer = PhenyxAssistantAnswer::text((string) $best['answer'], $this->pluginName, (float) $best['confidence']);
        $answer->suggestedAction = !empty($best['suggested_action']) ? json_decode($best['suggested_action'], true) : null;
        $answer->quickReplies = !empty($best['quick_replies']) ? (array) json_decode($best['quick_replies'], true) : [];

        return $answer;
    }

    /**
     * Résout le code langue de la requête (cf. PhenyxAssistantQuery::$isoCode,
     * déjà peuplé depuis Context::getContext()->language) en id_lang, avec
     * repli défensif sur la langue par défaut du shop — jamais d'appel direct
     * à Language::getIdByIso() avec un code non validé, qui fait un die() en
     * cas d'ISO invalide.
     *
     * @param string|null $isoCode
     * @return int
     */
    protected function resolveLangId($isoCode) {

        if ($isoCode && Validate::isLanguageIsoCode($isoCode)) {
            $idLang = (int) Language::getIdByIso($isoCode);

            if ($idLang) {
                return $idLang;
            }

        }

        return (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');
    }

}
