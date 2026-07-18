(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.pl = {
            strAND: "ORAZ",
            strAddColLeft: "Dodaj kolumnę po lewej stronie",
            strAddColRight: "Dodaj kolumnę po prawej stronie",
            strAddColumn: "Dodaj kolumnę",
            strAddRow: "Dodaj wiersz",
            strAddRowAbove: "Dodaj wiersz powyżej",
            strAddRowBelow: "Dodaj wiersz poniżej",
            strAddRows: "Dodaj {0} wierszy",
            strAlign: {
                bottom: "Wyrównaj do dołu",
                center: "Wyśrodkuj",
                left: "Wyrównaj do lewej",
                right: "Wyrównaj do prawej",
                top: "Wyrównaj do góry"
            },
            strAlignH: "Wyrównanie poziome",
            strAlignV: "Wyrównanie pionowe",
            strApply: "Zastosuj",
            strBlanks: "(Puste miejsca)",
            strBold: "Pogrubiony tekst",
            strBorder: "Granica",
            strBorderColor: "Kolor obramowania",
            strBorderStyle: "Styl graniczny",
            strBorders: {
                all: "Wszystkie granice",
                bottom: "Dolna granica",
                horizontal: "Granica pozioma",
                inner: "Wewnętrzna granica",
                left: "Lewa granica",
                none: "Brak granicy",
                outer: "Granica zewnętrzna",
                right: "Prawa granica",
                top: "Górna granica",
                vertical: "Granica pionowa"
            },
            strCancel: "Anuluj",
            strClear: "Jasne",
            strClearColor: "Jasny kolor",
            strClearFilter: "Wyczyść filtr",
            strClearText: "Wyczyść tekst",
            strCollapse: "Zwiń panel",
            strComment: "Komentarz",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Brak --",
                begin: "Zaczyna się od",
                between: "pomiędzy",
                contain: "Zawiera",
                empty: "Pusty",
                end: "Kończy się na",
                equal: "Równe",
                great: "Większy niż",
                gte: "Większe lub równe",
                less: "Mniej niż",
                lte: "Mniej niż lub równo",
                notbegin: "Nie zaczyna się od",
                notcontain: "Nie zawiera",
                notempty: "Nie pusty",
                notend: "Nie kończy się na",
                notequal: "Nie równe",
                range: "[Zakres]",
                regexp: "Wyrażenie regularne"
            },
            strCopy: "Kopiuj",
            strCut: "Cięcie",
            strDelete: "Usu?",
            strDeleteColumn: "Usuń kolumnę",
            strDeleteRow: "Usuń wiersz",
            strDownloadXlsx: "Pobierz plik Excela",
            strEdit: "Edytuj",
            strExitFullScreen: "Wyjdź z pełnego ekranu",
            strExpand: "Rozwiń panel",
            strExport: "Eksportuj dane",
            strFillColor: "Kolor wypełnienia",
            strFontFamily: "Rodzina czcionek",
            strFontSize: "Rozmiar czcionki",
            strFormat: "Formatuj komórki",
            strFormatMenu: {
                Accounting: "Księgowość",
                Currency: "Waluta",
                Custom: "Niestandardowe",
                DateTime: "Data i godzina",
                Fixed: "Dziesiętny",
                Fraction: "Ułamek",
                FullDate: "Pełna data",
                General: "Generał",
                Int: "Liczba całkowita",
                LongDate: "Długa randka",
                MediumDate: "Średnia data",
                Note: "Uwaga: formaty z * zmieniają się w zależności od ustawień regionalnych użytkownika.",
                Percent: "Procent",
                Scientific: "Naukowe",
                ShortDate: "Krótka randka",
                Standard: "Standardowe",
                Text: "Tekst",
                Time: "Czas"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Zwraca wartość bezwzględną liczby.Wartość bezwzględna liczby to liczba bez jej znaku."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Zwraca arccosinus lub odwrotny cosinus liczby.Arcuscosinus to kąt, którego cosinus jest liczbą.Zwracany kąt jest podawany w radianach w zakresie od 0 (zero) do pi."
                ],
                AND: [
                    "AND(logiczne1, [logiczne2], ...)",
                    "Zwraca wartość PRAWDA, jeśli wszystkie jej argumenty mają wartość PRAWDA i zwraca FAŁSZ, jeśli jeden lub więcej argumentów ma wartość FAŁSZ."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Zwraca arcusinus lub odwrotny sinus liczby.Arcsinus to kąt, którego sinus jest liczbą.Zwracany kąt jest podawany w radianach w zakresie od -pi/2 do pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Zwraca arcustangens lub odwrotny tangens liczby.Arcus tangens to kąt, którego tangens jest liczbą.Zwracany kąt jest podawany w radianach w zakresie od -pi/2 do pi/2."
                ],
                AVERAGE: [
                    "ŚREDNIA(liczba1, [liczba2], ...)",
                    "Zwraca średnią (średnią arytmetyczną) argumentów.Na przykład, jeśli zakres A1:A20 zawiera liczby, formuła =ŚREDNIA(A1:A20) zwraca średnią z tych liczb."
                ],
                AVERAGEIF: [
                    "ŚREDNIA JEŻELI(zakres; kryteria; [średni_zakres])",
                    "Zwraca średnią (średnią arytmetyczną) wszystkich komórek w zakresie spełniających dane kryteria."
                ],
                AVERAGEIFS: [
                    "ŚREDNIAIFS(zakres_średni, zakres_kryteriów1, kryteria1, [zakres_kryteriów2, kryteria2], ...)",
                    "Zwraca średnią (średnią arytmetyczną) wszystkich komórek spełniających wiele kryteriów."
                ],
                CEILING: [
                    "SUFIT(liczba, znaczenie)",
                    "Zwraca liczbę zaokrągloną w górę, od zera, do najbliższej wielokrotności istotności.Na przykład, jeśli chcesz uniknąć wpisywania cen w groszach, a cena Twojego produktu wynosi 4,42 USD, użyj formuły =SUFIT(4,42;0,05), aby zaokrąglić ceny w górę do najbliższej pięciocentówki."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Zwraca znak określony przez liczbę."
                ],
                CHOOSE: [
                    "WYBIERZ(numer_indeksu, wartość1, [wartość2], ...)",
                    "Jeśli numer_indeksu wynosi 1, WYBIERZ zwraca wartość 1;jeśli wynosi 2, WYBIERZ zwraca wartość 2;i tak dalej."
                ],
                CODE: [
                    "CODE(text)",
                    "Zwraca kod numeryczny dla pierwszego znaku ciągu tekstowego."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Zwraca odwołanie do komórki, w której występuje funkcja KOLUMNA."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Zwraca liczbę kolumn w tablicy lub odwołaniu."
                ],
                CONCATENATE: [
                    "POŁĄCZ.(tekst1, [tekst2], ...)",
                    "połączyć dwa lub więcej ciągów tekstowych w jeden ciąg."
                ],
                COS: [
                    "COS(number)",
                    "Zwraca cosinus podanego kąta (w radianach)."
                ],
                COUNT: [
                    "LICZBA(wartość1, [wartość2], ...)",
                    "Zlicza komórki zawierające liczby i zlicza liczby na liście argumentów."
                ],
                COUNTA: [
                    "LICZBA(wartość1, [wartość2], ...)",
                    "Funkcja COUNTA zlicza liczbę komórek, które nie są puste w zakresie."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Zlicza puste komórki w określonym zakresie komórek."
                ],
                COUNTIF: [
                    "LICZ.JEŻELI(zakres, kryteria)",
                    "zlicza liczbę komórek spełniających kryterium;na przykład, aby policzyć, ile razy dane miasto pojawia się na liście klientów."
                ],
                COUNTIFS: [
                    "COUNTIFS(zakres_kryteriów1, kryteria1, [zakres_kryteriów2, kryteria2]…)",
                    "Stosuje kryteria do komórek w wielu zakresach i zlicza, ile razy wszystkie kryteria zostały spełnione."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "zwraca kolejny numer seryjny reprezentujący określoną datę."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Oblicza liczbę dni, miesięcy lub lat pomiędzy dwiema datami.Jednostką może być „Y”, „M” lub „D”."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "konwertuje datę zapisaną jako tekst na liczbę kolejną rozpoznawaną przez program Excel jako datę.Na przykład formuła =DATAWARTOŚĆ(\"1.01.2008\") zwraca 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Zwraca dzień daty reprezentowany przez liczbę kolejną."
                ],
                DAYS: [
                    "DNI(data_końcowa, data_początkowa)",
                    "Zwraca liczbę dni pomiędzy dwiema datami."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Konwertuje radiany na stopnie."
                ],
                EOMONTH: [
                    "EOMONTH(data_początkowa, miesiące)",
                    "Zwraca liczbę kolejną dla ostatniego dnia miesiąca, czyli wskazanej liczby miesięcy przed lub po dacie_początkowej."
                ],
                EXP: [
                    "EXP(number)",
                    "Zwraca e podniesione do potęgi liczby."
                ],
                FIND: [
                    "ZNAJDŹ(znajdź_tekst, w_tekście, [liczba_początkowa])",
                    "Lokalizuje jeden ciąg tekstowy w drugim ciągu tekstowym i zwraca numer pozycji początkowej pierwszego ciągu tekstowego od pierwszego znaku drugiego ciągu tekstowego."
                ],
                FLOOR: [
                    "PIĘTRO(liczba; znaczenie)",
                    "Zaokrągla liczbę w dół, w kierunku zera, do najbliższej wielokrotności istotności."
                ],
                HLOOKUP: [
                    "WYSZUKAJ.POZIOMO(wartość_wyszukiwania, tablica_tabeli, numer_indeksu_wiersza, [wyszukiwanie_zakresu])",
                    "Wyszukuje wartość w górnym wierszu tabeli lub tablicy wartości, a następnie zwraca wartość w tej samej kolumnie z wiersza określonego w tabeli lub tablicy."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Zwraca godzinę wartości czasu.Godzina jest podawana jako liczba całkowita z zakresu od 0 (12:00) do 23 (23:00)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [przyjazna_nazwa])",
                    "tworzy skrót, który po kliknięciu komórki przeskakuje do innej lokalizacji w Internecie"
                ],
                IF: [
                    "JEŻELI(test_logiczny, wartość_jeśli_prawda, [wartość_jeśli_fałsz])",
                    "zwraca jedną wartość, jeśli warunek jest prawdziwy, i inną wartość, jeśli jest fałszywy."
                ],
                INDEX: [
                    "INDEKS(tablica, numer_wiersza, [numer_kolumny])",
                    "Zwraca wartość elementu w tabeli lub tablicy, wybranej na podstawie indeksów numerów wierszy i kolumn."
                ],
                INDIRECT: [
                    "POŚREDNI(tekst_ref, [a1])",
                    "Zwraca odwołanie określone przez ciąg tekstowy.Odniesienia są natychmiast oceniane w celu wyświetlenia ich zawartości."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Zwraca wartość PRAWDA, jeśli wartość jest pusta"
                ],
                LARGE: [
                    "DUŻY(tablica, k)",
                    "Zwraca k-tą największą wartość w zestawie danych."
                ],
                LEFT: [
                    "LEWO(tekst, [liczba_znaków])",
                    "zwraca pierwszy znak lub znaki w ciągu tekstowym na podstawie określonej liczby znaków."
                ],
                LEN: [
                    "LEN(text)",
                    "zwraca liczbę znaków w ciągu tekstowym."
                ],
                LOOKUP: [
                    "WYSZUKAJ(wartość_wyszukiwania, wektor_wyszukiwania, [wektor_wyniku])",
                    "Wyszukuje wartość w zakresie jednowierszowym lub jednokolumnowym (zwanym wektorem) i zwraca wartość z tej samej pozycji w drugim zakresie jednowierszowym lub jednokolumnowym."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Konwertuje wszystkie wielkie litery w ciągu tekstowym na małe litery."
                ],
                MATCH: [
                    "DOPASUJ(wartość_wyszukiwania, tablica_wyszukiwania, [typ_dopasowania])",
                    "Wyszukuje określony element w zakresie komórek, a następnie zwraca względną pozycję tego elementu w zakresie.typ_dopasowania może wynosić: 0 dla dokładnego dopasowania z opcją użycia symboli wieloznacznych;1 (domyślnie) dla mniej niż. Wartości w argumencie lookup_array muszą być umieszczone w kolejności rosnącej;-1 dla wartości większych niż, wartości w argumencie tablica_wyszukiwań muszą być umieszczone w kolejności malejącej."
                ],
                MAX: [
                    "MAX(liczba1, [liczba2], ...)",
                    "Zwraca największą wartość ze zbioru wartości."
                ],
                MEDIAN: [
                    "MEDIANA(liczba1, [liczba2], ...)",
                    "Zwraca medianę podanych liczb.Mediana to liczba znajdująca się w środku zbioru liczb."
                ],
                MID: [
                    "MID(tekst, liczba_początkowa, liczba_znaków)",
                    "zwraca określoną liczbę znaków z ciągu tekstowego, zaczynając od określonej pozycji, w oparciu o określoną liczbę znaków."
                ],
                MIN: [
                    "MIN(liczba1, [liczba2], ...)",
                    "Zwraca najmniejszą liczbę ze zbioru wartości."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Zwraca najczęściej występującą lub powtarzającą się wartość w tablicy lub zakresie danych."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Zwraca miesiąc daty reprezentowanej przez liczbę kolejną.Miesiąc podawany jest jako liczba całkowita z zakresu od 1 (styczeń) do 12 (grudzień)."
                ],
                OR: [
                    "LUB(logiczne1, [logiczne2], ...)",
                    "zwraca PRAWDA, jeśli którykolwiek z jej argumentów ma wartość PRAWDA i zwraca FAŁSZ, jeśli wszystkie argumenty mają wartość FAŁSZ."
                ],
                PI: [
                    "PI()",
                    "Zwraca liczbę 3,14159265358979, stałą matematyczną pi."
                ],
                POWER: [
                    "MOC(liczba, moc)",
                    "Zwraca wynik liczby podniesionej do potęgi."
                ],
                PRODUCT: [
                    "PRODUKT(liczba1, [liczba2], ...)",
                    "mnoży wszystkie liczby podane jako argumenty i zwraca iloczyn.ILOCZYN(A1:A3, C1:C3) jest odpowiednikiem =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Zapisuje pierwszą literę w każdym słowie wartości tekstowej wielką literą."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Konwertuje stopnie na radiany."
                ],
                RAND: [
                    "RAND()",
                    "Zwraca równomiernie rozłożoną losową liczbę rzeczywistą większą lub równą 0 i mniejszą niż 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Zwraca rangę liczby na liście liczb."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "ZAMIEŃ(stary_tekst, numer_początkowy, liczba_znaków, nowy_tekst)",
                    "zastępuje część ciągu tekstowego, w zależności od określonej liczby znaków, innym ciągiem tekstowym."
                ],
                REPT: [
                    "POWT(tekst; liczba_razy)",
                    "Powtarza tekst określoną liczbę razy."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "zwraca ostatni znak lub znaki w ciągu tekstowym na podstawie określonej liczby znaków."
                ],
                ROUND: [
                    "OKRĄG(liczba; liczba_cyfr)",
                    "zaokrągla liczbę do określonej liczby cyfr."
                ],
                ROUNDDOWN: [
                    "ZAOKR.W DÓŁ(liczba; liczba_cyfr)",
                    "Zaokrągla liczbę w dół, w stronę zera."
                ],
                ROUNDUP: [
                    "ZAOKR.W GÓRĘ(liczba; liczba_cyfr)",
                    "Zaokrągla liczbę w górę, od 0 (zero)."
                ],
                ROW: [
                    "ROW()",
                    "Zwraca odwołanie do komórki, w której pojawia się funkcja WIERSZ."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Zwraca liczbę wierszy w odwołaniu lub tablicy."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "zlokalizuj jeden ciąg tekstowy w drugim ciągu tekstowym i zwróć numer pozycji początkowej pierwszego ciągu tekstowego od pierwszego znaku drugiego ciągu tekstowego."
                ],
                SIN: [
                    "SIN(number)",
                    "Zwraca sinus podanego kąta (w radianach)."
                ],
                SMALL: [
                    "MAŁY(tablica, k)",
                    "Zwraca k-tą najmniejszą wartość w zestawie danych."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Zwraca dodatni pierwiastek kwadratowy."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Szacuje odchylenie standardowe na podstawie próbki.Odchylenie standardowe jest miarą tego, jak bardzo wartości różnią się od wartości średniej (średniej)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Oblicza odchylenie standardowe na podstawie całej populacji podanej jako argumenty."
                ],
                SUBSTITUTE: [
                    "SUBSTYTUT(tekst, stary_tekst, nowy_tekst, [numer_instancji])",
                    "Zastępuje nowy_tekst stary_tekst w ciągu tekstowym."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Dodaje swoje argumenty.Możesz dodawać pojedyncze wartości, odwołania do komórek lub zakresy albo kombinację wszystkich trzech."
                ],
                SUMIF: [
                    "SUMA.JEŻELI(zakres; kryteria; [zakres sumy])",
                    "dodaje wartości z zakresu spełniającego określone kryteria"
                ],
                SUMIFS: [
                    "SUMIFS(zakres_sumy, zakres_kryteriów1, kryteria1, [zakres_kryteriów2, kryteria2], ...)",
                    "dodaje wszystkie swoje argumenty, które spełniają wiele kryteriów."
                ],
                SUMPRODUCT: [
                    "SUMPRODUKT(tablica1, [tablica2], [tablica3], ...)",
                    "Mnoży odpowiednie składniki w podanych tablicach i zwraca sumę tych iloczynów."
                ],
                TAN: [
                    "TAN(number)",
                    "Zwraca tangens podanego kąta (w radianach)."
                ],
                TEXT: [
                    "TEKST (Wartość, którą chcesz sformatować, „Formatuj kod, który chcesz zastosować”)",
                    "Funkcja TEKST umożliwia zmianę sposobu wyświetlania liczby poprzez zastosowanie do niej formatowania za pomocą kodów formatu."
                ],
                TIME: [
                    "CZAS(godzina, minuta, sekunda)",
                    "Zwraca liczbę dziesiętną dla określonego czasu."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Zwraca liczbę dziesiętną czasu reprezentowanego przez ciąg tekstowy.Liczba dziesiętna to wartość z zakresu od 0 (zero) do 0,99988426, reprezentująca czas od 0:00:00 (12:00:00) do 23:59:59 (23:59:59)."
                ],
                TODAY: [
                    "TODAY()",
                    "Zwraca numer seryjny bieżącej daty."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Usuwa wszystkie spacje z tekstu z wyjątkiem pojedynczych spacji między wyrazami."
                ],
                TRUNC: [
                    "TRUNC(liczba; [liczba_cyfr])",
                    "Obcina liczbę do liczby całkowitej poprzez usunięcie części ułamkowej liczby."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Konwertuje tekst na wielkie litery."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Konwertuje ciąg tekstowy reprezentujący liczbę na liczbę."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Szacuje wariancję na podstawie próbki."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Oblicza wariancję na podstawie całej populacji."
                ],
                VLOOKUP: [
                    "WYSZUKAJ.PIONOWO (wartość_wyszukiwania, tablica_tabeli, numer_indeksu kolumny, [wyszukiwanie_zakresu])",
                    "wyszukaj wartość w tabeli lub zakres po wierszu.Na przykład sprawdź cenę części samochodowej według numeru części."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Zwraca rok odpowiadający dacie.Rok jest zwracany jako liczba całkowita z zakresu 1900–9999."
                ]
            },
            strFreezeFirstCol: "Zamroź pierwszą kolumnę",
            strFreezePanes: "Zamroź szyby",
            strFreezePanesUndo: "Odblokuj szyby",
            strFreezeTopRow: "Zamroź pierwszy rząd",
            strFrozenCols: "Zamrożone kolumny",
            strFrozenRows: "Zamrożone rzędy",
            strFullScreen: "Pełny ekran",
            strGroup_fixCols: "Napraw kolumny",
            strGroup_grandSummary: "Wielkie podsumowanie",
            strGroup_header: "Upuść grupę według kolumny",
            strGroup_merge: "Połącz komórki",
            strHideCols: "Ukryj kolumny",
            strHideRows: "Ukryj wiersze",
            strImport: "Importuj dane",
            strImportPic: "Wstaw pływający obraz",
            strImportPicCell: "Wstaw obraz do komórki",
            strInsertColumn: "Wstaw kolumnę",
            strInsertRow: "Wstaw wiersz",
            strInsertRows: "Wstaw {0} wierszy",
            strItalic: "Tekst kursywą",
            strLabel: "Etykieta",
            strLink: "Link",
            strLoading: "Ładowanie",
            strLocal: "pl",
            strLockCells: "Zablokuj komórki",
            strMenu: {
                export: "Możliwość eksportu kolumn",
                filter: "Filtruj",
                hideCols: "Widoczne kolumny"
            },
            strMerge: "Połącz komórki",
            strName: "Imię",
            strNextResult: "Nast?pny wynik",
            strNoRows: "Brak wynik�w do wy?wietlenia.",
            strNothingFound: "Nic nie znaleziono",
            strOR: "LUB",
            strOk: "OK",
            strOpen: "Otwórz",
            strPaste: "Wklej",
            strPrevResult: "Poprzedni wynik",
            strRedo: "Powtórz",
            strRename: "Zmień nazwę",
            strSearch: "Szukaj",
            strSelectAll: "Wybierz wszystko",
            strSelectedmatches: "Zaznaczone {0} z {1} pasuj?cych",
            strShowCols: "Pokaż kolumny",
            strShowRows: "Pokaż wiersze",
            strTP_aggPH: "Upuść kolumny dla agregatów",
            strTP_aggPane: "Agregaty",
            strTP_colPH: "Upuść kolumny, aby grupować kolumny",
            strTP_colPane: "Grupuj kolumny",
            strTP_pivot: "Tryb obrotowy",
            strTP_rowPH: "Upuść kolumny, aby grupować wiersze",
            strTP_rowPane: "Grupuj wiersze",
            strTabAdd: "Nowy arkusz",
            strTabClose: "Usuń arkusz",
            strTabHide: "Ukryj arkusz",
            strTabName: "sheet{0}",
            strTabRemove: "{0} zostanie trwale usunięty.\r\nCzy jesteś pewien?",
            strTabRename: "Zmień nazwę arkusza",
            strTabShow: "Pokaż arkusz",
            strTextColor: "Kolor tekstu",
            strUnderline: "Podkreśl tekst",
            strUndo: "Cofnij",
            strUnhide: "Odkryj",
            strUnmerge: "Rozłącz komórki",
            strUpdate: "Aktualizacja",
            strWrap: "Zawiń tekst"
        },
        pager = pq.pqPager.regional.pl = {
            strDisplay: "Wy?wietlanie {0} do {1} z {2} element�w.",
            strFirstPage: "Pierwsza strona",
            strLastPage: "Ostatnia strona",
            strNextPage: "Nast?pna strona",
            strPage: "Strona {0} z {1}",
            strPrevPage: "Poprzednia strona",
            strRefresh: "Od?wie?",
            strRpp: "Wierszy na stron?: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();