<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Cache persistant des reponses d'API externes.
 *
 * Volontairement en base plutot que dans le cache volatil : les reponses
 * survivent aux redemarrages, et surtout elles laissent une TRACE. Si un client
 * conteste son classement en tarif professionnel, on peut montrer ce que le
 * repertoire des entreprises repondait le jour de son inscription.
 *
 * Toutes les methodes echouent en silence. Un cache qui tombe ne doit jamais
 * empecher un appel de partir : au pire on interroge l'API pour rien.
 *
 * @since 1.0.0 (couche API externe)
 */
class ApiCache {

    /**
     * Empreinte stable d'une requete.
     *
     * Les parametres sont TRIES avant hachage : sans cela, deux appels
     * identiques ecrits dans un ordre different produiraient deux entrees.
     *
     * @param string $path
     * @param array  $query
     *
     * @return string
     */
    public static function hash($path, ?array $query = null) {

        $query = $query ?: [];
        ksort($query);

        return sha1((string) $path . '?' . http_build_query($query));
    }

    /**
     * Lit une reponse encore valide.
     *
     * @param string $providerCode
     * @param string $hash
     *
     * @return array|null Le contenu decode, ou null si absent/expire/illisible
     */
    public static function get($providerCode, $hash) {

        try {
            $row = Db::getInstance()->getRow(
                'SELECT `response` FROM `' . _DB_PREFIX_ . 'api_cache`
                 WHERE `provider_code` = \'' . pSQL($providerCode) . '\'
                   AND `request_hash` = \'' . pSQL($hash) . '\'
                   AND `date_expire` > NOW()'
            );
        } catch (\Throwable $e) {
            return null;
        }

        if (empty($row) || !isset($row['response']) || $row['response'] === null) {
            return null;
        }

        $decoded = json_decode($row['response'], true);

        // Une entree illisible vaut une absence : on la laisse expirer seule
        // plutot que de la supprimer, pour ne pas transformer une lecture en
        // ecriture au milieu d'un parcours client.
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Enregistre une reponse.
     *
     * @param string $providerCode
     * @param string $hash
     * @param array  $payload
     * @param int    $ttl        Duree de vie en secondes ; 0 ou moins = on n'ecrit rien
     * @param int    $httpCode
     * @param string $summary    Forme lisible de la requete
     *
     * @return bool
     */
    public static function set($providerCode, $hash, ?array $payload = null, $ttl = 0, $httpCode = 0, $summary = '') {

        $ttl = (int) $ttl;

        if ($ttl <= 0 || $payload === null) {
            return false;
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            return false;
        }

        try {
            // Toutes les colonnes NOT NULL sont fournies : un TEXT NOT NULL sans
            // valeur par defaut ferait echouer l'INSERT en mode strict.
            return (bool) Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'api_cache`
                 (`provider_code`, `request_hash`, `request_summary`, `http_code`, `response`, `date_add`, `date_expire`)
                 VALUES (
                    \'' . pSQL($providerCode) . '\',
                    \'' . pSQL($hash) . '\',
                    \'' . pSQL(Tools::substr((string) $summary, 0, 255)) . '\',
                    ' . (int) $httpCode . ',
                    \'' . pSQL($encoded, true) . '\',
                    NOW(),
                    DATE_ADD(NOW(), INTERVAL ' . $ttl . ' SECOND)
                 )
                 ON DUPLICATE KEY UPDATE
                    `request_summary` = VALUES(`request_summary`),
                    `http_code`       = VALUES(`http_code`),
                    `response`        = VALUES(`response`),
                    `date_add`        = VALUES(`date_add`),
                    `date_expire`     = VALUES(`date_expire`)'
            );
        } catch (\Throwable $e) {
            return false;
        }

    }

    /**
     * Purge les entrees expirees.
     *
     * A appeler depuis une tache planifiee, pas depuis un parcours client.
     *
     * @param string|null $providerCode Limiter a un fournisseur
     *
     * @return int Nombre de lignes supprimees, 0 en cas d'echec
     */
    public static function purge($providerCode = null) {

        $where = '`date_expire` <= NOW()';

        if ($providerCode !== null && $providerCode !== '') {
            $where .= ' AND `provider_code` = \'' . pSQL($providerCode) . '\'';
        }

        try {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'api_cache` WHERE ' . $where);

            return (int) Db::getInstance()->Affected_Rows();
        } catch (\Throwable $e) {
            return 0;
        }

    }

    /**
     * Oublie une entree precise, pour forcer une nouvelle interrogation.
     *
     * @param string $providerCode
     * @param string $hash
     *
     * @return bool
     */
    public static function forget($providerCode, $hash) {

        try {
            return (bool) Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'api_cache`
                 WHERE `provider_code` = \'' . pSQL($providerCode) . '\'
                   AND `request_hash` = \'' . pSQL($hash) . '\''
            );
        } catch (\Throwable $e) {
            return false;
        }

    }

}
