(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.nl = {
            strAND: "EN",
            strAddColLeft: "Kolom links toevoegen",
            strAddColRight: "Kolom rechts toevoegen",
            strAddColumn: "Kolom toevoegen",
            strAddRow: "Rij toevoegen",
            strAddRowAbove: "Voeg rij hierboven toe",
            strAddRowBelow: "Voeg hieronder een rij toe",
            strAddRows: "Voeg {0} rijen toe",
            strAlign: {
                bottom: "Onderkant uitlijnen",
                center: "Midden uitlijnen",
                left: "Links uitlijnen",
                right: "Rechts uitlijnen",
                top: "Boven uitlijnen"
            },
            strAlignH: "Horizontale uitlijning",
            strAlignV: "Verticale uitlijning",
            strApply: "Toepassen",
            strBlanks: "(spaties)",
            strBold: "Vette tekst",
            strBorder: "Grens",
            strBorderColor: "Randkleur",
            strBorderStyle: "Randstijl",
            strBorders: {
                all: "Alle grenzen",
                bottom: "Onderste rand",
                horizontal: "Horizontale rand",
                inner: "Binnenrand",
                left: "Linkerrand",
                none: "Geen grens",
                outer: "Buitengrens",
                right: "Rechter grens",
                top: "Bovenste rand",
                vertical: "Verticale rand"
            },
            strCancel: "Annuleer",
            strClear: "Duidelijk",
            strClearColor: "Heldere kleur",
            strClearFilter: "Filter wissen",
            strClearText: "Duidelijke tekst",
            strCollapse: "Paneel samenvouwen",
            strComment: "Commentaar",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Geen --",
                begin: "Begint met",
                between: "Tussendoor",
                contain: "Bevat",
                empty: "Leeg",
                end: "Eindigt met",
                equal: "Gelijk aan",
                great: "Groter dan",
                gte: "Groter dan of gelijk",
                less: "Minder dan",
                lte: "Minder dan of gelijk",
                notbegin: "Begint niet met",
                notcontain: "Bevat niet",
                notempty: "Niet leeg",
                notend: "Eindigt niet met",
                notequal: "Niet gelijken",
                range: "[ Bereik ]",
                regexp: "Reguliere expressie"
            },
            strCopy: "Kopieer",
            strCut: "Knippen",
            strDelete: "uitgeven",
            strDeleteColumn: "Kolom verwijderen",
            strDeleteRow: "Rij verwijderen",
            strDownloadXlsx: "Excel-bestand downloaden",
            strEdit: "opstellen",
            strExitFullScreen: "Sluit Volledig scherm af",
            strExpand: "Vouw paneel uit",
            strExport: "Gegevens exporteren",
            strFillColor: "Vulkleur",
            strFontFamily: "Lettertypefamilie",
            strFontSize: "Lettergrootte",
            strFormat: "Cellen opmaken",
            strFormatMenu: {
                Accounting: "Boekhouding",
                Currency: "Valuta",
                Custom: "Aangepast",
                DateTime: "Datum Tijd",
                Fixed: "Decimaal",
                Fraction: "Fractie",
                FullDate: "Volledige datum",
                General: "Algemeen",
                Int: "Geheel getal",
                LongDate: "Lange datum",
                MediumDate: "Middellange datum",
                Note: "Opmerking: Formaten met * veranderen afhankelijk van de landinstelling van de gebruiker.",
                Percent: "Percentage",
                Scientific: "Wetenschappelijk",
                ShortDate: "Korte datum",
                Standard: "Standaard",
                Text: "Tekst",
                Time: "Tijd"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Retourneert de absolute waarde van een getal.De absolute waarde van een getal is het getal zonder teken."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Retourneert de arccosinus, of inverse cosinus, van een getal.De arccosinus is de hoek waarvan de cosinus getal is.De geretourneerde hoek wordt gegeven in radialen in het bereik 0 (nul) tot pi."
                ],
                AND: [
                    "EN(logisch1, [logisch2], ...)",
                    "Retourneert TRUE als alle argumenten WAAR zijn, en retourneert FALSE als een of meer argumenten FALSE opleveren."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Retourneert de boogsinus, of inverse sinus, van een getal.De boogsinus is de hoek waarvan de sinus getal is.De geretourneerde hoek wordt gegeven in radialen in het bereik -pi/2 tot pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Retourneert de boogtangens, of inverse tangens, van een getal.De boogtangens is de hoek waarvan de raaklijn getal is.De geretourneerde hoek wordt gegeven in radialen in het bereik -pi/2 tot pi/2."
                ],
                AVERAGE: [
                    "GEMIDDELDE(getal1, [getal2], ...)",
                    "Retourneert het gemiddelde (rekenkundig gemiddelde) van de argumenten.Als het bereik A1:A20 bijvoorbeeld getallen bevat, retourneert de formule =GEMIDDELDE(A1:A20) het gemiddelde van die getallen."
                ],
                AVERAGEIF: [
                    "GEMIDDELDE.ALS(bereik; criteria; [gemiddeld bereik])",
                    "Retourneert het gemiddelde (rekenkundig gemiddelde) van alle cellen in een bereik die aan een bepaald criterium voldoen."
                ],
                AVERAGEIFS: [
                    "GEMIDDELDE.ALS(gemiddeld bereik, criteriabereik1, criteria1, [criteriabereik2, criteria2], ...)",
                    "Retourneert het gemiddelde (rekenkundig gemiddelde) van alle cellen die aan meerdere criteria voldoen."
                ],
                CEILING: [
                    "PLAFOND(getal, betekenis)",
                    "Retourneert een getal naar boven afgerond, weg van nul, naar het dichtstbijzijnde significante veelvoud.Als u bijvoorbeeld geen centen in uw prijzen wilt gebruiken en uw product € 4,42 kost, gebruikt u de formule =CEILING(4,42;0,05) om de prijzen naar boven af ​​te ronden op de dichtstbijzijnde nikkel."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Retourneert het teken dat is opgegeven door een getal."
                ],
                CHOOSE: [
                    "KIES(index_getal, waarde1, [waarde2], ...)",
                    "Als index_getal 1 is, retourneert CHOOSE waarde1;als het 2 is, retourneert CHOOSE waarde2;enzovoort."
                ],
                CODE: [
                    "CODE(text)",
                    "Retourneert een numerieke code voor het eerste teken in een teksttekenreeks."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Retourneert de verwijzing naar de cel waarin de functie KOLOM verschijnt."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Retourneert het aantal kolommen in een array of verwijzing."
                ],
                CONCATENATE: [
                    "SAMENVOEGEN(tekst1, [tekst2], ...)",
                    "voeg twee of meer tekstreeksen samen tot één tekenreeks."
                ],
                COS: [
                    "COS(number)",
                    "Geeft de cosinus van de gegeven hoek terug (in radialen)."
                ],
                COUNT: [
                    "AANTAL(waarde1, [waarde2], ...)",
                    "Telt het aantal cellen dat getallen bevat, en telt getallen in de lijst met argumenten."
                ],
                COUNTA: [
                    "AANTALA(waarde1, [waarde2], ...)",
                    "De functie AANTALA telt het aantal cellen dat niet leeg is in een bereik."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Telt lege cellen in een opgegeven celbereik."
                ],
                COUNTIF: [
                    "AANTAL.ALS(bereik; criteria)",
                    "telt het aantal cellen dat aan een criterium voldoet;om bijvoorbeeld te tellen hoe vaak een bepaalde stad in een klantenlijst voorkomt."
                ],
                COUNTIFS: [
                    "AANTAL.ALS(criteria_bereik1, criteria1, [criteria_bereik2, criteria2]…)",
                    "Past criteria toe op cellen in meerdere bereiken en telt het aantal keren dat aan alle criteria wordt voldaan."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "retourneert het opeenvolgende serienummer dat een bepaalde datum vertegenwoordigt."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Berekent het aantal dagen, maanden of jaren tussen twee datums.Eenheid kan 'Y', 'M' of 'D' zijn."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "converteert een datum die als tekst is opgeslagen naar een serienummer dat Excel als datum herkent.De formule =DATUMWAARDE(\"1/1/2008\") retourneert bijvoorbeeld 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Retourneert de dag van een datum, weergegeven door een serienummer."
                ],
                DAYS: [
                    "DAGEN(einddatum; startdatum)",
                    "Retourneert het aantal dagen tussen twee datums."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Converteert radialen naar graden."
                ],
                EOMONTH: [
                    "EOMONTH(startdatum; maanden)",
                    "Retourneert het serienummer voor de laatste dag van de maand, dat wil zeggen het aangegeven aantal maanden vóór of na startdatum."
                ],
                EXP: [
                    "EXP(number)",
                    "Retourneert e verheven tot de macht van het getal."
                ],
                FIND: [
                    "FIND(vind_tekst, binnen_tekst, [beginnummer])",
                    "Zoekt één tekstreeks binnen een tweede tekstreeks en retourneert het nummer van de startpositie van de eerste tekstreeks vanaf het eerste teken van de tweede tekstreeks."
                ],
                FLOOR: [
                    "FLOOR(getal, betekenis)",
                    "Rondt het getal naar beneden af, richting nul, naar het dichtstbijzijnde significante veelvoud."
                ],
                HLOOKUP: [
                    "HLOOKUP(opzoekwaarde, tabelmatrix, rijindexnummer, [bereikopzoeken])",
                    "Zoekt naar een waarde in de bovenste rij van een tabel of een array met waarden en retourneert vervolgens een waarde in dezelfde kolom uit een rij die u opgeeft in de tabel of array."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Retourneert het uur van een tijdwaarde.Het uur wordt gegeven als een geheel getal, variërend van 0 (12:00 uur) tot 23 (23:00 uur)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [vriendelijke_naam])",
                    "maakt een snelkoppeling die naar een andere locatie op internet springt wanneer u op een cel klikt"
                ],
                IF: [
                    "ALS(logische_test, waarde_if_true, [waarde_if_false])",
                    "retourneert één waarde als een voorwaarde waar is en een andere waarde als deze onwaar is."
                ],
                INDEX: [
                    "INDEX(matrix, rij_getal, [kolom_getal])",
                    "Retourneert de waarde van een element in een tabel of array, geselecteerd door de rij- en kolomnummerindexen."
                ],
                INDIRECT: [
                    "INDIRECT(ref_tekst, [a1])",
                    "Retourneert de verwijzing die is opgegeven door een teksttekenreeks.Referenties worden onmiddellijk geëvalueerd om hun inhoud weer te geven."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Retourneert TRUE als de waarde leeg is"
                ],
                LARGE: [
                    "GROOT(matrix, k)",
                    "Retourneert de k-de grootste waarde in een gegevensset."
                ],
                LEFT: [
                    "LINKS(tekst, [aantal_tekens])",
                    "retourneert het eerste teken of de eerste tekens in een teksttekenreeks, op basis van het aantal tekens dat u opgeeft."
                ],
                LEN: [
                    "LEN(text)",
                    "retourneert het aantal tekens in een teksttekenreeks."
                ],
                LOOKUP: [
                    "ZOEKEN(opzoekwaarde, opzoekvector, [resultaatvector])",
                    "Zoekt in een bereik van één rij of één kolom (ook wel een vector genoemd) naar een waarde en retourneert een waarde van dezelfde positie in een tweede bereik van één rij of één kolom."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Converteert alle hoofdletters in een tekstreeks naar kleine letters."
                ],
                MATCH: [
                    "VERGELIJKEN(opzoekwaarde, opzoekmatrix, [overeenkomsttype])",
                    "Zoekt naar een opgegeven item in een celbereik en retourneert vervolgens de relatieve positie van dat item in het bereik.match_type kan zijn: 0 voor exacte match met optie om jokertekens te gebruiken;1 (standaard) voor minder dan. De waarden in het lookup_array-argument moeten in oplopende volgorde worden geplaatst;-1 voor groter dan moeten waarden in het lookup_array-argument in aflopende volgorde worden geplaatst."
                ],
                MAX: [
                    "MAX(getal1, [getal2], ...)",
                    "Retourneert de grootste waarde in een reeks waarden."
                ],
                MEDIAN: [
                    "MEDIAAN(getal1, [getal2], ...)",
                    "Geeft de mediaan van de gegeven getallen terug.De mediaan is het getal in het midden van een reeks getallen."
                ],
                MID: [
                    "MID(tekst, startnummer, aantal_tekens)",
                    "retourneert een specifiek aantal tekens uit een teksttekenreeks, beginnend op de positie die u opgeeft, op basis van het aantal tekens dat u opgeeft."
                ],
                MIN: [
                    "MIN(getal1, [getal2], ...)",
                    "Retourneert het kleinste getal in een reeks waarden."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Retourneert de meest voorkomende of repetitieve waarde in een array of gegevensbereik."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Retourneert de maand van een datum die wordt weergegeven door een serienummer.De maand wordt gegeven als een geheel getal, variërend van 1 (januari) tot 12 (december)."
                ],
                OR: [
                    "OF(logisch1, [logisch2], ...)",
                    "retourneert WAAR als een van de argumenten WAAR is, en retourneert ONWAAR als al zijn argumenten ONWAAR zijn."
                ],
                PI: [
                    "PI()",
                    "Retourneert het getal 3,14159265358979, de wiskundige constante pi."
                ],
                POWER: [
                    "VERMOGEN(getal, macht)",
                    "Retourneert het resultaat van een getal verheven tot een macht."
                ],
                PRODUCT: [
                    "PRODUCT(getal1, [getal2], ...)",
                    "vermenigvuldigt alle getallen die als argumenten zijn gegeven en retourneert het product.PRODUCT(A1:A3, C1:C3) is gelijk aan =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Geeft de eerste letter van elk woord van een tekstwaarde een hoofdletter."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Converteert graden naar radialen."
                ],
                RAND: [
                    "RAND()",
                    "Retourneert een gelijkmatig verdeeld willekeurig reëel getal groter dan of gelijk aan 0 en kleiner dan 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Retourneert de rangorde van een getal in een lijst met getallen."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(oude_tekst, begin_getal, aantal_tekens, nieuwe_tekst)",
                    "vervangt een deel van een tekstreeks, gebaseerd op het aantal tekens dat u opgeeft, door een andere tekstreeks."
                ],
                REPT: [
                    "HERHALEN(tekst; aantal_maal)",
                    "Herhaalt tekst een bepaald aantal keren."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "retourneert het laatste teken of de laatste tekens in een teksttekenreeks, op basis van het aantal tekens dat u opgeeft."
                ],
                ROUND: [
                    "RONDE(getal; aantal_cijfers)",
                    "rondt een getal af op een bepaald aantal cijfers."
                ],
                ROUNDDOWN: [
                    "AFRONDEN(getal; aantal_cijfers)",
                    "Rondt een getal naar beneden af, richting nul."
                ],
                ROUNDUP: [
                    "ROUNDUP(getal; aantal_cijfers)",
                    "Rondt een getal naar boven af, weg van 0 (nul)."
                ],
                ROW: [
                    "ROW()",
                    "Retourneert de verwijzing naar de cel waarin de RIJ-functie verschijnt."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Retourneert het aantal rijen in een verwijzing of array."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "zoek één tekststring binnen een tweede tekststring en retourneer het nummer van de startpositie van de eerste tekststring vanaf het eerste teken van de tweede tekststring."
                ],
                SIN: [
                    "SIN(number)",
                    "Geeft de sinus van de gegeven hoek terug (in radialen)."
                ],
                SMALL: [
                    "KLEIN(matrix, k)",
                    "Retourneert de k-de kleinste waarde in een gegevensset."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Retourneert een positieve vierkantswortel."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Schat de standaardafwijking op basis van een steekproef.De standaarddeviatie is een maatstaf voor hoe ver de waarden afwijken van de gemiddelde waarde (het gemiddelde)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Berekent de standaardafwijking op basis van de gehele populatie, gegeven als argumenten."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(tekst, oude_tekst, nieuwe_tekst, [instantienummer])",
                    "Vervangt oude_tekst door nieuwe_tekst in een tekstreeks."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Voegt zijn argumenten toe.U kunt individuele waarden, celverwijzingen of bereiken of een combinatie van alle drie toevoegen."
                ],
                SUMIF: [
                    "SOM.ALS(bereik; criteria, [som_bereik])",
                    "voegt de waarden toe in een bereik dat voldoet aan de criteria die u opgeeft"
                ],
                SUMIFS: [
                    "SOMMEN(som_bereik, criteria_bereik1, criteria1, [criteria_bereik2, criteria2], ...)",
                    "voegt alle argumenten toe die aan meerdere criteria voldoen."
                ],
                SUMPRODUCT: [
                    "SOMPRODUCT(matrix1, [matrix2], [matrix3], ...)",
                    "Vermenigvuldigt overeenkomstige componenten in de gegeven matrices en retourneert de som van die producten."
                ],
                TAN: [
                    "TAN(number)",
                    "Geeft de tangens van de gegeven hoek terug (in radialen)."
                ],
                TEXT: [
                    "TEXT(Waarde die u wilt opmaken, \"Formaatcode die u wilt toepassen\")",
                    "Met de functie TEKST kunt u de manier wijzigen waarop een getal wordt weergegeven door er opmaak met opmaakcodes op toe te passen."
                ],
                TIME: [
                    "TIJD(uur, minuut, seconde)",
                    "Retourneert het decimale getal voor een bepaalde tijd."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Retourneert het decimale getal van de tijd die wordt weergegeven door een teksttekenreeks.Het decimale getal is een waarde variërend van 0 (nul) tot 0,99988426, die de tijden vertegenwoordigt van 0:00:00 (12:00:00 uur) tot 23:59:59 (23:59:59 uur)."
                ],
                TODAY: [
                    "TODAY()",
                    "Retourneert het serienummer van de huidige datum."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Verwijdert alle spaties uit tekst, behalve enkele spaties tussen woorden."
                ],
                TRUNC: [
                    "TRUNC(getal, [aantal_cijfers])",
                    "Kapt een getal af tot een geheel getal door het fractionele deel van het getal te verwijderen."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Converteert tekst naar hoofdletters."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Converteert een tekstreeks die een getal vertegenwoordigt naar een getal."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Schat de variantie op basis van een steekproef."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Berekent de variantie op basis van de gehele populatie."
                ],
                VLOOKUP: [
                    "VERT.ZOEKEN (lookup_value, table_array, col_index_num, [range_lookup])",
                    "een waarde opzoeken in een tabel of een bereik per rij.Zoek bijvoorbeeld een prijs van een auto-onderdeel op aan de hand van het onderdeelnummer."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Retourneert het jaar dat overeenkomt met een datum.Het jaar wordt geretourneerd als een geheel getal tussen 1900 en 9999."
                ]
            },
            strFreezeFirstCol: "Bevries de eerste kolom",
            strFreezePanes: "Vriesruiten bevriezen",
            strFreezePanesUndo: "Deelvensters ontdooien",
            strFreezeTopRow: "Bevries de eerste rij",
            strFrozenCols: "Bevroren kolommen",
            strFrozenRows: "Bevroren rijen",
            strFullScreen: "Volledig scherm",
            strGroup_fixCols: "Kolommen repareren",
            strGroup_grandSummary: "Grote samenvatting",
            strGroup_header: "Groep per kolom neerzetten",
            strGroup_merge: "Cellen samenvoegen",
            strHideCols: "Kolommen verbergen",
            strHideRows: "Rijen verbergen",
            strImport: "Gegevens importeren",
            strImportPic: "Zwevende afbeelding invoegen",
            strImportPicCell: "Afbeelding in cel invoegen",
            strInsertColumn: "Kolom invoegen",
            strInsertRow: "Rij invoegen",
            strInsertRows: "Voeg {0} rijen in",
            strItalic: "Cursieve tekst",
            strLabel: "Etiket",
            strLink: "Koppeling",
            strLoading: "Laden",
            strLocal: "nl",
            strLockCells: "Cellen vergrendelen",
            strMenu: {
                export: "Exporteerbare kolommen",
                filter: "Filteren",
                hideCols: "Zichtbare kolommen"
            },
            strMerge: "Cellen samenvoegen",
            strName: "Naam",
            strNextResult: "Volgende Resultaat",
            strNoRows: "Geen rijen weer te geven",
            strNothingFound: "niets gevonden",
            strOR: "OF",
            strOk: "Oké",
            strOpen: "Openen",
            strPaste: "Plakken",
            strPrevResult: "Vorige Resultaat",
            strRedo: "Opnieuw uitvoeren",
            strRename: "Hernoemen",
            strSearch: "Zoeken",
            strSelectAll: "Selecteer Alles",
            strSelectedmatches: "Gekozen {0} van {1} partuur(en)",
            strShowCols: "Kolommen weergeven",
            strShowRows: "Toon rijen",
            strTP_aggPH: "Zet kolommen voor aggregaten neer",
            strTP_aggPane: "Aggregaten",
            strTP_colPH: "Kolommen neerzetten voor kolomgroepering",
            strTP_colPane: "Groepeer kolommen",
            strTP_pivot: "Draaimodus",
            strTP_rowPH: "Kolommen neerzetten voor rijgroepering",
            strTP_rowPane: "Groepeer rijen",
            strTabAdd: "Nieuw blad",
            strTabClose: "Blad verwijderen",
            strTabHide: "Blad verbergen",
            strTabName: "sheet{0}",
            strTabRemove: "{0} zou permanent worden verwijderd.\r\nWeet je het zeker?",
            strTabRename: "Naam blad wijzigen",
            strTabShow: "Blad tonen",
            strTextColor: "Tekstkleur",
            strUnderline: "Tekst onderstrepen",
            strUndo: "Ongedaan maken",
            strUnhide: "Zichtbaar maken",
            strUnmerge: "Cellen samenvoegen",
            strUpdate: "Bijwerken",
            strWrap: "Tekst omwikkelen"
        },
        pager = pq.pqPager.regional.nl = {
            strDisplay: "Weergeven {0} t&m {1} van {2} artikelen.",
            strFirstPage: "Eerste pagina",
            strLastPage: "Laatste pagina",
            strNextPage: "Volgende pagina",
            strPage: "Pagina {0} van {1}",
            strPrevPage: "Vorige pagina",
            strRefresh: "Verfrissen",
            strRpp: "Notulen per pagina: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();