<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Socle des appels aux API externes.
 *
 * ─── POURQUOI CETTE CLASSE EXISTE ───
 *
 * Sans elle, chaque plugin qui a besoin d'un service exterieur ecrit son propre
 * cURL. On l'a deja vu tourner mal : dans ph_newsletter, une branche entiere
 * n'appelait jamais curl_exec() et les mises a jour de contacts ne partaient
 * pas — sans le moindre message. Tools::file_get_contents(), de son cote,
 * desactive la verification du certificat TLS et ne rend jamais le code de
 * statut : inutilisable comme fondation.
 *
 * ─── LES TROIS REGLES ───
 *
 *  1. ON NE BLOQUE JAMAIS UN PARCOURS CLIENT. Delais courts, et en cas d'echec
 *     l'appelant recoit null. Il decide alors quoi faire — typiquement
 *     basculer vers une validation manuelle. C'est le mode « fail_open », actif
 *     par defaut. Le desactiver revient a laisser un service tiers decider si
 *     vos clients peuvent s'inscrire.
 *
 *  2. AUCUN AVERTISSEMENT PHP NE DOIT FUIR. Le serveur tourne en PHP 8.5 avec
 *     display_errors actif : le texte d'un avertissement s'ecrit dans la sortie,
 *     et s'il tombe au milieu d'une reponse JSON ou d'un script genere, tout
 *     casse. D'ou les @ sur les acces incertains et les Throwable partout.
 *
 *  3. TOUT ECHEC LAISSE UNE TRACE. lastError() pour l'appelant, journal pour
 *     l'exploitant.
 *
 * ─── ECRIRE UN NOUVEAU FOURNISSEUR ───
 *
 *   class MonApi extends ExternalApi {
 *       const CODE = 'mon_api';
 *       public function chercher($x) {
 *           return $this->get('/search', ['q' => $x]);
 *       }
 *   }
 *   $api = MonApi::create();               // null si absent ou eteint
 *   $res = $api ? $api->chercher('x') : null;
 *
 * Il faut aussi une ligne dans api_provider portant le meme code.
 *
 * @since 1.0.0 (couche API externe)
 */
abstract class ExternalApi {

    /** Code du fournisseur dans api_provider. A redefinir dans chaque fille. */
    const CODE = '';

    /** @var ApiProvider */
    protected $provider;

    /** @var string|null Motif du dernier echec */
    protected $lastError;

    /** @var int Code HTTP du dernier appel */
    protected $lastHttpCode = 0;

    /** @var bool La derniere reponse venait-elle du cache ? */
    protected $lastFromCache = false;

    /**
     * @param ApiProvider $provider
     */
    protected function __construct(ApiProvider $provider) {

        $this->provider = $provider;
    }

    /**
     * Fabrique l'instance, ou null si le fournisseur est absent ou eteint.
     *
     * Volontairement nullable : l'appelant DOIT se demander quoi faire quand
     * l'API n'est pas disponible. Une fabrique qui leverait une exception
     * pousserait a l'entourer d'un try/catch vide.
     *
     * @return static|null
     */
    public static function create() {

        if (static::CODE === '') {
            return null;
        }

        $provider = ApiProvider::getByCode(static::CODE);

        if ($provider === null || !$provider->isUsable()) {
            return null;
        }

        return new static($provider);
    }

    /**
     * Le fournisseur laisse-t-il passer en cas de panne ?
     *
     * @return bool
     */
    public function failsOpen() {

        return (bool) $this->provider->fail_open;
    }

    /**
     * @return string|null
     */
    public function lastError() {

        return $this->lastError;
    }

    /**
     * @return int
     */
    public function lastHttpCode() {

        return $this->lastHttpCode;
    }

    /**
     * @return bool
     */
    public function lastFromCache() {

        return $this->lastFromCache;
    }

    /**
     * Appel GET, avec cache.
     *
     * @param string $path  Chemin relatif a base_url, commencant par /
     * @param array  $query Parametres d'URL
     * @param bool   $fresh Ignorer le cache en lecture
     *
     * @return array|null Le corps decode, ou null en cas d'echec
     */
    protected function get($path, ?array $query = null, $fresh = false) {

        $query = $query ?: [];
        $this->lastError = null;
        $this->lastHttpCode = 0;
        $this->lastFromCache = false;

        $ttl = (int) $this->provider->cache_ttl;
        $hash = ApiCache::hash($path, $query);

        if (!$fresh && $ttl > 0) {
            $cached = ApiCache::get(static::CODE, $hash);

            if ($cached !== null) {
                $this->lastFromCache = true;
                $this->lastHttpCode = 200;

                return $cached;
            }

        }

        $auth = $this->provider->authParts();
        $url = rtrim((string) $this->provider->base_url, '/') . '/' . ltrim((string) $path, '/');

        // extraQuery() est ajoute a l'URL mais N'ENTRE PAS dans l'empreinte de
        // cache ni dans le resume : c'est la que passent les cles portees par
        // une classe fille. Une cle ne doit jamais se retrouver en clair dans
        // la table api_cache.
        $allQuery = array_merge($query, $auth['query'], $this->extraQuery());

        if (!empty($allQuery)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($allQuery);
        }

        $payload = $this->execute($url, $auth['headers']);

        if ($payload === null) {
            return null;
        }

        if ($ttl > 0) {
            ApiCache::set(static::CODE, $hash, $payload, $ttl, $this->lastHttpCode, $path . '?' . http_build_query($query));
        }

        return $payload;
    }

    /**
     * Parametres d'URL ajoutes par une classe fille, hors empreinte de cache.
     *
     * Prevu pour les fournisseurs dont la cle ne vient pas d'api_provider —
     * typiquement une cle deja stockee ailleurs en configuration, qu'on ne veut
     * pas faire ressaisir. Voir TranslateApi.
     *
     * @return array<string,string>
     */
    protected function extraQuery() {

        return [];
    }

    /**
     * L'appel HTTP proprement dit.
     *
     * Rend le corps decode, ou null. Renseigne lastError et lastHttpCode.
     *
     * @param string $url
     * @param array  $headers
     *
     * @return array|null
     */
    protected function execute($url, ?array $headers = null) {

        if (!function_exists('curl_init')) {
            return $this->fail('cURL indisponible sur ce serveur');
        }

        $curl = @curl_init();

        if ($curl === false) {
            return $this->fail('curl_init a echoue');
        }

        $headers = $headers ?: [];
        $headers[] = 'Accept: application/json';

        @curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => max(1, (int) $this->provider->connect_timeout),
            CURLOPT_TIMEOUT        => max(1, (int) $this->provider->timeout),
            CURLOPT_FOLLOWLOCATION => false, // une redirection peut sortir du domaine attendu
            CURLOPT_SSL_VERIFYPEER => true,  // jamais desactive : c'est le seul controle d'identite du serveur
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERAGENT      => 'PhenyxDigital/' . (defined('_EPH_VERSION_') ? _EPH_VERSION_ : '1.0'),
        ]);

        $body = @curl_exec($curl);
        $errno = (int) @curl_errno($curl);
        $error = (string) @curl_error($curl);
        $this->lastHttpCode = (int) @curl_getinfo($curl, CURLINFO_HTTP_CODE);
        @curl_close($curl);

        if ($body === false || $errno !== 0) {
            return $this->fail('Appel reseau echoue (' . $errno . ') : ' . $error);
        }

        if ($this->lastHttpCode < 200 || $this->lastHttpCode >= 300) {

            /*
             * ⚠️ LE CORPS DE L'ERREUR ETAIT JETE (2026-08-11).
             *
             * « Reponse HTTP 403 » ne dit rien : une clef invalide, une API
             * non activee, une restriction par referent, une facturation
             * suspendue et un quota epuise rendent TOUS un 403. Or le
             * fournisseur explique lui-meme lequel, dans le corps de sa
             * reponse — que nous mettions a la poubelle.
             *
             * Constate en reel : trente-et-une expressions refusees par Google
             * sans qu'on puisse savoir pourquoi, alors que la reponse portait
             * le motif exact.
             *
             * On n'expose QUE le message : le corps complet peut contenir des
             * echos de la requete, donc la clef.
             */
            $detail = '';
            $erreur = json_decode((string) $body, true);

            if (is_array($erreur)) {

                if (isset($erreur['error']['message'])) {
                    $detail = (string) $erreur['error']['message'];

                    if (isset($erreur['error']['status'])) {
                        $detail = (string) $erreur['error']['status'] . ' — ' . $detail;
                    }

                } else if (isset($erreur['message'])) {
                    $detail = (string) $erreur['message'];
                }

            }

            return $this->fail(
                'Reponse HTTP ' . $this->lastHttpCode
                . ($detail !== '' ? ' : ' . mb_substr($detail, 0, 300) : '')
            );
        }

        // 204 : succes sans contenu. On rend un tableau vide, pas null, pour
        // que l'appelant distingue « rien a dire » de « ca n'a pas marche ».
        if ($this->lastHttpCode === 204 || $body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->fail('Reponse illisible : ' . json_last_error_msg());
        }

        if (!is_array($decoded)) {
            return $this->fail('Reponse inattendue : un tableau etait attendu');
        }

        return $decoded;
    }

    /**
     * Consigne un echec et rend null.
     *
     * @param string $message
     *
     * @return null
     */
    protected function fail($message) {

        $this->lastError = $message;

        try {
            PhenyxLogger::addLog('[API ' . static::CODE . '] ' . $message, 2);
        } catch (\Throwable $e) {
            // Un journal indisponible ne doit pas aggraver la panne qu'il devait
            // decrire. On abandonne silencieusement.
        }

        return null;
    }

}
