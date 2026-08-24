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

/* Lignes touchees depuis le chargement, adressees par menu_key. */
var wikimenuModifiees = {};

/* Passe a true une fois la grille trouvee et l'instantane pris. */
var wikimenuPret = false;

function wikimenuMarquer(cle) {

	if (cle) {
		wikimenuModifiees[cle] = true;
	}
}

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
		var modifiee = wikimenuModifiees[row.menu_key] === true;

		if (!modifiee && wikimenuSnapshot) {
			modifiee = (wikimenuSnapshot[row.menu_key] !== valeur);
		}

		if (!modifiee) {
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

				wikimenuModifiees = {};

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

/**
 * Garde commune aux deux boutons d'enregistrement.
 *
 * On ne prend PLUS l'instantane ici : le faire reviendrait a photographier les
 * modifications qu'on cherche a detecter, et la collecte ne trouverait jamais
 * rien a poster. Si la photo manque, on le dit.
 */
function wikimenuPretOuPlainte() {

	if (wikimenuPret) {
		return true;
	}

	wikimenuInit(0);

	if (wikimenuPret) {
		return true;
	}

	wikimenuToast('error', 'Grille non instrumentee : rechargez l onglet.', 5000);

	return false;
}

function saveWikimenuGridTranslations(type) {

	if (!wikimenuPretOuPlainte()) {
		return;
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

	if (!wikimenuPretOuPlainte()) {
		return;
	}

	var formData = new FormData($('form#translations_form')[0]);

	if (!wikimenuCollect(formData)) {
		wikimenuToast('info', wikimenu_nothing_to_save, 2500);

		return;
	}

	wikimenuSend(type, formData, true);
}

/* ---------------------------------------------------------------- API ---- */

/*
 * Meme correction que dans translation_phenyxwiki.js : on adresse par menu_key
 * et non par index de vue.
 *
 * deleteRow() suivi de addRow() remaniait la collection a chaque tour, si bien
 * qu'en traitement en serie les index memorises au depart ne designaient plus
 * les memes lignes des le deuxieme. On modifie desormais l'objet de donnees en
 * place — getData() rend le modele vivant — et on rafraichit l'affichage.
 */

/** Retrouve la ligne vivante correspondant a une cle « m:<id> » ou « c:<id> ». */
function wikimenuFindRow(grid, cle) {

	var trouve = null;

	$.each(grid.getData(), function (i, row) {

		if (row.menu_key === cle) {
			trouve = row;

			return false;
		}
	});

	return trouve;
}

function wikimenuRefresh(grid) {

	try {

		if (typeof grid.refreshDataAndView === 'function') {
			grid.refreshDataAndView();

			return;
		}

		if (typeof grid.refreshView === 'function') {
			grid.refreshView();

			return;
		}

		grid.refresh();

	} catch (e) {
		console.log('wikimenu : refresh impossible', e);
	}
}

function wikimenuApplyRow(grid, cle, nom) {

	var ligne = wikimenuFindRow(grid, cle);

	if (!ligne) {
		return;
	}

	ligne.name = nom;
	ligne.todo = 0;
	ligne.state = wikimenu_translated_label;

	// La ligne est modifiee en place : rien dans la grille ne le signalera,
	// c'est ici qu'on l'inscrit au registre.
	wikimenuMarquer(cle);

	wikimenuRefresh(grid);
}

function wikimenuApiRun(grid, file, position) {

	if (position >= file.length) {
		$('#wikimenu_api_all').prop('disabled', false);
		$('#wikimenu_api_progress').text('');
		wikimenuToast('success', wikimenu_done, 4000);

		return;
	}

	// file contient des menu_key, pas des index de vue.
	var cle = file[position];
	var ligne = wikimenuFindRow(grid, cle);

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
				wikimenuApplyRow(grid, cle, data.name);
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
			file.push(row.menu_key);
		}
	});

	if (!file.length) {
		wikimenuToast('info', wikimenu_no_row, 2500);

		return;
	}

	// Ici l'instantane peut encore etre pris sans risque : il precede les
	// traductions qu'on s'apprete a appliquer.
	wikimenuInit(0);

	$('#wikimenu_api_all').prop('disabled', true);
	wikimenuApiRun(grid, file, 0);
}

/* ===========================================================================
 * Mise en place, une fois la grille construite
 *
 * Le script pqGrid est injecte par le meme flux Ajax que ce fichier et
 * s'execute apres lui : au moment ou ces lignes tournent, la grille n'existe
 * pas encore. Un delai fixe de 800 ms tenait lieu d'attente ; il suffisait
 * qu'elle tarde pour que l'instantane ne soit jamais pris, et il l'etait alors
 * au moment d'enregistrer — donc apres les modifications, d'ou un
 * « rien a enregistrer » systematique. On attend reellement.
 * ========================================================================= */

function wikimenuEcouteEdition() {

	// Selon la version de pqGrid, l'edition d'une cellule remonte par
	// 'cellSave' ou par 'change'. Un branchement qui ne se declenche jamais ne
	// coute rien, l'instantane rattrapant le cas.
	$('#grid_AdminWikiMenu')
		.off('.wikimenu')
		.on('cellSave.wikimenu', function (evt, ui) {

			if (ui && ui.rowData) {
				wikimenuMarquer(ui.rowData.menu_key);
			}
		})
		.on('change.wikimenu', function (evt, ui) {

			$.each((ui && ui.updateList) || [], function (i, maj) {

				if (maj && maj.rowData) {
					wikimenuMarquer(maj.rowData.menu_key);
				}
			});
		});
}

function wikimenuInit(essais) {

	if (wikimenuPret) {
		return;
	}

	if (!wikimenuGrid()) {

		if (essais > 0) {
			window.setTimeout(function () {
				wikimenuInit(essais - 1);
			}, 250);
		}

		return;
	}

	wikimenuSnapshotInit();
	wikimenuEcouteEdition();

	wikimenuPret = true;
}

$(function () {
	// 60 essais espaces de 250 ms : quinze secondes de patience.
	wikimenuInit(60);
});
