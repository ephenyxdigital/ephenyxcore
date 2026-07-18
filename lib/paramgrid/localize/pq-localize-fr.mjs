import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.fr = {
            strAND: "ET",
            strAddColLeft: "Ajouter une colonne à gauche",
            strAddColRight: "Ajouter une colonne à droite",
            strAddColumn: "Ajouter une colonne",
            strAddRow: "Ajouter une ligne",
            strAddRowAbove: "Ajouter une ligne ci-dessus",
            strAddRowBelow: "Ajouter une ligne ci-dessous",
            strAddRows: "Ajouter {0} lignes",
            strAlign: {
                bottom: "Aligner en bas",
                center: "Aligner au centre",
                left: "Aligner à gauche",
                right: "Aligner à droite",
                top: "Aligner en haut"
            },
            strAlignH: "Alignement horizontal",
            strAlignV: "Alignement vertical",
            strApply: "Postuler",
            strBlanks: "(Blancs)",
            strBold: "Texte en gras",
            strBorder: "Frontière",
            strBorderColor: "Couleur de la bordure",
            strBorderStyle: "Style de bordure",
            strBorders: {
                all: "Toutes les frontières",
                bottom: "Bordure inférieure",
                horizontal: "Bordure horizontale",
                inner: "Bordure intérieure",
                left: "Bordure gauche",
                none: "Pas de frontière",
                outer: "Frontière extérieure",
                right: "Bordure droite",
                top: "Bordure supérieure",
                vertical: "Bordure verticale"
            },
            strCancel: "Annuler",
            strClear: "Claire",
            strClearColor: "Couleur claire",
            strClearFilter: "Effacer le filtre",
            strClearText: "Texte clair",
            strCollapse: "Réduire le panneau",
            strComment: "Commentaire",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Aucun --",
                begin: "Commence par",
                between: "Entre les deux",
                contain: "Contient",
                empty: "Vide",
                end: "Se termine par",
                equal: "Égal",
                great: "Plus grand que",
                gte: "Supérieur ou égal",
                less: "Moins de",
                lte: "Inférieur ou égal",
                notbegin: "Ne commence pas par",
                notcontain: "Ne contient pas",
                notempty: "Pas vide",
                notend: "Ne se termine pas par",
                notequal: "Pas égal",
                range: "[Gamme]",
                regexp: "Expression régulière"
            },
            strCopy: "Copier",
            strCut: "Couper",
            strDelete: "Supprimer",
            strDeleteColumn: "Supprimer la colonne",
            strDeleteRow: "Supprimer la ligne",
            strDownloadXlsx: "Télécharger le fichier Excel",
            strEdit: "Edit",
            strExitFullScreen: "Quitter le plein écran",
            strExpand: "Agrandir le panneau",
            strExport: "Exporter des données",
            strFillColor: "Couleur de remplissage",
            strFontFamily: "Famille de polices",
            strFontSize: "Taille de la police",
            strFormat: "Formater les cellules",
            strFormatMenu: {
                Accounting: "Comptabilité",
                Currency: "Devise",
                Custom: "Personnalisé",
                DateTime: "Date Heure",
                Fixed: "Décimal",
                Fraction: "Fraction",
                FullDate: "Date complète",
                General: "Général",
                Int: "Entier",
                LongDate: "Date longue",
                MediumDate: "Date moyenne",
                Note: "Remarque : les formats avec * changent en fonction des paramètres régionaux de l'utilisateur.",
                Percent: "Pourcentage",
                Scientific: "Scientifique",
                ShortDate: "Date courte",
                Standard: "Norme",
                Text: "Texte",
                Time: "Temps"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Renvoie la valeur absolue d'un nombre.La valeur absolue d'un nombre est le nombre sans son signe."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Renvoie l'arccosinus, ou cosinus inverse, d'un nombre.L'arc cosinus est l'angle dont le cosinus est un nombre.L'angle renvoyé est donné en radians dans la plage de 0 (zéro) à pi."
                ],
                AND: [
                    "ET(logique1, [logique2], ...)",
                    "Renvoie TRUE si tous ses arguments sont évalués à TRUE et renvoie FALSE si un ou plusieurs arguments sont évalués à FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Renvoie l'arc sinus, ou sinus inverse, d'un nombre.L'arc sinus est l'angle dont le sinus est un nombre.L'angle renvoyé est donné en radians dans la plage -pi/2 à pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Renvoie l'arctangente, ou tangente inverse, d'un nombre.L'arctangente est l'angle dont la tangente est un nombre.L'angle renvoyé est donné en radians dans la plage -pi/2 à pi/2."
                ],
                AVERAGE: [
                    "MOYENNE(numéro1, [numéro2], ...)",
                    "Renvoie la moyenne (moyenne arithmétique) des arguments.Par exemple, si la plage A1:A20 contient des nombres, la formule =AVERAGE(A1:A20) renvoie la moyenne de ces nombres."
                ],
                AVERAGEIF: [
                    "MOYENNEIF(plage, critères, [average_range])",
                    "Renvoie la moyenne (moyenne arithmétique) de toutes les cellules d'une plage qui répondent à un critère donné."
                ],
                AVERAGEIFS: [
                    "MOYENNEIFS(plage_moyenne, plage_critères1, critères1, [plage_critères2, critères2], ...)",
                    "Renvoie la moyenne (moyenne arithmétique) de toutes les cellules répondant à plusieurs critères."
                ],
                CEILING: [
                    "PLAFOND(nombre, signification)",
                    "Renvoie le nombre arrondi à la valeur supérieure, à partir de zéro, au multiple significatif le plus proche.Par exemple, si vous souhaitez éviter d'utiliser des centimes dans vos prix et que votre produit coûte 4,42 $, utilisez la formule =PLAFOND(4.42,0.05) pour arrondir les prix au nickel le plus proche."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Renvoie le caractère spécifié par un nombre."
                ],
                CHOOSE: [
                    "CHOISIR(num_index, valeur1, [valeur2], ...)",
                    "Si index_num est 1, CHOOSE renvoie valeur1 ;si c'est 2, CHOISIR renvoie valeur2 ;et ainsi de suite."
                ],
                CODE: [
                    "CODE(text)",
                    "Renvoie un code numérique pour le premier caractère d'une chaîne de texte."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Renvoie la référence de la cellule dans laquelle la fonction COLONNE apparaît."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Renvoie le nombre de colonnes dans un tableau ou une référence."
                ],
                CONCATENATE: [
                    "CONCATENER(texte1, [texte2], ...)",
                    "joindre deux ou plusieurs chaînes de texte en une seule chaîne."
                ],
                COS: [
                    "COS(number)",
                    "Renvoie le cosinus de l'angle donné (en radians)."
                ],
                COUNT: [
                    "COMPTE(valeur1, [valeur2], ...)",
                    "Compte le nombre de cellules contenant des nombres et compte les nombres dans la liste d'arguments."
                ],
                COUNTA: [
                    "COUNTA(valeur1, [valeur2], ...)",
                    "La fonction COUNTA compte le nombre de cellules qui ne sont pas vides dans une plage."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Compte les cellules vides dans une plage de cellules spécifiée."
                ],
                COUNTIF: [
                    "COUNTIF(plage, critères)",
                    "compte le nombre de cellules qui répondent à un critère ;par exemple, pour compter le nombre de fois qu'une ville particulière apparaît dans une liste de clients."
                ],
                COUNTIFS: [
                    "COUNTIFS(critère_plage1, critère1, [critère_plage2, critère2]…)",
                    "Applique des critères aux cellules sur plusieurs plages et compte le nombre de fois que tous les critères sont remplis."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "renvoie le numéro de série séquentiel qui représente une date particulière."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Calcule le nombre de jours, de mois ou d'années entre deux dates.L'unité peut être « Y », « M » ou « D »."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "convertit une date stockée sous forme de texte en un numéro de série qu'Excel reconnaît comme une date.Par exemple, la formule =DATEVALUE(\"1/1/2008\") renvoie 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Renvoie le jour d'une date, représenté par un numéro de série."
                ],
                DAYS: [
                    "JOURS(end_date, start_date)",
                    "Renvoie le nombre de jours entre deux dates."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Convertit les radians en degrés."
                ],
                EOMONTH: [
                    "EOMONTH(start_date, mois)",
                    "Renvoie le numéro de série du dernier jour du mois correspondant au nombre de mois indiqué avant ou après start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Renvoie e élevé à la puissance numérique."
                ],
                FIND: [
                    "TROUVER(find_text, inside_text, [start_num])",
                    "Localise une chaîne de texte dans une deuxième chaîne de texte et renvoie le numéro de la position de départ de la première chaîne de texte à partir du premier caractère de la deuxième chaîne de texte."
                ],
                FLOOR: [
                    "ÉTAGE(numéro, signification)",
                    "Arrondit le nombre vers le bas, vers zéro, au multiple significatif le plus proche."
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "Recherche une valeur dans la ligne supérieure d'un tableau ou d'un tableau de valeurs, puis renvoie une valeur dans la même colonne à partir d'une ligne que vous spécifiez dans le tableau ou le tableau."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Renvoie l'heure d'une valeur temporelle.L'heure est indiquée sous forme d'un nombre entier allant de 0 (00h00) à 23 (23h00)."
                ],
                HYPERLINK: [
                    "HYPERLIEN(url, [friendly_name])",
                    "crée un raccourci qui permet d'accéder à un autre emplacement sur Internet lorsque vous cliquez sur une cellule"
                ],
                IF: [
                    "SI (test_logique, valeur_si_true, [valeur_if_false])",
                    "renvoie une valeur si une condition est vraie et une autre valeur si elle est fausse."
                ],
                INDEX: [
                    "INDEX(tableau, numéro_ligne, [num_colonne])",
                    "Renvoie la valeur d'un élément dans une table ou un tableau, sélectionné par les index de numéro de ligne et de colonne."
                ],
                INDIRECT: [
                    "INDIRECT(ref_text, [a1])",
                    "Renvoie la référence spécifiée par une chaîne de texte.Les références sont immédiatement évaluées pour afficher leur contenu."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Renvoie VRAI si la valeur est vide"
                ],
                LARGE: [
                    "GRAND(tableau, k)",
                    "Renvoie la k-ème plus grande valeur dans un ensemble de données."
                ],
                LEFT: [
                    "GAUCHE(texte, [num_cars])",
                    "renvoie le ou les premiers caractères d'une chaîne de texte, en fonction du nombre de caractères que vous spécifiez."
                ],
                LEN: [
                    "LEN(text)",
                    "renvoie le nombre de caractères dans une chaîne de texte."
                ],
                LOOKUP: [
                    "RECHERCHE(lookup_value, lookup_vector, [result_vector])",
                    "Recherche une valeur dans une plage d'une ligne ou d'une colonne (appelée vecteur) et renvoie une valeur à partir de la même position dans une deuxième plage d'une ligne ou d'une colonne."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Convertit toutes les lettres majuscules d'une chaîne de texte en minuscules."
                ],
                MATCH: [
                    "MATCH(lookup_value, lookup_array, [match_type])",
                    "Recherche un élément spécifié dans une plage de cellules, puis renvoie la position relative de cet élément dans la plage.match_type peut être : 0 pour une correspondance exacte avec l'option d'utiliser des caractères génériques ;1 (par défaut) pour moins de, Les valeurs de l'argument lookup_array doivent être placées par ordre croissant ;-1 pour supérieur à, les valeurs de l'argument lookup_array doivent être placées par ordre décroissant."
                ],
                MAX: [
                    "MAX(numéro1, [numéro2], ...)",
                    "Renvoie la plus grande valeur dans un ensemble de valeurs."
                ],
                MEDIAN: [
                    "MÉDIANE(numéro1, [numéro2], ...)",
                    "Renvoie la médiane des nombres donnés.La médiane est le nombre situé au milieu d’un ensemble de nombres."
                ],
                MID: [
                    "MID(texte, start_num, num_chars)",
                    "renvoie un nombre spécifique de caractères d'une chaîne de texte, en commençant à la position que vous spécifiez, en fonction du nombre de caractères que vous spécifiez."
                ],
                MIN: [
                    "MIN(numéro1, [numéro2], ...)",
                    "Renvoie le plus petit nombre dans un ensemble de valeurs."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Renvoie la valeur la plus fréquente ou la plus répétitive dans un tableau ou une plage de données."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Renvoie le mois d'une date représentée par un numéro de série.Le mois est indiqué sous forme d'un nombre entier allant du 1 (janvier) au 12 (décembre)."
                ],
                OR: [
                    "OU(logique1, [logique2], ...)",
                    "renvoie TRUE si l'un de ses arguments est évalué à TRUE et renvoie FALSE si tous ses arguments sont évalués à FALSE."
                ],
                PI: [
                    "PI()",
                    "Renvoie le nombre 3.14159265358979, la constante mathématique pi."
                ],
                POWER: [
                    "PUISSANCE(nombre, puissance)",
                    "Renvoie le résultat d'un nombre élevé à une puissance."
                ],
                PRODUCT: [
                    "PRODUIT(numéro1, [numéro2], ...)",
                    "multiplie tous les nombres donnés en arguments et renvoie le produit.PRODUIT(A1:A3, C1:C3) équivaut à =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Met en majuscule la première lettre de chaque mot d'une valeur de texte."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Convertit les degrés en radians."
                ],
                RAND: [
                    "RAND()",
                    "Renvoie un nombre réel aléatoire uniformément réparti, supérieur ou égal à 0 et inférieur à 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Renvoie le rang d'un nombre dans une liste de nombres."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(old_text, start_num, num_chars, new_text)",
                    "remplace une partie d'une chaîne de texte, en fonction du nombre de caractères que vous spécifiez, par une chaîne de texte différente."
                ],
                REPT: [
                    "REPT(texte, nombre_fois)",
                    "Répète le texte un nombre de fois donné."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "renvoie le ou les derniers caractères d'une chaîne de texte, en fonction du nombre de caractères que vous spécifiez."
                ],
                ROUND: [
                    "ROND(nombre, num_chiffres)",
                    "arrondit un nombre à un nombre spécifié de chiffres."
                ],
                ROUNDDOWN: [
                    "ARRONDISSEMENT(nombre, num_digits)",
                    "Arrondit un nombre vers le bas, vers zéro."
                ],
                ROUNDUP: [
                    "ROUNDUP(nombre, num_digits)",
                    "Arrondit un nombre, loin de 0 (zéro)."
                ],
                ROW: [
                    "ROW()",
                    "Renvoie la référence de la cellule dans laquelle apparaît la fonction ROW."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Renvoie le nombre de lignes dans une référence ou un tableau."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "localise une chaîne de texte dans une deuxième chaîne de texte et renvoie le numéro de la position de départ de la première chaîne de texte à partir du premier caractère de la deuxième chaîne de texte."
                ],
                SIN: [
                    "SIN(number)",
                    "Renvoie le sinus de l'angle donné (en radians)."
                ],
                SMALL: [
                    "PETIT(tableau, k)",
                    "Renvoie la k-ème plus petite valeur d'un ensemble de données."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Renvoie une racine carrée positive."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Estimation de l’écart type sur la base d’un échantillon.L'écart type est une mesure de la mesure dans laquelle les valeurs sont dispersées par rapport à la valeur moyenne (la moyenne)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Calcule l'écart type en fonction de l'ensemble de la population donnée en arguments."
                ],
                SUBSTITUTE: [
                    "SUBSTITUT(texte, ancien_texte, nouveau_texte, [numéro_instance])",
                    "Remplace new_text par old_text dans une chaîne de texte."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Ajoute ses arguments.Vous pouvez ajouter des valeurs individuelles, des références de cellules ou des plages ou un mélange des trois."
                ],
                SUMIF: [
                    "SUMIF(plage, critères, [sum_range])",
                    "ajoute les valeurs dans une plage qui répondent aux critères que vous spécifiez"
                ],
                SUMIFS: [
                    "SUMIFS(plage_somme, plage_critères1, critères1, [plage_critères2, critères2], ...)",
                    "ajoute tous ses arguments qui répondent à plusieurs critères."
                ],
                SUMPRODUCT: [
                    "SOMMEPRODUCT(tableau1, [tableau2], [tableau3], ...)",
                    "Multiplie les composants correspondants dans les tableaux donnés et renvoie la somme de ces produits."
                ],
                TAN: [
                    "TAN(number)",
                    "Renvoie la tangente de l'angle donné (en radians)."
                ],
                TEXT: [
                    "TEXTE(Valeur que vous souhaitez formater, \"Formater le code que vous souhaitez appliquer\")",
                    "La fonction TEXTE vous permet de modifier la façon dont un nombre apparaît en lui appliquant un formatage avec des codes de format."
                ],
                TIME: [
                    "TEMPS(heure, minute, seconde)",
                    "Renvoie le nombre décimal pour une heure particulière."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Renvoie le nombre décimal de l'heure représentée par une chaîne de texte.Le nombre décimal est une valeur comprise entre 0 (zéro) et 0,99988426, représentant les heures de 0:00:00 (00:00:00) à 23:59:59 (23:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Renvoie le numéro de série de la date actuelle."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Supprime tous les espaces du texte, à l'exception des espaces simples entre les mots."
                ],
                TRUNC: [
                    "TRUNC(nombre, [num_digits])",
                    "Tronque un nombre en entier en supprimant la partie fractionnaire du nombre."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Convertit le texte en majuscules."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Convertit une chaîne de texte qui représente un nombre en nombre."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Estimation de la variance sur la base d'un échantillon."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Calcule la variance en fonction de l'ensemble de la population."
                ],
                VLOOKUP: [
                    "RECHERCHEV (lookup_value, table_array, col_index_num, [range_lookup])",
                    "rechercher une valeur dans un tableau ou une plage par ligne.Par exemple, recherchez le prix d’une pièce automobile à l’aide du numéro de pièce."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Renvoie l'année correspondant à une date.L'année est renvoyée sous forme d'entier compris entre 1900 et 9999."
                ]
            },
            strFreezeFirstCol: "Geler la première colonne",
            strFreezePanes: "Figer les volets",
            strFreezePanesUndo: "Dégeler les volets",
            strFreezeTopRow: "Geler la première ligne",
            strFrozenCols: "Colonnes gelées",
            strFrozenRows: "Lignes gelées",
            strFullScreen: "Plein écran",
            strGroup_fixCols: "Corriger les colonnes",
            strGroup_grandSummary: "Grand résumé",
            strGroup_header: "Supprimer le groupe par colonne",
            strGroup_merge: "Fusionner les cellules",
            strHideCols: "Masquer les colonnes",
            strHideRows: "Masquer les lignes",
            strImport: "Importer des données",
            strImportPic: "Insérer une image flottante",
            strImportPicCell: "Insérer une image dans une cellule",
            strInsertColumn: "Insérer une colonne",
            strInsertRow: "Insérer une ligne",
            strInsertRows: "Insérer {0} lignes",
            strItalic: "Texte en italique",
            strLabel: "Étiquette",
            strLink: "Lien",
            strLoading: "Chargement",
            strLocal: "fr",
            strLockCells: "Verrouiller les cellules",
            strMenu: {
                export: "Colonnes exportables",
                filter: "Filtrer",
                hideCols: "Colonnes visibles"
            },
            strMerge: "Fusionner les cellules",
            strName: "Nom",
            strNextResult: "Résultat suivant",
            strNoRows: "Pas de lignes à afficher.",
            strNothingFound: "Aucun résultat",
            strOR: "OU",
            strOk: "D'accord",
            strOpen: "Ouvert",
            strPaste: "Coller",
            strPrevResult: "Résultat précédent",
            strRedo: "Refaire",
            strRename: "Renommer",
            strSearch: "Rechercher",
            strSelectAll: "Sélectionner tout",
            strSelectedmatches: "Résultat {0} sur {1} résultat(s)",
            strShowCols: "Afficher les colonnes",
            strShowRows: "Afficher les lignes",
            strTP_aggPH: "Supprimer les colonnes pour les agrégats",
            strTP_aggPane: "Agrégats",
            strTP_colPH: "Supprimer des colonnes pour le regroupement de colonnes",
            strTP_colPane: "Colonnes de groupe",
            strTP_pivot: "Mode pivot",
            strTP_rowPH: "Supprimer des colonnes pour le regroupement de lignes",
            strTP_rowPane: "Regrouper les lignes",
            strTabAdd: "Nouvelle feuille",
            strTabClose: "Supprimer la feuille",
            strTabHide: "Masquer la feuille",
            strTabName: "sheet{0}",
            strTabRemove: "{0} serait supprimé définitivement.\r\nEs-tu sûr?",
            strTabRename: "Renommer la feuille",
            strTabShow: "Afficher la feuille",
            strTextColor: "Couleur du texte",
            strUnderline: "Souligner le texte",
            strUndo: "Annuler",
            strUnhide: "Afficher",
            strUnmerge: "Annuler la fusion des cellules",
            strUpdate: "Mise à jour",
            strWrap: "Retour à la ligne du texte"
        },
        pager = pq.pqPager.regional.fr = {
            strDisplay: "Voir {0} à {1} sur {2} résultats.",
            strFirstPage: "Première page",
            strLastPage: "Dernière page",
            strNextPage: "Page suivante",
            strPage: "Page {0} sur {1}",
            strPrevPage: "Page précédente",
            strRefresh: "Actualiser",
            strRpp: "Résultats par page: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);