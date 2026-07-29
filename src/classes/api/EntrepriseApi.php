<?php

namespace EphenyxDigital\QuantumCore;

/**
 * Repertoire des entreprises francaises (Etalab / data.gouv.fr).
 *
 * API OUVERTE : ni compte ni cle. Verifie le 2026-07-29 sur
 * recherche-entreprises.api.gouv.fr. Elle rend la raison sociale, l'adresse,
 * l'etat administratif de l'etablissement et le numero de TVA
 * intracommunautaire — ce dernier est directement utile a la facturation BtoB.
 *
 * ─── CE QU'ELLE PROUVE, ET CE QU'ELLE NE PROUVE PAS ───
 *
 * Le controle de Luhn, deja present dans Validate::isSiret(), verifie qu'un
 * numero est BIEN FORME. Fabriquer un numero qui le satisfait prend trente
 * secondes ; il ne prouve donc rien contre une saisie de mauvaise foi.
 *
 * Cette API repond a l'autre question : l'etablissement EXISTE-T-IL, et est-il
 * encore ouvert ? C'est ce qui separe une faute de frappe d'un faux numero.
 *
 * Elle ne dit en revanche rien de la personne qui saisit : rien ne garantit que
 * le client soit lie a l'entreprise dont il donne le SIRET. Pour un enjeu
 * tarifaire important, la validation manuelle reste le seul controle serieux.
 *
 * @since 1.0.0 (couche API externe)
 */
class EntrepriseApi extends ExternalApi {

    const CODE = 'entreprise';

    /** Numero bien forme et etablissement ouvert. */
    const STATUS_OK = 'ok';
    /** Numero bien forme mais inconnu du repertoire. */
    const STATUS_UNKNOWN = 'unknown';
    /** Etablissement connu mais administrativement ferme. */
    const STATUS_CLOSED = 'closed';
    /** Numero mal forme : ni 14 chiffres, ni cle de controle valide. */
    const STATUS_MALFORMED = 'malformed';
    /** API injoignable : on ne sait pas. A traiter en validation manuelle. */
    const STATUS_UNAVAILABLE = 'unavailable';

    /**
     * Ne garde que les chiffres.
     *
     * Les clients ecrivent leur SIRET avec des espaces, c'est meme la forme
     * officielle : « 441 639 465 00091 ».
     *
     * @param string $siret
     *
     * @return string
     */
    public static function normalize($siret) {

        return preg_replace('/\D+/', '', (string) $siret);
    }

    /**
     * Le numero est-il bien forme ?
     *
     * S'appuie sur Validate::isSiret() (14 chiffres + cle de Luhn), en ajoutant
     * l'exception de La Poste : ses etablissements, sous le SIREN 356000000, ne
     * satisfont pas Luhn. La regle admise est que la somme des chiffres doit
     * etre un multiple de 5. Sans cela, tout etablissement de La Poste serait
     * refuse.
     *
     * @param string $siret
     *
     * @return bool
     */
    public static function isWellFormed($siret) {

        $siret = static::normalize($siret);

        if (strlen($siret) !== 14) {
            return false;
        }

        if (strpos($siret, '356000000') === 0) {
            $sum = 0;

            for ($i = 0; $i < 14; $i++) {
                $sum += (int) $siret[$i];
            }

            return ($sum % 5) === 0;
        }

        return Validate::isSiret($siret);
    }

    /**
     * Verdict complet sur un SIRET.
     *
     * Rend toujours un tableau, jamais null : l'appelant doit pouvoir traiter
     * chaque cas sans verifier d'abord si l'objet existe.
     *
     * @param string $siret
     *
     * @return array{status: string, siret: string, etablissement: array|null, message: string}
     */
    public function check($siret) {

        $siret = static::normalize($siret);

        if (!static::isWellFormed($siret)) {
            return [
                'status'        => self::STATUS_MALFORMED,
                'siret'         => $siret,
                'etablissement' => null,
                'message'       => 'Numero incomplet ou cle de controle incorrecte',
            ];
        }

        $etablissement = $this->findBySiret($siret);

        // null distingue « API muette » de « non trouve » : findBySiret rend un
        // tableau vide dans le second cas.
        if ($etablissement === null) {
            return [
                'status'        => self::STATUS_UNAVAILABLE,
                'siret'         => $siret,
                'etablissement' => null,
                'message'       => (string) $this->lastError(),
            ];
        }

        if ($etablissement === []) {
            return [
                'status'        => self::STATUS_UNKNOWN,
                'siret'         => $siret,
                'etablissement' => null,
                'message'       => 'Aucun etablissement ne porte ce numero',
            ];
        }

        if (empty($etablissement['actif'])) {
            return [
                'status'        => self::STATUS_CLOSED,
                'siret'         => $siret,
                'etablissement' => $etablissement,
                'message'       => 'Etablissement administrativement ferme',
            ];
        }

        return [
            'status'        => self::STATUS_OK,
            'siret'         => $siret,
            'etablissement' => $etablissement,
            'message'       => '',
        ];
    }

    /**
     * Cherche un etablissement par son SIRET.
     *
     * @param string $siret
     *
     * @return array|null Tableau normalise ; [] si introuvable ; null si l'API n'a pas repondu
     */
    public function findBySiret($siret) {

        $siret = static::normalize($siret);

        if (strlen($siret) !== 14) {
            return [];
        }

        $payload = $this->get('/search', ['q' => $siret, 'per_page' => 1]);

        if ($payload === null) {
            return null;
        }

        $results = isset($payload['results']) && is_array($payload['results']) ? $payload['results'] : [];

        if (empty($results)) {
            return [];
        }

        $entreprise = $results[0];

        // L'etablissement recherche peut apparaitre a deux endroits selon la
        // requete : dans matching_etablissements, ou comme siege. On regarde les
        // deux, et on n'accepte que si le SIRET correspond EXACTEMENT — l'API
        // fait de la recherche approchee et pourrait rendre un voisin.
        $etab = null;

        if (isset($entreprise['matching_etablissements']) && is_array($entreprise['matching_etablissements'])) {

            foreach ($entreprise['matching_etablissements'] as $candidat) {

                if (isset($candidat['siret']) && (string) $candidat['siret'] === $siret) {
                    $etab = $candidat;
                    break;
                }

            }

        }

        if ($etab === null
            && isset($entreprise['siege']['siret'])
            && (string) $entreprise['siege']['siret'] === $siret) {
            $etab = $entreprise['siege'];
        }

        if ($etab === null) {
            return [];
        }

        return $this->normalizeEtablissement($entreprise, $etab);
    }

    /**
     * Reduit la reponse a ce dont le reste du logiciel a besoin.
     *
     * L'API rend beaucoup — dirigeants, finances, conventions collectives. On
     * n'en garde que l'utile : ce qui n'est pas extrait ici ne sera pas non plus
     * conserve en cache, et c'est tres bien ainsi.
     *
     * @param array $entreprise
     * @param array $etab
     *
     * @return array
     */
    protected function normalizeEtablissement(array $entreprise, array $etab) {

        $tva = null;

        if (isset($entreprise['tva']) && is_array($entreprise['tva']) && !empty($entreprise['tva'])) {
            $tva = (string) reset($entreprise['tva']);
        }

        $etat = isset($etab['etat_administratif']) ? (string) $etab['etat_administratif'] : '';

        return [
            'siret'          => isset($etab['siret']) ? (string) $etab['siret'] : '',
            'siren'          => isset($entreprise['siren']) ? (string) $entreprise['siren'] : '',
            'nom'            => isset($entreprise['nom_raison_sociale']) && $entreprise['nom_raison_sociale'] !== ''
            ? (string) $entreprise['nom_raison_sociale']
            : (isset($entreprise['nom_complet']) ? (string) $entreprise['nom_complet'] : ''),
            'enseigne'       => isset($etab['nom_commercial']) ? (string) $etab['nom_commercial'] : '',
            'adresse'        => isset($etab['adresse']) ? (string) $etab['adresse'] : '',
            'code_postal'    => isset($etab['code_postal']) ? (string) $etab['code_postal'] : '',
            'commune'        => isset($etab['libelle_commune']) ? (string) $etab['libelle_commune'] : '',
            'siege'          => !empty($etab['est_siege']),
            // « A » actif, « C » cesse. Toute autre valeur est traitee comme
            // fermee : mieux vaut demander une validation manuelle que d'ouvrir
            // des tarifs professionnels sur un etat qu'on ne sait pas lire.
            'actif'          => ($etat === 'A'),
            'etat'           => $etat,
            'activite'       => isset($etab['activite_principale']) ? (string) $etab['activite_principale'] : '',
            'date_creation'  => isset($etab['date_creation']) ? (string) $etab['date_creation'] : '',
            'tva'            => $tva,
            'date_reponse'   => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Cherche une entreprise par son SIREN (9 chiffres).
     *
     * @param string $siren
     *
     * @return array|null
     */
    public function findBySiren($siren) {

        $siren = static::normalize($siren);

        if (strlen($siren) !== 9) {
            return [];
        }

        $payload = $this->get('/search', ['q' => $siren, 'per_page' => 1]);

        if ($payload === null) {
            return null;
        }

        $results = isset($payload['results']) && is_array($payload['results']) ? $payload['results'] : [];

        if (empty($results) || !isset($results[0]['siren']) || (string) $results[0]['siren'] !== $siren) {
            return [];
        }

        $entreprise = $results[0];
        $siege = isset($entreprise['siege']) && is_array($entreprise['siege']) ? $entreprise['siege'] : [];

        return $this->normalizeEtablissement($entreprise, $siege);
    }

}
