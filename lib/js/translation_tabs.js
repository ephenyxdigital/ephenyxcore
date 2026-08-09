var expression_translated;

/**
 * Éditeur de traduction des onglets back-office (types 'tabs', 'plugintabs',
 * packs). Chargé par AdminTranslationsController::processTabTranslation(),
 * processTabTranslationPack() et processPluginTabTranslation().
 *
 * javaFound est injecté par les templates translation_tab*.tpl :
 * dictionnaire { expression source => traduction } construit depuis la base
 * (Translation::getExistingTranslationByIso()). Il sert de cache local :
 * - lecture AVANT tout appel API (clé = value.name, le libellé source ;
 *   l'ancienne version lisait value.expression, colonne inexistante dans
 *   colTabModel, si bien que le cache ne servait jamais) ;
 * - écriture APRÈS chaque traduction reçue, comme dans translations.min.js,
 *   pour que les libellés identiques ne déclenchent qu'un seul appel.
 */

if (typeof window.javaFound === 'undefined') {
    // Repli si le template n'a pas injecté la variable : cache vide mais
    // le code reste fonctionnel.
    window.javaFound = {};
}

// Lecture du cache : une traduction vide ('') est considérée comme absente.
function tabTranslationCacheGet(key) {
    if (typeof key !== 'undefined' && Object.prototype.hasOwnProperty.call(javaFound, key) && javaFound[key] !== '' && typeof javaFound[key] !== 'undefined') {
        return javaFound[key];
    }
    return undefined;
}

function getTabGridInstance() {
    if ($('#grid_AdminBackTab').pqGrid('instance')) {
        return $('#grid_AdminBackTab').pqGrid('instance');
    }
    console.log('no instance');
    return null;
}

// Collecte des lignes du grid ; onlyMissing = true → uniquement les lignes
// sans traduction (laissées UNSET côté PHP, cf. AdminTranslationsController).
function collectTabRows(onlyMissing) {
    var rows = [];
    var grid = getTabGridInstance();

    if (!grid) {
        return rows;
    }

    $.each(grid.getData(), function (k, value) {

        if (onlyMissing && typeof value.translation !== 'undefined') {
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

function apiTabTranslate() {
    proceedTabApiTranslate(collectTabRows(true));
}

function resetTabTranslate() {
    proceedTabApiTranslate(collectTabRows(false));
}

function proceedTabApiTranslate($rowData) {

    var grid = getTabGridInstance();

    if (!grid) {
        return;
    }

    var totalExpressions = $rowData.length;
    var current = 0;
    var expressionToTranslate = totalExpressions;

    // Rien a traiter : afficher directement l'etat final au lieu de laisser
    // « 0 expressions restantes » a l'ecran.
    if (totalExpressions === 0) {
        $('#grid_AdminBackTab .pq-grid-title').html(translation_complete);
        return;
    }

    $('#grid_AdminBackTab .pq-grid-title').html(search_translation + ' <span id="ace-countdonwn">' + expressionToTranslate + '</span> ' + to_remain);

    // Remplace la ligne par sa version traduite et met a jour le compteur.
    function applyTabTranslation(value, translation) {
        grid.deleteRow({
            rowIndx: value.rowIndx
        });
        grid.addRow({
            rowData: {
                id_back_tab: value.id_back_tab,
                tab_key: value.tab_key,
                plugin: value.plugin,
                class_name: value.class_name,
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
    function tabStepDone() {
        current++;

        if (current === totalExpressions) {
            $('#grid_AdminBackTab .pq-grid-title').html(translation_complete);
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

        var cached = tabTranslationCacheGet(value.name);

        if (typeof cached !== 'undefined') {
            applyTabTranslation(value, cached);
            tabStepDone();
            return;
        }

        var fichier = value.class_name || '';

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
                        applyTabTranslation(value, traduction);
                    }

                    tabStepDone();
                });
            },
            error: function () {
                // Un paquet perdu ne doit pas figer le compteur.
                $.each(paquet.lignes, function () {
                    tabStepDone();
                });
            }
        });
    });
}

function getGoogleTranslation(text) {

    // Cache d'abord : évite un appel synchrone (bloquant) inutile.
    var cached = tabTranslationCacheGet(text);

    if (typeof cached !== 'undefined') {
        return cached;
    }

    var result;
    $.ajax({
        type: "POST",
        url: AjaxLinkAdminTranslations,
        data: {
            action: "getGoogleTranslation",
            text: text,
            target: lang_selected,
            ajax: true
        },
        async: false,
        dataType: "json",
        success: function success(data) {
            result = data.translation;

            if (result) {
                javaFound[text] = result;
            }
        }
    });

    return result;
}

// Construit le FormData commun aux deux modes de sauvegarde :
// une entrée tab_key => translation par ligne du grid.
function buildTabTranslationsFormData() {

    var formData = new FormData($('form#translations_form')[0]);
    var grid = getTabGridInstance();

    if (grid) {
        $.each(grid.getData(), function (k, value) {
            // tab_key : id_back_tab pour un tab en base,
            // 'plugin:<plugin>:<class>' pour l'AdminController d'un plugin
            // non installé (repli sur id_back_tab si la colonne manque).
            var tabKey = (typeof value.tab_key !== 'undefined' && value.tab_key !== '') ? value.tab_key : value.id_back_tab;
            formData.append(tabKey, value.translation);
        });
    }

    return formData;
}

function saveGridTranslations(type) {

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: buildTabTranslationsFormData(),
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {

            if (data.success) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 4000
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 4000
                });
            }
        },
        complete: function complete(data) {
            closeForm(type);
        }
    });
}

function saveGridTranslationsAndStay(type) {

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: buildTabTranslationsFormData(),
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {

            if (data.success) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
    });
}

function closeForm(type) {

    $('#uperTranslate' + type).remove();
    $('#contentTranslate' + type).remove();
    $('#uperAdminTranslations a').trigger('click');
}
