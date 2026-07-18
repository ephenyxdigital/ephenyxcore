var expression_translated;

/**
 * Éditeur de traduction des titres de pages meta (type 'metas'/'pluginsmeta').
 * Calqué sur translation_tabs.js, mais : cible #grid_AdminMeta, poste
 * id_meta => translation, et utilise des noms de fonctions distincts pour ne
 * pas entrer en collision avec l'éditeur d'onglets si les deux sont ouverts.
 */

function apiMetaTranslate() {

    var $rowData = [];
    var data;
    if ($('#grid_AdminMeta').pqGrid("instance")) {
        var grid = $('#grid_AdminMeta').pqGrid("instance");
        data = grid.getData();
        $.each(data, function(k, value) {
            if (typeof value.translation === 'undefined' || value.translation === '') {
                var arr = {};
                data = grid.getRowData({ rowIndx: k });
                arr['rowIndx'] = k;
                for (var i in data) {
                    if (data.hasOwnProperty(i)) {
                        arr[i] = data[i];
                    }
                }
                $rowData.push(arr);
            }
        });
    } else {
        console.log('no instance');
    }

    proceedMetaApiTranslate($rowData);
}

function resetMetaTranslate() {

    var $rowData = [];
    var data;
    if ($('#grid_AdminMeta').pqGrid("instance")) {
        var grid = $('#grid_AdminMeta').pqGrid("instance");
        data = grid.getData();
        $.each(data, function(k, value) {
            var arr = {};
            data = grid.getRowData({ rowIndx: k });
            arr['rowIndx'] = k;
            for (var i in data) {
                if (data.hasOwnProperty(i)) {
                    arr[i] = data[i];
                }
            }
            $rowData.push(arr);
        });
    } else {
        console.log('no instance');
    }

    proceedMetaApiTranslate($rowData);
}

function proceedMetaApiTranslate($rowData) {

    var grid = $('#grid_AdminMeta').pqGrid("instance");
    var totalExpressions = $rowData.length;
    var current = 0;
    var expressionToTranslate = totalExpressions;
    $('#grid_AdminMeta .pq-grid-title').html(search_translation + ' <span id="ace-countdonwn">' + expressionToTranslate + '</span> ' + to_remain);

    $.each($rowData, function(index, value) {
        var r = value.name;

        if (void 0 !== javaFound[r]) {
            grid.deleteRow({ rowIndx: value.rowIndx });
            var c = {
                id_meta: value.id_meta,
                plugin: value.plugin,
                page: value.page,
                name: value.name,
                translation: javaFound[r]
            };
            grid.addRow({ rowData: c, rowIndx: value.rowIndx });
            expressionToTranslate--;
            $('#ace-countdonwn').html(expressionToTranslate);
            current++;
            if (current == totalExpressions) {
                $('#grid_AdminMeta .pq-grid-title').html(translation_complete);
            }
        } else {
            $.ajaxq("TranslateProcess", {
                url: AjaxLinkAdminTranslations,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: "getGoogleTranslation",
                    file_name: value.page,
                    text: value.name,
                    target: lang_selected,
                    ajax: true
                }
            }).success(function(response) {
                expression_translated = response.translation;
                grid.deleteRow({ rowIndx: value.rowIndx });
                var rowData = {
                    'id_meta': value.id_meta,
                    'plugin': value.plugin,
                    'page': value.page,
                    'name': value.name,
                    'translation': expression_translated
                };
                grid.addRow({ rowData: rowData, rowIndx: value.rowIndx });
                javaFound[value.name] = expression_translated;
                expressionToTranslate--;
                $('#ace-countdonwn').html(expressionToTranslate);
                current++;
                if (current == totalExpressions) {
                    $('#grid_AdminMeta .pq-grid-title').html(translation_complete);
                }
            });
        }
    });
}

function saveMetaGridTranslations(type) {

    var formData = new FormData($('form#translations_form')[0]);

    if ($('#grid_AdminMeta').pqGrid("instance")) {
        var grid = $('#grid_AdminMeta').pqGrid("instance");
        var data = grid.getData();
        $.each(data, function(k, value) {
            formData.append(value.id_meta, value.translation);
        });
    } else {
        console.log('no instance');
    }

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
            if (data.success) {
                Swal.fire({ position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 4000 });
            } else {
                Swal.fire({ position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 4000 });
            }
        },
        complete: function(data) {
            $('#uperTranslate' + type).remove();
            $('#contentTranslate' + type).remove();
            $('#uperAdminTranslations a').trigger('click');
        }
    });
}

function saveMetaGridTranslationsAndStay(type) {

    var formData = new FormData($('form#translations_form')[0]);

    if ($('#grid_AdminMeta').pqGrid("instance")) {
        var grid = $('#grid_AdminMeta').pqGrid("instance");
        var data = grid.getData();
        $.each(data, function(k, value) {
            formData.append(value.id_meta, value.translation);
        });
    } else {
        console.log('no instance');
    }

    $.ajax({
        url: AjaxLinkAdminTranslations,
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
            if (data.success) {
                Swal.fire({ position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire({ position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
            }
        }
    });
}

if (typeof window.closeForm !== 'function') {
    window.closeForm = function(type) {
        $('#uperTranslate' + type).remove();
        $('#contentTranslate' + type).remove();
        $('#uperAdminTranslations a').trigger('click');
    };
}
