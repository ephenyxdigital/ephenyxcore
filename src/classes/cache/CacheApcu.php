<?php

namespace EphenyxDigital\EphenyxCore;

use APCUIterator;


/**
 * Our Cache API class
 *
 * @package CacheAPI
 */
class CacheApcu extends CacheApi implements CacheApiInterface {

	/**
	 * {@inheritDoc}
	 */
	public function isSupported($test = false) {

		$supported = function_exists('apcu_fetch') && function_exists('apcu_store');

		if ($test) {
			return $supported;
		}

		return parent::isSupported() && $supported;
	}

	/**
	 * {@inheritDoc}
	 */
	public function connect() {

		return true;
	}

	protected function _set($key, $value, $ttl = 0) {

		return $this->putData($key, $value, $ttl);
	}

	protected function _get($key) {

		return $this->getData($key);
	}

	protected function _exists($key) {

		return (bool) $this->_get($key);
	}

	/**
	 * Sans objet pour APCu, qui n a pas de registre de clefs a tenir.
	 *
	 * Le corps precedent lisait `$this->keys`, propriete declaree nulle part
	 * — ni ici ni dans `CacheApi` : une propriete dynamique, depreciee depuis
	 * PHP 8.2. Et il ecrivait sous `$this->prefix`, que `putData()` prefixait
	 * une seconde fois. La methode reste, le contrat de `CacheApi` l exige.
	 */
	protected function _writeKeys() {

		return true;
	}

	public function getApcuValues() {

		ini_set('memory_limit', '-1');
		$result = [];

		// Le motif precedent etait un accent circonflexe suivi d'un point
		// echappe et d'une etoile : « debut, puis zero ou plus POINTS ». Zero
		// convenant, il acceptait tout. APCu etant une memoire partagee par
		// POOL PHP-FPM, cette methode rendait donc aussi les clefs des autres
		// sites du pool, que le str_replace() ci-dessous laissait telles
		// quelles faute de porter le bon prefixe. On filtre desormais sur le
		// prefixe d'instance.
		//
		// (Et ce commentaire est en // : ecrire la regex dans un bloc /* */
		//  refermait le commentaire sur sa propre etoile-slash.)

		foreach (new APCUIterator('/^' . preg_quote($this->prefix, '/') . '/') as $counter) {
			$result[str_replace($this->prefix, '', $counter['key'])] = Validate::isJSON($counter['value']) ? Tools::jsonDecode($counter['value'], true) : $counter['value'];
		}

		ksort($result);
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData($key, $ttl = null) {

		// Garde-fou : si l'extension APCu n'est pas chargée (ex. après un
		// changement de version PHP), on ne plante pas — on retombe sur la DB.
		if (!function_exists('apcu_fetch')) {
			return null;
		}

		$key = $this->prefix . strtr($key, ':/', '-_');

		$value = apcu_fetch($key);

		return !empty($value) ? $value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function putData($key, $value, $ttl = null) {

		if (!function_exists('apcu_store')) {
			return false;
		}

		$key = $this->prefix . strtr($key, ':/', '-_');
		return apcu_store($key, $value, $ttl !== null ? $ttl : $this->ttl);

	}

	/**
	 * Purge toutes les clefs commencant par $key.
	 *
	 * ═══ CETTE METHODE NE PURGEAIT RIEN ═══
	 *
	 * Elle appelait `apcu_delete($key . '*')`. `apcu_delete()` n a jamais
	 * accepte de joker : il cherchait donc une clef nommee litteralement
	 * « routesMeta_* », ne la trouvait pas, et rendait false. Le parcours par
	 * prefixe se fait avec `APCUIterator` — dont l import etait deja en tete
	 * de fichier, l intention y etait.
	 *
	 * Second oubli : ni le prefixe d instance ni la sanitisation. APCu est
	 * une memoire partagee par POOL PHP-FPM, et `getData()`/`putData()`
	 * composent la clef reelle avec `$this->prefix . strtr($key, ':/', '-_')`.
	 * Sans les reprendre a l identique, on ne vise pas les memes clefs que
	 * celles qui ont ete ecrites.
	 *
	 * @param string $key Prefixe de clef, sans le prefixe d instance.
	 * @return bool
	 */
	public function cleanByStartingKey($key) {

		if (!class_exists('APCUIterator') || !function_exists('apcu_delete')) {
			return false;
		}

		ini_set('memory_limit', '-1');

		$debut = $this->prefix . strtr($key, ':/', '-_');
		$result = true;

		foreach (new APCUIterator('/^' . preg_quote($debut, '/') . '/', APC_ITER_KEY) as $entree) {

			if (!apcu_delete($entree['key'])) {
				$result = false;
			}

		}

		return $result;
	}

	/**
	 * Supprime une clef.
	 *
	 * Deux corrections. La composition d abord : la clef etait supprimee sous
	 * `$this->prefix . $key`, SANS le `strtr(':/', '-_')` que `putData()`
	 * applique a l ecriture — toute clef portant `:` ou `/`, et il y en a
	 * (les noms de classe pleinement qualifies en portent), n etait donc
	 * jamais supprimee.
	 *
	 * Le garde ensuite : `_exists()` vaut `(bool) _get()`, et `getData()`
	 * rend null sur une valeur vide. Une clef stockee a 0, '' ou false
	 * passait pour absente et survivait a sa propre suppression. Le garde ne
	 * servait a rien de toute facon : `apcu_delete()` sur une clef absente
	 * rend false sans rien casser.
	 */
	public function removeData($key) {

		if (!function_exists('apcu_delete')) {
			return false;
		}

		return apcu_delete($this->prefix . strtr($key, ':/', '-_'));
	}

	protected function _delete($key) {

		return apcu_delete($key);
	}

	public function flush() {

		return (bool) $this->cleanCache();
	}

	/**
	 * {@inheritDoc}
	 */
	public function cleanCache($type = '') {

		return apcu_clear_cache();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion() {

		return phpversion('apcu');
	}

}

?>