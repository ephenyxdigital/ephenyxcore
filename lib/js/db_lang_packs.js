/**
 * Génération des packs de langue « base de données » (ephenyx.io master).
 * Déclenché depuis le bouton de la toolbar d'AdminLanguages (controller
 * spécifique). Exporte toutes les langues actives vers packs/database/<version>/.
 */
function generateDbLanguagePacks() {
    Swal.fire({
        title: 'Générer les packs de langue (BDD)',
        html:
            '<p style="font-size:13px;text-align:left;">Génère les packs <b>base de données</b> de toutes les langues actives ' +
            'd\'ephenyx.io dans <code>packs/database/&lt;version&gt;/</code> (version courante du cœur), ' +
            'puis régénère <code>index.json</code>.</p>' +
            '<p style="font-size:12px;text-align:left;color:#888;">Astuce : traduisez d\'abord les tables de la langue ' +
            '(menu contextuel « Traduire les tables ») pour des packs complets.</p>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Générer les packs',
        cancelButtonText: 'Annuler'
    }).then(function (res) {
        if (!res.value) {
            return;
        }
        $('#content').addClass('page-is-changing');
        $.ajax({
            type: 'POST',
            url: AjaxLinkAdminLanguages,
            data: {
                action: 'GenerateDbPacks',
                ajax: true
            },
            dataType: 'json',
            success: function (data) {
                if (data && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Packs générés',
                        html: data.message
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Échec', text: (data && data.message) ? data.message : 'Erreur inconnue' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Erreur réseau pendant la génération' });
            },
            complete: function () {
                $('#content').removeClass('page-is-changing');
            }
        });
    });
}
