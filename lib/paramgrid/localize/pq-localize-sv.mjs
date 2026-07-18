import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.sv = {
            strAND: "OCH",
            strAddColLeft: "Lägg till kolumn till vänster",
            strAddColRight: "Lägg till kolumn till höger",
            strAddColumn: "Lägg till kolumn",
            strAddRow: "Lägg till rad",
            strAddRowAbove: "Lägg till rad ovan",
            strAddRowBelow: "Lägg till rad nedan",
            strAddRows: "Lägg till {0} rader",
            strAlign: {
                bottom: "Justera botten",
                center: "Centrera",
                left: "Vänsterjustera",
                right: "Högerjustera",
                top: "Justera uppåt"
            },
            strAlignH: "Horisontell inriktning",
            strAlignV: "Vertikal inriktning",
            strApply: "Ansök",
            strBlanks: "(Blanka)",
            strBold: "Fet text",
            strBorder: "Gräns",
            strBorderColor: "Kantfärg",
            strBorderStyle: "Gränsstil",
            strBorders: {
                all: "Alla gränser",
                bottom: "Nedre kant",
                horizontal: "Horisontell kant",
                inner: "Inre gräns",
                left: "Vänster gräns",
                none: "Ingen gräns",
                outer: "Yttre gräns",
                right: "Höger gräns",
                top: "Top Border",
                vertical: "Vertikal kant"
            },
            strCancel: "Avbryt",
            strClear: "Rensa",
            strClearColor: "Klar färg",
            strClearFilter: "Rensa filter",
            strClearText: "Rensa text",
            strCollapse: "Dölj panelen",
            strComment: "Kommentera",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Ingen --",
                begin: "Börjar med",
                between: "Däremellan",
                contain: "Innehåller",
                empty: "Tom",
                end: "Slutar med",
                equal: "Lika",
                great: "Större än",
                gte: "Större än eller lika",
                less: "Mindre än",
                lte: "Mindre än eller lika",
                notbegin: "Inte börjar med",
                notcontain: "Innehåller inte",
                notempty: "Inte tomt",
                notend: "Slutar inte med",
                notequal: "Inte lika",
                range: "[Räckvidd]",
                regexp: "Reguljärt uttryck"
            },
            strCopy: "Kopiera",
            strCut: "Klipp",
            strDelete: "Ta bort",
            strDeleteColumn: "Ta bort kolumn",
            strDeleteRow: "Ta bort rad",
            strDownloadXlsx: "Ladda ner Excel-fil",
            strEdit: "Redigera",
            strExitFullScreen: "Avsluta helskärm",
            strExpand: "Expandera panelen",
            strExport: "Exportera data",
            strFillColor: "Fyllningsfärg",
            strFontFamily: "Teckensnittsfamilj",
            strFontSize: "Teckenstorlek",
            strFormat: "Formatera celler",
            strFormatMenu: {
                Accounting: "Bokföring",
                Currency: "Valuta",
                Custom: "Anpassad",
                DateTime: "Datum Tid",
                Fixed: "Decimal",
                Fraction: "Bråkdel",
                FullDate: "Fullständigt datum",
                General: "Allmänt",
                Int: "Heltal",
                LongDate: "Långt datum",
                MediumDate: "Medium datum",
                Note: "Obs! Format med * ändras beroende på användarens språk.",
                Percent: "Procentandel",
                Scientific: "Vetenskaplig",
                ShortDate: "Kort datum",
                Standard: "Standard",
                Text: "Text",
                Time: "Tid"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Returnerar det absoluta värdet av ett tal.Det absoluta värdet av ett tal är talet utan dess tecken."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Returnerar arccosinus, eller invers cosinus, för ett tal.Arccosinus är den vinkel vars cosinus är tal.Den returnerade vinkeln anges i radianer i intervallet 0 (noll) till pi."
                ],
                AND: [
                    "OCH(logisk1, [logisk2], ...)",
                    "Returnerar TRUE om alla dess argument evalueras till TRUE, och returnerar FALSE om ett eller flera argument evalueras till FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Returnerar arcsinus, eller invers sinus, för ett tal.Bågsinus är vinkeln vars sinus är tal.Den returnerade vinkeln anges i radianer i området -pi/2 till pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Returnerar arctangens, eller invers tangent, för ett tal.Arktangensen är vinkeln vars tangent är nummer.Den returnerade vinkeln anges i radianer i området -pi/2 till pi/2."
                ],
                AVERAGE: [
                    "AVERAGE(nummer1, [nummer2], ...)",
                    "Returnerar medelvärdet (arithmetiskt medelvärde) av argumenten.Till exempel, om intervallet A1:A20 innehåller tal, returnerar formeln =MEDEL(A1:A20) medelvärdet av dessa siffror."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(intervall; kriterier; [medelintervall])",
                    "Returnerar medelvärdet (arithmetiskt medelvärde) av alla celler i ett intervall som uppfyller ett givet kriterium."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(genomsnittsintervall, kriterieintervall1, kriterie1, [kriterieintervall2, kriterie2], ...)",
                    "Returnerar medelvärdet (arithmetiskt medelvärde) av alla celler som uppfyller flera kriterier."
                ],
                CEILING: [
                    "CEILING(tal, betydelse)",
                    "Returnerar tal avrundat uppåt, bort från noll, till närmaste multipel av signifikans.Om du till exempel vill undvika att använda slantar i dina priser och din produkt är prissatt till 4,42 USD, använd formeln =CEILING(4,42;0,05) för att runda av priserna upp till närmaste nickel."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Returnerar tecknet som anges av ett nummer."
                ],
                CHOOSE: [
                    "VÄLJ(index_tal, värde1, [värde2], ...)",
                    "Om index_num är 1, returnerar CHOOSE värde1;om det är 2, returnerar CHOOSE värde2;och så vidare."
                ],
                CODE: [
                    "CODE(text)",
                    "Returnerar en numerisk kod för det första tecknet i en textsträng."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Returnerar referens till cellen där funktionen COLUMN visas."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Returnerar antalet kolumner i en matris eller referens."
                ],
                CONCATENATE: [
                    "CONCATENATE(text1; [text2], ...)",
                    "sammanfoga två eller flera textsträngar till en sträng."
                ],
                COS: [
                    "COS(number)",
                    "Returnerar cosinus för den givna vinkeln (i radianer)."
                ],
                COUNT: [
                    "ANTAL(värde1, [värde2], ...)",
                    "Räknar antalet celler som innehåller siffror och räknar siffror i listan med argument."
                ],
                COUNTA: [
                    "ANTAL(värde1, [värde2], ...)",
                    "Funktionen COUNTA räknar antalet celler som inte är tomma i ett intervall."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Räknar tomma celler i ett specificerat cellintervall."
                ],
                COUNTIF: [
                    "ANTAL.OM(intervall; kriterier)",
                    "räknar antalet celler som uppfyller ett kriterium;till exempel för att räkna antalet gånger en viss stad förekommer i en kundlista."
                ],
                COUNTIFS: [
                    "ANTAL.OMF(kriterieområde1; kriterium1; [kriterieområde2; kriterium2]...)",
                    "Tillämpar kriterier på celler över flera intervall och räknar antalet gånger alla kriterier uppfylls."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "returnerar det sekventiella serienumret som representerar ett visst datum."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Beräknar antalet dagar, månader eller år mellan två datum.Enheten kan vara 'Y', 'M' eller 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "konverterar ett datum som lagras som text till ett serienummer som Excel känner igen som ett datum.Till exempel returnerar formeln =DATUMVÄRDE(\"1/1/2008\") 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Returnerar dagen för ett datum, representerat av ett serienummer."
                ],
                DAYS: [
                    "DAYS(slutdatum, startdatum)",
                    "Returnerar antalet dagar mellan två datum."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Omvandlar radianer till grader."
                ],
                EOMONTH: [
                    "EOMONTH(startdatum, månader)",
                    "Returnerar serienumret för den sista dagen i månaden som är det angivna antalet månader före eller efter start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Returnerar e upphöjd till talpotens."
                ],
                FIND: [
                    "FIND(hitta_text, inom_text, [startnummer])",
                    "Hittar en textsträng i en andra textsträng och returnerar numret på startpositionen för den första textsträngen från det första tecknet i den andra textsträngen."
                ],
                FLOOR: [
                    "GOLV(tal, betydelse)",
                    "Avrundar numret nedåt, mot noll, till närmaste multipel av signifikans."
                ],
                HLOOKUP: [
                    "SÖKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "Söker efter ett värde i den översta raden i en tabell eller en matris med värden och returnerar sedan ett värde i samma kolumn från en rad som du anger i tabellen eller matrisen."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Returnerar timmen för ett tidsvärde.Timmen anges som ett heltal, från 0 (12:00) till 23 (23:00)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [vänligt_namn])",
                    "skapar en genväg som hoppar till en annan plats på Internet när du klickar på en cell"
                ],
                IF: [
                    "OM(logiskt_test, värde_om_sant, [värde_om_falskt])",
                    "returnerar ett värde om ett villkor är sant och ett annat värde om det är falskt."
                ],
                INDEX: [
                    "INDEX(matris; radnummer; [kolumnnummer])",
                    "Returnerar värdet på ett element i en tabell eller en matris, vald av rad- och kolumnnummerindexen."
                ],
                INDIRECT: [
                    "INDIREKTA(ref_text, [a1])",
                    "Returnerar referensen som anges av en textsträng.Referenser utvärderas omedelbart för att visa deras innehåll."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Returnerar TRUE om värdet är tomt"
                ],
                LARGE: [
                    "LARGE(matris, k)",
                    "Returnerar det k:te största värdet i en datamängd."
                ],
                LEFT: [
                    "VÄNSTER(text, [antal_tecken])",
                    "returnerar det första tecknet eller tecknen i en textsträng, baserat på antalet tecken du anger."
                ],
                LEN: [
                    "LEN(text)",
                    "returnerar antalet tecken i en textsträng."
                ],
                LOOKUP: [
                    "SLÅ UPP(uppslagsvärde, uppslagsvektor, [resultatvektor])",
                    "Letar efter ett värde i ett intervall med en rad eller en kolumn (känd som en vektor) och returnerar ett värde från samma position i ett andra intervall med en rad eller en kolumn."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Konverterar alla versaler i en textsträng till gemener."
                ],
                MATCH: [
                    "MATCH(uppslagsvärde, uppslagsmatris, [matchningstyp])",
                    "Söker efter ett specificerat objekt i ett cellintervall och returnerar sedan objektets relativa position i intervallet.match_type kan vara: 0 för exakt matchning med möjlighet att använda jokertecken;1 (standard) för mindre än, Värdena i argumentet lookup_array måste placeras i stigande ordning;-1 för större än måste värden i argumentet lookup_array placeras i fallande ordning."
                ],
                MAX: [
                    "MAX(tal1; [nummer2], ...)",
                    "Returnerar det största värdet i en uppsättning värden."
                ],
                MEDIAN: [
                    "MEDIAN(tal1; [tal2], ...)",
                    "Returnerar medianen för de givna talen.Medianen är talet i mitten av en uppsättning tal."
                ],
                MID: [
                    "MID(text, startnummer, antal_tecken)",
                    "returnerar ett specifikt antal tecken från en textsträng, med början på den position du anger, baserat på antalet tecken du anger."
                ],
                MIN: [
                    "MIN(nummer1, [nummer2], ...)",
                    "Returnerar det minsta talet i en uppsättning värden."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Returnerar det vanligast förekommande eller repetitiva värdet i en matris eller ett dataintervall."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Returnerar månaden för ett datum som representeras av ett serienummer.Månaden anges som ett heltal, från 1 (januari) till 12 (december)."
                ],
                OR: [
                    "ELLER(logisk1, [logisk2], ...)",
                    "returnerar TRUE om något av dess argument utvärderas till TRUE, och returnerar FALSE om alla dess argument utvärderas till FALSE."
                ],
                PI: [
                    "PI()",
                    "Returnerar talet 3,14159265358979, den matematiska konstanten pi."
                ],
                POWER: [
                    "POWER(tal, effekt)",
                    "Returnerar resultatet av ett tal upphöjt till en potens."
                ],
                PRODUCT: [
                    "PRODUKT(nummer1, [nummer2], ...)",
                    "multiplicerar alla siffror som anges som argument och returnerar produkten.PRODUKT(A1:A3, C1:C3) motsvarar =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Versaler den första bokstaven i varje ord i ett textvärde."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Konverterar grader till radianer."
                ],
                RAND: [
                    "RAND()",
                    "Returnerar ett jämnt fördelat slumpmässigt reellt tal större än eller lika med 0 och mindre än 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Returnerar rangordningen för ett nummer i en lista med nummer."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(gammal_text, startnummer, antal_tecken, ny_text)",
                    "ersätter en del av en textsträng, baserat på antalet tecken du anger, med en annan textsträng."
                ],
                REPT: [
                    "REPT(text; antal_ggr)",
                    "Upprepar text ett givet antal gånger."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "returnerar det eller de sista tecknen i en textsträng, baserat på antalet tecken du anger."
                ],
                ROUND: [
                    "ROUND(tal; antal_siffror)",
                    "avrundar ett tal till ett angivet antal siffror."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(tal; antal_siffror)",
                    "Avrundar ett tal nedåt, mot noll."
                ],
                ROUNDUP: [
                    "ROUNDUP(tal; antal_siffror)",
                    "Avrundar ett tal uppåt, bort från 0 (noll)."
                ],
                ROW: [
                    "ROW()",
                    "Returnerar referensen till cellen där ROW-funktionen visas."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Returnerar antalet rader i en referens eller array."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "lokalisera en textsträng inom en andra textsträng och returnera numret på startpositionen för den första textsträngen från det första tecknet i den andra textsträngen."
                ],
                SIN: [
                    "SIN(number)",
                    "Returnerar sinus för den givna vinkeln (i radianer)."
                ],
                SMALL: [
                    "SMALL(matris, k)",
                    "Returnerar det k:te minsta värdet i en datamängd."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Returnerar en positiv kvadratrot."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Uppskattar standardavvikelsen baserat på ett urval.Standardavvikelsen är ett mått på hur brett värdena är spridda från medelvärdet (medelvärdet)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Beräknar standardavvikelsen baserat på hela populationen som anges som argument."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(text; gammal_text; ny_text; [instansnummer])",
                    "Ersätter ny_text med gammal_text i en textsträng."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Lägger till sina argument.Du kan lägga till individuella värden, cellreferenser eller intervall eller en blandning av alla tre."
                ],
                SUMIF: [
                    "SUMMAOM(intervall; kriterier; [summa_intervall])",
                    "lägger till värdena i ett intervall som uppfyller kriterier som du anger"
                ],
                SUMIFS: [
                    "SUMIFS(summa_intervall; kriterieområde1; kriterie1; [kriterieområde2; kriterie2], ...)",
                    "lägger till alla sina argument som uppfyller flera kriterier."
                ],
                SUMPRODUCT: [
                    "SUMMAPRODUKT(matris1, [matris2], [matris3], ...)",
                    "Multiplicerar motsvarande komponenter i de givna arrayerna och returnerar summan av dessa produkter."
                ],
                TAN: [
                    "TAN(number)",
                    "Returnerar tangenten för den givna vinkeln (i radianer)."
                ],
                TEXT: [
                    "TEXT(Värde du vill formatera, \"Formatera kod du vill använda\")",
                    "TEXT-funktionen låter dig ändra hur ett nummer visas genom att använda formatering på det med formatkoder."
                ],
                TIME: [
                    "TID(timme, minut, sekund)",
                    "Returnerar decimaltalet för en viss tid."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Returnerar decimaltalet för tiden som representeras av en textsträng.Decimaltalet är ett värde som sträcker sig från 0 (noll) till 0,99988426, vilket representerar tiderna från 0:00:00 (12:00:00 AM) till 23:59:59 (11:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Returnerar serienumret för det aktuella datumet."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Tar bort alla mellanslag från text utom enstaka mellanslag mellan ord."
                ],
                TRUNC: [
                    "TRUNC(tal; [antal_siffror])",
                    "Trunkerar ett tal till ett heltal genom att ta bort bråkdelen av talet."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Konverterar text till versaler."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Konverterar en textsträng som representerar ett tal till ett tal."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Beräknar varians baserat på ett urval."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Beräknar varians baserat på hela populationen."
                ],
                VLOOKUP: [
                    "VLOOKUP (lookup_value, table_array, col_index_num, [range_lookup])",
                    "slå upp ett värde i en tabell eller ett intervall för rad.Slå till exempel upp ett pris på en bildel efter artikelnumret."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Returnerar året som motsvarar ett datum.Årtalet returneras som ett heltal i intervallet 1900-9999."
                ]
            },
            strFreezeFirstCol: "Frys första kolumnen",
            strFreezePanes: "Frys rutor",
            strFreezePanesUndo: "Frigör rutor",
            strFreezeTopRow: "Frys första raden",
            strFrozenCols: "Frysta kolumner",
            strFrozenRows: "Frysta rader",
            strFullScreen: "Helskärm",
            strGroup_fixCols: "Fixa kolumner",
            strGroup_grandSummary: "Stor sammanfattning",
            strGroup_header: "Släpp grupp för kolumn",
            strGroup_merge: "Slå samman celler",
            strHideCols: "Dölj kolumner",
            strHideRows: "Göm rader",
            strImport: "Importera data",
            strImportPic: "Infoga flytande bild",
            strImportPicCell: "Infoga bild i cellen",
            strInsertColumn: "Infoga kolumn",
            strInsertRow: "Infoga rad",
            strInsertRows: "Infoga {0} rader",
            strItalic: "Kursiv text",
            strLabel: "Etikett",
            strLink: "Länk",
            strLoading: "Laddar",
            strLocal: "sv",
            strLockCells: "Lås celler",
            strMenu: {
                export: "Exporterbara kolumner",
                filter: "Filtrera",
                hideCols: "Synliga kolumner"
            },
            strMerge: "Slå samman celler",
            strName: "Namn",
            strNextResult: "Nästa resultat",
            strNoRows: "Inga rader att visa.",
            strNothingFound: "Inget hittades",
            strOR: "ELLER",
            strOk: "Okej",
            strOpen: "Öppna",
            strPaste: "Klistra in",
            strPrevResult: "Tidigare resultat",
            strRedo: "Gör om",
            strRename: "Byt namn",
            strSearch: "Sök",
            strSelectAll: "Välj Alla",
            strSelectedmatches: "Valda {0} av {1} matchningar",
            strShowCols: "Visa kolumner",
            strShowRows: "Visa rader",
            strTP_aggPH: "Släpp kolumner för aggregat",
            strTP_aggPane: "Aggregat",
            strTP_colPH: "Släpp kolumner för kolumngruppering",
            strTP_colPane: "Gruppera kolumner",
            strTP_pivot: "Pivotläge",
            strTP_rowPH: "Släpp kolumner för radgruppering",
            strTP_rowPane: "Gruppera rader",
            strTabAdd: "Nytt blad",
            strTabClose: "Ta bort arket",
            strTabHide: "Göm arket",
            strTabName: "sheet{0}",
            strTabRemove: "{0} skulle raderas permanent.\r\nÄr du säker?",
            strTabRename: "Byt namn på arket",
            strTabShow: "Visa blad",
            strTextColor: "Text färg",
            strUnderline: "Stryk under text",
            strUndo: "Ångra",
            strUnhide: "Visa upp",
            strUnmerge: "Ta bort sammanslagningen av celler",
            strUpdate: "Uppdatering",
            strWrap: "Radbryt text"
        },
        pager = pq.pqPager.regional.sv = {
            strDisplay: "Visar {0} till {1} ​​av {2} objekt.",
            strFirstPage: "Första sidan",
            strLastPage: "Sista sidan",
            strNextPage: "Nästa sida",
            strPage: "Sida {0} av {1}",
            strPrevPage: "Föregående sida",
            strRefresh: "Uppdatera",
            strRpp: "Poster per sida: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);