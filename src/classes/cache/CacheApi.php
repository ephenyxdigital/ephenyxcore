<?php

namespace EphenyxDigital\EphenyxCore;


abstract class CacheApi {

	const APIS_DEFAULT = FileBased::class;

	protected static $instance;
	/**
	 * @var string The maximum SMF version that this will work with.
	 */
	protected $version_compatible = _EPH_VERSION_;

	/**
	 * @var string The minimum SMF version that this will work with.
	 */
	protected $min_eph_version = _EPH_VERSION_;

	abstract protected function _set($key, $value, $ttl = 0);

	abstract protected function _get($key);

	abstract protected function _exists($key);

	abstract protected function _writeKeys();

	abstract protected function _delete($key);

	abstract public function flush();

	protected $keys = [];

	/**
	 * Plafond du cache local, et taille de la tranche evincee lorsqu'il est
	 * atteint. Voir store() pour le detail du choix.
	 */
	const LOCAL_MAX_ENTREES = 10000;
	const LOCAL_EVICTION = 2500;

	protected static $local = [];

	/**
	 * @var string The prefix for all keys.
	 */
	protected $prefix = '';

	/**
	 * @var int The default TTL.
	 */
	protected $ttl = 1864000;

	public $boardurl;

	public $cachedir;

	public $boarddir;

	public $context;

	/**
	 * Does basic setup of a cache method when we create the object but before we call connect.
	 *
	 * @access public
	 */
	public function __construct() {

		$this->boardurl = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
		$this->cachedir = _EPH_CACHE_DIR_ . 'cacheapi/';
		$this->boarddir = _EPH_ROOT_DIR_;
		$this->setPrefix();
	}

	public static function getInstance() {

		if (!static::$instance) {
			$sql = new DbQuery();
			$sql->select('`value`');
			$sql->from('configuration');
			$sql->where('`name` = \'EPH_PAGE_CACHE_TYPE\'');
			$cachingSystem = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue($sql, false);

			if ($cachingSystem) {
				static::$instance = new $cachingSystem();
			} else {
				static::$instance = new FileBased();
			}

		}

		return static::$instance;
	}

	public static function retrieveAll() {
		return CacheApi::$local;
	}

	public static function isEnabled() {

		return (bool) Context::getContext()->phenyxConfig->get('EPH_CACHE_ENABLED');
	}

	public function get($key) {

		if (!isset($this->keys[$key])) {
			return false;
		}

		return $this->_get($key);
	}

	/**
	 * Checks whether we can use the cache method performed by this API.
	 *
	 * @access public
	 * @param bool $test Test if this is supported or enabled.
	 * @return bool Whether or not the cache is supported
	 */
	public function isSupported($test = false) {

		$cache_enable = Context::getContext()->cache_enable;

		if ($test) {
			return true;
		}

		return !empty($cache_enable);
	}

	/**
	 * Sets the cache prefix.
	 *
	 * The prefix has two jobs: keep instances that share a cache backend from
	 * seeing each other's entries, and rotate wholesale whenever the instance
	 * configuration changes so stale entries can never be served.
	 *
	 * It used to be md5($boardurl . filemtime('settings.inc.php')). That worked
	 * back when settings.inc.php held the database credentials inline: touching
	 * the credentials touched the file, and the mtime rotated the prefix. It no
	 * longer holds anything variable — it just reads $_ENV — so:
	 *
	 *   - changing the database in .env left the prefix untouched and the old
	 *     database's cache was served;
	 *   - separation rested on $_SERVER['SERVER_NAME'] alone, which is EMPTY in
	 *     CLI (cron). Every site's cron then fell back to md5('' . $mtime), and
	 *     since the same settings.inc.php is deployed everywhere, two sites
	 *     deployed in the same pass produced an IDENTICAL prefix. Harmless with
	 *     FileBased (one cache dir per site) but not with CacheApcu, where APCu
	 *     is a single shared memory segment per PHP-FPM pool and the prefix is
	 *     the only thing keeping sites apart.
	 *
	 * The prefix is now derived from what actually identifies the instance
	 * rather than from a file timestamp. _EPH_ROOT_DIR_ is what separates sites
	 * when boardurl is empty; the database coordinates and the version are what
	 * make the prefix rotate when it must.
	 *
	 * Credentials are deliberately excluded: the prefix ends up in cache file
	 * names on disk.
	 *
	 * @access public
	 * @param string $prefix The prefix to use.
	 *     If empty, the prefix will be generated automatically.
	 * @return bool If this was successful or not.
	 */
	public function setPrefix($prefix = '') {

		if (!is_string($prefix)) {
			$prefix = '';
		}

		// Use the supplied prefix, if there is one.

		if (!empty($prefix)) {
			$this->prefix = $prefix;

			return true;
		}

		$identity = [
			$this->boardurl,
			defined('_EPH_ROOT_DIR_') ? _EPH_ROOT_DIR_ : '',
			defined('_DB_SERVER_') ? _DB_SERVER_ : '',
			defined('_DB_NAME_') ? _DB_NAME_ : '',
			defined('_DB_PREFIX_') ? _DB_PREFIX_ : '',
			defined('_EPH_VERSION_') ? _EPH_VERSION_ : '',
		];

		$this->prefix = md5(implode('|', $identity)) . '-';

		return true;
	}

	/**
	 * Gets the prefix as defined from set or the default.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getPrefix() {

		return $this->prefix;
	}

	/**
	 * Sets a default Time To Live, if this isn't specified we let the class define it.
	 *
	 * @access public
	 * @param int $ttl The default TTL
	 * @return bool If this was successful or not.
	 */
	public function setDefaultTTL($ttl = 120) {

		$this->ttl = $ttl;

		return true;
	}

	/**
	 * Gets the TTL as defined from set or the default.
	 *
	 * @access public
	 * @return int the value of $ttl.
	 */
	public function getDefaultTTL() {

		return $this->ttl;
	}

	/**
	 * Invalidate all cached data.
	 *
	 * @return bool Whether or not we could invalidate the cache.
	 */
	public function invalidateCache() {

		if (is_writable($this->cachedir . '/' . 'index.php')) {
			@touch($this->cachedir . '/' . 'index.php');
		}

		return true;
	}

	/**
	 * Closes connections to the cache method.
	 *
	 * @access public
	 * @return bool Whether the connections were closed.
	 */
	public function quit() {

		return true;
	}

	/**
	 * Specify custom settings that the cache API supports.
	 *
	 * @access public
	 * @param array $config_vars Additional config_vars, see ManageSettings.php for usage.
	 */
	public function cacheSettings(array &$config_vars) {}

	/**
	 * Gets the latest version of SMF this is compatible with.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getCompatibleVersion() {

		return $this->version_compatible;
	}

	/**
	 * Gets the min version that we support.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getMinimumVersion() {

		return $this->min_eph_version;
	}

	/**
	 * Gets the Version of the Caching API.
	 *
	 * @access public
	 * @return string the value of $key.
	 */
	public function getVersion() {

		return $this->min_eph_version;
	}

	public static function isStored($key) {

		return isset(CacheApi::$local[$key]);
	}

	public static function store($key, $value) {

		/*
		 * ─── LE CACHE SE VIDAIT ENTIEREMENT (corrige le 2026-08-21) ───
		 *
		 * Le code d'origine purgeait la TOTALITE du cache local des qu'il
		 * depassait 1000 entrees :
		 *
		 *     if (count(CacheApi::$local) > 1000) {
		 *         CacheApi::$local = [];
		 *     }
		 *
		 * Deux defauts s'y cumulaient.
		 *
		 * Le seuil d'abord. Une page categorie de ce site emet 1125 requetes :
		 * le plafond etait donc franchi EN COURS DE RENDU, et tout ce qui
		 * avait deja ete mis en cache repartait en base. Le mecanisme
		 * s'effondrait precisement sur les pages qui en avaient le plus besoin,
		 * et il annulait en partie les correctifs qui s'appuient sur lui.
		 *
		 * La politique d'eviction ensuite. Tout jeter est le pire choix
		 * possible : les entrees les plus sollicitees — configuration, groupes
		 * de taxes, modeles d'objets — disparaissaient avec le reste.
		 *
		 * On releve donc le plafond a dix fois la page la plus lourde observee,
		 * et on n'evince plus qu'une tranche : les entrees les plus anciennes,
		 * PHP conservant l'ordre d'insertion des tableaux. Trois quarts du
		 * cache survivent a une eviction, au lieu de rien.
		 *
		 * Le test isset() evite d'evincer pour rien lorsqu'on ne fait que
		 * reecrire une clef deja presente : le tableau ne grandit pas.
		 */
		if (!isset(CacheApi::$local[$key]) && count(CacheApi::$local) >= static::LOCAL_MAX_ENTREES) {
			CacheApi::$local = array_slice(CacheApi::$local, static::LOCAL_EVICTION, null, true);
		}

		CacheApi::$local[$key] = $value;
	}

	public static function retrieve($key) {

		return isset(CacheApi::$local[$key]) ? CacheApi::$local[$key] : null;
	}

	public static function clean($key) {

		if (strpos($key, '*') !== false) {
			$regexp = str_replace('\\*', '.*', preg_quote($key, '#'));

			foreach (array_keys(CacheApi::$local) as $key) {

				if (preg_match('#^' . $regexp . '$#', $key)) {
					unset(CacheApi::$local[$key]);
				}

			}

		} else {
			unset(CacheApi::$local[$key]);
		}

	}

	/**
	 * Run housekeeping of this cache
	 * exp. clean up old data or do optimization
	 *
	 * @access public
	 * @return void
	 */
	public function housekeeping() {}

	/**
	 * Gets the class identifier of the current caching API implementation.
	 *
	 * @access public
	 * @return string the unique identifier for the current class implementation.
	 */
	public function getImplementationClassKeyName() {

		$class_name = get_class($this);

		if ($position = strrpos($class_name, '\\')) {
			return substr($class_name, $position + 1);
		} else {
			return get_class($this);
		}

	}

}

?>