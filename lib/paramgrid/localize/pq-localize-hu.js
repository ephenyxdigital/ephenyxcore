(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.hu = {
            strAND: "ÉS",
            strAddColLeft: "Bal oldali oszlop hozzáadása",
            strAddColRight: "Jobb oszlop hozzáadása",
            strAddColumn: "Oszlop hozzáadása",
            strAddRow: "Sor hozzáadása",
            strAddRowAbove: "Add hozzá a fenti sort",
            strAddRowBelow: "Adjon hozzá egy sort alább",
            strAddRows: "Adjon hozzá {0} sort",
            strAlign: {
                bottom: "Alulra igazítás",
                center: "Középre igazítás",
                left: "Balra igazítás",
                right: "Jobbra igazítás",
                top: "Felső igazítás"
            },
            strAlignH: "Vízszintes igazítás",
            strAlignV: "Függőleges igazítás",
            strApply: "Alkalmazni",
            strBlanks: "( Üres )",
            strBold: "Félkövér szöveg",
            strBorder: "Határ",
            strBorderColor: "Szegély színe",
            strBorderStyle: "Szegély stílus",
            strBorders: {
                all: "Minden határ",
                bottom: "Alsó szegély",
                horizontal: "Vízszintes határ",
                inner: "Belső határ",
                left: "Bal határ",
                none: "Nincs határ",
                outer: "Külső Határ",
                right: "Jobb határ",
                top: "Felső szegély",
                vertical: "Függőleges szegély"
            },
            strCancel: "Mégsem",
            strClear: "Világos",
            strClearColor: "Tiszta szín",
            strClearFilter: "Szűrő törlése",
            strClearText: "Szöveg törlése",
            strCollapse: "Panel összecsukása",
            strComment: "Megjegyzés",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Egyik sem --",
                begin: "Ezzel kezdődik",
                between: "Közben",
                contain: "Tartalmaz",
                empty: "Üres",
                end: "Ezzel véget ér",
                equal: "Egyenlő",
                great: "Nagyobb mint",
                gte: "Nagyobb vagy egyenlő",
                less: "Kevesebb mint",
                lte: "Kisebb vagy egyenlő",
                notbegin: "Nem azzal kezdődik",
                notcontain: "Nem tartalmaz",
                notempty: "Nem üres",
                notend: "Nem ezzel végződik",
                notequal: "Nem egyenlő",
                range: "[ Tartomány ]",
                regexp: "Reguláris kifejezés"
            },
            strCopy: "Másolás",
            strCut: "Vágás",
            strDelete: "Töröl",
            strDeleteColumn: "Oszlop törlése",
            strDeleteRow: "Sor törlése",
            strDownloadXlsx: "Töltse le az Excel fájlt",
            strEdit: "Szerkeszt",
            strExitFullScreen: "Lépjen ki a teljes képernyőből",
            strExpand: "Panel kibontása",
            strExport: "Adatok exportálása",
            strFillColor: "Kitöltés színe",
            strFontFamily: "Betűcsalád",
            strFontSize: "Betűméret",
            strFormat: "Formázza a cellákat",
            strFormatMenu: {
                Accounting: "Számvitel",
                Currency: "Pénznem",
                Custom: "Egyedi",
                DateTime: "Dátum Idő",
                Fixed: "Tizedes",
                Fraction: "Frakció",
                FullDate: "Teljes dátum",
                General: "tábornok",
                Int: "Egész szám",
                LongDate: "Hosszú randevú",
                MediumDate: "Közepes dátum",
                Note: "Megjegyzés: A *-gal jelölt formátumok a felhasználói területi beállításoktól függően változnak.",
                Percent: "Százalék",
                Scientific: "Tudományos",
                ShortDate: "Rövid dátum",
                Standard: "Szabványos",
                Text: "Szöveg",
                Time: "Idő"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Egy szám abszolút értékét adja vissza.Egy szám abszolút értéke az előjel nélküli szám."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Egy szám arckoszinuszát vagy inverz koszinuszát adja eredményül.Az arkoszinusz az a szög, amelynek koszinusza a szám.A visszaadott szög radiánban van megadva a 0 (nulla) és pi közötti tartományban."
                ],
                AND: [
                    "ÉS(logikai1, [logikai2], ...)",
                    "IGAZ értéket ad vissza, ha minden argumentuma IGAZ, és FALSE értéket ad vissza, ha egy vagy több argumentum HAMIS."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Egy szám arcszinuszát vagy inverz szinuszát adja eredményül.Az arcszinusz az a szög, amelynek szinusza a szám.A visszaadott szög radiánban van megadva a -pi/2 és pi/2 tartományban."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Egy szám arctangensét vagy inverz érintőjét adja eredményül.Az arctangens az a szög, amelynek érintője a szám.A visszaadott szög radiánban van megadva a -pi/2 és pi/2 tartományban."
                ],
                AVERAGE: [
                    "ÁTLAG(szám1, [szám2], ...)",
                    "Az argumentumok átlagát (számtani átlagát) adja eredményül.Például, ha az A1:A20 tartomány számokat tartalmaz, az =ÁTLAG(A1:A20) képlet ezeknek a számoknak az átlagát adja vissza."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(tartomány, feltételek, [átlagos_tartomány])",
                    "Egy adott kritériumnak megfelelő tartomány összes cellájának átlagát (számtani átlagát) adja eredményül."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(átlagos_tartomány, feltételtartomány1, kritérium1, [feltételtartomány2, kritérium2], ...)",
                    "A több feltételnek megfelelő összes cella átlagát (számtani átlagát) adja eredményül."
                ],
                CEILING: [
                    "MENNYEZET(szám, jelentősége)",
                    "A számot felfelé kerekítve adja vissza, nullától távolabb, a szignifikancia legközelebbi többszörösére.Például, ha el szeretné kerülni a fillérek használatát az árakban, és a termék ára 4,42 USD, használja a =MENET (4,42,0,05) képletet az árak felfelé kerekítéséhez a legközelebbi nikkelre."
                ],
                CHAR: [
                    "CHAR(number)",
                    "A szám által megadott karaktert adja vissza."
                ],
                CHOOSE: [
                    "KIVÁLASZT (index_szám, érték1, [érték2], ...)",
                    "Ha az index_száma 1, a CHOOSE értéke1 értéket adja vissza;ha 2, a CHOOSE értéke2 értéket adja vissza;és így tovább."
                ],
                CODE: [
                    "CODE(text)",
                    "A szöveges karakterlánc első karakterének numerikus kódját adja vissza."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Annak a cellának a hivatkozását adja vissza, amelyben az OSZLOP függvény megjelenik."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Egy tömbben vagy hivatkozásban lévő oszlopok számát adja vissza."
                ],
                CONCATENATE: [
                    "CONCATENATE(szöveg1, [szöveg2], ...)",
                    "két vagy több szöveges karakterláncot egyesíthet egy karakterláncba."
                ],
                COS: [
                    "COS(number)",
                    "Az adott szög koszinuszát adja eredményül (radiánban)."
                ],
                COUNT: [
                    "COUNT(érték1, [érték2], ...)",
                    "Megszámolja a számokat tartalmazó cellák számát, és megszámolja a számokat az argumentumlistán belül."
                ],
                COUNTA: [
                    "COUNTA(érték1, [érték2], ...)",
                    "A COUNTA függvény megszámolja a tartományban nem üres cellák számát."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Megszámolja az üres cellákat egy megadott cellatartományban."
                ],
                COUNTIF: [
                    "COUNTIF(tartomány, kritérium)",
                    "megszámolja a kritériumnak megfelelő cellák számát;például, hogy megszámolja, hányszor jelenik meg egy adott város az ügyféllistán."
                ],
                COUNTIFS: [
                    "COUNTIFS(feltételtartomány1, kritérium1, [feltételtartomány2, kritérium2]…)",
                    "Feltételeket alkalmaz több tartományban lévő cellákra, és megszámolja, hányszor teljesül az összes feltétel."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "egy adott dátumot jelentő sorozatszámot adja vissza."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Kiszámítja a két dátum közötti napok, hónapok vagy évek számát.Az egység lehet 'Y', 'M' vagy 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "a szövegként tárolt dátumot sorozatszámmá alakítja, amelyet az Excel dátumként ismer fel.Például a =DATEVALUE(\"1/1/2008\") képlet 39448-at ad vissza."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Egy dátum napját adja vissza, egy sorozatszámmal."
                ],
                DAYS: [
                    "DAYS(záró_dátum, kezdési_dátum)",
                    "Két dátum közötti napok számát adja vissza."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "A radiánokat fokokká alakítja."
                ],
                EOMONTH: [
                    "EOMONTH(kezdési_dátum, hónapok)",
                    "A hónap utolsó napjának sorozatszámát adja vissza, amely a start_date előtti vagy utáni hónapok száma."
                ],
                EXP: [
                    "EXP(number)",
                    "Az e-t a szám hatványára emelve adja vissza."
                ],
                FIND: [
                    "KERESÉS(szöveg keresése, szövegen belül, [kezdeti_szám])",
                    "Megkeres egy szöveges karakterláncot egy második szöveges karakterláncon belül, és visszaadja az első szöveges karakterlánc kezdőpozíciójának számát a második karakterlánc első karakteréből."
                ],
                FLOOR: [
                    "EMELET(szám, jelentősége)",
                    "Lefelé kerekíti a számot, nulla felé, a szignifikancia legközelebbi többszörösére."
                ],
                HLOOKUP: [
                    "HLOOKUP(keresési_érték, táblázat_tömb, sor_index_száma, [tartomány_keresése])",
                    "Értéket keres egy táblázat vagy értéktömb legfelső sorában, majd ugyanabban az oszlopban ad vissza egy értéket a táblázatban vagy tömbben megadott sorból."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Egy időérték óráját adja vissza.Az óra egész számként van megadva, 0 (12:00) és 23 (23:00) között."
                ],
                HYPERLINK: [
                    "HIPERLINK(url, [barátságos_név])",
                    "parancsikont hoz létre, amely egy másik helyre ugrik az interneten, amikor egy cellára kattint"
                ],
                IF: [
                    "IF(logikai_teszt, érték_ha_igaz, [érték_ha_hamis])",
                    "egy értéket ad vissza, ha egy feltétel igaz, és egy másik értéket, ha hamis."
                ],
                INDEX: [
                    "INDEX(tömb, sor_száma, [oszlop_száma])",
                    "Egy táblázatban vagy tömbben lévő elem értékét adja vissza, amelyet a sor- és oszlopszám-indexek választanak ki."
                ],
                INDIRECT: [
                    "INDIRECT(ref_text, [a1])",
                    "A szöveges karakterlánc által megadott hivatkozást adja vissza.A hivatkozásokat azonnal kiértékeljük, hogy megjelenítsük tartalmukat."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "IGAZ értéket ad vissza, ha az érték üres"
                ],
                LARGE: [
                    "LARGE(tömb, k)",
                    "Egy adathalmaz k-adik legnagyobb értékét adja vissza."
                ],
                LEFT: [
                    "LEFT(szöveg, [karakterek száma])",
                    "visszaadja egy szöveges karakterlánc első karakterét vagy karaktereit, a megadott karakterek száma alapján."
                ],
                LEN: [
                    "LEN(text)",
                    "egy szöveges karakterlánc karaktereinek számát adja vissza."
                ],
                LOOKUP: [
                    "KERESÉS(keresési_érték, keresési_vektor, [eredményvektor])",
                    "Egysoros vagy egyoszlopos tartományban (vektorként ismert) keres egy értéket, és ugyanabból a pozícióból ad vissza egy értéket egy második egysoros vagy egyoszlopos tartományban."
                ],
                LOWER: [
                    "LOWER(text)",
                    "A szöveges karakterlánc összes nagybetűjét kisbetűvé alakítja."
                ],
                MATCH: [
                    "EGYEZÉS(keresési_érték, keresési_tömb, [egyezési_típus])",
                    "Megkeres egy adott elemet egy cellatartományban, majd visszaadja az elem relatív pozícióját a tartományban.match_type lehet: 0 a pontos egyezéshez helyettesítő karakterek használatának lehetőségével;1 (alapértelmezett ) kisebb, mint, A lookup_array argumentum értékeit növekvő sorrendben kell elhelyezni;-1 nagyobb mint esetén, a lookup_array argumentum értékeit csökkenő sorrendben kell elhelyezni."
                ],
                MAX: [
                    "MAX(szám1, [szám2], ...)",
                    "Egy értékkészlet legnagyobb értékét adja vissza."
                ],
                MEDIAN: [
                    "MEDIAN(szám1, [szám2], ...)",
                    "A megadott számok mediánját adja eredményül.A medián egy számhalmaz közepén lévő szám."
                ],
                MID: [
                    "MID(szöveg, kezdő_szám, karakterek száma)",
                    "adott számú karaktert ad vissza egy szöveges karakterláncból, a megadott pozíciótól kezdve, a megadott karakterek száma alapján."
                ],
                MIN: [
                    "MIN(szám1, [szám2], ...)",
                    "Egy értékkészlet legkisebb számát adja vissza."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Egy tömbben vagy adattartományban a leggyakrabban előforduló vagy ismétlődő értéket adja vissza."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Egy dátum hónapját adja vissza, amelyet sorozatszám képvisel.A hónap egész számként van megadva, 1 (január) és 12 (december) között."
                ],
                OR: [
                    "VAGY(logikai1, [logikai2], ...)",
                    "IGAZ értéket ad vissza, ha bármely argumentuma IGAZ értékű, és FALSE értéket ad vissza, ha minden argumentuma HAMIS."
                ],
                PI: [
                    "PI()",
                    "Visszaadja a 3,14159265358979 számot, a pi matematikai állandót."
                ],
                POWER: [
                    "TELJESÍTMÉNY(szám, teljesítmény)",
                    "Hatványra emelt szám eredményét adja vissza."
                ],
                PRODUCT: [
                    "TERMÉK(szám1, [szám2], ...)",
                    "megszorozza az argumentumként megadott számokat, és visszaadja a szorzatot.TERMÉK(A1:A3, C1:C3) =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "A szövegérték minden szavának első betűjét nagybetűvel írja."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "A fokokat radiánokká alakítja."
                ],
                RAND: [
                    "RAND()",
                    "Egyenletes eloszlású véletlenszerű valós számot ad eredményül, amely nagyobb vagy egyenlő, mint 0 és kisebb, mint 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Egy szám rangját adja vissza egy számlistában."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(régi_szöveg, kezdő_szám, karakterek száma, új_szöveg)",
                    "lecseréli egy szöveges karakterlánc egy részét a megadott karakterek száma alapján egy másik szöveges karakterláncra."
                ],
                REPT: [
                    "REPT(szöveg;szám_szor)",
                    "Adott számú alkalommal ismétli a szöveget."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "a megadott karakterek száma alapján egy szöveges karakterlánc utolsó karakterét vagy karaktereit adja vissza."
                ],
                ROUND: [
                    "ROUND(szám, számjegyek_száma)",
                    "egy számot meghatározott számú számjegyre kerekít."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(szám, számjegyek_száma)",
                    "Lefelé kerekít egy számot, nulla felé."
                ],
                ROUNDUP: [
                    "ROUNDUP(szám, számjegyek_száma)",
                    "Felfelé kerekít egy számot 0-tól (nullától) távolabb."
                ],
                ROW: [
                    "ROW()",
                    "Annak a cellának a hivatkozását adja vissza, amelyben a ROW függvény megjelenik."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Egy hivatkozásban vagy tömbben lévő sorok számát adja vissza."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "keressen meg egy szöveges karakterláncot egy második szöveges karakterláncon belül, és adja vissza az első szöveges karakterlánc kezdőpozíciójának számát a második karakterlánc első karakteréből."
                ],
                SIN: [
                    "SIN(number)",
                    "Az adott szög szinuszát adja eredményül (radiánban)."
                ],
                SMALL: [
                    "KIS(tömb, k)",
                    "Egy adathalmaz k-adik legkisebb értékét adja vissza."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Pozitív négyzetgyököt ad vissza."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Egy minta alapján megbecsüli a szórást.A szórás annak mértéke, hogy az értékek milyen mértékben oszlanak el az átlagos értéktől (átlag)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Kiszámítja a szórást az argumentumként megadott teljes sokaság alapján."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(szöveg, régi_szöveg, új_szöveg, [példányszám])",
                    "Helyettesíti az új_szöveget a régi_szöveg helyett egy szöveges karakterláncban."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Hozzáteszi az érveit.Hozzáadhat egyedi értékeket, cellahivatkozásokat vagy tartományokat, vagy mindhárom keverékét."
                ],
                SUMIF: [
                    "SUMIF(tartomány; feltételek; [összeg_tartomány])",
                    "hozzáadja az értékeket egy olyan tartományban, amely megfelel az Ön által megadott feltételeknek"
                ],
                SUMIFS: [
                    "SUMIFS(összeg_tartomány, feltételek_tartománya1, kritérium1, [feltétel_tartomány2, feltételek2], ...)",
                    "hozzáadja az összes olyan argumentumát, amely több feltételnek is megfelel."
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(tömb1, [tömb2], [tömb3], ...)",
                    "Megszorozza a megfelelő komponenseket az adott tömbökben, és ezeknek a szorzatoknak az összegét adja vissza."
                ],
                TAN: [
                    "TAN(number)",
                    "Az adott szög tangensét adja eredményül (radiánban)."
                ],
                TEXT: [
                    "SZÖVEG (Formázni kívánt érték, \"Alkalmazni kívánt kód formátuma\")",
                    "A SZÖVEG funkcióval megváltoztathatja a szám megjelenési módját úgy, hogy formátumkódokkal formázza őket."
                ],
                TIME: [
                    "IDŐ (óra, perc, másodperc)",
                    "Egy adott idő decimális számjegyét adja vissza."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "A szöveges karakterlánc által képviselt idő decimális számát adja vissza.A decimális szám 0 (nulla) és 0,99988426 közötti érték, amely a 0:00:00 (0:00:00) és 23:59:59 (23:59:59) közötti időpontokat jelenti."
                ],
                TODAY: [
                    "TODAY()",
                    "Az aktuális dátum sorozatszámát adja vissza."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Eltávolítja az összes szóközt a szövegből, kivéve a szavak közötti szóközöket."
                ],
                TRUNC: [
                    "TRUNC(szám, [számjegyek_száma])",
                    "Egész számmá csonkolja a számot a szám tört részének eltávolításával."
                ],
                UPPER: [
                    "UPPER(text)",
                    "A szöveget nagybetűssé alakítja."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Számot jelentő szöveges karakterláncot számmá alakít."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Becsli a szórást egy minta alapján."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Kiszámítja a szórást a teljes sokaság alapján."
                ],
                VLOOKUP: [
                    "VLOOKUP (keresési_érték, táblázat_tömb, oszlop_index_száma, [tartomány_keresése])",
                    "táblázatban vagy tartományban soronként keressen ki egy értéket.Például keresse meg egy autóalkatrész árát a cikkszám alapján."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "A dátumnak megfelelő évet adja vissza.Az évet egész számként adja vissza az 1900-9999 tartományban."
                ]
            },
            strFreezeFirstCol: "Az első oszlop rögzítése",
            strFreezePanes: "Lefagyasztja az ablakokat",
            strFreezePanesUndo: "Oldja fel az ablaktáblákat",
            strFreezeTopRow: "Az első sor rögzítése",
            strFrozenCols: "Lefagyott oszlopok",
            strFrozenRows: "Rögzített sorok",
            strFullScreen: "Teljes képernyő",
            strGroup_fixCols: "Oszlopok javítása",
            strGroup_grandSummary: "Nagy összefoglaló",
            strGroup_header: "Csoportosíts oszloponként",
            strGroup_merge: "Cellák egyesítése",
            strHideCols: "Oszlopok elrejtése",
            strHideRows: "Sorok elrejtése",
            strImport: "Adatok importálása",
            strImportPic: "Lebegő kép beszúrása",
            strImportPicCell: "Kép beszúrása a cellába",
            strInsertColumn: "Oszlop beszúrása",
            strInsertRow: "Sor beszúrása",
            strInsertRows: "{0} sor beszúrása",
            strItalic: "Dőlt szöveg",
            strLabel: "Címke",
            strLink: "Link",
            strLoading: "Betöltés",
            strLocal: "hu",
            strLockCells: "Zárja le a cellákat",
            strMenu: {
                export: "Exportálható oszlopok",
                filter: "Szűrés",
                hideCols: "Látható oszlopok"
            },
            strMerge: "Cellák egyesítése",
            strName: "Név",
            strNextResult: "Következõ találat",
            strNoRows: "Nincs megjelenítendő sorok",
            strNothingFound: "Nincs találat",
            strOR: "VAGY",
            strOk: "Rendben",
            strOpen: "Nyissa meg",
            strPaste: "Beillesztés",
            strPrevResult: "Elõzõ találat",
            strRedo: "Újra",
            strRename: "Átnevezés",
            strSearch: "Keresés",
            strSelectAll: "Válassza az Összes lehetőséget",
            strSelectedmatches: "{1} találatból {0} kiválasztva",
            strShowCols: "Oszlopok megjelenítése",
            strShowRows: "Sorok megjelenítése",
            strTP_aggPH: "Dobja el az aggregátumok oszlopait",
            strTP_aggPane: "Aggregátumok",
            strTP_colPH: "Dobja el az oszlopokat az oszlopok csoportosításához",
            strTP_colPane: "Csoportosítsa az oszlopokat",
            strTP_pivot: "Pivot mód",
            strTP_rowPH: "Dobja el az oszlopokat a sorok csoportosításához",
            strTP_rowPane: "Csoportosítsd a sorokat",
            strTabAdd: "Új lap",
            strTabClose: "Távolítsa el a lapot",
            strTabHide: "Lap elrejtése",
            strTabName: "sheet{0}",
            strTabRemove: "{0} véglegesen törölve lesz.\r\nBiztos vagy benne?",
            strTabRename: "Lap átnevezése",
            strTabShow: "Lap megjelenítése",
            strTextColor: "Szöveg színe",
            strUnderline: "A szöveg aláhúzása",
            strUndo: "Visszavonás",
            strUnhide: "Felfed",
            strUnmerge: "A cellák egyesítésének megszüntetése",
            strUpdate: "Frissítés",
            strWrap: "Szöveg tördelése"
        },
        pager = pq.pqPager.regional.hu = {
            strDisplay: "{2} találatból megjelenítve {0} - {1}.",
            strFirstPage: "Elsõ oldal",
            strLastPage: "Utolsó oldal",
            strNextPage: "Következõ oldal",
            strPage: "Oldal: {0} / {1}",
            strPrevPage: "Elõzõ oldal",
            strRefresh: "Frissítés",
            strRpp: "Találatok az oldalon: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();