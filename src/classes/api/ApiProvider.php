<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Configuration d'un fournisseur d'API externe.
 *
 * Une ligne par API. La classe ne fait AUCUN appel reseau : elle ne porte que
 * les reglages. Les appels sont l'affaire de ExternalApi et de ses classes
 * filles.
 *
 * Le decoupage est volontaire. Un employe doit pouvoir couper une API depuis
 * le back-office quand elle repond mal, sans qu'on ait a redeployer du code
 * sur les quinze sites qui partagent le paquet vendor.
 *
 * @since 1.0.0 (couche API externe)
 */
class ApiProvider extends PhenyxObjectModel {

    /** Aucune authentification : API ouverte. */
    const AUTH_NONE = 'none';
    /** Cle passee en parametre d'URL, sous le nom auth_name. */
    const AUTH_QUERY = 'query';
    /** Cle passee en en-tete HTTP, sous le nom auth_name. */
    const AUTH_HEADER = 'header';
    /** Cle passee en « Authorization: Bearer <cle> ». */
    const AUTH_BEARER = 'bearer';

    public $code;
    public $name;
    public $base_url;
    public $auth_type = self::AUTH_NONE;
    public $auth_name;
    public $auth_key;
    public $connect_timeout = 3;
    public $timeout = 5;
    public $cache_ttl = 86400;
    public $rate_limit = 0;
    public $fail_open = 1;
    public $active = 0;
    public $date_add;
    public $date_upd;

    /** @var array<string,ApiProvider|false> memo par requete */
    protected static $byCode = [];

    public static $definition = [
        'table'   => 'api_provider',
        'primary' => 'id_api_provider',
        'fields'  => [
            'code'            => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 64],
            'name'            => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 128],
            'base_url'        => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'auth_type'       => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 16],
            'auth_name'       => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 64],
            'auth_key'        => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'connect_timeout' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'timeout'         => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'cache_ttl'       => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'rate_limit'      => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'fail_open'       => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active'          => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add'        => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'date_upd'        => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    /**
     * Charge un fournisseur par son code technique.
     *
     * Rend null si le code est inconnu — jamais d'exception : l'appelant est
     * souvent en plein parcours client, et une API absente ne doit pas faire
     * tomber une inscription.
     *
     * @param string $code
     *
     * @return ApiProvider|null
     */
    public static function getByCode($code) {

        $code = (string) $code;

        if ($code === '') {
            return null;
        }

        if (array_key_exists($code, static::$byCode)) {
            return static::$byCode[$code] ?: null;
        }

        $id = 0;

        try {
            $id = (int) Db::getInstance()->getValue(
                'SELECT `id_api_provider` FROM `' . _DB_PREFIX_ . 'api_provider` WHERE `code` = \'' . pSQL($code) . '\''
            );
        } catch (\Throwable $e) {
            // Table absente (SQL pas encore joue sur ce site) : on se tait et
            // on rend null. Le fournisseur sera simplement considere eteint.
            static::$byCode[$code] = false;

            return null;
        }

        if ($id <= 0) {
            static::$byCode[$code] = false;

            return null;
        }

        $provider = new static($id);

        if (!Validate::isLoadedObject($provider)) {
            static::$byCode[$code] = false;

            return null;
        }

        static::$byCode[$code] = $provider;

        return $provider;
    }

    /**
     * Cree la ligne d'un fournisseur si elle n'existe pas — et JAMAIS deux fois.
     *
     * ─── POURQUOI EN PHP, ET PAS DANS UN install.sql ───
     *
     * Un plugin qui a besoin d'une API veut poser sa ligne dans api_provider a
     * l'installation. Trois raisons interdisent de le faire en SQL :
     *
     *  1. `Plugin::installsql()` decoupe le fichier avec
     *     `preg_split("/;\s*[\r\n]+/", $sql)`. N'IMPORTE QUEL point-virgule
     *     suivi d'un saut de ligne coupe la requete — y compris dans un
     *     commentaire `--` ou dans une chaine. Un INSERT commente se retrouve
     *     scinde en fragments invalides, et l'installation echoue sans dire ou.
     *
     *  2. `installsql()` n'examine jamais le retour de `Db::execute()` : une
     *     requete refusee passe inapercue et `install()` rend quand meme true.
     *
     *  3. Deux plugins peuvent avoir besoin du MEME fournisseur — `bic` sert
     *     a ph_salesforce comme a ph_sepa. Le second installe ne doit ni creer
     *     un doublon, ni ecraser la cle saisie par l'exploitant apres le
     *     premier.
     *
     * ─── CE QUE LA METHODE FAIT, ET SURTOUT CE QU'ELLE NE FAIT PAS ───
     *
     * Si la ligne existe, elle n'est PAS remplacee : `auth_key` et `active`
     * sont des reglages d'exploitation, pas des donnees de plugin. Un
     * reinstall ne doit jamais eteindre une API allumee ni effacer une cle.
     * Seule `base_url` est rafraichie — c'est la convention deja retenue par
     * `sql/api_layer.sql` (`ON DUPLICATE KEY UPDATE base_url = VALUES(...)`),
     * pour qu'un changement d'adresse arrive par mise a jour du code.
     *
     * A la creation, la ligne est posee ETEINTE (`active = 0`), meme si les
     * defauts disent le contraire. Une API allumee sans cle valide est pire
     * qu'une API absente : `create()` rend un objet utilisable qui echoue a
     * chaque appel, la ou `null` fait proprement basculer l'appelant vers son
     * repli. C'est a l'exploitant d'allumer, une fois la cle saisie.
     *
     * Rend null sans bruit si la table n'existe pas — le SQL de la couche API
     * n'a pas ete joue sur tous les sites, et cela ne doit pas empecher un
     * plugin de s'installer.
     *
     * @param string $code     Code technique du fournisseur
     * @param array  $defaults Colonnes a poser a la creation
     *
     * @return ApiProvider|null
     */
    public static function ensure($code, ?array $defaults = null) {

        $code = (string) $code;

        if ($code === '') {
            return null;
        }

        $defaults = $defaults ?: [];
        $existing = static::getByCode($code);

        if ($existing !== null) {

            if (!empty($defaults['base_url']) && $defaults['base_url'] !== $existing->base_url) {

                try {
                    $existing->base_url = (string) $defaults['base_url'];
                    $existing->update();
                } catch (\Throwable $e) {
                    // Sans consequence : l'ancienne adresse reste en place.
                }

            }

            return $existing;
        }

        try {
            $provider = new static();

            foreach (['code', 'name', 'base_url', 'auth_type', 'auth_name', 'auth_key',
                'connect_timeout', 'timeout', 'cache_ttl', 'rate_limit', 'fail_open'] as $field) {

                if (array_key_exists($field, $defaults)) {
                    $provider->{$field} = $defaults[$field];
                }

            }

            $provider->code = $code;
            $provider->active = 0;

            if (!$provider->add()) {
                return null;
            }

        } catch (\Throwable $e) {
            // Table absente, ou colonne inconnue : le plugin s'installe quand
            // meme, l'API sera simplement consideree eteinte.
            return null;
        }

        // getByCode() a memorise « absent » juste avant la creation.
        static::resetCache();

        return $provider;
    }

    /**
     * Le fournisseur est-il utilisable ?
     *
     * @return bool
     */
    public function isUsable() {

        return (bool) $this->active && !empty($this->base_url);
    }

    /**
     * Reglages d'authentification a appliquer a une requete.
     *
     * Rend deux tableaux : les parametres a ajouter a l'URL, et les en-tetes.
     * Les classes filles n'ont pas a connaitre le detail.
     *
     * @return array{query: array<string,string>, headers: array<int,string>}
     */
    public function authParts() {

        $query = [];
        $headers = [];

        if (empty($this->auth_key)) {
            return ['query' => $query, 'headers' => $headers];
        }

        switch ($this->auth_type) {
        case self::AUTH_QUERY:

            if (!empty($this->auth_name)) {
                $query[$this->auth_name] = $this->auth_key;
            }

            break;
        case self::AUTH_HEADER:

            if (!empty($this->auth_name)) {
                $headers[] = $this->auth_name . ': ' . $this->auth_key;
            }

            break;
        case self::AUTH_BEARER:
            $headers[] = 'Authorization: Bearer ' . $this->auth_key;
            break;
        }

        return ['query' => $query, 'headers' => $headers];
    }

    /**
     * Vide le memo de requete. Utile apres une ecriture depuis le back-office.
     */
    public static function resetCache() {

        static::$byCode = [];
    }

}
