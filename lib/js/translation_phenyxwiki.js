/**
 * 09/09/2026 : les pages viennent de ph_elearning (cle id_elearning_page,
 * grille #grid_TranslateEducationPages) — ph_wiki est retire.
 * Sauvegarde de la grille de traduction du wiki developpeur
 * (type 'phenyxwiki' de AdminTranslationsController, cf.
 * includes/specific_controllers/backend/AdminTranslationsController.php).
 *
 * Modele : translation_wiki_tabs.js, avec deux differences.
 *
 * 1. La grille cible est #grid_TranslateEducationPages, et chaque ligne poste
 *    id_elearning_page => JSON({meta_title, meta_description, meta_keywords, content}).
 *
 * 2. On ne poste QUE les lignes modifiees. Le champ content d'une page du wiki
 *    fait couramment 20 a 50 Ko : renvoyer les 36 pages a chaque
 *    enregistrement produirait un POST de plusieurs megaoctets, qui bute sur
 *    post_max_size avant meme d'atteindre PHP — et sans message clair, le
 *    serveur repondant par un corps vide.
 *
 *    Deux mecanismes concourent a reperer ces lignes :
 *
 *    - un instantane pris DES QUE la grille existe (cf. phenyxwikiInit) ;
 *    - un registre des lignes touchees, alimente par la traduction automatique
 *      et par l'edition manuelle.
 *
 *    L'un rattrape l'autre. On ne peut pas se reposer sur grid.getChanges() :
 *    la grille est alimentee en 'local' depuis dataModel.data, sans
 *    requestModel, et pqGrid n'y tient pas de liste de mise a jour exploitable.
 *
 * ─── Correction du 18 aout 2026 ───────────────────────────────────────────
 *
 * L'instantane etait pris paresseusement : s'il etait encore null au moment de
 * l'enregistrement, savePhenyxwikiGridTranslations() le prenait sur place —
 * c'est-a-dire APRES les modifications. La photo montrait alors exactement
 * l'etat courant, la comparaison ne trouvait aucun ecart, et l'utilisateur
 * lisait "Aucune page modifiee a enregistrer" alors qu'il venait de tout
 * traduire. Le declencheur : le setTimeout de 800 ms qui prenait la photo
 * echouait silencieusement si la grille n'etait pas encore construite, et
 * personne ne repassait derriere.
 *
 * L'instantane est desormais pris par une attente active, et jamais au moment
 * d'enregistrer.
 */

var phenyxwikiSnapshot = null;

/* Lignes touchees depuis le chargement, adressees par id_elearning_page. */
var phenyxwikiModifiees = {};

/* Passe a true une fois la grille trouvee et l'instrumentation posee. */
var phenyxwikiPret = false;

function phenyxwikiGrid() {

	var $grid = $('#grid_TranslateEducationPages');

	if (!$grid.length || !$grid.pqGrid('instance')) {
		return null;
	}

	return $grid.pqGrid('instance');
}

/**
 * Empreinte d'une ligne, sur les quatre champs traduisibles uniquement.
 * ref_meta_title, state et content_preview ne sont pas editables et n'ont pas
 * a declencher une ecriture.
 */
function phenyxwikiRowKey(row) {

	return JSON.stringify([
		row.meta_title || '',
		row.meta_description || '',
		row.meta_keywords || '',
		row.content || ''
	]);
}

function phenyxwikiMarquer(idWiki) {

	var id = parseInt(idWiki, 10);

	if (id) {
		phenyxwikiModifiees[id] = true;
	}
}

/** Instantane des lignes telles qu'elles ont ete chargees. */
function phenyxwikiSnapshotInit() {

	var grid = phenyxwikiGrid();

	if (!grid) {
		return false;
	}

	phenyxwikiSnapshot = {};

	$.each(grid.getData(), function (k, row) {
		phenyxwikiSnapshot[row.id_elearning_page] = phenyxwikiRowKey(row);
	});

	return true;
}

/**
 * Remplit formData avec les seules lignes dont un champ a bouge.
 * Renvoie le nombre de lignes ajoutees.
 */
function phenyxwikiCollect(formData) {

	var grid = phenyxwikiGrid();

	if (!grid) {
		console.log('phenyxwiki : pas d instance de grille');

		return 0;
	}

	var count = 0;

	$.each(grid.getData(), function (k, row) {

		var id = parseInt(row.id_elearning_page, 10);
		var modifiee = phenyxwikiModifiees[id] === true;

		if (!modifiee && phenyxwikiSnapshot) {
			// Instantane absent pour cette ligne (ajoutee a chaud) : on la
			// considere comme modifiee, le PHP refera de toute facon la
			// comparaison contre la base avant d'ecrire.
			modifiee = (phenyxwikiSnapshot[row.id_elearning_page] !== phenyxwikiRowKey(row));
		}

		if (!modifiee) {
			return;
		}

		formData.append(row.id_elearning_page, JSON.stringify({
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

				// L'etat enregistre devient la nouvelle reference : un second
				// enregistrement sans nouvelle modification ne renverra rien.
				phenyxwikiModifiees = {};

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

/**
 * Garde commune aux deux boutons d'enregistrement.
 *
 * On ne prend PLUS l'instantane ici : le faire reviendrait a photographier les
 * modifications qu'on cherche a detecter. Si la photo manque, on le dit.
 */
function phenyxwikiPretOuPlainte() {

	if (phenyxwikiPret) {
		return true;
	}

	phenyxwikiInit(0);

	if (phenyxwikiPret) {
		return true;
	}

	phenyxwikiToast(
		'error',
		'Grille non instrumentee : rechargez l onglet avant d enregistrer.',
		5000
	);

	return false;
}

function savePhenyxwikiGridTranslations(type) {

	if (!phenyxwikiPretOuPlainte()) {
		return;
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

	if (!phenyxwikiPretOuPlainte()) {
		return;
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

/* ===========================================================================
 * Mise en place, une fois la grille construite
 *
 * Le script pqGrid est injecte dans #jsAdminPhenyxWiki par le meme flux Ajax
 * que ce fichier, et s'execute apres lui : au moment ou ces lignes tournent,
 * #grid_TranslateEducationPages n'existe pas encore. Un delai fixe de 800 ms tenait
 * lieu d'attente ; il suffisait qu'un chapitre soit long a rendre pour que la
 * photo ne soit jamais prise. On attend donc reellement, par essais espaces.
 * ========================================================================= */

function phenyxwikiEcouteEdition() {

	// Ceinture et bretelles : selon la version de pqGrid, l'edition d'une
	// cellule remonte par 'cellSave' ou par 'change'. Un branchement qui ne
	// se declenche jamais ne coute rien, l'instantane rattrapant le cas.
	$('#grid_TranslateEducationPages')
		.off('.phenyxwiki')
		.on('cellSave.phenyxwiki', function (evt, ui) {

			if (ui && ui.rowData) {
				phenyxwikiMarquer(ui.rowData.id_elearning_page);
			}
		})
		.on('change.phenyxwiki', function (evt, ui) {

			$.each((ui && ui.updateList) || [], function (i, maj) {

				if (maj && maj.rowData) {
					phenyxwikiMarquer(maj.rowData.id_elearning_page);
				}
			});
		});
}

function phenyxwikiInit(essais) {

	if (phenyxwikiPret) {
		return;
	}

	var grid = phenyxwikiGrid();

	if (!grid) {

		if (essais > 0) {
			window.setTimeout(function () {
				phenyxwikiInit(essais - 1);
			}, 250);
		}

		return;
	}

	phenyxwikiSnapshotInit();
	phenyxwikiInstallApercu(grid);
	phenyxwikiEcouteEdition();

	phenyxwikiPret = true;
}

$(function () {
	// 60 essais espaces de 250 ms : quinze secondes de patience, largement de
	// quoi couvrir le rendu du plus long des chapitres.
	phenyxwikiInit(60);
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

/*
 * ═══ POURQUOI ON ADRESSE PAR id_elearning_page ET NON PAR INDEX ═══
 *
 * Correction du 18 aout 2026, apres le constat qui a mis sur la voie : page
 * par page tout fonctionne, en serie certaines lignes reviennent fausses.
 *
 * La file etait construite avec les index de grid.getData(), puis chaque ligne
 * etait reecrite par deleteRow() suivi de addRow(). Or `rowIndx` designe une
 * position dans la VUE — elle bouge avec le tri et le filtrage — et surtout
 * deleteRow/addRow remanie la collection a chaque tour. Au deuxieme tour, les
 * index memorises au depart ne designaient plus les memes lignes : la
 * traduction d'une page atterrissait sur sa voisine.
 *
 * D'ou l'ecart avec le traitement unitaire, ou il n'y a qu'un seul tour et
 * donc aucun decalage possible.
 *
 * On adresse desormais par id_elearning_page, qui ne bouge jamais, et on modifie l'objet
 * de donnees EN PLACE — getData() rend le modele vivant, pas une copie — au
 * lieu de supprimer puis reinserer.
 */

/** Retrouve la ligne vivante correspondant a un id_elearning_page. */
function phenyxwikiFindRow(grid, idWiki) {

	var trouve = null;

	$.each(grid.getData(), function (i, row) {

		if (parseInt(row.id_elearning_page, 10) === parseInt(idWiki, 10)) {
			trouve = row;

			return false;
		}
	});

	return trouve;
}

/** Rafraichit l'affichage sans toucher au nombre de lignes. */
function phenyxwikiRefresh(grid) {

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
		// Un rafraichissement rate ne doit pas interrompre la file : les
		// donnees sont deja a jour dans le modele, seul l'affichage attend.
		console.log('phenyxwiki : refresh impossible', e);
	}
}

function phenyxwikiApplyRow(grid, idWiki, payload, scope) {

	var ligne = phenyxwikiFindRow(grid, idWiki);

	if (!ligne) {
		return;
	}

	ligne.meta_title = payload.meta_title;
	ligne.meta_description = payload.meta_description;
	ligne.meta_keywords = payload.meta_keywords;

	// Les trois metas viennent d'etre ecrites, quelle que soit la portee.
	ligne.todo_metas = 0;

	if (scope === 'all' && typeof payload.content === 'string') {
		ligne.content = payload.content;
		ligne.content_size = payload.content.length;

		if (typeof payload.content_preview === 'string') {
			ligne.content_preview = payload.content_preview;
		}

		/*
		 * 'todo' est l'etat de la PAGE, contenu compris : il ne tombe que sur
		 * une traduction complete. Le faire tomber apres les seules metas
		 * aurait sorti la page de la file du bouton "tout, contenu compris" —
		 * elle serait restee avec un contenu francais et un statut "Traduit".
		 *
		 * Le libelle vient de la meme source que la colonne technique pour
		 * qu'ils ne divergent pas.
		 */
		ligne.todo = 0;
		ligne.state = phenyxwiki_translated_label;
	}

	// La ligne est modifiee en place : rien dans la grille ne le signalera,
	// c'est ici et nulle part ailleurs qu'on l'inscrit au registre.
	phenyxwikiMarquer(idWiki);

	phenyxwikiRefresh(grid);
}

/**
 * Greffe le rendu de l'apercu sur la colonne content_preview.
 *
 * Le render ne peut pas venir du PHP : ParamGrid emet le colModel tel quel,
 * une fonction declaree cote PHP arriverait en chaine de caracteres et serait
 * ignoree. On l'ajoute donc apres coup.
 *
 * Il ne touche qu'a l'affichage : ui.rowData.content reste intact, donc la
 * collecte et l'API continuent de lire le texte integral.
 */
function phenyxwikiInstallApercu(grid) {

	if (!grid || typeof grid.option !== 'function') {
		return;
	}

	var colModel = grid.option('colModel');
	var trouve = false;

	$.each(colModel, function (i, col) {

		if (col.dataIndx !== 'content_preview') {
			return;
		}

		trouve = true;

		col.render = function (ui) {

			var texte = (ui.rowData && typeof ui.rowData.content_preview === 'string')
				? ui.rowData.content_preview
				: '';

			if (texte === '') {
				return '';
			}

			// Echappement par le DOM : le contenu wiki porte des chevrons. Le
			// guillemet double, lui, n'est pas echappe par .html() et casserait
			// l'attribut title — d'ou le remplacement explicite.
			var html = $('<div/>').text(texte).html();
			var infobulle = html.replace(/"/g, '&quot;');

			return '<div class="pw-apercu" title="' + infobulle + '">'
				+ html.replace(/\n/g, '<br>')
				+ '</div>';
		};
	});

	if (!trouve) {
		return;
	}

	grid.option('colModel', colModel);
	phenyxwikiRefresh(grid);
}

/**
 * Traite une file d'id_elearning_page, une page a la fois.
 */
function phenyxwikiApiRun(grid, file, scope, position) {

	if (position >= file.length) {
		phenyxwikiApiButtons(false);
		phenyxwikiProgress('');
		phenyxwikiToast('success', phenyxwiki_done, 4000);

		return;
	}

	// file contient des id_elearning_page, pas des index de vue : voir le commentaire
	// au-dessus de phenyxwikiFindRow().
	var idWiki = file[position];
	var ligne = phenyxwikiFindRow(grid, idWiki);

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
			id_elearning_page: ligne.id_elearning_page,
			target: lang_selected,
			scope: scope,
			ajax: true
		},
		success: function (data) {

			if (data && data.success) {
				phenyxwikiApplyRow(grid, idWiki, data, scope);

				/*
				 * Le serveur signale les paquets refuses par l'API. Sans cela,
				 * un contenu revenu en francais passait pour traduit : on
				 * enregistrait, la page etait marquee faite, et personne ne
				 * s'en apercevait avant la relecture.
				 */
				if (data.warning) {
					phenyxwikiToast('warning', data.warning, 7000);
				}

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

			/*
			 * Pause entre deux pages.
			 *
			 * Une page de contenu demande plusieurs dizaines d'appels a l'API
			 * la ou les trois metas n'en demandent qu'un. Les enchainer sans
			 * repit sature le quota par minute de Google, qui repond alors 403
			 * userRateLimitExceeded — et TranslateApi etant fail-open, le texte
			 * revient en francais sans erreur visible.
			 *
			 * 800 ms pour le contenu, 200 ms pour les seules metas.
			 */
			var repit = (scope === 'all') ? 400 : 200;

			window.setTimeout(function () {
				phenyxwikiApiRun(grid, file, scope, position + 1);
			}, repit);
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

	/*
	 * Chaque portee a sa propre file.
	 *
	 * 'metas' se fonde sur todo_metas — vide ou encore identique au francais,
	 * champ par champ — et non sur 'todo', qui decrit l'etat de la page entiere.
	 * Une page au contenu deja traduit sortait sinon de la file alors que ses
	 * trois metas etaient restees en francais : c'est ce qui rendait le bouton
	 * "champs meta manquants" apparemment inoperant.
	 */
	var champ = (scope === 'metas') ? 'todo_metas' : 'todo';
	var file = [];

	$.each(grid.getData(), function (i, row) {

		if (parseInt(row[champ], 10) === 1) {
			file.push(row.id_elearning_page);
		}
	});

	if (!file.length) {
		phenyxwikiToast('info', phenyxwiki_no_row, 2500);

		return;
	}

	if (scope === 'all' && !window.confirm(phenyxwiki_confirm_all)) {
		return;
	}

	// Ici l'instantane peut encore etre pris sans risque : il precede les
	// traductions qu'on s'apprete a appliquer.
	phenyxwikiInit(0);

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

	// Meme voie que le traitement en serie : on resout l'id_elearning_page tout de
	// suite, et phenyxwikiApiRun() ne connait plus que des identifiants.
	var choisie = grid.getRowData({rowIndx: selection[0].rowIndx});

	if (!choisie) {
		phenyxwikiToast('info', 'Selectionnez une ligne', 2500);

		return;
	}

	phenyxwikiInit(0);

	phenyxwikiApiButtons(true);
	phenyxwikiApiRun(grid, [choisie.id_elearning_page], 'all', 0);
}
