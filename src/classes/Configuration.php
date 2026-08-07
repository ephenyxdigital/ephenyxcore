<?php


namespace EphenyxDigital\QuantumCore;

use Exception;
use Language;
/**
 * Class Configuration
 *
 * @since 1.9.1.0
 */
class Configuration extends PhenyxObjectModel {

    // Default configuration consts
    // @since 1.0.1
    protected static $instance;

    const ONE_PHONE_AT_LEAST = 'EPH_ONE_PHONE_AT_LEAST';
    const GROUP_FEATURE_ACTIVE = 'EPH_GROUP_FEATURE_ACTIVE';
    const COUNTRY_DEFAULT = 'EPH_COUNTRY_DEFAULT';
    const REWRITING_SETTINGS = 'EPH_REWRITING_SETTINGS';
    const NAVIGATION_PIPE = 'EPH_NAVIGATION_PIPE';
    const SHOP_ENABLE = 'EPH_SHOP_ENABLE';
    const SSL_ENABLED = 'EPH_SSL_ENABLED';
    const SSL_ENABLED_EVERYWHERE = 'EPH_SSL_ENABLED_EVERYWHERE';
    const MAIL_TYPE = 'EPH_MAIL_TYPE';
    const PASSWD_TIME_BACK = 'EPH_PASSWD_TIME_BACK';
    const PASSWD_TIME_FRONT = 'EPH_PASSWD_TIME_FRONT';
    const TIMEZONE = 'EPH_TIMEZONE';
    const SHOW_ALL_PLUGINS = 'EPH_SHOW_ALL_PLUGINS';
    const BACKUP_ALL = 'EPH_BACKUP_ALL';
    const TRACKING_DIRECT_TRAFFIC = 'TRACKING_DIRECT_TRAFFIC';
    const META_KEYWORDS = 'EPH_META_KEYWORDS';
    const CIPHER_ALGORITHM = 'EPH_CIPHER_ALGORITHM';
    const CUSTOMER_SERVICE_FILE_UPLOAD = 'EPH_CUSTOMER_SERVICE_FILE_UPLOAD';
    const CUSTOMER_SERVICE_SIGNATURE = 'EPH_CUSTOMER_SERVICE_SIGNATURE';
    const SMARTY_FORCE_COMPILE = 'EPH_SMARTY_FORCE_COMPILE';
    const STORES_DISPLAY_CMS = 'EPH_STORES_DISPLAY_CMS';
    const STORES_DISPLAY_FOOTER = 'EPH_STORES_DISPLAY_FOOTER';
    const STORES_SIMPLIFIED = 'EPH_STORES_SIMPLIFIED';
    const SHOP_LOGO_WIDTH = 'SHOP_LOGO_WIDTH';
    const SHOP_LOGO_HEIGHT = 'SHOP_LOGO_HEIGHT';
    const EDITORIAL_IMAGE_WIDTH = 'EDITORIAL_IMAGE_WIDTH';
    const EDITORIAL_IMAGE_HEIGHT = 'EDITORIAL_IMAGE_HEIGHT';
    const STATSDATA_CUSTOMER_PAGESVIEWS = 'EPH_STATSDATA_CUSTOMER_PAGESVIEWS';
    const STATSDATA_PAGESVIEWS = 'EPH_STATSDATA_PAGESVIEWS';
    const STATSDATA_PLUGINS = 'EPH_STATSDATA_PLUGINS';
    const GEOLOCATION_ENABLED = 'EPH_GEOLOCATION_ENABLED';
    const ALLOWED_COUNTRIES = 'EPH_ALLOWED_COUNTRIES';
    const GEOLOCATION_BEHAVIOR = 'EPH_GEOLOCATION_BEHAVIOR';
    const LOCALE_LANGUAGE = 'EPH_LOCALE_LANGUAGE';
    const LOCALE_COUNTRY = 'EPH_LOCALE_COUNTRY';
    const ATTACHMENT_MAXIMUM_SIZE = 'EPH_ATTACHMENT_MAXIMUM_SIZE';
    const SMARTY_CACHE = 'EPH_SMARTY_CACHE';
    const DIMENSION_UNIT = 'EPH_DIMENSION_UNIT';
    const GEOLOCATION_WHITELIST = 'EPH_GEOLOCATION_WHITELIST';
    const LOGS_BY_EMAIL = 'EPH_LOGS_BY_EMAIL';
    const COOKIE_CHECKIP = 'EPH_COOKIE_CHECKIP';
    const STORES_CENTER_LAT = 'EPH_STORES_CENTER_LAT';
    const STORES_CENTER_LONG = 'EPH_STORES_CENTER_LONG';
    const CANONICAL_REDIRECT = 'EPH_CANONICAL_REDIRECT';
    const IMG_UPDATE_TIME = 'EPH_IMG_UPDATE_TIME';
    const BACKUP_DROP_TABLE = 'EPH_BACKUP_DROP_TABLE';
    const IMAGE_QUALITY = 'EPH_IMAGE_QUALITY';
    const PNG_QUALITY = 'EPH_PNG_QUALITY';
    const JPEG_QUALITY = 'EPH_JPEG_QUALITY';
    const COOKIE_LIFETIME_FO = 'EPH_COOKIE_LIFETIME_FO';
    const COOKIE_LIFETIME_BO = 'EPH_COOKIE_LIFETIME_BO';
    const RESTRICT_DELIVERED_COUNTRIES = 'EPH_RESTRICT_DELIVERED_COUNTRIES';
    const SHOW_NEW_CUSTOMERS = 'EPH_SHOW_NEW_CUSTOMERS';
    const SHOW_NEW_MESSAGES = 'EPH_SHOW_NEW_MESSAGES';
    const SHOP_DEFAULT = 'EPH_SHOP_DEFAULT';
    const UNIDENTIFIED_GROUP = 'EPH_UNIDENTIFIED_GROUP';
    const GUEST_GROUP = 'EPH_GUEST_GROUP';
    const CUSTOMER_GROUP = 'EPH_CUSTOMER_GROUP';
    const SMARTY_CONSOLE = 'EPH_SMARTY_CONSOLE';
    const LIMIT_UPLOAD_IMAGE_VALUE = 'EPH_LIMIT_UPLOAD_IMAGE_VALUE';
    const LIMIT_UPLOAD_FILE_VALUE = 'EPH_LIMIT_UPLOAD_FILE_VALUE';
    const TOKEN_ENABLE = 'EPH_TOKEN_ENABLE';
    const STATS_RENDER = 'EPH_STATS_RENDER';
    const STATS_OLD_CONNECT_AUTO_CLEAN = 'EPH_STATS_OLD_CONNECT_AUTO_CLEAN';
    const STATS_GRID_RENDER = 'EPH_STATS_GRID_RENDER';
    const BASE_DISTANCE_UNIT = 'EPH_BASE_DISTANCE_UNIT';
    const SHOP_DOMAIN = 'EPH_SHOP_DOMAIN';
    const SHOP_DOMAIN_SSL = 'EPH_SHOP_DOMAIN_SSL';
    const LANG_DEFAULT = 'EPH_LANG_DEFAULT';
    const ALLOW_HTML_IFRAME = 'EPH_ALLOW_HTML_IFRAME';
    const SHOP_NAME = 'EPH_SHOP_NAME';
    const SHOP_EMAIL = 'EPH_SHOP_EMAIL';
    const MAIL_METHOD = 'EPH_MAIL_METHOD';
    const SHOP_ACTIVITY = 'EPH_SHOP_ACTIVITY';
    const LOGO = 'EPH_LOGO';
    const FAVICON = 'EPH_FAVICON';
    const STORES_ICON = 'EPH_STORES_ICON';
    const MAIL_SERVER = 'EPH_MAIL_SERVER';
    const MAIL_USER = 'EPH_MAIL_USER';
    const MAIL_PASSWD = 'EPH_MAIL_PASSWD';
    const MAIL_SMTP_ENCRYPTION = 'EPH_MAIL_SMTP_ENCRYPTION';
    const MAIL_SMTP_PORT = 'EPH_MAIL_SMTP_PORT';
    const ALLOW_MOBILE_DEVICE = 'EPH_ALLOW_MOBILE_DEVICE';
    const CUSTOMER_CREATION_EMAIL = 'EPH_CUSTOMER_CREATION_EMAIL';
    const SMARTY_CONSOLE_KEY = 'EPH_SMARTY_CONSOLE_KEY';
    const DASHBOARD_USE_PUSH = 'EPH_DASHBOARD_USE_PUSH';
    const DASHBOARD_SIMULATION = 'EPH_DASHBOARD_SIMULATION';
    const USE_HTMLPURIFIER = 'EPH_USE_HTMLPURIFIER';
    const SMARTY_CACHING_TYPE = 'EPH_SMARTY_CACHING_TYPE';
    const SMARTY_CLEAR_CACHE = 'EPH_SMARTY_CLEAR_CACHE';
    const DETECT_LANG = 'EPH_DETECT_LANG';
    const DETECT_COUNTRY = 'EPH_DETECT_COUNTRY';
    const ROUND_TYPE = 'EPH_ROUND_TYPE';
    const LOG_EMAILS = 'EPH_LOG_EMAILS';
    const CUSTOMER_NWSL = 'EPH_CUSTOMER_NWSL';
    const CUSTOMER_OPTIN = 'EPH_CUSTOMER_OPTIN';
    const LOG_PLUGIN_PERFS_MODULO = 'EPH_LOG_PLUGIN_PERFS_MODULO';
    const PAGE_CACHE_CONTROLLERS = 'EPH_PAGE_CACHE_CONTROLLERS';
    const ROUTE_CMS_RULE = 'EPH_ROUTE_cms_rule';
    const DISABLE_OVERRIDES = 'EPH_DISABLE_OVERRIDES';
    const CUSTOMCODE_METAS = 'EPH_CUSTOMCODE_METAS';
    const CUSTOMCODE_CSS = 'EPH_CUSTOMCODE_CSS';
    const CUSTOMCODE_JS = 'EPH_CUSTOMCODE_JS';
    const STORE_REGISTERED = 'EPH_STORE_REGISTERED';
    const EPHENYX_LICENSE_KEY = '_EPHENYX_LICENSE_KEY_';

    public $cachedConfigurations = [
        'EPH_PAGE_CACHE_ENABLED',
        'EPH_CACHE_ENABLED',
        'EPH_DEDUCTIBLE_VAT_DEFAULT_ACCOUNT',
        'EPH_PROFIT_DEFAULT_ACCOUNT',
        'EPH_PURCHASE_DEFAULT_ACCOUNT',
        'EPH_LANG_DEFAULT',
        'EPH_PAGE_CACHE_HOOKS',
    ];
    // @codingStandardsIgnoreStart
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'configuration',
        'primary'   => 'id_configuration',
        'multilang' => true,
        'fields'    => [
            'name'       => ['type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254],
            'value'      => ['type' => self::TYPE_NOTHING],
            'date_add'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'   => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'generated'  => ['type' => self::TYPE_BOOL, 'lang' => true],
            'value_lang' => ['type' => self::TYPE_NOTHING, 'lang' => true],
        ],
    ];
    /** @var array Configuration cache */
    protected static $_cache = [];

    /**
     * Le tableau complet a-t-il ete charge depuis la base ?
     *
     * ─── POURQUOI UN DRAPEAU, ET NON UN COMPTAGE (2026-08-07) ───
     *
     * configurationIsLoaded() se contentait de compter les entrees de
     * $_cache['configuration']. Or set() — que updateValue() appelle a chaque
     * enregistrement — y ecrit UNE SEULE clef. Des cet instant le compte vaut
     * 1, la methode repondait « charge », et loadConfiguration() n'etait plus
     * jamais appelee.
     *
     * Le tableau ne contenait donc que les quelques clefs ecrites a la main,
     * et TOUTES les autres repartaient en base a chaque lecture. C'est la
     * cause reelle des 233 lectures de EPH_LANG_DEFAULT par page : non pas un
     * cache absent, mais un cache que l'on croyait plein alors qu'il tenait
     * dans une ligne.
     *
     * Le piege ne se voyait pas depuis un script isole : sans updateValue()
     * prealable, le premier get() chargeait bien tout, et la mesure affichait
     * zero requete. Il fallait le contexte du back-office, ou un
     * enregistrement precede les lectures, pour que le defaut se manifeste.
     *
     * @var bool
     */
    protected static $chargementComplet = false;
    /** @var array Vars types */
    protected static $types = [];
    /** @var string Key */
    public $name;
    /** @var string Value */
    public $value;
    public $generated;

    public $value_lang;
    /** @var string Object creation date */
    public $date_add;
    /** @var string Object last modification date */
    public $date_upd;

    public function __construct($id = null, $idLang = null) {

        $this->className = get_class($this);
        $this->context = Context::getContext();
        $this->context->cache_enable = $this->get('EPH_PAGE_CACHE_ENABLED');

        if ($this->context->cache_enable && !is_object($this->context->cache_api)) {
            $this->context->cache_api = CacheApi::getInstance();
        }

        if (!isset(PhenyxObjectModel::$loaded_classes[$this->className])) {
            $this->def = PhenyxObjectModel::getDefinition($this->className);
            PhenyxObjectModel::$loaded_classes[$this->className] = get_object_vars($this);

        } else {

            foreach (PhenyxObjectModel::$loaded_classes[$this->className] as $key => $value) {
                $this->{$key}

                = $value;
            }

        }

        if ($id) {
            $this->id = $id;
            $entityMapper = Adapter_ServiceLocator::get("Adapter_EntityMapper");
            $entityMapper->load($id, $idLang, $this, $this->def, static::$cache_objects);
        }

        $this->_session = PhenyxSession::getInstance();

    }

    public static function getInstance($id = null, $idLang = null) {

        if (!isset(static::$instance)) {
            static::$instance = new Configuration($id, $idLang);
        }

        return static::$instance;
    }

    public function configurationIsLoaded() {

        // cf. $chargementComplet : un comptage ne distingue pas « tout charge »
        // de « une seule clef posee par set() ».
        return static::$chargementComplet
        && isset(static::$_cache['configuration'])
        && is_array(static::$_cache['configuration']);
    }

    public function clearConfigurationCacheForTesting() {

        static::$_cache = [];
        static::$chargementComplet = false;
    }

    public function getGlobalValue($key, $idLang = null) {

        return $this->get($key, $idLang);
    }

    public function get($key, $idLang = null, $use_cache = true) {

        if (defined('_EPH_DO_NOT_LOAD_CONFIGURATION_') && _EPH_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        /*
         * ═══ POURQUOI CETTE METHODE LANCAIT 727 REQUETES PAR PAGE ═══
         *
         * Releve du 2026-08-07, onglet « Double » du profilage : 233 lectures
         * de EPH_LANG_DEFAULT, 228 de EPH_REWRITING_SETTINGS, 129 de
         * EPH_PAGE_CACHE_ENABLED, 63 de EPH_PAGE_CACHE_TYPE... sur 992
         * requetes au total.
         *
         * Le cache existait pourtant, et a deux etages. Les deux etaient hors
         * service :
         *
         *  1. loadConfiguration() charge TOUTE la table en une requete dans
         *     static::$_cache['configuration'][$lang]['global'][$nom]. Cette
         *     methode etait bien appelee... puis le tableau obtenu etait
         *     ignore, et une requete individuelle partait quand meme. Le
         *     tableau n'etait lu NULLE PART pour servir une valeur.
         *
         *  2. Un second cache, en session, echouait systematiquement :
         *
         *     - la clef etait fabriquee AVANT le cast `(int) $idLang`, quatre
         *       lignes plus bas : un appel avec null lisait « cnfig_X_ » et
         *       ecrivait « cnfig_X_0 ». Les deux ne se rencontraient jamais ;
         *     - le test `!empty($result)` traitait une valeur a 0 ou vide
         *       comme un cache absent — soit precisement le cas de
         *       EPH_PAGE_CACHE_ENABLED et EPH_REWRITING_SETTINGS.
         *
         * ⚠️ Et surtout : ce cache de session n'etait invalide PAR PERSONNE.
         * updateValue() ecrit bien en session, mais sous « cnfig_X-lang »
         * (tiret), clef destinee a hasKey(). Le reparer tel quel aurait fige
         * la configuration pour toute la duree de la session : une valeur
         * modifiee ne serait plus jamais relue. C'est pour cela qu'il est
         * SUPPRIME et non corrige.
         *
         * On s'appuie donc uniquement sur le cache statique, qui a les bonnes
         * proprietes : rempli en une requete, rafraichi par set() — donc par
         * updateValue() qui la termine — vide par deleteByName() et
         * deleteFromContext(), et mort a la fin de la requete HTTP.
         */
        $this->validateKey($key);

        // Le cast d'abord : tout ce qui suit s'indexe dessus.
        $idLang = (int) $idLang;

        if ($use_cache) {

            if (!$this->configurationIsLoaded()) {
                $this->loadConfiguration();
            }

            /*
             * array_key_exists et non isset : une valeur legitimement nulle
             * doit compter comme trouvee, sans quoi on repart en base a chaque
             * appel — l'erreur meme que corrige ce correctif.
             */
            if (isset(static::$_cache['configuration'][$idLang]['global'])
                && array_key_exists($key, static::$_cache['configuration'][$idLang]['global'])) {

                return static::$_cache['configuration'][$idLang]['global'][$key];
            }

        }

        /*
         * Filet de securite : clef ecrite en base apres le chargement du
         * tableau, ou appel explicite avec $use_cache a false. Le resultat
         * rejoint le cache pour que le cas ne se represente pas dans la meme
         * requete.
         */
        $sql = new DbQuery();

        if ($idLang > 0) {
            $sql->select('cl.`value_lang`');
        } else {
            $sql->select('c.`value`');
        }

        $sql->from('configuration', 'c');

        if ($idLang > 0) {
            $sql->leftJoin('configuration_lang', 'cl', 'cl.id_configuration = c.id_configuration AND cl.id_lang = ' . $idLang);
        }

        $sql->where('c.`name` = \'' . $key . '\'');
        $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql);

        // deleteByName() et deleteFromContext() remettent le cache a null : on
        // ne veut pas indexer null sur un serveur ou display_errors est actif.
        if (!is_array(static::$_cache['configuration'])) {
            static::$_cache['configuration'] = [];
        }

        if (!isset(static::$_cache['configuration'][$idLang])) {
            static::$_cache['configuration'][$idLang] = ['global' => []];
        }

        static::$_cache['configuration'][$idLang]['global'][$key] = $value;

        return $value;
    }
    
    public function getLangs($key) {
        
        $value = [];
        foreach (Language::getIDs(false) as $idLang) {
            $sql = new DbQuery();
            $sql->select('cl.`value_lang`');
            $sql->from('configuration_lang', 'cl');
            $sql->leftJoin('configuration', 'c', 'c.id_configuration = cl.id_configuration');
            $sql->where('c.`name` = \'' . $key . '\'');
            $sql->where('cl.id_lang = ' . $idLang);
            
            $value[$idLang] = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql);
            
        }



        return $value;
    }

    public function getKey($key, $idLang = null, $use_cache = true) {

        if (defined('_EPH_DO_NOT_LOAD_CONFIGURATION_') && _EPH_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        if ($use_cache) {

            if (!is_object($this->context->_session)) {
                $this->context->_session = PhenyxSession::getInstance();
            }

            $result = $this->context->_session->get('cnfig_' . $key . '-' . $idLang);

            if (!empty($result)) {
                return $result;
            }

        }

        $this->validateKey($key);

        if (!$this->configurationIsLoaded()) {
            $this->loadConfiguration();
        }

        $idLang = (int) $idLang;

        $sql = new DbQuery();

        if ($idLang > 0) {
            $sql->select('cl.`value_lang`');
        } else {
            $sql->select('c.`value`');
        }

        $sql->from('configuration', 'c');

        if ($idLang > 0) {
            $sql->leftJoin('configuration_lang', 'cl', 'cl.id_configuration = c.id_configuration AND cl.id_lang = ' . $idLang);
        }

        $sql->where('c.`name` = \'' . $key . '\'');
        $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql);
        $this->context->_session->set('cnfig_' . $key . '-' . $idLang, $result);
        return $value;
    }

    public function loadConfiguration() {

        return $this->loadConfigurationFromDB();
    }

    public function loadConfigurationFromDB() {

        /*
         * ⚠️ L'INSTANTANE EN SESSION A ETE RETIRE (2026-08-07).
         *
         * Cette methode gardait en session, sous la clef
         * 'loadConfigurationFromDB', une copie de TOUTE la table de
         * configuration — et la relisait en priorite au chargement suivant.
         *
         * Or rien ne l'invalidait jamais : ni updateValue(), ni
         * deleteByName(), ni deleteFromContext(). Une valeur modifiee depuis
         * le back-office ne serait donc jamais reapparue tant que la session
         * de l'employe vivait.
         *
         * Ce piege ne s'est pas declenche jusqu'ici parce que get() ignorait
         * ce tableau et repartait en base a chaque appel — c'est-a-dire que
         * les 727 requetes par page MASQUAIENT le cache casse. En branchant
         * get() sur le cache, il fallait donc retirer l'instantane dans le
         * meme geste, faute de quoi on gagnait la vitesse en perdant la
         * justesse.
         *
         * Une requete par requete HTTP : c'est le bon compromis, et cela reste
         * une requete au lieu de sept cent vingt-sept.
         */
        static::$_cache['configuration'] = [];
        static::$chargementComplet = false;

        $rows = Db::getInstance()->executeS(
            (new DbQuery())
                ->select('c.`name`, cl.`id_lang`, IFNULL(cl.`value_lang`, c.`value`) AS `value`')
                ->from('configuration', 'c')
                ->leftJoin('configuration_lang', 'cl', 'c.`id_configuration` = cl.`id_configuration`')
        );

        if (!is_array($rows)) {
            // Echec de lecture : on ne pretend surtout pas au chargement
            // complet, sans quoi toutes les clefs seraient reputees absentes.
            return;
        }

        static::$chargementComplet = true;

        foreach ($rows as $row) {
            $lang = ($row['id_lang']) ? $row['id_lang'] : 0;
            static::$types[$row['name']] = ($lang) ? 'lang' : 'normal';

            if (!isset(static::$_cache['configuration'][$lang])) {
                static::$_cache['configuration'][$lang] = [
                    'global' => [],
                ];
            }

            static::$_cache['configuration'][$lang]['global'][$row['name']] = $row['value'];

        }

    }

    public function hasKey($key, $idLang = null, $use_cache = true) {

        if ($use_cache && class_exists('Context')) {

            if (!is_object($this->context->_session)) {
                $this->context->_session = PhenyxSession::getInstance();
            }

            $result = $this->context->_session->get('hasKey_' . $key . '-' . $idLang);

            if (!empty($result) && is_array($result)) {
                return $result;
            }

        }

        $sql = new DbQuery();
        $sql->select('c.`id_configuration`');
        $sql->from('configuration', 'c');

        if (!is_null($idLang)) {
            $sql->leftJoin('configuration_lang', 'cl', 'cl.id_configuration = c.id_configuration AND cl.id_lang = ' . $idLang);
        }

        $sql->where('c.`name` = \'' . $key . '\'');

        $result = (bool) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql);

        if (class_exists('Context')) {
            $this->context->_session->set('hasKey_' . $key . '-' . $idLang, $result);

        }

        return $result;
    }

    public function getInt($key) {

        $resultsArray = [];

        foreach (Language::getIDs() as $idLang) {
            $resultsArray[$idLang] = $this->get($key, $idLang);
        }

        return $resultsArray;
    }

    public function getMultiShopValues($key, $idLang = null) {

        return $this->get($key, $idLang, null);
    }

    public function getMultiple($keys, $idLang = null) {

        if (!is_array($keys)) {
            throw new PhenyxException('keys var is not an array');
        }

        $idLang = (int) $idLang;

        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $idLang);
        }

        return $results;
    }

    public function updateGlobalValue($key, $values, $html = false) {

        return $this->updateValue($key, $values, $html, 0, 0);
    }

    public function updateValue($key, $values, $html = false, $script = false) {

        if (!is_object($this->context->_session)) {
            $this->context->_session = PhenyxSession::getInstance();
        }

        $this->validateKey($key);

        if (!is_array($values)) {
            $values = [$values];
        }

        if ($html) {

            foreach ($values as &$value) {
                $value = Tools::purifyHTML($value);
            }

            unset($value);
        }

        if (!$script) {

            foreach ($values as &$value) {
                $value = pSQL($value, $html);
            }

        }

        $result = true;
        $idConfig = $this->getIdByName($key);
        $configuration = new Configuration($idConfig);

        foreach ($values as $lang => $value) {

            if ($this->hasKey($key, $lang)) {

                if (!$lang) {
                    $configuration->value = $value;

                } else {
                    $configuration->value = null;
                    $configuration->value_lang[$lang] = $value;

                }

                try {
                    $configuration->update(true);
                } catch (Exception $e) {

                }

                $this->context->_session->set('cnfig_' . $key . '-' . $lang, $value);

            } else {

                $configuration->name = $key;
                $configuration->value = $lang ? null : $value;
                $configuration->date_add = date('Y-m-d H:i:s');
                $configuration->date_upd = date('Y-m-d H:i:s');

                if ($lang) {
                    $configuration->value_lang[$lang] = $value;
                }

                try {
                    $configuration->add();
                } catch (Exception $e) {

                }

                $this->context->_session->set('cnfig_' . $key . '-' . $lang, $value);

            }

        }

        $this->set($key, $values);

        return $result;
    }

    public function getIdByName($key) {

        $this->validateKey($key);

        $sql = 'SELECT `id_configuration`
                FROM `' . _DB_PREFIX_ . 'configuration`
                WHERE name = \'' . $key . '\'';

        return (int) Db::getInstance()->getValue($sql);
    }

    public function set($key, $values) {

        $this->validateKey($key);

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $lang => $value) {

            static::$_cache['configuration'][$lang]['global'][$key] = $value;

        }

    }

    public function deleteByName($key) {

        static::validateKey($key);

        $result = Db::getInstance()->execute(
            '
        DELETE FROM `' . _DB_PREFIX_ . 'configuration_lang`
        WHERE `id_configuration` IN (
            SELECT `id_configuration`
            FROM `' . _DB_PREFIX_ . 'configuration`
            WHERE `name` = "' . $key . '"
        )'
        );

        $result2 = Db::getInstance()->delete('configuration', '`name` = "' . $key . '"');

        static::$_cache['configuration'] = null;
        static::$chargementComplet = false;

        return ($result && $result2);
    }

    public function deleteFromContext($key) {

        $id = $this->getIdByName($key);
        Db::getInstance()->delete(
            'configuration',
            '`id_configuration` = ' . (int) $id
        );
        Db::getInstance()->delete(
            'configuration_lang',
            '`id_configuration` = ' . (int) $id
        );

        static::$_cache['configuration'] = null;
        static::$chargementComplet = false;
    }
	 public function isLangKey($key) {

        $this->validateKey($key);

        return (isset(static::$types[$key]) && static::$types[$key] == 'lang') ? true : false;
    }

    protected function validateKey($key) {

        if (is_null($key)) {
            return false;
        }

        if (!Validate::isConfigName($key)) {
            $e = new PhenyxException(sprintf(
                Tools::displayError('[%s] is not a valid configuration key'),
                Tools::htmlentitiesUTF8($key)
            ));
            die($e->displayMessage());
        }

    }
}
  