(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.de = {
            strAND: "UND",
            strAddColLeft: "Spalte links hinzufügen",
            strAddColRight: "Spalte rechts hinzufügen",
            strAddColumn: "Spalte hinzufügen",
            strAddRow: "Zeile hinzufügen",
            strAddRowAbove: "Zeile oben hinzufügen",
            strAddRowBelow: "Zeile unten hinzufügen",
            strAddRows: "Fügen Sie {0} Zeilen hinzu",
            strAlign: {
                bottom: "Unten ausrichten",
                center: "Mittig ausrichten",
                left: "Linksbündig",
                right: "Rechts ausrichten",
                top: "Oben ausrichten"
            },
            strAlignH: "Horizontale Ausrichtung",
            strAlignV: "Vertikale Ausrichtung",
            strApply: "Bewerben",
            strBlanks: "(Leerzeichen)",
            strBold: "Fettgedruckter Text",
            strBorder: "Grenze",
            strBorderColor: "Randfarbe",
            strBorderStyle: "Grenzstil",
            strBorders: {
                all: "Alle Grenzen",
                bottom: "Unterer Rand",
                horizontal: "Horizontaler Rand",
                inner: "Innere Grenze",
                left: "Linker Rand",
                none: "Keine Grenze",
                outer: "Äußere Grenze",
                right: "Rechte Grenze",
                top: "Oberer Rand",
                vertical: "Vertikaler Rand"
            },
            strCancel: "Abbrechen",
            strClear: "Klar",
            strClearColor: "Klare Farbe",
            strClearFilter: "Filter löschen",
            strClearText: "Klarer Text",
            strCollapse: "Bedienfeld ausblenden",
            strComment: "Kommentar",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Keine --",
                begin: "Beginnt mit",
                between: "Dazwischen",
                contain: "Enthält",
                empty: "Leer",
                end: "Endet mit",
                equal: "Gleich",
                great: "Größer als",
                gte: "Größer oder gleich",
                less: "Weniger als",
                lte: "Kleiner oder gleich",
                notbegin: "Nicht beginnt mit",
                notcontain: "Nicht enthalten",
                notempty: "Nicht leer",
                notend: "Nicht endet mit",
                notequal: "Nicht gleich",
                range: "[Bereich]",
                regexp: "Regulärer Ausdruck"
            },
            strCopy: "Kopieren",
            strCut: "Schneiden",
            strDelete: "Löschen",
            strDeleteColumn: "Spalte löschen",
            strDeleteRow: "Zeile löschen",
            strDownloadXlsx: "Laden Sie die Excel-Datei herunter",
            strEdit: "Bearbeiten",
            strExitFullScreen: "Beenden Sie den Vollbildmodus",
            strExpand: "Bereich erweitern",
            strExport: "Daten exportieren",
            strFillColor: "Füllfarbe",
            strFontFamily: "Schriftfamilie",
            strFontSize: "Schriftgröße",
            strFormat: "Zellen formatieren",
            strFormatMenu: {
                Accounting: "Buchhaltung",
                Currency: "Währung",
                Custom: "Benutzerdefiniert",
                DateTime: "Datum Uhrzeit",
                Fixed: "Dezimal",
                Fraction: "Bruchteil",
                FullDate: "Vollständiges Datum",
                General: "Allgemein",
                Int: "Ganzzahl",
                LongDate: "Langes Date",
                MediumDate: "Mittleres Datum",
                Note: "Hinweis: Formate mit * ändern sich je nach Benutzergebietsschema.",
                Percent: "Prozentsatz",
                Scientific: "Wissenschaftlich",
                ShortDate: "Kurzes Date",
                Standard: "Standard",
                Text: "Text",
                Time: "Zeit"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Gibt den absoluten Wert einer Zahl zurück.Der Absolutwert einer Zahl ist die Zahl ohne Vorzeichen."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Gibt den Arkuskosinus oder Umkehrkosinus einer Zahl zurück.Der Arkuskosinus ist der Winkel, dessen Kosinus die Zahl ist.Der zurückgegebene Winkel wird im Bogenmaß im Bereich von 0 (Null) bis Pi angegeben."
                ],
                AND: [
                    "AND(logisch1, [logisch2], ...)",
                    "Gibt TRUE zurück, wenn alle seine Argumente als TRUE ausgewertet werden, und gibt FALSE zurück, wenn ein oder mehrere Argumente als FALSE ausgewertet werden."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Gibt den Arkussinus oder Umkehrsinus einer Zahl zurück.Der Arkussinus ist der Winkel, dessen Sinus die Zahl ist.Der zurückgegebene Winkel wird im Bogenmaß im Bereich von -pi/2 bis pi/2 angegeben."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Gibt den Arkustangens oder Umkehrtangens einer Zahl zurück.Der Arkustangens ist der Winkel, dessen Tangens die Zahl ist.Der zurückgegebene Winkel wird im Bogenmaß im Bereich von -pi/2 bis pi/2 angegeben."
                ],
                AVERAGE: [
                    "DURCHSCHNITT(Zahl1, [Zahl2], ...)",
                    "Gibt den Durchschnitt (arithmetisches Mittel) der Argumente zurück.Wenn beispielsweise der Bereich A1:A20 Zahlen enthält, gibt die Formel =AVERAGE(A1:A20) den Durchschnitt dieser Zahlen zurück."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(Bereich, Kriterien, [average_range])",
                    "Gibt den Durchschnitt (arithmetisches Mittel) aller Zellen in einem Bereich zurück, die ein bestimmtes Kriterium erfüllen."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(average_range, Kriterien_Bereich1, Kriterien1, [Kriterien_Bereich2, Kriterien2], ...)",
                    "Gibt den Durchschnitt (arithmetisches Mittel) aller Zellen zurück, die mehrere Kriterien erfüllen."
                ],
                CEILING: [
                    "DECKE(Anzahl, Bedeutung)",
                    "Gibt die von Null weg auf das nächste Vielfache der Signifikanz aufgerundete Zahl zurück.Wenn Sie beispielsweise die Verwendung von Pennys in Ihren Preisen vermeiden möchten und Ihr Produkt einen Preis von 4,42 $ hat, verwenden Sie die Formel =CEILING(4.42,0.05), um die Preise auf den nächsten Nickel aufzurunden."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Gibt das durch eine Zahl angegebene Zeichen zurück."
                ],
                CHOOSE: [
                    "CHOOSE(index_num, value1, [value2], ...)",
                    "Wenn index_num 1 ist, gibt CHOOSE den Wert1 zurück;wenn es 2 ist, gibt CHOOSE den Wert2 zurück;und so weiter."
                ],
                CODE: [
                    "CODE(text)",
                    "Gibt einen numerischen Code für das erste Zeichen in einer Textzeichenfolge zurück."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Gibt den Verweis auf die Zelle zurück, in der die COLUMN-Funktion erscheint."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Gibt die Anzahl der Spalten in einem Array oder einer Referenz zurück."
                ],
                CONCATENATE: [
                    "CONCATENATE(text1, [text2], ...)",
                    "Verbinden Sie zwei oder mehr Textzeichenfolgen zu einer Zeichenfolge."
                ],
                COS: [
                    "COS(number)",
                    "Gibt den Kosinus des angegebenen Winkels (im Bogenmaß) zurück."
                ],
                COUNT: [
                    "COUNT(Wert1, [Wert2], ...)",
                    "Zählt die Anzahl der Zellen, die Zahlen enthalten, und zählt die Zahlen in der Liste der Argumente."
                ],
                COUNTA: [
                    "COUNTA(Wert1, [Wert2], ...)",
                    "Die Funktion COUNTA zählt die Anzahl der nicht leeren Zellen in einem Bereich."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Zählt leere Zellen in einem angegebenen Zellbereich."
                ],
                COUNTIF: [
                    "COUNTIF(Bereich, Kriterien)",
                    "zählt die Anzahl der Zellen, die ein Kriterium erfüllen;um beispielsweise zu zählen, wie oft eine bestimmte Stadt in einer Kundenliste erscheint."
                ],
                COUNTIFS: [
                    "COUNTIFS(Kriterien_Bereich1, Kriterien1, [Kriterien_Bereich2, Kriterien2]…)",
                    "Wendet Kriterien auf Zellen in mehreren Bereichen an und zählt, wie oft alle Kriterien erfüllt sind."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "gibt die fortlaufende Seriennummer zurück, die ein bestimmtes Datum darstellt."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Berechnet die Anzahl der Tage, Monate oder Jahre zwischen zwei Daten.Die Einheit kann „Y“, „M“ oder „D“ sein."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "wandelt ein als Text gespeichertes Datum in eine Seriennummer um, die Excel als Datum erkennt.Beispielsweise gibt die Formel =DATEVALUE(\"1/1/2008\") 39448 zurück."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Gibt den Tag eines Datums zurück, dargestellt durch eine Seriennummer."
                ],
                DAYS: [
                    "TAGE(end_date, start_date)",
                    "Gibt die Anzahl der Tage zwischen zwei Daten zurück."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Konvertiert Bogenmaß in Grad."
                ],
                EOMONTH: [
                    "EOMONTH(start_date, Monate)",
                    "Gibt die Seriennummer für den letzten Tag des Monats zurück, der der angegebenen Anzahl von Monaten vor oder nach start_date entspricht."
                ],
                EXP: [
                    "EXP(number)",
                    "Gibt e hoch mit der Zahl zurück."
                ],
                FIND: [
                    "FIND(find_text, Within_text, [start_num])",
                    "Sucht eine Textzeichenfolge innerhalb einer zweiten Textzeichenfolge und gibt die Nummer der Startposition der ersten Textzeichenfolge ab dem ersten Zeichen der zweiten Textzeichenfolge zurück."
                ],
                FLOOR: [
                    "FLOOR(Anzahl, Bedeutung)",
                    "Rundet die Zahl in Richtung Null auf das nächste Vielfache der Signifikanz ab."
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "Sucht nach einem Wert in der obersten Zeile einer Tabelle oder eines Wertearrays und gibt dann einen Wert in derselben Spalte aus einer Zeile zurück, die Sie in der Tabelle oder dem Array angeben."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Gibt die Stunde eines Zeitwerts zurück.Die Stunde wird als Ganzzahl angegeben und reicht von 0 (12:00 Uhr) bis 23 (23:00 Uhr)."
                ],
                HYPERLINK: [
                    "HYPERLINK(URL, [freundlicher_Name])",
                    "erstellt eine Verknüpfung, die zu einer anderen Stelle im Internet springt, wenn Sie auf eine Zelle klicken"
                ],
                IF: [
                    "IF(logical_test, value_if_true, [value_if_false])",
                    "gibt einen Wert zurück, wenn eine Bedingung wahr ist, und einen anderen Wert, wenn sie falsch ist."
                ],
                INDEX: [
                    "INDEX(array, row_num, [column_num])",
                    "Gibt den Wert eines Elements in einer Tabelle oder einem Array zurück, ausgewählt durch die Zeilen- und Spaltennummernindizes."
                ],
                INDIRECT: [
                    "INDIREKT(ref_text, [a1])",
                    "Gibt die durch eine Textzeichenfolge angegebene Referenz zurück.Referenzen werden sofort ausgewertet, um deren Inhalt anzuzeigen."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Gibt TRUE zurück, wenn der Wert leer ist"
                ],
                LARGE: [
                    "LARGE(array, k)",
                    "Gibt den k-größten Wert in einem Datensatz zurück."
                ],
                LEFT: [
                    "LEFT(text, [num_chars])",
                    "Gibt das erste Zeichen oder die ersten Zeichen in einer Textzeichenfolge zurück, basierend auf der von Ihnen angegebenen Anzahl von Zeichen."
                ],
                LEN: [
                    "LEN(text)",
                    "gibt die Anzahl der Zeichen in einer Textzeichenfolge zurück."
                ],
                LOOKUP: [
                    "LOOKUP(lookup_value, lookup_vector, [result_vector])",
                    "Sucht in einem einzeiligen oder einspaltigen Bereich (sogenannter Vektor) nach einem Wert und gibt einen Wert von derselben Position in einem zweiten einzeiligen oder einspaltigen Bereich zurück."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Wandelt alle Großbuchstaben in einer Textzeichenfolge in Kleinbuchstaben um."
                ],
                MATCH: [
                    "MATCH(lookup_value, lookup_array, [match_type])",
                    "Sucht nach einem angegebenen Element in einem Zellbereich und gibt dann die relative Position dieses Elements im Bereich zurück.match_type kann sein: 0 für exakte Übereinstimmung mit Option zur Verwendung von Platzhaltern;1 (Standard) für kleiner als. Die Werte im Argument „lookup_array“ müssen in aufsteigender Reihenfolge platziert werden.-1 für größer als, Werte im Argument „lookup_array“ müssen in absteigender Reihenfolge platziert werden."
                ],
                MAX: [
                    "MAX(Nummer1, [Nummer2], ...)",
                    "Gibt den größten Wert in einer Reihe von Werten zurück."
                ],
                MEDIAN: [
                    "MEDIAN(Zahl1, [Zahl2], ...)",
                    "Gibt den Median der angegebenen Zahlen zurück.Der Median ist die Zahl in der Mitte einer Zahlenmenge."
                ],
                MID: [
                    "MID(text, start_num, num_chars)",
                    "Gibt eine bestimmte Anzahl von Zeichen aus einer Textzeichenfolge zurück, beginnend an der von Ihnen angegebenen Position, basierend auf der von Ihnen angegebenen Anzahl von Zeichen."
                ],
                MIN: [
                    "MIN(Nummer1, [Nummer2], ...)",
                    "Gibt die kleinste Zahl in einer Reihe von Werten zurück."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Gibt den am häufigsten vorkommenden oder sich wiederholenden Wert in einem Array oder Datenbereich zurück."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Gibt den Monat eines Datums zurück, das durch eine Seriennummer dargestellt wird.Der Monat wird als Ganzzahl im Bereich von 1 (Januar) bis 12 (Dezember) angegeben."
                ],
                OR: [
                    "ODER(logisch1, [logisch2], ...)",
                    "gibt TRUE zurück, wenn eines seiner Argumente als TRUE ausgewertet wird, und gibt FALSE zurück, wenn alle seine Argumente als FALSE ausgewertet werden."
                ],
                PI: [
                    "PI()",
                    "Gibt die Zahl 3,14159265358979 zurück, die mathematische Konstante Pi."
                ],
                POWER: [
                    "POWER(Anzahl, Leistung)",
                    "Gibt das Ergebnis einer potenzierten Zahl zurück."
                ],
                PRODUCT: [
                    "PRODUKT(Nummer1, [Nummer2], ...)",
                    "multipliziert alle als Argumente angegebenen Zahlen und gibt das Produkt zurück.PRODUCT(A1:A3, C1:C3) entspricht =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Schreibt den ersten Buchstaben in jedem Wort eines Textwerts groß."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Wandelt Grad in Bogenmaß um."
                ],
                RAND: [
                    "RAND()",
                    "Gibt eine gleichmäßig verteilte zufällige reelle Zahl zurück, die größer oder gleich 0 und kleiner als 1 ist."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Gibt den Rang einer Zahl in einer Zahlenliste zurück."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(old_text, start_num, num_chars, new_text)",
                    "Ersetzt einen Teil einer Textzeichenfolge basierend auf der von Ihnen angegebenen Anzahl von Zeichen durch eine andere Textzeichenfolge."
                ],
                REPT: [
                    "REPT(text, number_times)",
                    "Wiederholt den Text eine bestimmte Anzahl von Malen."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "Gibt das letzte Zeichen oder die letzten Zeichen in einer Textzeichenfolge zurück, basierend auf der von Ihnen angegebenen Anzahl von Zeichen."
                ],
                ROUND: [
                    "ROUND(Zahl, num_digits)",
                    "Rundet eine Zahl auf eine angegebene Anzahl von Stellen."
                ],
                ROUNDDOWN: [
                    "ABRUNDEN(Zahl, num_digits)",
                    "Rundet eine Zahl ab, Richtung Null."
                ],
                ROUNDUP: [
                    "AUFRUNDEN(Zahl, num_digits)",
                    "Rundet eine Zahl von 0 (Null) weg auf."
                ],
                ROW: [
                    "ROW()",
                    "Gibt den Verweis auf die Zelle zurück, in der die ROW-Funktion erscheint."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Gibt die Anzahl der Zeilen in einer Referenz oder einem Array zurück."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "Suchen Sie eine Textzeichenfolge innerhalb einer zweiten Textzeichenfolge und geben Sie die Nummer der Startposition der ersten Textzeichenfolge ab dem ersten Zeichen der zweiten Textzeichenfolge zurück."
                ],
                SIN: [
                    "SIN(number)",
                    "Gibt den Sinus des angegebenen Winkels (im Bogenmaß) zurück."
                ],
                SMALL: [
                    "KLEIN(Array, k)",
                    "Gibt den k-kleinsten Wert in einem Datensatz zurück."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Gibt eine positive Quadratwurzel zurück."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Schätzt die Standardabweichung basierend auf einer Stichprobe.Die Standardabweichung ist ein Maß dafür, wie weit Werte vom Durchschnittswert (dem Mittelwert) abweichen."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Berechnet die Standardabweichung basierend auf der gesamten als Argumente angegebenen Grundgesamtheit."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(text, old_text, new_text, [instance_num])",
                    "Ersetzt new_text für old_text in einer Textzeichenfolge."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Fügt seine Argumente hinzu.Sie können einzelne Werte, Zellbezüge oder Bereiche oder eine Mischung aus allen dreien hinzufügen."
                ],
                SUMIF: [
                    "SUMIF(Bereich, Kriterien, [sum_range])",
                    "Fügt die Werte in einem Bereich hinzu, die den von Ihnen angegebenen Kriterien entsprechen"
                ],
                SUMIFS: [
                    "SUMIFS(sum_range, Kriterien_Bereich1, Kriterien1, [Kriterien_Bereich2, Kriterien2], ...)",
                    "fügt alle seine Argumente hinzu, die mehrere Kriterien erfüllen."
                ],
                SUMPRODUCT: [
                    "SUMMENPRODUKT(array1, [array2], [array3], ...)",
                    "Multipliziert entsprechende Komponenten in den angegebenen Arrays und gibt die Summe dieser Produkte zurück."
                ],
                TAN: [
                    "TAN(number)",
                    "Gibt den Tangens des angegebenen Winkels (im Bogenmaß) zurück."
                ],
                TEXT: [
                    "TEXT(Wert, den Sie formatieren möchten, „Formatcode, den Sie anwenden möchten“)",
                    "Mit der TEXT-Funktion können Sie die Darstellung einer Zahl ändern, indem Sie sie mit Formatcodes formatieren."
                ],
                TIME: [
                    "ZEIT (Stunde, Minute, Sekunde)",
                    "Gibt die Dezimalzahl für einen bestimmten Zeitpunkt zurück."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Gibt die Dezimalzahl der durch eine Textzeichenfolge dargestellten Zeit zurück.Die Dezimalzahl ist ein Wert im Bereich von 0 (Null) bis 0,99988426 und stellt die Zeiten von 0:00:00 (12:00:00 Uhr) bis 23:59:59 (23:59:59 Uhr) dar."
                ],
                TODAY: [
                    "TODAY()",
                    "Gibt die Seriennummer des aktuellen Datums zurück."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Entfernt alle Leerzeichen aus dem Text, mit Ausnahme einzelner Leerzeichen zwischen Wörtern."
                ],
                TRUNC: [
                    "TRUNC(Zahl, [num_digits])",
                    "Schneidet eine Zahl auf eine Ganzzahl ab, indem der Bruchteil der Zahl entfernt wird."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Wandelt Text in Großbuchstaben um."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Konvertiert eine Textzeichenfolge, die eine Zahl darstellt, in eine Zahl."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Schätzt die Varianz basierend auf einer Stichprobe."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Berechnet die Varianz basierend auf der gesamten Grundgesamtheit."
                ],
                VLOOKUP: [
                    "VLOOKUP (lookup_value, table_array, col_index_num, [range_lookup])",
                    "Suchen Sie einen Wert in einer Tabelle oder einem Bereich zeilenweise.Suchen Sie beispielsweise anhand der Teilenummer nach dem Preis eines Autoteils."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Gibt das Jahr zurück, das einem Datum entspricht.Das Jahr wird als Ganzzahl im Bereich 1900-9999 zurückgegeben."
                ]
            },
            strFreezeFirstCol: "Erste Spalte einfrieren",
            strFreezePanes: "Scheiben einfrieren",
            strFreezePanesUndo: "Scheiben auftauen",
            strFreezeTopRow: "Erste Reihe einfrieren",
            strFrozenCols: "Gefrorene Säulen",
            strFrozenRows: "Gefrorene Zeilen",
            strFullScreen: "Vollbild",
            strGroup_fixCols: "Spalten reparieren",
            strGroup_grandSummary: "Große Zusammenfassung",
            strGroup_header: "Gruppe nach Spalte löschen",
            strGroup_merge: "Zellen zusammenführen",
            strHideCols: "Spalten ausblenden",
            strHideRows: "Zeilen ausblenden",
            strImport: "Daten importieren",
            strImportPic: "Schwebendes Bild einfügen",
            strImportPicCell: "Bild in Zelle einfügen",
            strInsertColumn: "Spalte einfügen",
            strInsertRow: "Zeile einfügen",
            strInsertRows: "Fügen Sie {0} Zeilen ein",
            strItalic: "Kursiver Text",
            strLabel: "Etikett",
            strLink: "Link",
            strLoading: "Laden",
            strLocal: "de",
            strLockCells: "Zellen sperren",
            strMenu: {
                export: "Exportierbare Spalten",
                filter: "Filtern",
                hideCols: "Sichtbare Spalten"
            },
            strMerge: "Zellen zusammenführen",
            strName: "Name",
            strNextResult: "Weiter Ergebnis",
            strNoRows: "Keine anzuzeigenden Zeilen.",
            strNothingFound: "Kein Ergebnis gefunden",
            strOR: "ODER",
            strOk: "Okay",
            strOpen: "Offen",
            strPaste: "Einfügen",
            strPrevResult: "Vorheriges Ergebnis",
            strRedo: "Wiederholen",
            strRename: "Umbenennen",
            strSearch: "Suchen",
            strSelectAll: "Wählen Sie „Alle“ aus",
            strSelectedmatches: "Ergebnis {0} von {1}",
            strShowCols: "Spalten anzeigen",
            strShowRows: "Zeilen anzeigen",
            strTP_aggPH: "Spalten für Aggregate löschen",
            strTP_aggPane: "Aggregate",
            strTP_colPH: "Löschen Sie Spalten zur Spaltengruppierung",
            strTP_colPane: "Gruppenspalten",
            strTP_pivot: "Pivot-Modus",
            strTP_rowPH: "Löschen Sie Spalten zur Zeilengruppierung",
            strTP_rowPane: "Gruppieren Sie Zeilen",
            strTabAdd: "Neues Blatt",
            strTabClose: "Blatt entfernen",
            strTabHide: "Blatt ausblenden",
            strTabName: "sheet{0}",
            strTabRemove: "{0} würde dauerhaft gelöscht.\r\nBist du sicher?",
            strTabRename: "Blatt umbenennen",
            strTabShow: "Blatt anzeigen",
            strTextColor: "Textfarbe",
            strUnderline: "Text unterstreichen",
            strUndo: "Rückgängig machen",
            strUnhide: "Einblenden",
            strUnmerge: "Zellen trennen",
            strUpdate: "Aktualisieren",
            strWrap: "Text umbrechen"
        },
        pager = pq.pqPager.regional.de = {
            strDisplay: "Ergebnis {0} bis {1} von {2}.",
            strFirstPage: "Erste Seite",
            strLastPage: "Letzte Seite",
            strNextPage: "Nächste Seite",
            strPage: "Seite {0} von {1}",
            strPrevPage: "Vorherige Seite",
            strRefresh: "Neu laden",
            strRpp: "Ergebnisse pro Seite: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();