(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.cs = {
            strAND: "A",
            strAddColLeft: "Přidat sloupec vlevo",
            strAddColRight: "Přidat sloupec vpravo",
            strAddColumn: "Přidat sloupec",
            strAddRow: "Přidat řádek",
            strAddRowAbove: "Přidat řádek výše",
            strAddRowBelow: "Přidejte řádek níže",
            strAddRows: "Přidat řádky ({0}).",
            strAlign: {
                bottom: "Spodní zarovnání",
                center: "Zarovnání na střed",
                left: "Zarovnat doleva",
                right: "Zarovnat vpravo",
                top: "Zarovnat nahoru"
            },
            strAlignH: "Horizontální zarovnání",
            strAlignV: "Vertikální zarovnání",
            strApply: "Použít",
            strBlanks: "( mezery )",
            strBold: "Tučný text",
            strBorder: "Hranice",
            strBorderColor: "Barva ohraničení",
            strBorderStyle: "Styl ohraničení",
            strBorders: {
                all: "Všechny hranice",
                bottom: "Dolní okraj",
                horizontal: "Horizontální hranice",
                inner: "Vnitřní hranice",
                left: "Levá hranice",
                none: "Bez hranic",
                outer: "Vnější hranice",
                right: "Pravá hranice",
                top: "Horní okraj",
                vertical: "Vertikální ohraničení"
            },
            strCancel: "Zrušit",
            strClear: "Jasný",
            strClearColor: "Jasná barva",
            strClearFilter: "Vymazat filtr",
            strClearText: "Vymazat text",
            strCollapse: "Sbalit panel",
            strComment: "Komentář",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Žádné --",
                begin: "Začíná s",
                between: "Mezi tím",
                contain: "Obsahuje",
                empty: "Prázdný",
                end: "Končí s",
                equal: "rovná se",
                great: "Větší než",
                gte: "Větší nebo rovno",
                less: "Méně než",
                lte: "Menší nebo rovno",
                notbegin: "Ne začíná s",
                notcontain: "Neobsahuje",
                notempty: "Ne prázdné",
                notend: "Ne nekončí",
                notequal: "Ne rovná se",
                range: "[rozsah]",
                regexp: "Regulární výraz"
            },
            strCopy: "Kopírovat",
            strCut: "Řez",
            strDelete: "Smazat",
            strDeleteColumn: "Smazat sloupec",
            strDeleteRow: "Smazat řádek",
            strDownloadXlsx: "Stáhnout soubor Excel",
            strEdit: "Upravit",
            strExitFullScreen: "Ukončete celou obrazovku",
            strExpand: "Rozbalit panel",
            strExport: "Export dat",
            strFillColor: "Barva výplně",
            strFontFamily: "Rodina písem",
            strFontSize: "Velikost písma",
            strFormat: "Formátování buněk",
            strFormatMenu: {
                Accounting: "Účetnictví",
                Currency: "Měna",
                Custom: "Vlastní",
                DateTime: "Datum Čas",
                Fixed: "Desetinné",
                Fraction: "Zlomek",
                FullDate: "Celé datum",
                General: "Generál",
                Int: "Celé číslo",
                LongDate: "Dlouhé rande",
                MediumDate: "Střední datum",
                Note: "Poznámka: Formáty označené * se mění podle národního prostředí uživatele.",
                Percent: "Procento",
                Scientific: "Vědecký",
                ShortDate: "Krátké datum",
                Standard: "Standardní",
                Text: "Text",
                Time: "Čas"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Vrátí absolutní hodnotu čísla.Absolutní hodnota čísla je číslo bez znaménka."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Vrátí arkkosinus nebo inverzní kosinus čísla.Arkusinus je úhel, jehož kosinus je číslo.Vrácený úhel je udán v radiánech v rozsahu 0 (nula) až pí."
                ],
                AND: [
                    "AND(logický1, [logický2], ...)",
                    "Vrátí TRUE, pokud jsou všechny jeho argumenty vyhodnoceny jako TRUE, a vrátí FALSE, pokud je jeden nebo více argumentů vyhodnoceno jako FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Vrátí arkussinus neboli inverzní sinus čísla.Arkussinus je úhel, jehož sinus je číslo.Vrácený úhel je udán v radiánech v rozsahu -pi/2 až pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Vrátí arkustangens neboli inverzní tangens čísla.Arkustangens je úhel, jehož tangens je číslo.Vrácený úhel je udán v radiánech v rozsahu -pi/2 až pi/2."
                ],
                AVERAGE: [
                    "AVERAGE(číslo1; [číslo2]; ...)",
                    "Vrátí průměr (aritmetický průměr) argumentů.Pokud například rozsah A1:A20 obsahuje čísla, vzorec =AVERAGE(A1:A20) vrátí průměr těchto čísel."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(rozsah; kritéria; [průměrný_rozsah])",
                    "Vrátí průměr (aritmetický průměr) všech buněk v rozsahu, které splňují zadaná kritéria."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(průměrný_rozsah; rozsah_kritérií1; kritérium1; [rozsah_kritérií2; kritéria2]; ...)",
                    "Vrátí průměr (aritmetický průměr) všech buněk, které splňují více kritérií."
                ],
                CEILING: [
                    "STROP(číslo, význam)",
                    "Vrátí číslo zaokrouhlené nahoru, směrem od nuly, na nejbližší násobek významnosti.Pokud se například chcete vyhnout používání haléřů v cenách a váš produkt má cenu 4,42 USD, použijte vzorec =STROP(4,42, 0,05) k zaokrouhlení cen nahoru na nejbližší nikl."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Vrátí znak určený číslem."
                ],
                CHOOSE: [
                    "CHOOSE(číslo_indexu; hodnota1; [hodnota2]; ...)",
                    "Pokud je index_num 1, CHOOSE vrátí hodnotu1;pokud je 2, CHOOSE vrátí hodnotu2;a tak dále."
                ],
                CODE: [
                    "CODE(text)",
                    "Vrátí číselný kód pro první znak v textovém řetězci."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Vrátí odkaz na buňku, ve které se funkce COLUMN vyskytuje."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Vrátí počet sloupců v poli nebo odkazu."
                ],
                CONCATENATE: [
                    "CONCATENATE(text1; [text2]; ...)",
                    "spojit dva nebo více textových řetězců do jednoho řetězce."
                ],
                COS: [
                    "COS(number)",
                    "Vrátí kosinus daného úhlu (v radiánech)."
                ],
                COUNT: [
                    "POČET(hodnota1; [hodnota2]; ...)",
                    "Spočítá počet buněk, které obsahují čísla, a spočítá čísla v seznamu argumentů."
                ],
                COUNTA: [
                    "COUNTA(hodnota1; [hodnota2]; ...)",
                    "Funkce COUNTA počítá počet buněk, které nejsou prázdné v rozsahu."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Počítá prázdné buňky v určeném rozsahu buněk."
                ],
                COUNTIF: [
                    "COUNTIF(rozsah; kritéria)",
                    "spočítá počet buněk, které splňují kritérium;například spočítat, kolikrát se konkrétní město objevilo v seznamu zákazníků."
                ],
                COUNTIFS: [
                    "COUNTIFS(rozsah_kritérií1; kritérium1; [rozsah_kritérií2; kritéria2]…)",
                    "Aplikuje kritéria na buňky ve více rozsazích a spočítá, kolikrát byla všechna kritéria splněna."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "vrátí pořadové číslo, které představuje konkrétní datum."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Vypočítá počet dní, měsíců nebo let mezi dvěma daty.Jednotkou může být „Y“, „M“ nebo „D“."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "převede datum uložené jako text na sériové číslo, které Excel rozpozná jako datum.Například vzorec =DATEVALUE(\"1/1/2008\") vrátí 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Vrátí den data reprezentovaný pořadovým číslem."
                ],
                DAYS: [
                    "DAYS(datum_konce; datum_zahájení)",
                    "Vrátí počet dní mezi dvěma daty."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Převádí radiány na stupně."
                ],
                EOMONTH: [
                    "EOMONTH(počáteční_datum; měsíce)",
                    "Vrátí sériové číslo pro poslední den v měsíci, což je zadaný počet měsíců před nebo po start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Vrátí e umocněné na číslo."
                ],
                FIND: [
                    "NAJÍT(najít_text; v_textu; [počáteční_číslo])",
                    "Vyhledá jeden textový řetězec v druhém textovém řetězci a vrátí číslo počáteční pozice prvního textového řetězce z prvního znaku druhého textového řetězce."
                ],
                FLOOR: [
                    "PODLAŽÍ(číslo; význam)",
                    "Zaokrouhlí číslo dolů, směrem k nule, na nejbližší násobek významnosti."
                ],
                HLOOKUP: [
                    "HLOOKUP(vyhledávací_hodnota; pole_tabulky; číslo indexu_řádku; [vyhledání_rozsahu])",
                    "Vyhledá hodnotu v horním řádku tabulky nebo pole hodnot a poté vrátí hodnotu ve stejném sloupci z řádku, který zadáte v tabulce nebo poli."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Vrátí hodinu časové hodnoty.Hodina je uvedena jako celé číslo v rozsahu od 0 (00:00) do 23 (23:00)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [přátelský_název])",
                    "vytvoří zástupce, který po kliknutí na buňku přeskočí na jiné místo na internetu"
                ],
                IF: [
                    "KDYŽ(logický_test, hodnota_pokud_pravda, [hodnota_pokud_nepravda])",
                    "vrátí jednu hodnotu, pokud je podmínka pravdivá, a jinou hodnotu, pokud je nepravdivá."
                ],
                INDEX: [
                    "INDEX(pole; číslo_řádku; [číslo_sloupce])",
                    "Vrátí hodnotu prvku v tabulce nebo poli vybranou indexy čísel řádků a sloupců."
                ],
                INDIRECT: [
                    "NEPŘÍMÉ(ref_text; [a1])",
                    "Vrátí odkaz určený textovým řetězcem.Reference jsou okamžitě vyhodnoceny, aby se zobrazil jejich obsah."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Pokud je hodnota prázdná, vrátí hodnotu TRUE"
                ],
                LARGE: [
                    "VELKÉ(pole; k)",
                    "Vrátí k-tou největší hodnotu v sadě dat."
                ],
                LEFT: [
                    "VLEVO(text; [počet_znaků])",
                    "vrátí první znak nebo znaky v textovém řetězci na základě zadaného počtu znaků."
                ],
                LEN: [
                    "LEN(text)",
                    "vrátí počet znaků v textovém řetězci."
                ],
                LOOKUP: [
                    "VYHLEDAT(hledaná_hodnota; vyhledávací_vektor; [vektor_výsledku])",
                    "Vyhledá hodnotu v rozsahu jednoho řádku nebo jednoho sloupce (známého jako vektor) a vrátí hodnotu ze stejné pozice v druhém rozsahu jednoho řádku nebo jednoho sloupce."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Převede všechna velká písmena v textovém řetězci na malá písmena."
                ],
                MATCH: [
                    "MATCH(hledaná_hodnota; vyhledávací_pole; [typ_shody])",
                    "Vyhledá zadanou položku v rozsahu buněk a poté vrátí relativní pozici této položky v rozsahu.match_type může být: 0 pro přesnou shodu s možností použití zástupných znaků;1 ( výchozí ) pro méně než, Hodnoty v argumentu pole vyhledávání musí být umístěny ve vzestupném pořadí;-1 pro větší než, hodnoty v argumentu pole_hledání musí být umístěny v sestupném pořadí."
                ],
                MAX: [
                    "MAX(číslo1; [číslo2]; ...)",
                    "Vrátí největší hodnotu v sadě hodnot."
                ],
                MEDIAN: [
                    "MEDIAN(číslo1; [číslo2]; ...)",
                    "Vrátí medián daných čísel.Medián je číslo uprostřed množiny čísel."
                ],
                MID: [
                    "MID(text; počáteční_číslo; počet_znaků)",
                    "vrátí konkrétní počet znaků z textového řetězce, počínaje od zadané pozice, na základě zadaného počtu znaků."
                ],
                MIN: [
                    "MIN(číslo1; [číslo2]; ...)",
                    "Vrátí nejmenší číslo v sadě hodnot."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Vrátí nejčastěji se vyskytující nebo opakující se hodnotu v poli nebo rozsahu dat."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Vrátí měsíc data reprezentovaný pořadovým číslem.Měsíc je uveden jako celé číslo v rozsahu od 1 (leden) do 12 (prosinec)."
                ],
                OR: [
                    "NEBO(logický1; [logický2]; ...)",
                    "vrátí TRUE, pokud je některý z jeho argumentů vyhodnocen jako TRUE, a vrátí FALSE, pokud všechny jeho argumenty vyhodnotí jako FALSE."
                ],
                PI: [
                    "PI()",
                    "Vrátí číslo 3,14159265358979, matematickou konstantu pí."
                ],
                POWER: [
                    "POWER(číslo, síla)",
                    "Vrátí výsledek čísla umocněného na mocninu."
                ],
                PRODUCT: [
                    "PRODUKT(číslo1; [číslo2]; ...)",
                    "vynásobí všechna čísla zadaná jako argumenty a vrátí součin.PRODUKT(A1:A3, C1:C3) je ekvivalentní =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Převede první písmeno v každém slově textové hodnoty na velké."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Převádí stupně na radiány."
                ],
                RAND: [
                    "RAND()",
                    "Vrátí rovnoměrně rozložené náhodné reálné číslo větší nebo rovné 0 a menší než 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Vrátí pořadí čísla v seznamu čísel."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(starý_text, počáteční_číslo, počet_znaků, nový_text)",
                    "nahradí část textového řetězce na základě počtu znaků, které zadáte, jiným textovým řetězcem."
                ],
                REPT: [
                    "REPT(text; počet_krát)",
                    "Opakuje text stanovený početkrát."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "vrátí poslední znak nebo znaky v textovém řetězci na základě zadaného počtu znaků."
                ],
                ROUND: [
                    "ROUND(číslo; počet_číslic)",
                    "zaokrouhlí číslo na zadaný počet číslic."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(číslo; počet_číslic)",
                    "Zaokrouhlí číslo dolů, směrem k nule."
                ],
                ROUNDUP: [
                    "ROUNDUP(číslo; počet_číslic)",
                    "Zaokrouhlí číslo nahoru od 0 (nuly)."
                ],
                ROW: [
                    "ROW()",
                    "Vrátí odkaz na buňku, ve které se funkce ROW vyskytuje."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Vrátí počet řádků v odkazu nebo poli."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "vyhledejte jeden textový řetězec v druhém textovém řetězci a vraťte číslo počáteční pozice prvního textového řetězce z prvního znaku druhého textového řetězce."
                ],
                SIN: [
                    "SIN(number)",
                    "Vrátí sinus daného úhlu (v radiánech)."
                ],
                SMALL: [
                    "MALÝ(pole; k)",
                    "Vrátí k-tou nejmenší hodnotu v sadě dat."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Vrátí kladnou druhou odmocninu."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Odhaduje směrodatnou odchylku na základě vzorku.Směrodatná odchylka je mírou toho, jak široce jsou hodnoty rozptýleny od průměrné hodnoty (průměr)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Vypočítá směrodatnou odchylku na základě celé populace zadané jako argumenty."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(text; starý_text; nový_text; [číslo_instance])",
                    "Nahrazuje nový_text za starý_text v textovém řetězci."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Přidává své argumenty.Můžete přidat jednotlivé hodnoty, odkazy na buňky nebo rozsahy nebo kombinaci všech tří."
                ],
                SUMIF: [
                    "SUMIF(rozsah; kritéria; [rozsah_součtu])",
                    "přidá hodnoty v rozsahu, který splňuje kritéria, která zadáte"
                ],
                SUMIFS: [
                    "SUMIFS(rozsah_součtu; rozsah_kritérií1; kritérium1; [rozsah_kritérií2; kritéria2]; ...)",
                    "přidá všechny své argumenty, které splňují více kritérií."
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(pole1; [pole2]; [pole3]; ...)",
                    "Vynásobí odpovídající komponenty v daných polích a vrátí součet těchto součinů."
                ],
                TAN: [
                    "TAN(number)",
                    "Vrátí tangens daného úhlu (v radiánech)."
                ],
                TEXT: [
                    "TEXT(hodnota, kterou chcete formátovat, \"Formátovat kód, který chcete použít\")",
                    "Funkce TEXT vám umožňuje změnit způsob zobrazení čísla tím, že na něj použijete formátování pomocí kódů formátu."
                ],
                TIME: [
                    "TIME(hodina, minuta, sekunda)",
                    "Vrátí desetinné číslo pro konkrétní čas."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Vrátí desetinné číslo času reprezentovaného textovým řetězcem.Desetinné číslo je hodnota v rozsahu od 0 (nula) do 0,99988426, která představuje časy od 0:00:00 (00:00:00 AM) do 23:59:59 (23:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Vrátí sériové číslo aktuálního data."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Odebere z textu všechny mezery kromě jednotlivých mezer mezi slovy."
                ],
                TRUNC: [
                    "TRUNC(číslo; [počet_číslic])",
                    "Zkrátí číslo na celé číslo odstraněním zlomkové části čísla."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Převede text na velká písmena."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Převede textový řetězec, který představuje číslo, na číslo."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Odhaduje rozptyl na základě vzorku."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Vypočítá rozptyl na základě celé populace."
                ],
                VLOOKUP: [
                    "SVYHLEDAT (vyhledávací_hodnota, pole_tabulky, číslo indexu_sloupce, [vyhledání_rozsahu])",
                    "vyhledat hodnotu v tabulce nebo rozsah po řádku.Vyhledejte například cenu automobilového dílu podle čísla dílu."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Vrátí rok odpovídající datu.Rok je vrácen jako celé číslo v rozsahu 1900-9999."
                ]
            },
            strFreezeFirstCol: "Zmrazit první sloupec",
            strFreezePanes: "Zmrazit tabule",
            strFreezePanesUndo: "Rozmrazte panely",
            strFreezeTopRow: "Zmrazit první řádek",
            strFrozenCols: "Zmrazené sloupy",
            strFrozenRows: "Zmrazené řádky",
            strFullScreen: "Celá obrazovka",
            strGroup_fixCols: "Opravte sloupce",
            strGroup_grandSummary: "Velké shrnutí",
            strGroup_header: "Přetáhněte skupinu po sloupci",
            strGroup_merge: "Sloučit buňky",
            strHideCols: "Skrýt sloupce",
            strHideRows: "Skrýt řádky",
            strImport: "Importujte data",
            strImportPic: "Vložit plovoucí obrázek",
            strImportPicCell: "Vložit obrázek do buňky",
            strInsertColumn: "Vložit sloupec",
            strInsertRow: "Vložit řádek",
            strInsertRows: "Vložit řádky ({0}).",
            strItalic: "Text kurzívou",
            strLabel: "Štítek",
            strLink: "Odkaz",
            strLoading: "Načítání",
            strLocal: "cs",
            strLockCells: "Zamknout buňky",
            strMenu: {
                export: "Exportovatelné sloupce",
                filter: "Filtr",
                hideCols: "Viditelné sloupce"
            },
            strMerge: "Sloučit buňky",
            strName: "Jméno",
            strNextResult: "Další výsledek",
            strNoRows: "Žádné řádky k zobrazení.",
            strNothingFound: "Nic nenalezeno",
            strOR: "NEBO",
            strOk: "Dobře",
            strOpen: "Otevřít",
            strPaste: "Vložit",
            strPrevResult: "Předchozí výsledek",
            strRedo: "Znovu",
            strRename: "Přejmenovat",
            strSearch: "Hledat",
            strSelectAll: "Vyberte Vše",
            strSelectedmatches: "Vybráno {0} z {1} shod",
            strShowCols: "Zobrazit sloupce",
            strShowRows: "Zobrazit řádky",
            strTP_aggPH: "Vypustit sloupce pro agregáty",
            strTP_aggPane: "Agregáty",
            strTP_colPH: "Vynechejte sloupce pro seskupení sloupců",
            strTP_colPane: "Seskupit sloupce",
            strTP_pivot: "Pivotní režim",
            strTP_rowPH: "Vynechejte sloupce pro seskupení řádků",
            strTP_rowPane: "Seskupit řádky",
            strTabAdd: "Nový list",
            strTabClose: "Odstraňte list",
            strTabHide: "Skrýt list",
            strTabName: "sheet{0}",
            strTabRemove: "{0} bude trvale smazáno.\r\njsi si jistý?",
            strTabRename: "Přejmenovat list",
            strTabShow: "Zobrazit list",
            strTextColor: "Barva textu",
            strUnderline: "Podtrhněte text",
            strUndo: "Vrátit zpět",
            strUnhide: "Odkrýt",
            strUnmerge: "Zrušte sloučení buněk",
            strUpdate: "Aktualizovat",
            strWrap: "Zalomit text"
        },
        pager = pq.pqPager.regional.cs = {
            strDisplay: "Zobrazuje se {0} až {1} z {2} položek.",
            strFirstPage: "První stránka",
            strLastPage: "Poslední stránka",
            strNextPage: "Další stránka",
            strPage: "Stránka {0} z {1}",
            strPrevPage: "Předchozí stránka",
            strRefresh: "Obnovit",
            strRpp: "Počet záznamů na stránku: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();