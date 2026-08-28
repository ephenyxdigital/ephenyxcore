<?php

namespace EphenyxDigital\EphenyxCore;


interface CacheApiInterface {
	/**
	 * Checks whether we can use the cache method performed by this API.
	 *
	 * @access public
	 * @param bool $test Test if this is supported or enabled.
	 * @return bool Whether or not the cache is supported
	 */
	public function isSupported($test = false);

	/**
	 * Connects to the cache method. This defines our $key. If this fails, we return false, otherwise we return true.
	 *
	 * @access public
	 * @return bool Whether or not the cache method was connected to.
	 */
	public function connect();

	/**
	 * Retrieves an item from the cache.
	 *
	 * @access public
	 * @param string $key The key to use, the prefix is applied to the key name.
	 * @param int    $ttl Overrides the default TTL. Not really used anymore,
	 *                    but is kept for backwards compatibility.
	 * @return mixed The result from the cache, if there is no data or it is invalid, we return null.
	 * @todo Seperate existence checking into its own method
	 */
	public function getData($key, $ttl = null);

	/**
	 * Stores a value, regardless of whether or not the key already exists (in
	 * which case it will overwrite the existing value for that key).
	 *
	 * @access public
	 * @param string $key   The key to use, the prefix is applied to the key name.
	 * @param mixed  $value The data we wish to save. Use null to delete.
	 * @param int    $ttl   How long (in seconds) the data should be cached for.
	 *                      The default TTL will be used if this is null.
	 * @return bool Whether or not we could save this to the cache.
	 * @todo Seperate deletion into its own method
	 */
	public function putData($key, $value, $ttl = null);

	/**
	 * Clean out the cache.
	 *
	 * @param string $type If supported, the type of cache to clear, blank/data or user.
	 * @return bool Whether or not we could clean the cache.
	 */
	public function cleanCache($type = '');

	/**
	 * Purge toutes les clefs commencant par $key.
	 *
	 * Au contrat parce que le socle l appelle sans condition — `Meta::add()`,
	 * `update()` et `delete()` sur `metaGetPages_`. `CacheApi` en donne une
	 * implementation par defaut qui journalise au lieu de purger : un backend
	 * tiers qui etend `CacheApi` reste donc valide sans rien ecrire.
	 */
	public function cleanByStartingKey($key);

	/**
	 * Supprime une clef. Meme raison, meme filet dans `CacheApi`.
	 */
	public function removeData($key);

	/**
	 * Gets the class identifier of the current caching API implementation.
	 *
	 * @access public
	 * @return string the unique identifier for the current class implementation.
	 */
	public function getImplementationClassKeyName();
}

?>



