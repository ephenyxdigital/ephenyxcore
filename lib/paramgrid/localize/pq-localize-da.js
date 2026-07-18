(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.da = {
            strAND: "OG",
            strAddColLeft: "Tilføj kolonne til venstre",
            strAddColRight: "Tilføj kolonne til højre",
            strAddColumn: "Tilføj kolonne",
            strAddRow: "Tilføj række",
            strAddRowAbove: "Tilføj række ovenfor",
            strAddRowBelow: "Tilføj række nedenfor",
            strAddRows: "Tilføj {0} rækker",
            strAlign: {
                bottom: "Juster nederst",
                center: "Centerjuster",
                left: "Venstrejusteret",
                right: "Højrejuster",
                top: "Top juster"
            },
            strAlignH: "Horisontal justering",
            strAlignV: "Lodret justering",
            strApply: "Ansøg",
            strBlanks: "(Blanke)",
            strBold: "Fed tekst",
            strBorder: "Grænse",
            strBorderColor: "Kantfarve",
            strBorderStyle: "Grænsestil",
            strBorders: {
                all: "Alle grænser",
                bottom: "Nederste kant",
                horizontal: "Vandret kant",
                inner: "Indre grænse",
                left: "Venstre grænse",
                none: "Ingen grænse",
                outer: "Ydergrænse",
                right: "Højre grænse",
                top: "Øverste kant",
                vertical: "Lodret kant"
            },
            strCancel: "Annuller",
            strClear: "Klar",
            strClearColor: "Klar farve",
            strClearFilter: "Ryd filter",
            strClearText: "Ryd tekst",
            strCollapse: "Skjul panelet",
            strComment: "Kommentar",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Ingen --",
                begin: "Begynder med",
                between: "Ind imellem",
                contain: "Indeholder",
                empty: "Tom",
                end: "Ender med",
                equal: "Lige til",
                great: "Større end",
                gte: "Større end eller lig",
                less: "Mindre end",
                lte: "Mindre end eller lig",
                notbegin: "Ikke begynder med",
                notcontain: "Ikke indeholder",
                notempty: "Ikke tom",
                notend: "slutter ikke med",
                notequal: "Ikke lig",
                range: "[Rækkevidde]",
                regexp: "Regelmæssigt udtryk"
            },
            strCopy: "Kopiér",
            strCut: "Klip",
            strDelete: "Slet",
            strDeleteColumn: "Slet kolonne",
            strDeleteRow: "Slet række",
            strDownloadXlsx: "Download Excel-fil",
            strEdit: "Rediger",
            strExitFullScreen: "Afslut fuld skærm",
            strExpand: "Udvid panelet",
            strExport: "Eksporter data",
            strFillColor: "Fyld farve",
            strFontFamily: "Fontfamilie",
            strFontSize: "Skriftstørrelse",
            strFormat: "Formater celler",
            strFormatMenu: {
                Accounting: "Regnskab",
                Currency: "Valuta",
                Custom: "Brugerdefineret",
                DateTime: "Dato Tid",
                Fixed: "Decimal",
                Fraction: "Brøk",
                FullDate: "Fuld dato",
                General: "Generelt",
                Int: "Heltal",
                LongDate: "Lang dato",
                MediumDate: "Medium dato",
                Note: "Bemærk: Formater med * ændres i henhold til brugerens landestandard.",
                Percent: "Procentdel",
                Scientific: "Videnskabeligt",
                ShortDate: "Kort dato",
                Standard: "Standard",
                Text: "Tekst",
                Time: "Tid"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Returnerer den absolutte værdi af et tal.Den absolutte værdi af et tal er tallet uden fortegn."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Returnerer arccosinus, eller invers cosinus, af et tal.Arccosinus er den vinkel, hvis cosinus er tal.Den returnerede vinkel er angivet i radianer i området 0 (nul) til pi."
                ],
                AND: [
                    "OG(logisk1, [logisk2], ...)",
                    "Returnerer SAND, hvis alle dens argumenter evalueres til SAND, og returnerer FALSK, hvis et eller flere argumenter evalueres til FALSK."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Returnerer arcsinus, eller invers sinus, af et tal.Arcsinus er den vinkel, hvis sinus er tal.Den returnerede vinkel er angivet i radianer i området -pi/2 til pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Returnerer arctangens, eller invers tangent, af et tal.Arktangensen er den vinkel, hvis tangent er tal.Den returnerede vinkel er angivet i radianer i området -pi/2 til pi/2."
                ],
                AVERAGE: [
                    "AVERAGE(tal1, [tal2], ...)",
                    "Returnerer gennemsnittet (aritmetisk middelværdi) af argumenterne.For eksempel, hvis området A1:A20 indeholder tal, returnerer formlen =MIDDEL(A1:A20) gennemsnittet af disse tal."
                ],
                AVERAGEIF: [
                    "MIDDELHVIS(interval; kriterier; [gennemsnit_interval])",
                    "Returnerer gennemsnittet (aritmetisk middelværdi) af alle celler i et område, der opfylder et givet kriterium."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(gennemsnitsområde, kriterieområde1, kriterie1, [kriterieområde2, kriterie2], ...)",
                    "Returnerer gennemsnittet (aritmetisk middelværdi) af alle celler, der opfylder flere kriterier."
                ],
                CEILING: [
                    "LOFT(tal, betydning)",
                    "Returnerer tal rundet op, væk fra nul, til nærmeste multiplum af betydning.For eksempel, hvis du vil undgå at bruge øre i dine priser, og dit produkt er prissat til $4,42, skal du bruge formlen =LOFT(4,42,0,05) til at runde priserne op til nærmeste nikkel."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Returnerer tegnet angivet af et tal."
                ],
                CHOOSE: [
                    "VÆLG(indekstal; værdi1; [værdi2], ...)",
                    "Hvis index_num er 1, returnerer CHOOSE værdi1;hvis det er 2, returnerer CHOOSE værdi2;og så videre."
                ],
                CODE: [
                    "CODE(text)",
                    "Returnerer en numerisk kode for det første tegn i en tekststreng."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Returnerer reference til den celle, hvori funktionen KOLONNE vises."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Returnerer antallet af kolonner i en matrix eller reference."
                ],
                CONCATENATE: [
                    "KONKATENERE(tekst1; [tekst2], ...)",
                    "sammenføj to eller flere tekststrenge til én streng."
                ],
                COS: [
                    "COS(number)",
                    "Returnerer cosinus for den givne vinkel (i radianer)."
                ],
                COUNT: [
                    "ANTAL(værdi1, [værdi2], ...)",
                    "Tæller antallet af celler, der indeholder tal, og tæller tal på listen over argumenter."
                ],
                COUNTA: [
                    "ANTAL(værdi1; [værdi2], ...)",
                    "Funktionen COUNTA tæller antallet af celler, der ikke er tomme i et område."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Tæller tomme celler i et specificeret celleområde."
                ],
                COUNTIF: [
                    "ANTALHVIS(interval; kriterier)",
                    "tæller antallet af celler, der opfylder et kriterium;for eksempel at tælle antallet af gange, en bestemt by optræder på en kundeliste."
                ],
                COUNTIFS: [
                    "ANTAL.HVIS(kriterieområde1; kriterium1; [kriterieområde2; kriterie2]...)",
                    "Anvender kriterier på celler på tværs af flere områder og tæller antallet af gange, alle kriterier er opfyldt."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "returnerer det sekventielle serienummer, der repræsenterer en bestemt dato."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Beregner antallet af dage, måneder eller år mellem to datoer.Enheden kan være 'Y', 'M' eller 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "konverterer en dato, der er gemt som tekst, til et serienummer, som Excel genkender som en dato.For eksempel returnerer formlen =DATOVÆRDI(\"1/1/2008\") 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Returnerer dagen for en dato, repræsenteret ved et serienummer."
                ],
                DAYS: [
                    "DAYS(slutdato, startdato)",
                    "Returnerer antallet af dage mellem to datoer."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Konverterer radianer til grader."
                ],
                EOMONTH: [
                    "EOMONTH(startdato, måneder)",
                    "Returnerer serienummeret for den sidste dag i måneden, som er det angivne antal måneder før eller efter start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Returnerer e hævet til tallets potens."
                ],
                FIND: [
                    "FIND(find_tekst; inden_tekst; [startnummer])",
                    "Finder en tekststreng i en anden tekststreng og returnerer nummeret på startpositionen for den første tekststreng fra det første tegn i den anden tekststreng."
                ],
                FLOOR: [
                    "GULV(tal, betydning)",
                    "Afrunder antallet nedad, mod nul, til nærmeste multiplum af betydning."
                ],
                HLOOKUP: [
                    "HOPSLAG(opslagsværdi; tabelmatrix; rækkeindekstal; [områdeopslag])",
                    "Søger efter en værdi i den øverste række af en tabel eller en matrix af værdier og returnerer derefter en værdi i den samme kolonne fra en række, du angiver i tabellen eller matrixen."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Returnerer timeværdien for en tidsværdi.Timen er angivet som et heltal, der spænder fra 0 (12:00 A.M.) til 23 (11:00 P.M.)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [venligt_navn])",
                    "opretter en genvej, der hopper til en anden placering på internettet, når du klikker på en celle"
                ],
                IF: [
                    "HVIS(logisk_test, værdi_hvis_sand, [værdi_hvis_falsk])",
                    "returnerer én værdi, hvis en betingelse er sand, og en anden værdi, hvis den er falsk."
                ],
                INDEX: [
                    "INDEX(matrix; række_nummer; [kolonne_nummer])",
                    "Returnerer værdien af et element i en tabel eller et array, valgt af række- og kolonnenummerindeksene."
                ],
                INDIRECT: [
                    "INDIREKTE(ref_tekst, [a1])",
                    "Returnerer referencen angivet af en tekststreng.Referencer evalueres straks for at vise deres indhold."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Returnerer TRUE, hvis værdien er tom"
                ],
                LARGE: [
                    "LARGE(matrix, k)",
                    "Returnerer den k. største værdi i et datasæt."
                ],
                LEFT: [
                    "VENSTRE(tekst, [antal_tegn])",
                    "returnerer det eller de første tegn i en tekststreng, baseret på det antal tegn, du angiver."
                ],
                LEN: [
                    "LEN(text)",
                    "returnerer antallet af tegn i en tekststreng."
                ],
                LOOKUP: [
                    "OPSLAG(opslagsværdi; opslagsvektor; [resultatvektor])",
                    "Søger i et område med én række eller én kolonne (kendt som en vektor) efter en værdi og returnerer en værdi fra den samme position i et andet område med én række eller én kolonne."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Konverterer alle store bogstaver i en tekststreng til små bogstaver."
                ],
                MATCH: [
                    "MATCH(opslagsværdi; opslagsmatrix; [matchtype])",
                    "Søger efter et specificeret element i et celleområde og returnerer derefter den relative position for det pågældende element i området.match_type kan være: 0 for eksakt match med mulighed for at bruge jokertegn;1 (standard) for mindre end, Værdierne i lookup_array-argumentet skal placeres i stigende rækkefølge;-1 for større end skal værdier i lookup_array-argumentet placeres i faldende rækkefølge."
                ],
                MAX: [
                    "MAKS(tal1; [tal2], ...)",
                    "Returnerer den største værdi i et sæt værdier."
                ],
                MEDIAN: [
                    "MEDIAN(tal1; [tal2], ...)",
                    "Returnerer medianen af de givne tal.Medianen er tallet i midten af ​​et sæt tal."
                ],
                MID: [
                    "MID(tekst, startnummer, antal_tegn)",
                    "returnerer et bestemt antal tegn fra en tekststreng, startende ved den position, du angiver, baseret på antallet af tegn, du angiver."
                ],
                MIN: [
                    "MIN(tal1; [tal2], ...)",
                    "Returnerer det mindste tal i et sæt værdier."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Returnerer den hyppigst forekommende eller gentagne værdi i en matrix eller et dataområde."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Returnerer måneden for en dato repræsenteret af et serienummer.Måneden er angivet som et heltal, der spænder fra 1 (januar) til 12 (december)."
                ],
                OR: [
                    "ELLER(logisk1; [logisk2], ...)",
                    "returnerer TRUE, hvis nogen af dens argumenter evalueres til TRUE, og returnerer FALSE, hvis alle dens argumenter evalueres til FALSE."
                ],
                PI: [
                    "PI()",
                    "Returnerer tallet 3,14159265358979, den matematiske konstant pi."
                ],
                POWER: [
                    "POWER(tal, effekt)",
                    "Returnerer resultatet af et tal hævet til en potens."
                ],
                PRODUCT: [
                    "PRODUKT(nummer1, [nummer2], ...)",
                    "multiplicerer alle de tal givet som argumenter og returnerer produktet.PRODUKT(A1:A3, C1:C3) svarer til =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Sætter det første bogstav med stort i hvert ord i en tekstværdi."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Konverterer grader til radianer."
                ],
                RAND: [
                    "RAND()",
                    "Returnerer et ligeligt fordelt tilfældigt reelt tal større end eller lig med 0 og mindre end 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Returnerer rangeringen af et tal på en liste over tal."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(gammel_tekst; startnummer; antal_tegn; ny_tekst)",
                    "erstatter en del af en tekststreng, baseret på det antal tegn, du angiver, med en anden tekststreng."
                ],
                REPT: [
                    "REPT(tekst; antal_gange)",
                    "Gentager teksten et givet antal gange."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "returnerer det eller de sidste tegn i en tekststreng, baseret på det antal tegn, du angiver."
                ],
                ROUND: [
                    "RUND(tal; antal_cifre)",
                    "runder et tal til et bestemt antal cifre."
                ],
                ROUNDDOWN: [
                    "RUNDDOWN(tal; antal_cifre)",
                    "Runder et tal nedad mod nul."
                ],
                ROUNDUP: [
                    "ROUNDUP(tal; antal_cifre)",
                    "Afrunder et tal opad, væk fra 0 (nul)."
                ],
                ROW: [
                    "ROW()",
                    "Returnerer referencen for den celle, hvori ROW-funktionen vises."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Returnerer antallet af rækker i en reference eller matrix."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "find en tekststreng i en anden tekststreng, og returner nummeret på startpositionen for den første tekststreng fra det første tegn i den anden tekststreng."
                ],
                SIN: [
                    "SIN(number)",
                    "Returnerer sinus for den givne vinkel (i radianer)."
                ],
                SMALL: [
                    "SMALL(matrix, k)",
                    "Returnerer den k-te mindste værdi i et datasæt."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Returnerer en positiv kvadratrod."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Estimerer standardafvigelsen baseret på en stikprøve.Standardafvigelsen er et mål for, hvor vidt værdierne er spredt fra gennemsnitsværdien (middelværdien)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Beregner standardafvigelsen baseret på hele populationen givet som argumenter."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(tekst; gammel_tekst; ny_tekst; [forekomst_nummer])",
                    "Erstatter ny_tekst med gammel_tekst i en tekststreng."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Tilføjer sine argumenter.Du kan tilføje individuelle værdier, cellereferencer eller intervaller eller en blanding af alle tre."
                ],
                SUMIF: [
                    "SUMHVIS(interval; kriterier; [sum_interval])",
                    "tilføjer værdierne i et område, der opfylder de kriterier, du angiver"
                ],
                SUMIFS: [
                    "SUMMER(sum_område; kriterieområde1; kriterie1; [kriterieområde2; kriterie2], ...)",
                    "tilføjer alle sine argumenter, der opfylder flere kriterier."
                ],
                SUMPRODUCT: [
                    "SUMPRODUKT(matrix1, [matrix2], [matrix3], ...)",
                    "Multiplicerer tilsvarende komponenter i de givne arrays og returnerer summen af disse produkter."
                ],
                TAN: [
                    "TAN(number)",
                    "Returnerer tangenten af ​​den givne vinkel (i radianer)."
                ],
                TEXT: [
                    "TEXT(Værdi, du vil formatere, \"Formatér kode, du vil anvende\")",
                    "TEKST-funktionen lader dig ændre den måde, et tal vises på, ved at anvende formatering på det med formatkoder."
                ],
                TIME: [
                    "TID (time, minut, sekund)",
                    "Returnerer decimaltallet for et bestemt tidspunkt."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Returnerer decimaltallet for tiden repræsenteret af en tekststreng.Decimaltallet er en værdi, der går fra 0 (nul) til 0,99988426, der repræsenterer tiderne fra 0:00:00 (12:00:00 AM) til 23:59:59 (11:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Returnerer serienummeret på den aktuelle dato."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Fjerner alle mellemrum fra tekst undtagen enkelte mellemrum mellem ord."
                ],
                TRUNC: [
                    "TRUNC(tal; [antal_cifre])",
                    "Afkorter et tal til et heltal ved at fjerne brøkdelen af tallet."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Konverterer tekst til store bogstaver."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Konverterer en tekststreng, der repræsenterer et tal, til et tal."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Estimerer varians baseret på en stikprøve."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Beregner varians baseret på hele populationen."
                ],
                VLOOKUP: [
                    "VLOOKUP (opslagsværdi, tabelmatrix, col_index_num, [områdeopslag])",
                    "slå en værdi op i en tabel eller et område for række.Slå f.eks. prisen på en bildele op efter varenummeret."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Returnerer året svarende til en dato.Året returneres som et heltal i intervallet 1900-9999."
                ]
            },
            strFreezeFirstCol: "Frys første kolonne",
            strFreezePanes: "Frys ruder",
            strFreezePanesUndo: "Frigør ruder",
            strFreezeTopRow: "Frys første række",
            strFrozenCols: "Frosne kolonner",
            strFrozenRows: "Frosne rækker",
            strFullScreen: "Fuld skærm",
            strGroup_fixCols: "Ret kolonner",
            strGroup_grandSummary: "Stort resumé",
            strGroup_header: "Drop gruppe for kolonne",
            strGroup_merge: "Flet celler",
            strHideCols: "Skjul kolonner",
            strHideRows: "Skjul rækker",
            strImport: "Importer data",
            strImportPic: "Indsæt flydende billede",
            strImportPicCell: "Indsæt billede i celle",
            strInsertColumn: "Indsæt kolonne",
            strInsertRow: "Indsæt række",
            strInsertRows: "Indsæt {0} rækker",
            strItalic: "Kursiv tekst",
            strLabel: "Etiket",
            strLink: "Link",
            strLoading: "Indlæser",
            strLocal: "da",
            strLockCells: "Lås celler",
            strMenu: {
                export: "Eksporterbare kolonner",
                filter: "Filter",
                hideCols: "Synlige kolonner"
            },
            strMerge: "Flet celler",
            strName: "Navn",
            strNextResult: "Næste resultat",
            strNoRows: "Ingen rækker at vise.",
            strNothingFound: "Intet fundet",
            strOR: "ELLER",
            strOk: "Okay",
            strOpen: "Åbn",
            strPaste: "Indsæt",
            strPrevResult: "Tidligere resultat",
            strRedo: "Gentag",
            strRename: "Omdøb",
            strSearch: "Søg",
            strSelectAll: "Vælg alle",
            strSelectedmatches: "Valgte {0} af {1} matcher",
            strShowCols: "Vis kolonner",
            strShowRows: "Vis rækker",
            strTP_aggPH: "Slip kolonner for aggregater",
            strTP_aggPane: "Aggregater",
            strTP_colPH: "Slip kolonner til kolonnegruppering",
            strTP_colPane: "Grupper kolonner",
            strTP_pivot: "Pivot-tilstand",
            strTP_rowPH: "Slip kolonner til rækkegruppering",
            strTP_rowPane: "Gruppe rækker",
            strTabAdd: "Nyt ark",
            strTabClose: "Fjern arket",
            strTabHide: "Skjul ark",
            strTabName: "sheet{0}",
            strTabRemove: "{0} ville blive slettet permanent.\r\nEr du sikker?",
            strTabRename: "Omdøb arket",
            strTabShow: "Vis ark",
            strTextColor: "Tekst farve",
            strUnderline: "Understreg tekst",
            strUndo: "Fortryd",
            strUnhide: "Vis frem",
            strUnmerge: "Fjern fletningen af celler",
            strUpdate: "Opdatering",
            strWrap: "Ombryd tekst"
        },
        pager = pq.pqPager.regional.da = {
            strDisplay: "Viser {0} til {1} ​​af {2} elementer.",
            strFirstPage: "Første side",
            strLastPage: "Sidste side",
            strNextPage: "Næste side",
            strPage: "Side {0} af {1}",
            strPrevPage: "Forrige side",
            strRefresh: "Opdater",
            strRpp: "Registreringer pr. side: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();