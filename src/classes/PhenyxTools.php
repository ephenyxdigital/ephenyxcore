<?php

namespace EphenyxDigital\QuantumCore;

use AppendIterator;
use Archive_Tar;
use DateTime;
use DirectoryIterator;
use Guest;
use Language;
use Link;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


use \Curl\Curl;
use \Curl\MultiCurl;

/**
 * Class PhenyxToolsCore
 *
 * @since 1.9.1.0
 */
class PhenyxTools {

	protected static $instance;

	protected $_url;

	protected $_crypto_key;

	public $context;

	public $ephenyx_shop_active;

	public $default_theme;

	public $plugins = [];

	public $license;

	public $license_key;

	/** @var string Dernière erreur SQL rencontrée par une fonction de maintenance (premier échec). */
	public $lastError = '';

	/**
	 * La derniere operation a-t-elle ete SAUTEE plutot qu'executee ?
	 *
	 * cleanBackTabs() ne se relance pas si la derniere maintenance a moins de
	 * 180 jours. Elle rendait alors true, et l'ecran annoncait « reconstruction
	 * reussie » sans que rien n'ait eu lieu. L'appelant peut desormais faire la
	 * difference.
	 */
	public $lastSkipped = false;

	/**
	 * Information a remonter a l'ecran quand l'operation s'est deroulee mais
	 * qu'une de ses etapes a ete volontairement annulee.
	 *
	 * Le cas type : la garde anti-catastrophe de cleanMetas(), qui renonce a
	 * purger quand la liste des pages legitimes semble tronquee. Sans ce canal,
	 * l'annulation n'apparaissait que dans le journal et l'ecran annoncait un
	 * succes complet.
	 */
	public $lastNotice = '';

	/**
	 * Mode simulation : rien n'est ecrit, tout est consigne.
	 *
	 * Les LECTURES s'executent normalement — c'est ce qui rend l'analyse exacte :
	 * les memes SELECT, les memes decomptes, les memes decisions. Seules les
	 * ecritures sont interceptees. Voir runSql() et plan().
	 *
	 * Mis en place le 2026-07-30, apres qu'une reconstruction des metas a
	 * impose une restauration de la base : il manquait simplement un moyen de
	 * savoir ce qu'une operation allait faire AVANT de la subir.
	 */
	public $dryRun = false;

	/**
	 * Autoriser la SUPPRESSION d'onglets pendant cleanBackTabs() ?
	 *
	 * Faux par defaut depuis le 2026-07-30. Le critere de suppression — « pas de
	 * classe de controleur » — s'est revele faux pour toute une famille
	 * d'onglets, et une suppression ne se repare que par restauration. L'etape
	 * se contente donc de SIGNALER les candidats ; la renumerotation, elle, se
	 * fait normalement.
	 *
	 * Passer a true en connaissance de cause, apres avoir lu le rapport de
	 * simulation.
	 */
	public $allowTabDeletion = false;

	/** @var array Ecritures qui auraient eu lieu, en mode simulation. */
	public $plan = [];

	/**
	 * Consigne une action qui ne passe pas par runSql() — suppression d'objet,
	 * reinitialisation de plugin, reecriture de fichier.
	 *
	 * @param string $table Table ou domaine concerne
	 * @param string $op    Nature de l'action
	 * @param string $note  Detail lisible
	 *
	 * @return void
	 */
	public function plan($table, $op, $note = '') {

		$this->plan[] = [
			'table' => $table,
			'op'    => $op,
			'note'  => $note,
		];
	}

	/**
	 * Compte-rendu lisible de ce qu'une simulation aurait fait.
	 *
	 * @return array{total: int, lines: array<int,string>}
	 */
	public function planSummary() {

		$counts = [];

		foreach ($this->plan as $entry) {
			$key = $entry['op'] . ' ' . $entry['table'];
			$counts[$key] = isset($counts[$key]) ? $counts[$key] + 1 : 1;
		}

		ksort($counts);
		$lines = [];

		foreach ($counts as $key => $n) {
			$lines[] = $n . ' × ' . $key;
		}

		// Les notes portent l'information que le decompte ne dit pas : quelles
		// metas exactement, quels plugins reinitialises.
		foreach ($this->plan as $entry) {

			if ($entry['note'] !== '') {
				$lines[] = '— ' . $entry['note'];
			}

		}

		return ['total' => count($this->plan), 'lines' => $lines];
	}

	public function __construct() {

		$this->context = Context::getContext();

		if (!isset($this->context->phenyxConfig)) {
			$this->context->phenyxConfig = Configuration::getInstance();

		}

		if (!isset($this->context->company)) {
			$this->context->company = Company::initialize();
		}

		if (!isset($this->context->theme)) {
			$this->context->theme = new Theme((int) $this->context->company->id_theme);
		}

		if (!isset($this->context->language)) {
			$this->context->language = $this->context->_tools->jsonDecode($this->context->_tools->jsonEncode(Language::buildObject($this->context->phenyxConfig->get('EPH_LANG_DEFAULT'))));
		}

		if (!isset($this->context->_link)) {
			$this->context->_link = Link::getInstance();
		}

		if (!isset($this->context->translations)) {

			$this->context->translations = new Translate($this->context->language->iso_code, $this->context->company);
		}
		
		if (!isset($this->context->_tools)) {
            $this->context->_tools = PhenyxTool::getInstance();
        }

		$this->default_theme = $this->context->theme->directory;

		if (!isset($this->context->language)) {
			$this->context->language = $this->context->_tools->jsonDecode($this->context->_tools->jsonEncode(Language::buildObject($this->context->phenyxConfig->get('EPH_LANG_DEFAULT'))));
		}

		$this->ephenyx_shop_active = $this->context->phenyxConfig->get('_EPHENYX_SHOP_ACTIVE_');
        if ($this->context->company->company_url !== 'ephenyx.io') {
    		$this->license_key = $this->context->phenyxConfig->get('_EPHENYX_LICENSE_KEY_', null, false);
            $this->_url = _EPH_PHENYX_API_;
            $string = $this->license_key . '/' . $this->context->company->company_url;
            $this->_crypto_key = $this->context->_tools->encrypt_decrypt('encrypt', $string, _PHP_ENCRYPTION_KEY_,  $this->license_key);

            $this->license = $this->checkLicense();
            $this->context->license = $this->license;

            $this->plugins = $this->getInstalledPluginsDirOnDisk();
        }

	}

	public function getInstalledPluginsDirOnDisk() {
        
		$cacheId = 'getInstalledPluginsDirOnDisk';

		if ($this->context->cache_enable && is_object($this->context->cache_api)) {
			$value = $this->context->cache_api->getData($cacheId);
			$temp = empty($value) ? null : Tools::jsonDecode($value, true);

			if (!empty($temp)) {
				return $temp;
			}

		}

		$plugins = [];
		$pluginList = [];
		$plugs = scandir(_EPH_PLUGIN_DIR_);

		foreach ($plugs as $name) {

			if (in_array($name, ['.', '..'])) {
				continue;
			}

			if (is_file(_EPH_PLUGIN_DIR_ . $name)) {
				continue;
			} else

			if (is_dir(_EPH_PLUGIN_DIR_ . $name . DIRECTORY_SEPARATOR) && file_exists(_EPH_PLUGIN_DIR_ . $name . '/' . $name . '.php')) {

				if (!Validate::isPluginName($name)) {
					throw new PhenyxException(sprintf('Plugin %s is not a valid plugin name', $name));
				}

				$pluginList[] = $name;
			}
        }

		$plugs = scandir(_EPH_SPECIFIC_PLUGIN_DIR_);

		foreach ($plugs as $name) {

            if (in_array($name, ['.', '..'])) {
				continue;
			}

			if (is_file(_EPH_SPECIFIC_PLUGIN_DIR_ . $name)) {
				continue;
			} else

			if (is_dir(_EPH_SPECIFIC_PLUGIN_DIR_ . $name . DIRECTORY_SEPARATOR) && file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $name . DIRECTORY_SEPARATOR . $name . '.php')) {

				if (!Validate::isPluginName($name)) {
					throw new PhenyxException(sprintf('Plugin %s is not a valid plugin name', $name));
				}

				$pluginList[] = $name;
			}

        }

			foreach ($pluginList as $plugin) {

				if (in_array($plugin, ['.', '..'])) {
					continue;
				}

				if (Plugin::isInstalled($plugin)) {
					$plugins[$plugin] = true;
				}

			}

		

		if ($this->context->cache_enable && is_object($this->context->cache_api)) {
			$temp = $plugins === null ? null : Tools::jsonEncode($plugins);
			$this->context->cache_api->putData($cacheId, $temp, 3600);
		}

		return $plugins;

	}

	public static function getInstance() {

		if (!PhenyxTools::$instance) {
			PhenyxTools::$instance = new PhenyxTools(); 
		}

		return PhenyxTools::$instance;
	}
    
    public static function getBasesLink($domain, $ssl = null, $relativeProtocol = false) {
       
        $link = Link::getInstance();      
    
        return $link->getBasesLink($domain, $ssl, $relativeProtocol);
    }
    

	public function generateCurrentJson($use_cache = true): array {

		// Lecture du cache si disponible	
    	if ($use_cache && file_exists(_EPH_CONFIG_DIR_ . 'json/new_json.json')) {
        	$md5List = file_get_contents(_EPH_CONFIG_DIR_ . 'json/new_json.json');
        	unlink(_EPH_CONFIG_DIR_ . 'json/new_json.json');
        	return Tools::jsonDecode($md5List, true) ?? [];
		}

    	if (!$use_cache && file_exists(_EPH_CONFIG_DIR_ . 'json/new_json.json')) {
        	unlink(_EPH_CONFIG_DIR_ . 'json/new_json.json');
    	}

		// CORRIGÉ : initialisé à [] — retour null impossible même si tous les fichiers sont exclus
    	$md5List = [];
    	$excludes = [];

    	$directories = Theme::getInstalledThemeDirectories();

    	$recursive_directory = [
        	'app/xml',
        	'content/backoffice',
        	'content/css',
        	'content/fonts',
        	'content/js',
        	'content/localization',
        	'content/img/pdfWorker',
        	'content/mails',
        	'content/mp3',
        	'content/pdf',
        	'content/themes/phenyx-theme-default',
        	'includes/classes',
        	'includes/controllers',
        	'vendor/ephenyxdigital',
        	'webephenyx',
    	];

    	$iso_langs = [];
    	$languages = Language::getLanguages(false);

    	foreach ($languages as $language) {
        	$recursive_directory[] = 'content/translations/' . $language['iso_code'];
        	$iso_langs[]           = $language['iso_code'];
    	}

    	// CORRIGÉ : $this->plugins est ['pluginName' => true/false]
    	// L'ancien code faisait foreach($this->plugins as $plugin) ce qui donnait
    	// $plugin = true/false au lieu du nom du plugin.
    	foreach ($this->plugins as $plugin => $installed) {

        	if ($installed && is_dir(_EPH_PLUGIN_DIR_ . $plugin)) {
            	$recursive_directory[] = 'includes/plugins/' . $plugin;
        	}
		}

    	$iterator = new AppendIterator();
    	$iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/content/themes/'));

    	foreach ($recursive_directory as $directory) {
			
        	if (is_dir(_EPH_ROOT_DIR_ . '/' . $directory)) {
            	$iterator->append(new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator(_EPH_ROOT_DIR_ . '/' . $directory . '/')
            	));
			}
    	}

    	$iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/app/'));
    	$iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/'));

    	// Construction des exclusions de thèmes
    	foreach ($directories as $directory) {

        	if ($directory === 'phenyx-theme-default') {
				continue;
        	}

        	foreach (['css', 'fonts', 'font', 'img', 'js', 'plugins', 'pdf', 'mail', 'docs'] as $sub) {
            	$excludes[] = '/' . $directory . '/' . $sub . '/';
        	}
    	}

    	$excludedFiles = ['.', '..', '.htaccess', '.env', 'composer.lock', 'settings.inc.php', 'settings.inc.old.php', '.gitattributes', '.php-ini', '.php-version', 'config.json'];
    	$excludedExtensions = ['txt', 'zip', 'dat', 'log'];
    	$excludedPaths = ['/uploads/', '/cache/', '/views/docs/', 'sitemap.xml'];

    	foreach ($iterator as $file) {

        	if (in_array($file->getFilename(), $excludedFiles, true)) {
            	continue;
        	}

        	if (is_dir($file->getPathname())) {
            	continue;
        	}

        	$filePath = str_replace(_EPH_ROOT_DIR_, '', $file->getPathname());
        	$ext      = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
			
			if (str_contains($filePath, 'uploads/revslider')) {
                continue;
            }

        	if (in_array($ext, $excludedExtensions, true)) {
            	continue;
        	}

        	// Vérification des exclusions de chemin
        	$skip = false;

        	foreach ($excludes as $exclude) {

            	if (str_contains($filePath, $exclude)) {
                	$skip = true;
                	break;
            	}
			}

        	foreach ($excludedPaths as $excludedPath) {

				if (str_contains($filePath, $excludedPath)) {
					$skip = true;
                	break;
            	}
        	}

        	if ($skip) {
            	continue;
        	}

        	// Exclusion des CSS personnalisés
        	if (str_contains($filePath, 'custom_') && $ext === 'css') {
            	continue;
        	}
			
        	// CORRIGÉ : filtre des traductions de plugins
        	// Avant : le continue portait sur foreach($this->plugins), pas sur foreach($iterator)
        	// → les fichiers de traduction non pertinents étaient inclus quand même.
        	// Après : flag $skipFile qui porte sur la bonne boucle.
        	if (str_contains($filePath, '/plugins/') && str_contains($filePath, '/translations/')) {
            	$skipFile = false;

            	foreach ($this->plugins as $plugin => $installed) {

                	if (str_contains($filePath, '/plugins/' . $plugin . '/translations/')) {
                    	$isoTest = str_replace('/includes/plugins/' . $plugin . '/translations/', '', $filePath);
						$test2 = str_replace('/'.$file->getFilename(), '', $isoTest);
                    	$isoTest = str_replace('.php', '', $isoTest);
						if (!in_array($test2, $iso_langs)) {
							if (!in_array($isoTest, $iso_langs)) {
								$skipFile = true;
                    		}
						}

                    	break;
                	}
            	}

            	if ($skipFile) {
                	continue;
            	}
        	}

        	$md5List[$filePath] = md5_file($file->getPathname());
    	}

    	return $md5List;
	}


	public function generateOwnCurrentJson(): bool {

		// Supprimer le cache périmé s'il existe
    	if (file_exists(_EPH_CONFIG_DIR_ . 'json/new_json.json')) {
        	unlink(_EPH_CONFIG_DIR_ . 'json/new_json.json');
    	}

    	// Générer la nouvelle liste (sans lire le cache)
    	$md5List = $this->generateCurrentJson(false);

    	if (!empty($md5List)) {
        	file_put_contents(
            	_EPH_CONFIG_DIR_ . 'json/new_json.json',
            	json_encode($md5List, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        	);
        	chmod(_EPH_CONFIG_DIR_ . 'json/new_json.json', 0777);
        	return true;
    	}

    	return false;
	}


	public static function getConfiguration($tags) {

		return Context::getContext()->phenyxConfig->get($tags);
	}

	public static function execHook($hook, $args = [], $return = false) {

		return Hook::getInstance()->exec($hook, $args, null, $return);
	}

	public static function getSmartyLink(string $method, $args) {

    	$link = new Link();

    	if (method_exists($link, $method)) {

        	if (!is_array($args)) {
            	$args = [$args];
        	}	

        	return $link->{$method}(implode(',', $args)); // ← était $link->{method}
    	}

    	return null;
	}

	public static function addJsDef($jsDef) {

		return Context::getContext()->media->addJsDef($jsDef);

	}

	public static function addJsDefL($params, $content, $smarty = null, &$repeat = false) {

		return Context::getContext()->media->addJsDefL($params, $content, $smarty, $repeat);
	}

	// =========================================================================
	// AUTH API PAR SIGNATURE (Ed25519) — côté client
	// =========================================================================

	/**
	 * Génère la paire de clés Ed25519 au premier appel et enrôle la clé publique
	 * auprès d'ephenyx.io (requête authentifiée par le schéma legacy, accepté par
	 * le dispatcher en mode dual). Idempotent : ne refait rien une fois enrôlé.
	 */
	private function ensureApiEnrollment() {

		$cfg = $this->context->phenyxConfig;

		if (!$cfg->get('_EPH_API_PRIVATE_KEY_', null, false)) {
			$pair = sodium_crypto_sign_keypair();
			$cfg->updateValue('_EPH_API_PRIVATE_KEY_', base64_encode(sodium_crypto_sign_secretkey($pair)));
			$cfg->updateValue('_EPH_API_PUBLIC_KEY_', base64_encode(sodium_crypto_sign_publickey($pair)));
			$cfg->updateValue('_EPH_API_KEY_ID_', 1);
		}

		if (!$cfg->get('_EPH_API_KEY_REGISTERED_', null, false)) {
			if ($this->registerApiPublicKey()) {
				$cfg->updateValue('_EPH_API_KEY_REGISTERED_', 1);
			}
		}
	}

	/**
	 * Construit le payload signé d'une requête API.
	 * Les paramètres métier sont transportés dans `body` (string JSON), dont on
	 * signe le sha256 avec ts + nonce pour garantir intégrité et anti-rejeu.
	 */
	private function signedPayload($action, array $params = []) {

		$cfg        = $this->context->phenyxConfig;
		$licenseKey = $cfg->get('_EPHENYX_LICENSE_KEY_', null, false);
		$sk         = base64_decode($cfg->get('_EPH_API_PRIVATE_KEY_', null, false), true);

		$bodyStr = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$ts      = time();
		$nonce   = bin2hex(random_bytes(16));
		$base    = $licenseKey . "\n" . $action . "\n" . $ts . "\n" . $nonce . "\n" . hash('sha256', $bodyStr);
		$sig     = base64_encode(sodium_crypto_sign_detached($base, $sk));

		return [
			'action'      => $action,
			'license_key' => $licenseKey,
			'ts'          => $ts,
			'nonce'       => $nonce,
			'body'        => $bodyStr,
			'sig'         => $sig,
			'alg'         => 'ed25519',
			'kid'         => (int) $cfg->get('_EPH_API_KEY_ID_', null, false) ?: 1,
		];
	}

	/** Appel API générique. Signe la requête si la clé est enrôlée, sinon repli legacy. */
	private function apiCall($action, array $params = []) {

		$this->ensureApiEnrollment();

		$cfg = $this->context->phenyxConfig;

		if ($cfg->get('_EPH_API_KEY_REGISTERED_', null, false)) {
			// Schéma cible : requête signée Ed25519.
			$data_array = $this->signedPayload($action, $params);
		} else {
			// Repli legacy tant que la clé publique n'est pas enrôlée côté serveur
			// (ex. serveur pas encore migré). Garantit la compatibilité ascendante.
			$data_array = array_merge($params, [
				'action'      => $action,
				'license_key' => $cfg->get('_EPHENYX_LICENSE_KEY_', null, false),
				'crypto_key'  => $this->_crypto_key,
			]);
		}

		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setTimeout(30);
		$curl->post($this->_url, json_encode($data_array));

		return $curl->response;
	}

	/**
	 * Enregistre la clé publique du site sur ephenyx.io. Authentifié par le
	 * schéma legacy (crypto_key) le temps de la transition.
	 */
	public function registerApiPublicKey() {

		$cfg = $this->context->phenyxConfig;

		$data_array = [
			'action'      => 'registerPublicKey',
			'license_key' => $cfg->get('_EPHENYX_LICENSE_KEY_', null, false),
			'crypto_key'  => $this->_crypto_key,
			'public_key'  => $cfg->get('_EPH_API_PUBLIC_KEY_', null, false),
			'key_alg'     => 'ed25519',
			'key_id'      => 1,
		];

		$curl = new Curl();
		$curl->setDefaultJsonDecoder($assoc = true);
		$curl->setHeader('Content-Type', 'application/json');
		$curl->setTimeout(30);
		$curl->post($this->_url, json_encode($data_array));

		$resp = $curl->response;

		return is_array($resp) ? !empty($resp['success']) : false;
	}

	public function checkLicense() {

		return $this->apiCall('checkLicence', []);
	}

	public function getPhenyxPlugins() {

		$plugins = Plugin::getInstalledPluginsOnDisk();

		$response = $this->apiCall('getPhenyxPlugins', ['plugins' => $plugins]);
		$response = Tools::jsonDecode(Tools::jsonEncode($response), true);

		if (is_array($response)) {
			file_put_contents(
				_EPH_CONFIG_DIR_ . 'json/plugin_sources.json',
				json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
			);
			chmod(_EPH_CONFIG_DIR_ . 'json/plugin_sources.json', 0777);
			return true;
		}

		return false;

	}

	// =========================================================================
	// ph_upgrader — actions de mise a jour du coeur (meme auth que getPhenyxPlugins)
	// =========================================================================

	public function checkCoreUpdate($channel = 'stable') {

		return $this->apiCall('checkCoreUpdate', ['channel' => $channel, 'version' => _EPH_VERSION_]);
	}

	/**
	 * Chemin de mise a jour ordonne entre la version installee et la derniere
	 * publiee du canal. Ajoute le 2026-07-28 : voir la note en tete de
	 * CoreUpgrader::run() (plugin ph_upgrader) — les paquets etant des deltas,
	 * un site en retard de deux versions doit les appliquer l'une apres
	 * l'autre.
	 */
	public function getUpgradePath($channel = 'stable') {

		return $this->apiCall('getUpgradePath', ['channel' => $channel, 'version' => _EPH_VERSION_]);
	}

	public function getCorePackage($version) {

		return $this->apiCall('getCorePackage', ['version' => $version]);
	}

	public function reportUpgrade($from, $to, $state, $message = '') {

		return $this->apiCall('reportUpgrade', [
			'version_from' => $from,
			'version_to'   => $to,
			'state'        => $state,
			'message'      => $message,
		]);
	}

	/**
	 * Met a jour UNIQUEMENT la version (_EPH_VERSION_) dans settings.inc.php,
	 * en place. Les adresses de base et les secrets sont desormais charges depuis
	 * .env ($_ENV) : on ne reecrit donc plus tout le fichier (ce qui figerait les
	 * credentials en dur et casserait le schema .env).
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	public function writeNewSettings($version) {

		$settingsFile = _EPH_CONFIG_DIR_ . 'settings.inc.php';

		if (!is_file($settingsFile) || !is_writable($settingsFile)) {
			PhenyxLogger::addLog('writeNewSettings: settings.inc.php absent ou non inscriptible', 3, null, 'PhenyxTools');
			return false;
		}

		$content = file_get_contents($settingsFile);

		if ($content === false) {
			return false;
		}

		// Sauvegarde de securite.
		@copy($settingsFile, str_replace('.php', '.old.php', $settingsFile));

		$safeVersion = addslashes($version);
		$count       = 0;

		// Remplacement en place de la valeur du define _EPH_VERSION_.
		$new = preg_replace(
			"/define\\(\\s*'_EPH_VERSION_'\\s*,\\s*'[^']*'\\s*\\)\\s*;/",
			"define('_EPH_VERSION_', '" . $safeVersion . "');",
			$content,
			1,
			$count
		);

		if ($new === null) {
			return false;
		}

		// Si le define n'existait pas, on l'ajoute en fin de fichier.
		if ($count === 0) {
			$new = rtrim($content) . PHP_EOL . "define('_EPH_VERSION_', '" . $safeVersion . "');" . PHP_EOL;
		}

		$ok = (bool) @file_put_contents($settingsFile, $new);

		if ($ok && function_exists('opcache_invalidate')) {
			opcache_invalidate($settingsFile, true);
		}

		return $ok;
	}

	public function alterSqlTable(string $table, string $column, string $type, string $after): void {

    	$query = 'SELECT `COLUMN_NAME`
        	FROM `INFORMATION_SCHEMA`.`COLUMNS`
			WHERE `TABLE_SCHEMA` = \'' . pSQL(_DB_NAME_) . '\'
        	AND `TABLE_NAME` = \'' . pSQL(_DB_PREFIX_ . $table) . '\'
			AND `COLUMN_NAME` = \'' . pSQL($column) . '\'';

    	$result = Db::getInstance()->getValue(trim($query));

    	if ($result !== $column) {
        	$sql = 'ALTER TABLE `' . bqSQL(_DB_PREFIX_ . $table) . '`'
				. ' ADD `' . bqSQL($column) . '` ' . $type
				. ' AFTER `' . bqSQL($after) . '`';
        	Db::getInstance()->execute(trim($sql));
    	}
	}

	public function checkString($string) {

		if (!is_numeric($string)) {
			$string = addslashes($string);
		}

		return $string;
	}

	/**
	 * ───────────────────────────────────────────────────────────────────────
	 * Helpers de maintenance de schéma — idempotents et gardés.
	 *
	 * En MySQL, les ALTER TABLE (DROP/ADD PRIMARY KEY, DROP/ADD INDEX) font un
	 * COMMIT implicite : ils ne sont PAS annulables par ROLLBACK. La seule
	 * protection fiable est donc de ne jamais exécuter une opération qui
	 * échouerait sur l'état courant (supprimer un index absent, ajouter une clé
	 * déjà présente). Conséquence : une fonction de maintenance ne s'interrompt
	 * plus en laissant la table sans clé primaire, et peut être relancée sans
	 * provoquer d'erreur — plus besoin de restaurer la table d'origine.
	 * ───────────────────────────────────────────────────────────────────────
	 *
	 * @param string $table nom de table SANS préfixe
	 * @param string $index nom d'index ('PRIMARY' = clé primaire)
	 */
	public function indexExists($table, $index) {

		$count = Db::getInstance()->getValue(
			'SELECT COUNT(*) FROM information_schema.STATISTICS'
			. ' WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\''
			. ' AND TABLE_NAME = \'' . _DB_PREFIX_ . pSQL($table) . '\''
			. ' AND INDEX_NAME = \'' . pSQL($index) . '\''
		);

		return (int) $count > 0;
	}

	public function primaryKeyExists($table) {

		return $this->indexExists($table, 'PRIMARY');
	}

	public function dropIndexIfExists($table, $index) {

		if (!$this->indexExists($table, $index)) {
			return true;
		}

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` DROP INDEX `' . bqSQL($index) . '`');
	}

	public function dropPrimaryKeyIfExists($table) {

		if (!$this->primaryKeyExists($table)) {
			return true;
		}

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` DROP PRIMARY KEY');
	}

	public function addPrimaryKeyIfMissing($table, $columns) {

		if ($this->primaryKeyExists($table)) {
			return true;
		}

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` ADD PRIMARY KEY (' . $columns . ')');
	}

	public function addIndexIfMissing($table, $index, $columns, $unique = false) {

		if ($this->indexExists($table, $index)) {
			return true;
		}

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` ADD ' . ($unique ? 'UNIQUE' : 'INDEX') . ' `' . bqSQL($index) . '` (' . $columns . ')');
	}

	/**
	 * Met la colonne en AUTO_INCREMENT. À appeler APRÈS que la clé primaire
	 * existe (une colonne AUTO_INCREMENT doit être indexée).
	 */
	public function setAutoIncrement($table, $column, $definition = 'INT(10) UNSIGNED NOT NULL') {

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` CHANGE `' . bqSQL($column) . '` `' . bqSQL($column) . '` ' . $definition . ' AUTO_INCREMENT');
	}

	/**
	 * Retire l'attribut AUTO_INCREMENT (nécessaire avant de pouvoir DROP la PK).
	 */
	public function removeAutoIncrement($table, $column, $definition = 'INT(10) UNSIGNED NOT NULL') {

		return $this->runSql('ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` CHANGE `' . bqSQL($column) . '` `' . bqSQL($column) . '` ' . $definition);
	}

	/**
	 * Exécute une requête, accumule le résultat et MÉMORISE le 1er échec
	 * (requête + message SQL) dans $this->lastError pour pouvoir le remonter à
	 * l'utilisateur. $result reste un booléen ET-accumulé.
	 */
	/**
	 * Exécute une requête et mémorise le 1er échec (message SQL + requête) dans
	 * $this->lastError. Retourne le booléen de succès (sans accumuler).
	 */
	/** @var int Profondeur d'imbrication des transactions. */
	protected $txDepth = 0;

	/** @var bool Le .htaccess doit-il etre reecrit apres validation ? */
	protected $htaccessPending = false;

	/**
	 * Execute une operation de maintenance dans une transaction.
	 *
	 * ─── POURQUOI C'EST DEVENU POSSIBLE ───
	 *
	 * Ces methodes retiraient l'auto-increment et la cle primaire avant de
	 * renumeroter, puis les remettaient. C'etait inutile sur les deux plans :
	 *
	 *  - l'AUTO_INCREMENT ne concerne que les INSERT, il n'a jamais empeche de
	 *    mettre a jour la colonne ;
	 *  - la CLE PRIMAIRE n'exige que l'unicite, et l'astuce du decalage — tout
	 *    deplacer au-dela du plus grand identifiant avant de redescendre a
	 *    1..n — la garantit a chaque instant. cleanHook s'en passe meme
	 *    naturellement : en parcourant par identifiant croissant et en
	 *    assignant 1, 2, 3..., la cible est toujours inferieure ou egale a la
	 *    valeur courante, donc deja liberee.
	 *
	 * Et c'etait surtout NUISIBLE : en MySQL, tout ALTER TABLE valide
	 * implicitement la transaction en cours. Tant qu'ils etaient la, aucune
	 * protection n'etait possible — une interruption laissait la table a moitie
	 * renumerotee, parfois sans cle primaire, sans reprise possible. C'est ce
	 * qui a impose deux restaurations le 2026-07-30.
	 *
	 * Les vingt-huit manipulations de schema ont donc ete retirees. Les
	 * operations sont desormais purement transactionnelles.
	 *
	 * ⚠️ Cela suppose des tables InnoDB. Sur une table MyISAM, la transaction
	 * est silencieusement sans effet — le comportement est alors celui d'avant,
	 * ni meilleur ni pire.
	 *
	 * @param string   $label Nom de l'operation, pour le journal
	 * @param callable $work  Le corps de l'operation ; rend un booleen
	 *
	 * @return bool
	 */
	public function transactional($label, callable $work, ?array $renumbered = null) {

		$renumbered = $renumbered ?: [];

		// En simulation rien n'est ecrit : une transaction n'aurait pas d'objet.
		if ($this->dryRun) {
			$ok = (bool) $work();

			foreach ($renumbered as $table => $column) {
				$this->plan($table, 'RECALAGE AUTO_INCREMENT');
			}

			return $ok;
		}

		$this->beginTx();

		try {
			$ok = (bool) $work();
		} catch (\Throwable $e) {
			$this->rollbackTx();
			$this->lastError = $label . ' : ' . $e->getMessage();
			PhenyxLogger::addLog('PhenyxTools::' . $label . ' — annulee, rien n\'a ete modifie : ' . $e->getMessage(), 3);

			return false;
		}

		if (!$ok) {
			$this->rollbackTx();
			PhenyxLogger::addLog('PhenyxTools::' . $label . ' — annulee, rien n\'a ete modifie. ' . $this->lastError, 3);

			return false;
		}

		$this->commitTx();

		// ─── Recalage du compteur d'auto-increment, APRES validation ───
		//
		// La renumerotation rend les identifiants contigus de 1 a n, mais le
		// compteur de la table reste sur son ancien plafond : la prochaine
		// insertion repartirait tres au-dessus, et le rangement n'aurait servi
		// qu'a moitie.
		//
		// L'ancien code obtenait ce recalage par effet de bord — reattribuer
		// AUTO_INCREMENT a une colonne fait repartir le compteur a MAX(id)+1.
		// En retirant ces ALTER pour rendre les operations transactionnelles,
		// j'avais supprime le recalage avec. On le refait donc explicitement,
		// et HORS transaction puisque c'est du DDL.
		foreach ($renumbered as $table => $column) {
			$this->syncAutoIncrement($table, $column);
		}

		return true;
	}

	/**
	 * Recale le compteur d'auto-increment sur MAX(id) + 1.
	 *
	 * A n'appeler qu'en dehors d'une transaction : c'est du DDL, donc un COMMIT
	 * implicite en MySQL.
	 *
	 * @param string $table  Sans le prefixe
	 * @param string $column Colonne portant l'auto-increment
	 *
	 * @return bool
	 */
	public function syncAutoIncrement($table, $column) {

		if ($this->dryRun) {
			return true;
		}

		try {
			$next = (int) Db::getInstance()->getValue(
				'SELECT IFNULL(MAX(`' . bqSQL($column) . '`), 0) + 1 FROM `' . _DB_PREFIX_ . bqSQL($table) . '`'
			);

			if ($next < 1) {
				$next = 1;
			}

			return (bool) Db::getInstance()->execute(
				'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($table) . '` AUTO_INCREMENT = ' . $next
			);
		} catch (\Throwable $e) {
			// Sans gravite : les insertions continueront simplement au-dessus de
			// l'ancien plafond. On le signale sans faire echouer l'operation,
			// qui est deja validee.
			PhenyxLogger::addLog(
				'PhenyxTools::syncAutoIncrement(' . $table . ') a échoué : ' . $e->getMessage(),
				2
			);

			return false;
		}

	}

	/**
	 * @return bool
	 */
	public function beginTx() {

		if ($this->dryRun) {
			return true;
		}

		if ($this->txDepth++ > 0) {
			return true;
		}

		return (bool) Db::getInstance()->execute('START TRANSACTION');
	}

	/**
	 * @return bool
	 */
	public function commitTx() {

		if ($this->dryRun) {
			return true;
		}

		if (--$this->txDepth > 0) {
			return true;
		}

		$this->txDepth = 0;

		return (bool) Db::getInstance()->execute('COMMIT');
	}

	/**
	 * @return bool
	 */
	public function rollbackTx() {

		if ($this->dryRun) {
			return true;
		}

		$this->txDepth = 0;

		return (bool) Db::getInstance()->execute('ROLLBACK');
	}

	public function runSql($sql) {

		// ─── Mode simulation ───
		//
		// On intercepte ici, au point de passage unique de toutes les ecritures.
		// Les lectures n'empruntent pas ce chemin (elles vont directement a
		// Db::getInstance()->executeS/getValue), donc l'analyse reste exacte :
		// memes decomptes, memes decisions, aucune ecriture.
		if ($this->dryRun) {
			$table = '?';

			if (preg_match('/(?:FROM|INTO|UPDATE|TABLE)\s+`?' . preg_quote(_DB_PREFIX_, '/') . '?([a-z_]+)`?/i', $sql, $m)) {
				$table = $m[1];
			}

			$op = 'REQUETE';

			if (preg_match('/^\s*(DELETE|UPDATE|INSERT|ALTER|TRUNCATE|DROP)/i', $sql, $m)) {
				$op = strtoupper($m[1]);
			}

			$this->plan($table, $op);

			return true;
		}

		$ok = (bool) Db::getInstance()->execute($sql);

		if (!$ok && $this->lastError === '') {
			$this->lastError = Db::getInstance()->getMsgError() . ' — SQL: ' . $sql;
		}

		return $ok;
	}

	/**
	 * Idem runSql() mais accumule le résultat global dans $result (ET booléen).
	 */
	public function exec($sql, &$result) {

		$ok = $this->runSql($sql);
		$result = ((bool) $result) && $ok;

		return $ok;
	}

	public function tableExists($table) {

		$count = Db::getInstance()->getValue(
			'SELECT COUNT(*) FROM information_schema.TABLES'
			. ' WHERE TABLE_SCHEMA = \'' . _DB_NAME_ . '\''
			. ' AND TABLE_NAME = \'' . _DB_PREFIX_ . pSQL($table) . '\''
		);

		return (int) $count > 0;
	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanBackTabsWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanBackTabs() {

		return $this->transactional('cleanBackTabs', function () {
			return $this->cleanBackTabsWork();
		}, ['back_tab' => 'id_back_tab']);
	}

	protected function cleanBackTabsWork() {

		$today = date("Y-m-d");
		$date = new DateTime($today);
		$date->modify('-180 days');
		$dateCheck = $date->format('Y-m-d');
		$last_maintenance = $this->context->phenyxConfig->get('BACK_TAB_MAINTENANCE');

		if (!is_null($last_maintenance) && $last_maintenance > $dateCheck) {
			// On rend true, mais on le SIGNALE : sans cela l'ecran annonce
			// « reconstruction reussie » alors qu'absolument rien n'a ete fait,
			// et l'employe croit avoir agi.
			$this->lastSkipped = true;

			return true;
		}

		$this->lastError = '';
		$this->lastSkipped = false;
		$result = true;

		// ⚠️ L'etape 1 supprime un onglet quand class_exists() ne trouve pas son
		// controleur. Or l'index des classes ne se regenere QUE si le fichier
		// indexe a disparu : un index perime fait donc croire a l'absence d'une
		// classe bien presente, et l'onglet — plus sa meta — est supprime a tort.
		//
		// ⚠️ Supprimer le FICHIER ne suffit pas : PhenyxAutoload le charge une
		// seule fois, dans son constructeur, et garde l'index en memoire pour
		// toute la requete. Un unlink n'aurait donc aucun effet sur les
		// class_exists() qui suivent. Il faut demander la reconstruction, qui
		// reassigne l'index en memoire ET reecrit le fichier.
		try {
			PhenyxAutoload::getInstance()->generateIndex();
		} catch (\Throwable $e) {
			// Index non reconstruit : on renonce a l'etape de suppression
			// plutot que de juger sur une information peut-etre fausse.
			$this->lastError = 'Index des classes non reconstruit, suppression des onglets ignoree : ' . $e->getMessage();
			PhenyxLogger::addLog('PhenyxTools::cleanBackTabs — ' . $this->lastError, 3);

			$skipTabDeletion = true;
		}

		// ── 1) Suppression des onglets obsolètes — version SÉCURISÉE.
		//    On ne supprime QUE les onglets : sans plugin associé, sans enfants,
		//    dont le nom ne contient pas 'Parent', ET dont la classe contrôleur
		//    est introuvable. Évite de détruire les onglets de plugins (dont la
		//    classe peut ne pas être chargée pendant la maintenance) et les
		//    nœuds parents du menu.
		$tabClasses = empty($skipTabDeletion)
		? Db::getInstance()->executeS(
			'SELECT `id_back_tab`, `class_name`, `plugin`, `id_parent` FROM `' . _DB_PREFIX_ . 'back_tab` ORDER BY `id_back_tab` ASC'
		)
		: [];

		$hasAccessTable = $this->tableExists('employee_access');
		$candidates = [];

		foreach ($tabClasses as $tablasse) {

			if (class_exists($tablasse['class_name'] . 'Controller')) {
				continue;
			}

			if (str_contains($tablasse['class_name'], 'Parent')) {
				continue;
			}

			if (!empty($tablasse['plugin'])) {
				continue;
			}

			// ⚠️ ONGLET CACHE — ne jamais supprimer.
			//
			// Un onglet monte avec id_parent negatif n'apparait pas dans le menu :
			// il sert de point d'accroche aux PERMISSIONS et de cible aux appels
			// ajax. L'absence de classe de controleur ne prouve donc rien a son
			// sujet, alors que le critere ci-dessus le condamne.
			//
			// Constate le 2026-07-30 : AdminAdminConfigurationPanel,
			// AdminRebuildTabs, AdminFlushApi, AdminFlushSession et AdminFlushFull
			// — les actions de l'ecran Performance — ont ete supprimes ainsi.
			if ((int) $tablasse['id_parent'] < 0) {
				continue;
			}

			// Des permissions pointent vers cet onglet : quelqu'un s'appuie
			// dessus, quoi qu'en dise l'absence de controleur.
			if ($hasAccessTable) {
				$used = (int) Db::getInstance()->getValue(
					'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'employee_access` WHERE `id_back_tab` = ' . (int) $tablasse['id_back_tab']
				);

				if ($used > 0) {
					continue;
				}

			}

			$hasChildren = (int) Db::getInstance()->getValue(
				(new DbQuery())
					->select('COUNT(*)')
					->from('back_tab')
					->where('`id_parent` = ' . (int) $tablasse['id_back_tab'])
			);

			if ($hasChildren > 0) {
				continue;
			}

			$id_meta = Meta::getIdMetaByPage(strtolower($tablasse['class_name']));

			// Ces suppressions passent par les objets, pas par runSql() : il faut
			// les consigner explicitement en simulation.
			if ($this->dryRun) {
				$this->plan('back_tab', 'SUPPRESSION', 'Onglet « ' . $tablasse['class_name'] .' » (contrôleur introuvable)');

				if ($id_meta > 0) {
					$this->plan('meta', 'SUPPRESSION', 'Méta de « ' . $tablasse['class_name'] . ' »');
				}

				continue;
			}

			// Suppression desactivee par defaut : on signale, on ne detruit pas.
			if (!$this->allowTabDeletion) {
				$candidates[] = $tablasse['class_name'];
				continue;
			}

			$bckTab = new BackTab($tablasse['id_back_tab']);
			$bckTab->delete();

			if ($id_meta > 0) {
				$meta = new Meta($id_meta);
				$meta->delete();
			}

		}

		if (!empty($candidates)) {
			// Signale sans detruire : la suppression ne se repare que par
			// restauration, et le critere s'est deja trompe.
			$this->lastNotice = sprintf(
				'%1$d onglet(s) sans contrôleur repéré(s), non supprimé(s) : %2$s. Vérifiez avant d\'autoriser leur suppression.',
				count($candidates),
				implode(', ', $candidates)
			);
			PhenyxLogger::addLog('cleanBackTabs — onglets sans contrôleur, conservés : ' . implode(', ', $candidates), 1);
		}

		// ── 2) Renumérotation contiguë des id_back_tab EN PRÉSERVANT la hiérarchie.
		//    BUG corrigé : l'ancienne version décalait id_back_tab sans jamais
		//    remapper id_parent → l'arborescence était détruite (id_parent
		//    pointant vers des ids disparus). On construit une correspondance
		//    old_id => new_id appliquée À LA FOIS à id_back_tab ET à id_parent.
		$rows = Db::getInstance()->executeS(
			'SELECT `id_back_tab`, `id_parent` FROM `' . _DB_PREFIX_ . 'back_tab` ORDER BY `id_back_tab` ASC'
		);

		$map = [];
		$new = 1;
		$maxId = 0;

		foreach ($rows as $row) {
			$oldId = (int) $row['id_back_tab'];
			$map[$oldId] = $new;
			$new++;

			if ($oldId > $maxId) {
				$maxId = $oldId;
			}
		}

		// Offset garanti supérieur à tous les ids existants : aucune collision
		// pendant la transition.
		$offset = $maxId + 1;
		$hasAccess = $this->tableExists('employee_access');

		// Retrait de l'AUTO_INCREMENT pour réassigner librement (la PK reste).

		// Phase A : tout vers (new_id + offset) ; id_parent remappé identiquement.
		foreach ($rows as $row) {
			$oldId = (int) $row['id_back_tab'];
			$oldParent = (int) $row['id_parent'];
			$tmpId = $map[$oldId] + $offset;

			// Orphelin : id_parent designe un onglet qui n'existe plus. L'ancienne
			// ecriture conservait la valeur telle quelle — apres renumerotation,
			// cette valeur designe un AUTRE onglet, et l'orphelin reapparait sous
			// un parent sans rapport. On le remonte a la racine : visible, donc
			// corrigeable, plutot que discretement mal range.
			if ($oldParent > 0 && !isset($map[$oldParent])) {
				PhenyxLogger::addLog(
					'PhenyxTools::cleanBackTabs — onglet ' . $oldId . ' rattache a un parent inexistant ('
					. $oldParent . '), remonte a la racine.',
					2
				);
				$oldParent = 0;
			}

			$tmpParent = ($oldParent > 0) ? ($map[$oldParent] + $offset) : $oldParent;

			$this->exec('UPDATE `' . _DB_PREFIX_ . 'back_tab` SET `id_back_tab` = ' . $tmpId . ', `id_parent` = ' . $tmpParent . ' WHERE `id_back_tab` = ' . $oldId, $result);
			$this->exec('UPDATE `' . _DB_PREFIX_ . 'back_tab_lang` SET `id_back_tab` = ' . $tmpId . ' WHERE `id_back_tab` = ' . $oldId, $result);

			if ($hasAccess) {
				$this->exec('UPDATE `' . _DB_PREFIX_ . 'employee_access` SET `id_back_tab` = ' . $tmpId . ' WHERE `id_back_tab` = ' . $oldId, $result);
			}
		}

		// Phase B : retrait de l'offset → ids finaux 1..n, hiérarchie intacte.
		$this->exec('UPDATE `' . _DB_PREFIX_ . 'back_tab` SET `id_back_tab` = `id_back_tab` - ' . $offset . ' WHERE `id_back_tab` > ' . $offset, $result);
		$this->exec('UPDATE `' . _DB_PREFIX_ . 'back_tab` SET `id_parent` = `id_parent` - ' . $offset . ' WHERE `id_parent` > ' . $offset, $result);
		$this->exec('UPDATE `' . _DB_PREFIX_ . 'back_tab_lang` SET `id_back_tab` = `id_back_tab` - ' . $offset . ' WHERE `id_back_tab` > ' . $offset, $result);

		if ($hasAccess) {
			$this->exec('UPDATE `' . _DB_PREFIX_ . 'employee_access` SET `id_back_tab` = `id_back_tab` - ' . $offset . ' WHERE `id_back_tab` > ' . $offset, $result);
		}

		// Remise de l'AUTO_INCREMENT (la PK est intacte).

		if ($result && $this->context->cache_enable && is_object($this->context->cache_api)) {
			$this->context->cache_api->cleanByStartingKey('generateTabs_');
			$this->context->cache_api->cleanByStartingKey('getBckTab_');
		}

		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('BACK_TAB_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	public function getLegitimeMeta() {

		$controllers = [];

		// (1) Comportement historique : Meta::getPages() capte correctement les
		//     pages dont la classe est déjà chargée (via Reflection).
		$ctrls = Meta::getPages();

		if (is_array($ctrls)) {

			foreach ($ctrls as $ctrl) {

				if (!is_array($ctrl)) {
					continue;
				}

				foreach ($ctrl as $k => $value) {
					$controllers[] = str_contains((string) $k, '/') ? explode('/', $k)[1] : $k;
				}

			}

		}

		// (2) FIABILISATION : scan DIRECT des fichiers contrôleurs, par nom de
		//     fichier, SANS Reflection ni chargement de classe. Indispensable en
		//     contexte de maintenance où la plupart des classes ne sont pas
		//     chargées (sinon getPages() renvoie une liste tronquée et cleanMetas
		//     supprimait des métas valides).
		//     Token = nom de fichier sans 'Controller.php' en minuscules :
		//       CategoryController.php      -> 'category'
		//       AdminPhlinksController.php  -> 'adminphlinks'
		//     ce qui correspond au champ meta.page.
		$scan = [];

		$coreDirs = [
			_EPH_CORE_DIR_ . '/includes/controllers/front/',
			_EPH_CORE_DIR_ . '/includes/controllers/backend/',
			_EPH_CORE_DIR_ . '/includes/specific_controllers/',
		];

		// ⚠️ Tools::scandir() rend des chemins RELATIFS au repertoire de depart
		// (« admin/AdminFooController.php »). basename() s'en accommode, mais
		// toute lecture du fichier exige le chemin complet. On absolutise donc
		// des la collecte.
		foreach ($coreDirs as $dir) {

			if (is_dir($dir)) {

				foreach ((array) Tools::scandir($dir, 'php', '', true) as $rel) {
					$scan[] = rtrim($dir, '/') . '/' . $rel;
				}

			}

		}

		// ⚠️ Le scan portait sur la RACINE de chaque plugin, en recursif. Sur une
		// installation fournie cela represente plus de six mille fichiers PHP
		// repartis dans plus de deux mille repertoires — vues, traductions,
		// bibliotheques embarquees — pour n'en retenir qu'une poignee de
		// *Controller.php. Le cout etait tel que la requete pouvait expirer sans
		// rendre la moindre reponse.
		//
		// Les controleurs d'un plugin vivent toujours dans l'un de ces deux
		// emplacements. On ne parcourt plus qu'eux.
		foreach (Plugin::getPluginsInstalled() as $plugin) {

			foreach ([_EPH_PLUGIN_DIR_, _EPH_SPECIFIC_PLUGIN_DIR_] as $base) {
				$root = $base . $plugin['name'];

				if (!is_dir($root)) {
					continue;
				}

				$dirs = [$root . '/controllers/'];

				// classes/controller/ — mais pas seulement : ph_wiki porte un
				// « classes/controlller/ » avec trois L. Un chemin fige aurait
				// exclu ce controleur de la liste des pages valides, rendant sa
				// meta supprimable. On accepte donc toute variante.
				foreach ((array) @glob($root . '/classes/*', GLOB_ONLYDIR) as $sub) {

					if (stripos(basename($sub), 'controll') === 0) {
						$dirs[] = $sub . '/';
					}

				}

				foreach ($dirs as $dir) {

					if (is_dir($dir)) {

						foreach ((array) Tools::scandir($dir, 'php', '', true) as $rel) {
							$scan[] = rtrim($dir, '/') . '/' . $rel;
						}

					}

				}

			}

		}

		foreach ($scan as $file) {
			$base = basename($file);

			if ($base === 'index.php') {
				continue;
			}

			$name = preg_replace('/Controller\.php$/i', '', $base);

			// On ne retient que les fichiers *Controller.php.
			if ($name === $base) {
				continue;
			}

			$controllers[] = strtolower($name);

			// ⚠️ LE NOM DU FICHIER NE SUFFIT PAS.
			//
			// La colonne meta.page stocke le php_self du controleur, PAS son nom
			// de fichier. Les deux coincident presque toujours — mais pas
			// toujours :
			//
			//   AuthController.php          -> php_self 'authentication'
			//   MyAccountController.php     -> php_self 'my-account'
			//   PfgModelController.php      -> php_self 'pfg-model'
			//   AdminGsiteMapsController.php-> php_self 'admingsitemap'
			//
			// Le repli par nom de fichier declarait donc « authentication »
			// illegitime, et sa meta partait a la suppression — avec, dans la
			// foulee, la regle de reecriture correspondante dans le .htaccess.
			// C'est ce qui a casse le site le 2026-07-30.
			//
			// On lit donc le php_self a la source. Une simple expression
			// reguliere suffit : ni chargement de classe, ni Reflection, donc
			// utilisable en contexte de maintenance.
			$src = @file_get_contents($file);

			if ($src !== false && preg_match('/\$php_self\s*=\s*[\'"]([^\'"]+)[\'"]/', $src, $m)) {
				$controllers[] = strtolower($m[1]);
			}

		}

		return array_values(array_unique(array_filter($controllers)));

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanGuestWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanGuest() {

		return $this->transactional('cleanGuest', function () {
			return $this->cleanGuestWork();
		}, ['guest' => 'id_guest']);
	}

	protected function cleanGuestWork() {

		$query = 'SELECT id_guest  FROM `' . _DB_PREFIX_ . 'guest` WHERE id_user = 0';

		$guests = Db::getInstance()->executeS($query);

		foreach ($guests as $guest) {
			$guest = new Guest($guest['id_guest']);
			$guest->delete();
		}

		$result = true;


		$query = 'SELECT id_guest  FROM `' . _DB_PREFIX_ . 'guest` ORDER BY id_guest ASC';

		$guests = Db::getInstance()->executeS($query);
		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_guest`) + 1')
				->from('guest')
		);

		foreach ($guests as $guest) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'guest` SET `id_guest` = ' . $maxIndex . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'guest_meta` SET `id_guest` = ' . $maxIndex . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'connections` SET `id_guest` = ' . $maxIndex . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			Hook::getInstance()->exec('updateGuestIndex', ['index' => $maxIndex, 'id_guest' => $guest['id_guest']]);
			$maxIndex++;
		}

		$query = 'SELECT id_guest  FROM `' . _DB_PREFIX_ . 'guest` ORDER BY id_guest ASC';

		$guests = Db::getInstance()->executeS($query);
		$i = 1;

		foreach ($guests as $guest) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'guest` SET `id_guest` = ' . $i . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'guest_meta` SET `id_guest` = ' . $i . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'connections` SET `id_guest` = ' . $i . ' WHERE `id_guest` = ' . $guest['id_guest'];
			$this->exec($sql, $result);
			Hook::getInstance()->exec('updateGuestIndex', ['index' => $i, 'id_guest' => $guest['id_guest']]);

			$i++;
		}


		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('GUEST_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanConfigurationWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanConfiguration() {

		return $this->transactional('cleanConfiguration', function () {
			return $this->cleanConfigurationWork();
		}, ['configuration' => 'id_configuration']);
	}

	protected function cleanConfigurationWork() {

		// $result doit être initialisé AVANT la première boucle : elle fait
		// déjà $result &= ... (suppression des configuration_lang orphelins).
		// Auparavant l'init était placée après cette boucle, ce qui (1) émettait
		// un warning "undefined variable" et (2) écrasait/masquait le résultat
		// des suppressions d'orphelins.
		$result = true;

		$query = 'SELECT id_configuration  FROM `' . _DB_PREFIX_ . 'configuration_lang` ORDER BY id_configuration ASC';
		$configurations = Db::getInstance()->executeS($query);

		foreach ($configurations as $configuration) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_configuration`')
					->from('configuration')
					->where('`id_configuration` = ' . (int) $configuration['id_configuration'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'configuration_lang` WHERE id_configuration = ' . $configuration['id_configuration'];
				$this->exec($sql, $result);
			}

		}


		$query = 'SELECT id_configuration  FROM `' . _DB_PREFIX_ . 'configuration` ORDER BY id_configuration ASC';

		$configurations = Db::getInstance()->executeS($query);
		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_configuration`) + 1')
				->from('configuration')
		);

		foreach ($configurations as $configuration) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'configuration` SET `id_configuration` = ' . $maxIndex . ' WHERE `id_configuration` = ' . $configuration['id_configuration'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'configuration_lang` SET `id_configuration` = ' . $maxIndex . ' WHERE `id_configuration` = ' . $configuration['id_configuration'];
			$this->exec($sql, $result);

			$maxIndex++;
		}

		$query = 'SELECT id_configuration  FROM `' . _DB_PREFIX_ . 'configuration` ORDER BY id_configuration ASC';

		$configurations = Db::getInstance()->executeS($query);
		$i = 1;

		foreach ($configurations as $configuration) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'configuration` SET `id_configuration` = ' . $i . ' WHERE `id_configuration` = ' . $configuration['id_configuration'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'configuration_lang` SET `id_configuration` = ' . $i . ' WHERE `id_configuration` = ' . $configuration['id_configuration'];
			$this->exec($sql, $result);

			$i++;
		}


		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('CONFIGURATION_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanMetasWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanMetas() {

		$this->htaccessPending = false;

		$ok = $this->transactional('cleanMetas', function () {
			return $this->cleanMetasWork();
		}, ['meta' => 'id_meta', 'theme_meta' => 'id_theme_meta']);

		// Apres validation seulement : le .htaccess porte les regles de
		// reecriture derivees des metas, il ne doit refleter que ce qui a
		// reellement ete enregistre.
		if ($ok && $this->htaccessPending && !$this->dryRun) {

			try {
				Tools::generateHtaccess();
			} catch (\Throwable $e) {
				PhenyxLogger::addLog('cleanMetas — régénération du .htaccess impossible : ' . $e->getMessage(), 3);
			}

		}

		return $ok;
	}

	protected function cleanMetasWork() {

		$result = true;
		$this->lastNotice = '';

		$legitimeMetas = $this->getLegitimeMeta();
		$idLang = $this->context->language->id;

		$metas = Db::getInstance()->executeS(
			'SELECT `id_meta`, `page`, `plugin` FROM `' . _DB_PREFIX_ . 'meta` ORDER BY `id_meta` ASC'
		);

		// ── Suppression DÉFENSIVE des métas parasites.
		//    getLegitimeMeta() s'appuie sur Meta::getPages() qui lit le php_self
		//    des contrôleurs par Reflection. En contexte de maintenance, beaucoup
		//    de classes ne sont pas chargées → la liste « légitime » peut être
		//    TRONQUÉE, ce qui faisait supprimer des métas valides (table meta
		//    vidée intégralement). Deux garde-fous :
		//      1) on ne supprime jamais une méta rattachée à un plugin ;
		//      2) GARDE ANTI-CATASTROPHE : si la purge viserait plus de la moitié
		//         de la table, la liste est jugée non fiable et AUCUNE suppression
		//         n'est effectuée (on journalise la raison).
		$toDelete = [];

		foreach ($metas as $meta) {

			if (in_array($meta['page'], $legitimeMetas)) {
				continue;
			}

			if (!empty($meta['plugin'])) {
				continue;
			}

			$toDelete[] = (int) $meta['id_meta'];
		}

		$total = count($metas);

		if ($total > 0 && count($toDelete) > ($total / 2)) {
			PhenyxLogger::addLog('cleanMetas : purge annulée par sécurité — ' . count($toDelete) . '/' . $total . ' métas auraient été supprimées (liste des pages légitimes probablement tronquée car classes non chargées).', 3);

			// L'annulation ne doit pas rester confinee au journal : l'ecran
			// annoncait « reconstruction reussie » sans mentionner que l'etape
			// principale avait ete abandonnee.
			$this->lastNotice = sprintf(
				'Purge annulée par sécurité : %1$d métas sur %2$d auraient été supprimées, la liste des pages valides semble incomplète. Le rangement a bien eu lieu, aucune méta n\'a été supprimée.',
				count($toDelete),
				$total
			);

			$toDelete = [];
		}

		// ⚠️ Ici se trouvait « new Meta($id); $obj->delete(); » dans une boucle.
		//
		// Meta::delete() se termine par Tools::generateHtaccess(), une fonction
		// de plus de trois cents lignes qui REECRIT INTEGRALEMENT le .htaccess.
		// Supprimer cinquante metas declenchait donc cinquante reecritures
		// completes du fichier. Deux consequences, toutes deux constatees le
		// 2026-07-30 : la requete expirait sans rendre de reponse, et une mort
		// en pleine ecriture laissait un .htaccess TRONQUE — donc un routage
		// casse.
		//
		// On supprime desormais en une seule requete par table, et on ne
		// regenere le .htaccess qu'une fois, a la fin.
		if (!empty($toDelete) && $this->dryRun) {

			// En simulation on nomme les metas visees : un decompte ne dit pas
			// si la liste est raisonnable, la liste des pages le dit.
			foreach ($metas as $meta) {

				if (in_array((int) $meta['id_meta'], $toDelete, true)) {
					$this->plan('meta', 'SUPPRESSION', 'Méta « ' . $meta['page'] . ' »');
				}

			}

			$toDelete = [];
		}

		if (!empty($toDelete)) {
			$ids = implode(',', array_map('intval', $toDelete));

			// meta_lang et theme_meta sont retires explicitement : le nettoyage
			// des orphelins qui suit ne couvre que la langue courante.
			$this->exec('DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE `id_meta` IN (' . $ids . ')', $result);
			$this->exec('DELETE FROM `' . _DB_PREFIX_ . 'theme_meta` WHERE `id_meta` IN (' . $ids . ')', $result);
			$this->exec('DELETE FROM `' . _DB_PREFIX_ . 'meta` WHERE `id_meta` IN (' . $ids . ')', $result);

			// ⚠️ La reecriture du .htaccess est DIFFEREE apres la validation.
			//
			// C'est une ecriture de FICHIER : elle ne s'annule pas. Faite ici,
			// a l'interieur de la transaction, elle survivrait a un retour
			// arriere — le fichier refleterait des suppressions que la base
			// n'aurait pas enregistrees, et la page perdrait sa regle de
			// reecriture pour rien. Voir cleanMetas().
			$this->htaccessPending = true;

			if ($this->dryRun) {
				$this->plan('.htaccess', 'REECRITURE');
			}

		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'theme_meta` ORDER BY id_meta ASC';
		$themeMetas = Db::getInstance()->executeS($query);

		foreach ($themeMetas as $themeMeta) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_meta`')
					->from('meta')
					->where('`id_meta` = ' . (int) $themeMeta['id_meta'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'theme_meta` WHERE id_meta = ' . $themeMeta['id_meta'];
				$this->exec($sql, $result);
			}

		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_lang = ' . $idLang . ' ORDER BY id_meta ASC';
		$metaLangs = Db::getInstance()->executeS($query);

		foreach ($metaLangs as $metaLang) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_meta`')
					->from('meta')
					->where('`id_meta` = ' . (int) $metaLang['id_meta'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE id_meta = ' . $metaLang['id_meta'];
				$this->exec($sql, $result);
			}

		}

		$query = 'SELECT id_theme_meta  FROM `' . _DB_PREFIX_ . 'theme_meta` ORDER BY id_theme_meta ASC';

		$theme_metas = Db::getInstance()->executeS($query);
		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_theme_meta`) + 1')
				->from('theme_meta')
		);

		foreach ($theme_metas as $theme_meta) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'theme_meta` SET `id_theme_meta` = ' . $maxIndex . ' WHERE `id_theme_meta` = ' . $theme_meta['id_theme_meta'];
			$this->exec($sql, $result);

			$maxIndex++;
		}

		$query = 'SELECT id_theme_meta  FROM `' . _DB_PREFIX_ . 'theme_meta` ORDER BY id_theme_meta ASC';

		$theme_metas = Db::getInstance()->executeS($query);
		$result &= $this->dropIndexIfExists('theme_meta', 'id_theme_2');
		$result &= $this->dropIndexIfExists('theme_meta', 'id_theme');
		$result &= $this->dropIndexIfExists('theme_meta', 'id_meta');
		$i = 1;

		foreach ($theme_metas as $theme_meta) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'theme_meta` SET `id_theme_meta` = ' . $i . ' WHERE `id_theme_meta` = ' . $theme_meta['id_theme_meta'];
			$this->exec($sql, $result);

			$i++;
		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'meta` ORDER BY id_meta ASC';

		$metas = Db::getInstance()->executeS($query);

		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_meta`) + 1')
				->from('meta')
		);

		$result &= $this->dropIndexIfExists('meta', 'page');
		$result &= $this->dropIndexIfExists('meta_lang', 'id_lang');

		foreach ($metas as $meta) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta` SET `id_meta` = ' . $maxIndex . ' WHERE `id_meta` = ' . $meta['id_meta'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta_lang` SET `id_meta` = ' . $maxIndex . ' WHERE `id_meta` = ' . $meta['id_meta'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'theme_meta` SET `id_meta` = ' . $maxIndex . ' WHERE `id_meta` = ' . $meta['id_meta'];
			$this->exec($sql, $result);

			$maxIndex++;

		}

		$query = 'SELECT id_meta  FROM `' . _DB_PREFIX_ . 'meta` ORDER BY id_meta ASC';

		$metas = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($metas as $meta) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'meta_lang` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'theme_meta` SET id_meta = ' . $i . ' WHERE id_meta = ' . $meta['id_meta'];

			$this->exec($sql, $result);
			$i++;
		}

		// PK d'abord, PUIS AUTO_INCREMENT (une colonne auto_increment doit être indexée).
		$result &= $this->addIndexIfMissing('meta', 'page', '`page`', true);
		$result &= $this->addIndexIfMissing('meta_lang', 'id_lang', '`id_lang`');
		$result &= $this->addIndexIfMissing('theme_meta', 'id_theme_2', '`id_theme`, `id_meta`', true);
		$result &= $this->addIndexIfMissing('theme_meta', 'id_theme', '`id_theme`');
		$result &= $this->addIndexIfMissing('theme_meta', 'id_meta', '`id_meta`');

		if ($result && $this->context->cache_enable && is_object($this->context->cache_api)) {
			$this->context->cache_api->cleanByStartingKey('metaGetPages_');
		}

		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('META_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanPluginHookWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanPluginHook() {

		return $this->transactional('cleanPluginHook', function () {
			return $this->cleanPluginHookWork();
		}, ['hook_plugin' => 'id_hook_plugin']);
	}

	protected function cleanPluginHookWork() {

		$result = true;
		$query = 'SELECT hp.id_plugin, hp.id_hook, h.name as hookname, p.name
        FROM `' . _DB_PREFIX_ . 'hook_plugin` hp
        LEFT JOIN `' . _DB_PREFIX_ . 'hook` h On h.id_hook = hp.id_hook
        LEFT JOIN `' . _DB_PREFIX_ . 'plugin` p On p.id_plugin = hp.id_plugin
        ORDER BY hp.id_plugin ASC';
		$pluginHooks = Db::getInstance()->executeS($query);

		foreach ($pluginHooks as $pluginhook) {

			// L'exemption codee en dur de revslider est devenue inutile : elle
			// est couverte par la regle generale du __call, plus bas. Elle est
			// conservee un temps par prudence, mais ne devrait plus rien filtrer.
			if ($pluginhook['name'] == 'revslider') {
				continue;
			}

			$method = false;

			$retroHookName = $this->context->_hook->getRetroHookName($pluginhook['hookname']);

			if (file_exists(_EPH_PLUGIN_DIR_ . $pluginhook['name'] . '/' . $pluginhook['name'] . '.php')) {
				require_once _EPH_PLUGIN_DIR_ . $pluginhook['name'] . '/' . $pluginhook['name'] . '.php';
			} else

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $pluginhook['name'] . '/' . $pluginhook['name'] . '.php')) {
				require_once _EPH_SPECIFIC_PLUGIN_DIR_ . $pluginhook['name'] . '/' . $pluginhook['name'] . '.php';
			}

			if (class_exists($pluginhook['name'], false)) {

				$plugin = Plugin::getInstanceByName($pluginhook['name']);

				// ⚠️ NE PAS CONFONDRE « ancre non implementee » ET « plugin non
				//    construit ».
				//
				//    getInstanceByName() rend FALSE quand le chargement echoue —
				//    fichier illisible, dependance absente, erreur au
				//    constructeur. method_exists(false, ...) repond « non » pour
				//    absolument tout : le plugin perdait alors la TOTALITE de ses
				//    ancres.
				//
				//    Constate le 2026-07-30 : ph_ecommerce figurait parmi les
				//    suppressions pour actionRegisterAutoloader, alors que
				//    hookActionRegisterAutoloader existe bel et bien dans son
				//    fichier. Ne pas pouvoir instancier un plugin ne prouve rien
				//    sur ce qu'il implemente.
				if (!is_object($plugin)) {
					PhenyxLogger::addLog(
						'cleanPluginHook — « ' . $pluginhook['name'] . ' » n\'a pas pu être instancié, '
						. 'ses ancres sont conservées par précaution.',
						2
					);

					continue;
				}

				// ⚠️ UN PLUGIN QUI ROUTE SES ANCRES PAR __call() N'A AUCUNE
				//    METHODE LITTERALE — et method_exists() repond « non » pour
				//    toutes. Le critere le condamnait donc en bloc.
				//
				//    Trois plugins sont dans ce cas : ph_manager (route tout
				//    hookXxx vers contenthookvalue()), phenyxbanners (route les
				//    hookdisplay* vers displayNativeHook()) et revslider — d'ou
				//    l'exemption en dur de ce dernier, qui traitait le symptome
				//    sans nommer la cause.
				//
				//    ph_manager est le plugin maitre : supprimer ses
				//    enregistrements revient a eteindre l'affichage du site.
				//
				//    On ne peut pas savoir ce qu'un __call accepte. On s'abstient
				//    donc, ce qui est la seule position tenable.
				if (method_exists($plugin, '__call')) {
					continue;
				}

				if (method_exists($plugin, 'hook' . $pluginhook['hookname']) || method_exists($plugin, 'hook' . $retroHookName)) {
					$method = true;
				}

				if ($method) {
					continue;
				}

				// On NOMME la suppression en simulation. Un decompte ne dit pas
				// si une liste est raisonnable ; « ph_manager perd
				// displayFooterBottom » le dit immediatement. Lecon des metas :
				// 4 suppressions anonymes cachaient la page de connexion.
				if ($this->dryRun) {

					// Diagnostic : cinq hypotheses sont tombees pour expliquer
					// pourquoi ph_ecommerce figure ici alors que la methode
					// existe dans son fichier. On cesse de supposer et on
					// consigne ce que le code VOIT — classe reelle de l'objet,
					// noms exactement testes, delimites pour reveler tout
					// caractere parasite.
					$this->plan(
						'hook_plugin',
						'SUPPRESSION',
						$pluginhook['name'] . ' perd l\'ancre ' . $pluginhook['hookname']
						. '  [classe=' . get_class($plugin)
						. ' | testé: «hook' . $pluginhook['hookname'] . '»'
						. ' et «hook' . $retroHookName . '»'
						. ' | méthodes hook de l\'objet: ' . count(array_filter(
							get_class_methods($plugin),
							function ($m) {
								return stripos($m, 'hook') === 0;
							}
						)) . ']'
					);

					continue;
				}

				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin` WHERE `id_hook` = ' . $pluginhook['id_hook'] . ' AND `id_plugin` = ' . $pluginhook['id_plugin'];
				$this->exec($sql, $result);
			}

		}

		$query = 'SELECT `id_hook_plugin`  FROM `' . _DB_PREFIX_ . 'hook_plugin` ORDER BY `id_hook_plugin` ASC';
		$hookPlugins = Db::getInstance()->executeS($query);
		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_hook_plugin`) + 1')
				->from('hook_plugin')
		);

		foreach ($hookPlugins as $hook) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET `id_hook_plugin` = ' . $maxIndex . ' WHERE `id_hook_plugin` = ' . $hook['id_hook_plugin'];
			$this->exec($sql, $result);

			$maxIndex++;

		}

		$query = 'SELECT `id_hook_plugin`  FROM `' . _DB_PREFIX_ . 'hook_plugin` ORDER BY `id_hook_plugin` ASC';
		$hookPlugins = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($hookPlugins as $hook) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET `id_hook_plugin` = ' . $i . ' WHERE `id_hook_plugin` = ' . $hook['id_hook_plugin'];
			$this->exec($sql, $result);

			$i++;

		}


		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('PLUGIN_HOOK_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanPluginsWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanPlugins() {

		$ok = $this->transactional('cleanPlugins', function () {
			return $this->cleanPluginsWork();
		}, ['plugin' => 'id_plugin']);

		if (!$ok) {
			return false;
		}

		// Hors transaction, et seulement si la renumerotation a abouti : la
		// reinitialisation des plugins fait du DDL, qui romprait la transaction.
		// En simulation elle se contente de se declarer, sans rien executer.
		$result = true;
		$this->resetPlugin($result);

		return (bool) $result;
	}

	protected function cleanPluginsWork() {

		$result = true;

		// ─── Purge des lignes satellites orphelines, AVANT toute renumerotation ───
		//
		// La premiere passe ne deplace que les identifiants PRESENTS dans la
		// table plugin. Une ligne satellite qui reference un plugin supprime
		// garde donc son identifiant bas — et entre en collision quand la
		// seconde passe redescend les autres vers 1..n.
		//
		// Constate le 2026-07-30 :
		//   UPDATE eph_plugin_access SET id_plugin = 14 WHERE id_plugin = 80
		// echouait sur la cle primaire composite (id_profile, id_plugin), une
		// ligne occupant deja le couple vise.
		//
		// L'ancien code masquait le probleme en supprimant la cle primaire le
		// temps de l'operation : il ne corrigeait rien, il FABRIQUAIT des
		// doublons en silence. On supprime plutot les orphelins, ce qui est le
		// seul traitement honnete — et c'est dans la transaction, donc annulable.
		//
		// id_plugin > 0 : certaines tables, payment_mode notamment, portent des
		// lignes sans plugin rattache qui sont parfaitement legitimes.
		$satellites = [
			'hook_plugin',
			'hook_plugin_exceptions',
			'plugin_access',
			'plugin_group',
		];

		if ($this->ephenyx_shop_active) {
			$satellites[] = 'plugin_carrier';
			$satellites[] = 'plugin_country';
			$satellites[] = 'plugin_currency';
			$satellites[] = 'payment_mode';
		}

		foreach ($satellites as $satellite) {

			if (!$this->tableExists($satellite)) {
				continue;
			}

			$orphans = (int) Db::getInstance()->getValue(
				'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . bqSQL($satellite) . '` s
				 WHERE s.`id_plugin` > 0
				   AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'plugin` p WHERE p.`id_plugin` = s.`id_plugin`)'
			);

			if ($orphans === 0) {
				continue;
			}

			if ($this->dryRun) {
				$this->plan($satellite, 'PURGE ORPHELINS', $orphans . ' ligne(s) de « ' . $satellite . ' » désignent un plugin disparu');
				continue;
			}

			$this->exec(
				'DELETE s FROM `' . _DB_PREFIX_ . bqSQL($satellite) . '` s
				 WHERE s.`id_plugin` > 0
				   AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'plugin` p WHERE p.`id_plugin` = s.`id_plugin`)',
				$result
			);
		}

		$query = 'SELECT id_plugin  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY id_plugin ASC';
		$plugs = Db::getInstance()->executeS($query);
		$maxIndex = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
			(new DbQuery())
				->select('MAX(`id_plugin`) + 1')
				->from('plugin')
		);

		foreach ($plugs as $plugin) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin` SET id_plugin = ' . $maxIndex . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET id_plugin = ' . $maxIndex . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin_exceptions` SET id_plugin = ' . $maxIndex . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_access` SET id_plugin = ' . $maxIndex . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_group` SET id_plugin = ' . $maxIndex . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);

			if ($this->ephenyx_shop_active) {
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_carrier` SET id_plugin = ' . $maxIndex . ' WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_country` SET id_plugin = ' . $maxIndex . ' WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_currency` SET id_plugin = ' . $maxIndex . '  WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'payment_mode` SET id_plugin = ' . $maxIndex . '  WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
			}

			$maxIndex++;

		}

		// ── Ordre impose aux plugins socles ────────────────────────────────────
		//
		// Cinq plugins declarent « $this->removable = false » : ils ne peuvent
		// jamais etre retires, et tout le reste s'appuie sur eux. Leur ordre de
		// chargement compte, et la simple position en base ne le garantit pas —
		// une renumerotation les eparpillait au gre de leur position courante.
		//
		// L'ordre ci-dessous est un CHOIX, il ne se deduit d'aucune donnee :
		// ph_manager d'abord parce qu'il porte l'infrastructure, puis les trois
		// obligatoires, puis revslider.
		$query = 'SELECT id_plugin, name  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY position ASC';
		$plugins = Db::getInstance()->executeS($query);
		$plugins = $this->sortCorePluginsFirst($plugins);
		$i = 1;

		foreach ($plugins as $plugin) {

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin` SET id_plugin = ' . $i . ', position = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_access` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin_exceptions` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_group` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
			$this->exec($sql, $result);

			if ($this->ephenyx_shop_active) {
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_carrier` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_country` SET id_plugin = ' . $i . ' WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'plugin_currency` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
				$sql = 'UPDATE `' . _DB_PREFIX_ . 'payment_mode` SET id_plugin = ' . $i . '  WHERE id_plugin = ' . $plugin['id_plugin'];
				$this->exec($sql, $result);
			}

			$i++;

		}


		// ⚠️ resetPlugin() N'EST PLUS APPELE ICI.
		//
		// Reinitialiser un plugin cree des tables et des onglets — donc du DDL,
		// donc un COMMIT implicite en MySQL. Lance a l'interieur de la
		// transaction, il l'aurait rompue en plein milieu et rendu la protection
		// illusoire, ce qui est pire que pas de protection du tout puisqu'on
		// croirait etre couvert.
		//
		// La renumerotation est donc validee d'abord, et la reinitialisation se
		// fait apres, hors transaction — voir cleanPlugins().

		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('PLUGIN_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	/**
	 * Ordre des plugins socles, celui dans lequel ils doivent etre numerotes.
	 *
	 * Ce sont les cinq qui declarent « $this->removable = false » : ils ne
	 * peuvent jamais etre desinstalles. Verifie sur le depot le 2026-07-30.
	 *
	 * ⚠️ Cette liste doit rester en accord avec les declarations removable des
	 * plugins. sortCorePluginsFirst() s'en assure a chaque passage et journalise
	 * tout ecart, plutot que de laisser un nouveau plugin socle glisser
	 * silencieusement en fin de liste.
	 */
	const CORE_PLUGIN_ORDER = [
		'ph_manager',
		'ph_upgrader',
		'ph_blockcms',
		'ph_link',
		'revslider',
	];

	/**
	 * Remonte les plugins socles en tete, dans l'ordre impose.
	 *
	 * Les autres conservent leur ordre d'entree — celui de leur position.
	 *
	 * @param array $plugins Lignes portant au moins « name »
	 *
	 * @return array
	 */
	public function sortCorePluginsFirst(array $plugins) {

		$head = [];
		$tail = [];
		$byName = [];

		foreach ($plugins as $plugin) {
			$name = isset($plugin['name']) ? (string) $plugin['name'] : '';

			if ($name !== '' && in_array($name, self::CORE_PLUGIN_ORDER, true)) {
				$byName[$name] = $plugin;
			} else {
				$tail[] = $plugin;
			}

		}

		// On suit l'ordre de la constante, pas celui de la base : c'est tout
		// l'objet de la manoeuvre. Un socle absent de la base est simplement
		// saute — il n'est peut-etre pas installe sur ce site.
		foreach (self::CORE_PLUGIN_ORDER as $name) {

			if (isset($byName[$name])) {
				$head[] = $byName[$name];
			}

		}

		// Filet : un plugin non desinstallable qui ne figure pas dans la
		// constante se retrouverait relegue en fin de liste sans que personne
		// le remarque. On le signale.
		foreach ($tail as $plugin) {
			$name = isset($plugin['name']) ? (string) $plugin['name'] : '';

			if ($name === '') {
				continue;
			}

			$file = _EPH_PLUGIN_DIR_ . $name . '/' . $name . '.php';

			if (!@is_file($file)) {
				continue;
			}

			$src = @file_get_contents($file);

			if ($src !== false && preg_match('/\$this->removable\s*=\s*false/', $src)) {
				PhenyxLogger::addLog(
					'PhenyxTools::sortCorePluginsFirst — le plugin « ' . $name . ' » se declare non desinstallable '
					. 'mais ne figure pas dans CORE_PLUGIN_ORDER : son rang n\'est pas garanti.',
					2
				);
			}

		}

		return array_merge($head, $tail);
	}

	public function resetPlugin(&$result = true) {

		$query = 'SELECT *  FROM `' . _DB_PREFIX_ . 'plugin` ORDER BY id_plugin ASC';
		$plugins = Db::getInstance()->executeS($query);

		foreach ($plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				require_once _EPH_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php';
			} else

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php')) {
				require_once _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin['name'] . '/' . $plugin['name'] . '.php';
			}

			// La reinitialisation d'un plugin est l'action la plus lourde de tout
			// l'ecran : ancres reenregistrees, onglets reinstalles, topics
			// resemes. En simulation on se contente de la nommer.
			if ($this->dryRun) {
				$this->plan('plugin', 'REINITIALISATION', 'Plugin « ' . $plugin['name'] . ' »');
				continue;
			}

			if (class_exists($plugin['name'], false)) {

				$tmpPlugin = Adapter_ServiceLocator::get($plugin['name']);

				if (method_exists($tmpPlugin, 'reset')) {

					$plugin = Plugin::getInstanceByName($plugin['name']);

					try {

						$result &= $plugin->reset();

					} catch (PhenyxException $e) {

						PhenyxLogger::addLog("Plugin reset error for :" . $plugin['name'] . " " . $e->getMessage(), 4);

					}

				}

			}

		}

		if ($this->context->cache_enable && is_object($this->context->cache_api)) {
			$this->context->cache_api->cleanCache();
		}

		// NE PAS détruire la session ici : resetPlugin() est appelé depuis
		// cleanPlugins() via une requête AJAX admin. Détruire la session
		// déconnectait l'employé en cours et faisait échouer la suite du flux.
		// Le vidage de cache ci-dessus suffit à rafraîchir l'état des plugins.

		return $result;

	}

	/**
	 * Enveloppe transactionnelle — le corps est dans cleanHookWork().
	 * Voir transactional() pour le raisonnement.
	 */
	public function cleanHook() {

		return $this->transactional('cleanHook', function () {
			return $this->cleanHookWork();
		}, ['hook' => 'id_hook']);
	}

	protected function cleanHookWork() {

		$result = true;

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions`  ORDER BY id_hook ASC';

		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin_exceptions` WHERE id_hook = ' . $hook['id_hook'];

				$this->exec($sql, $result);
			}

		}

		$query = 'SELECT DISTINCT(id_hook)  FROM `' . _DB_PREFIX_ . 'hook_plugin`  ORDER BY id_hook ASC';

		$hooks = Db::getInstance()->executeS($query);

		foreach ($hooks as $hook) {
			$parent = Db::getInstance()->getValue(
				(new DbQuery())
					->select('`id_hook`')
					->from('hook')
					->where('`id_hook` = ' . (int) $hook['id_hook'])
			);

			if (!$parent) {
				$sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_plugin` WHERE id_hook = ' . $hook['id_hook'];

				$this->exec($sql, $result);
			}

		}

		$query = 'SELECT *  FROM `' . _DB_PREFIX_ . 'hook` ORDER BY id_hook ASC';

		$hooks = Db::getInstance()->executeS($query);

		$i = 1;

		foreach ($hooks as $hook) {
			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];

			$this->exec($sql, $result);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin_exceptions` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];

			$this->exec($sql, $result);

			$sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin` SET id_hook = ' . $i . ' WHERE id_hook = ' . $hook['id_hook'];
			$this->exec($sql, $result);
			$i++;

		}

		// ⚠️ Un ALTER TABLE subsistait ici, sous une forme que mon retrait du
		// 2026-07-30 n'avait pas vue : l'affectation et l'execution etaient sur
		// deux lignes distinctes, alors que je ne cherchais que la forme en une
		// seule.
		//
		// Il faisait doublon avec syncAutoIncrement(), appele apres validation —
		// et surtout il rompait la transaction, puisqu'en MySQL tout ALTER TABLE
		// valide implicitement. Une operation de 1 700 ecritures se retrouvait
		// donc coupee en deux, la seconde moitie sans protection.
		//
		// Le recalage du compteur est desormais fait par l'enveloppe.

		if ($result) {
			// En simulation on n'horodate pas : sinon l'analyse ferait croire
			// a une maintenance faite, et l'execution reelle serait sautee.
			if (!$this->dryRun) {
				$this->context->phenyxConfig->updateValue('HOOK_MAINTENANCE', date("Y-m-d"));
			}
		}

		return $result;

	}

	public function exportLang($iso, $theme, $plugins) {

		if ($iso && $theme) {

			$items = array_flip(Language::getFilesList($iso, $theme, false, false, false, false, false));
			$plugins = array_flip($this->getPluginFilesList($iso, $theme, $plugins));
			$fileName = _EPH_TRANSLATIONS_DIR_ . '/export/' . $iso . '.gzip';
			$gz = new Archive_Tar($fileName, true);
			$gz->createModify($items, null, _SHOP_ROOT_DIR_);
			$gz->addModify($plugins, null, _EPH_ROOT_DIR_ . '/includes');

			$pathFile = _EPH_ROOT_DIR_ . '/packs/' . _EPH_VERSION_ . '/' . $iso . '/' . $iso . '.gzip';
			copy($fileName, $pathFile);

		} else {

			$this->errors[] = $this->la('Please select a language and a theme.');
		}

		if (count($this->errors)) {
			$result = [
				'success' => false,
				'message' => implode(PHP_EOL, $this->errors),
			];
		} else {

			$result = [
				'success' => true,
				'message' => $this->la('Language has been exported successfully'),
			];
		}

		die(Tools::jsonEncode($result));
	}

	public function getPluginFilesList($isoFrom, $themeFrom, $plugins) {

		$filesPlugins = [];
		$number = 0;

		foreach ($plugins as $mod) {

			$modDir = null;

			if (is_dir(_EPH_PLUGIN_DIR_ . $mod)) {
				$modDir = _EPH_PLUGIN_DIR_ . $mod;
			} else if (is_dir(_EPH_SPECIFIC_PLUGIN_DIR_ . $mod)) {
				$modDir = _EPH_SPECIFIC_PLUGIN_DIR_ . $mod;
			}

			if (!$modDir) {
				continue;
			}

			// Legacy flat lang file

			if (file_exists($modDir . '/translations/' . (string) $isoFrom . '.php')) {
				$filesPlugins[$modDir . '/translations/' . (string) $isoFrom . '.php'] = ++$number;
			}

			// Per-domain lang files (admin, class, front, mail, pdf): each is independent and must all be exported

			foreach (['admin', 'class', 'front', 'mail', 'pdf'] as $domain) {

				$domainFile = $modDir . '/translations/' . (string) $isoFrom . '/' . $domain . '.php';

				if (file_exists($domainFile)) {
					$filesPlugins[$domainFile] = ++$number;
				}

			}

			

		}

		return $filesPlugins;
	}

	/**
	 * @deprecated Refactor 2026-05 — l'agrégation pré-page des traductions n'est
	 * plus utile : Translate.php charge maintenant les fichiers de chaque plugin
	 * à la demande avec un cache mémoire process-wide. La méthode est conservée
	 * en no-op pour ne pas casser un éventuel appelant externe (modules tiers).
	 *
	 * L'ancien corps (~300 lignes d'agrégation + écriture de fichiers .php) a
	 * été retiré ici — voir l'historique git pour la version complète.
	 *
	 * À supprimer définitivement quand on aura confirmé qu'aucun module externe
	 * ne l'appelle (recherche full-tree sur "mergeLanguages" + audit de quelques
	 * mois en prod sans incident).
	 */
	public function mergeLanguages() {

		return true;
	}

	protected function _legacy_mergeLanguages_DO_NOT_USE() {

		$iso = $this->context->language->iso_code;
		$_plugins = $this->getPlugins();

		$_LANGAD = [];

		if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/admin.php')) {
			@include _EPH_TRANSLATIONS_DIR_ . $iso . '/admin.php';
			$_LANGAD = $_LANGADM;
		}

		$toInsert = [];

		if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/admin.php')) {

			@include _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/admin.php';

			if (isset($_LANGOVADM) && is_array($_LANGOVADM)) {
				$_LANGAD = array_merge(
					$_LANGAD,
					$_LANGOVADM
				);
			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/admin.php')) {

				@include _EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/admin.php';

				if (is_array($_LANGADM)) {
					$_LANGAD = array_merge(
						$_LANGAD,
						$_LANGADM
					);
				}

			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/admin.php')) {

				@include _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/admin.php';

				if (is_array($_LANGADM)) {
					$_LANGAD = array_merge(
						$_LANGAD,
						$_LANGADM
					);
				}

			}

		}

		$toInsert = $_LANGAD;
		ksort($toInsert);
		$file = fopen(_EPH_TRANSLATIONS_DIR_ . $iso . '/admin.php', "w");
		fwrite($file, "<?php\n\nglobal \$_LANGADM;\n\n");
		fwrite($file, "\$_LANGADM = [];\n");

		foreach ($toInsert as $key => $value) {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
			fwrite($file, '$_LANGADM[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");
		}

		fwrite($file, "\n" . 'return $_LANGADM;' . "\n");
		fclose($file);
		$_LANGCLAS = [];

		if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/class.php')) {
			@include _EPH_TRANSLATIONS_DIR_ . $iso . '/class.php';
			$_LANGCLAS = $_LANGCLASS;
		}

		$toInsert = [];

		if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/class.php')) {

			@include _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/class.php';

			if (isset($_LANGOVCLASS) && is_array($_LANGOVCLASS)) {
				$_LANGCLAS = array_merge(
					$_LANGCLAS,
					$_LANGOVCLASS
				);
			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/class.php')) {
				require_once _EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/class.php';

				if (is_array($_LANGCLASS)) {
					$_LANGCLAS = array_merge(
						$_LANGCLAS,
						$_LANGCLASS
					);
				}

			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/class.php')) {
				require_once _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/class.php';

				if (is_array($_LANGCLASS)) {
					$_LANGCLAS = array_merge(
						$_LANGCLAS,
						$_LANGCLASS
					);
				}

			}

		}

		$toInsert = $_LANGCLAS;
		ksort($toInsert);
		$file = fopen(_EPH_TRANSLATIONS_DIR_ . $iso . '/class.php', "w");
		fwrite($file, "<?php\n\nglobal \$_LANGCLASS;\n\n");
		fwrite($file, "\$_LANGCLASS = [];\n");

		foreach ($toInsert as $key => $value) {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
			fwrite($file, '$_LANGCLASS[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");
		}

		fwrite($file, "\n" . 'return $_LANGCLASS;' . "\n");
		fclose($file);

		$_LANGFRON = [];

		if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/front.php')) {
			@include _EPH_TRANSLATIONS_DIR_ . $iso . '/front.php';
			$_LANGFRON = $_LANGFRONT;
		}

		$toInsert = [];

		if (file_exists(_EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php')) {

			@include _EPH_OVERRIDE_TRANSLATIONS_DIR_ . $iso . '/front.php';

			if (isset($_LANGOVFRONT) && is_array($_LANGOVFRONT)) {
				$_LANGFRON = array_merge(
					$_LANGFRON,
					$_LANGOVFRONT
				);
			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/front.php')) {

				require_once _EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/front.php';

				if (is_array($_LANGFRONT)) {
					$_LANGFRON = array_merge(
						$_LANGFRON,
						$_LANGFRONT
					);
				}

			}

		}

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/front.php')) {

				require_once _EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/front.php';

				if (is_array($_LANGFRONT)) {
					$_LANGFRON = array_merge(
						$_LANGFRON,
						$_LANGFRONT
					);
				}

			}

		}

		$toInsert = $_LANGFRON;
		ksort($toInsert);
		$file = fopen(_EPH_TRANSLATIONS_DIR_ . $iso . '/front.php', "w");
		fwrite($file, "<?php\n\nglobal \$_LANGFRONT;\n\n");
		fwrite($file, "\$_LANGFRONT = [];\n");

		foreach ($toInsert as $key => $value) {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
			fwrite($file, '$_LANGFRONT[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");
		}

		fwrite($file, "\n" . 'return $_LANGFRONT;' . "\n");
		fclose($file);

		$_LANGMAI = [];

		if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/mail.php')) {
			@include _EPH_TRANSLATIONS_DIR_ . $iso . '/mail.php';
			$_LANGMAI = $_LANGMAIL;
		}

		$toInsert = [];

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/mail.php')) {

				@include _EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/mail.php';

				if (is_array($_LANGMAIL)) {
					$_LANGMAI = array_merge(
						$_LANGMAI,
						$_LANGMAIL
					);
				}

			}

		}

		$toInsert = $_LANGMAI;
		ksort($toInsert);
		$file = fopen(_EPH_TRANSLATIONS_DIR_ . $iso . '/mail.php', "w");
		fwrite($file, "<?php\n\nglobal \$_LANGMAIL;\n\n");
		fwrite($file, "\$_LANGMAIL = [];\n");

		foreach ($toInsert as $key => $value) {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
			fwrite($file, '$_LANGMAIL[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");
		}

		fwrite($file, "\n" . 'return $_LANGMAIL;' . "\n");
		fclose($file);

		$_LANGPD = [];

		if (file_exists(_EPH_TRANSLATIONS_DIR_ . $iso . '/pdf.php')) {
			@include _EPH_TRANSLATIONS_DIR_ . $iso . '/pdf.php';
			$_LANGPD = $_LANGPDF;
		}

		$toInsert = [];

		foreach ($_plugins as $plugin) {

			if (file_exists(_EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/pdf.php')) {

				@include _EPH_PLUGIN_DIR_ . $plugin . DIRECTORY_SEPARATOR . 'translations/' . $iso . '/pdf.php';

				if (is_array($_LANGPDF)) {
					$_LANGPD = array_merge(
						$_LANGPD,
						$_LANGPDF
					);
				}

			}

		}

		$toInsert = $_LANGPD;
		ksort($toInsert);
		$file = fopen(_EPH_TRANSLATIONS_DIR_ . $iso . '/pdf.php', "w");
		fwrite($file, "<?php\n\nglobal \$_LANGPDF;\n\n");
		fwrite($file, "\$_LANGPDF = [];\n");

		foreach ($toInsert as $key => $value) {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);

			fwrite($file, '$_LANGPDF[\'' . translateSQL($key, true) . '\'] = \'' . translateSQL($value, true) . '\';' . "\n");
		}

		fwrite($file, "\n" . 'return $_LANGPDF;' . "\n");
		fclose($file);

		$this->context->translations = new Translate($iso, $this->context->company);
		$this->context->phenyxConfig->updateValue('CURENT_MERGE_LANG_' . $this->context->language->iso_code, 1);

		return true;

	}

	public function getPlugins() {

		$plugs = [];
		$plugins = Plugin::getPluginsDirOnDisk();

		foreach ($plugins as $plugin) {

			if (Plugin::isInstalled($plugin)) {

				if (is_dir(_EPH_PLUGIN_DIR_ . $plugin . '/translations/' . $this->context->language->iso_code)) {
					$plugs[] = $plugin;
				} else

				if (is_dir(_EPH_SPECIFIC_PLUGIN_DIR_ . $plugin . '/translations/' . $this->context->language->iso_code)) {
					$plugs[] = $plugin;
				}

			}

		}

		return $plugs;
	}
    
    public function getIoFiles($content, $destination) {
        
        $path = dirname($destination);
        if(!is_dir(_EPH_ROOT_DIR_.$path)) {
            mkdir(_EPH_ROOT_DIR_.$path);
        }
                
        return file_put_contents(_EPH_ROOT_DIR_.$destination, $content);
    }

}
	
