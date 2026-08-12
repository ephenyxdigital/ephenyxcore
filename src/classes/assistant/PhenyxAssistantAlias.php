<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Class PhenyxAssistantAlias
 *
 * ÉTAGE 1 du pipeline de résolution de l'assistant BO : mémorise qu'une
 * formulation donnée mène à un topic donné, pour ne plus jamais avoir à la
 * re-deviner.
 *
 * Pourquoi cette table existe
 * ---------------------------
 * La tolérance aux fautes de frappe (PhenyxAssistantLayer::isCloseEnough(),
 * distance de Levenshtein) ne couvre qu'UN des trois écarts possibles entre la
 * question d'un employé et les mots-clés d'un topic :
 *
 *  - faute de frappe      "modifer" → "modifier"        → Levenshtein sait faire
 *  - écart de vocabulaire "fiche client" vs "utilisateur" → AUCUNE distance
 *                          d'édition ne rapproche ces mots
 *  - écart de cadrage     "ça ne marche pas quand je..."  → intention différente
 *
 * Les deux derniers sont ceux qui explosent avec le nombre de topics (un employé
 * dit "bon de livraison", le topic dit "expédition"). Seule une table d'alias les
 * couvre — et elle a l'avantage de s'alimenter par l'usage plutôt que par la
 * maintenance d'un lexique de synonymes par langue.
 *
 * Le mécanisme est directement inspiré de `search_synonyms` du plugin
 * ph_ecommerce (héritage PrestaShop) : mémoriser le résultat d'une résolution
 * coûteuse ou ambiguë pour que la même question ne la refasse jamais.
 *
 * Base de données
 * ---------------
 * ⚠️ Comme PhenyxAssistantTopic (et pour la même raison : la table porte une clé
 * vers id_phenyx_assistant_topic, une jointure inter-bases serait impossible),
 * cette table vit dans la base CRM centralisée "phenyx-traduction" et NON dans
 * la base boutique locale. D'où le constructeur / add() / update() surchargés qui
 * passent par Db::getCrmInstance() au lieu de Db::getInstance() — un
 * `extends PhenyxObjectModel` naïf écrirait dans la base boutique, où la table
 * n'existe pas.
 *
 * Conséquence assumée (décision Jeff du 2026-07-25) : les alias sont MUTUALISÉS
 * entre tous les sites partageant cette base CRM. Ce qu'un employé apprend au
 * chatbot sur un site profite aux autres. Si une pollution de vocabulaire
 * inter-clients apparaît, ajouter une colonne `shop_scope` (défaut '' =
 * comportement actuel) et l'inclure dans la clé d'unicité.
 *
 * @since 1.0.0 (assistant BO)
 */
#[\AllowDynamicProperties]
class PhenyxAssistantAlias extends PhenyxObjectModel {

    /** Posé par install_assistant_topics.php / un seeder — vérité de référence. */
    const ORIGIN_SEED = 'seed';

    /** Réassignation manuelle depuis l'écran BO — la plus fiable après un seed. */
    const ORIGIN_ADMIN = 'admin';

    /** Clic d'un employé sur une puce de désambiguïsation (étage 4). */
    const ORIGIN_EMPLOYEE = 'employee';

    /** Mémorisation automatique d'une résolution par scoring franc. */
    const ORIGIN_AUTO = 'auto';

    public $id_phenyx_assistant_topic;

    public $id_lang;

    /** @var string Cf. PhenyxAssistantLayer::questionSignature() */
    public $question_norm;

    /** @var string|null Texte réellement tapé, conservé pour l'écran d'audit */
    public $question_raw;

    /** @var string Cf. les constantes ORIGIN_* */
    public $origin = self::ORIGIN_AUTO;

    public $hits = 0;

    public $active = true;

    public $date_add;

    public $date_upd;

    /** @var string Cf. PhenyxAssistantTopic::$dbUser — credentials CRM issus de .env */
    protected $dbUser;

    /** @var string */
    protected $dbPasswd;

    /** @var string */
    protected $dbName;

    /** @var string */
    protected $dbServer;

    public static $definition = [
        'table'     => 'phenyx_assistant_alias',
        'primary'   => 'id_phenyx_assistant_alias',
        // PAS multilang : id_lang est une colonne ordinaire de la table
        // principale (une signature est propre à une langue), il n'y a pas de
        // table _alias_lang.
        'multilang' => false,
        'have_meta' => false,
        'fields'    => [
            'id_phenyx_assistant_topic' => ['type' => self::TYPE_INT, 'required' => true],
            'id_lang'                   => ['type' => self::TYPE_INT, 'required' => true],
            'question_norm'             => ['type' => self::TYPE_STRING, 'size' => 255, 'required' => true],
            'question_raw'              => ['type' => self::TYPE_STRING, 'size' => 500],
            'origin'                    => ['type' => self::TYPE_STRING, 'size' => 16],
            'hits'                      => ['type' => self::TYPE_INT],
            'active'                    => ['type' => self::TYPE_BOOL],
            'date_add'                  => ['type' => self::TYPE_DATE],
            'date_upd'                  => ['type' => self::TYPE_DATE],
        ],
    ];

    /**
     * Cf. PhenyxAssistantTopic::__construct() — même principe, en plus simple
     * (pas de multilang, pas de champ JSON) : on reproduit le sous-ensemble de
     * PhenyxObjectModel::__construct() réellement nécessaire, puis on délègue le
     * chargement à Adapter_EntityMapper::load() en lui passant explicitement les
     * credentials CRM.
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

        $this->dbUser   = defined('_EPH_CRM_DB_USER_') ? _EPH_CRM_DB_USER_ : '';
        $this->dbPasswd = defined('_EPH_CRM_DB_PASSWD_') ? _EPH_CRM_DB_PASSWD_ : '';
        $this->dbName   = defined('_EPH_CRM_DB_NAME_') ? _EPH_CRM_DB_NAME_ : '';
        $this->dbServer = defined('_EPH_CRM_DB_SERVER_') ? _EPH_CRM_DB_SERVER_ : '';

        if ($id) {
            $entityMapper = Adapter_ServiceLocator::get('Adapter_EntityMapper');
            $entityMapper->load(
                $id, $idLang, $this, $this->def, false,
                $this->dbUser, $this->dbPasswd, $this->dbName, $this->dbServer
            );
        }

    }

    /**
     * Cf. PhenyxAssistantTopic::add() — écrit sur Db::getCrmInstance().
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

        if (!$result = $db->insert($this->def['table'], $this->getFields(), $nullValues)) {
            return false;
        }

        $this->id = $db->Insert_ID();

        return (bool) $result;
    }

    /**
     * Cf. PhenyxAssistantTopic::update() — écrit sur Db::getCrmInstance().
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

        return (bool) $db->update(
            $this->def['table'],
            $this->getFields(),
            '`' . pSQL($this->def['primary']) . '` = ' . (int) $this->id,
            0,
            $nullValues
        );
    }

    /**
     * Résout une signature de question en ligne de topic complète, prête à être
     * transformée en réponse — même forme que
     * PhenyxAssistantTopic::getActiveForLang().
     *
     * Une seule requête plutôt qu'un resolve() suivi d'un chargement de topic :
     * les deux tables vivent dans la même base CRM, autant en profiter. La
     * jointure impose `t.active = 1` — un alias appris qui pointe vers un topic
     * désactivé (plugin désinstallé, cf. le contrat des couches) doit être
     * ignoré silencieusement et laisser les étages suivants travailler, pas
     * renvoyer une réponse morte.
     *
     * @param string      $signature   Cf. PhenyxAssistantLayer::questionSignature()
     * @param int         $idLang
     * @param string|null $entityClass Même filtre que getActiveForLang()
     * @return array|null
     */
    public static function resolveTopicRow($signature, $idLang, $entityClass = null) {

        $signature = trim((string) $signature);

        if ($signature === '') {
            return null;
        }

        $query = (new DbQuery())
            ->select('t.id_phenyx_assistant_topic, t.code, t.suggested_action, t.confidence, tl.label, tl.keywords, tl.answer, tl.quick_replies, a.id_phenyx_assistant_alias, a.origin')
            ->from('phenyx_assistant_alias', 'a')
            ->innerJoin('phenyx_assistant_topic', 't', 't.id_phenyx_assistant_topic = a.id_phenyx_assistant_topic AND t.active = 1')
            ->innerJoin('phenyx_assistant_topic_lang', 'tl', 'tl.id_phenyx_assistant_topic = t.id_phenyx_assistant_topic AND tl.id_lang = ' . (int) $idLang)
            ->where('a.active = 1')
            ->where('a.id_lang = ' . (int) $idLang)
            ->where('a.question_norm = \'' . pSQL($signature) . '\'');

        if ($entityClass !== null) {
            $query->where('t.entity_class = \'' . pSQL($entityClass) . '\'');
        }

        try {
            $row = PhenyxAssistantTopic::getCrmDb()->getRow($query);
        } catch (\Throwable $e) {
            // ⚠️ Ne couvre PAS les erreurs SQL : DbPDO::_query() intercepte la
            // PDOException, la journalise et renvoie false sans la relancer (cf.
            // la note détaillée dans PhenyxAssistantTopic::hasAnyScreenBinding()).
            // Table absente ou requête malformée ressortent donc par le
            // `if (!$row) return null;` ci-dessous — ce catch ne voit qu'un échec
            // de connexion à la base CRM. Dans tous les cas l'étage 1 est
            // simplement inopérant et les suivants prennent le relais.
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantAlias::resolveTopicRow : base CRM injoignable (%s).', $e->getMessage()),
                2,
                null,
                static::class
            );

            return null;
        }

        if (!$row) {
            return null;
        }

        self::bumpHits((int) $row['id_phenyx_assistant_alias']);

        return $row;
    }

    /**
     * Incrémente le compteur d'usage. UPDATE direct plutôt qu'un chargement
     * d'objet suivi d'un update() : appelé sur le chemin chaud d'une réponse, et
     * `hits` n'est qu'un indicateur de tri pour l'écran d'audit — aucune logique
     * ne dépend de son exactitude.
     *
     * @param int $idAlias
     * @return void
     */
    protected static function bumpHits($idAlias) {

        if (!$idAlias) {
            return;
        }

        try {
            PhenyxAssistantTopic::getCrmDb()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'phenyx_assistant_alias`
                 SET `hits` = `hits` + 1
                 WHERE `id_phenyx_assistant_alias` = ' . (int) $idAlias
            );
        } catch (\Throwable $e) {
            // Un compteur non incrémenté n'a aucune conséquence fonctionnelle.
        }

    }

    /**
     * Enregistre (ou renforce) un alias.
     *
     * Hiérarchie des origines : seed > admin > employee > auto. Un alias ne peut
     * être écrasé que par une origine au moins aussi fiable — sans quoi une
     * mémorisation automatique pourrait défaire une réassignation faite à la main
     * par un administrateur, ce qui rendrait l'écran BO inutile.
     *
     * @param string      $signature   Cf. PhenyxAssistantLayer::questionSignature()
     * @param string|null $rawQuestion Texte réellement tapé (audit)
     * @param int         $idTopic
     * @param int         $idLang
     * @param string      $origin      Cf. les constantes ORIGIN_*
     * @return bool
     */
    public static function learn($signature, $rawQuestion, $idTopic, $idLang, $origin = self::ORIGIN_AUTO) {

        $signature = trim((string) $signature);

        if ($signature === '' || !(int) $idTopic || !(int) $idLang) {
            return false;
        }

        try {
            $existing = PhenyxAssistantTopic::getCrmDb()->getRow(
                (new DbQuery())
                    ->select('id_phenyx_assistant_alias, id_phenyx_assistant_topic, origin')
                    ->from('phenyx_assistant_alias')
                    ->where('id_lang = ' . (int) $idLang)
                    ->where('question_norm = \'' . pSQL($signature) . '\'')
            );

            if ($existing) {

                // Même cible et origine au moins aussi fiable déjà en place :
                // rien à faire, on évite une écriture inutile sur le chemin
                // chaud.
                if ((int) $existing['id_phenyx_assistant_topic'] === (int) $idTopic
                    && self::originRank($existing['origin']) >= self::originRank($origin)) {
                    return true;
                }

                if (self::originRank($origin) < self::originRank($existing['origin'])) {
                    return false;
                }

                $alias = new static((int) $existing['id_phenyx_assistant_alias']);
                $alias->id_phenyx_assistant_topic = (int) $idTopic;
                $alias->id_lang = (int) $idLang;
                $alias->question_norm = $signature;
                $alias->question_raw = $rawQuestion !== null ? Tools::substr((string) $rawQuestion, 0, 500) : null;
                $alias->origin = $origin;
                $alias->active = true;

                return (bool) $alias->update();
            }

            $alias = new static();
            $alias->id_phenyx_assistant_topic = (int) $idTopic;
            $alias->id_lang = (int) $idLang;
            $alias->question_norm = $signature;
            $alias->question_raw = $rawQuestion !== null ? Tools::substr((string) $rawQuestion, 0, 500) : null;
            $alias->origin = $origin;
            $alias->hits = 0;
            $alias->active = true;

            return (bool) $alias->add();
        } catch (\Throwable $e) {
            PhenyxLogger::addLog(
                sprintf('PhenyxAssistantAlias::learn a échoué (%s)', $e->getMessage()),
                2,
                null,
                static::class
            );

            return false;
        }

    }

    /**
     * Fiabilité relative d'une origine, cf. learn().
     *
     * @param string|null $origin
     * @return int
     */
    public static function originRank($origin) {

        $ranks = [
            self::ORIGIN_SEED     => 40,
            self::ORIGIN_ADMIN    => 30,
            self::ORIGIN_EMPLOYEE => 20,
            self::ORIGIN_AUTO     => 10,
        ];

        return isset($ranks[$origin]) ? $ranks[$origin] : 0;
    }

}
