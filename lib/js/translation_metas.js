var expression_translated;

/**
 * Éditeur de traduction des titres de pages meta (type 'metas'/'pluginsmeta').
 * Calqué sur translation_tabs.js, mais : cible #grid_AdminMeta, poste
 * id_meta => translation, et utilise des noms de fonctions distincts pour ne
 * pas entrer en collision avec l'éditeur d'onglets si les deux sont ouverts.
 *
 * javaFound est injecté par translation_meta.tpl : dictionnaire
 * { expression source => traduction } construit depuis la base
 * (Translation::getExistingTranslationByIso()). Comme dans
 * translations.min.js, il est lu AVANT tout appel API (clé = value.name,
 * le titre source) et réalimenté APRÈS chaque traduction reçue, si bien
 * que les titres identiques ne déclenchent qu'un seul appel.
 */

if (typeof window.javaFound === 'undefined') {
    // Repli si le template n'a pas injecté la variable : cache vide mais
    // le code reste fonctionnel.
    window.javaFound = {};
}

// Lecture du cache : une traduction vide ('') est considérée comme absente.
function metaTranslationCacheGet(key) {
    if (typeof key !== 'undefined' && Object.prototype.hasOwnProperty.call(javaFound, key) && javaFound[key] !== '' && typeof javaFound[key] !== 'undefined') {
        return javaFound[key];
    }
    return undefined;
}

function getMetaGridInstance() {
    if ($('#grid_AdminMeta').pqGrid('instance')) {
        return $('#grid_AdminMeta').pqGrid('instance');
    }
    console.log('no instance');
    return null;
}

// Collecte des lignes du grid ; onlyMissing = true → uniquement les lignes
// sans traduction (undefined ou chaîne vide).
function collectMetaRows(onlyMissing) {
    var rows = [];
    var grid = getMetaGridInstance();

    if (!grid) {
        return rows;
    }

    $.each(grid.getData(), function (k, value) {

        if (onlyMissing && typeof value.translation !== 'undefined' && value.translation !== '') {
            return;
        }

        var rowData = grid.getRowData({
            rowIndx: k
        });
        var arr = {
            rowIndx: k
        };

        for (var i in rowData) {

            if (rowData.hasOwnProperty(i)) {
                arr[i] = rowData[i];
            }
        }

        rows.push(arr);
    });

    return rows;
}

function apiMetaTranslate() {
    proceedMetaApiTranslate(collectMetaRows(true));
}

function resetMetaTranslate() {
    proceedMetaApiTranslate(collectMetaRows(false));
}

function proceedMetaApiTranslate($rowData) {

    var grid = getMetaGridInstance();

    if (!grid) {
        return;
    }

    var totalExpressions = $rowData.length;
    var current = 0;
    var expressionToTranslate = totalExpressions;

    // Rien a traiter : afficher directement l'etat final au lieu de laisser
    // « 0 expressions restantes » a l'ecran.
    if (totalExpressions === 0) {
        $('#grid_AdminMeta .pq-grid-title').html(translation_complete);
        return;
    }

    $('#grid_AdminMeta .pq-grid-title').html(search_translation + ' <span id="ace-countdonwn">' + expressionToTranslate + '</span> ' + to_remain);

    // Remplace la ligne par sa version traduite et met a jour le compteur.
    function applyMetaTranslation(value, translation) {
        grid.deleteRow({
            rowIndx: value.rowIndx
        });
        grid.addRow({
            rowData: {
                id_meta: value.id_meta,
                meta_key: value.meta_key,
                plugin: value.plugin,
                page: value.page,
                name: value.name,
                translation: translation
            },
            rowIndx: value.rowIndx
        });
        expressionToTranslate--;
        $('#ace-countdonwn').html(expressionToTranslate);
    }

    // A appeler pour CHAQUE ligne (succes, cache ou echec) afin que le titre
    // passe bien a « traduction terminee » meme si un appel echoue.
    function metaStepDone() {
        current++;

        if (current === totalExpressions) {
            $('#grid_AdminMeta .pq-grid-title').html(translation_complete);
        }
    }

    /*
     * ═══ UN LOT AU LIEU D'UNE REQUETE PAR LIGNE (2026-08-09) ═══
     *
     * Chaque ligne partait dans sa propre requete, mise en file par $.ajaxq.
     * Rien ne saturait le serveur, mais les latences s'additionnaient : trois
     * cents libelles, c'etaient trois cents allers-retours l'un apres l'autre.
     *
     * L'action « getGoogleTranslations » regroupe desormais jusqu'a cent
     * libelles par appel (parametres « q » repetes de l'API v2, via
     * TranslateApi::translateBatch).
     *
     * ⚠️ Les libelles que le fournisseur n'a pas su traduire ne figurent PAS
     * dans la reponse : la ligne reste manquante, on n'y ecrit jamais la
     * chaine source.
     */
    var PAQUET = 100;
    var parFichier = {};

    $.each($rowData, function (index, value) {

        var cached = metaTranslationCacheGet(value.name);

        if (typeof cached !== 'undefined') {
            applyMetaTranslation(value, cached);
            metaStepDone();
            return;
        }

        var fichier = value.page || '';

        if (typeof parFichier[fichier] === 'undefined') {
            parFichier[fichier] = [];
        }

        parFichier[fichier].push(value);
    });

    var paquets = [];

    $.each(parFichier, function (fichier, lignes) {

        for (var i = 0; i < lignes.length; i += PAQUET) {
            paquets.push({
                file_name: fichier,
                lignes: lignes.slice(i, i + PAQUET)
            });
        }

    });

    $.each(paquets, function (rang, paquet) {
        var textes = $.map(paquet.lignes, function (ligne) {
            return ligne.name;
        });

        $.ajax({
            url: AjaxLinkAdminTranslations,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getGoogleTranslations',
                texts: JSON.stringify(textes),
                file_name: paquet.file_name,
                target: lang_selected,
                ajax: true
            },
            success: function (response) {
                var carte = (response && response.translations) ? response.translations : {};

                $.each(paquet.lignes, function (i, value) {
                    var traduction = carte[value.name];

                    if (traduction) {
                        // Alimente le cache : les libelles identiques suivants
                        // seront servis sans appel.
                        javaFound[value.name] = traduction;
                        applyMetaTranslation(value, traduction);
                    }

                    metaStepDone();
                });
            },
            error: function () {
                // Un paquet perdu ne doit pas figer le compteur.
                $.each(paquet.lignes, function () {
                    metaStepDone();
                });
            }
        });
    });
}

// Construit le FormData commun aux deux modes de sauvegarde :
// une entrée meta_key => translation par ligne du grid.
function buildMetaTranslationsFormData() {

    var formData = new FormData($('form#translations_form')[0]);
    var grid = getMetaGridInstance();

    if (grid) {
        $.each(grid.getData(), function (k, value) {
            // meta_key : id_meta pour une page en base, 'plugin:<plugin>:<page>'
            // pour la page d'un plugin non installé (repli sur id_meta si la
            // colonne manque, ex. cache navigateur d'une version antérieure).
            var metaKey = (typeof value.meta_key !== 'undefined' && value.meta_key !== '') ? value.meta_key : value.id_meta;
            formData.append(metaKey, value.translation);
        });
    }

    return formData;
}

function saveMetaGridTranslations(type) {

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: buildMetaTranslationsFormData(),
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {

            if (data.success) {
                Swal.fire({ position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 4000 });
            } else {
                Swal.fire({ position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 4000 });
            }
        },
        complete: function (data) {
            $('#uperTranslate' + type).remove();
            $('#contentTranslate' + type).remove();
            $('#uperAdminTranslations a').trigger('click');
        }
    });
}

function saveMetaGridTranslationsAndStay(type) {

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: buildMetaTranslationsFormData(),
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {

            if (data.success) {
                Swal.fire({ position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire({ position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
            }
        }
    });
}

if (typeof window.closeForm !== 'function') {
    window.closeForm = function (type) {
        $('#uperTranslate' + type).remove();
        $('#contentTranslate' + type).remove();
        $('#uperAdminTranslations a').trigger('click');
    };
}
