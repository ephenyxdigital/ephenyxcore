(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.ro = {
            strAND: "ȘI",
            strAddColLeft: "Adăugați coloana din stânga",
            strAddColRight: "Adăugați coloana la dreapta",
            strAddColumn: "Adăugați o coloană",
            strAddRow: "Adăugați un rând",
            strAddRowAbove: "Adăugați rândul de mai sus",
            strAddRowBelow: "Adăugați rândul de mai jos",
            strAddRows: "Adăugați {0} rânduri",
            strAlign: {
                bottom: "Alinierea de jos",
                center: "Alinierea la centru",
                left: "Aliniere la stânga",
                right: "Aliniați la dreapta",
                top: "Alinierea de sus"
            },
            strAlignH: "Alinierea orizontală",
            strAlignV: "Alinierea verticală",
            strApply: "Aplicați",
            strBlanks: "( spații libere )",
            strBold: "Text îngroșat",
            strBorder: "Frontieră",
            strBorderColor: "Culoarea chenarului",
            strBorderStyle: "Stil de chenar",
            strBorders: {
                all: "Toate Granițele",
                bottom: "Chenar de jos",
                horizontal: "Frontieră orizontală",
                inner: "Frontiera interioară",
                left: "Chenarul din stânga",
                none: "Fără Frontieră",
                outer: "Frontiera exterioară",
                right: "Chenarul din dreapta",
                top: "Chenar de sus",
                vertical: "Chenar vertical"
            },
            strCancel: "Anulează",
            strClear: "Clar",
            strClearColor: "Culoare clară",
            strClearFilter: "Șterge filtrul",
            strClearText: "Text clar",
            strCollapse: "Restrângeți panoul",
            strComment: "Comentariu",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Niciuna --",
                begin: "Începe cu",
                between: "Între mijloc",
                contain: "Conține",
                empty: "Gol",
                end: "Se termină cu",
                equal: "Egal",
                great: "Mai mare decât",
                gte: "Mai mare sau egal",
                less: "Mai puțin decât",
                lte: "Mai mic sau egal",
                notbegin: "Nu începe cu",
                notcontain: "Nu conține",
                notempty: "Nu gol",
                notend: "Nu se termină cu",
                notequal: "Nu este egal",
                range: "[ Interval ]",
                regexp: "Expresia regulată"
            },
            strCopy: "Copiere",
            strCut: "Tăiați",
            strDelete: "Ștergeți",
            strDeleteColumn: "Șterge coloana",
            strDeleteRow: "Ștergeți rândul",
            strDownloadXlsx: "Descărcați fișierul Excel",
            strEdit: "Editați",
            strExitFullScreen: "Ieșiți din ecranul complet",
            strExpand: "Extindeți panoul",
            strExport: "Exportați date",
            strFillColor: "Culoare de umplere",
            strFontFamily: "Familia de fonturi",
            strFontSize: "Dimensiunea fontului",
            strFormat: "Formatați celulele",
            strFormatMenu: {
                Accounting: "Contabilitate",
                Currency: "Moneda",
                Custom: "Personalizat",
                DateTime: "Data Ora",
                Fixed: "Decimală",
                Fraction: "Fracție",
                FullDate: "Data completă",
                General: "general",
                Int: "Număr întreg",
                LongDate: "Întâlnire lungă",
                MediumDate: "Data medie",
                Note: "Notă: Formatele cu * se modifică în funcție de localitatea utilizatorului.",
                Percent: "Procent",
                Scientific: "științific",
                ShortDate: "Întâlnire scurtă",
                Standard: "Standard",
                Text: "Text",
                Time: "timpul"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Returnează valoarea absolută a unui număr.Valoarea absolută a unui număr este numărul fără semnul său."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Returnează arccosinusul sau cosinusul invers al unui număr.Arccosinusul este unghiul al cărui cosinus este numărul.Unghiul returnat este dat în radiani în intervalul 0 (zero) până la pi."
                ],
                AND: [
                    "AND(logic1, [logic2], ...)",
                    "Returnează TRUE dacă toate argumentele sale sunt evaluate la TRUE și returnează FALSE dacă unul sau mai multe argumente sunt evaluate la FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Returnează arcsinusul sau sinusul invers al unui număr.Arcsinusul este unghiul al cărui sinus este numărul.Unghiul returnat este dat în radiani în intervalul -pi/2 până la pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Returnează arctangenta sau tangenta inversă a unui număr.Arctangenta este unghiul a cărui tangentă este numărul.Unghiul returnat este dat în radiani în intervalul -pi/2 până la pi/2."
                ],
                AVERAGE: [
                    "MEDIE(număr1, [număr2], ...)",
                    "Returnează media (media aritmetică) a argumentelor.De exemplu, dacă intervalul A1:A20 conține numere, formula =AVERAGE(A1:A20) returnează media acelor numere."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(interval, criterii, [interval_medie])",
                    "Returnează media (media aritmetică) a tuturor celulelor dintr-un interval care îndeplinesc un anumit criteriu."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(interval_medie, interval_criterii1, criteriu1, [interval_criterii2, criterii2], ...)",
                    "Returnează media (media aritmetică) a tuturor celulelor care îndeplinesc mai multe criterii."
                ],
                CEILING: [
                    "PLAFON(număr, semnificație)",
                    "Returnează numărul rotunjit în sus, departe de zero, la cel mai apropiat multiplu de semnificație.De exemplu, dacă doriți să evitați să folosiți bănuți în prețurile dvs. și produsul dvs. are un preț de 4,42 USD, utilizați formula =CEILING(4,42,0,05) pentru a rotunji prețurile la cel mai apropiat nichel."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Returnează caracterul specificat de un număr."
                ],
                CHOOSE: [
                    "ALEGE (num_index, valoare1, [valoare2], ...)",
                    "Dacă index_num este 1, CHOOSE returnează valoarea1;dacă este 2, CHOOSE returnează valoarea2;și așa mai departe."
                ],
                CODE: [
                    "CODE(text)",
                    "Returnează un cod numeric pentru primul caracter dintr-un șir de text."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Returnează referința celulei în care apare funcția COLUMN."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Returnează numărul de coloane dintr-o matrice sau referință."
                ],
                CONCATENATE: [
                    "CONCATENATE(text1, [text2], ...)",
                    "uniți două sau mai multe șiruri de text într-un șir."
                ],
                COS: [
                    "COS(number)",
                    "Returnează cosinusul unghiului dat (în radiani)."
                ],
                COUNT: [
                    "COUNT(valoare1, [valoare2], ...)",
                    "Numărează numărul de celule care conțin numere și numără numerele din lista de argumente."
                ],
                COUNTA: [
                    "COUNTA(valoare1, [valoare2], ...)",
                    "Funcția COUNTA numără numărul de celule care nu sunt goale într-un interval."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Numărează celulele goale dintr-un interval specificat de celule."
                ],
                COUNTIF: [
                    "COUNTIF(interval, criterii)",
                    "numără numărul de celule care îndeplinesc un criteriu;de exemplu, pentru a număra de câte ori un anumit oraș apare într-o listă de clienți."
                ],
                COUNTIFS: [
                    "COUNTIFS(interval_criterii1, criteriu1, [interval_criterii2, criterii2]…)",
                    "Aplică criterii celulelor din mai multe intervale și numără de câte ori sunt îndeplinite toate criteriile."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "returnează numărul de serie secvenţial care reprezintă o anumită dată."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Calculează numărul de zile, luni sau ani dintre două date.Unitatea poate fi „Y”, „M” sau „D”."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "convertește o dată care este stocată ca text într-un număr de serie pe care Excel îl recunoaște ca dată.De exemplu, formula =DATEVALUE(\"1/1/2008\") returnează 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Returnează ziua unei date, reprezentată de un număr de serie."
                ],
                DAYS: [
                    "DAYS(data_sfârșit, data_început)",
                    "Returnează numărul de zile dintre două date."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Transformă radianii în grade."
                ],
                EOMONTH: [
                    "EOMONTH(data_start, luni)",
                    "Returnează numărul de serie pentru ultima zi a lunii, care este numărul indicat de luni înainte sau după start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Returnează e ridicat la puterea numărului."
                ],
                FIND: [
                    "FIND(găsește_text, în_text, [start_num])",
                    "Localizează un șir de text într-un al doilea șir de text și returnează numărul poziției de pornire a primului șir de text din primul caracter al celui de al doilea șir de text."
                ],
                FLOOR: [
                    "FLOOR(număr, semnificație)",
                    "Rotunjește numărul în jos, spre zero, la cel mai apropiat multiplu de semnificație."
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "Caută o valoare în rândul de sus al unui tabel sau al unei matrice de valori, apoi returnează o valoare în aceeași coloană dintr-un rând pe care îl specificați în tabel sau matrice."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Returnează ora unei valori de timp.Ora este dată ca un număr întreg, variind de la 0 (12:00 A.M.) la 23 (11:00 P.M.)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [friendly_name])",
                    "creează o comandă rapidă care sare în altă locație de pe Internet atunci când faceți clic pe o celulă"
                ],
                IF: [
                    "IF(test_logic, valoare_dacă_adevărată, [valoare_dacă_fals])",
                    "returnează o valoare dacă o condiție este adevărată și o altă valoare dacă este falsă."
                ],
                INDEX: [
                    "INDEX(matrice, row_num, [column_num])",
                    "Returnează valoarea unui element dintr-un tabel sau dintr-o matrice, selectată de indicii numerelor de rând și coloane."
                ],
                INDIRECT: [
                    "INDIRECT(text_ref, [a1])",
                    "Returnează referința specificată de un șir de text.Referințele sunt imediat evaluate pentru a-și afișa conținutul."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Returnează TRUE dacă valoarea este goală"
                ],
                LARGE: [
                    "MARE (matrice, k)",
                    "Returnează a k-a cea mai mare valoare dintr-un set de date."
                ],
                LEFT: [
                    "LEFT(text, [num_chars])",
                    "returnează primul caracter sau primele caractere dintr-un șir de text, în funcție de numărul de caractere specificat."
                ],
                LEN: [
                    "LEN(text)",
                    "returnează numărul de caractere dintr-un șir de text."
                ],
                LOOKUP: [
                    "LOOKUP(lookup_value, lookup_vector, [result_vector])",
                    "Caută într-un interval de un rând sau de o coloană (cunoscut ca vector) o valoare și returnează o valoare din aceeași poziție într-un al doilea interval de un rând sau de o coloană."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Convertește toate literele mari dintr-un șir de text în minuscule."
                ],
                MATCH: [
                    "MATCH(valoare_căutare, matrice_căutare, [tip_potrivire])",
                    "Caută un articol specificat într-un interval de celule și apoi returnează poziția relativă a acelui articol în interval.match_type poate fi: 0 pentru potrivirea exactă cu opțiunea de a folosi metacaracterele;1 (implicit) pentru mai puțin de, Valorile din argumentul lookup_array trebuie plasate în ordine crescătoare;-1 pentru mai mare decât, valorile din argumentul lookup_array trebuie plasate în ordine descrescătoare."
                ],
                MAX: [
                    "MAX(număr1, [număr2], ...)",
                    "Returnează cea mai mare valoare dintr-un set de valori."
                ],
                MEDIAN: [
                    "MEDIAN(număr1, [număr2], ...)",
                    "Returnează mediana numerelor date.Mediana este numărul din mijlocul unui set de numere."
                ],
                MID: [
                    "MID(text, start_num, num_cars)",
                    "returnează un anumit număr de caractere dintr-un șir de text, începând cu poziția pe care o specificați, pe baza numărului de caractere pe care îl specificați."
                ],
                MIN: [
                    "MIN(număr1, [număr2], ...)",
                    "Returnează cel mai mic număr dintr-un set de valori."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Returnează valoarea cea mai frecventă sau repetitivă dintr-o matrice sau un interval de date."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Returnează luna unei date reprezentată de un număr de serie.Luna este dată ca număr întreg, variind de la 1 (ianuarie) la 12 (decembrie)."
                ],
                OR: [
                    "SAU(logic1, [logic2], ...)",
                    "returnează TRUE dacă oricare dintre argumentele sale este evaluat la TRUE și returnează FALSE dacă toate argumentele sale sunt evaluate la FALSE."
                ],
                PI: [
                    "PI()",
                    "Returnează numărul 3,14159265358979, constanta matematică pi."
                ],
                POWER: [
                    "PUTERE (număr, putere)",
                    "Returnează rezultatul unui număr ridicat la o putere."
                ],
                PRODUCT: [
                    "PRODUS(număr1, [număr2], ...)",
                    "înmulțește toate numerele date ca argumente și returnează produsul.PRODUS(A1:A3; C1:C3) este echivalent cu =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Scrie cu majuscule prima literă din fiecare cuvânt al unei valori de text."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Transformă grade în radiani."
                ],
                RAND: [
                    "RAND()",
                    "Returnează un număr real aleatoriu distribuit uniform, mai mare sau egal cu 0 și mai mic decât 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Returnează rangul unui număr într-o listă de numere."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(text_vechi, num_început, num_care, text_nou)",
                    "înlocuiește o parte dintr-un șir de text, în funcție de numărul de caractere pe care îl specificați, cu un șir de text diferit."
                ],
                REPT: [
                    "REPT(text, number_times)",
                    "Repetă textul de un anumit număr de ori."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "returnează ultimul caracter sau ultimele caractere dintr-un șir de text, în funcție de numărul de caractere specificat."
                ],
                ROUND: [
                    "ROUND(număr, num_cifre)",
                    "rotunjește un număr la un anumit număr de cifre."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(număr, num_cifre)",
                    "Rotunjește un număr în jos, spre zero."
                ],
                ROUNDUP: [
                    "ROUNDUP(număr, num_cifre)",
                    "Rotunjește un număr în sus, departe de 0 (zero)."
                ],
                ROW: [
                    "ROW()",
                    "Returnează referința celulei în care apare funcția ROW."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Returnează numărul de rânduri dintr-o referință sau o matrice."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "localizați un șir de text într-un al doilea șir de text și returnați numărul poziției de pornire a primului șir de text din primul caracter al celui de al doilea șir de text."
                ],
                SIN: [
                    "SIN(number)",
                    "Returnează sinusul unghiului dat (în radiani)."
                ],
                SMALL: [
                    "MIC (matrice, k)",
                    "Returnează a k-a cea mai mică valoare dintr-un set de date."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Returnează o rădăcină pătrată pozitivă."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Estimează abaterea standard pe baza unui eșantion.Abaterea standard este o măsură a modului în care valorile sunt dispersate față de valoarea medie (media)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Calculează abaterea standard pe baza întregii populații date ca argumente."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(text, text_vechi, text_nou, [num_instanță])",
                    "Înlocuiește text_nou cu text_vechi într-un șir de text."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Adaugă argumentele sale.Puteți adăuga valori individuale, referințe de celule sau intervale sau o combinație a tuturor celor trei."
                ],
                SUMIF: [
                    "SUMIF(gamă, criterii, [interval_sumă])",
                    "adaugă valorile dintr-un interval care îndeplinesc criteriile pe care le specificați"
                ],
                SUMIFS: [
                    "SUMIFS(interval_sumă, interval_criterii1, criteriu1, [interval_criterii2, criteriu2], ...)",
                    "adaugă toate argumentele sale care îndeplinesc mai multe criterii."
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(matrice1, [matrice2], [matrice3], ...)",
                    "Înmulțește componentele corespunzătoare din matricele date și returnează suma acelor produse."
                ],
                TAN: [
                    "TAN(number)",
                    "Returnează tangenta unghiului dat (în radiani)."
                ],
                TEXT: [
                    "TEXT(Valoare pe care doriți să o formatați, „Format codul pe care doriți să îl aplicați”)",
                    "Funcția TEXT vă permite să schimbați modul în care apare un număr, aplicând formatarea acestuia cu coduri de format."
                ],
                TIME: [
                    "TIME(oră, minut, secundă)",
                    "Returnează numărul zecimal pentru o anumită oră."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Returnează numărul zecimal al timpului reprezentat de un șir de text.Numărul zecimal este o valoare cuprinsă între 0 (zero) și 0,99988426, reprezentând orele de la 0:00:00 (12:00:00 AM) la 23:59:59 (23:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Returnează numărul de serie al datei curente."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Elimină toate spațiile din text, cu excepția spațiilor unice dintre cuvinte."
                ],
                TRUNC: [
                    "TRUNC(număr, [num_cifre])",
                    "Trunchiază un număr într-un număr întreg prin eliminarea părții fracționale a numărului."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Convertește textul în majuscule."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Convertește un șir de text care reprezintă un număr într-un număr."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Estimează variația pe baza unui eșantion."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Calculează varianța pe baza întregii populații."
                ],
                VLOOKUP: [
                    "CĂUTARE V (lookup_value, table_array, col_index_num, [range_lookup])",
                    "căutați o valoare într-un tabel sau într-un interval după rând.De exemplu, căutați prețul unei piese auto după numărul piesei."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Returnează anul corespunzător unei date.Anul este returnat ca un număr întreg în intervalul 1900-9999."
                ]
            },
            strFreezeFirstCol: "Înghețați prima coloană",
            strFreezePanes: "Înghețați geamurile",
            strFreezePanesUndo: "Dezghețați geamurile",
            strFreezeTopRow: "Înghețați primul rând",
            strFrozenCols: "Coloane înghețate",
            strFrozenRows: "Rânduri înghețate",
            strFullScreen: "Ecran complet",
            strGroup_fixCols: "Fixați coloanele",
            strGroup_grandSummary: "Mare rezumat",
            strGroup_header: "Trimiteți grupul după coloană",
            strGroup_merge: "Îmbinați celulele",
            strHideCols: "Ascundeți coloanele",
            strHideRows: "Ascundeți rândurile",
            strImport: "Importă date",
            strImportPic: "Inserați imaginea plutitoare",
            strImportPicCell: "Inserați imaginea în celulă",
            strInsertColumn: "Inserați o coloană",
            strInsertRow: "Inserați rând",
            strInsertRows: "Inserați {0} rânduri",
            strItalic: "Text italic",
            strLabel: "Etichetă",
            strLink: "Link",
            strLoading: "Încărcare",
            strLocal: "ro",
            strLockCells: "Blocați celulele",
            strMenu: {
                export: "Coloane exportabile",
                filter: "Filtru",
                hideCols: "Coloane vizibile"
            },
            strMerge: "Îmbinați celulele",
            strName: "Nume",
            strNextResult: "Următorul rezultat",
            strNoRows: "Nu există rânduri de afișat.",
            strNothingFound: "Nu s-a găsit nimic",
            strOR: "SAU",
            strOk: "Ok",
            strOpen: "Deschide",
            strPaste: "Lipiți",
            strPrevResult: "Rezultatul anterior",
            strRedo: "Reface",
            strRename: "Redenumiți",
            strSearch: "Caută",
            strSelectAll: "Selectați Toate",
            strSelectedmatches: "S-au selectat {0} din {1} potriviri",
            strShowCols: "Afișați coloanele",
            strShowRows: "Afișați rânduri",
            strTP_aggPH: "Eliminați coloanele pentru agregate",
            strTP_aggPane: "Agregate",
            strTP_colPH: "Eliminați coloanele pentru gruparea coloanelor",
            strTP_colPane: "Grupați coloanele",
            strTP_pivot: "Modul pivot",
            strTP_rowPH: "Eliminați coloanele pentru gruparea rândurilor",
            strTP_rowPane: "Grupați rânduri",
            strTabAdd: "Foaie nouă",
            strTabClose: "Scoateți foaia",
            strTabHide: "Ascunde foaia",
            strTabName: "sheet{0}",
            strTabRemove: "{0} va fi șters definitiv.\r\nesti sigur?",
            strTabRename: "Redenumiți foaia",
            strTabShow: "Arată foaia",
            strTextColor: "Culoare text",
            strUnderline: "Subliniați textul",
            strUndo: "Anulați",
            strUnhide: "Afișează",
            strUnmerge: "Deconectați celulele",
            strUpdate: "Actualizare",
            strWrap: "Încheiere text"
        },
        pager = pq.pqPager.regional.ro = {
            strDisplay: "Se afișează de la {0} la {1} din {2} articole.",
            strFirstPage: "Prima Pagina",
            strLastPage: "Ultima Pagina",
            strNextPage: "Pagina următoare",
            strPage: "Pagina {0} din {1}",
            strPrevPage: "Pagina anterioară",
            strRefresh: "Reîmprospătați",
            strRpp: "Înregistrări pe pagină: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();