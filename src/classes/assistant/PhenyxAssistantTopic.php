<?php

namespace EphenyxDigital\QuantumCore;

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
            'keywords'      => ['type' => self::TYPE_STRING, 'size' => 500, 'lang' => true, 'required' => true],
            'answer'        => ['type' => self::TYPE_HTML, 'lang' => true, 'required' => true],
            'quick_replies' => ['type' => self::TYPE_STRING, 'size' => 500, 'lang' => true],
        ],
    ];

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
        }

        if ($idLang !== null) {
            $this->id_lang = (Language::getLanguage($idLang) !== false) ? $idLang : $this->context->phenyxConfig->get('EPH_LANG_DEFAULT');
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

        return $result;
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
            ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.keywords, tl.answer, tl.quick_replies')
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
