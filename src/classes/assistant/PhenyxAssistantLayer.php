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

    /**
     * Étages de résolution possibles d'une question, du plus exact au moins
     * exact. Reportés dans PhenyxAssistantAnswer::$raw['stage'] puis dans
     * eph_phenyx_assistant_log.match_stage, et utilisés par
     * PhenyxAssistantRuleProvider::generate() pour arbitrer entre les couches
     * (une résolution exacte doit toujours battre une approximation, quel que
     * soit le plugin qui la produit).
     *
     * Aujourd'hui seuls STAGE_KEYWORD et STAGE_NONE sont réellement produits.
     * Les trois autres sont déclarés dès maintenant pour que le log et
     * l'arbitrage n'aient pas à changer de vocabulaire quand les étages
     * correspondants arriveront (cf. assistant-filtrage-etage-plan.md) :
     *  - STAGE_SCREEN    : liage déterministe écran -> topic ;
     *  - STAGE_ALIAS     : signature de question déjà apprise ;
     *  - STAGE_AMBIGUOUS : deux topics trop proches, on demande à l'employé.
     */
    const STAGE_SCREEN = 'screen';

    const STAGE_ALIAS = 'alias';

    const STAGE_KEYWORD = 'keyword';

    const STAGE_AMBIGUOUS = 'ambiguous';

    const STAGE_NONE = 'none';

    /**
     * Topic servi quand le meilleur candidat vise un écran hors du périmètre de
     * l'employé. Cf. buildRestrictedAnswer().
     */
    const RESTRICTED_TOPIC_CODE = 'core.access.restricted';

    /**
     * Topic servi quand l'employé clique une puce désignant un écran présent dans
     * son menu mais pas encore documenté. Cf.
     * PhenyxAssistant::answerForController().
     */
    const UNDOCUMENTED_TOPIC_CODE = 'core.screen.undocumented';

    /**
     * Topic servi quand la puce cliquée désigne non pas un écran mais un
     * REGROUPEMENT du menu — « Clients », « Données locales »… — c'est-à-dire un
     * onglet intermédiaire qui n'a pas d'écran propre mais porte des enfants.
     *
     * Sans lui, ces parents tombaient dans UNDOCUMENTED_TOPIC_CODE et
     * l'assistant répondait « je ne sais pas t'expliquer cet écran, mais je peux
     * t'y conduire » — deux fois faux : ce n'est pas un écran, et il n'y a nulle
     * part où conduire. Cf. PhenyxAssistant::answerForController().
     */
    const MENU_GROUP_TOPIC_CODE = 'core.screen.menugroup';

    /**
     * Jeton remplacé par le NOMBRE de familles de menu dans la réponse du tour
     * racine. Cf. buildTopicAnswer().
     */
    const FAMILY_COUNT_TOKEN = '{nbFamilies}';

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

    /**
     * Métadonnées du dernier matching tenté par cette couche (cf. les
     * constantes STAGE_* et findBestTopic()), y compris quand il n'a abouti à
     * aucune réponse. Lu par PhenyxAssistant::collectCandidates() pour que le
     * log garde une trace du "presque-match" au lieu d'enregistrer un échec
     * muet.
     *
     * @var array|null
     */
    protected $lastMatchMeta;

    public function __construct($pluginName = null) {

        $this->pluginName = $pluginName;
    }

    /**
     * @return array|null Cf. $lastMatchMeta et PhenyxAssistantAnswer::$raw
     */
    public function getLastMatchMeta() {

        return $this->lastMatchMeta;
    }

    /**
     * Accès public à questionSignature(), pour PhenyxAssistant::resolve() qui
     * doit écrire un alias avec EXACTEMENT la même normalisation que celle qui le
     * relira. Deux implémentations divergentes de la signature produiraient des
     * alias jamais retrouvés — un bug silencieux et coûteux à diagnostiquer, d'où
     * ce point d'entrée plutôt qu'une duplication de l'algorithme.
     *
     * @param string $text
     * @return string
     */
    public function publicQuestionSignature($text) {

        return $this->questionSignature($text);
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
     * Mots interrogatifs et de liaison sans aucune valeur discriminante, retirés
     * de la SIGNATURE d'une question (cf. questionSignature()).
     *
     * ⚠️ À ne pas confondre avec $lowSignalKeywords : ceux-là sont des verbes
     * d'action BO ("gérer", "créer") qui doivent compter UN PEU dans le score
     * (d'où le poids réduit), parce qu'ils portent quand même une intention.
     * Ceux-ci sont du bruit de formulation pur : "comment", "où", "je",
     * "peut-on" ne distinguent rien du tout et empêcheraient deux formulations
     * de la même question de produire la même signature.
     *
     * Liste bornée aux langues où le besoin est concret (FR/EN) : son absence
     * pour une langue ne casse rien, elle dégrade seulement la qualité des
     * signatures dans cette langue — un alias appris en russe restera exact,
     * simplement moins généralisable.
     */
    protected static $questionStopWords = [
        // fr
        'comment', 'ou', 'pourquoi', 'quand', 'quel', 'quelle', 'quels', 'quelles',
        'que', 'quoi', 'qui', 'est', 'ce', 'se', 'je', 'tu', 'il', 'elle', 'on',
        'nous', 'vous', 'le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'a',
        'au', 'aux', 'en', 'et', 'pour', 'dans', 'sur', 'avec', 'sans', 'par',
        'peut', 'peux', 'puis', 'dois', 'doit', 'faut', 'faire', 'fais', 'arrive',
        'aimerais', 'voudrais', 'veux', 'besoin', 'svp', 'merci',
        // en
        'how', 'where', 'why', 'when', 'which', 'what', 'who', 'is', 'are', 'the',
        'an', 'do', 'does', 'did', 'can', 'could', 'should', 'would', 'to', 'for',
        'in', 'on', 'with', 'without', 'by', 'my', 'need', 'want', 'please',
        'thanks',
    ];

    /**
     * Signature comparable d'une question libre, servant de clé à la table
     * d'alias (étage 1) : tokens normalisés, mots interrogatifs retirés,
     * dédoublonnés, TRIÉS.
     *
     * Le tri est volontaire : "comment créer un client" et "créer un client
     * comment" doivent produire la même signature. L'ordre des mots ne porte pas
     * d'information utile ici — c'est déjà le constat qui avait motivé le
     * matching tolérant à l'ordre (retour Jeff du 2026-07-21 sur "marque créer un
     * client"), et figer l'ordre ferait rater des alias pourtant déjà appris.
     *
     * @param string $text
     * @return string Chaîne vide si la question ne contient que du bruit
     */
    protected function questionSignature($text) {

        $tokens = array_diff($this->tokenize($text), static::$questionStopWords);
        $tokens = array_unique($tokens);
        sort($tokens);

        return implode(' ', $tokens);
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
     * ⚠️ Retour Jeff 2026-07-23 (bug : la question "Gestion de la société"
     * faisait gagner le topic core.tour.tools à cause d'un faux positif —
     * "gestion" et "session" sont tous deux des mots de 7 lettres à
     * distance de Levenshtein 2, donc "close enough" pour le seuil ci-dessous
     * bien qu'ils n'aient strictement rien à voir. On exige maintenant que
     * le premier caractère soit identique avant même de calculer la
     * distance : une vraie faute de frappe touche presque toujours le
     * milieu/la fin d'un mot, jamais sa première lettre — ça élimine ce
     * genre de collision entre mots sans rapport tout en gardant la
     * tolérance utile ("crer"/"creer", "modifer"/"modifier"...).
     *
     * @param string $token
     * @param string $variant
     * @return bool
     */
    protected function isCloseEnough($token, $variant) {

        if ($token === '' || $variant === '' || $token[0] !== $variant[0]) {
            return false;
        }

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

        // Remis à zéro dès l'entrée : les sorties anticipées ci-dessous (question
        // vide, aucun topic pour la langue) ne passent pas par findBestTopic() et
        // laisseraient sinon les métadonnées de l'appel PRÉCÉDENT en place —
        // PhenyxAssistant::collectCandidates() les lirait alors comme si elles
        // décrivaient la question courante.
        $this->lastMatchMeta = null;

        $tokens = $this->tokenize($query->text);

        if (!$tokens) {
            return null;
        }

        $idLang = $this->resolveLangId($query->isoCode);
        $sourceController = isset($query->extra['sourceController']) ? (string) $query->extra['sourceController'] : null;
        $sourceType = isset($query->extra['sourceType']) ? (string) $query->extra['sourceType'] : null;
        $isContextual = !empty($query->extra['isContextual']);

        /* ------------------------------------------------------------------
         * ÉTAGE 0 — routage déterministe par écran
         *
         * Une question "contextuelle" n'est pas une question : c'est le libellé
         * de l'écran, envoyé automatiquement par
         * notifyPhenyxAssistantControllerOpened(). Cet ensemble est fini et déjà
         * lié sans ambiguïté à un contrôleur, donc résolu par un lookup, jamais
         * par du scoring de texte — cf. les quatre faux-positifs des 22, 23 et
         * 24/07/2026, tous issus de ce chemin.
         * ------------------------------------------------------------------ */
        if ($isContextual && PhenyxAssistantTopic::hasAnyScreenBinding()) {
            $bound = PhenyxAssistantTopic::getBoundToScreen($sourceController, $sourceType, $idLang, $entityClass);

            if ($bound !== null) {
                return $this->buildTopicAnswer($bound, $query, self::STAGE_SCREEN, 0.0);
            }

            // Écran sans topic lié : SILENCE ASSUMÉ. On ne retombe pas sur le
            // scoring du libellé — c'est exactement ce qui produisait les
            // faux-positifs, et Jeff a explicitement préféré "je ne trouve pas
            // encore de réponse" à un rapprochement approximatif (2026-07-23).
            // Les écrans concernés remontent dans la grille "Écrans non
            // documentés", triés par volume de questions réelles.
            $this->lastMatchMeta = [
                'stage'         => self::STAGE_NONE,
                'score'         => 0.0,
                'idTopic'       => null,
                'runnerUpTopic' => null,
                'runnerUpScore' => null,
            ];

            return null;
        }

        /* ------------------------------------------------------------------
         * ÉTAGE 1 — alias appris
         *
         * Couvre les écarts de VOCABULAIRE ("fiche client" vs mot-clé
         * "utilisateur"), qu'aucune distance d'édition ne peut rapprocher.
         * Lookup exact sur une signature normalisée : coût constant quel que
         * soit le nombre de topics.
         * ------------------------------------------------------------------ */
        $signature = $this->questionSignature($query->text);
        $aliased = PhenyxAssistantAlias::resolveTopicRow($signature, $idLang, $entityClass);

        if ($aliased !== null) {

            // Même contrôle qu'à l'étage 2 plus bas : un alias appris par un
            // employé mieux doté ne doit pas devenir une porte dérobée vers un
            // écran que celui-ci n'a pas le droit de voir. Les alias sont
            // partagés, les droits non.
            if (!PhenyxAssistantTopic::isTopicAllowed($aliased['id_phenyx_assistant_topic'])) {
                return $this->buildRestrictedAnswer($query);
            }

            return $this->buildTopicAnswer($aliased, $query, self::STAGE_ALIAS, 0.0);
        }

        $topics = PhenyxAssistantTopic::getActiveForLang($idLang, $entityClass);

        if (!$topics) {
            $fallbackLang = (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');

            if ($fallbackLang && $fallbackLang !== $idLang) {
                $topics = PhenyxAssistantTopic::getActiveForLang($fallbackLang, $entityClass);
                // La suite (alias appris, désambiguïsation) doit travailler dans
                // la langue réellement utilisée, sinon on écrirait des alias
                // rattachés à une langue dont l'employé n'a rien vu.
                $idLang = $fallbackLang;
            }

        }

        if (!$topics) {
            return null;
        }

        $this->topicCount = count($topics);

        // Pondération IDF : la requête de document frequency n'est lancée QUE si
        // le corpus atteint le seuil au-delà duquel keywordWeight() s'en sert.
        // En dessous, elle était un aller-retour perdu vers la base CRM
        // DISTANTE à chaque question, pour un résultat jamais lu — cause
        // directe de la lenteur signalée par Jeff le 2026-07-25.
        if ($this->topicCount >= self::IDF_MIN_CORPUS) {
            $this->documentFrequency = PhenyxAssistantTopic::getDocumentFrequency($idLang, $topics);
        } else {
            $this->documentFrequency = [];
        }

        // Retour Jeff 2026-07-23 : les topics "core.tour" / "core.tour.*"
        // sont des résumés volontairement larges d'un GROUPE de menus (cf.
        // le bouton "Faire un tour..."), pas une documentation précise d'un
        // écran donné. Un premier essai les excluait entièrement du message
        // contextuel automatique, mais ça perdait aussi les cas où un topic
        // tour est un TRÈS BON match spécifique (ex. "Traductions du
        // back-office en Arabic" recoupe "back"+"office"+"traductions" dans
        // core.tour.administration — 3 mots-clés distincts, pas une
        // coïncidence) : sans lui, un topic sans rapport gagnait par défaut
        // (ex. core.contentanywhere.list sur le seul mot "office"), ce qui
        // est pire qu'avant. Le bon critère n'est donc pas "topic tour ou
        // pas", mais "match fort ou faible" : cf. findBestTopic(), un topic
        // tour ne peut gagner un round-trip automatique que s'il atteint un
        // score renforcé (self::TOUR_CONTEXTUAL_MIN_SCORE), pas le seuil
        // normal — sinon on préfère "je ne trouve pas encore de réponse" à
        // un rapprochement approximatif (ex. "Gestion des Traductions" ~
        // "gestion" seul + "traductions" ne suffit plus).
        /* ------------------------------------------------------------------
         * ÉTAGES 2 et 3 — scoring par mots-clés pondérés, puis tolérance aux
         * fautes de frappe (cf. findBestTopic() / isCloseEnough()).
         * ------------------------------------------------------------------ */
        $meta = null;
        $best = $this->findBestTopic($query->text, $topics, $isContextual, $meta);

        // Métadonnées de matching capturées IMMÉDIATEMENT après cet appel : le
        // filterScreenDependentQuickReplies() plus bas rappelle findBestTopic()
        // pour chaque quick reply et écraserait $meta si on attendait.
        $matchMeta = $meta;

        // Conservé même quand la couche ne répond pas : un "aucune réponse" est
        // une information utile, pas un trou. Le meilleur score atteint (souvent
        // > 0 mais sous le seuil) est ce qui permettra de calibrer les seuils
        // sur des données réelles plutôt qu'à l'intuition — cf. la grille
        // "Questions à arbitrer" filtrée sur match_stage = 'none'.
        // Récupéré par PhenyxAssistant::collectCandidates() via
        // getLastMatchMeta().
        $this->lastMatchMeta = $matchMeta;

        if ($best === null) {
            return null;
        }

        /*
         * DROITS — le meilleur candidat documente-t-il un écran que cet employé
         * voit dans son menu ?
         *
         * ⚠️ Trou signalé par Jeff le 2026-07-26 : jusqu'ici les étages 1 à 3
         * ignoraient totalement le profil. Seules les PUCES étaient filtrées, si
         * bien qu'un employé sans accès à « Société » tapant « société » recevait
         * la description complète de la famille. Le menu lui cachait l'écran, et
         * l'assistant le lui décrivait.
         *
         * Contrôlé ICI, à la sélection, et non en amont sur la liste des
         * candidats : c'est ce qui permet de distinguer « rien ne correspond » de
         * « ça correspond mais ce n'est pas pour toi », et donc de le DIRE plutôt
         * que de servir un candidat moins bon en faisant semblant. Un topic sans
         * liage d'écran reste accessible à tous — cf.
         * PhenyxAssistantTopic::isTopicAllowed().
         */
        if (!PhenyxAssistantTopic::isTopicAllowed($best['id_phenyx_assistant_topic'])) {
            return $this->buildRestrictedAnswer($query);
        }

        /* ------------------------------------------------------------------
         * ÉTAGE 4 — désambiguïser plutôt que deviner
         *
         * Deux topics trop proches : jusqu'ici c'est l'ordre d'insertion en base
         * qui tranchait, autrement dit le hasard (cause directe du bug CMS du
         * 2026-07-22 et du bug mailtemplates du 2026-07-24). On demande
         * maintenant à l'employé, et son clic écrit l'alias de l'étage 1 — la
         * désambiguïsation EST le mécanisme d'apprentissage, pas une corvée
         * supplémentaire.
         * ------------------------------------------------------------------ */
        if (!empty($matchMeta['ambiguousWith'])) {
            return $this->buildAmbiguityAnswer($best, $matchMeta, $query, $topics);
        }

        return $this->buildTopicAnswer($best, $query, self::STAGE_KEYWORD, (float) $matchMeta['score'], $matchMeta, $topics);
    }

    /**
     * Fabrique une réponse à partir d'une ligne de topic, quel que soit l'étage
     * qui l'a désignée. Factorisé parce que les étages 0, 1 et 2 produisent
     * exactement la même forme de réponse — seules les métadonnées changent.
     *
     * @param array                $topic      Ligne brute (getActiveForLang() / getBoundToScreen() / resolveTopicRow())
     * @param PhenyxAssistantQuery $query
     * @param string               $stage      Cf. les constantes STAGE_*
     * @param float                $score
     * @param array|null           $meta       Métadonnées déjà calculées, le cas échéant
     * @param array|null           $topics     Tous les topics de la langue, s'ils sont déjà chargés
     * @return PhenyxAssistantAnswer
     */
    protected function buildTopicAnswer(array $topic, PhenyxAssistantQuery $query, $stage, $score = 0.0, $meta = null, $topics = null) {

        $idTopic = isset($topic['id_phenyx_assistant_topic']) ? (int) $topic['id_phenyx_assistant_topic'] : null;

        if (!is_array($meta)) {
            $meta = [
                'stage'         => $stage,
                'score'         => (float) $score,
                'idTopic'       => $idTopic,
                'runnerUpTopic' => null,
                'runnerUpScore' => null,
            ];
        }

        $meta['stage'] = $stage;
        $meta['idTopic'] = $idTopic;
        $this->lastMatchMeta = $meta;

        $answer = PhenyxAssistantAnswer::text((string) $topic['answer'], $this->pluginName, (float) $topic['confidence']);
        $answer->idTopic = $idTopic;
        // Exposé pour le hook actionAssistantAnswerAugment : c'est sur ce code
        // que les plugins reconnaissent le topic qu'ils veulent enrichir.
        $answer->topicCode = isset($topic['code']) ? (string) $topic['code'] : null;
        $answer->raw = $meta;
        $answer->suggestedAction = !empty($topic['suggested_action']) ? json_decode($topic['suggested_action'], true) : null;

        /*
         * Faute d'action déclarée, proposer d'ouvrir l'écran documenté par ce
         * topic — déduit du liage (cf.
         * PhenyxAssistantTopic::getBoundControllerForTopic()), pas déclaré deux
         * fois.
         *
         * Sans effet visible sur le round-trip contextuel de l'ÉTAGE 0 : l'employé
         * y est déjà sur l'écran, et renderPhenyxAssistantSuggestedAction() filtre
         * le bouton quand action.controller vaut current_controller. Utile en
         * revanche sur une question tapée ou une quick reply, où il évite de
         * décrire un écran sans y emmener.
         */
        if (empty($answer->suggestedAction) && $idTopic) {
            $screenAction = PhenyxAssistantTopic::getScreenActionForTopic(
                $idTopic,
                $this->resolveLangId($query->isoCode)
            );

            if ($screenAction) {
                $answer->suggestedAction = $screenAction;
            }

        }

        // Le bouton porte le nom du MENU, jamais le nom de classe PHP — vaut pour
        // l'action déclarée par le topic comme pour celle déduite ci-dessus. Cf.
        // PhenyxAssistantTopic::decorateActionLabels() pour le détail.
        $answer->suggestedAction = PhenyxAssistantTopic::decorateActionLabels(
            $answer->suggestedAction,
            $this->resolveLangId($query->isoCode)
        );

        $quickReplies = !empty($topic['quick_replies']) ? (array) json_decode($topic['quick_replies'], true) : [];

        /*
         * Quick replies muettes tant que l'écran documenté n'est pas celui qu'a
         * l'employé sous les yeux — même règle et mêmes raisons que dans
         * PhenyxAssistant::answerForTopic(), appliquée ici au flux des questions
         * tapées. Un topic sans liage d'écran (tour, rubrique, notion générale)
         * n'est pas concerné et garde ses propositions.
         *
         * Placé AVANT filterScreenDependentQuickReplies() : quand la règle
         * s'applique, elle vide la liste et court-circuite du même coup le
         * chargement paresseux de tous les topics — un aller-retour de moins vers
         * la base CRM distante.
         */
        if ($quickReplies && $idTopic) {
            $boundController = PhenyxAssistantTopic::getBoundControllerForTopic($idTopic);
            $currentScreen = isset($query->extra['sourceController'])
            ? (string) $query->extra['sourceController']
            : '';

            if ($boundController && $boundController !== $currentScreen) {
                $quickReplies = [];
            }

        }

        // Retour Jeff 2026-07-22 : certaines quick replies mènent à une action
        // "physique" sur l'écran courant (menu contextuel simulé, bouton mis en
        // évidence — cf. core.grid.contextmenu / core.grid.fields et
        // renderPhenyxAssistantSuggestedAction() dans ephenyx.js). Elles n'ont
        // aucun intérêt si l'employé n'est pas sur une liste au moment où il
        // pose la question (ex: demande générale depuis le tableau de bord) —
        // on les retire dans ce cas plutôt que de proposer une puce qui ne
        // ferait rien de concret une fois cliquée.
        $sourceType = isset($query->extra['sourceType']) ? (string) $query->extra['sourceType'] : null;

        // Le filtrage n'a d'effet QUE hors d'un écran de liste : sur une liste,
        // filterScreenDependentQuickReplies() conserve tout et se réduit à une
        // boucle de findBestTopic() pour rien. On court-circuite donc, ce qui
        // évite en prime le chargement de tous les topics juste en dessous —
        // deuxième aller-retour vers la base CRM distante économisé sur le
        // chemin le plus fréquent (retour Jeff 2026-07-25 : popup lente).
        if ($quickReplies && $sourceType !== 'list') {
            // Chargement PARESSEUX de la liste complète des topics : les étages 0
            // et 1 n'en ont pas besoin pour répondre, seulement pour filtrer les
            // quick replies. Inutile de payer cette requête quand le topic
            // retenu n'en propose aucune.
            if (!is_array($topics)) {
                $topics = PhenyxAssistantTopic::getActiveForLang(
                    $this->resolveLangId($query->isoCode),
                    null
                );
            }

            $quickReplies = $this->filterScreenDependentQuickReplies($quickReplies, $topics, $sourceType);
        }

        $answer->quickReplies = $quickReplies;

        /*
         * TOUR : les puces sont dérivées du MENU RÉEL au lieu d'être stockées.
         *
         * Cf. PhenyxAssistantTopic::getMenuFamilyTopics() pour le pourquoi
         * détaillé — en résumé, les sept familles listées en dur dans
         * `quick_replies` de core.tour deviennent fausses dès qu'un plugin
         * remanie le menu, et deux d'entre elles pointaient vers des familles
         * disparues du niveau 1 sur ephenyx.io.
         *
         * Traitement particulier par convention de `code`, comme
         * isTourOnlyTopic() le fait déjà pour le seuil contextuel : c'est le
         * topic RACINE du tour, pas ses déclinaisons core.tour.*.
         *
         * Repli conservé volontairement : sans aucun liage `source_type = 'menu'`
         * en base, on garde les puces stockées. Un site qui n'a pas encore joué
         * le seeder de liages conserve donc exactement le comportement actuel,
         * plutôt qu'un tour vide.
         */
        if ($this->isTourRootTopic($topic)) {
            $families = PhenyxAssistantTopic::getMenuFamilyTopics($this->resolveLangId($query->isoCode));

            if ($families) {
                $answer->topicChips = $families;
                // Les puces stockées feraient doublon avec la liste dynamique.
                $answer->quickReplies = [];

                /*
                 * Le NOMBRE de familles est écrit dans la réponse (idée de Jeff,
                 * 2026-07-26). Il ne peut pas l'être dans le wiki : il dépend des
                 * plugins installés ET du profil de l'employé — deux employés du
                 * même site n'ont pas le même compte.
                 *
                 * Jeton nommé plutôt que %d et sprintf : les réponses contiennent
                 * des pourcentages rédigés, et un sprintf sur du texte libre casse
                 * au premier « 100 % » qu'un rédacteur écrira. Un topic dont la
                 * traduction ne porte pas le jeton reste simplement inchangé.
                 */
                $answer->text = str_replace(
                    self::FAMILY_COUNT_TOKEN,
                    (string) count($families),
                    (string) $answer->text
                );
            }

        } else {
            /*
             * FAMILLE DE MENU : même dérivation, un cran plus bas.
             *
             * Cf. PhenyxAssistantTopic::getMenuChildrenTopics() pour la mesure qui
             * a motivé ceci — le tour de « Société » ne montrait que les trois
             * écrans pour lesquels un plugin avait écrit un hook, alors que trois
             * autres avaient déjà un topic et un liage.
             *
             * On ne teste pas le `code` du topic ici, contrairement à la racine du
             * tour : c'est la présence d'un liage `source_type = 'menu'` qui fait
             * d'un topic une famille. La donnée décide, pas une convention de
             * nommage — un plugin peut ainsi déclarer sa propre famille sans
             * qu'aucun `code` soit connu du cœur.
             *
             * Repli inchangé : sans liage 'menu', $parent est null et rien ne se
             * passe. Un topic ordinaire garde donc exactement son comportement.
             */
            $parent = PhenyxAssistantTopic::getMenuBindingForTopic($answer->idTopic);

            if ($parent) {
                $children = PhenyxAssistantTopic::getMenuChildrenTopics(
                    $parent,
                    $this->resolveLangId($query->isoCode)
                );

                if ($children) {
                    // Les puces éventuellement déjà posées gardent leur place, à
                    // la suite : la dérivation complète, elle n'écrase pas.
                    $answer->topicChips = PhenyxAssistantTopic::mergeTopicChips(
                        $children,
                        (array) $answer->topicChips
                    );
                    // Idem racine du tour : les quick replies stockées
                    // énuméraient à la main ce qu'on vient de dériver.
                    $answer->quickReplies = [];

                    /*
                     * ⚠️ Une famille n'expose AUCUN bouton d'ouverture — ni déclaré,
                     * ni dérivé (règle du 2026-07-26, cf. le commentaire de
                     * core.tour.company dans install_assistant_topics.php).
                     *
                     * J'avais d'abord dérivé un bouton par écran enfant, pour
                     * combler l'asymétrie entre « Société » (trois boutons rédigés)
                     * et « Configuration de l'ERP » (aucun). Mauvaise correction :
                     * elle alignait par le haut alors qu'il fallait aligner par le
                     * bas. Chaque écran a déjà sa puce, et son bouton apparaît sur
                     * SA réponse ; doubler chaque puce d'un bouton n'ajoutait aucun
                     * chemin et encombrait la popup.
                     *
                     * On neutralise donc aussi une action éventuellement déclarée
                     * par le topic : sans ça, un vieux contenu non repris
                     * continuerait d'afficher des boutons sur une famille.
                     */
                    $answer->suggestedAction = null;
                }

            }

        }

        return $answer;
    }

    /**
     * Réponse servie quand le meilleur candidat documente un écran que l'employé
     * ne voit pas dans son menu.
     *
     * Arbitrage retenu avec Jeff le 2026-07-26 : on ne se contente pas de rendre le
     * topic invisible, on DIT que le sujet existe mais sort du périmètre. Se taire
     * donnerait l'impression d'un assistant ignorant, et pousserait l'employé à
     * reformuler indéfiniment une question qui n'aboutira jamais.
     *
     * Le texte vient du wiki comme tout le reste (traduit dans les 11 langues,
     * éditable depuis le back-office) plutôt que d'un $this->l() — cf. la leçon du
     * 2026-07-25 sur Plugin::l(), qui renvoie false tant que l'expression n'est pas
     * en base.
     *
     * ⚠️ Aucun détail sur le topic écarté n'est divulgué : ni son libellé, ni
     * l'écran visé. L'employé apprend qu'il y a une limite, pas ce qu'elle cache.
     *
     * @param PhenyxAssistantQuery $query
     * @return PhenyxAssistantAnswer|null Null si le topic n'est pas encore seedé —
     *         on retombe alors sur « aucune réponse », jamais sur la réponse
     *         interdite.
     */
    protected function buildRestrictedAnswer(PhenyxAssistantQuery $query) {

        $row = PhenyxAssistantTopic::getRowByCode(
            self::RESTRICTED_TOPIC_CODE,
            $this->resolveLangId($query->isoCode)
        );

        if (!$row || empty($row['answer'])) {
            return null;
        }

        $answer = PhenyxAssistantAnswer::text((string) $row['answer'], null, 0.9);
        $answer->idTopic = (int) $row['id_phenyx_assistant_topic'];
        $answer->topicCode = (string) $row['code'];

        // Journalisé comme 'none' : du point de vue de la mesure, la question n'a
        // pas trouvé de réponse utile pour cet employé. Les faire remonter dans la
        // grille « Questions à arbitrer » est même souhaitable — un volume élevé
        // signalerait un profil mal dimensionné.
        $answer->raw = [
            'stage'         => self::STAGE_NONE,
            'score'         => null,
            'idTopic'       => (int) $row['id_phenyx_assistant_topic'],
            'runnerUpTopic' => null,
            'runnerUpScore' => null,
        ];

        return $answer;
    }

    /**
     * Le topic RACINE du tour — celui qui propose les grandes familles du menu.
     * Distinct de isTourOnlyTopic(), qui couvre aussi les déclinaisons
     * core.tour.* : seule la racine voit ses puces remplacées par la liste
     * dérivée du menu.
     *
     * @param array $topic
     * @return bool
     */
    protected function isTourRootTopic(array $topic) {

        return isset($topic['code']) && $topic['code'] === 'core.tour';
    }

    /**
     * Fabrique une réponse de DÉSAMBIGUÏSATION : pas de contenu de topic, mais
     * une question en retour et les libellés des candidats en lice.
     *
     * Le libellé vient de la colonne `label` de _lang, avec repli sur `code` :
     * ni `code` seul (technique, illisible pour un employé) ni `answer`
     * (beaucoup trop long pour une puce) ne conviennent, et dériver un libellé du
     * premier mot-clé donnerait des puces incompréhensibles.
     *
     * @param array                $best
     * @param array                $meta
     * @param PhenyxAssistantQuery $query
     * @param array                $topics
     * @return PhenyxAssistantAnswer
     */
    protected function buildAmbiguityAnswer(array $best, array $meta, PhenyxAssistantQuery $query, array $topics) {

        $choices = [];

        foreach ([$best, $meta['ambiguousWith']] as $candidate) {

            if (!is_array($candidate) || empty($candidate['id_phenyx_assistant_topic'])) {
                continue;
            }

            $choices[] = [
                'idTopic' => (int) $candidate['id_phenyx_assistant_topic'],
                'label'   => $this->topicLabel($candidate),
            ];
        }

        $meta['stage'] = self::STAGE_AMBIGUOUS;
        $meta['idTopic'] = null;
        unset($meta['ambiguousWith']);
        $this->lastMatchMeta = $meta;

        // Confiance volontairement faible : une demande de précision ne doit
        // jamais battre une vraie réponse d'une autre couche (cf.
        // PhenyxAssistantAnswer::stageRank(), STAGE_AMBIGUOUS est le rang le plus
        // bas au-dessus de STAGE_NONE).
        $answer = PhenyxAssistantAnswer::text('', $this->pluginName, 0.2);
        $answer->raw = $meta;
        $answer->ambiguousChoices = $choices;
        // Le texte affiché est composé côté JS à partir des choix (le libellé
        // "Did you mean..." vit dans footer.tpl, jamais dans un .js statique —
        // piège connu des {la s='...'}). On met néanmoins les libellés dans
        // $text pour que le log reste lisible et que le repli sans JS affiche
        // quelque chose d'utile.
        $answer->text = implode(' / ', array_column($choices, 'label'));

        return $answer;
    }

    /**
     * Longueur maximale d'un libellé de repli extrait de la réponse, cf.
     * topicLabel(). Au-delà, la puce déborde de la popup.
     */
    const LABEL_FALLBACK_MAX_LENGTH = 70;

    /**
     * Libellé affichable d'un topic.
     *
     * 1. la colonne `label` traduite, si elle est remplie ;
     * 2. sinon, un extrait en texte brut du DÉBUT DE LA RÉPONSE ;
     * 3. sinon seulement, le `code`.
     *
     * ⚠️ Le repli sur `code` était initialement le seul prévu, et c'était une
     * erreur d'ergonomie constatée immédiatement (retour Jeff 2026-07-25) : les
     * puces de désambiguïsation affichaient « core.contentanywhere.create » et
     * « core.contentanywhere.list », c'est-à-dire deux identifiants techniques
     * qu'aucun employé ne peut départager. Les 25 topics antérieurs au
     * 2026-07-25 n'ont pas encore de `label` — attendre qu'ils soient tous
     * traduits en 11 langues avant que la désambiguïsation soit utilisable
     * n'était pas acceptable, d'où ce repli intermédiaire toujours lisible.
     *
     * @param array $topic
     * @return string
     */
    protected function topicLabel(array $topic) {

        if (!empty($topic['label'])) {
            return (string) $topic['label'];
        }

        if (!empty($topic['answer'])) {
            // strip_tags puis décodage des entités : les réponses contiennent du
            // HTML simple (<strong>, <br />) qui n'a pas sa place dans une puce.
            $plain = trim(html_entity_decode(strip_tags((string) $topic['answer']), ENT_QUOTES, 'UTF-8'));
            // Espaces multiples/retours ligne écrasés, sinon l'extrait est troué.
            $plain = trim(preg_replace('/\s+/u', ' ', $plain));

            if ($plain !== '') {

                if (Tools::strlen($plain) > self::LABEL_FALLBACK_MAX_LENGTH) {
                    // Coupe au dernier espace pour ne pas tronquer en plein mot.
                    $cut = Tools::substr($plain, 0, self::LABEL_FALLBACK_MAX_LENGTH);
                    $lastSpace = Tools::strrpos($cut, ' ');
                    $plain = ($lastSpace !== false && $lastSpace > 20 ? Tools::substr($cut, 0, $lastSpace) : $cut) . '…';
                }

                return $plain;
            }

        }

        return isset($topic['code']) ? (string) $topic['code'] : '';
    }

    /**
     * Un topic "tour" (code égal à 'core.tour' ou commençant par
     * 'core.tour.') — cf. TOUR_CONTEXTUAL_MIN_SCORE et findBestTopic().
     * Convention par préfixe de `code` plutôt qu'une colonne dédiée en base
     * (ex. `tour_only`) : plus simple pour un premier jet, cohérent avec les
     * autres conventions par nom déjà en place dans ce framework (ex.
     * AdminController::$controller_name) — à remplacer par une colonne
     * explicite si cette convention devient ambiguë (ex. un futur topic
     * métier légitimement nommé "core.tour.xxx" qui ne serait PAS un simple
     * résumé de menu).
     *
     * @param array $topic Ligne brute de PhenyxAssistantTopic::getActiveForLang()
     * @return bool
     */
    protected function isTourOnlyTopic(array $topic) {
        $code = isset($topic['code']) ? (string) $topic['code'] : '';

        return $code === 'core.tour' || strpos($code, 'core.tour.') === 0;
    }

    /**
     * Vocabulaire BO générique (verbes d'action / mots d'UI) qui revient dans
     * la quasi-totalité des topics "liste"/"création"/"tour" (gérer, voir,
     * ajouter, nouveau...) sans jamais être ce qui distingue un topic d'un
     * autre — cf. `keywords` de core.user.list / core.cms.list /
     * core.contentanywhere.list, qui partagent tous "gestion"/"gerer" par
     * exemple. Comptés à poids réduit dans findBestTopic() plutôt qu'à poids
     * plein comme un mot-clé métier (ex. "societe", "plugin", "traductions")
     * — retour Jeff 2026-07-23, 3e occurrence du même bug (Outils/Société,
     * puis CMS/Traductions) : une question ne recoupant QUE ce type de mot
     * générique (ex. "Gestion des Traductions" ~ "gestion" seul, partagé par
     * une dizaine de topics) ne doit PAS suffire à désigner un gagnant — dans
     * ce cas Jeff préfère explicitement une réponse "je ne trouve pas encore
     * de réponse" (cf. capture "Synchronisation partenaires") plutôt qu'un
     * topic à peine apparenté choisi par un simple ordre d'insertion en base.
     * Liste volontairement bornée à FR/EN (langues où le bug a été
     * concrètement observé) plutôt qu'une liste exhaustive sur les 11
     * langues — à étoffer si le même symptôme apparaît dans une autre langue.
     */
    protected static $lowSignalKeywords = [
        // fr
        'gestion', 'gerer', 'administrer', 'liste', 'lister', 'voir', 'afficher',
        'chercher', 'rechercher', 'trouver', 'consulter', 'menu', 'creer', 'cree',
        'creation', 'ajouter', 'ajoute', 'ajout', 'nouveau', 'nouvelle', 'nouvel',
        'modifier', 'supprimer', 'dupliquer', 'options',
        // en
        'management', 'manage', 'administer', 'list', 'view', 'show', 'find',
        'search', 'create', 'add', 'new', 'edit', 'delete', 'duplicate',
    ];

    /**
     * Taille minimale du corpus (nombre de topics actifs dans la langue) en
     * dessous de laquelle l'IDF n'est pas significatif : sur 20 topics, la
     * fréquence d'un mot-clé relève autant du hasard de rédaction que de sa
     * généricité réelle. En dessous, on conserve $lowSignalKeywords, calibrée à
     * la main sur ce corpus-là et éprouvée par trois correctifs.
     */
    const IDF_MIN_CORPUS = 50;

    /**
     * Plancher du poids d'un mot-clé. Sans lui, un mot présent dans la quasi
     * totalité des topics tomberait à ~0 et disparaîtrait du score, alors qu'il
     * garde une valeur de confirmation quand il est matché en même temps que
     * d'autres.
     */
    const MIN_KEYWORD_WEIGHT = 0.15;

    /**
     * Document frequency des mots-clés de la langue courante (cf.
     * PhenyxAssistantTopic::getDocumentFrequency()), positionné par
     * matchBestTopic() avant l'appel à findBestTopic().
     *
     * @var array
     */
    protected $documentFrequency = [];

    /** @var int Nombre de topics du corpus courant, cf. IDF_MIN_CORPUS */
    protected $topicCount = 0;

    /**
     * Poids d'un mot-clé dans le score de findBestTopic().
     *
     * Au-delà de IDF_MIN_CORPUS topics, le poids est la RARETÉ du mot-clé dans le
     * corpus (IDF) : un mot présent dans beaucoup de topics ne distingue rien et
     * voit son poids s'effondrer tout seul. Cela remplace la liste
     * $lowSignalKeywords, qui a dû être corrigée trois fois entre le 2026-07-22 et
     * le 2026-07-24, n'a jamais couvert que FR/EN, et devenait intenable à
     * l'échelle des ~70 contrôleurs de ph_ecommerce (où "produit", "commande" ou
     * "client" deviendront aussi peu discriminants que "gestion" l'est
     * aujourd'hui, sans figurer dans aucune liste).
     *
     * Trois propriétés qui règlent le problème de fond : auto-calibré (ajouter
     * des topics rééquilibre les poids sans intervention), indifférent à la
     * langue (rien à traduire sur 11 langues), et transitoire propre (sous le
     * seuil de corpus, comportement strictement identique à avant).
     *
     * Le seuil de décision de findBestTopic() reste `>= 1`, ce qui conserve la
     * propriété voulue depuis le 2026-07-23 : un seul mot générique matché ne
     * suffit jamais à désigner un gagnant.
     *
     * @param string $variant
     * @return float
     */
    protected function keywordWeight($variant) {

        if ($this->topicCount < self::IDF_MIN_CORPUS) {
            return in_array($variant, self::$lowSignalKeywords, true) ? 0.3 : 1.0;
        }

        $df = isset($this->documentFrequency[$variant]) ? (int) $this->documentFrequency[$variant] : 1;

        // Normalisé par log($N + 1) pour rester dans [0, 1] : un mot-clé unique à
        // un seul topic vaut ~1 (comme avant), un mot-clé omniprésent tend vers 0
        // et est retenu au plancher.
        $idf = log(($this->topicCount + 1) / ($df + 1)) / log($this->topicCount + 1);

        return max(self::MIN_KEYWORD_WEIGHT, min(1.0, $idf));
    }

    /**
     * Écart RELATIF minimal entre le meilleur et le second topic pour désigner un
     * gagnant sans demander confirmation. En dessous, on désambiguïse (étage 4).
     *
     * 0.34 signifie : le gagnant doit devancer le second d'au moins un tiers de
     * son propre score. Sur les cas réels connus — "Gestion des modèles
     * d'e-mails" après ajout du mot-clé "mails" gagnait 2.3 contre 0.3, soit 87 %
     * d'écart — ce seuil laisse largement passer les matchs francs et n'attrape
     * que les vraies égalités, celles qui étaient jusqu'ici tranchées par l'ordre
     * d'insertion en base.
     *
     * À recalibrer sur les données de `runner_up_score` du log plutôt qu'à
     * l'intuition : c'est précisément pour ça que cette colonne existe.
     */
    const AMBIGUITY_MARGIN = 0.34;

    /**
     * Écart ABSOLU au-delà duquel on tranche, même si l'écart relatif est faible.
     * Les deux conditions doivent être réunies pour désambiguïser.
     *
     * ⚠️ Ce garde-fou n'était pas dans la conception initiale — il vient d'une
     * simulation sur le corpus réel (2026-07-25) qui a montré une RÉGRESSION sans
     * lui : "Comment gérer les pages CMS ?" score 2.6 pour core.cms.list contre
     * 2.0 pour core.cms.create ("pages" ~ "page" à une lettre près). Soit 23 %
     * d'écart relatif, donc sous AMBIGUITY_MARGIN — l'assistant aurait reposé la
     * question alors que Jeff avait précisément réglé ce cas le 2026-07-22 en
     * ajoutant les mots-clés "gérer/gestion" aux topics *.list pour départager.
     * Redemander une précision sur un cas déjà arbitré serait vécu comme une
     * régression, à juste titre.
     *
     * Calibrage à 0.25, juste EN DESSOUS du poids d'un mot-clé faible (0.3, cf.
     * keywordWeight()). Ce n'est pas arbitraire : les départages que Jeff a
     * ajoutés à la main sont précisément des mots-clés faibles ("gérer",
     * "gestion" sur les topics *.list, 2026-07-22), donc un écart d'exactement
     * 0.3. Le seuil doit laisser passer ce cas — un départage volontaire est une
     * décision, pas une égalité — tout en attrapant l'égalité vraie (1.0 contre
     * 1.0, cas "modeles"/"modules" du 2026-07-24, écart 0).
     *
     * ⚠️ Pourquoi 0.25 et pas 0.3 : `2.3 - 2.0` vaut 0.29999999999999982 en
     * flottant, donc `$gap < 0.3` serait VRAI et rouvrirait le cas CMS malgré
     * l'intention inverse. Le seuil est placé sous la valeur nominale plutôt que
     * dessus, ce qui évite d'avoir à manipuler un epsilon dans la comparaison.
     */
    const AMBIGUITY_MIN_GAP = 0.25;

    /**
     * Score minimal qu'un topic "tour" (cf. isTourOnlyTopic()) doit atteindre
     * pour pouvoir gagner un round-trip CONTEXTUEL automatique (cf.
     * $contextual dans findBestTopic() ci-dessous) — nettement au-dessus du
     * seuil normal (1) : un topic tour ne doit répondre automatiquement que
     * s'il recoupe plusieurs mots-clés distincts et significatifs (ex.
     * "back"+"office"+"traductions", score 3), jamais sur un seul mot-clé
     * générique ou une coïncidence isolée (ex. "traductions" seul, ou
     * "gestion"+"traductions" où "gestion" ne pèse presque rien, cf.
     * keywordWeight()) — retour Jeff 2026-07-23.
     */
    const TOUR_CONTEXTUAL_MIN_SCORE = 2.0;

    /**
     * Cherche, parmi $topics (lignes brutes déjà chargées pour la langue
     * courante — cf. getActiveForLang()), celui dont les mots-clés recoupent
     * le mieux $text. Factorisé hors de matchBestTopic() pour être réutilisé
     * par filterScreenDependentQuickReplies() (résoudre à quel topic mènerait
     * le clic sur une quick reply donnée, sans re-questionner la base).
     *
     * @param string $text        Texte à scorer (question de l'employé, ou libellé d'une quick reply)
     * @param array  $topics      Lignes brutes de PhenyxAssistantTopic::getActiveForLang()
     * @param bool   $contextual  true si cet appel vient du round-trip AUTOMATIQUE
     *                            (notifyPhenyxAssistantControllerOpened(), cf.
     *                            isContextual dans matchBestTopic()) plutôt que
     *                            d'une question explicite de l'employé — impose
     *                            alors TOUR_CONTEXTUAL_MIN_SCORE aux topics
     *                            "tour" (cf. isTourOnlyTopic()).
     * @param array|null $meta    PASSÉ PAR RÉFÉRENCE (out-param). Renseigné avec
     *                            l'étage, le score et le second candidat — cf.
     *                            PhenyxAssistantAnswer::$raw. Volontairement un
     *                            out-param plutôt qu'un changement de type de
     *                            retour : filterScreenDependentQuickReplies()
     *                            appelle cette méthode en boucle et n'attend
     *                            qu'une ligne de topic ou null.
     *                            Non typé (pas `array &$meta`) pour rester
     *                            compatible avec un défaut null sans dépendre du
     *                            nullable implicite, déprécié en PHP 8.4.
     * @return array|null Meilleure ligne topic, ou null si aucun recoupement
     */
    protected function findBestTopic($text, array $topics, $contextual = false, &$meta = null) {

        $meta = [
            'stage'         => self::STAGE_NONE,
            'score'         => 0.0,
            'idTopic'       => null,
            'runnerUpTopic' => null,
            'runnerUpScore' => null,
        ];

        // ⚠️ Les mots interrogatifs sont RETIRÉS avant scoring (retour Jeff
        // 2026-07-25). Ils ne portent aucune information discriminante, mais ils
        // participaient quand même au score — et pire, à la comparaison
        // Levenshtein d'isCloseEnough(). Cas réel observé : "Comment activer le
        // HTTPS ?" ne renvoyait pas core.preferences.ssl mais une
        // désambiguïsation entre core.contentanywhere.create et .list, parce que
        // le mot français « comment » est à distance de Levenshtein 2 du mot-clé
        // anglais « content » (même initiale, 7 lettres chacun, donc sous le
        // seuil). Les deux topics contentanywhere marquaient 1.0 avant même que
        // le topic SSL — pourtant en correspondance EXACTE sur « https » — ne
        // soit atteint : à égalité de score, ni `> $bestScore` ni `> $secondScore`
        // ne sont vrais, il n'apparaissait donc même pas dans les propositions.
        //
        // Même classe de collision que gestion/session (2026-07-23) et
        // modeles/modules (2026-07-24), mais traitée cette fois à la racine :
        // un mot de formulaire pure ne doit jamais entrer dans le calcul.
        $tokens = array_values(array_diff($this->tokenize($text), static::$questionStopWords));

        if (!$tokens) {
            return null;
        }

        $normalizedText = $this->normalizeText($text);
        $best = null;
        $bestScore = 0;
        // Second meilleur topic : ne change RIEN au choix du gagnant, sert
        // uniquement à mesurer de combien il gagne. Un écart faible signale deux
        // topics dont les mots-clés se chevauchent trop — c'est la donnée qui
        // manquait pour diagnostiquer les faux-positifs des 22, 23 et
        // 24/07/2026 autrement qu'en rejouant le scoring à la main.
        $second = null;
        $secondScore = 0;

        foreach ($topics as $topic) {

            $variants = preg_split('/\s+/u', trim((string) $topic['keywords']));
            $score = 0;

            foreach ($variants as $variant) {

                if ($variant !== '' && $this->variantMatches($variant, $tokens, $normalizedText)) {
                    $score += $this->keywordWeight($variant);
                }

            }

            if ($contextual && $this->isTourOnlyTopic($topic) && $score < self::TOUR_CONTEXTUAL_MIN_SCORE) {
                continue;
            }

            if ($score > $bestScore) {
                $second = $best;
                $secondScore = $bestScore;
                $bestScore = $score;
                $best = $topic;
            } else if ($score > $secondScore) {
                $secondScore = $score;
                $second = $topic;
            }

        }

        $meta['score'] = (float) $bestScore;

        if ($second !== null && $secondScore > 0) {
            $meta['runnerUpTopic'] = isset($second['id_phenyx_assistant_topic']) ? (int) $second['id_phenyx_assistant_topic'] : null;
            $meta['runnerUpScore'] = (float) $secondScore;
        }

        if ($best === null || $bestScore < 1) {
            // $meta['stage'] reste STAGE_NONE, mais 'score' porte le meilleur
            // score atteint : c'est ce qui permet de voir qu'une question a
            // frôlé le seuil (0.6 sur deux mots génériques, par exemple) plutôt
            // que de n'avoir aucune information sur un échec.
            return null;
        }

        $meta['stage'] = self::STAGE_KEYWORD;
        $meta['idTopic'] = isset($best['id_phenyx_assistant_topic']) ? (int) $best['id_phenyx_assistant_topic'] : null;

        // ÉTAGE 4 : écart trop faible pour trancher honnêtement. On signale
        // l'ambiguïté à l'appelant (matchBestTopic) au lieu de désigner un
        // gagnant par ordre d'insertion en base.
        //
        // Volontairement PAS appliqué quand $contextual est vrai : ce chemin est
        // désormais traité par l'étage 0, et de toute façon poser une question à
        // l'employé qui vient juste d'ouvrir un écran (sans rien demander) serait
        // intrusif.
        // Les DEUX conditions doivent être réunies : écart relatif faible ET
        // écart absolu faible (cf. AMBIGUITY_MIN_GAP, sans lequel on rouvrirait
        // des cas déjà arbitrés à la main).
        $gap = $bestScore - $secondScore;

        if (!$contextual && $second !== null && $secondScore > 0
            && $gap < self::AMBIGUITY_MIN_GAP
            && ($gap / $bestScore) < self::AMBIGUITY_MARGIN) {
            $meta['ambiguousWith'] = $second;
        }

        return $best;
    }

    /**
     * Retire de $quickReplies celles qui, si cliquées, mèneraient à un topic
     * dont l'action suggérée n'a de sens que sur une liste déjà ouverte (cf.
     * ephenyx.js: 'simulateGridContextMenu' / 'highlightAndClickElement'),
     * sauf si l'employé est justement sur une liste ($sourceType === 'list').
     * Une quick reply qui ne résout à aucun topic connu, ou dont l'action
     * n'est pas de ce type, est toujours conservée.
     *
     * @param string[] $quickReplies
     * @param array    $topics       Lignes brutes de PhenyxAssistantTopic::getActiveForLang()
     * @param string|null $sourceType 'list' | 'add' | 'edit' | null (cf. current_type côté JS)
     * @return string[]
     */
    protected function filterScreenDependentQuickReplies(array $quickReplies, array $topics, $sourceType) {

        if (!$quickReplies) {
            return $quickReplies;
        }

        $screenDependentTypes = ['simulateGridContextMenu', 'highlightAndClickElement'];

        return array_values(array_filter($quickReplies, function ($replyText) use ($topics, $sourceType, $screenDependentTypes) {

            $topic = $this->findBestTopic((string) $replyText, $topics);

            if (!$topic || empty($topic['suggested_action'])) {
                return true;
            }

            $action = json_decode($topic['suggested_action'], true);

            if (empty($action['type']) || !in_array($action['type'], $screenDependentTypes, true)) {
                return true;
            }

            return $sourceType === 'list';
        }));
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
