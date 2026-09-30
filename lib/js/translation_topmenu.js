/**
 * Traduction du menu du front office (type 'topmenu' de
 * AdminTranslationsController, 09/09/2026) : sauvegarde de la grille et
 * traduction automatique des lignes manquantes.
 *
 * Modele : translation_tabs.js, avec deux differences.
 *
 * 1. Une liste de champs par ligne (aujourd'hui le seul `name`, la liste
 *    vient du PHP) : chaque ligne est postee sous la forme
 *      <niveau>:<id> => JSON({name, …}).
 *    Le PHP ignore les lignes dont tous les champs valent la reference —
 *    la grille les pre-remplit avec la langue du site pour donner une base a
 *    ecraser, il ne faut pas l'ecrire telle quelle dans une autre langue.
 *
 * 2. L'API traduit la REFERENCE de chaque champ (colonnes ref_*), jamais la
 *    cellule affichee, qui peut deja contenir une traduction partielle.
 *    Les libelles voyagent par lots de cent (action getPhenyxTranslations,
 *    un vrai tableau texts[] — pas de JSON, cf. le commentaire du PHP).
 *
 * Aucune variable de module pour l'etat : la grille EST l'etat.
 */

function topmenuGrid() {

	var $grid = $('#grid_TranslateTopMenu');

	if ($grid.length && $grid.pqGrid('instance')) {
		return $grid.pqGrid('instance');
	}

	return null;
}

/** Les champs traduits, dans l'ordre du PHP (injectes par le gabarit). */
function topmenuFieldList() {

	return (typeof topmenu_fields !== 'undefined' && topmenu_fields) ? topmenu_fields : ['name'];
}

/** Toutes les lignes, ou seulement celles a traduire. */
function topmenuRows(onlyMissing) {

	var rows = [];
	var grid = topmenuGrid();

	if (!grid) {
		return rows;
	}

	$.each(grid.getData(), function (k, value) {

		if (onlyMissing && parseInt(value.todo, 10) !== 1) {
			return;
		}

		var row = grid.getRowData({rowIndx: k});
		var copie = {rowIndx: k};

		for (var i in row) {
			if (row.hasOwnProperty(i)) {
				copie[i] = row[i];
			}
		}

		rows.push(copie);
	});

	return rows;
}

/* ── L'API ─────────────────────────────────────────────────────────────── */

function topmenuApiTranslate(onlyMissing) {

	var grid = topmenuGrid();

	if (!grid) {
		return;
	}

	var rows = topmenuRows(onlyMissing);
	var fields = topmenuFieldList();

	/* Chaque texte de reference distinct part une seule fois. */
	var textes = [];
	var vus = {};

	$.each(rows, function (i, row) {
		$.each(fields, function (j, field) {
			var ref = $.trim(row['ref_' + field] || '');

			if (ref !== '' && !vus[ref]) {
				vus[ref] = true;
				textes.push(ref);
			}
		});
	});

	if (!textes.length) {
		$('#grid_TranslateTopMenu .pq-grid-title').html(translation_complete);
		return;
	}

	var restant = textes.length;
	var carte = {};
	var PAQUET = 100;
	var paquets = [];

	for (var p = 0; p < textes.length; p += PAQUET) {
		paquets.push(textes.slice(p, p + PAQUET));
	}

	$('#grid_TranslateTopMenu .pq-grid-title').html(search_translation + ' <span id="topmenu-countdown">' + restant + '</span> ' + to_remain);

	var aFaire = paquets.length;

	function paquetFini() {

		aFaire--;

		if (aFaire > 0) {
			return;
		}

		/* Tout est revenu : on repose les lignes avec leurs traductions. */
		$.each(rows, function (i, row) {
			var data = {};
			var change = false;

			for (var k in row) {
				if (row.hasOwnProperty(k) && k !== 'rowIndx') {
					data[k] = row[k];
				}
			}

			$.each(fields, function (j, field) {
				var ref = $.trim(row['ref_' + field] || '');

				if (ref !== '' && carte[ref]) {
					data[field] = carte[ref];
					change = true;
				}
			});

			if (!change) {
				return;
			}

			data.todo = 0;
			data.state = topmenu_translated_label;

			/* Meme geste que translation_tabs.js : la ligne est remplacee. */
			grid.deleteRow({rowIndx: row.rowIndx});
			grid.addRow({rowData: data, rowIndx: row.rowIndx});
		});

		grid.refreshView();
		$('#grid_TranslateTopMenu .pq-grid-title').html(translation_complete);
	}

	$.each(paquets, function (rang, paquet) {

		$.ajax({
			url: AjaxLinkAdminTranslations,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'getPhenyxTranslations',
				texts: paquet,
				file_name: 'topmenu',
				target: lang_selected,
				ajax: true
			},
			success: function (response) {

				var recu = (response && response.translations) ? response.translations : {};

				$.each(paquet, function (i, texte) {
					if (recu[texte]) {
						carte[texte] = recu[texte];
					}

					restant--;
				});

				$('#topmenu-countdown').html(restant);

				if (response && response.message && $.isEmptyObject(recu)) {
					Swal.fire({position: 'top-end', icon: 'warning', title: response.message, showConfirmButton: false, timer: 5000});
				}
			},
			error: function () {
				restant -= paquet.length;
				$('#topmenu-countdown').html(restant);
			},
			complete: function () {
				paquetFini();
			}
		});
	});
}

/* ── L'enregistrement ──────────────────────────────────────────────────── */

function topmenuFormData() {

	var formData = new FormData($('form#translations_form')[0]);
	var grid = topmenuGrid();
	var fields = topmenuFieldList();
	var count = 0;

	if (!grid) {
		return null;
	}

	$.each(grid.getData(), function (k, value) {

		var row = grid.getRowData({rowIndx: k});
		var data = {};

		$.each(fields, function (j, field) {
			data[field] = row[field] || '';
		});

		formData.append(row.row_key, JSON.stringify(data));
		count++;
	});

	return count ? formData : null;
}

function topmenuSave(type, stay) {

	var formData = topmenuFormData();

	if (!formData) {
		Swal.fire({position: 'top-end', icon: 'info', title: topmenu_nothing_to_save, showConfirmButton: false, timer: 3000});
		return;
	}

	$.ajax({
		url: AjaxLinkAdminTranslations,
		type: 'POST',
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (data) {

			Swal.fire({
				position: 'top-end',
				icon: (data && data.success) ? 'success' : 'error',
				title: (data && data.message) ? data.message : '',
				showConfirmButton: false,
				timer: 4000
			});

			if (data && data.success && !stay) {
				closeForm(type);
			}
		},
		error: function () {
			Swal.fire({position: 'top-end', icon: 'error', title: topmenu_save_failed, showConfirmButton: false, timer: 4000});
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
