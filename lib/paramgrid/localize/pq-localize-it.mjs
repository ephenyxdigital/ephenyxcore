import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.it = {
            strAND: "E",
            strAddColLeft: "Aggiungi colonna a sinistra",
            strAddColRight: "Aggiungi colonna a destra",
            strAddColumn: "Aggiungi colonna",
            strAddRow: "Aggiungi riga",
            strAddRowAbove: "Aggiungi la riga sopra",
            strAddRowBelow: "Aggiungi la riga qui sotto",
            strAddRows: "Aggiungi {0} righe",
            strAlign: {
                bottom: "Allinea in basso",
                center: "Allinea al centro",
                left: "Allinea a sinistra",
                right: "Allinea a destra",
                top: "Allinea in alto"
            },
            strAlignH: "Allineamento orizzontale",
            strAlignV: "Allineamento verticale",
            strApply: "Applicare",
            strBlanks: "(Spazi vuoti)",
            strBold: "Testo in grassetto",
            strBorder: "Confine",
            strBorderColor: "Colore del bordo",
            strBorderStyle: "Stile del bordo",
            strBorders: {
                all: "Tutti i confini",
                bottom: "Bordo inferiore",
                horizontal: "Bordo orizzontale",
                inner: "Confine interno",
                left: "Bordo sinistro",
                none: "Nessun confine",
                outer: "Confine esterno",
                right: "Bordo destro",
                top: "Bordo superiore",
                vertical: "Bordo verticale"
            },
            strCancel: "Annulla",
            strClear: "Chiara",
            strClearColor: "Colore chiaro",
            strClearFilter: "Cancella filtro",
            strClearText: "Testo chiaro",
            strCollapse: "Comprimi pannello",
            strComment: "Commento",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Nessuno --",
                begin: "Inizia con",
                between: "Nel mezzo",
                contain: "Contiene",
                empty: "Vuoto",
                end: "Finisce con",
                equal: "Uguali",
                great: "Maggiore di",
                gte: "Maggiore o uguale",
                less: "Meno di",
                lte: "Minore o uguale",
                notbegin: "Non inizia con",
                notcontain: "Non contiene",
                notempty: "Non vuoto",
                notend: "Non finisce con",
                notequal: "Non uguali",
                range: "[ Gamma ]",
                regexp: "Espressione regolare"
            },
            strCopy: "Copia",
            strCut: "Taglia",
            strDelete: "Cancella",
            strDeleteColumn: "Elimina colonna",
            strDeleteRow: "Elimina riga",
            strDownloadXlsx: "Scarica il file Excel",
            strEdit: "Modifica",
            strExitFullScreen: "Esci dallo schermo intero",
            strExpand: "Espandi pannello",
            strExport: "Esporta dati",
            strFillColor: "Colore riempimento",
            strFontFamily: "Famiglia di caratteri",
            strFontSize: "Dimensione del carattere",
            strFormat: "Formattare le celle",
            strFormatMenu: {
                Accounting: "Contabilità",
                Currency: "Valuta",
                Custom: "Personalizzato",
                DateTime: "Data Ora",
                Fixed: "Decimale",
                Fraction: "Frazione",
                FullDate: "Data completa",
                General: "Generale",
                Int: "Intero",
                LongDate: "Data lunga",
                MediumDate: "Data media",
                Note: "Nota: i formati con * cambiano in base alle impostazioni internazionali dell'utente.",
                Percent: "Percentuale",
                Scientific: "Scientifico",
                ShortDate: "Data breve",
                Standard: "Norma",
                Text: "Testo",
                Time: "Tempo"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Restituisce il valore assoluto di un numero.Il valore assoluto di un numero è il numero senza segno."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Restituisce l'arcoseno, o coseno inverso, di un numero.L'arcocoseno è l'angolo il cui coseno è il numero.L'angolo restituito è espresso in radianti nell'intervallo da 0 (zero) a pi greco."
                ],
                AND: [
                    "AND(logico1, [logico2], ...)",
                    "Restituisce VERO se tutti gli argomenti restituiscono VERO e restituisce FALSO se uno o più argomenti restituiscono FALSO."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Restituisce l'arcoseno, o seno inverso, di un numero.L'arcoseno è l'angolo il cui seno è il numero.L'angolo restituito è espresso in radianti nell'intervallo da -pi/2 a pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Restituisce l'arcotangente, o tangente inversa, di un numero.L'arcotangente è l'angolo la cui tangente è un numero.L'angolo restituito è espresso in radianti nell'intervallo da -pi/2 a pi/2."
                ],
                AVERAGE: [
                    "MEDIA(numero1, [numero2], ...)",
                    "Restituisce la media (media aritmetica) degli argomenti.Ad esempio, se l'intervallo A1:A20 contiene numeri, la formula =MEDIA(A1:A20) restituisce la media di tali numeri."
                ],
                AVERAGEIF: [
                    "MEDIA.SE(intervallo, criteri, [intervallo_medio])",
                    "Restituisce la media (media aritmetica) di tutte le celle in un intervallo che soddisfano un determinato criterio."
                ],
                AVERAGEIFS: [
                    "MEDIA.SE(intervallo_medio, intervallo_criteri1, criteri1, [intervallo_criteri2, criteri2], ...)",
                    "Restituisce la media (media aritmetica) di tutte le celle che soddisfano più criteri."
                ],
                CEILING: [
                    "CEILING(numero, significato)",
                    "Restituisce il numero arrotondato per eccesso, lontano da zero, al multiplo significativo più vicino.Ad esempio, se vuoi evitare di utilizzare centesimi nei prezzi e il tuo prodotto ha un prezzo di $ 4,42, utilizza la formula =CEILING(4.42,0.05) per arrotondare i prezzi al nichel più vicino."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Restituisce il carattere specificato da un numero."
                ],
                CHOOSE: [
                    "SCEGLI(num_indice, valore1, [valore2], ...)",
                    "Se indice_num è 1, SCEGLI restituisce valore1;se è 2, SCEGLI restituisce valore2;e così via."
                ],
                CODE: [
                    "CODE(text)",
                    "Restituisce un codice numerico per il primo carattere in una stringa di testo."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Restituisce il riferimento della cella in cui appare la funzione COLONNA."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Restituisce il numero di colonne in una matrice o riferimento."
                ],
                CONCATENATE: [
                    "CONCATENA(testo1, [testo2], ...)",
                    "unire due o più stringhe di testo in un'unica stringa."
                ],
                COS: [
                    "COS(number)",
                    "Restituisce il coseno dell'angolo specificato (in radianti)."
                ],
                COUNT: [
                    "COUNT(valore1, [valore2], ...)",
                    "Conta il numero di celle che contengono numeri e conta i numeri all'interno dell'elenco di argomenti."
                ],
                COUNTA: [
                    "CONTA(valore1, [valore2], ...)",
                    "La funzione CONTA.CONTA conta il numero di celle non vuote in un intervallo."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Conta le celle vuote in un intervallo di celle specificato."
                ],
                COUNTIF: [
                    "CONTA.SE(intervallo, criteri)",
                    "conta il numero di celle che soddisfano un criterio;ad esempio, per contare il numero di volte in cui una determinata città appare nell'elenco dei clienti."
                ],
                COUNTIFS: [
                    "CONTA.SE(intervallo_criteri1, criteri1, [intervallo_criteri2, criteri2]…)",
                    "Applica i criteri alle celle di più intervalli e conta il numero di volte in cui tutti i criteri vengono soddisfatti."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "restituisce il numero seriale sequenziale che rappresenta una data particolare."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Calcola il numero di giorni, mesi o anni tra due date.L'unità può essere \"Y\", \"M\" o \"D\"."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "converte una data memorizzata come testo in un numero seriale che Excel riconosce come data.Ad esempio, la formula =DATAVALORE(\"1/1/2008\") restituisce 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Restituisce il giorno di una data, rappresentato da un numero seriale."
                ],
                DAYS: [
                    "GIORNI(data_fine, data_inizio)",
                    "Restituisce il numero di giorni tra due date."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Converte i radianti in gradi."
                ],
                EOMONTH: [
                    "EOMONTH(data_inizio, mesi)",
                    "Restituisce il numero di serie per l'ultimo giorno del mese ovvero il numero indicato di mesi prima o dopo start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Restituisce e elevato alla potenza del numero."
                ],
                FIND: [
                    "TROVA(trova_testo, all'interno del_testo, [numero_iniziale])",
                    "Individua una stringa di testo all'interno di una seconda stringa di testo e restituisce il numero della posizione iniziale della prima stringa di testo dal primo carattere della seconda stringa di testo."
                ],
                FLOOR: [
                    "FLOOR(numero, significato)",
                    "Arrotonda il numero per difetto, verso lo zero, al multiplo significativo più vicino."
                ],
                HLOOKUP: [
                    "CERCA.ORIZZ(valore_ricerca, matrice_tabella, numero_indice_riga, [ricerca_intervallo])",
                    "Cerca un valore nella riga superiore di una tabella o in una matrice di valori, quindi restituisce un valore nella stessa colonna da una riga specificata nella tabella o nella matrice."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Restituisce l'ora di un valore temporale.L'ora viene fornita come numero intero, compreso tra 0 (00:00) e 23 (23:00)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [nome descrittivo])",
                    "crea un collegamento che passa a un'altra posizione in Internet quando si fa clic su una cella"
                ],
                IF: [
                    "SE(test_logico, valore_se_vero, [valore_se_falso])",
                    "restituisce un valore se una condizione è vera e un altro valore se è falsa."
                ],
                INDEX: [
                    "INDICE(matrice, numero_riga, [numero_colonna])",
                    "Restituisce il valore di un elemento in una tabella o in un array, selezionato dagli indici dei numeri di riga e colonna."
                ],
                INDIRECT: [
                    "INDIRETTO(testo_rif, [a1])",
                    "Restituisce il riferimento specificato da una stringa di testo.I riferimenti vengono immediatamente valutati per visualizzarne il contenuto."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Restituisce TRUE se il valore è vuoto"
                ],
                LARGE: [
                    "GRANDE(matrice, k)",
                    "Restituisce il k-esimo valore più grande in un set di dati."
                ],
                LEFT: [
                    "SINISTRA(testo, [num_caratteri])",
                    "restituisce il primo carattere o i primi caratteri in una stringa di testo, in base al numero di caratteri specificato."
                ],
                LEN: [
                    "LEN(text)",
                    "restituisce il numero di caratteri in una stringa di testo."
                ],
                LOOKUP: [
                    "CERCA(valore_ricerca, vettore_ricerca, [vettore_risultato])",
                    "Cerca un valore in un intervallo di una riga o di una colonna (noto come vettore) e restituisce un valore dalla stessa posizione in un secondo intervallo di una riga o di una colonna."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Converte tutte le lettere maiuscole in una stringa di testo in minuscole."
                ],
                MATCH: [
                    "CONFRONTA(valore_ricerca, matrice_ricerca, [tipo_corrispondenza])",
                    "Cerca un elemento specificato in un intervallo di celle, quindi restituisce la posizione relativa di tale elemento nell'intervallo.match_type può essere: 0 per corrispondenza esatta con opzione per utilizzare caratteri jolly;1 ( predefinito ) per minore di, i valori nell'argomento lookup_array devono essere inseriti in ordine crescente;-1 per maggiore di, i valori nell'argomento lookup_array devono essere inseriti in ordine decrescente."
                ],
                MAX: [
                    "MAX(numero1, [numero2], ...)",
                    "Restituisce il valore più grande in un insieme di valori."
                ],
                MEDIAN: [
                    "MEDIANA(numero1, [numero2], ...)",
                    "Restituisce la mediana dei numeri indicati.La mediana è il numero al centro di un insieme di numeri."
                ],
                MID: [
                    "MID(testo, numero_iniziale, numero_caratteri)",
                    "restituisce un numero specifico di caratteri da una stringa di testo, a partire dalla posizione specificata, in base al numero di caratteri specificato."
                ],
                MIN: [
                    "MIN(numero1, [numero2], ...)",
                    "Restituisce il numero più piccolo in un insieme di valori."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Restituisce il valore più frequente o ripetitivo in una matrice o intervallo di dati."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Restituisce il mese di una data rappresentata da un numero seriale.Il mese viene fornito come numero intero, compreso tra 1 (gennaio) e 12 (dicembre)."
                ],
                OR: [
                    "OR(logico1, [logico2], ...)",
                    "restituisce VERO se uno qualsiasi degli argomenti restituisce VERO e restituisce FALSO se tutti gli argomenti restituiscono FALSO."
                ],
                PI: [
                    "PI()",
                    "Restituisce il numero 3.14159265358979, la costante matematica pi greco."
                ],
                POWER: [
                    "POTENZA(numero, potenza)",
                    "Restituisce il risultato di un numero elevato a potenza."
                ],
                PRODUCT: [
                    "PRODOTTO(numero1, [numero2], ...)",
                    "moltiplica tutti i numeri forniti come argomenti e restituisce il prodotto.PRODOTTO(A1:A3; C1:C3) equivale a =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Rende maiuscola la prima lettera di ogni parola di un valore di testo."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Converte i gradi in radianti."
                ],
                RAND: [
                    "RAND()",
                    "Restituisce un numero reale casuale distribuito uniformemente maggiore o uguale a 0 e inferiore a 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Restituisce il rango di un numero in un elenco di numeri."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "SOSTITUISCI(vecchio_testo, numero_iniziale, numero_caratteri, nuovo_testo)",
                    "sostituisce parte di una stringa di testo, in base al numero di caratteri specificati, con una stringa di testo diversa."
                ],
                REPT: [
                    "REPT(testo, numero_volte)",
                    "Ripete il testo un determinato numero di volte."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "restituisce l'ultimo carattere o gli ultimi caratteri in una stringa di testo, in base al numero di caratteri specificato."
                ],
                ROUND: [
                    "ROUND(numero, num_cifre)",
                    "arrotonda un numero a un numero di cifre specificato."
                ],
                ROUNDDOWN: [
                    "ARROTONDA PER GIÙ(numero, num_cifre)",
                    "Arrotonda un numero per difetto, verso lo zero."
                ],
                ROUNDUP: [
                    "ROUNDUP(numero, num_cifre)",
                    "Arrotonda un numero per eccesso, lontano da 0 (zero)."
                ],
                ROW: [
                    "ROW()",
                    "Restituisce il riferimento della cella in cui appare la funzione RIGA."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Restituisce il numero di righe in un riferimento o una matrice."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "individua una stringa di testo all'interno di una seconda stringa di testo e restituisce il numero della posizione iniziale della prima stringa di testo dal primo carattere della seconda stringa di testo."
                ],
                SIN: [
                    "SIN(number)",
                    "Restituisce il seno dell'angolo specificato (in radianti)."
                ],
                SMALL: [
                    "PICCOLO(matrice, k)",
                    "Restituisce il k-esimo valore più piccolo in un set di dati."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Restituisce una radice quadrata positiva."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Stima la deviazione standard sulla base di un campione.La deviazione standard è una misura di quanto ampiamente i valori sono dispersi dal valore medio (la media)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Calcola la deviazione standard in base all'intera popolazione fornita come argomenti."
                ],
                SUBSTITUTE: [
                    "SOSTITUTO(testo, vecchio_testo, nuovo_testo, [numero_istanza])",
                    "Sostituisce new_text con old_text in una stringa di testo."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Aggiunge i suoi argomenti.Puoi aggiungere singoli valori, riferimenti di cella o intervalli o un mix di tutti e tre."
                ],
                SUMIF: [
                    "SOMMA.SE(intervallo, criteri, [intervallo_somma])",
                    "aggiunge i valori in un intervallo che soddisfano i criteri specificati"
                ],
                SUMIFS: [
                    "SOMMA.FS(intervallo_somma, intervallo_criteri1, criteri1, [intervallo_criteri2, criteri2], ...)",
                    "aggiunge tutti i suoi argomenti che soddisfano più criteri."
                ],
                SUMPRODUCT: [
                    "PRODOTTOSOMMA(matrice1, [matrice2], [matrice3], ...)",
                    "Moltiplica i componenti corrispondenti negli array specificati e restituisce la somma di tali prodotti."
                ],
                TAN: [
                    "TAN(number)",
                    "Restituisce la tangente dell'angolo specificato (in radianti)."
                ],
                TEXT: [
                    "TEXT(Valore che desideri formattare, \"Codice formato che desideri applicare\")",
                    "La funzione TESTO ti consente di modificare il modo in cui appare un numero applicandogli la formattazione con codici di formato."
                ],
                TIME: [
                    "TEMPO(ora, minuto, secondo)",
                    "Restituisce il numero decimale per un'ora particolare."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Restituisce il numero decimale dell'ora rappresentata da una stringa di testo.Il numero decimale è un valore compreso tra 0 (zero) e 0,99988426, che rappresenta le ore dalle 0:00:00 (00:00:00) alle 23:59:59 (23:59:59)."
                ],
                TODAY: [
                    "TODAY()",
                    "Restituisce il numero seriale della data corrente."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Rimuove tutti gli spazi dal testo ad eccezione dei singoli spazi tra le parole."
                ],
                TRUNC: [
                    "TRUNC(numero, [num_cifre])",
                    "Tronca un numero in un numero intero rimuovendo la parte frazionaria del numero."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Converte il testo in maiuscolo."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Converte una stringa di testo che rappresenta un numero in un numero."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Stima la varianza sulla base di un campione."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Calcola la varianza in base all'intera popolazione."
                ],
                VLOOKUP: [
                    "CERCA.VERT (valore_ricerca, matrice_tabella, numero_indice_col, [ricerca_intervallo])",
                    "cercare un valore in una tabella o un intervallo per riga.Ad esempio, cerca il prezzo di un componente automobilistico in base al numero del componente."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Restituisce l'anno corrispondente a una data.L'anno viene restituito come numero intero compreso tra 1900 e 9999."
                ]
            },
            strFreezeFirstCol: "Blocca la prima colonna",
            strFreezePanes: "Blocca i riquadri",
            strFreezePanesUndo: "Sblocca i riquadri",
            strFreezeTopRow: "Blocca la prima riga",
            strFrozenCols: "Colonne congelate",
            strFrozenRows: "Righe congelate",
            strFullScreen: "Schermo intero",
            strGroup_fixCols: "Correggi le colonne",
            strGroup_grandSummary: "Grande sintesi",
            strGroup_header: "Elimina il gruppo per colonna",
            strGroup_merge: "Unisci celle",
            strHideCols: "Nascondi colonne",
            strHideRows: "Nascondi righe",
            strImport: "Importa dati",
            strImportPic: "Inserisci immagine mobile",
            strImportPicCell: "Inserisci l'immagine nella cella",
            strInsertColumn: "Inserisci colonna",
            strInsertRow: "Inserisci riga",
            strInsertRows: "Inserisci {0} righe",
            strItalic: "Testo corsivo",
            strLabel: "Etichetta",
            strLink: "Collegamento",
            strLoading: "Caricamento in corso",
            strLocal: "it",
            strLockCells: "Blocca le celle",
            strMenu: {
                export: "Colonne esportabili",
                filter: "Filtra",
                hideCols: "Colonne visibili"
            },
            strMerge: "Unisci celle",
            strName: "Nome",
            strNextResult: "Risultato successivo",
            strNoRows: "Nessun file da visualizzare.",
            strNothingFound: "Nessun risultato trovato",
            strOR: "O",
            strOk: "Ok",
            strOpen: "Aperto",
            strPaste: "Incolla",
            strPrevResult: "Risultato precedente",
            strRedo: "Rifare",
            strRename: "Rinominare",
            strSearch: "Cerca",
            strSelectAll: "Seleziona tutto",
            strSelectedmatches: "Selezionato {0} di {1} risultato(i)",
            strShowCols: "Mostra colonne",
            strShowRows: "Mostra righe",
            strTP_aggPH: "Eliminare le colonne per gli aggregati",
            strTP_aggPane: "Aggregati",
            strTP_colPH: "Elimina colonne per il raggruppamento di colonne",
            strTP_colPane: "Colonne di gruppo",
            strTP_pivot: "Modalità pivot",
            strTP_rowPH: "Elimina colonne per il raggruppamento di righe",
            strTP_rowPane: "Righe di gruppo",
            strTabAdd: "Nuovo foglio",
            strTabClose: "Rimuovere il foglio",
            strTabHide: "Nascondi foglio",
            strTabName: "sheet{0}",
            strTabRemove: "{0} verrebbe eliminato definitivamente.\r\nSei sicuro?",
            strTabRename: "Rinomina foglio",
            strTabShow: "Mostra foglio",
            strTextColor: "Colore del testo",
            strUnderline: "Sottolinea il testo",
            strUndo: "Annulla",
            strUnhide: "Scopri",
            strUnmerge: "Separa le celle",
            strUpdate: "Aggiorna",
            strWrap: "Wrap Text"
        },
        pager = pq.pqPager.regional.it = {
            strDisplay: "Visualizzazione {0} a {1} di {2} risultati.",
            strFirstPage: "Prima Pagina",
            strLastPage: "Ultima pagina",
            strNextPage: "Pagina successiva",
            strPage: "Pagina {0} di {1}",
            strPrevPage: "Pagina precedente",
            strRefresh: "Aggiorna",
            strRpp: "Risultati per pagina: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);