<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistantTopic
 *
 * PhenyxObjectModel (multilang) pour la table phenyx_assistant_topic /
 * phenyx_assistant_topic_lang (cf. sql/phenyx_assistant_topic.sql). Un
 * "topic" est une réponse candidate autonome (une question de FAQ) :
 * mots-clés + réponse + quick replies traduits par langue, action suggérée
 * et confiance communes à toutes les langues.
 *
 * Sépare le contenu de l'assistant du code PHP des couches
 * (PhenyxAssistantLayer) : une couche peut interroger ces tables via
 * PhenyxAssistantLayer::matchBestTopic() plutôt que coder ses réponses en
 * dur, ce qui permet de couvrir plusieurs langues sans redéploiement et
 * prépare le terrain pour un futur RAG (v1, provider LLM local, cf.
 * PhenyxAssistantProviderInterface).
 *
 * Base de données : cf. retour utilisateur du 2026-07-21 — ces deux tables
 * ne vivent PAS dans la base boutique locale mais dans la base centralisée
 * "phenyx-traduction" (même principe que la classe Translation), afin de
 * pouvoir un jour mutualiser le contenu du wiki entre tous les clients
 * QuantumCore plutôt que de le dupliquer boutique par boutique. D'où :
 *  - $dbUser/$dbPasswd/$dbName/$dbServer lus depuis les constantes
 *    _EPH_CRM_DB_* (définies dans defines_inc.php à partir du .env, cf.
 *    Translation::__construct()) ;
 *  - __construct() ne délègue pas à PhenyxObjectModel::__construct() (qui
 *    chargerait toujours depuis la base locale) mais reproduit le minimum
 *    nécessaire, en passant les credentials CRM à Adapter_EntityMapper::load()
 *    — celui-ci sait déjà basculer sur Db::getCrmInstance() dès que les
 *    credentials fournis diffèrent de la base locale (cf.
 *    Adapter_EntityMapper::load(), branche $db = ... Db::getCrmInstance(...)) ;
 *  - add()/update() surchargés pour écrire sur Db::getCrmInstance() (table
 *    principale + table _lang), sur le modèle de Translation::add()/update()
 *    mais avec la boucle multilang de PhenyxObjectModel::add()/update().
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantTopic extends PhenyxObjectModel {

    public $code;

    public $source_plugin;

    public $entity_class;

    public $suggested_action;

    public $confidence;

    public $active = true;

    public $date_add;

    public $date_upd;

    /**
     * Libellé court et lisible du topic dans cette langue ("Manage email
     * templates"). Distinct de $code (technique, non traduit) et de $answer
     * (trop long) : sert aux puces de désambiguïsation, à la grille de
     * réassignation et à l'écran de traduction du wiki.
     *
     * Peut être vide — l'affichage retombe alors sur $code plutôt que sur une
     * chaîne vide, de sorte que la colonne puisse être ajoutée en base avant
     * d'être remplie sans bloquer le déploiement.
     *
     * @var string
     */
    public $label;

    /** @var string Mots-clés normalisés (espaces), traduits par langue */
    public $keywords;

    /** @var string Réponse affichée, HTML simple autorisé, traduite par langue */
    public $answer;

    /** @var string Tableau JSON de libellés de quick replies, traduit par langue */
    public $quick_replies;

    /** @var string Cf. Translation::$dbUser — credentials CRM issus de .env */
    protected $dbUser;

    /** @var string */
    protected $dbPasswd;

    /** @var string */
    protected $dbName;

    /** @var string */
    protected $dbServer;

    public static $definition = [
        'table'      => 'phenyx_assistant_topic',
        'primary'    => 'id_phenyx_assistant_topic',
        'multilang'  => true,
        'have_meta'  => false,
        'fields'     => [
            'code'             => ['type' => self::TYPE_STRING, 'size' => 64, 'required' => true],
            'source_plugin'    => ['type' => self::TYPE_STRING, 'size' => 64],
            'entity_class'     => ['type' => self::TYPE_STRING, 'size' => 64],
            'suggested_action' => ['type' => self::TYPE_STRING, 'size' => 500],
            'confidence'       => ['type' => self::TYPE_FLOAT],
            'active'           => ['type' => self::TYPE_BOOL],
            'date_add'         => ['type' => self::TYPE_DATE],
            'date_upd'         => ['type' => self::TYPE_DATE],

            /* Lang fields */
            'label'         => ['type' => self::TYPE_STRING, 'size' => 128, 'lang' => true],
            'keywords'      => ['type' => self::TYPE_STRING, 'size' => 500, 'lang' => true, 'required' => true],
            'answer'        => ['type' => self::TYPE_HTML, 'lang' => true, 'required' => true],
            'quick_replies' => ['type' => self::TYPE_STRING, 'lang' => true],
        ],
    ];

    /**
     * Cache par langue du "document frequency" des mots-clés (nombre de topics
     * actifs contenant chaque mot-clé), consommé par
     * PhenyxAssistantLayer::keywordWeight() pour la pondération IDF. Statique et
     * non persisté : recalculé une fois par requête PHP au pire, ce qui suffit —
     * l'alternative (CacheApi) demanderait une invalidation inter-processus pour
     * un gain nul à cette échelle.
     *
     * @var array<int, array>
     */
    protected static $documentFrequencyCache = [];

    /**
     * Cf. Translation::__construct() — même principe, adapté au cas
     * multilang : on reproduit le sous-ensemble de
     * PhenyxObjectModel::__construct() réellement nécessaire (résolution de
     * $this->def via le cache de classes chargées, contexte) puis on
     * délègue le chargement à Adapter_EntityMapper::load() en lui passant
     * explicitement les credentials CRM — c'est ce load() qui gère déjà la
     * jointure vers la table _lang pour les classes multilang.
     *
     * @param int|null $id
     * @param int|null $idLang
     */
    public function __construct($id = null, $idLang = null) {

        $this->className = get_class($this);

        if (!isset(PhenyxObjectModel::$loaded_classes[$this->className])) {
            $this->def = PhenyxObjectModel::getDefinition($this->className);
            PhenyxObjectModel::$loaded_classes[$this->className] = get_object_vars($this);
        } else {

            foreach (PhenyxObjectModel::$loaded_classes[$this->className] as $key => $value) {
                $this->{$key} = $value;
            }

        }

        $this->context = Context::getContext();

        if (!PhenyxObjectModel::$hook_instance) {
            PhenyxObjectModel::$hook_instance = Hook::getInstance();
            $this->context->_hook = PhenyxObjectModel::$hook_instance;
        }

        $this->dbUser   = defined('_EPH_CRM_DB_USER_')   ? _EPH_CRM_DB_USER_   : '';
        $this->dbPasswd = defined('_EPH_CRM_DB_PASSWD_') ? _EPH_CRM_DB_PASSWD_ : '';
        $this->dbName   = defined('_EPH_CRM_DB_NAME_')   ? _EPH_CRM_DB_NAME_   : '';
        $this->dbServer = defined('_EPH_CRM_DB_SERVER_') ? _EPH_CRM_DB_SERVER_ : '';

        if ($id) {
            $entityMapper = Adapter_ServiceLocator::get('Adapter_EntityMapper');
            $entityMapper->load(
                $id, $idLang, $this, $this->def, false,
                $this->dbUser, $this->dbPasswd, $this->dbName, $this->dbServer
            );
            $this->unpackJsonFields();
        }

        if ($idLang !== null) {
            $this->id_lang = (Language::getLanguage($idLang) !== false) ? $idLang : $this->context->phenyxConfig->get('EPH_LANG_DEFAULT');
        }

    }

    /**
     * Décode en tableau PHP les champs stockés en JSON brut, juste après le
     * chargement — convention habituelle de Jeff pour les champs JSON d'un
     * PhenyxObjectModel : décodage à la construction, ré-encodage dans
     * add()/update() (cf. packJsonFields()).
     *
     * - suggested_action : champ simple (non lang), toujours scalaire.
     * - quick_replies : champ lang, donc soit un tableau [$idLang => json],
     *   soit un scalaire json si l'objet a été chargé pour un $idLang précis
     *   (cf. Adapter_EntityMapper::load()) — les deux cas sont gérés.
     *
     * @return void
     */
    protected function unpackJsonFields() {

        if (!is_null($this->suggested_action) && is_string($this->suggested_action) && Validate::isJSON($this->suggested_action)) {
            $this->suggested_action = Tools::jsonDecode($this->suggested_action, true);
        }

        if (is_array($this->quick_replies)) {

            foreach ($this->quick_replies as $idLang => $value) {

                if (!is_null($value) && is_string($value) && Validate::isJSON($value)) {
                    $this->quick_replies[$idLang] = Tools::jsonDecode($value, true);
                }

            }

        } elseif (!is_null($this->quick_replies) && is_string($this->quick_replies) && Validate::isJSON($this->quick_replies)) {
            $this->quick_replies = Tools::jsonDecode($this->quick_replies, true);
        }

    }

    /**
     * Ré-encode en JSON les champs décodés par unpackJsonFields(), avant
     * écriture en base — à appeler en tout début de add()/update(), avant
     * getFields()/getFieldsLang().
     *
     * @return void
     */
    protected function packJsonFields() {

        if (is_array($this->suggested_action)) {
            $this->suggested_action = Tools::jsonEncode($this->suggested_action);
        }

        if (is_array($this->quick_replies)) {

            foreach ($this->quick_replies as $idLang => $value) {

                if (is_array($value)) {
                    $this->quick_replies[$idLang] = Tools::jsonEncode($value);
                }

            }

        }

    }

    /**
     * Cf. PhenyxObjectModel::add() — même logique (insert table principale
     * puis boucle sur les champs lang), mais sur Db::getCrmInstance() au
     * lieu de Db::getInstance(), cf. Translation::add().
     *
     * @param bool $autoDate
     * @param bool $nullValues
     * @return bool
     */
    public function add($autoDate = true, $nullValues = false) {

        $this->packJsonFields();

        if (isset($this->id) && !$this->force_id) {
            unset($this->id);
        }

        if ($autoDate && property_exists($this, 'date_add')) {
            $this->date_add = date('Y-m-d H:i:s');
        }

        if ($autoDate && property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
        }

        $db = Db::getCrmInstance($this->dbUser, $this->dbPasswd, $this->dbName, $this->dbServer);

        $fields = $this->getFields();

        if (!$result = $db->insert($this->def['table'], $fields, $nullValues)) {
            return false;
        }

        $this->id = $db->Insert_ID();

        if (!$result) {
            return false;
        }

        if (!empty($this->def['multilang'])) {
            $fields = $this->getFieldsLang();

            if ($fields && is_array($fields)) {

                foreach ($fields as $field) {

                    foreach (array_keys($field) as $key) {

                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PhenyxException('key ' . $key . ' is not table or identifier');
                        }

                    }

                    $field[$this->def['primary']] = (int) $this->id;

                    $result &= $db->insert($this->def['table'] . '_lang', $field);
                }

            }

        }

        $this->reindexKeywords();

        return $result;
    }

    /**
     * Cf. PhenyxObjectModel::update() — même logique (update table
     * principale puis upsert par langue), mais sur Db::getCrmInstance(),
     * cf. Translation::update().
     *
     * @param bool $nullValues
     * @return bool
     */
    public function update($nullValues = false) {

        $this->packJsonFields();

        if (property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
        }

        if (property_exists($this, 'date_add') && empty($this->date_add)) {
            $this->date_add = date('Y-m-d H:i:s');
        }

        $db = Db::getCrmInstance($this->dbUser, $this->dbPasswd, $this->dbName, $this->dbServer);

        if (!$result = $db->update($this->def['table'], $this->getFields(), '`' . pSQL($this->def['primary']) . '` = ' . (int) $this->id, 0, $nullValues)) {
            return false;
        }

        if (!empty($this->def['multilang'])) {
            $fields = $this->getFieldsLang();

            if (is_array($fields)) {

                foreach ($fields as $field) {

                    foreach (array_keys($field) as $key) {

                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PhenyxException('key ' . $key . ' is not a valid table or identifier');
                        }

                    }

                    $where = pSQL($this->def['primary']) . ' = ' . (int) $this->id . ' AND id_lang = ' . (int) $field['id_lang'];

                    if ($db->getValue(
                        (new DbQuery())
                            ->select('COUNT(*)')
                            ->from(pSQL($this->def['table']) . '_lang')
                            ->where($where)
                    )) {
                        $result &= $db->update($this->def['table'] . '_lang', $field, $where);
                    } else {
                        $result &= $db->insert($this->def['table'] . '_lang', $field, $nullValues);
                    }

                }

            }

        }

        $this->reindexKeywords();

        return $result;
    }

    /**
     * Reconstruit l'index inversé (phenyx_assistant_keyword) pour CE topic, et
     * vide le cache de document frequency.
     *
     * Appelé depuis add() et update() : c'est le seul endroit par lequel les
     * mots-clés d'un topic changent, que l'écriture vienne du seeder, de l'écran
     * CRUD ou de la grille de traduction du wiki.
     *
     * Stratégie delete-then-insert plutôt qu'un diff : un topic a une dizaine de
     * mots-clés par langue, le diff coûterait plus cher en code qu'en requêtes.
     *
     * "Best effort" assumé : l'index n'est qu'une accélération (cf.
     * getDocumentFrequency(), qui sait recompter sans lui). Si la table n'existe
     * pas encore sur ce site, l'écriture du topic ne doit surtout pas échouer
     * pour autant — d'où le catch silencieux.
     *
     * @return void
     */
    protected function reindexKeywords() {

        // Le cache de df devient faux dès qu'un mot-clé bouge, y compris si
        // l'indexation ci-dessous échoue.
        self::$documentFrequencyCache = [];

        if (!$this->id) {
            return;
        }

        // $this->keywords est soit un tableau [id_lang => 'mots clés'] (cas
        // normal après getFieldsLang()), soit un scalaire si l'objet a été
        // chargé pour une seule langue (cf. Adapter_EntityMapper::load()).
        $byLang = is_array($this->keywords) ? $this->keywords : [];

        if (!$byLang && !is_array($this->keywords) && !empty($this->id_lang)) {
            $byLang = [(int) $this->id_lang => (string) $this->keywords];
        }

        try {
            $db = self::getCrmDb();
            $db->delete('phenyx_assistant_keyword', 'id_phenyx_assistant_topic = ' . (int) $this->id);

            // Insertion GROUPÉE (Db::insert() accepte une liste de lignes et en
            // fait un seul INSERT ... VALUES (...),(...)).
            //
            // Mesuré sur le corpus réel : install_assistant_topics.php écrit 25
            // topics × 11 langues × ~10 mots-clés, soit ~2 773 lignes d'index. En
            // une requête par mot-clé, ce sont 2 773 allers-retours vers une base
            // CRM DISTANTE — c'est ce qui a rendu le script si lent au premier
            // essai. Regroupé, il en reste une par (topic, langue), soit ~275.
            //
            // Chunks de 200 lignes malgré tout : une requête unique de 2 773
            // valeurs risquerait max_allowed_packet sur un hébergement contraint.
            $rows = [];

            foreach ($byLang as $idLang => $keywords) {

                foreach (array_unique(preg_split('/\s+/u', trim((string) $keywords))) as $keyword) {

                    if ($keyword === '') {
                        continue;
                    }

                    $rows[] = [
                        // Tronqué à la taille de la colonne : un mot-clé plus
                        // long serait de toute façon rejeté en base stricte, et
                        // il vaut mieux un index partiel qu'une écriture de topic
                        // qui échoue.
                        'keyword'                   => pSQL(Tools::substr($keyword, 0, 64)),
                        'id_lang'                   => (int) $idLang,
                        'id_phenyx_assistant_topic' => (int) $this->id,
                    ];
                }

            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $db->insert('phenyx_assistant_keyword', $chunk, false, true, Db::INSERT_IGNORE);
            }

        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::reindexKeywords a échoué (%s) — index inversé ignoré, le scoring retombe sur un comptage direct.', $e->getMessage()),
                1,
                null,
                static::class
            );
        }

    }

    /**
     * Charge les topics actifs (et leur traduction pour $idLang), en lecture
     * plate (pas d'hydratation d'objets un par un) puisqu'ils sont
     * uniquement scorés/comparés par PhenyxAssistantLayer::matchBestTopic(),
     * jamais modifiés à ce stade. Lit sur la base CRM centralisée
     * "phenyx-traduction" — cf. note de classe.
     *
     * @param int         $idLang
     * @param string|null $entityClass Filtre optionnel (ex: 'User')
     * @return array Lignes brutes (associatives) : id_phenyx_assistant_topic,
     *               code, suggested_action, confidence, keywords, answer,
     *               quick_replies...
     */
    public static function getActiveForLang($idLang, $entityClass = null) {

        $query = (new DbQuery())
            ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.label, tl.keywords, tl.answer, tl.quick_replies')
            ->from('phenyx_assistant_topic', 't')
            ->innerJoin('phenyx_assistant_topic_lang', 'tl', 'tl.id_phenyx_assistant_topic = t.id_phenyx_assistant_topic AND tl.id_lang = ' . (int) $idLang)
            ->where('t.active = 1');

        if ($entityClass !== null) {
            $query->where('t.entity_class = \'' . pSQL($entityClass) . '\'');
        }

        try {
            $rows = self::getCrmDb()->executeS($query);
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::getActiveForLang a échoué (%s)', $e->getMessage()),
                2,
                null,
                static::class
            );

            return [];
        }

        return is_array($rows) ? $rows : [];
    }

    /**
     * ÉTAGE 0 du pipeline de résolution : le topic explicitement lié à un écran
     * BO, sans aucun scoring de texte.
     *
     * Motivation (cf. assistant-filtrage-etage-plan.md §1.2) : le round-trip
     * contextuel automatique envoie le LIBELLÉ de l'écran comme s'il s'agissait
     * d'une question tapée. Ce libellé n'est pas une question — c'est un
     * identifiant d'écran déguisé, issu d'un ensemble fini et connu d'avance. Le
     * faire scorer par recoupement de mots-clés a produit quatre faux-positifs
     * entre le 2026-07-22 et le 2026-07-24 (dont "Gestion des modèles d'e-mails"
     * → topic Plugins, via la collision "modeles"/"modules"). Un liage explicite
     * supprime cette classe de bugs par construction.
     *
     * Liage par `controller_name` et JAMAIS par libellé affiché : c'est
     * précisément la divergence de libellés entre ephenyx.io ("Gestion des
     * Templates des emails") et ephenyx.digital ("Gestion des modèles
     * d'e-mails") qui a causé le bug du 2026-07-24.
     *
     * Résolution du type d'écran : correspondance exacte sur $sourceType
     * d'abord, puis repli sur les liages "tout type" (`source_type = ''`) — un
     * écran peut ainsi avoir un topic générique et un topic spécifique à son
     * formulaire d'édition.
     *
     * Résolution de l'ONGLET (2026-08-03) : troisième dimension de spécificité,
     * après le contrôleur et le type. Les écrans multi-onglets (AdminSeo,
     * AdminPreferences, AdminPerformance...) exposent l'onglet courant dans la
     * variable JS globale dataTab — la 'key' de leur configurateur. Même
     * philosophie de repli que source_type : l'onglet exact gagne, le liage
     * `tab_key = ''` (tout l'écran) rattrape. Et l'onglet PRIME sur le type,
     * parce qu'il est plus fin : un liage (onglet exact, type '') bat un liage
     * (onglet '', type exact).
     *
     * @param string|null $controllerName Cf. back_tab.class_name / current_controller (nav.js)
     * @param string|null $sourceType     'list'|'add'|'edit'|'view'|'home', cf. current_type (nav.js)
     * @param int         $idLang
     * @param string|null $entityClass    Même filtre que getActiveForLang() : une couche de
     *                                    plugin ne doit répondre que pour ses propres entités
     * @param string|null $tabKey         Onglet courant de l'écran, cf. dataTab (seo.js et frères) ;
     *                                    null ou '' = pas d'onglet, liages d'écran seuls
     * @return array|null Ligne brute, même forme que getActiveForLang()
     */
    public static function getBoundToScreen($controllerName, $sourceType, $idLang, $entityClass = null, $tabKey = null) {

        if (empty($controllerName)) {
            return null;
        }

        /*
         * ⚠️ GARDE DE VERSION. La colonne tab_key naît d'une migration de la
         * base CRM (upgrade_assistant_tab_key.php), et cette base est PARTAGÉE :
         * ce code peut tourner avant qu'elle ait été jouée. Or DbPDO::_query()
         * ne relance pas les erreurs SQL — il journalise et rend false. Une
         * requête sur une colonne absente ne lèverait donc RIEN : l'étage 0
         * entier deviendrait muet, en silence. On vérifie la colonne une fois
         * par requête PHP, et on n'ajoute la dimension que si elle existe.
         */
        $avecOnglet = ($tabKey !== null && $tabKey !== '' && self::hasTabKeyColumn());

        $query = (new DbQuery())
            ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.label, tl.keywords, tl.answer, tl.quick_replies, ts.source_type, ts.priority')
            ->from('phenyx_assistant_topic_screen', 'ts')
            ->innerJoin('phenyx_assistant_topic', 't', 't.id_phenyx_assistant_topic = ts.id_phenyx_assistant_topic')
            ->innerJoin('phenyx_assistant_topic_lang', 'tl', 'tl.id_phenyx_assistant_topic = t.id_phenyx_assistant_topic AND tl.id_lang = ' . (int) $idLang)
            ->where('t.active = 1')
            ->where('ts.controller_name = \'' . pSQL($controllerName) . '\'');

        // Un $entityClass null signifie "pas de filtre" — la couche core répond
        // pour tous les topics quel que soit leur entity_class — et non
        // "uniquement les topics sans entité". Même convention que
        // getActiveForLang() juste au-dessus.
        if ($entityClass !== null) {
            $query->where('t.entity_class = \'' . pSQL($entityClass) . '\'');
        }

        /*
         * Les tris se COMPOSENT : (onglet exact) DESC d'abord, puis
         * (type exact) DESC, puis priority. Sans onglet demandé — écran sans
         * onglets, dataTab remis à null par nav.js, ou base non migrée — on ne
         * sert QUE les liages tab_key = '' : comportement strictement identique
         * à avant la migration.
         */
        $ordre = [];

        if ($avecOnglet) {
            $query->where('ts.tab_key IN (\'' . pSQL($tabKey) . '\', \'\')');
            $ordre[] = '(ts.tab_key = \'' . pSQL($tabKey) . '\') DESC';
        } else if (self::hasTabKeyColumn()) {
            $query->where('ts.tab_key = \'\'');
        }

        if (!empty($sourceType)) {
            $query->where('ts.source_type IN (\'' . pSQL($sourceType) . '\', \'\')');
            // Le type exact gagne sur le liage générique, puis priority départage
            // deux liages de même spécificité.
            $ordre[] = '(ts.source_type = \'' . pSQL($sourceType) . '\') DESC';
        } else {
            $query->where('ts.source_type = \'\'');
        }

        $ordre[] = 'ts.priority DESC';
        $query->orderBy(implode(', ', $ordre));

        try {
            $row = self::getCrmDb()->getRow($query);
        } catch (\Throwable $e) {
            // ⚠️ Ne couvre PAS les erreurs SQL : DbPDO::_query() intercepte la
            // PDOException, la journalise et renvoie false sans la relancer (cf.
            // la note détaillée dans hasAnyScreenBinding()). Une table absente
            // ou une requête malformée passe donc par le `return $row ?: null`
            // ci-dessous, pas par ici — seul un échec de connexion à la base CRM
            // arrive dans ce catch. Dans les deux cas l'assistant n'est pas
            // cassé : les étages suivants prennent le relais.
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::getBoundToScreen : base CRM injoignable (%s).', $e->getMessage()),
                2,
                null,
                static::class
            );

            return null;
        }

        return $row ?: null;
    }

    /**
     * Une ligne de topic par son `code`, dans la langue demandée.
     *
     * Sert aux plugins qui enrichissent une réponse du cœur (cf.
     * PhenyxAssistant::HOOK_ANSWER_AUGMENT) : leur texte d'enrichissement doit
     * vivre dans le WIKI, pas dans une chaîne $this->l().
     *
     * ⚠️ Raison précise (constatée le 2026-07-25) : Plugin::l() délègue à
     * Translation::getExistingTranslation(), qui renvoie FALSE quand l'expression
     * n'est pas encore dans la table `translation` — sans aucun repli sur la
     * chaîne source. Un texte d'aide neuf écrit avec l() est donc littéralement
     * VIDE jusqu'à collecte par l'outil de traduction du BO. Le wiki, lui, est
     * multilingue par construction, éditable depuis le back-office, et suit le
     * même chemin que tout le reste du contenu de l'assistant.
     *
     * Repli sur la langue par défaut du shop si la traduction demandée manque,
     * comme getActiveForLang().
     *
     * @param string $code
     * @param int    $idLang
     * @return array|null
     */
    public static function getRowByCode($code, $idLang) {

        if (empty($code)) {
            return null;
        }

        $fetch = function ($lang) use ($code) {

            return self::getCrmDb()->getRow(
                (new DbQuery())
                    ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.label, tl.keywords, tl.answer, tl.quick_replies')
                    ->from('phenyx_assistant_topic', 't')
                    ->innerJoin('phenyx_assistant_topic_lang', 'tl', 'tl.id_phenyx_assistant_topic = t.id_phenyx_assistant_topic AND tl.id_lang = ' . (int) $lang)
                    ->where('t.active = 1')
                    ->where('t.code = \'' . pSQL($code) . '\'')
            );
        };

        try {
            $row = $fetch((int) $idLang);

            if (!$row) {
                $fallback = (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');

                if ($fallback && $fallback !== (int) $idLang) {
                    $row = $fetch($fallback);
                }

            }

        } catch (\Throwable $e) {
            return null;
        }

        return $row ?: null;
    }

    /**
     * Le contrôleur d'écran lié à un topic — lecture INVERSE du liage.
     *
     * Sert à proposer automatiquement « Ouvrir cet écran » avec la réponse d'un
     * topic qui ne déclare pas d'action propre (retour Jeff 2026-07-25 : les
     * puces de la famille Société répondaient sans emmener l'employé sur
     * l'écran).
     *
     * On DÉDUIT le contrôleur du liage plutôt que de le déclarer une seconde fois
     * dans le `suggested_action` du topic : la relation « ce topic documente cet
     * écran » est déjà écrite dans phenyx_assistant_topic_screen, la redire
     * ouvrirait la porte aux deux déclarations divergentes. Un plugin qui ajoute
     * un écran documenté obtient donc le bouton d'ouverture sans rien de plus
     * qu'une ligne dans assistant/screens.php.
     *
     * ⚠️ Les liages `source_type = 'menu'` sont EXCLUS : leur controller_name est
     * un onglet parent (AdminParentServices…), qui n'a pas d'écran à ouvrir.
     *
     * @param int $idTopic
     * @return string|null
     */
    public static function getBoundControllerForTopic($idTopic) {

        if (!(int) $idTopic) {
            return null;
        }

        try {
            $controller = self::getCrmDb()->getValue(
                (new DbQuery())
                    ->select('controller_name')
                    ->from('phenyx_assistant_topic_screen')
                    ->where('id_phenyx_assistant_topic = ' . (int) $idTopic)
                    ->where('source_type != \'menu\'')
                    ->orderBy('priority DESC')
            );
        } catch (\Throwable $e) {
            return null;
        }

        return $controller ? (string) $controller : null;
    }

    /**
     * Action « ouvrir l'écran documenté par ce topic », libellée avec le NOM DU
     * MENU et non le nom de classe du contrôleur.
     *
     * ⚠️ Retour Jeff 2026-07-25 : « Ouvrir AdminInvoiceModels » ne veut rien dire
     * pour un employé. Le bon libellé est celui qu'il lit dans le menu — « Gérer
     * les modèles de Facture » — c'est-à-dire le `name` traduit de back_tab, la
     * même source que l'attribut data-name des onglets.
     *
     * Le mécanisme `labelKey` existant reste utile pour un libellé RÉDIGÉ (« Ouvrez
     * les paramètres de votre entreprise »), mais il suppose de déclarer une
     * globale JS par écran : intenable sur ~70 contrôleurs. Ici le libellé est
     * DÉDUIT, donc gratuit et automatiquement juste — y compris quand un plugin
     * renomme une entrée de menu.
     *
     * ⚠️ Deux bases, encore : le liage vit dans la base CRM, back_tab dans la base
     * boutique. D'où les deux étapes. getTab() passant par CacheApi, le surcoût
     * réel est négligeable.
     *
     * @param int $idTopic
     * @param int $idLang
     * @return array|null Action prête pour PhenyxAssistantAnswer::$suggestedAction
     */
    /**
     * Complète une action « openTargetController » avec le LIBELLÉ DU MENU de
     * l'écran visé, quand elle n'en porte pas déjà un.
     *
     * ⚠️ Pourquoi cette fonction existe (retour Jeff, 2026-07-27).
     *
     * Le libellé déduit était posé par getScreenActionForTopic() — mais celle-ci
     * n'est appelée QUE lorsque le topic ne déclare aucune action. Or la plupart
     * des topics d'écran en déclarent une, par exemple
     * ['type' => 'openTargetController', 'controller' => 'AdminUsers'], sans
     * libellé. Résultat : le bouton retombait sur le repli historique et
     * affichait « Ouvrir AdminUsers » — un nom de classe PHP, qui ne veut rien
     * dire pour un employé, là où le menu qu'il a sous les yeux dit « Liste des
     * clients ».
     *
     * On décore donc l'action APRÈS son assemblage, quelle que soit son origine,
     * déclarée ou déduite. Rien à rédiger dans les topics : le libellé vient de
     * back_tab_lang, il est déjà traduit et il suit automatiquement un
     * renommage d'onglet.
     *
     * Trois cas laissés intacts :
     *  - une action qui porte déjà 'label' (rien à faire) ;
     *  - une action qui porte 'labelKey' — libellé RÉDIGÉ, prioritaire côté JS,
     *    cf. renderPhenyxAssistantSuggestedAction() ;
     *  - un contrôleur absent de back_tab : pas de libellé, repli sur la classe.
     *
     * @param array|null $action Action unique ou TABLEAU d'actions (un topic peut
     *                           en proposer plusieurs, cf. core.tour.company)
     * @param int        $idLang
     * @return array|null L'action décorée, dans la même forme qu'en entrée
     */
    public static function decorateActionLabels($action, $idLang) {

        if (empty($action) || !is_array($action)) {
            return $action;
        }

        // Tableau d'actions : liste sans clé 'type' à sa racine.
        if (!isset($action['type'])) {

            foreach ($action as $key => $single) {
                $action[$key] = static::decorateActionLabels($single, $idLang);
            }

            return $action;
        }

        if ($action['type'] !== 'openTargetController') {
            return $action;
        }

        if (empty($action['controller']) || !empty($action['label']) || !empty($action['labelKey'])) {
            return $action;
        }

        try {
            $idTab = (int) BackTab::getIdFromClassName((string) $action['controller']);

            if ($idTab) {
                $tab = BackTab::getTab((int) $idLang, $idTab);

                if (is_array($tab) && !empty($tab['name'])) {
                    $action['label'] = (string) $tab['name'];
                }

            }

        } catch (\Throwable $e) {
            // Un libellé manquant dégrade l'ergonomie, il ne doit jamais empêcher
            // l'action : on rend l'action telle quelle.
        }

        return $action;
    }

    public static function getScreenActionForTopic($idTopic, $idLang) {

        $controller = self::getBoundControllerForTopic($idTopic);

        if (!$controller) {
            return null;
        }

        $action = [
            'type'       => 'openTargetController',
            'controller' => $controller,
        ];

        try {
            $idTab = (int) BackTab::getIdFromClassName($controller);

            if ($idTab) {
                $tab = BackTab::getTab((int) $idLang, $idTab);

                if (is_array($tab) && !empty($tab['name'])) {
                    // Consommé par renderPhenyxAssistantSuggestedAction() côté JS,
                    // qui préfixe avec « Ouvrir ». Absent = repli sur le nom de
                    // classe, comme avant.
                    $action['label'] = (string) $tab['name'];
                }

            }

        } catch (\Throwable $e) {
            // Un libellé manquant dégrade l'ergonomie, il ne doit pas empêcher
            // l'action : on renvoie l'action sans 'label'.
        }

        return $action;
    }

    /**
     * Construit des puces de navigation à partir de codes de topics.
     *
     * Raccourci destiné aux plugins qui rattachent leurs écrans à une réponse du
     * cœur via le hook actionAssistantAnswerAugment (cf.
     * PhenyxAssistantAnswer::$topicChips) : ils déclarent des `code`, on leur
     * rend des paires idTopic/label prêtes à renvoyer.
     *
     * Le libellé vient du champ `label` du topic, donc DÉJÀ TRADUIT dans les 11
     * langues — un plugin n'a aucun texte à rédiger pour se rendre visible dans
     * le tour, contrairement à une contribution par 'text' qui devrait être
     * traduite à part.
     *
     * Les codes inconnus ou inactifs sont silencieusement ignorés : un plugin
     * partiellement seedé produit moins de puces, il ne casse pas la réponse.
     *
     * @param string[] $codes
     * @param int      $idLang
     * @return array [['idTopic' => int, 'label' => string], ...] dans l'ordre des codes
     */
    public static function buildTopicChips(array $codes, $idLang) {

        $out = [];

        foreach ($codes as $code) {
            $row = self::getRowByCode($code, $idLang);

            if (!$row) {
                continue;
            }

            $label = !empty($row['label']) ? (string) $row['label'] : (string) $row['code'];
            $out[] = [
                'idTopic' => (int) $row['id_phenyx_assistant_topic'],
                'label'   => $label,
            ];
        }

        return $out;
    }

    /**
     * @var array|null Cache par requête de getVisibleControllers().
     */
    protected static $visibleControllersCache = null;

    /**
     * @var array|null Cache par requête de getBoundControllersByTopic().
     */
    protected static $boundControllersCache = null;

    /**
     * Contrôleurs liés, indexés par id de topic — en UNE requête.
     *
     * ⚠️ getBoundControllerForTopic() existe déjà mais interroge un topic à la
     * fois : l'appeler pendant le scoring ferait une requête par candidat vers la
     * base CRM DISTANTE, sur le chemin le plus chaud de l'assistant. La table des
     * liages tient en quelques dizaines de lignes, on la charge d'un coup.
     *
     * Les liages `source_type = 'menu'` sont inclus : une famille de menu invisible
     * doit elle aussi disparaître du scoring, au même titre qu'un écran.
     *
     * @return array [id_topic => class_name, ...]
     */
    public static function getBoundControllersByTopic() {

        if (static::$boundControllersCache !== null) {
            return static::$boundControllersCache;
        }

        $map = [];

        try {
            $rows = self::getCrmDb()->executeS(
                (new DbQuery())
                    ->select('id_phenyx_assistant_topic, controller_name, source_type')
                    ->from('phenyx_assistant_topic_screen')
            );
        } catch (\Throwable $e) {
            // Table absente ou base CRM injoignable : on renvoie une carte vide.
            // Conséquence assumée — aucun topic ne sera considéré comme restreint,
            // donc l'assistant se comporte comme avant ce lot plutôt que de se
            // taire sur tout.
            static::$boundControllersCache = [];

            return [];
        }

        foreach ((array) $rows as $row) {
            $idTopic = (int) $row['id_phenyx_assistant_topic'];

            // Un topic peut porter plusieurs liages (list + edit…), tous vers le
            // MÊME écran en pratique. Le premier suffit pour juger de sa
            // visibilité.
            if (!isset($map[$idTopic])) {
                $map[$idTopic] = (string) $row['controller_name'];
            }

        }

        static::$boundControllersCache = $map;

        return $map;
    }

    /**
     * Ce topic est-il accessible à l'employé courant ?
     *
     * Règle retenue avec Jeff le 2026-07-26 : un topic SANS liage d'écran reste
     * visible de tous. Le tour, l'aide sur les plugins ou une explication générale
     * ne dépendent d'aucun droit, et les filtrer rendrait l'assistant muet sur
     * tout ce qui n'est pas un écran. Seul un topic qui documente un écran ABSENT
     * du menu de cet employé est écarté.
     *
     * @param int $idTopic
     * @return bool
     */
    public static function isTopicAllowed($idTopic) {

        $map = static::getBoundControllersByTopic();
        $idTopic = (int) $idTopic;

        if (!isset($map[$idTopic])) {
            return true;
        }

        return static::isControllerVisible($map[$idTopic]);
    }

    /**
     * Ensemble des `class_name` que CET employé voit réellement dans son menu.
     *
     * ⚠️ POURQUOI PASSER PAR generateTabs() (retour Jeff, 2026-07-26 : « le menu
     * est variable en fonction du profil de l'employé »).
     *
     * getMenuFamilyTopics() et getMenuChildrenTopics() rejouaient à la main les
     * filtres du menu, et n'en appliquaient que DEUX sur les quatre :
     *
     *   1. BackTab::checkTabRights()            — repris
     *   2. master vs employee->phenyx_admin     — repris
     *   3. Plugin::isActive($tab['plugin'])     — OUBLIÉ
     *   4. (bool) $tab['active']                — OUBLIÉ
     *
     * Un onglet appartenant à un plugin désactivé, ou désactivé lui-même,
     * produisait donc quand même une puce. Dupliquer une règle métier, c'est
     * s'engager à la maintenir en double : on lit désormais la source.
     *
     * Bénéfice au passage : generateTabs() est déjà mis en cache par employé
     * (clé `generateTabs_{id}`), donc l'appel est bon marché, et tout filtre qui
     * y sera ajouté plus tard s'appliquera ici sans qu'on y touche.
     *
     * @return array|null Ensemble class_name => true. NULL si le menu ne peut pas
     *                    être déterminé (pas d'employé connecté) — l'appelant doit
     *                    alors s'abstenir de filtrer plutôt que de tout masquer.
     */
    public static function getVisibleControllers() {

        if (static::$visibleControllersCache !== null) {
            return static::$visibleControllersCache ?: null;
        }

        $context = Context::getContext();

        if (!isset($context->employee) || empty($context->employee->id) || !isset($context->_tools)) {
            // Pas d'employé : generateTabs() refuse de travailler (et le
            // journalise). On renvoie null = « inconnu », surtout pas un ensemble
            // vide qui ferait passer tous les écrans pour interdits.
            return null;
        }

        try {
            $tabs = $context->_tools->generateTabs();
        } catch (\Throwable $e) {
            return null;
        }

        if (!is_array($tabs) || !$tabs) {
            return null;
        }

        $visible = [];

        // Parcours récursif : l'arbre descend jusqu'au niveau 3 (sub_tabs de
        // sub_tabs), et ph_ecommerce l'utilise réellement sur trois niveaux.
        $walk = function ($nodes) use (&$walk, &$visible) {

            foreach ((array) $nodes as $node) {

                if (!empty($node['class_name'])) {
                    $visible[(string) $node['class_name']] = true;
                }

                if (!empty($node['sub_tabs'])) {
                    $walk($node['sub_tabs']);
                }

            }

        };

        $walk($tabs);

        static::$visibleControllersCache = $visible;

        return $visible ?: null;
    }

    /**
     * Cet employé voit-il cet écran dans son menu ?
     *
     * @param string $className
     * @return bool True aussi quand le menu est indéterminable : en cas de doute
     *              on n'entrave pas l'assistant.
     */
    public static function isControllerVisible($className) {

        $visible = static::getVisibleControllers();

        if ($visible === null) {
            return true;
        }

        return isset($visible[(string) $className]);
    }

    /**
     * Familles de menu de NIVEAU 1 réellement présentes sur ce site, jointes aux
     * topics qui les documentent.
     *
     * ⚠️ Motivation (retour Jeff 2026-07-25). Le topic `core.tour` listait ses
     * sept familles en dur dans ses `quick_replies`. Or le menu du back-office
     * est une DONNÉE (eph_back_tab), qu'un plugin remanie : ph_ecommerce ajoute
     * des familles (Nos produits, Gestion Commerciale, Paramètres comptables,
     * Statements…) et surtout DÉPLACE AdminCms et AdminContentAnyWhere sous
     * d'autres parents — deux des sept puces du cœur pointaient donc vers des
     * familles qui n'existent plus au niveau 1 sur ephenyx.io. Le hook
     * d'enrichissement ne pouvait pas corriger ça : il sait ajouter, pas retirer.
     *
     * Le tour est donc DÉRIVÉ du menu au lieu d'être affirmé. Trois bénéfices
     * au-delà du bug : c'est juste sur tous les sites et pour tout futur plugin
     * sans une ligne à changer ; les libellés des puces reprennent le `name`
     * traduit du back_tab, donc exactement ce que l'employé lit à l'écran (la
     * puce disait « Gestion des pages CMS » quand le menu affiche « Gestion des
     * pages de type CMS ») ; et les DROITS sont respectés, puisqu'on part de la
     * même source filtrée que le menu lui-même.
     *
     * ⚠️ Requête en DEUX temps, imposée par la répartition des bases : back_tab
     * vit dans la base BOUTIQUE, phenyx_assistant_topic_screen dans la base CRM.
     * Aucune jointure SQL possible — on croise en PHP, en conservant l'ordre du
     * menu (`position`), qui est celui que l'employé a sous les yeux.
     *
     * @param int $idLang
     * @return array [['idTopic' => int, 'code' => string, 'label' => string], ...]
     */
    public static function getMenuFamilyTopics($idLang) {

        $idLang = (int) $idLang;

        /* --- 1. Les familles visibles, depuis la base boutique ------------- */

        try {
            // idParent = 1 : la racine du menu, cf.
            // PhenyxTool::generateTabs() qui appelle getBackTabs($lang, 1).
            $families = BackTab::getBackTabs($idLang, 1);
        } catch (\Throwable $e) {
            return [];
        }

        if (!is_array($families) || !$families) {
            return [];
        }

        $byClass = [];

        foreach ($families as $family) {

            if (empty($family['class_name'])) {
                continue;
            }

            // Filtrage délégué à getVisibleControllers(), donc à generateTabs() :
            // les QUATRE règles du menu s'appliquent, pas seulement les deux que
            // cette boucle rejouait auparavant (cf. la note de cette méthode).
            if (!static::isControllerVisible($family['class_name'])) {
                continue;
            }

            $byClass[(string) $family['class_name']] = isset($family['name']) ? (string) $family['name'] : (string) $family['class_name'];
        }

        if (!$byClass) {
            return [];
        }

        /* --- 2. Les topics liés à ces familles, depuis la base CRM --------- */

        try {
            $rows = self::getCrmDb()->executeS(
                (new DbQuery())
                    ->select('ts.controller_name, t.id_phenyx_assistant_topic, t.code')
                    ->from('phenyx_assistant_topic_screen', 'ts')
                    ->innerJoin('phenyx_assistant_topic', 't', 't.id_phenyx_assistant_topic = ts.id_phenyx_assistant_topic AND t.active = 1')
                    ->where('ts.source_type = \'menu\'')
                    ->where('ts.controller_name IN (\'' . implode('\', \'', array_map('pSQL', array_keys($byClass))) . '\')')
            );
        } catch (\Throwable $e) {
            return [];
        }

        if (!is_array($rows) || !$rows) {
            return [];
        }

        $topicByClass = [];

        foreach ($rows as $row) {
            $topicByClass[(string) $row['controller_name']] = $row;
        }

        /* --- 3. Croisement, dans l'ordre du menu -------------------------- */

        $out = [];

        foreach ($byClass as $className => $menuLabel) {

            /*
             * ⚠️ RENVERSEMENT DE LA RÈGLE (Jeff, 2026-07-26).
             *
             * Une famille non documentée produisait jusqu'ici AUCUNE puce. Le tour
             * ne montrait donc que ce qui était déjà rédigé — « Gestion des
             * Partenaires » et « Outils » manquaient à l'appel sans que rien ne
             * l'explique, et l'employé n'avait aucun moyen de savoir si la famille
             * n'existait pas ou si l'assistant l'ignorait.
             *
             * Le MENU est désormais le squelette, les topics la chair optionnelle :
             * chaque entrée visible produit sa puce, documentée ou non. Une puce
             * sans topic porte idTopic = 0 ; l'assistant répondra qu'il n'a pas
             * encore d'information sur cet écran, tout en proposant de l'ouvrir.
             *
             * Dire « je ne sais pas encore » sur un écran nommé vaut mieux que de
             * faire comme s'il n'existait pas — et ça transforme le tour en
             * inventaire honnête plutôt qu'en vitrine de ce qui est fait.
             */
            $documented = isset($topicByClass[$className]);

            $out[] = [
                'idTopic' => $documented ? (int) $topicByClass[$className]['id_phenyx_assistant_topic'] : 0,
                'code'    => $documented ? (string) $topicByClass[$className]['code'] : '',
                // Le libellé vient du MENU, pas du topic : c'est le mot que
                // l'employé a sous les yeux.
                'label'   => $menuLabel,
                // Indispensable quand idTopic vaut 0 : c'est alors la seule chose
                // qui permette au navigateur de désigner l'écran au serveur.
                'controller' => $className,
            ];
        }

        return $out;
    }

    /**
     * Ordre de préférence des `source_type` quand un même contrôleur porte
     * plusieurs liages. La contrainte UNIQUE porte sur (controller_name,
     * source_type) : un écran peut donc être lié plusieurs fois (une fiche
     * d'accueil en 'list', un topic de formulaire en 'edit', etc.). Une puce de
     * famille doit pointer vers UN topic — celui qui présente l'écran.
     *
     * @var array
     */
    protected static $chipSourceTypePreference = ['menu', 'list', 'view', 'edit', 'add'];

    /**
     * Le contrôleur auquel ce topic est lié en tant que FAMILLE de menu, ou null.
     * Pendant de getBoundControllerForTopic(), qui exclut au contraire
     * `source_type = 'menu'` parce qu'il cherche un écran à ouvrir : une famille
     * n'est pas un écran, elle n'a pas d'action « ouvrir ».
     *
     * @param int $idTopic
     * @return string|null
     */
    public static function getMenuBindingForTopic($idTopic) {

        if (!(int) $idTopic) {
            return null;
        }

        try {
            $controller = self::getCrmDb()->getValue(
                (new DbQuery())
                    ->select('controller_name')
                    ->from('phenyx_assistant_topic_screen')
                    ->where('id_phenyx_assistant_topic = ' . (int) $idTopic)
                    ->where('source_type = \'menu\'')
            );
        } catch (\Throwable $e) {
            return null;
        }

        return $controller ? (string) $controller : null;
    }

    /**
     * Puces d'une FAMILLE de menu, dérivées de ses onglets ENFANTS.
     *
     * ⚠️ Motivation (mesure du 2026-07-26, §4ter de check_assistant.php). Le tour
     * de « Société » ne proposait que TROIS puces, et les trois venaient des
     * hooks d'augmentation de ph_einvoicing et ph_ecommerce. « Identité »,
     * « Gestion des contenus d'email » et « Configuration de l'ERP » avaient
     * pourtant chacun un topic actif et un liage d'étage 0 — ils étaient
     * simplement invisibles dans le tour, parce qu'un topic de famille servait
     * ses `quick_replies` stockées plus ce que les plugins voulaient bien
     * ajouter. Autrement dit : un écran n'apparaissait que si QUELQU'UN avait
     * écrit un hook pour lui.
     *
     * getMenuFamilyTopics() avait déjà résolu ce problème, mais au seul NIVEAU 1
     * (les familles vues depuis la racine du tour). Cette méthode applique la
     * même idée un cran plus bas, pour n'importe quel parent. Conséquence
     * d'échelle, et c'est le vrai gain : sur les ~209 écrans encore à documenter,
     * écrire le liage SUFFIT à faire apparaître l'écran dans le tour de sa
     * famille. Plus de puce à maintenir en parallèle, dans 11 langues, ni de hook
     * à écrire écran par écran.
     *
     * La composition se fait naturellement : un enfant qui est lui-même un parent
     * (AdminParentConfig sous Société) reçoit une puce vers son propre topic de
     * famille, lequel dérivera à son tour ses enfants.
     *
     * ⚠️ Deux bases : back_tab est dans la base BOUTIQUE, les liages dans la base
     * CRM. Aucune jointure SQL possible — on croise en PHP, en conservant l'ordre
     * du menu (`position`), qui est celui que l'employé a sous les yeux.
     *
     * @param string $parentClassName Nom de classe de la famille (ex. AdminParentCompany)
     * @param int    $idLang
     * @return array [['idTopic' => int, 'code' => string, 'label' => string], ...]
     */
    /**
     * Extrait l'argument passé à openAjaxController() dans le `function` d'un
     * onglet de back_tab.
     *
     * Les entrées de menu portent leur action sous forme de code, par exemple
     * `openAjaxController('AdminCustomerPieces', 'INVOICE')`. Le second argument
     * est ce qui distingue Devis, Commande, Bon de livraison et Facture — quatre
     * entrées pour un seul contrôleur.
     *
     * Rend une chaîne vide quand il n'y a pas d'argument, ce qui est le cas de la
     * grande majorité des onglets.
     *
     * @param string $function Contenu du champ `function` de back_tab
     * @return string
     */
    protected static function extractMenuArgument($function) {

        $function = (string) $function;

        if ($function === '' || strpos($function, ',') === false) {
            return '';
        }

        if (!preg_match('/openAjaxController\(\s*[\'"][A-Za-z_]+[\'"]\s*,\s*[\'"]([A-Za-z0-9_-]+)[\'"]/', $function, $m)) {
            return '';
        }

        return (string) $m[1];
    }

    public static function getMenuChildrenTopics($parentClassName, $idLang) {

        $parentClassName = (string) $parentClassName;
        $idLang = (int) $idLang;

        if ($parentClassName === '') {
            return [];
        }

        /* --- 1. Les enfants visibles, depuis la base boutique -------------- */

        try {
            $idParent = (int) BackTab::getIdFromClassName($parentClassName);

            if (!$idParent) {
                return [];
            }

            $children = BackTab::getBackTabs($idLang, $idParent);
        } catch (\Throwable $e) {
            return [];
        }

        if (!is_array($children) || !$children) {
            return [];
        }

        /*
         * ⚠️ UNE ENTRÉE PAR ONGLET, PAS PAR CLASSE — corrigé le 2026-07-27.
         *
         * Ce tableau était indexé sur `class_name`. Or un même contrôleur peut
         * occuper PLUSIEURS entrées de menu, distinguées par l'argument passé à
         * openAjaxController() : dans Gestion Commerciale, AdminCustomerPieces
         * apparaît cinq fois (Devis, Commandes, Bons de livraison, Factures…) et
         * AdminSupplierPieces six fois, l'argument valant le type de pièce.
         *
         * Les entrées s'écrasaient donc mutuellement, la dernière gagnant : la
         * rubrique « Ventes » ne rendait que 3 pastilles sur 6, et « Achats » 3
         * sur 8. Devis, Commandes et Bons de livraison n'existaient tout
         * simplement pas pour l'assistant, alors qu'ils sont sous les yeux de
         * l'employé dans son menu.
         *
         * La bonne clé est l'onglet lui-même : chaque ligne de back_tab est un
         * écran distinct du point de vue de l'employé, même quand deux lignes
         * pointent la même classe PHP.
         *
         * L'argument est conservé dans 'arg' — il ne coûte rien, il documente ce
         * que la pastille désigne réellement, et il servira le jour où le bouton
         * d'ouverture devra rouvrir le bon type de pièce plutôt que l'écran nu.
         */

        $entries = [];

        foreach ($children as $child) {

            if (empty($child['class_name'])) {
                continue;
            }

            // Idem getMenuFamilyTopics() : filtrage délégué à generateTabs() via
            // getVisibleControllers(), pour ne jamais proposer un écran absent du
            // menu de CET employé.
            if (!static::isControllerVisible($child['class_name'])) {
                continue;
            }

            $entries[] = [
                'className' => (string) $child['class_name'],
                'label'     => isset($child['name']) && $child['name'] !== ''
                ? (string) $child['name']
                : (string) $child['class_name'],
                'arg'       => static::extractMenuArgument(isset($child['function']) ? $child['function'] : ''),
            ];
        }

        if (!$entries) {
            return [];
        }

        // Dédoublonnage des classes pour la seule requête CRM ci-dessous : cinq
        // entrées AdminCustomerPieces ne justifient pas cinq fois le même IN().
        $byClass = [];

        foreach ($entries as $entry) {
            $byClass[$entry['className']] = $entry['label'];
        }

        /* --- 2. Les topics liés à ces écrans, depuis la base CRM ----------- */

        try {
            $rows = self::getCrmDb()->executeS(
                (new DbQuery())
                    ->select('ts.controller_name, ts.source_type, t.id_phenyx_assistant_topic, t.code')
                    ->from('phenyx_assistant_topic_screen', 'ts')
                    ->innerJoin('phenyx_assistant_topic', 't', 't.id_phenyx_assistant_topic = ts.id_phenyx_assistant_topic AND t.active = 1')
                    ->where('ts.controller_name IN (\'' . implode('\', \'', array_map('pSQL', array_keys($byClass))) . '\')')
            );
        } catch (\Throwable $e) {
            /*
             * ⚠️ On CONTINUE avec une liste vide au lieu de rendre [] (corrigé le
             * 2026-07-27). Cf. la note ci-dessous : les puces viennent du menu,
             * pas du wiki. Une base CRM injoignable doit dégrader la réponse en
             * « voici les écrans, non documentés », pas la supprimer.
             */
            $rows = [];
        }

        if (!is_array($rows)) {
            $rows = [];
        }

        /*
         * ⚠️ PAS de `return []` quand $rows est vide.
         *
         * C'était le cas jusqu'au 2026-07-27, et cela contredisait la règle posée
         * juste en dessous à l'étape 4 : « tout écran visible a sa puce, documenté
         * ou non ». Le menu est le squelette, les topics ne sont que la chair.
         *
         * Conséquence du bug : un regroupement dont AUCUN enfant n'était encore
         * documenté ne rendait rien du tout — donc pas de puces, donc l'assistant
         * répondait « je ne sais rien » sur le parent ET n'offrait aucun chemin,
         * alors qu'il connaissait parfaitement les noms de ses enfants. C'est
         * exactement le pire cas : celui d'un domaine fonctionnel neuf, où
         * l'employé a le plus besoin d'être orienté.
         */

        /* --- 3. Un seul topic par écran, le plus « présentation » ---------- */

        $topicByClass = [];

        foreach ($rows as $row) {
            $className = (string) $row['controller_name'];
            $rank = array_search((string) $row['source_type'], static::$chipSourceTypePreference, true);
            // Un source_type inconnu passe en dernier plutôt que d'être écarté :
            // mieux vaut une puce que rien si c'est le seul liage de l'écran.
            $rank = ($rank === false) ? count(static::$chipSourceTypePreference) : (int) $rank;

            if (!isset($topicByClass[$className]) || $rank < $topicByClass[$className]['rank']) {
                $topicByClass[$className] = [
                    'rank'       => $rank,
                    'idTopic'    => (int) $row['id_phenyx_assistant_topic'],
                    'code'       => (string) $row['code'],
                    'sourceType' => (string) $row['source_type'],
                ];
            }

        }

        /* --- 4. Croisement, dans l'ordre du menu -------------------------- */

        $out = [];

        // On boucle sur les ENTRÉES de menu, pas sur les classes : cf. la note de
        // l'étape 1. Plusieurs entrées peuvent partager le même topic — c'est le
        // cas voulu pour les types de pièces, où Devis et Facture désignent le
        // même écran filtré différemment (arbitrage du 2026-07-27).
        foreach ($entries as $entry) {

            $className = $entry['className'];
            $menuLabel = $entry['label'];

            // Même renversement que dans getMenuFamilyTopics() : tout écran visible
            // a sa puce, documenté ou non.
            $documented = isset($topicByClass[$className]);

            $out[] = [
                'arg' => $entry['arg'],
                'idTopic' => $documented ? $topicByClass[$className]['idTopic'] : 0,
                'code'    => $documented ? $topicByClass[$className]['code'] : '',
                // Le libellé vient du MENU, pas du topic : c'est le mot que
                // l'employé a sous les yeux.
                'label'   => $menuLabel,
                'controller' => $className,
                'sourceType' => $documented ? $topicByClass[$className]['sourceType'] : '',
            ];
        }

        return $out;
    }

    /*
     * NOTE (2026-07-26) — buildOpenActionsFromChildren() a existé ici quelques
     * heures et a été retirée.
     *
     * Elle dérivait un bouton « Ouvrir … » par écran enfant d'une famille, pour
     * combler l'asymétrie entre « Société » (trois boutons rédigés à la main) et
     * « Configuration de l'ERP » (aucun). C'était aligner par le haut alors qu'il
     * fallait aligner par le bas : la règle retenue est qu'une famille ORIENTE
     * (elle propose ses onglets sous forme de puces) et qu'un écran AGIT (sa
     * réponse porte le bouton d'ouverture). Doubler chaque puce d'un bouton
     * n'ouvrait aucun chemin nouveau et encombrait la popup.
     *
     * Les clés 'controller' et 'sourceType' ajoutées à getMenuChildrenTopics()
     * sont conservées : elles ne coûtent rien, ne sont pas lues côté navigateur,
     * et documentent ce que chaque puce désigne.
     */

    /**
     * Fusionne deux listes de puces en éliminant les doublons par idTopic, la
     * première liste gagnant en cas de conflit.
     *
     * ⚠️ Nécessaire pour la COEXISTENCE avec les hooks d'augmentation déjà
     * déployés : ph_einvoicing contribue explicitement ses deux écrans au tour de
     * « Société », et ph_ecommerce son modèle de facture. Or ces trois écrans sont
     * aussi des enfants d'AdminParentCompany, donc désormais dérivés
     * automatiquement. Sans déduplication, ils apparaîtraient deux fois.
     *
     * On ne retire pas ces hooks pour autant : un plugin peut légitimement vouloir
     * pousser une puce vers un écran qui n'est PAS un enfant de la famille
     * décrite, et la dérivation ne le remplace pas dans ce cas.
     *
     * @param array $chips   Puces dérivées du menu (prioritaires : libellé du menu)
     * @param array $extra   Puces contribuées (hooks, contenu stocké)
     * @return array
     */
    public static function mergeTopicChips(array $chips, array $extra) {

        /*
         * ⚠️ La clé de dédoublonnage ne peut PLUS être le seul idTopic depuis que
         * les écrans non documentés produisent eux aussi une puce (2026-07-26) :
         * ils portent tous idTopic = 0, et un dédoublonnage sur cette valeur les
         * réduirait à une seule puce. On retombe donc sur le contrôleur quand il
         * n'y a pas de topic, et on n'écarte une puce sans identité d'aucune sorte
         * que parce qu'on ne saurait pas quoi en faire au clic.
         */
        /*
         * ⚠️ L'ARGUMENT DE MENU ENTRE DANS LA CLÉ — ajouté le 2026-07-27.
         *
         * Sans lui, le correctif d'indexation de getMenuChildrenTopics() serait
         * resté sans effet : les cinq entrées AdminCustomerPieces portent le
         * MÊME idTopic (un topic par contrôleur, arbitrage du jour), donc un
         * dédoublonnage sur le seul idTopic les aurait de nouveau réduites à une.
         * Deux entrées ne sont le même écran que si elles partagent aussi leur
         * argument.
         */
        $keyOf = function ($chip) {

            $arg = isset($chip['arg']) && $chip['arg'] !== '' ? '#' . (string) $chip['arg'] : '';

            if (!empty($chip['idTopic'])) {
                return 't' . (int) $chip['idTopic'] . $arg;
            }

            if (!empty($chip['controller'])) {
                return 'c' . (string) $chip['controller'] . $arg;
            }

            return null;
        };

        $seen = [];

        foreach ($chips as $chip) {
            $key = $keyOf($chip);

            if ($key !== null) {
                $seen[$key] = true;
            }

        }

        foreach ($extra as $chip) {
            $key = $keyOf($chip);

            if ($key === null || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $chips[] = $chip;
        }

        return $chips;
    }

    /**
     * Action « ouvrir cet écran » pour un contrôleur donné, ou null si l'ouvrir
     * n'aurait pas de sens.
     *
     * ⚠️ Un PARENT de menu n'ouvre rien : « Ouvrir Gestion des Partenaires »
     * produirait un clic sans effet, puisque simulateBackTabNavigation() déroulerait
     * un sous-menu au lieu d'atteindre un écran. On le détecte par la présence
     * d'ENFANTS dans l'arbre — c'est un fait du menu, pas une convention de nommage
     * sur « AdminParent… » à laquelle un plugin n'est pas tenu.
     *
     * @param string $className
     * @param int    $idLang
     * @return array|null
     */
    public static function buildOpenActionForController($className, $idLang) {

        $className = (string) $className;
        $idTab = (int) BackTab::getIdFromClassName($className);

        if (!$idTab) {
            return null;
        }

        try {
            $children = BackTab::getBackTabs((int) $idLang, $idTab);
        } catch (\Throwable $e) {
            $children = [];
        }

        if (is_array($children) && $children) {
            // C'est une famille : elle se parcourt, elle ne s'ouvre pas.
            return null;
        }

        $label = $className;
        $tab = BackTab::getTab((int) $idLang, $idTab);

        if (is_array($tab) && !empty($tab['name'])) {
            $label = (string) $tab['name'];
        }

        return [
            'type'       => 'openTargetController',
            'controller' => $className,
            'label'      => $label,
        ];
    }

    /**
     * Une ligne de topic par son id, dans la langue demandée. Pendant de
     * getRowByCode(), pour les puces qui désignent un topic par son id (cf.
     * PhenyxAssistantAnswer::$topicChips).
     *
     * @param int $idTopic
     * @param int $idLang
     * @return array|null
     */
    public static function getRowById($idTopic, $idLang) {

        if (!(int) $idTopic) {
            return null;
        }

        $fetch = function ($lang) use ($idTopic) {

            return self::getCrmDb()->getRow(
                (new DbQuery())
                    ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.label, tl.keywords, tl.answer, tl.quick_replies')
                    ->from('phenyx_assistant_topic', 't')
                    ->innerJoin('phenyx_assistant_topic_lang', 'tl', 'tl.id_phenyx_assistant_topic = t.id_phenyx_assistant_topic AND tl.id_lang = ' . (int) $lang)
                    ->where('t.active = 1')
                    ->where('t.id_phenyx_assistant_topic = ' . (int) $idTopic)
            );
        };

        try {
            $row = $fetch((int) $idLang);

            if (!$row) {
                $fallback = (int) Configuration::getInstance()->get('EPH_LANG_DEFAULT');

                if ($fallback && $fallback !== (int) $idLang) {
                    $row = $fetch($fallback);
                }

            }

        } catch (\Throwable $e) {
            return null;
        }

        return $row ?: null;
    }

    /**
     * La colonne tab_key existe-t-elle sur la table des liages ?
     *
     * Garde de version pour la dimension ONGLET (migration
     * upgrade_assistant_tab_key.php). Même motif que hasAnyScreenBinding()
     * ci-dessous, et pour la même raison : la base CRM est partagée, ce code
     * peut la précéder. Sans cette garde, une requête mentionnant tab_key sur
     * une base non migrée rendrait false sans lever — DbPDO n'expose pas les
     * erreurs SQL — et l'étage 0 entier deviendrait muet en silence.
     *
     * Résultat mémorisé pour la durée de la requête PHP.
     *
     * @return bool
     */
    public static function hasTabKeyColumn() {

        static $has = null;

        if ($has !== null) {
            return $has;
        }

        try {
            $colonnes = self::getCrmDb()->executeS(
                'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'phenyx_assistant_topic_screen` LIKE \'tab_key\''
            );
            $has = (is_array($colonnes) && count($colonnes) > 0);
        } catch (\Throwable $e) {
            $has = false;
        }

        return $has;
    }

    /**
     * Existe-t-il AU MOINS UN liage écran → topic en base ?
     *
     * Garde anti-régression indispensable au déploiement de l'étage 0. Le paquet
     * vendor est partagé entre plusieurs sites : si le code de l'étage 0 arrive
     * sur un site où le seeder (install_assistant_screens.php) n'a pas encore
     * tourné, TOUTE l'aide contextuelle deviendrait muette d'un coup, sur tous
     * les écrans. Tant que la table est vide, on conserve donc l'ancien
     * comportement (scoring du libellé d'écran) — dégradé mais fonctionnel — et
     * on bascule automatiquement sur l'étage 0 dès le premier liage inséré.
     *
     * Résultat mémorisé pour la durée de la requête : appelé à chaque question,
     * mais la réponse ne change pas en cours de requête.
     *
     * @return bool
     */
    public static function hasAnyScreenBinding() {

        static $has = null;

        if ($has !== null) {
            return $has;
        }

        try {
            $db = self::getCrmDb();

            // ⚠️ PAS de ->limit(1) ici : Db::getRow() — auquel getValue() délègue
            // — ajoute INCONDITIONNELLEMENT ' LIMIT 1' à la requête, sans
            // vérifier s'il y en a déjà un. La version initiale produisait donc
            // « ... LIMIT 1 LIMIT 1 », erreur MariaDB 1064 relevée dans les logs
            // le 2026-07-25. Le SELECT 1 suffit : on ne veut savoir que si AU
            // MOINS une ligne existe.
            $has = (bool) $db->getValue(
                (new DbQuery())
                    ->select('1')
                    ->from('phenyx_assistant_topic_screen')
            );

            // ⚠️ Contrôle d'erreur EXPLICITE, indispensable ici : DbPDO::_query()
            // intercepte la PDOException, la journalise et renvoie false — il ne
            // la relance PAS. Un `try/catch` ne voit donc jamais une erreur SQL,
            // et « requête en échec » est indistinguable de « table vide », les
            // deux donnant false. C'est exactement ce qui a rendu le bug du
            // LIMIT dupliqué invisible : l'étage 0 restait inactif, l'assistant
            // retombait silencieusement sur l'ancien comportement, et la seule
            // trace était une ligne dans le log PDO.
            if (!$has && (int) $db->getNumberError() !== 0) {
                PhenyxLogger::addLog(
                    sprintf(
                        'PhenyxAssistantTopic::hasAnyScreenBinding : erreur SQL %s (%s) — étage 0 désactivé, l\'assistant retombe sur le scoring du libellé d\'écran. Vérifier que sql/phenyx_assistant_stages.sql a bien été exécuté sur la base CRM.',
                        $db->getNumberError(),
                        $db->getMsgError()
                    ),
                    3,
                    null,
                    static::class
                );
            }

        } catch (\Throwable $e) {
            // Ne couvre en pratique que l'échec de CONNEXION à la base CRM
            // (credentials _EPH_CRM_DB_* absentes, serveur injoignable), pas les
            // erreurs SQL — cf. la note ci-dessus.
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::hasAnyScreenBinding : base CRM injoignable (%s) — étage 0 désactivé.', $e->getMessage()),
                3,
                null,
                static::class
            );
            $has = false;
        }

        return $has;
    }

    /**
     * Nombre de topics actifs contenant chaque mot-clé, pour une langue donnée
     * ("document frequency" de l'IDF, cf.
     * PhenyxAssistantLayer::keywordWeight()).
     *
     * Lu depuis l'index inversé phenyx_assistant_keyword s'il est alimenté, avec
     * repli sur un comptage direct des lignes déjà chargées : l'index n'est
     * qu'une accélération, jamais une dépendance dure.
     *
     * @param int   $idLang
     * @param array $loadedTopics Lignes de getActiveForLang(), utilisées en repli
     * @return array [mot-clé => nombre de topics]
     */
    public static function getDocumentFrequency($idLang, array $loadedTopics = []) {

        $idLang = (int) $idLang;

        if (isset(self::$documentFrequencyCache[$idLang])) {
            return self::$documentFrequencyCache[$idLang];
        }

        $frequency = [];

        try {
            $rows = self::getCrmDb()->executeS(
                (new DbQuery())
                    ->select('k.keyword, COUNT(DISTINCT k.id_phenyx_assistant_topic) AS nb')
                    ->from('phenyx_assistant_keyword', 'k')
                    ->innerJoin('phenyx_assistant_topic', 't', 't.id_phenyx_assistant_topic = k.id_phenyx_assistant_topic AND t.active = 1')
                    ->where('k.id_lang = ' . $idLang)
                    ->groupBy('k.keyword')
            );

            if (is_array($rows)) {

                foreach ($rows as $row) {
                    $frequency[(string) $row['keyword']] = (int) $row['nb'];
                }

            }

        } catch (\Throwable $e) {
            // Index absent ou pas encore alimenté : repli silencieux ci-dessous.
            $frequency = [];
        }

        if (!$frequency) {

            foreach ($loadedTopics as $topic) {

                if (empty($topic['keywords'])) {
                    continue;
                }

                // array_unique : un mot-clé répété dans le même topic ne compte
                // qu'une fois — c'est un "document frequency", pas un "term
                // frequency".
                foreach (array_unique(preg_split('/\s+/u', trim((string) $topic['keywords']))) as $keyword) {

                    if ($keyword === '') {
                        continue;
                    }

                    $frequency[$keyword] = isset($frequency[$keyword]) ? $frequency[$keyword] + 1 : 1;
                }

            }

        }

        self::$documentFrequencyCache[$idLang] = $frequency;

        return $frequency;
    }

    /**
     * Écrit (ou met à jour) un lot de topics. Idempotent par `code`.
     *
     * Factorisé ICI et non dupliqué dans chaque plugin : l'écriture d'un topic
     * multilingue en base CRM suppose de connaître la découverte des langues
     * installées, la convention `label`/`keywords`/`answer`/`quick_replies` et
     * l'encodage JSON des deux champs composites. Recopier ça dans chaque plugin
     * garantirait des divergences — et un plugin qui n'écrirait pas `label`
     * casserait les puces de désambiguïsation.
     *
     * Format attendu de chaque définition (identique à celui de
     * install_assistant_topics.php) :
     *
     *   [
     *       'code'             => 'ph_ecommerce.preferences.search',
     *       'entity_class'     => null,
     *       'confidence'       => 0.85,
     *       'suggested_action' => null,          // ou un tableau
     *       'translations'     => [
     *           'fr' => ['label' => '...', 'keywords' => '...', 'answer' => '...', 'quick_replies' => []],
     *           ...
     *       ],
     *   ]
     *
     * @param array       $definitions
     * @param string|null $sourcePlugin Renseigne la colonne source_plugin, ce qui
     *                                  permet à deactivateForPlugin() de retrouver
     *                                  ces topics à la désinstallation
     * @return array ['created' => int, 'updated' => int, 'failed' => int, 'untranslated' => string[]]
     */
    public static function seedTopics(array $definitions, $sourcePlugin = null) {

        $report = ['created' => 0, 'updated' => 0, 'failed' => 0, 'untranslated' => []];

        $idLangByIso = [];

        foreach (Language::getLanguages(true) as $lang) {
            $idLangByIso[$lang['iso_code']] = (int) $lang['id_lang'];
        }

        if (!$idLangByIso) {
            return $report;
        }

        $now = date('Y-m-d H:i:s');
        $translatedIsos = [];

        foreach ($definitions as $def) {

            if (empty($def['code']) || empty($def['translations'])) {
                $report['failed']++;
                continue;
            }

            try {
                $idTopic = (int) self::getCrmDb()->getValue(
                    (new DbQuery())
                        ->select('id_phenyx_assistant_topic')
                        ->from('phenyx_assistant_topic')
                        ->where('code = \'' . pSQL($def['code']) . '\'')
                );

                $topic = $idTopic ? new static($idTopic) : new static();
                $topic->code = $def['code'];
                $topic->source_plugin = $sourcePlugin;
                $topic->entity_class = isset($def['entity_class']) ? $def['entity_class'] : null;
                $topic->suggested_action = !empty($def['suggested_action']) ? json_encode($def['suggested_action']) : null;
                $topic->confidence = isset($def['confidence']) ? $def['confidence'] : 0.8;
                $topic->active = true;
                $topic->date_upd = $now;

                if (!$idTopic) {
                    $topic->date_add = $now;
                }

                $topic->label = [];
                $topic->keywords = [];
                $topic->answer = [];
                $topic->quick_replies = [];

                foreach ($idLangByIso as $iso => $idLang) {

                    if (!isset($def['translations'][$iso])) {
                        continue;
                    }

                    $translatedIsos[$iso] = true;
                    $t = $def['translations'][$iso];
                    // Jamais null : la colonne label est NOT NULL DEFAULT ''.
                    $topic->label[$idLang] = isset($t['label']) ? $t['label'] : '';
                    $topic->keywords[$idLang] = $t['keywords'];
                    $topic->answer[$idLang] = $t['answer'];
                    $topic->quick_replies[$idLang] = !empty($t['quick_replies']) ? json_encode($t['quick_replies']) : null;
                }

                $ok = $idTopic ? $topic->update() : $topic->add();

                if ($ok) {
                    $report[$idTopic ? 'updated' : 'created']++;
                } else {
                    $report['failed']++;
                }

            } catch (\Throwable $e) {
                $report['failed']++;
                PhenyxLogger::addLog(
                    sprintf('PhenyxAssistantTopic::seedTopics a échoué sur "%s" (%s)', $def['code'], $e->getMessage()),
                    2,
                    null,
                    static::class
                );
            }

        }

        $report['untranslated'] = array_values(array_diff(array_keys($idLangByIso), array_keys($translatedIsos)));

        return $report;
    }

    /**
     * Écrit un lot de liages « quelque chose du BO → topic » dans
     * phenyx_assistant_topic_screen.
     *
     * Deux usages, distingués par `source_type` :
     *  - un TYPE D'ÉCRAN ('list', 'add', 'edit', 'view', ou '' pour tous) : le
     *    topic d'accueil de cet écran, servi par l'ÉTAGE 0 sans aucun scoring ;
     *  - la valeur 'menu' : le topic qui documente la famille de menu de niveau 1
     *    dont ce `class_name` est la racine, utilisé par le tour dérivé
     *    (getMenuFamilyTopics()).
     *
     * Format attendu de chaque entrée :
     *   ['topic' => 'ph_ecommerce.tour.product',
     *    'controller' => 'AdminParentProduct',
     *    'source_type' => 'menu',   // optionnel, '' par défaut
     *    'priority' => 0]           // optionnel
     *
     * Le topic est désigné par son `code` et non par un id : un plugin n'a pas à
     * connaître les identifiants d'une base qu'il ne maîtrise pas.
     *
     * Idempotent : ON DUPLICATE KEY UPDATE sur la clé d'unicité
     * (controller_name, source_type), donc réexécutable, et réaffecte proprement
     * un écran à un autre topic si la déclaration a changé.
     *
     * @param array       $bindings
     * @param string|null $sourcePlugin Uniquement pour la journalisation
     * @return array ['written' => int, 'skipped' => int]
     */
    public static function seedScreenBindings(array $bindings, $sourcePlugin = null) {

        $report = ['written' => 0, 'skipped' => 0];

        if (!$bindings) {
            return $report;
        }

        // Résolution des codes en ids en UNE requête plutôt qu'une par liage.
        $codes = [];

        foreach ($bindings as $binding) {

            if (!empty($binding['topic'])) {
                $codes[] = $binding['topic'];
            }

        }

        $codes = array_unique($codes);

        if (!$codes) {
            return $report;
        }

        try {
            $db = self::getCrmDb();
            $rows = $db->executeS(
                (new DbQuery())
                    ->select('id_phenyx_assistant_topic, code')
                    ->from('phenyx_assistant_topic')
                    ->where('code IN (\'' . implode('\', \'', array_map('pSQL', $codes)) . '\')')
            );
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::seedScreenBindings : base CRM injoignable (%s)', $e->getMessage()),
                2,
                null,
                static::class
            );

            return $report;
        }

        $idByCode = [];

        foreach ((array) $rows as $row) {
            $idByCode[(string) $row['code']] = (int) $row['id_phenyx_assistant_topic'];
        }

        foreach ($bindings as $binding) {

            if (empty($binding['topic']) || empty($binding['controller']) || !isset($idByCode[$binding['topic']])) {
                // Topic inconnu : le seeder de topics n'a pas encore tourné, ou
                // le code est erroné. On ignore plutôt que de créer un liage
                // orphelin.
                $report['skipped']++;
                continue;
            }

            $idTopic = $idByCode[$binding['topic']];
            $sourceType = isset($binding['source_type']) ? (string) $binding['source_type'] : '';
            $priority = isset($binding['priority']) ? (int) $binding['priority'] : 0;

            /*
             * Dimension ONGLET (2026-08-03) : une entree peut porter
             * 'tab' => 'generalParams' pour ne viser qu'un onglet de l'ecran
             * (cf. dataTab cote JS). '' = tout l'ecran, comme avant.
             *
             * ⚠️ Sur une base non migree, un liage d'onglet est IGNORE et
             * compte en 'skipped' — plutot que d'etre silencieusement ecrase
             * en liage d'ecran, ce qui ferait repondre le topic d'un onglet a
             * l'ecran entier. Le liage d'ecran classique, lui, passe partout.
             */
            $tabKey = isset($binding['tab']) ? (string) $binding['tab'] : '';
            $colonneTab = self::hasTabKeyColumn();

            if ($tabKey !== '' && !$colonneTab) {
                $report['skipped']++;
                continue;
            }

            try {
                $ok = $db->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'phenyx_assistant_topic_screen`
                        (`id_phenyx_assistant_topic`, `controller_name`, `source_type`, ' . ($colonneTab ? '`tab_key`, ' : '') . '`priority`)
                     VALUES (' . (int) $idTopic . ', \'' . pSQL($binding['controller']) . '\', \'' . pSQL($sourceType) . '\', ' . ($colonneTab ? '\'' . pSQL($tabKey) . '\', ' : '') . $priority . ')
                     ON DUPLICATE KEY UPDATE
                        `id_phenyx_assistant_topic` = ' . (int) $idTopic . ',
                        `priority` = ' . $priority
                );

                $report[$ok ? 'written' : 'skipped']++;
            } catch (\Throwable $e) {
                $report['skipped']++;
            }

        }

        if ($sourcePlugin) {
            PhenyxLogger::addLog(
                sprintf('%s: liages assistant écrits (%d posés, %d ignorés)', $sourcePlugin, $report['written'], $report['skipped']),
                1,
                null,
                $sourcePlugin
            );
        }

        return $report;
    }

    /**
     * Désactive les topics d'un plugin — à appeler depuis son uninstall().
     *
     * DÉSACTIVATION et non suppression, volontairement : les alias appris
     * (eph_phenyx_assistant_alias) pointent vers ces topics. Les supprimer
     * laisserait des alias orphelins, et surtout perdrait définitivement
     * l'apprentissage accumulé si le plugin est réinstallé plus tard. Côté
     * lecture, un topic inactif est déjà ignoré partout (`t.active = 1` dans
     * getActiveForLang(), getBoundToScreen() et
     * PhenyxAssistantAlias::resolveTopicRow()).
     *
     * @param string $sourcePlugin
     * @return bool
     */
    public static function deactivateForPlugin($sourcePlugin) {

        if (empty($sourcePlugin)) {
            return false;
        }

        try {
            return (bool) self::getCrmDb()->update(
                'phenyx_assistant_topic',
                ['active' => 0, 'date_upd' => date('Y-m-d H:i:s')],
                'source_plugin = \'' . pSQL($sourcePlugin) . '\''
            );
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantTopic::deactivateForPlugin(%s) a échoué (%s)', $sourcePlugin, $e->getMessage()),
                2,
                null,
                static::class
            );

            return false;
        }

    }

    /**
     * Petite fabrique statique pour ne pas dupliquer la lecture des 4
     * constantes _EPH_CRM_DB_* dans chaque méthode statique (getActiveForLang(),
     * et celles ajoutées côté AdminTranslationsController pour la grille de
     * traduction du wiki).
     *
     * @return Db
     */
    public static function getCrmDb() {

        return Db::getCrmInstance(
            defined('_EPH_CRM_DB_USER_')   ? _EPH_CRM_DB_USER_   : '',
            defined('_EPH_CRM_DB_PASSWD_') ? _EPH_CRM_DB_PASSWD_ : '',
            defined('_EPH_CRM_DB_NAME_')   ? _EPH_CRM_DB_NAME_   : '',
            defined('_EPH_CRM_DB_SERVER_') ? _EPH_CRM_DB_SERVER_ : ''
        );
    }

}
