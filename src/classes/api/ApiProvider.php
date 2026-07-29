<?php

namespace EphenyxDigital\QuantumCore;

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
