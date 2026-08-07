/**
 * Sauvegarde de la grille de traduction du wiki developpeur
 * (type 'phenyxwiki' de AdminTranslationsController, cf.
 * includes/specific_controllers/backend/AdminTranslationsController.php).
 *
 * Modele : translation_wiki_tabs.js, avec deux differences.
 *
 * 1. La grille cible est #grid_AdminPhenyxWiki, et chaque ligne poste
 *    id_wiki => JSON({meta_title, meta_description, meta_keywords, content}).
 *
 * 2. On ne poste QUE les lignes modifiees. Le champ content d'une page du wiki
 *    fait couramment 20 a 50 Ko : renvoyer les 36 pages a chaque
 *    enregistrement produirait un POST de plusieurs megaoctets, qui bute sur
 *    post_max_size avant meme d'atteindre PHP — et sans message clair, le
 *    serveur repondant par un corps vide.
 *
 *    La comparaison se fait contre un instantane pris au premier
 *    enregistrement suivant le chargement de la grille (cf. snapshot()). On ne
 *    peut pas se reposer sur grid.getChanges() : la grille est alimentee en
 *    'local' depuis dataModel.data, sans requestModel, et pqGrid n'y tient pas
 *    de liste de mise a jour exploitable.
 */

var phenyxwikiSnapshot = null;

function phenyxwikiGrid() {

	var $grid = $('#grid_AdminPhenyxWiki');

	if (!$grid.length || !$grid.pqGrid('instance')) {
		return null;
	}

	return $grid.pqGrid('instance');
}

/**
 * Empreinte d'une ligne, sur les quatre champs traduisibles uniquement.
 * ref_meta_title et state ne sont pas editables et n'ont pas a declencher
 * une ecriture.
 */
function phenyxwikiRowKey(row) {

	return JSON.stringify([
		row.meta_title || '',
		row.meta_description || '',
		row.meta_keywords || '',
		row.content || ''
	]);
}

/**
 * Instantane des lignes telles qu'elles ont ete chargees. Pris paresseusement
 * au premier appel : a l'execution de ce fichier, la grille n'est pas encore
 * construite (le script pqGrid est injecte dans #jsAdminPhenyxWiki juste
 * apres).
 */
function phenyxwikiSnapshotInit() {

	var grid = phenyxwikiGrid();

	if (!grid) {
		return;
	}

	phenyxwikiSnapshot = {};

	$.each(grid.getData(), function (k, row) {
		phenyxwikiSnapshot[row.id_wiki] = phenyxwikiRowKey(row);
	});
}

/**
 * Remplit formData avec les seules lignes dont un champ a bouge.
 * Renvoie le nombre de lignes ajoutees.
 */
function phenyxwikiCollect(formData) {

	var grid = phenyxwikiGrid();

	if (!grid) {
		console.log('no instance');

		return 0;
	}

	var count = 0;

	$.each(grid.getData(), function (k, row) {

		var key = phenyxwikiRowKey(row);

		// Instantane absent (premier enregistrement d'une ligne ajoutee a
		// chaud) : on considere la ligne comme modifiee, le PHP refera de
		// toute facon la comparaison contre la base avant d'ecrire.
		if (phenyxwikiSnapshot && phenyxwikiSnapshot[row.id_wiki] === key) {
			return;
		}

		formData.append(row.id_wiki, JSON.stringify({
			meta_title: row.meta_title,
			meta_description: row.meta_description,
			meta_keywords: row.meta_keywords,
			content: row.content
		}));

		count++;
	});

	return count;
}

function phenyxwikiToast(icon, title, timer) {

	Swal.fire({
		position: 'top-end',
		icon: icon,
		title: title,
		showConfirmButton: false,
		timer: timer || 4000
	});
}

function phenyxwikiSend(type, formData, stay) {

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

			if (data.success) {
				phenyxwikiToast('success', data.message, stay ? 2000 : 4000);

				// L'instantane devient l'etat enregistre : un second
				// enregistrement sans nouvelle modification ne renverra rien.
				if (stay) {
					phenyxwikiSnapshotInit();
				}

			} else {
				phenyxwikiToast('error', data.message);
			}
		},
		error: function () {
			phenyxwikiToast('error', 'POST refuse par le serveur (taille ?)');
		},
		complete: function () {

			$('#ajax_running').slideUp();

			if (stay) {
				$('#save_and_staytranslation_btn').prop('disabled', false);

				return;
			}

			$('#uperTranslate' + type).remove();
			$('#contentTranslate' + type).remove();
			$('#uperAdminTranslations a').trigger('click');
		}
	});
}

function savePhenyxwikiGridTranslations(type) {

	if (phenyxwikiSnapshot === null) {
		phenyxwikiSnapshotInit();
	}

	var formData = new FormData($('form#translations_form')[0]);
	var count = phenyxwikiCollect(formData);

	if (!count) {
		phenyxwikiToast('info', phenyxwiki_nothing_to_save, 2500);
		$('#uperTranslate' + type).remove();
		$('#contentTranslate' + type).remove();
		$('#uperAdminTranslations a').trigger('click');

		return;
	}

	phenyxwikiSend(type, formData, false);
}

function savePhenyxwikiGridTranslationsAndStay(type) {

	if (phenyxwikiSnapshot === null) {
		phenyxwikiSnapshotInit();
	}

	var formData = new FormData($('form#translations_form')[0]);
	var count = phenyxwikiCollect(formData);

	if (!count) {
		phenyxwikiToast('info', phenyxwiki_nothing_to_save, 2500);

		return;
	}

	$('#save_and_staytranslation_btn').prop('disabled', true);
	phenyxwikiSend(type, formData, true);
}

/*
 * L'instantane est pris une fois la grille construite. On laisse un delai :
 * le script pqGrid est injecte dans #jsAdminPhenyxWiki par le meme flux
 * Ajax que ce fichier, et s'execute apres lui.
 */
$(function () {
	window.setTimeout(phenyxwikiSnapshotInit, 800);
});

/* ===========================================================================
 * Traduction automatique via l'API Google
 *
 * Les boutons de la barre remplissent la grille ; ils n'ecrivent rien. C'est
 * l'enregistrement qui decide, pour deux raisons : une traduction machine se
 * relit, et l'operation etant lancee page par page depuis le navigateur, une
 * coupure en cours de route ne doit pas laisser la moitie des pages ecrites.
 *
 * Les appels sont SEQUENTIELS, une page a la fois. En parallele, on saturerait
 * le quota Google et on perdrait la progression lisible ; un chapitre demande
 * deja plusieurs dizaines d'appels cote serveur.
 * ========================================================================= */

function phenyxwikiProgress(texte) {

	$('#phenyxwiki_api_progress').text(texte || '');
}

function phenyxwikiApiButtons(disabled) {

	$('#phenyxwiki_api_metas').prop('disabled', disabled);
	$('#phenyxwiki_api_all').prop('disabled', disabled);
	$('#phenyxwiki_api_row').prop('disabled', disabled);
}

/**
 * Reecrit une ligne de la grille.
 *
 * On passe par deleteRow puis addRow au meme index, comme le fait deja
 * apiTranslate() dans le script de traduction generique : c'est le seul motif
 * eprouve dans ce projet pour rafraichir une cellule apres coup.
 */
function phenyxwikiApplyRow(grid, rowIndx, payload, scope) {

	var courant = grid.getRowData({rowIndx: rowIndx});

	if (!courant) {
		return;
	}

	var ligne = $.extend({}, courant);

	ligne.meta_title = payload.meta_title;
	ligne.meta_description = payload.meta_description;
	ligne.meta_keywords = payload.meta_keywords;

	if (scope === 'all' && typeof payload.content === 'string') {
		ligne.content = payload.content;
		ligne.content_size = payload.content.length;
	}

	// La ligne cesse d'etre "a traduire" des qu'elle est remplie : le
	// libelle vient de la meme source que la colonne technique pour qu'ils
	// ne divergent pas.
	ligne.todo = 0;
	ligne.state = phenyxwiki_translated_label;

	grid.deleteRow({rowIndx: rowIndx});
	grid.addRow({rowData: ligne, rowIndx: rowIndx});
}

/**
 * Traite une file d'index de lignes, une par une.
 */
function phenyxwikiApiRun(grid, file, scope, position) {

	if (position >= file.length) {
		phenyxwikiApiButtons(false);
		phenyxwikiProgress('');
		phenyxwikiToast('success', phenyxwiki_done, 4000);

		return;
	}

	var rowIndx = file[position];
	var ligne = grid.getRowData({rowIndx: rowIndx});

	if (!ligne) {
		phenyxwikiApiRun(grid, file, scope, position + 1);

		return;
	}

	phenyxwikiProgress(
		phenyxwiki_running + ' ' + (position + 1) + '/' + file.length + ' — ' + (ligne.ref_meta_title || '')
	);

	$.ajax({
		url: AjaxLinkAdminTranslations,
		type: 'POST',
		dataType: 'json',
		data: {
			action: 'phenyxwikiApiTranslate',
			id_wiki: ligne.id_wiki,
			target: lang_selected,
			scope: scope,
			ajax: true
		},
		success: function (data) {

			if (data && data.success) {
				phenyxwikiApplyRow(grid, rowIndx, data, scope);
			} else {
				phenyxwikiToast('error', (data && data.message) ? data.message : 'API', 5000);
			}
		},
		error: function () {
			// Une page en echec ne doit pas arreter la file : le plus probable
			// est un depassement de temps sur un chapitre long, les suivants
			// passeront.
			phenyxwikiToast('error', 'HTTP ' + (ligne.ref_meta_title || ''), 3000);
		},
		complete: function () {
			phenyxwikiApiRun(grid, file, scope, position + 1);
		}
	});
}

/**
 * @param scope 'metas' pour les trois champs courts, 'all' pour y ajouter le
 *              contenu — plusieurs dizaines d'appels par page, d'ou la
 *              confirmation.
 */
function phenyxwikiApiTranslate(scope) {

	var grid = phenyxwikiGrid();

	if (!grid) {
		return;
	}

	if (lang_selected === phenyxwiki_ref_iso) {
		phenyxwikiToast('error', 'Langue cible = langue de reference', 3000);

		return;
	}

	var file = [];

	$.each(grid.getData(), function (i, row) {

		if (parseInt(row.todo, 10) === 1) {
			file.push(i);
		}
	});

	if (!file.length) {
		phenyxwikiToast('info', phenyxwiki_no_row, 2500);

		return;
	}

	if (scope === 'all' && !window.confirm(phenyxwiki_confirm_all)) {
		return;
	}

	if (phenyxwikiSnapshot === null) {
		phenyxwikiSnapshotInit();
	}

	phenyxwikiApiButtons(true);
	phenyxwikiApiRun(grid, file, scope, 0);
}

/**
 * Traduit la seule ligne selectionnee, contenu compris. C'est la porte
 * d'entree recommandee : on verifie le rendu sur une page avant de lancer
 * l'ensemble.
 */
function phenyxwikiApiTranslateRow() {

	var grid = phenyxwikiGrid();

	if (!grid) {
		return;
	}

	var selection = grid.SelectRow().getSelection();

	if (!selection || !selection.length) {
		phenyxwikiToast('info', 'Selectionnez une ligne', 2500);

		return;
	}

	if (phenyxwikiSnapshot === null) {
		phenyxwikiSnapshotInit();
	}

	phenyxwikiApiButtons(true);
	phenyxwikiApiRun(grid, [selection[0].rowIndx], 'all', 0);
}
