import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.id = {
            strAND: "DAN",
            strAddColLeft: "Tambahkan kolom ke kiri",
            strAddColRight: "Tambahkan kolom ke kanan",
            strAddColumn: "Tambahkan Kolom",
            strAddRow: "Tambahkan Baris",
            strAddRowAbove: "Tambahkan baris di atas",
            strAddRowBelow: "Tambahkan baris di bawah",
            strAddRows: "Tambahkan {0} Baris",
            strAlign: {
                bottom: "Perataan bawah",
                center: "Rata tengah",
                left: "Rata kiri",
                right: "Sejajarkan kanan",
                top: "Sejajarkan atas"
            },
            strAlignH: "Penjajaran horizontal",
            strAlignV: "Penjajaran vertikal",
            strApply: "Terapkan",
            strBlanks: "(Kosong)",
            strBold: "Teks tebal",
            strBorder: "Perbatasan",
            strBorderColor: "Warna Perbatasan",
            strBorderStyle: "Gaya Perbatasan",
            strBorders: {
                all: "Semua Perbatasan",
                bottom: "Batas Bawah",
                horizontal: "Perbatasan Horisontal",
                inner: "Perbatasan Dalam",
                left: "Perbatasan Kiri",
                none: "Tanpa Batas",
                outer: "Perbatasan Luar",
                right: "Perbatasan Kanan",
                top: "Perbatasan Atas",
                vertical: "Perbatasan Vertikal"
            },
            strCancel: "Batalkan",
            strClear: "Jernih",
            strClearColor: "Warna Bening",
            strClearFilter: "Hapus Filter",
            strClearText: "Hapus Teks",
            strCollapse: "Ciutkan panel",
            strComment: "Komentar",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Tidak ada --",
                begin: "Dimulai dengan",
                between: "Di antara keduanya",
                contain: "Berisi",
                empty: "Kosong",
                end: "Berakhir Dengan",
                equal: "Setara",
                great: "Lebih besar dari",
                gte: "Lebih besar dari atau sama",
                less: "Kurang dari",
                lte: "Kurang dari atau sama",
                notbegin: "Tidak dimulai dengan",
                notcontain: "Tidak berisi",
                notempty: "Tidak kosong",
                notend: "Tidak berakhir dengan",
                notequal: "Tidak setara",
                range: "[ Rentang ]",
                regexp: "Ekspresi reguler"
            },
            strCopy: "Salin",
            strCut: "Potong",
            strDelete: "Hapus",
            strDeleteColumn: "Hapus Kolom",
            strDeleteRow: "Hapus Baris",
            strDownloadXlsx: "Unduh file Excelnya",
            strEdit: "Sunting",
            strExitFullScreen: "Keluar dari Layar Penuh",
            strExpand: "Perluas panel",
            strExport: "Ekspor data",
            strFillColor: "Isi Warna",
            strFontFamily: "Keluarga font",
            strFontSize: "Ukuran huruf",
            strFormat: "Memformat sel",
            strFormatMenu: {
                Accounting: "Akuntansi",
                Currency: "Mata uang",
                Custom: "Adat",
                DateTime: "Tanggal Waktu",
                Fixed: "Desimal",
                Fraction: "Pecahan",
                FullDate: "Tanggal Lengkap",
                General: "Umum",
                Int: "bilangan bulat",
                LongDate: "Tanggal Panjang",
                MediumDate: "Tanggal Sedang",
                Note: "Catatan: Format dengan * berubah sesuai dengan lokal pengguna.",
                Percent: "Persentase",
                Scientific: "Ilmiah",
                ShortDate: "Tanggal Singkat",
                Standard: "Standar",
                Text: "Teks",
                Time: "Waktu"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Mengembalikan nilai absolut suatu angka.Nilai mutlak suatu bilangan adalah bilangan yang tidak mempunyai tanda."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Mengembalikan arccosinus, atau invers cosinus, suatu bilangan.Arccosine adalah sudut yang cosinusnya berupa bilangan.Sudut kembali diberikan dalam radian dalam rentang 0 (nol) hingga pi."
                ],
                AND: [
                    "DAN(logis1, [logis2], ...)",
                    "Mengembalikan TRUE jika semua argumennya bernilai TRUE, dan mengembalikan FALSE jika satu atau lebih argumen bernilai FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Mengembalikan arcsinus, atau sinus invers, suatu bilangan.Arcsinus adalah sudut yang sinusnya berupa bilangan.Sudut yang dikembalikan diberikan dalam radian dalam rentang -pi/2 hingga pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Mengembalikan arctangen, atau invers tangen, suatu bilangan.Garis singgung busur adalah sudut yang garis singgungnya berupa bilangan.Sudut yang dikembalikan diberikan dalam radian dalam rentang -pi/2 hingga pi/2."
                ],
                AVERAGE: [
                    "RATA-RATA(angka1, [angka2], ...)",
                    "Mengembalikan rata-rata (rata-rata aritmatika) argumen.Misalnya, jika rentang A1:A20 berisi angka, rumus =AVERAGE(A1:A20) mengembalikan rata-rata angka tersebut."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(rentang, kriteria, [rentang_rata-rata])",
                    "Mengembalikan rata-rata (rata-rata aritmatika) semua sel dalam rentang yang memenuhi kriteria tertentu."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(rentang_rata-rata, rentang_kriteria1, kriteria1, [rentang_kriteria2, kriteria2], ...)",
                    "Mengembalikan rata-rata (rata-rata aritmatika) semua sel yang memenuhi beberapa kriteria."
                ],
                CEILING: [
                    "CEILING (angka, signifikansi)",
                    "Mengembalikan angka yang dibulatkan ke atas, menjauhi nol, hingga kelipatan signifikansi terdekat.Misalnya, jika Anda ingin menghindari penggunaan sen dalam harga dan produk Anda diberi harga $4,42, gunakan rumus =CEILING(4.42,0.05) untuk membulatkan harga ke nikel terdekat."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Mengembalikan karakter yang ditentukan oleh angka."
                ],
                CHOOSE: [
                    "PILIH(angka_indeks, nilai1, [nilai2], ...)",
                    "Jika angka_indeks adalah 1, CHOOSE mengembalikan nilai1;jika 2, CHOOSE mengembalikan nilai2;dan sebagainya."
                ],
                CODE: [
                    "CODE(text)",
                    "Mengembalikan kode numerik untuk karakter pertama dalam string teks."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Mengembalikan referensi sel tempat fungsi COLUMN muncul."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Mengembalikan jumlah kolom dalam array atau referensi."
                ],
                CONCATENATE: [
                    "MENGHUBUNGKAN(teks1, [teks2], ...)",
                    "menggabungkan dua atau lebih string teks menjadi satu string."
                ],
                COS: [
                    "COS(number)",
                    "Mengembalikan kosinus dari sudut tertentu (dalam radian)."
                ],
                COUNT: [
                    "JUMLAH(nilai1, [nilai2], ...)",
                    "Menghitung jumlah sel yang berisi angka, dan menghitung angka dalam daftar argumen."
                ],
                COUNTA: [
                    "COUNTA(nilai1, [nilai2], ...)",
                    "Fungsi COUNTA menghitung jumlah sel yang tidak kosong dalam suatu rentang."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Menghitung sel kosong dalam rentang sel tertentu."
                ],
                COUNTIF: [
                    "COUNTIF(rentang, kriteria)",
                    "menghitung jumlah sel yang memenuhi kriteria;misalnya, untuk menghitung berapa kali kota tertentu muncul dalam daftar pelanggan."
                ],
                COUNTIFS: [
                    "COUNTIFS(rentang_kriteria1, kriteria1, [rentang_kriteria2, kriteria2]…)",
                    "Menerapkan kriteria ke sel di beberapa rentang dan menghitung berapa kali semua kriteria terpenuhi."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "mengembalikan nomor seri berurutan yang mewakili tanggal tertentu."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Menghitung jumlah hari, bulan, atau tahun antara dua tanggal.Satuannya bisa 'Y', 'M' atau 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "mengonversi tanggal yang disimpan sebagai teks menjadi nomor seri yang dikenali Excel sebagai tanggal.Misalnya, rumus =DATEVALUE(\"1/1/2008\") menghasilkan 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Mengembalikan hari dari suatu tanggal, diwakili oleh nomor seri."
                ],
                DAYS: [
                    "HARI(tanggal_akhir, tanggal_mulai)",
                    "Mengembalikan jumlah hari antara dua tanggal."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Mengubah radian menjadi derajat."
                ],
                EOMONTH: [
                    "EOMONTH(tanggal_mulai, bulan)",
                    "Mengembalikan nomor seri untuk hari terakhir pada bulan yang merupakan jumlah bulan yang ditunjukkan sebelum atau sesudah tanggal_mulai."
                ],
                EXP: [
                    "EXP(number)",
                    "Mengembalikan e dipangkatkan dengan angka."
                ],
                FIND: [
                    "TEMUKAN(temukan_teks, dalam_teks, [angka_mulai])",
                    "Menemukan satu string teks dalam string teks kedua, dan mengembalikan nomor posisi awal string teks pertama dari karakter pertama string teks kedua."
                ],
                FLOOR: [
                    "LANTAI(angka, signifikansi)",
                    "Membulatkan angka ke bawah, menuju nol, ke kelipatan signifikansi terdekat."
                ],
                HLOOKUP: [
                    "HLOOKUP(nilai_pencarian, array_tabel, jumlah_indeks_baris, [pencarian_kisaran])",
                    "Mencari nilai di baris atas tabel atau larik nilai, lalu mengembalikan nilai di kolom yang sama dari baris yang Anda tentukan di tabel atau larik."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Mengembalikan jam dari nilai waktu.Jam diberikan sebagai bilangan bulat, mulai dari 0 (12:00) hingga 23 (11:00)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [nama_ramah])",
                    "membuat pintasan yang melompat ke lokasi lain di Internet saat Anda mengklik sel"
                ],
                IF: [
                    "JIKA(uji_logis, nilai_jika_benar, [nilai_jika_salah])",
                    "mengembalikan satu nilai jika kondisinya benar dan nilai lainnya jika kondisinya salah."
                ],
                INDEX: [
                    "INDEKS(array, angka_baris, [angka_kolom])",
                    "Mengembalikan nilai elemen dalam tabel atau larik, yang dipilih berdasarkan indeks nomor baris dan kolom."
                ],
                INDIRECT: [
                    "TIDAK LANGSUNG(ref_text, [a1])",
                    "Mengembalikan referensi yang ditentukan oleh string teks.Referensi segera dievaluasi untuk menampilkan isinya."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Mengembalikan TRUE jika nilainya kosong"
                ],
                LARGE: [
                    "BESAR (array, k)",
                    "Mengembalikan nilai terbesar ke-k dalam kumpulan data."
                ],
                LEFT: [
                    "KIRI(teks, [num_chars])",
                    "mengembalikan karakter pertama atau beberapa karakter dalam string teks, berdasarkan jumlah karakter yang Anda tentukan."
                ],
                LEN: [
                    "LEN(text)",
                    "mengembalikan jumlah karakter dalam string teks."
                ],
                LOOKUP: [
                    "PENCARIAN(nilai_pencarian, vektor_pencarian, [vektor_hasil])",
                    "Mencari nilai dalam rentang satu baris atau satu kolom (dikenal sebagai vektor) dan mengembalikan nilai dari posisi yang sama dalam rentang satu baris atau satu kolom kedua."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Mengonversi semua huruf besar dalam string teks menjadi huruf kecil."
                ],
                MATCH: [
                    "MATCH(nilai_pencarian, array_pencarian, [tipe_pencocokan])",
                    "Mencari item tertentu dalam rentang sel, lalu mengembalikan posisi relatif item tersebut dalam rentang tersebut.match_type dapat berupa: 0 untuk pencocokan tepat dengan opsi untuk menggunakan wildcard;1 ( default ) kurang dari, Nilai dalam argumen lookup_array harus ditempatkan dalam urutan menaik;-1 untuk lebih besar dari, nilai dalam argumen lookup_array harus ditempatkan dalam urutan menurun."
                ],
                MAX: [
                    "MAX(angka1, [angka2], ...)",
                    "Mengembalikan nilai terbesar dalam serangkaian nilai."
                ],
                MEDIAN: [
                    "MEDIAN(nomor1, [nomor2], ...)",
                    "Mengembalikan median dari angka yang diberikan.Median adalah angka yang berada di tengah-tengah sekumpulan angka."
                ],
                MID: [
                    "MID(teks, angka_mulai, angka_karakter)",
                    "mengembalikan sejumlah karakter tertentu dari string teks, dimulai dari posisi yang Anda tentukan, berdasarkan jumlah karakter yang Anda tentukan."
                ],
                MIN: [
                    "MIN(nomor1, [nomor2], ...)",
                    "Mengembalikan angka terkecil dalam sekumpulan nilai."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Mengembalikan nilai yang paling sering muncul, atau berulang, dalam larik atau rentang data."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Mengembalikan bulan dari tanggal yang diwakili oleh nomor seri.Bulan diberikan dalam bilangan bulat, mulai dari 1 (Januari) hingga 12 (Desember)."
                ],
                OR: [
                    "ATAU(logis1, [logis2], ...)",
                    "mengembalikan TRUE jika salah satu argumennya bernilai TRUE, dan mengembalikan FALSE jika semua argumennya bernilai FALSE."
                ],
                PI: [
                    "PI()",
                    "Mengembalikan angka 3.14159265358979, konstanta matematika pi."
                ],
                POWER: [
                    "KEKUATAN (angka, daya)",
                    "Mengembalikan hasil angka yang dipangkatkan."
                ],
                PRODUCT: [
                    "PRODUK(nomor1, [nomor2], ...)",
                    "mengalikan semua angka yang diberikan sebagai argumen dan mengembalikan produk.PRODUK(A1:A3, C1:C3) setara dengan =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Mengkapitalkan huruf pertama di setiap kata dari nilai teks."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Mengubah derajat menjadi radian."
                ],
                RAND: [
                    "RAND()",
                    "Mengembalikan bilangan real acak yang terdistribusi merata lebih besar dari atau sama dengan 0 dan kurang dari 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Mengembalikan peringkat nomor dalam daftar nomor."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(teks_lama, angka_mulai, angka_karakter, teks_baru)",
                    "mengganti bagian string teks, berdasarkan jumlah karakter yang Anda tentukan, dengan string teks berbeda."
                ],
                REPT: [
                    "REPT(teks, angka_kali)",
                    "Mengulangi teks beberapa kali."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "mengembalikan karakter terakhir atau beberapa karakter dalam string teks, berdasarkan jumlah karakter yang Anda tentukan."
                ],
                ROUND: [
                    "PUTARAN(angka, angka_digit)",
                    "membulatkan angka ke sejumlah digit tertentu."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(angka, angka_digit)",
                    "Membulatkan angka ke bawah, menuju nol."
                ],
                ROUNDUP: [
                    "PEMBULATAN(angka, angka_digit)",
                    "Membulatkan angka ke atas, menjauhi 0 (nol)."
                ],
                ROW: [
                    "ROW()",
                    "Mengembalikan referensi sel tempat fungsi ROW muncul."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Mengembalikan jumlah baris dalam referensi atau larik."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "temukan satu string teks dalam string teks kedua, dan kembalikan nomor posisi awal string teks pertama dari karakter pertama string teks kedua."
                ],
                SIN: [
                    "SIN(number)",
                    "Mengembalikan sinus dari sudut tertentu (dalam radian)."
                ],
                SMALL: [
                    "KECIL(array, k)",
                    "Mengembalikan nilai terkecil ke-k dalam kumpulan data."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Mengembalikan akar kuadrat positif."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Memperkirakan deviasi standar berdasarkan sampel.Standar deviasi merupakan ukuran seberapa jauh nilai-nilai tersebar dari nilai rata-rata (mean)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Menghitung deviasi standar berdasarkan seluruh populasi yang diberikan sebagai argumen."
                ],
                SUBSTITUTE: [
                    "PENGGANTI(teks, teks_lama, teks_baru, [angka_instance])",
                    "Mengganti teks_baru dengan teks_lama dalam string teks."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Menambahkan argumennya.Anda dapat menambahkan nilai individual, referensi sel atau rentang, atau campuran ketiganya."
                ],
                SUMIF: [
                    "SUMIF(rentang, kriteria, [jumlah_rentang])",
                    "menambahkan nilai dalam rentang yang memenuhi kriteria yang Anda tentukan"
                ],
                SUMIFS: [
                    "SUMIFS(rentang_jumlah, rentang_kriteria1, kriteria1, [rentang_kriteria2, kriteria2], ...)",
                    "menambahkan semua argumennya yang memenuhi berbagai kriteria."
                ],
                SUMPRODUCT: [
                    "SUMPRODUK(array1, [array2], [array3], ...)",
                    "Mengalikan komponen yang sesuai dalam array tertentu, dan mengembalikan jumlah produk tersebut."
                ],
                TAN: [
                    "TAN(number)",
                    "Mengembalikan garis singgung sudut tertentu (dalam radian)."
                ],
                TEXT: [
                    "TEXT(Nilai yang ingin Anda format, \"Kode format yang ingin Anda terapkan\")",
                    "Fungsi TEXT memungkinkan Anda mengubah tampilan angka dengan menerapkan pemformatan menggunakan kode format."
                ],
                TIME: [
                    "WAKTU (jam, menit, detik)",
                    "Mengembalikan angka desimal untuk waktu tertentu."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Mengembalikan angka desimal waktu yang diwakili oleh string teks.Angka desimal adalah nilai yang berkisar dari 0 (nol) hingga 0,99988426, mewakili waktu dari 0:00:00 (12:00:00) hingga 23:59:59 (23:59:59)."
                ],
                TODAY: [
                    "TODAY()",
                    "Mengembalikan nomor seri tanggal sekarang."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Menghapus semua spasi dari teks kecuali satu spasi antar kata."
                ],
                TRUNC: [
                    "TRUNC(angka, [angka_digit])",
                    "Memotong suatu bilangan menjadi bilangan bulat dengan menghilangkan bagian pecahan dari bilangan tersebut."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Mengonversi teks menjadi huruf besar."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Mengonversi string teks yang mewakili angka menjadi angka."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Memperkirakan varians berdasarkan sampel."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Menghitung varians berdasarkan seluruh populasi."
                ],
                VLOOKUP: [
                    "VLOOKUP (nilai_pencarian, array_tabel, col_index_num, [range_lookup])",
                    "mencari nilai dalam tabel atau rentang demi baris.Misalnya, cari harga suku cadang otomotif berdasarkan nomor suku cadangnya."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Mengembalikan tahun yang sesuai dengan tanggal.Tahun dikembalikan sebagai bilangan bulat dalam kisaran 1900-9999."
                ]
            },
            strFreezeFirstCol: "Bekukan kolom pertama",
            strFreezePanes: "Bekukan panel",
            strFreezePanesUndo: "Cairkan panel",
            strFreezeTopRow: "Bekukan baris pertama",
            strFrozenCols: "Kolom Beku",
            strFrozenRows: "Baris Beku",
            strFullScreen: "Layar Penuh",
            strGroup_fixCols: "Perbaiki kolom",
            strGroup_grandSummary: "Ringkasan besar",
            strGroup_header: "Letakkan kelompok demi kolom",
            strGroup_merge: "Gabungkan sel",
            strHideCols: "Sembunyikan Kolom",
            strHideRows: "Sembunyikan Baris",
            strImport: "Impor data",
            strImportPic: "Sisipkan gambar mengambang",
            strImportPicCell: "Sisipkan gambar ke dalam sel",
            strInsertColumn: "Sisipkan Kolom",
            strInsertRow: "Sisipkan Baris",
            strInsertRows: "Sisipkan {0} Baris",
            strItalic: "Teks miring",
            strLabel: "Label",
            strLink: "Tautan",
            strLoading: "Memuat",
            strLocal: "id",
            strLockCells: "Kunci sel",
            strMenu: {
                export: "Kolom yang dapat diekspor",
                filter: "Menyaring",
                hideCols: "Kolom yang terlihat"
            },
            strMerge: "Gabungkan sel",
            strName: "Nama",
            strNextResult: "Hasil Selanjutnya",
            strNoRows: "Tidak ada baris untuk ditampilkan.",
            strNothingFound: "Tidak ada yang ditemukan",
            strOR: "ATAU",
            strOk: "Oke",
            strOpen: "Buka",
            strPaste: "Tempel",
            strPrevResult: "Hasil Sebelumnya",
            strRedo: "Ulangi",
            strRename: "Ganti nama",
            strSearch: "Cari",
            strSelectAll: "Pilih Semua",
            strSelectedmatches: "{0} dari {1} kecocokan yang dipilih",
            strShowCols: "Tampilkan Kolom",
            strShowRows: "Tampilkan Baris",
            strTP_aggPH: "Jatuhkan kolom untuk agregat",
            strTP_aggPane: "Agregat",
            strTP_colPH: "Jatuhkan kolom untuk pengelompokan kolom",
            strTP_colPane: "Kolom grup",
            strTP_pivot: "Modus berputar",
            strTP_rowPH: "Jatuhkan kolom untuk pengelompokan baris",
            strTP_rowPane: "Baris kelompok",
            strTabAdd: "Lembar baru",
            strTabClose: "Hapus lembar",
            strTabHide: "Sembunyikan lembar",
            strTabName: "sheet{0}",
            strTabRemove: "{0} akan dihapus secara permanen.\r\nApa kamu yakin?",
            strTabRename: "Ganti nama lembar",
            strTabShow: "Tampilkan lembar",
            strTextColor: "Warna Teks",
            strUnderline: "Garis bawahi teks",
            strUndo: "Membatalkan",
            strUnhide: "Perlihatkan",
            strUnmerge: "Pisahkan sel",
            strUpdate: "Pembaruan",
            strWrap: "Bungkus Teks"
        },
        pager = pq.pqPager.regional.id = {
            strDisplay: "Menampilkan {0} hingga {1} dari {2} item.",
            strFirstPage: "Halaman Pertama",
            strLastPage: "Halaman Terakhir",
            strNextPage: "Halaman Berikutnya",
            strPage: "Halaman {0} dari {1}",
            strPrevPage: "Halaman Sebelumnya",
            strRefresh: "Segarkan",
            strRpp: "Catatan per halaman: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);