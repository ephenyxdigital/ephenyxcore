/**
 * Sauvegarde de la grille de traduction du wiki de l'assistant BO (type
 * 'wiki' de AdminTranslationsController, cf. includes/specific_controllers/
 * backend/AdminTranslationsController.php — ephenyx.io uniquement).
 *
 * Modelé sur vendor/ephenyxdigital/quantumcore/lib/js/translation_tabs.js
 * (saveGridTranslations()/saveGridTranslationsAndStay()), mais avec deux
 * différences :
 *  - la grille cible est #grid_AdminAssistantTopic (pas #grid_AdminBackTab) ;
 *  - chaque ligne poste un objet JSON {keywords, answer, quick_replies}
 *    (id_phenyx_assistant_topic => JSON) plutôt qu'une simple chaîne
 *    (id_back_tab => translation), puisqu'un topic a trois champs
 *    traduisibles au lieu d'un seul. Cf. ajaxProcessWikiGridTranslate()
 *    côté PHP, qui json_decode() chaque valeur postée.
 */

function saveWikiGridTranslations(type) {

	var formData = new FormData($('form#translations_form')[0]);

	if ($('#grid_AdminAssistantTopic').pqGrid('instance')) {
		var grid = $('#grid_AdminAssistantTopic').pqGrid('instance');
		var data = grid.getData();
		$.each(data, function(k, value) {
			formData.append(value.id_phenyx_assistant_topic, JSON.stringify({
				keywords: value.keywords,
				answer: value.answer,
				quick_replies: value.quick_replies
			}));
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
		dataType: 'json',
		success: function(data) {
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
		complete: function() {
			$('#uperTranslate' + type).remove();
			$('#contentTranslate' + type).remove();
			$('#uperAdminTranslations a').trigger('click');
		}
	});
}

function saveWikiGridTranslationsAndStay(type) {

	var formData = new FormData($('form#translations_form')[0]);

	if ($('#grid_AdminAssistantTopic').pqGrid('instance')) {
		var grid = $('#grid_AdminAssistantTopic').pqGrid('instance');
		var data = grid.getData();
		$.each(data, function(k, value) {
			formData.append(value.id_phenyx_assistant_topic, JSON.stringify({
				keywords: value.keywords,
				answer: value.answer,
				quick_replies: value.quick_replies
			}));
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
		dataType: 'json',
		success: function(data) {
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
