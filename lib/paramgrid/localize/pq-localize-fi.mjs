import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.fi = {
            strAND: "JA",
            strAddColLeft: "Lisää sarake vasemmalle",
            strAddColRight: "Lisää sarake oikealle",
            strAddColumn: "Lisää sarake",
            strAddRow: "Lisää rivi",
            strAddRowAbove: "Lisää rivi yläpuolelle",
            strAddRowBelow: "Lisää rivi alle",
            strAddRows: "Lisää {0} riviä",
            strAlign: {
                bottom: "Tasaus alaosaan",
                center: "Keskitä",
                left: "Tasaa vasemmalle",
                right: "Tasaus oikealle",
                top: "Ylätasaus"
            },
            strAlignH: "Vaakasuora kohdistus",
            strAlignV: "Pystysuuntainen kohdistus",
            strApply: "Käytä",
            strBlanks: "( Tyhjät )",
            strBold: "Lihavoitu teksti",
            strBorder: "Raja",
            strBorderColor: "Reunuksen väri",
            strBorderStyle: "Reunuksen tyyli",
            strBorders: {
                all: "Kaikki rajat",
                bottom: "Alareuna",
                horizontal: "Vaakasuora raja",
                inner: "Sisäraja",
                left: "Vasen reuna",
                none: "Ei rajaa",
                outer: "Ulkoraja",
                right: "Oikea reuna",
                top: "Yläreuna",
                vertical: "Pystyreuna"
            },
            strCancel: "Peruuta",
            strClear: "Selkeä",
            strClearColor: "Kirkas väri",
            strClearFilter: "Tyhjennä suodatin",
            strClearText: "Tyhjennä teksti",
            strCollapse: "Tiivistä paneeli",
            strComment: "Kommentoi",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Ei yhtään --",
                begin: "Alkaa",
                between: "Välissä",
                contain: "Sisältää",
                empty: "Tyhjä",
                end: "Päättyy",
                equal: "Yhtä",
                great: "Suurempi kuin",
                gte: "Suurempi tai yhtä suuri",
                less: "Vähemmän kuin",
                lte: "Pienempi tai yhtä suuri",
                notbegin: "Ei aloita",
                notcontain: "Ei sisällä",
                notempty: "Ei tyhjä",
                notend: "Ei pääty",
                notequal: "Ei ole tasa-arvoinen",
                range: "[ Alue ]",
                regexp: "Säännöllinen lauseke"
            },
            strCopy: "Kopioi",
            strCut: "Leikkaa",
            strDelete: "Poista",
            strDeleteColumn: "Poista sarake",
            strDeleteRow: "Poista rivi",
            strDownloadXlsx: "Lataa Excel-tiedosto",
            strEdit: "Muokkaa",
            strExitFullScreen: "Poistu koko näytön tilasta",
            strExpand: "Laajenna paneeli",
            strExport: "Vie tiedot",
            strFillColor: "Täyttöväri",
            strFontFamily: "Fonttiperhe",
            strFontSize: "Fontin koko",
            strFormat: "Muotoile solut",
            strFormatMenu: {
                Accounting: "Kirjanpito",
                Currency: "Valuutta",
                Custom: "Mukautettu",
                DateTime: "Päivämäärä Aika",
                Fixed: "Desimaali",
                Fraction: "Murto-osa",
                FullDate: "Täysi päivämäärä",
                General: "Kenraali",
                Int: "Kokonaisluku",
                LongDate: "Pitkä treffi",
                MediumDate: "Keskipitkä päivämäärä",
                Note: "Huomautus: *-merkityt muodot muuttuvat käyttäjän kieli-asetuksen mukaan.",
                Percent: "Prosenttiosuus",
                Scientific: "Tieteellinen",
                ShortDate: "Lyhyt päivämäärä",
                Standard: "Vakio",
                Text: "Teksti",
                Time: "Aika"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Palauttaa luvun itseisarvon.Luvun itseisarvo on luku ilman sen etumerkkiä."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Palauttaa luvun arkosinin tai käänteiskosinin.Arkosiini on kulma, jonka kosini on luku.Palautettu kulma annetaan radiaaneina välillä 0 (nolla) - pi."
                ],
                AND: [
                    "JA(looginen1, [looginen2], ...)",
                    "Palauttaa arvon TOSI, jos kaikkien sen argumenttien arvo on TOSI, ja palauttaa EPÄTOSI, jos yhden tai useamman argumentin arvo on EPÄTOSI."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Palauttaa luvun arsinin tai käänteissinin.Arsini on kulma, jonka sini on luku.Palautettu kulma annetaan radiaaneina välillä -pi/2 - pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Palauttaa luvun arktangentin tai käänteisen tangentin.Arktangentti on kulma, jonka tangentti on luku.Palautettu kulma annetaan radiaaneina välillä -pi/2 - pi/2."
                ],
                AVERAGE: [
                    "KESKIARVO(numero1, [numero2], ...)",
                    "Palauttaa argumenttien keskiarvon (aritmeettisen keskiarvon).Jos esimerkiksi alue A1:A20 sisältää numeroita, kaava =KESKIKORIA(A1:A20) palauttaa näiden lukujen keskiarvon."
                ],
                AVERAGEIF: [
                    "KESKIMÄÄRÄJOS(väli, ehdot, [keskiarvo_väli])",
                    "Palauttaa kaikkien tietyn ehdon täyttävien solujen keskiarvon (aritmeettinen keskiarvo)."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(keskimääräinen_väli, ehtojen_väli1, ehdot1, [ehtoväli2, ehdot2], ...)",
                    "Palauttaa kaikkien useita ehtoja täyttävien solujen keskiarvon (aritmeettisen keskiarvon)."
                ],
                CEILING: [
                    "KATTO(luku, merkitys)",
                    "Palauttaa luvun pyöristettynä ylöspäin, poispäin nollasta, lähimpään merkityksen kerrannaiseen.Jos esimerkiksi haluat välttää pennien käyttämisen hinnoissasi ja tuotteesi hinta on 4,42 dollaria, pyöristä hinnat lähimpään nikkeliin kaavalla =KATO(4,42,0,05)."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Palauttaa numerolla määritellyn merkin."
                ],
                CHOOSE: [
                    "VALITSE(indeksin_määrä, arvo1, [arvo2], ...)",
                    "Jos indeksin_numero on 1, CHOOSE palauttaa arvon1;jos se on 2, CHOOSE palauttaa arvon2;ja niin edelleen."
                ],
                CODE: [
                    "CODE(text)",
                    "Palauttaa numerokoodin tekstimerkkijonon ensimmäiselle merkille."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Palauttaa viitteen soluun, jossa COLUMN-funktio näkyy."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Palauttaa taulukon tai viitteen sarakkeiden määrän."
                ],
                CONCATENATE: [
                    "CONCATENATE(teksti1, [teksti2], ...)",
                    "yhdistää kaksi tai useampia tekstijonoja yhdeksi merkkijonoksi."
                ],
                COS: [
                    "COS(number)",
                    "Palauttaa annetun kulman kosinin (radiaaneina)."
                ],
                COUNT: [
                    "COUNT(arvo1, [arvo2], ...)",
                    "Laskee numeroita sisältävien solujen määrän ja laskee luvut argumenttiluettelossa."
                ],
                COUNTA: [
                    "COUNTA(arvo1, [arvo2], ...)",
                    "COUNTA-funktio laskee niiden solujen määrän, jotka eivät ole tyhjiä alueella."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Laskee tyhjät solut määritetyllä solualueella."
                ],
                COUNTIF: [
                    "COUNTIF(väli, kriteeri)",
                    "laskee kriteerin täyttävien solujen määrän;esimerkiksi laskeaksesi, kuinka monta kertaa tietty kaupunki näkyy asiakasluettelossa."
                ],
                COUNTIFS: [
                    "COUNTIFS(kriteerien_väli1, ehdot1, [ehtoväli2, ehdot2]…)",
                    "Käyttää ehtoja useiden eri alueiden soluihin ja laskee, kuinka monta kertaa kaikki ehdot täyttyvät."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "palauttaa sarjanumeron, joka edustaa tiettyä päivämäärää."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Laskee päivien, kuukausien tai vuosien määrän kahden päivämäärän välillä.Yksikkö voi olla 'Y', 'M' tai 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "muuntaa tekstinä tallennetun päivämäärän sarjanumeroksi, jonka Excel tunnistaa päivämääräksi.Esimerkiksi kaava =PÄIVÄYSARVO(\"1/1/2008\") palauttaa 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Palauttaa päivämäärän päivämäärän, jota edustaa sarjanumero."
                ],
                DAYS: [
                    "PÄIVÄT(lopetuspäivä, aloituspäivä)",
                    "Palauttaa kahden päivämäärän välisten päivien määrän."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Muuntaa radiaanit asteina."
                ],
                EOMONTH: [
                    "EOMONTH(alkamispäivä, kuukautta)",
                    "Palauttaa kuukauden viimeisen päivän sarjanumeron, joka on ilmoitettu kuukausien lukumäärä ennen tai jälkeen alkamispäivämäärää."
                ],
                EXP: [
                    "EXP(number)",
                    "Palauttaa e:n korotettuna luvun potenssiin."
                ],
                FIND: [
                    "ETSI(etsi_teksti, tekstin sisällä, [aloitusnumero])",
                    "Paikantaa yhden tekstimerkkijonon toisesta tekstimerkkijonosta ja palauttaa ensimmäisen tekstimerkkijonon aloituspaikan numeron toisen tekstimerkkijonon ensimmäisestä merkistä."
                ],
                FLOOR: [
                    "FLOOR(luku, merkitys)",
                    "Pyöristää luvun alaspäin nollaa kohti lähimpään merkityksen kerrannaiseen."
                ],
                HLOOKUP: [
                    "HHAKU(haun_arvo, taulukon_taulukko, rivin_indeksin_numero, [alueen_haku])",
                    "Etsii arvoa taulukon tai arvotaulukon ylimmältä riviltä ja palauttaa sitten arvon samassa sarakkeessa taulukossa tai taulukossa määrittämältäsi riviltä."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Palauttaa aika-arvon tunnin.Tunti on annettu kokonaislukuna, joka vaihtelee välillä 0 (12:00 A.M.) - 23 (23:00)."
                ],
                HYPERLINK: [
                    "HYPERLINKKI(url, [ystävällinen_nimi])",
                    "luo pikakuvakkeen, joka hyppää toiseen paikkaan Internetissä, kun napsautat solua"
                ],
                IF: [
                    "IF(looginen_testi, arvo_jos_tosi, [arvo_jos_epätosi])",
                    "palauttaa yhden arvon, jos ehto on tosi, ja toisen arvon, jos se on epätosi."
                ],
                INDEX: [
                    "INDEKSI(taulukko, rivin_numero, [sarakkeen_numero])",
                    "Palauttaa taulukon tai taulukon elementin arvon, joka on valittu rivi- ja sarakenumeroindeksillä."
                ],
                INDIRECT: [
                    "EPÄSUORA(viiteteksti, [a1])",
                    "Palauttaa tekstimerkkijonon määrittämän viittauksen.Viitteet arvioidaan välittömästi niiden sisällön näyttämiseksi."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Palauttaa TOSI, jos arvo on tyhjä"
                ],
                LARGE: [
                    "LARGE(taulukko, k)",
                    "Palauttaa tietojoukon k:nneksi suurimman arvon."
                ],
                LEFT: [
                    "LEFT(teksti, [merkkien_määrä])",
                    "palauttaa tekstimerkkijonon ensimmäisen merkin tai merkit määrittämäsi merkkien määrän perusteella."
                ],
                LEN: [
                    "LEN(text)",
                    "palauttaa merkkijonon merkkien määrän."
                ],
                LOOKUP: [
                    "HAKU(hakuarvo, hakuvektori, [tulosvektori])",
                    "Etsii arvoa yhden rivin tai yhden sarakkeen alueelta (tunnetaan vektorina) ja palauttaa arvon samasta paikasta toiselta yhden rivin tai yhden sarakkeen alueelta."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Muuntaa kaikki tekstimerkkijonon isot kirjaimet pieniksi."
                ],
                MATCH: [
                    "VASTUU(hakuarvo, hakutaulukko, [hakutyyppi])",
                    "Etsii tietyn kohteen solualueelta ja palauttaa sitten kyseisen kohteen suhteellisen sijainnin alueella.match_type voi olla: 0 tarkalle haulle, jossa on mahdollisuus käyttää jokerimerkkejä;1 (oletus) alle, Arvot lookup_array-argumentissa on asetettava nousevaan järjestykseen;-1 suurempi kuin, arvot lookup_array argumentissa on sijoitettava laskevaan järjestykseen."
                ],
                MAX: [
                    "MAX(numero1, [numero2], ...)",
                    "Palauttaa arvojoukon suurimman arvon."
                ],
                MEDIAN: [
                    "KEDIAANI(numero1, [numero2], ...)",
                    "Palauttaa annettujen lukujen mediaanin.Mediaani on luku joukon keskellä oleva luku."
                ],
                MID: [
                    "MID(teksti, aloitusnumero, merkkien määrä)",
                    "palauttaa tietyn määrän merkkejä tekstimerkkijonosta alkaen määrittämästäsi kohdasta määrittämiesi merkkien määrän perusteella."
                ],
                MIN: [
                    "MIN(numero1, [numero2], ...)",
                    "Palauttaa arvojoukon pienimmän luvun."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Palauttaa useimmin esiintyvän tai toistuvan arvon taulukossa tai tietoalueella."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Palauttaa päivämäärän kuukauden, jota edustaa sarjanumero.Kuukausi annetaan kokonaislukuna 1 (tammikuu) ja 12 (joulukuu) välillä."
                ],
                OR: [
                    "TAI(looginen1, [looginen2], ...)",
                    "palauttaa TOSI, jos jokin sen argumenteista on TOSI, ja palauttaa EPÄTOSI, jos kaikkien argumenttien arvo on EPÄTOSI."
                ],
                PI: [
                    "PI()",
                    "Palauttaa luvun 3.14159265358979, matemaattisen vakion pi."
                ],
                POWER: [
                    "TEHO(numero, teho)",
                    "Palauttaa potenssiin korotetun luvun tuloksen."
                ],
                PRODUCT: [
                    "TUOTE(numero1, [numero2], ...)",
                    "kertoo kaikki argumenteiksi annetut luvut ja palauttaa tuotteen.TUOTE(A1:A3, C1:C3) vastaa =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Muuttaa tekstiarvon jokaisen sanan ensimmäisen kirjaimen isoksi."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Muuntaa asteet radiaaneiksi."
                ],
                RAND: [
                    "RAND()",
                    "Palauttaa tasaisesti jakautuneen satunnaisen reaaliluvun, joka on suurempi tai yhtä suuri kuin 0 ja pienempi kuin 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Palauttaa luvun arvon numeroluettelossa."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "KORVAUS(vanha_teksti, aloitusnumero, merkkien_määrä, uusi_teksti)",
                    "korvaa osan tekstistä eri merkkijonolla määrittämiesi merkkien lukumäärän perusteella."
                ],
                REPT: [
                    "REPT(teksti, lukumäärä_kertaa)",
                    "Toistaa tekstiä tietyn määrän kertoja."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "palauttaa tekstimerkkijonon viimeisen merkin tai merkit määrittämäsi merkkien määrän perusteella."
                ],
                ROUND: [
                    "ROUND(numero, lukujen_määrä)",
                    "pyöristää luvun tiettyyn määrään numeroita."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(numero, lukujen_määrä)",
                    "Pyöristää luvun alaspäin kohti nollaa."
                ],
                ROUNDUP: [
                    "ROUNDUP(numero, numerot_määrä)",
                    "Pyöristää luvun ylöspäin poispäin 0:sta (nolla)."
                ],
                ROW: [
                    "ROW()",
                    "Palauttaa viitteen soluun, jossa ROW-funktio näkyy."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Palauttaa viitteen tai taulukon rivien määrän."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "paikantaa yksi tekstimerkkijono toisesta tekstimerkkijonosta ja palauttaa ensimmäisen tekstimerkkijonon aloituskohdan numero toisen tekstimerkkijonon ensimmäisestä merkistä."
                ],
                SIN: [
                    "SIN(number)",
                    "Palauttaa annetun kulman sinin (radiaaneina)."
                ],
                SMALL: [
                    "PIENI(taulukko, k)",
                    "Palauttaa tietojoukon k:nneksi pienimmän arvon."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Palauttaa positiivisen neliöjuuren."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Arvioi keskihajonnan otoksen perusteella.Keskihajonta on mitta siitä, kuinka laajalle arvot ovat hajallaan keskiarvosta (keskiarvo)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Laskee keskihajonnan argumenteina annetun koko perusjoukon perusteella."
                ],
                SUBSTITUTE: [
                    "KORVAA(teksti, vanha_teksti, uusi_teksti, [instanssin_numero])",
                    "Korvaa uusi_teksti tekstin vanha_teksti tekstijonossa."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Lisää argumenttinsa.Voit lisätä yksittäisiä arvoja, soluviittauksia tai alueita tai kaikkien kolmen yhdistelmän."
                ],
                SUMIF: [
                    "SUMMA(väli, ehdot, [summa_väli])",
                    "lisää arvot alueelle, joka täyttää määrittämäsi ehdot"
                ],
                SUMIFS: [
                    "SUMIFS(summa_alue, ehtojen_väli1, ehdot1, [ehtoväli2, ehdot2], ...)",
                    "lisää kaikki argumenttinsa, jotka täyttävät useita ehtoja."
                ],
                SUMPRODUCT: [
                    "SUMMA(taulukko1, [taulukko2], [taulukko3], ...)",
                    "Kertoo annettujen taulukoiden vastaavat komponentit ja palauttaa näiden tulojen summan."
                ],
                TAN: [
                    "TAN(number)",
                    "Palauttaa annetun kulman tangentin (radiaaneina)."
                ],
                TEXT: [
                    "TEKSTI (Muotoiltava arvo, \"Muotoile koodi, jota haluat käyttää\")",
                    "TEKSTI-toiminnolla voit muuttaa tapaa, jolla numero esiintyy muotoilemalla sitä muotokoodeilla."
                ],
                TIME: [
                    "AIKA (tunti, minuutti, sekunti)",
                    "Palauttaa desimaaliluvun tietyltä ajalta."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Palauttaa tekstimerkkijonon edustaman ajan desimaaliluvun.Desimaaliluku on arvo, joka vaihtelee välillä 0 (nolla) - 0,99988426, ja se edustaa aikoja 0:00:00 (0:00:00) - 23:59:59 (23:59:59)."
                ],
                TODAY: [
                    "TODAY()",
                    "Palauttaa nykyisen päivämäärän sarjanumeron."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Poistaa tekstistä kaikki välilyönnit paitsi yksittäiset välilyönnit sanojen välillä."
                ],
                TRUNC: [
                    "TRUNC(numero, [numerot_määrä])",
                    "Katkaisee luvun kokonaisluvuksi poistamalla luvun murto-osan."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Muuntaa tekstin isoiksi kirjaimiksi."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Muuntaa numeroa edustavan tekstijonon luvuksi."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Arvioi varianssin otoksen perusteella."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Laskee varianssin koko populaation perusteella."
                ],
                VLOOKUP: [
                    "VHAKU (haun_arvo, taulukon_taulukko, sarakkeen_indeksin_määrä, [alueen_haku])",
                    "etsiä arvoa taulukosta tai alueelta riviltä.Voit esimerkiksi etsiä auton osan hintaa osanumeron perusteella."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Palauttaa päivämäärää vastaavan vuoden.Vuosi palautetaan kokonaislukuna välillä 1900-9999."
                ]
            },
            strFreezeFirstCol: "Pysäytä ensimmäinen sarake",
            strFreezePanes: "Jäädyttää ruudut",
            strFreezePanesUndo: "Avaa ruutujen jäädytys",
            strFreezeTopRow: "Jäädytä ensimmäinen rivi",
            strFrozenCols: "Jäädytetyt sarakkeet",
            strFrozenRows: "Jäädytetyt rivit",
            strFullScreen: "Koko näyttö",
            strGroup_fixCols: "Korjaa sarakkeet",
            strGroup_grandSummary: "Suuri yhteenveto",
            strGroup_header: "Pudota ryhmä sarakkeelta",
            strGroup_merge: "Yhdistä solut",
            strHideCols: "Piilota sarakkeet",
            strHideRows: "Piilota rivit",
            strImport: "Tuo tiedot",
            strImportPic: "Lisää kelluva kuva",
            strImportPicCell: "Lisää kuva soluun",
            strInsertColumn: "Lisää sarake",
            strInsertRow: "Lisää rivi",
            strInsertRows: "Lisää {0} riviä",
            strItalic: "Kursivoitu teksti",
            strLabel: "Label",
            strLink: "Linkki",
            strLoading: "Ladataan",
            strLocal: "fi",
            strLockCells: "Lukitse solut",
            strMenu: {
                export: "Vietävät sarakkeet",
                filter: "Suodata",
                hideCols: "Näkyviä sarakkeita"
            },
            strMerge: "Yhdistä solut",
            strName: "Nimi",
            strNextResult: "Seuraava tulos",
            strNoRows: "Ei näytettäviä rivejä.",
            strNothingFound: "Mitään ei löytynyt",
            strOR: "TAI",
            strOk: "Ok",
            strOpen: "Avaa",
            strPaste: "Liitä",
            strPrevResult: "Edellinen tulos",
            strRedo: "Toista",
            strRename: "Nimeä uudelleen",
            strSearch: "Etsi",
            strSelectAll: "Valitse Kaikki",
            strSelectedmatches: "Valittu {0}/{1} osumaa",
            strShowCols: "Näytä sarakkeet",
            strShowRows: "Näytä rivit",
            strTP_aggPH: "Pudota sarakkeita aggregaatteja varten",
            strTP_aggPane: "Aggregaatit",
            strTP_colPH: "Pudota sarakkeet sarakkeiden ryhmittelyä varten",
            strTP_colPane: "Ryhmittele sarakkeet",
            strTP_pivot: "Pivot-tila",
            strTP_rowPH: "Pudota sarakkeita rivien ryhmittelyä varten",
            strTP_rowPane: "Ryhmittele rivit",
            strTabAdd: "Uusi arkki",
            strTabClose: "Poista arkki",
            strTabHide: "Piilota arkki",
            strTabName: "sheet{0}",
            strTabRemove: "{0} poistetaan pysyvästi.\r\nOletko varma?",
            strTabRename: "Nimeä taulukko uudelleen",
            strTabShow: "Näytä arkki",
            strTextColor: "Tekstin väri",
            strUnderline: "Alleviivaa teksti",
            strUndo: "Kumoa",
            strUnhide: "Näytä",
            strUnmerge: "Pura solujen yhdistäminen",
            strUpdate: "Päivitä",
            strWrap: "Kääri teksti"
        },
        pager = pq.pqPager.regional.fi = {
            strDisplay: "Näytetään {0}–{1}/{2} kohdetta.",
            strFirstPage: "Ensimmäinen sivu",
            strLastPage: "Viimeinen sivu",
            strNextPage: "Seuraava sivu",
            strPage: "Sivu {0}/{1}",
            strPrevPage: "Edellinen sivu",
            strRefresh: "Päivitä",
            strRpp: "Tietueita sivua kohden: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);