<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Resolution du BIC a partir d'un IBAN (ibanapi.com).
 *
 * ─── POURQUOI UNE API, ET PAS UNE TABLE ───
 *
 * Le BIC n'est pas contenu dans l'IBAN : il ne se calcule pas, il se resout.
 * Ce qu'on peut extraire d'un IBAN francais, c'est le code banque (CIB) — cinq
 * chiffres, positions 5 a 9. Le passage du CIB au BIC demande ensuite une table
 * de correspondance, et cette table n'est pas librement disponible :
 *
 *   - le Fichier des Implantations Bancaires de la Banque de France, qui est la
 *     source officielle, s'obtient sur demande ;
 *   - les listes qui circulent librement (annuaires GitHub et consorts) portent
 *     raison sociale, ville et BIC — pas le code banque. Elles ne repondent donc
 *     PAS a la question posee ici.
 *
 * Et la correspondance n'est de toute facon pas 1:1 : Credit Agricole, Banque
 * Populaire et Caisse d'Epargne ont un BIC par caisse regionale, distingue par
 * le code guichet et non par le code banque. Une table simplifiee rendrait le
 * BIC du siege, pas celui de l'agence — une erreur silencieuse, donc la pire.
 *
 * ─── CE QUI EST DELIBERE ───
 *
 *  1. LA CLE MOD-97 EST VERIFIEE AVANT TOUT APPEL RESEAU. Elle est gratuite et
 *     hors ligne, et elle ecarte l'essentiel des fautes de frappe. Meme logique
 *     que la cle de Luhn avant l'appel SIRET dans EntrepriseApi : on ne paie pas
 *     un credit d'API pour apprendre qu'un chiffre a saute.
 *
 *  2. LA CLE RIB EST VERIFIEE AUSSI, pour les IBAN francais. Elle controle la
 *     coherence interne banque/guichet/compte, et attrape des transpositions
 *     qu'un IBAN reconstruit a la main satisferait.
 *
 *  3. RIEN N'EST BLOQUANT. Depuis le reglement SEPA, un virement en zone euro
 *     se fait a l'IBAN seul : le BIC est un confort, pas une exigence. Un BIC
 *     absent n'est donc jamais un motif de refus — l'appelant recoit un statut
 *     et decide, et le champ doit rester saisissable a la main.
 *
 *  4. AUCUNE DONNEE PERSONNELLE N'EST CONSERVEE. La reponse porte l'adresse et
 *     le telephone de l'agence : on n'en garde que le nom de banque, le BIC et
 *     la ville. Ce qui n'est pas repris dans normalizeBank() ne va pas non plus
 *     en cache.
 *
 * ─── LE PIEGE DES CODES D'ERREUR ───
 *
 * ibanapi.com aligne son statut HTTP sur son champ `result` : un IBAN refuse
 * rend un 4xx, pas un 200 avec un corps explicatif. ExternalApi::execute()
 * traite tout 4xx comme un echec et rend null — on perdrait la distinction
 * entre « le service n'a pas repondu » et « le service dit non ». D'ou la
 * lecture de lastHttpCode() dans check() : c'est la seule facon de retrouver
 * l'information sans modifier le socle.
 *
 * Ligne a poser dans api_provider :
 *   code=bic, base_url=https://api.ibanapi.com/v1,
 *   auth_type=query, auth_name=api_key, auth_key=<votre cle>
 *
 * @since 1.0.0 (couche API externe)
 */
class BicApi extends ExternalApi {

    const CODE = 'bic';

    /** IBAN valide et BIC trouve. */
    const STATUS_OK = 'ok';
    /** IBAN valide, mais le repertoire ne rend pas de BIC pour cette banque. */
    const STATUS_UNKNOWN = 'unknown';
    /** Longueur, alphabet ou cle de controle incorrects. Rien n'est parti sur le reseau. */
    const STATUS_MALFORMED = 'malformed';
    /** Service muet, quota epuise, cle invalide. On ne sait pas. */
    const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Longueur officielle de l'IBAN par pays (registre IBAN).
     *
     * Volontairement limitee a ce qui est verifie : un pays absent de cette
     * table n'est pas refuse, il est seulement controle sur la fourchette
     * 15-34 et la cle mod-97. Mieux vaut laisser passer un IBAN etranger
     * exotique que refuser un client parce que la table n'a pas ete tenue.
     *
     * @var array<string,int>
     */
    protected static $lengths = [
        'AD' => 24, 'AT' => 20, 'BE' => 16, 'BG' => 22, 'CH' => 21, 'CY' => 28,
        'CZ' => 24, 'DE' => 22, 'DK' => 18, 'EE' => 20, 'ES' => 24, 'FI' => 18,
        'FR' => 27, 'GB' => 22, 'GI' => 23, 'GR' => 27, 'HR' => 21, 'HU' => 28,
        'IE' => 22, 'IS' => 26, 'IT' => 27, 'LI' => 21, 'LT' => 20, 'LU' => 20,
        'LV' => 21, 'MC' => 27, 'MT' => 31, 'NL' => 18, 'NO' => 15, 'PL' => 28,
        'PT' => 25, 'RO' => 24, 'SE' => 24, 'SI' => 19, 'SK' => 24, 'SM' => 27,
        'VA' => 22,
        /* Collectivites d'outre-mer : meme format que la metropole. */
        'BL' => 27, 'GF' => 27, 'GP' => 27, 'MF' => 27, 'MQ' => 27, 'NC' => 27,
        'PF' => 27, 'PM' => 27, 'RE' => 27, 'TF' => 27, 'WF' => 27, 'YT' => 27,
    ];

    /**
     * Pose la ligne api_provider du service, si elle manque.
     *
     * A appeler depuis le `install()` de chaque plugin qui a besoin du BIC —
     * aujourd'hui ph_salesforce et ph_sepa. La description du fournisseur vit
     * ICI, dans la classe qui l'utilise, et pas recopiee dans chaque plugin :
     * deux copies divergent, et c'est l'adresse de l'API qui en fait les frais.
     *
     * `ApiProvider::ensure()` garantit qu'une ligne existante n'est ni
     * dupliquee ni ecrasee : le second plugin installe ne touche pas a la cle
     * saisie apres le premier.
     *
     * @return ApiProvider|null
     */
    public static function ensureProvider() {

        return ApiProvider::ensure(self::CODE, [
            'name'            => 'IBAN API — resolution BIC',
            'base_url'        => 'https://api.ibanapi.com/v1',
            'auth_type'       => ApiProvider::AUTH_QUERY,
            'auth_name'       => 'api_key',
            'auth_key'        => '',
            'connect_timeout' => 3,
            'timeout'         => 5,

            /* Volontairement sans cache. ApiCache indexe par md5 de la requete
               et recopie celle-ci en clair dans request_summary — or la requete
               CONTIENT l'IBAN. BicApi memorise a sa place, a la maille banque
               plus guichet, dans app/cache/bic_branch_map.json : c'est de cela
               que depend le BIC, jamais du numero de compte. */
            'cache_ttl'       => 0,

            'rate_limit'      => 0,
            'fail_open'       => 1,
        ]);
    }

    /**
     * Majuscules, sans espaces ni ponctuation.
     *
     * Les IBAN se transmettent par groupes de quatre — c'est meme la forme
     * imprimee sur les RIB : « FR76 3000 6000 0112 3456 7890 189 ».
     *
     * @param string $iban
     *
     * @return string
     */
    public static function normalize($iban) {

        return preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $iban));
    }

    /**
     * Code pays de l'IBAN, ou chaine vide.
     *
     * @param string $iban
     *
     * @return string
     */
    public static function country($iban) {

        $iban = static::normalize($iban);

        return preg_match('/^[A-Z]{2}/', $iban) ? substr($iban, 0, 2) : '';
    }

    /**
     * L'IBAN est-il bien forme ?
     *
     * Trois controles : l'alphabet et la longueur du pays, puis la cle mod-97
     * de la norme ISO 13616, puis — pour la France seulement — la cle RIB.
     *
     * @param string $iban
     *
     * @return bool
     */
    public static function isWellFormed($iban) {

        $iban = static::normalize($iban);

        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban)) {
            return false;
        }

        $country = substr($iban, 0, 2);

        if (isset(static::$lengths[$country])) {

            if (strlen($iban) !== static::$lengths[$country]) {
                return false;
            }

        } else if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        if (!static::checksumMatches($iban)) {
            return false;
        }

        if ($country === 'FR' && !static::ribKeyMatches($iban)) {
            return false;
        }

        return true;
    }

    /**
     * Cle de controle ISO 13616 : les quatre premiers caracteres passent a la
     * fin, chaque lettre devient sa position dans l'alphabet + 9, et le nombre
     * obtenu doit valoir 1 modulo 97.
     *
     * Le modulo est calcule par tranches : l'IBAN fait jusqu'a 34 caracteres,
     * soit un entier de plus de 40 chiffres — hors de portee d'un int PHP, et
     * on ne veut pas dependre de bcmath ni de gmp, qui ne sont pas garantis.
     *
     * @param string $iban Deja normalise
     *
     * @return bool
     */
    protected static function checksumMatches($iban) {

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $digits = '';

        for ($i = 0, $len = strlen($rearranged); $i < $len; $i++) {
            $char = $rearranged[$i];
            $digits .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        return static::mod97($digits) === 1;
    }

    /**
     * Modulo 97 d'un entier donne sous forme de chaine.
     *
     * @param string $number Chiffres uniquement
     *
     * @return int
     */
    protected static function mod97($number) {

        $remainder = 0;

        for ($i = 0, $len = strlen($number); $i < $len; $i++) {
            $remainder = ($remainder * 10 + (int) $number[$i]) % 97;
        }

        return $remainder;
    }

    /**
     * Cle RIB francaise : 97 - ((89 x banque + 15 x guichet + 3 x compte) mod 97).
     *
     * Ce controle fait doublon avec la cle mod-97 pour une saisie honnete, mais
     * pas pour un IBAN reconstruit : on peut recalculer la cle IBAN apres avoir
     * transpose deux chiffres du compte, on ne peut pas recalculer les deux.
     *
     * Les numeros de compte anciens contiennent des lettres, converties selon la
     * table interbancaire (A,J → 1 ; B,K,S → 2 ; ... ; I,R,Z → 9).
     *
     * @param string $iban Deja normalise, suppose francais
     *
     * @return bool
     */
    protected static function ribKeyMatches($iban) {

        if (strlen($iban) !== 27) {
            return false;
        }

        $bank = substr($iban, 4, 5);
        $branch = substr($iban, 9, 5);
        $account = substr($iban, 14, 11);
        $key = (int) substr($iban, 25, 2);

        $converted = '';

        for ($i = 0; $i < 11; $i++) {
            $char = $account[$i];

            if (ctype_digit($char)) {
                $converted .= $char;

                continue;
            }

            if (!ctype_alpha($char)) {
                return false;
            }

            // A=1..I=9, J=1..R=9, S=2..Z=9 : c'est la table interbancaire.
            $rank = ord($char) - 64;
            $converted .= (string) ((($rank - 1) % 9) + 1);
        }

        $sum = static::mod97($bank) * 89
            + static::mod97($branch) * 15
            + static::mod97($converted) * 3;

        return (97 - ($sum % 97)) === $key;
    }

    /**
     * Code banque (CIB) d'un IBAN francais, ou chaine vide.
     *
     * @param string $iban
     *
     * @return string
     */
    public static function bankCode($iban) {

        $iban = static::normalize($iban);

        return (substr($iban, 0, 2) === 'FR' && strlen($iban) === 27) ? substr($iban, 4, 5) : '';
    }

    /**
     * Code guichet d'un IBAN francais, ou chaine vide.
     *
     * @param string $iban
     *
     * @return string
     */
    public static function branchCode($iban) {

        $iban = static::normalize($iban);

        return (substr($iban, 0, 2) === 'FR' && strlen($iban) === 27) ? substr($iban, 9, 5) : '';
    }

    /**
     * Verdict complet sur un IBAN.
     *
     * Rend toujours un tableau, jamais null : l'appelant doit pouvoir traiter
     * chaque cas sans verifier d'abord si l'objet existe.
     *
     * @param string $iban
     *
     * @return array{status: string, iban: string, bank_code: string, branch_code: string, bank: array|null, message: string}
     */
    public function check($iban) {

        $iban = static::normalize($iban);

        $verdict = [
            'status'      => self::STATUS_MALFORMED,
            'iban'        => $iban,
            'bank_code'   => static::bankCode($iban),
            'branch_code' => static::branchCode($iban),
            'bank'        => null,
            'message'     => '',
        ];

        if (!static::isWellFormed($iban)) {
            $verdict['message'] = 'IBAN incomplet ou cle de controle incorrecte';

            return $verdict;
        }

        $bank = $this->findByIban($iban);

        // null distingue « service muet » de « pas de BIC » : findByIban rend un
        // tableau vide dans le second cas.
        if ($bank === null) {

            /*
             * Le service aligne son statut HTTP sur son champ `result`. Un 4xx
             * autre que 401/403/429 est donc un refus PORTANT SUR L'IBAN, pas
             * une panne — et comme notre cle mod-97 est deja passee, la lecture
             * honnete est « le repertoire ne connait pas ce compte », pas « le
             * numero est mal forme ».
             */
            $code = $this->lastHttpCode();

            if ($code >= 400 && $code < 500 && !in_array($code, [401, 403, 408, 429], true)) {
                $verdict['status'] = self::STATUS_UNKNOWN;
                $verdict['message'] = 'Aucune banque connue pour cet IBAN';

                return $verdict;
            }

            $verdict['status'] = self::STATUS_UNAVAILABLE;
            $verdict['message'] = (string) $this->lastError();

            return $verdict;
        }

        if ($bank === [] || empty($bank['bic'])) {
            $verdict['status'] = self::STATUS_UNKNOWN;
            $verdict['message'] = 'IBAN valide, mais aucun BIC au repertoire';

            return $verdict;
        }

        $verdict['status'] = self::STATUS_OK;
        $verdict['bank'] = $bank;

        return $verdict;
    }

    /**
     * Interroge le repertoire.
     *
     * @param string $iban
     *
     * @return array|null Tableau normalise ; [] si sans BIC ; null si le service n'a pas repondu
     */
    public function findByIban($iban) {

        $iban = static::normalize($iban);

        if ($iban === '') {
            return [];
        }

        $bank = static::bankCode($iban);
        $branch = static::branchCode($iban);

        // Table locale d'abord. Elle repond sans reseau et sans credit.
        $known = static::recall($bank, $branch);

        if ($known !== null) {
            $this->lastFromCache = true;

            return $known;
        }

        $payload = $this->get('/validate/' . rawurlencode($iban));

        if ($payload === null) {
            return null;
        }

        if (!isset($payload['data']) || !is_array($payload['data'])) {
            return [];
        }

        $result = $this->normalizeBank($payload['data']);

        if ($result !== []) {
            static::remember($bank, $branch, $result);
        }

        return $result;
    }

    /* ======================================================================
     *   Table locale banque + guichet → BIC
     * ======================================================================
     *
     * ── POURQUOI ELLE EXISTE ──
     *
     * Deux raisons, et la premiere est la plus importante.
     *
     *  1. UN IBAN N'A RIEN A FAIRE DANS UN CACHE. ExternalApi::get() ecrit la
     *     requete en clair dans api_cache.request_summary, et l'empreinte est
     *     un simple md5 : pour une donnee de paiement dont l'espace est petit
     *     — banque, guichet et onze chiffres — ce n'est pas une anonymisation.
     *     On ne memorise donc PAS par IBAN. On pose `cache_ttl = 0` sur la
     *     ligne api_provider, et on memorise ici, a la maille qui convient.
     *
     *  2. LE BIC NE DEPEND PAS DU COMPTE. Il depend de la banque et du guichet.
     *     Deux agents de la meme agence donnent le meme BIC : le second appel
     *     serait un credit depense pour une reponse deja connue.
     *
     * Effet de bord heureux : on reconstruit peu a peu la table CIB+guichet →
     * BIC qu'on ne pouvait pas telecharger, et elle se remplit exactement des
     * banques que vos agents utilisent vraiment.
     *
     * Le fichier est un simple JSON : pas de table SQL a deployer, et une
     * suppression suffit a repartir de zero. Toutes les ecritures sont gardees
     * par @ et par un try/catch — un cache indisponible ne doit jamais faire
     * echouer une saisie de RIB.
     */

    /** Au-dela, on cesse d'ajouter : le fichier est relu en entier a chaque appel. */
    const MAP_MAX_ENTRIES = 5000;

    /**
     * @return string
     */
    protected static function mapFile() {

        $dir = defined('_EPH_CACHE_DIR_') ? _EPH_CACHE_DIR_ : sys_get_temp_dir() . '/';

        return rtrim($dir, '/') . '/bic_branch_map.json';
    }

    /**
     * @return array<string,array>
     */
    protected static function loadMap() {

        static $map = null;

        if ($map !== null) {
            return $map;
        }

        $map = [];
        $file = static::mapFile();

        try {

            if (@is_readable($file)) {
                $decoded = json_decode((string) @file_get_contents($file), true);
                $map = is_array($decoded) ? $decoded : [];
            }

        } catch (\Throwable $e) {
            $map = [];
        }

        return $map;
    }

    /**
     * Le couple banque/guichet est-il deja connu ?
     *
     * @param string $bank
     * @param string $branch
     *
     * @return array|null
     */
    protected static function recall($bank, $branch) {

        if ($bank === '' || $branch === '') {
            return null;
        }

        $map = static::loadMap();
        $key = $bank . $branch;

        return isset($map[$key]) && is_array($map[$key]) ? $map[$key] : null;
    }

    /**
     * Retient un couple banque/guichet.
     *
     * @param string $bank
     * @param string $branch
     * @param array  $bic
     *
     * @return void
     */
    protected static function remember($bank, $branch, array $bic) {

        if ($bank === '' || $branch === '' || empty($bic['bic'])) {
            return;
        }

        $map = static::loadMap();
        $key = $bank . $branch;

        if (isset($map[$key]) || count($map) >= static::MAP_MAX_ENTRIES) {
            return;
        }

        $map[$key] = $bic;

        try {
            @file_put_contents(
                static::mapFile(),
                json_encode($map, JSON_UNESCAPED_UNICODE),
                LOCK_EX
            );
        } catch (\Throwable $e) {
            // Cache non ecrit : on repayera un credit la prochaine fois. Sans
            // gravite, et surtout sans consequence pour l'agent qui saisit.
            return;
        }

    }

    /**
     * Resume de la requete ecrit en cache — REDIGE.
     *
     * Filet de securite : si un exploitant remet un `cache_ttl` sur la ligne
     * api_provider, l'IBAN ne doit toujours pas se retrouver en clair dans
     * api_cache.request_summary.
     *
     * @param string $path
     * @param array  $query
     *
     * @return string
     */
    protected function cacheSummary($path, ?array $query = null) {

        return '/validate/[IBAN redige]';
    }

    /**
     * Reduit la reponse a ce dont le reste du logiciel a besoin.
     *
     * Le service rend l'adresse postale et le telephone de l'agence. Rien de
     * tout cela ne sert a payer un agent : ce qui n'est pas extrait ici ne sera
     * pas non plus conserve en cache, et c'est tres bien ainsi.
     *
     * @param array $data
     *
     * @return array
     */
    protected function normalizeBank(array $data) {

        $bank = isset($data['bank']) && is_array($data['bank']) ? $data['bank'] : [];

        $bic = isset($bank['bic']) ? strtoupper(trim((string) $bank['bic'])) : '';

        if ($bic === '') {
            return [];
        }

        return [
            'bic'       => $bic,
            'name'      => isset($bank['bank_name']) ? (string) $bank['bank_name'] : '',
            'city'      => isset($bank['city']) ? (string) $bank['city'] : '',
            'country'   => isset($data['country_code']) ? (string) $data['country_code'] : '',
            /* Utile a l'appelant : hors SEPA, le BIC redevient obligatoire. */
            'sepa'      => !empty($data['sepa']),
        ];
    }

}
