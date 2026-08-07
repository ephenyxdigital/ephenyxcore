/**
 * Grille de traduction du sommaire du wiki (type 'phenyxwikimenu' de
 * AdminTranslationsController).
 *
 * Meme principe que translation_phenyxwiki.js : on ne poste que les lignes
 * modifiees, et la traduction automatique remplit la grille sans rien ecrire.
 * Un seul champ ici, `name`, et une cle composite `menu_key` — « m:<id> »
 * pour une rubrique, « c:<id> » pour une page fille — parce que les deux
 * tables ont des auto-increments independants.
 */

var wikimenuSnapshot = null;

function wikimenuGrid() {

	var $grid = $('#grid_AdminWikiMenu');

	if (!$grid.length || !$grid.pqGrid('instance')) {
		return null;
	}

	return $grid.pqGrid('instance');
}

function wikimenuToast(icon, title, timer) {

	Swal.fire({
		position: 'top-end',
		icon: icon,
		title: title,
		showConfirmButton: false,
		timer: timer || 4000
	});
}

function wikimenuSnapshotInit() {

	var grid = wikimenuGrid();

	if (!grid) {
		return;
	}

	wikimenuSnapshot = {};

	$.each(grid.getData(), function (k, row) {
		wikimenuSnapshot[row.menu_key] = row.name || '';
	});
}

function wikimenuCollect(formData) {

	var grid = wikimenuGrid();

	if (!grid) {
		console.log('no instance');

		return 0;
	}

	var count = 0;

	$.each(grid.getData(), function (k, row) {

		var valeur = row.name || '';

		if (wikimenuSnapshot && wikimenuSnapshot[row.menu_key] === valeur) {
			return;
		}

		formData.append(row.menu_key, JSON.stringify({name: valeur}));
		count++;
	});

	return count;
}

function wikimenuSend(type, formData, stay) {

	$('#ajax_running').slideDown();

	$.ajax({
		url: AjaxLinkAdminTranslations,
		type: 'POST',
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (data) {

			if (data && data.success) {
				wikimenuToast('success', data.message, stay ? 2000 : 4000);

				if (stay) {
					wikimenuSnapshotInit();
				}

			} else {
				wikimenuToast('error', (data && data.message) ? data.message : 'Erreur', 5000);
			}
		},
		complete: function () {

			$('#ajax_running').slideUp();

			if (stay) {
				return;
			}

			$('#uperTranslate' + type).remove();
			$('#contentTranslate' + type).remove();
			$('#uperAdminTranslations a').trigger('click');
		}
	});
}

function saveWikimenuGridTranslations(type) {

	if (wikimenuSnapshot === null) {
		wikimenuSnapshotInit();
	}

	var formData = new FormData($('form#translations_form')[0]);

	if (!wikimenuCollect(formData)) {
		wikimenuToast('info', wikimenu_nothing_to_save, 2500);
		$('#uperTranslate' + type).remove();
		$('#contentTranslate' + type).remove();
		$('#uperAdminTranslations a').trigger('click');

		return;
	}

	wikimenuSend(type, formData, false);
}

function saveWikimenuGridTranslationsAndStay(type) {

	if (wikimenuSnapshot === null) {
		wikimenuSnapshotInit();
	}

	var formData = new FormData($('form#translations_form')[0]);

	if (!wikimenuCollect(formData)) {
		wikimenuToast('info', wikimenu_nothing_to_save, 2500);

		return;
	}

	wikimenuSend(type, formData, true);
}

/* ---------------------------------------------------------------- API ---- */

function wikimenuApplyRow(grid, rowIndx, nom) {

	var courant = grid.getRowData({rowIndx: rowIndx});

	if (!courant) {
		return;
	}

	var ligne = $.extend({}, courant);
	ligne.name = nom;
	ligne.todo = 0;
	ligne.state = wikimenu_translated_label;

	grid.deleteRow({rowIndx: rowIndx});
	grid.addRow({rowData: ligne, rowIndx: rowIndx});
}

function wikimenuApiRun(grid, file, position) {

	if (position >= file.length) {
		$('#wikimenu_api_all').prop('disabled', false);
		$('#wikimenu_api_progress').text('');
		wikimenuToast('success', wikimenu_done, 4000);

		return;
	}

	var rowIndx = file[position];
	var ligne = grid.getRowData({rowIndx: rowIndx});

	if (!ligne) {
		wikimenuApiRun(grid, file, position + 1);

		return;
	}

	$('#wikimenu_api_progress').text(
		wikimenu_running + ' ' + (position + 1) + '/' + file.length + ' — ' + (ligne.reference || '')
	);

	$.ajax({
		url: AjaxLinkAdminTranslations,
		type: 'POST',
		dataType: 'json',
		data: {
			action: 'phenyxwikimenuApiTranslate',
			menu_key: ligne.menu_key,
			target: lang_selected,
			ajax: true
		},
		success: function (data) {

			if (data && data.success) {
				wikimenuApplyRow(grid, rowIndx, data.name);
			} else {
				wikimenuToast('error', (data && data.message) ? data.message : 'API', 4000);
			}
		},
		complete: function () {
			wikimenuApiRun(grid, file, position + 1);
		}
	});
}

function wikimenuApiTranslate() {

	var grid = wikimenuGrid();

	if (!grid) {
		return;
	}

	if (lang_selected === wikimenu_ref_iso) {
		wikimenuToast('error', 'Langue cible = langue de reference', 3000);

		return;
	}

	var file = [];

	$.each(grid.getData(), function (i, row) {

		// fallback = 1 : l'entree n'a pas de libelle propre, elle affiche le
		// titre de sa page. Rien a traduire ici, cela se fait sur la page.
		if (parseInt(row.todo, 10) === 1 && parseInt(row.fallback, 10) !== 1) {
			file.push(i);
		}
	});

	if (!file.length) {
		wikimenuToast('info', wikimenu_no_row, 2500);

		return;
	}

	if (wikimenuSnapshot === null) {
		wikimenuSnapshotInit();
	}

	$('#wikimenu_api_all').prop('disabled', true);
	wikimenuApiRun(grid, file, 0);
}

$(function () {
	window.setTimeout(wikimenuSnapshotInit, 800);
});
