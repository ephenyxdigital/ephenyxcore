<?php

namespace EphenyxDigital\EphenyxCore;

/**
 * Traduction automatique par Google Translate (API v2).
 *
 * ─── POURQUOI CETTE CLASSE REMPLACE getGoogleTranslation() ───
 *
 * Deux implementations coexistaient — une dans PhenyxTool, une recopiee dans
 * AdminLanguagesController — et aucune des deux n'avait ni cache ni gestion
 * d'erreur. Consequences :
 *
 *   - CHAQUE APPEL EST FACTURE. Retraduire deux fois la meme chaine se payait
 *     deux fois. Sur une boucle de quarante libelles relancee a chaque essai,
 *     ca chiffre.
 *   - UNE EXCEPTION FAISAIT TOMBER LE LOT. TranslateClient leve sur panne
 *     reseau, quota depasse ou cle invalide. Au milieu d'une boucle, tout
 *     s'arretait, et rien n'etait enregistre.
 *
 * ─── L'ORDRE DE RESOLUTION ───
 *
 *   1. La table `translation`, consultee d'abord. Elle vit dans une base
 *      SEPAREE ET PARTAGEE entre les sites (Db::getCrmInstance) : une chaine
 *      traduite pour un site l'est pour tous, definitivement. C'est le vrai
 *      magasin, et c'est aussi ce que lit Translate::getDbTranslation() en
 *      derniere etape de resolution des libelles.
 *   2. Le cache d'api_cache, court, qui absorbe les repetitions a l'interieur
 *      d'un meme traitement.
 *   3. Google, en dernier recours — et le resultat redescend en 1.
 *
 * ─── FAIL-OPEN ───
 *
 * Un echec rend la chaine SOURCE, jamais null ni vide. Un libelle en anglais
 * vaut infiniment mieux qu'un libelle absent, et c'est le comportement qu'avait
 * deja l'ancienne methode d'AdminLanguagesController.
 *
 * @since 1.0.0 (couche API externe)
 */
class TranslateApi extends ExternalApi {

    const CODE = 'google_translate';

    /** Chemin de l'API v2, relatif a base_url. */
    const ENDPOINT = '/language/translate/v2';

    /** Au-dela, Google refuse la requete. On decoupe les lots. */
    const MAX_BATCH = 100;

    /**
     * Longueur maximale, en octets, de la partie « q=... » d'une requete.
     *
     * ⚠️ C'est la borne qui manquait. MAX_BATCH compte des CHAINES ; celle-ci
     * compte des CARACTERES. Cent mots courts tiennent dans une adresse, dix
     * phrases d'aide n'y tiennent pas — et le refus de Google est silencieux.
     *
     * LA VALEUR N'EST PAS DEVINEE. Le traducteur du wiki
     * (AdminTranslationsController specifique, PHENYXWIKI_API_URL_BUDGET)
     * decoupe deja a 1500 octets, et il fonctionne : quelqu'un avait rencontre
     * la limite de ce cote, l'avait resolue la, et ne l'avait pas ramenee ici.
     * On reprend sa valeur eprouvee plutot que d'en inventer une autre.
     *
     * En la posant a ce niveau, TOUS les appelants en beneficient — le budget
     * du wiki devient une ceinture par-dessus les bretelles, ce qui ne coute
     * rien.
     */
    const MAX_URL_LENGTH = 1500;

    /** @var Translation|null */
    protected $store;

    /**
     * La cle vit dans la configuration, pas dans api_provider.
     *
     * Elle y est deja (EPH_GOOGLE_TRANSLATE_API_KEY, reglee depuis les
     * preferences) : la recopier ailleurs creerait deux sources de verite et,
     * tot ou tard, deux valeurs differentes. Si quelqu'un renseigne malgre tout
     * auth_key sur le fournisseur, c'est elle qui l'emporte.
     *
     * Passer par extraQuery() plutot que par le tableau de requete a une
     * consequence voulue : la cle n'entre ni dans l'empreinte de cache ni dans
     * le resume lisible, elle ne se retrouve donc jamais en clair dans
     * api_cache.
     *
     * @return array<string,string>
     */
    protected function extraQuery() {

        if (!empty($this->provider->auth_key)) {
            return ['key' => (string) $this->provider->auth_key];
        }

        // Configuration::get() n'est PAS statique dans ce depot : la lecture
        // passe par le contexte, comme partout ailleurs dans quantumcore
        // (cf. Meta.php). Un Configuration::get() statique leve un fatal.
        $key = '';

        try {
            $context = Context::getContext();

            if ($context !== null && isset($context->phenyxConfig)) {
                $key = (string) $context->phenyxConfig->get('EPH_GOOGLE_TRANSLATE_API_KEY');
            }

        } catch (\Throwable $e) {
            return [];
        }

        return $key !== '' ? ['key' => $key] : [];
    }

    /**
     * @return Translation
     */
    protected function store() {

        if ($this->store === null) {
            $this->store = new Translation();
        }

        return $this->store;
    }

    /**
     * Traduction connue, deja enregistree ?
     *
     * @param string $origin
     * @param string $target
     *
     * @return string|null
     */
    protected function known($origin, $target) {

        try {
            $known = $this->store()->getExistingTranslation($target, $origin);
        } catch (\Throwable $e) {
            return null;
        }

        return ($known !== false && $known !== null && $known !== '') ? (string) $known : null;
    }

    /**
     * Enregistre une traduction dans le magasin partage.
     *
     * Echoue en silence : ne pas pouvoir memoriser une traduction ne doit pas
     * empecher de la rendre.
     *
     * @param string      $origin
     * @param string      $target
     * @param string      $translation
     * @param string|null $fileName
     *
     * @return void
     */
    protected function remember($origin, $target, $translation, $fileName = null) {

        if ($translation === '' || $translation === $origin) {
            return;
        }

        try {

            // Sans ce controle, chaque appel empilerait une ligne de plus pour
            // la meme chaine : l'ancienne methode de PhenyxTool ajoutait sans
            // jamais verifier.
            if ($this->known($origin, $target) !== null) {
                return;
            }

            $row = new Translation();
            $row->iso_code = $target;
            $row->file_name = $fileName;
            $row->origin = $origin;
            $row->translation = $translation;
            $row->date_upd = date('Y-m-d H:i:s');
            $row->add();
        } catch (\Throwable $e) {
            PhenyxLogger::addLog('[API ' . static::CODE . '] enregistrement impossible : ' . $e->getMessage(), 1);
        }

    }

    /**
     * Traduit une chaine.
     *
     * @param string      $text
     * @param string      $target   Code ISO de la langue visee
     * @param string|null $source   Langue d'origine ; detectee si absente
     * @param string|null $fileName Contexte, memorise avec la traduction
     *
     * @return string La traduction, ou la chaine source si rien n'a abouti
     */
    public function translate($text, $target, $source = null, $fileName = null) {

        // Un tableau multilang passe par erreur produisait chez Google
        // « Starting an object on a scalar field ». On rend l'entree telle
        // quelle plutot que de laisser filer.
        if (!is_string($text) || trim($text) === '' || empty($target)) {
            return $text;
        }

        $known = $this->known($text, $target);

        if ($known !== null) {
            return $known;
        }

        $result = $this->translateBatch([$text], $target, $source, $fileName);

        return isset($result[0]) ? $result[0] : $text;
    }

    /**
     * Traduit plusieurs chaines en un seul appel.
     *
     * L'API v2 accepte des parametres « q » repetes. Traduire une table de
     * quarante libelles passe ainsi de quarante allers-retours a un seul —
     * moins de latence, et beaucoup moins de risque de heurter un quota.
     *
     * @param array       $texts
     * @param string      $target
     * @param string|null $source
     * @param string|null $fileName
     *
     * @return array Memes cles qu'en entree ; valeur source la ou rien n'a abouti
     */
    public function translateBatch(array $texts, $target, $source = null, $fileName = null) {

        $out = $texts;

        if (empty($texts) || empty($target)) {
            return $out;
        }

        // Ce qui reste vraiment a demander, apres le magasin partage.
        $todo = [];

        foreach ($texts as $k => $text) {

            if (!is_string($text) || trim($text) === '') {
                continue;
            }

            $known = $this->known($text, $target);

            if ($known !== null) {
                $out[$k] = $known;
                continue;
            }

            $todo[$k] = $text;
        }

        if (empty($todo)) {
            return $out;
        }

        foreach ($this->decouper($todo) as $chunk) {
            $this->askGoogle($chunk, $target, $source, $fileName, $out);
        }

        return $out;
    }

    /**
     * Decoupe le lot en requetes que Google acceptera.
     *
     * ═══ POURQUOI PAS array_chunk() TOUT SEUL (2026-08-11) ═══
     *
     * askGoogle() envoie les libelles en GET, un parametre « q » par chaine.
     * MAX_BATCH bornait le NOMBRE de chaines, jamais la LONGUEUR de l'adresse.
     * Or trente phrases completes — pas des mots isoles, des phrases d'aide de
     * plusieurs lignes — depassent largement la limite de taille d'URL.
     *
     * L'echec est total et muet : Google refuse la requete entiere, askGoogle
     * sort par son fail-open, et les trente-deux chaines reviennent identiques
     * a elles-memes. A l'ecran, l'employe voit « traduction terminee » et pas
     * une seule ligne remplie.
     *
     * Constate en reel : sur un lot de trente-deux libelles d'aide, seule
     * « APE code » est revenue traduite — et encore, parce qu'elle etait deja
     * dans le magasin, donc jamais partie chez Google.
     *
     * On borne donc AUSSI la longueur encodee. La limite retenue est basse a
     * dessein : une adresse de requete traverse des serveurs mandataires dont
     * on ne choisit pas les reglages, et un lot de dix phrases reste
     * infiniment moins couteux que dix appels separes.
     *
     * @param array $todo
     *
     * @return array[] Liste de lots, clefs d'origine preservees
     */
    protected function decouper(array $todo) {

        $lots    = [];
        $courant = [];
        $taille  = 0;

        foreach ($todo as $clef => $texte) {

            // « q= » plus la valeur encodee, plus le « & » qui la relie.
            $poids = strlen(rawurlencode($texte)) + 4;

            if (count($courant) > 0
                && ($taille + $poids > self::MAX_URL_LENGTH || count($courant) >= self::MAX_BATCH)) {
                $lots[]  = $courant;
                $courant = [];
                $taille  = 0;
            }

            $courant[$clef] = $texte;
            $taille += $poids;
        }

        if (count($courant) > 0) {
            $lots[] = $courant;
        }

        return $lots;
    }

    /**
     * Un lot, un appel.
     *
     * @param array       $chunk
     * @param string      $target
     * @param string|null $source
     * @param string|null $fileName
     * @param array       $out      Modifie sur place
     *
     * @return void
     */
    protected function askGoogle(array $chunk, $target, $source, $fileName, array &$out) {

        // http_build_query rendrait « q[0]=..&q[1]=.. », que Google ne comprend
        // pas : il attend « q=..&q=.. ». D'ou la construction a la main, et le
        // passage par le chemin plutot que par le tableau de parametres.
        $parts = [];

        foreach ($chunk as $text) {
            $parts[] = 'q=' . rawurlencode($text);
        }

        $parts[] = 'target=' . rawurlencode($target);
        $parts[] = 'format=text';

        if (!empty($source)) {
            $parts[] = 'source=' . rawurlencode($source);
        }

        $requete = self::ENDPOINT . '?' . implode('&', $parts);
        $payload = $this->get($requete);

        if ($payload === null) {

            /*
             * ⚠️ LE FAIL-OPEN NE DOIT PAS ETRE MUET (2026-08-11).
             *
             * Il ne l'etait pas par negligence mais par principe : une API
             * injoignable ne doit pas faire tomber un ecran. Sauf qu'ici,
             * rendre les chaines sources SANS RIEN DIRE produit un resultat
             * indiscernable d'un succes — l'appelant filtre ce qui n'a pas
             * change, ne trouve rien, et annonce « traduction terminee ».
             *
             * On garde le fail-open, on retire le silence. La longueur de la
             * requete est journalisee : c'est elle qui a fait echouer les lots
             * de phrases completes, et sans elle on cherche du cote du quota.
             */
            PhenyxLogger::addLog(
                '[API ' . static::CODE . '] lot de ' . count($chunk) . ' chaine(s) non traduit : '
                . ((string) $this->lastError() !== '' ? $this->lastError() : 'aucune reponse')
                . ' (code ' . (int) $this->lastHttpCode() . ', requete de ' . strlen($requete) . ' octets)',
                2
            );

            // Fail-open : $out garde deja les chaines sources.
            return;
        }

        $translations = isset($payload['data']['translations']) && is_array($payload['data']['translations'])
        ? $payload['data']['translations']
        : [];

        if (count($translations) !== count($chunk)) {
            PhenyxLogger::addLog(
                '[API ' . static::CODE . '] reponse incoherente : ' . count($translations)
                . ' traduction(s) pour ' . count($chunk) . ' chaine(s)',
                2
            );

            return;
        }

        // array_keys preserve la correspondance : Google rend les traductions
        // dans l'ordre des « q ».
        $keys = array_keys($chunk);

        foreach ($translations as $i => $t) {

            if (!isset($t['translatedText'])) {
                continue;
            }

            // Meme avec format=text, Google echappe apostrophes et chevrons.
            $value = html_entity_decode((string) $t['translatedText'], ENT_QUOTES, 'UTF-8');

            if ($value === '') {
                continue;
            }

            $key = $keys[$i];
            $out[$key] = $value;
            $this->remember($chunk[$key], $target, $value, $fileName);
        }

    }

}
