import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.ja = {
            strAND: "そして",
            strAddColLeft: "左に列を追加",
            strAddColRight: "列を右に追加",
            strAddColumn: "列の追加",
            strAddRow: "行を追加",
            strAddRowAbove: "上に行を追加",
            strAddRowBelow: "下に行を追加",
            strAddRows: "{0} 行を追加",
            strAlign: {
                bottom: "下揃え",
                center: "中央揃え",
                left: "左揃え",
                right: "右揃え",
                top: "上揃え"
            },
            strAlignH: "水平方向の配置",
            strAlignV: "垂直方向の配置",
            strApply: "申し込む",
            strBlanks: "(空白)",
            strBold: "太字のテキスト",
            strBorder: "境界線",
            strBorderColor: "枠線の色",
            strBorderStyle: "ボーダースタイル",
            strBorders: {
                all: "すべての国境",
                bottom: "下枠",
                horizontal: "水平方向の境界線",
                inner: "内側の境界線",
                left: "左枠",
                none: "境界線なし",
                outer: "外枠",
                right: "右枠線",
                top: "上枠",
                vertical: "垂直境界線"
            },
            strCancel: "キャンセル",
            strClear: "クリア",
            strClearColor: "クリアカラー",
            strClearFilter: "クリアフィルター",
            strClearText: "クリアテキスト",
            strCollapse: "パネルを折りたたむ",
            strComment: "コメント",
            strCondition: "Condition:",
            strConditions: {
                "": "-- なし --",
                begin: "から始まる",
                between: "その間",
                contain: "含まれています",
                empty: "空の",
                end: "で終わる",
                equal: "等しい",
                great: "より大きい",
                gte: "以上",
                less: "以下",
                lte: "以下",
                notbegin: "で始まらない",
                notcontain: "含まれていない",
                notempty: "空ではありません",
                notend: "で終わらない",
                notequal: "等しくない",
                range: "【範囲】",
                regexp: "正規表現"
            },
            strCopy: "コピー",
            strCut: "カット",
            strDelete: "削除",
            strDeleteColumn: "列の削除",
            strDeleteRow: "行の削除",
            strDownloadXlsx: "Excelファイルをダウンロード",
            strEdit: "編集",
            strExitFullScreen: "全画面表示を終了する",
            strExpand: "パネルを展開する",
            strExport: "データのエクスポート",
            strFillColor: "塗りつぶしの色",
            strFontFamily: "フォントファミリー",
            strFontSize: "フォントサイズ",
            strFormat: "セルの書式設定",
            strFormatMenu: {
                Accounting: "会計",
                Currency: "通貨",
                Custom: "カスタム",
                DateTime: "日付時刻",
                Fixed: "10進数",
                Fraction: "分数",
                FullDate: "完全な日付",
                General: "一般",
                Int: "整数",
                LongDate: "ロングデート",
                MediumDate: "中期日付",
                Note: "注: * の付いた形式はユーザーのロケールに応じて変わります。",
                Percent: "パーセンテージ",
                Scientific: "科学的",
                ShortDate: "ショートデート",
                Standard: "標準",
                Text: "テキスト",
                Time: "時間"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "数値の絶対値を返します。数値の絶対値は、符号のない数値です。"
                ],
                ACOS: [
                    "ACOS(number)",
                    "数値の逆余弦、または逆余弦を返します。逆余弦は、余弦が数値である角度です。返される角度は、0 (ゼロ) から pi までの範囲のラジアンで与えられます。"
                ],
                AND: [
                    "AND(論理1, [論理2], ...)",
                    "すべての引数が TRUE と評価される場合は TRUE を返し、1 つ以上の引数が FALSE と評価される場合は FALSE を返します。"
                ],
                ASIN: [
                    "ASIN(number)",
                    "数値の逆正弦、または逆正弦を返します。逆正弦は、正弦が数値である角度です。返される角度は、-pi/2 ～ pi/2 の範囲のラジアンで与えられます。"
                ],
                ATAN: [
                    "ATAN(number)",
                    "数値の逆正接、または逆正接を返します。逆正接は、正接が数値である角度です。返される角度は、-pi/2 ～ pi/2 の範囲のラジアンで与えられます。"
                ],
                AVERAGE: [
                    "AVERAGE(数値1, [数値2], ...)",
                    "引数の平均 (算術平均) を返します。たとえば、範囲 A1:A20 に数値が含まれている場合、数式 =AVERAGE(A1:A20) はそれらの数値の平均を返します。"
                ],
                AVERAGEIF: [
                    "AVERAGEIF(範囲, 基準, [平均範囲])",
                    "指定された条件を満たす範囲内のすべてのセルの平均 (算術平均) を返します。"
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(平均範囲, 基準範囲1, 基準1, [基準範囲2, 基準2], ...)",
                    "複数の基準を満たすすべてのセルの平均 (算術平均) を返します。"
                ],
                CEILING: [
                    "CEILING(数値、重要度)",
                    "ゼロから離れて最も近い有効値の倍数に切り上げられた数値を返します。たとえば、価格にペニーを使用したくない場合、製品の価格が $4.42 である場合は、式 =CEILING(4.42,0.05) を使用して、価格を最も近いニッケル単位に切り上げます。"
                ],
                CHAR: [
                    "CHAR(number)",
                    "数値で指定された文字を返します。"
                ],
                CHOOSE: [
                    "CHOOSE(インデックス番号, 値1, [値2], ...)",
                    "Index_num が 1 の場合、CHOOSE は value1 を返します。2 の場合、CHOOSE は value2 を返します。等々。"
                ],
                CODE: [
                    "CODE(text)",
                    "テキスト文字列の最初の文字の数値コードを返します。"
                ],
                COLUMN: [
                    "COLUMN()",
                    "COLUMN 関数が出現するセルの参照を返します。"
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "配列または参照内の列の数を返します。"
                ],
                CONCATENATE: [
                    "CONCATENATE(テキスト1, [テキスト2], ...)",
                    "2 つ以上のテキスト文字列を 1 つの文字列に結合します。"
                ],
                COS: [
                    "COS(number)",
                    "指定された角度のコサイン (ラジアン単位) を返します。"
                ],
                COUNT: [
                    "COUNT(値1, [値2], ...)",
                    "数値を含むセルの数を数え、引数のリスト内の数値を数えます。"
                ],
                COUNTA: [
                    "COUNTA(値1, [値2], ...)",
                    "COUNTA 関数は、範囲内の空ではないセルの数をカウントします。"
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "指定されたセル範囲内の空のセルをカウントします。"
                ],
                COUNTIF: [
                    "COUNTIF(範囲、基準)",
                    "基準を満たすセルの数を数えます。たとえば、特定の都市が顧客リストに表示される回数を数えます。"
                ],
                COUNTIFS: [
                    "COUNTIFS(基準範囲1, 基準1, [基準範囲2, 基準2]…)",
                    "複数の範囲にわたるセルに条件を適用し、すべての条件が満たされた回数をカウントします。"
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "特定の日付を表す連続したシリアル番号を返します。"
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "2 つの日付の間の日数、月数、または年数を計算します。単位は「Y」、「M」、または「D」です。"
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "テキストとして保存されている日付を、Excel が日付として認識するシリアル番号に変換します。たとえば、数式 =DATEVALUE(\"1/1/2008\") は 39448 を返します。"
                ],
                DAY: [
                    "DAY(serial_number)",
                    "シリアル番号で表される日付の日を返します。"
                ],
                DAYS: [
                    "DAYS(終了日, 開始日)",
                    "2 つの日付間の日数を返します。"
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "ラジアンを度に変換します。"
                ],
                EOMONTH: [
                    "EOMONTH(開始日, 月)",
                    "start_date の前後、指定された月数の月の最終日のシリアル番号を返します。"
                ],
                EXP: [
                    "EXP(number)",
                    "e の数値乗を返します。"
                ],
                FIND: [
                    "FIND(検索テキスト, テキスト内, [開始番号])",
                    "2 番目のテキスト文字列内の 1 つのテキスト文字列を検索し、2 番目のテキスト文字列の最初の文字から最初のテキスト文字列の開始位置の番号を返します。"
                ],
                FLOOR: [
                    "FLOOR(数値、重要度)",
                    "数値をゼロに向けて最も近い有効値の倍数に切り捨てます。"
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "テーブルまたは値の配列の一番上の行で値を検索し、テーブルまたは配列で指定した行から同じ列の値を返します。"
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "時刻値の時間を返します。時間は、0 (午前 12:00) から 23 (午後 11:00) の範囲の整数で指定されます。"
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [フレンドリー名])",
                    "セルをクリックするとインターネット上の別の場所にジャンプするショートカットを作成します"
                ],
                IF: [
                    "IF(論理テスト, 真の場合の値, [偽の場合の値])",
                    "条件が true の場合は 1 つの値を返し、条件が false の場合は別の値を返します。"
                ],
                INDEX: [
                    "INDEX(配列, row_num, [column_num])",
                    "行番号と列番号のインデックスによって選択された、テーブルまたは配列内の要素の値を返します。"
                ],
                INDIRECT: [
                    "INDIRECT(ref_text, [a1])",
                    "テキスト文字列で指定された参照を返します。参照はすぐに評価されてその内容が表示されます。"
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "値が空白の場合は TRUE を返します"
                ],
                LARGE: [
                    "LARGE(配列, k)",
                    "データセット内の k 番目に大きい値を返します。"
                ],
                LEFT: [
                    "LEFT(テキスト, [num_chars])",
                    "指定した文字数に基づいて、テキスト文字列の最初の文字を返します。"
                ],
                LEN: [
                    "LEN(text)",
                    "テキスト文字列内の文字数を返します。"
                ],
                LOOKUP: [
                    "LOOKUP(ルックアップ値, ルックアップベクトル, [結果ベクトル])",
                    "1 行または 1 列の範囲 (ベクトルと呼ばれる) で値を検索し、2 番目の 1 行または 1 列の範囲内の同じ位置から値を返します。"
                ],
                LOWER: [
                    "LOWER(text)",
                    "テキスト文字列内のすべての大文字を小文字に変換します。"
                ],
                MATCH: [
                    "MATCH(lookup_value, lookup_array, [match_type])",
                    "セル範囲内で指定された項目を検索し、範囲内のその項目の相対位置を返します。match_type は次のとおりです。ワイルドカードを使用するオプションを使用して完全に一致する場合は 0。1 (デフォルト) より小さい場合。lookup_array 引数の値は昇順に配置する必要があります。より大きい場合は -1。lookup_array 引数の値は降順に配置する必要があります。"
                ],
                MAX: [
                    "MAX(数値1, [数値2], ...)",
                    "値のセット内の最大値を返します。"
                ],
                MEDIAN: [
                    "MEDIAN(数値1, [数値2], ...)",
                    "指定された数値の中央値を返します。中央値は、一連の数値の中央にある数値です。"
                ],
                MID: [
                    "MID(テキスト、開始番号、文字数)",
                    "指定した文字数に基づいて、指定した位置から始まるテキスト文字列から特定の数の文字を返します。"
                ],
                MIN: [
                    "MIN(数値1, [数値2], ...)",
                    "値のセット内の最小の数値を返します。"
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "配列またはデータ範囲内で最も頻繁に発生する値、または繰り返し発生する値を返します。"
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "シリアル番号で表される日付の月を返します。月は、1 (1 月) から 12 (12 月) までの範囲の整数で指定されます。"
                ],
                OR: [
                    "OR(論理1, [論理2], ...)",
                    "いずれかの引数が TRUE と評価される場合は TRUE を返し、すべての引数が FALSE と評価される場合は FALSE を返します。"
                ],
                PI: [
                    "PI()",
                    "数値 3.14159265358979、数学定数 pi を返します。"
                ],
                POWER: [
                    "POWER(数値、べき乗)",
                    "数値をべき乗した結果を返します。"
                ],
                PRODUCT: [
                    "PRODUCT(数値1, [数値2], ...)",
                    "引数として指定されたすべての数値を乗算し、積を返します。PRODUCT(A1:A3, C1:C3) は =A1 * A2 * A3 * C1 * C2 * C3 と同等です。"
                ],
                PROPER: [
                    "PROPER(text)",
                    "テキスト値の各単語の最初の文字を大文字にします。"
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "度をラジアンに変換します。"
                ],
                RAND: [
                    "RAND()",
                    "0 以上 1 未満の均等に分散されたランダムな実数を返します。"
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "数値のリスト内の数値の順位を返します。"
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(old_text, start_num, num_chars, new_text)",
                    "指定した文字数に基づいて、テキスト文字列の一部を別のテキスト文字列に置き換えます。"
                ],
                REPT: [
                    "REPT(テキスト,number_times)",
                    "テキストを指定された回数繰り返します。"
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "指定した文字数に基づいて、テキスト文字列の最後の文字を返します。"
                ],
                ROUND: [
                    "ROUND(数値, 桁数)",
                    "数値を指定された桁数に丸めます。"
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(数値, 桁数)",
                    "数値をゼロに向かって切り捨てます。"
                ],
                ROUNDUP: [
                    "ROUNDUP(数値, 桁数)",
                    "数値を 0 (ゼロ) から離れる方向に切り上げます。"
                ],
                ROW: [
                    "ROW()",
                    "ROW 関数が出現するセルの参照を返します。"
                ],
                ROWS: [
                    "ROWS(array)",
                    "参照または配列内の行数を返します。"
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "2 番目のテキスト文字列内の 1 つのテキスト文字列を検索し、2 番目のテキスト文字列の最初の文字から最初のテキスト文字列の開始位置の番号を返します。"
                ],
                SIN: [
                    "SIN(number)",
                    "指定された角度のサインを(ラジアン単位で)返します。"
                ],
                SMALL: [
                    "SMALL(配列, k)",
                    "データセット内の k 番目に小さい値を返します。"
                ],
                SQRT: [
                    "SQRT(number)",
                    "正の平方根を返します。"
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "サンプルに基づいて標準偏差を推定します。標準偏差は、値が平均値 (平均) からどの程度広く分散しているかを示す尺度です。"
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "引数として指定された母集団全体に基づいて標準偏差を計算します。"
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(テキスト, 古いテキスト, 新しいテキスト, [インスタンス番号])",
                    "テキスト文字列内の old_text を new_text に置き換えます。"
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "引数を追加します。個々の値、セル参照または範囲、または 3 つすべてを組み合わせて追加できます。"
                ],
                SUMIF: [
                    "SUMIF(範囲, 基準, [合計範囲])",
                    "指定した基準を満たす範囲の値を追加します"
                ],
                SUMIFS: [
                    "SUMIFS(合計範囲, 基準範囲1, 基準1, [基準範囲2, 基準2], ...)",
                    "複数の基準を満たすすべての引数を追加します。"
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(配列1, [配列2], [配列3], ...)",
                    "指定された配列内の対応するコンポーネントを乗算し、それらの積の合計を返します。"
                ],
                TAN: [
                    "TAN(number)",
                    "指定された角度のタンジェント (ラジアン単位) を返します。"
                ],
                TEXT: [
                    "TEXT(書式設定したい値、「適用したい書式コード」)",
                    "TEXT 関数を使用すると、書式コードを使用して数値に書式設定を適用することで、数値の表示方法を変更できます。"
                ],
                TIME: [
                    "TIME(時、分、秒)",
                    "特定の時刻の 10 進数を返します。"
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "テキスト文字列で表される時刻を 10 進数で返します。10 進数は 0 (ゼロ) から 0.99988426 の範囲の値で、0:00:00 (午前 12:00:00) から 23:59:59 (午後 11:59:59) までの時間を表します。"
                ],
                TODAY: [
                    "TODAY()",
                    "現在の日付のシリアル番号を返します。"
                ],
                TRIM: [
                    "TRIM(text)",
                    "単語間の 1 つのスペースを除いて、テキストからすべてのスペースを削除します。"
                ],
                TRUNC: [
                    "TRUNC(数字, [桁数])",
                    "数値の小数部分を削除して、数値を整数に切り捨てます。"
                ],
                UPPER: [
                    "UPPER(text)",
                    "テキストを大文字に変換します。"
                ],
                VALUE: [
                    "VALUE(text)",
                    "数値を表すテキスト文字列を数値に変換します。"
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "サンプルに基づいて分散を推定します。"
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "母集団全体に基づいて分散を計算します。"
                ],
                VLOOKUP: [
                    "VLOOKUP (ルックアップ値、テーブル配列、列インデックス番号、[ルックアップ範囲])",
                    "テーブル内の値または行ごとの範囲を検索します。たとえば、自動車部品の価格を部品番号で検索します。"
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "日付に対応する年を返します。年は 1900 ～ 9999 の範囲の整数として返されます。"
                ]
            },
            strFreezeFirstCol: "最初の列を固定する",
            strFreezePanes: "ペインをフリーズする",
            strFreezePanesUndo: "ペインのフリーズを解除する",
            strFreezeTopRow: "最初の行を固定する",
            strFrozenCols: "凍結カラム",
            strFrozenRows: "凍結された行",
            strFullScreen: "全画面表示",
            strGroup_fixCols: "列を修正する",
            strGroup_grandSummary: "総括",
            strGroup_header: "列ごとにグループをドロップ",
            strGroup_merge: "セルを結合する",
            strHideCols: "列を非表示にする",
            strHideRows: "行を非表示にする",
            strImport: "データのインポート",
            strImportPic: "フローティング画像を挿入",
            strImportPicCell: "セルに画像を挿入",
            strInsertColumn: "列の挿入",
            strInsertRow: "行の挿入",
            strInsertRows: "{0} 行を挿入",
            strItalic: "斜体のテキスト",
            strLabel: "ラベル",
            strLink: "リンク",
            strLoading: "読み込み中",
            strLocal: "ja",
            strLockCells: "セルをロックする",
            strMenu: {
                export: "エクスポート可能な列",
                filter: "フィルター",
                hideCols: "表示される列"
            },
            strMerge: "セルを結合する",
            strName: "名前",
            strNextResult: "次の結果",
            strNoRows: "表示する行がありません。",
            strNothingFound: "見つかりませんでした。",
            strOR: "または",
            strOk: "わかりました",
            strOpen: "開く",
            strPaste: "ペースト",
            strPrevResult: "前の結果",
            strRedo: "やり直し",
            strRename: "名前の変更",
            strSearch: "検索",
            strSelectAll: "すべて選択",
            strSelectedmatches: "{1} 件中 {0} 件の一致を選択しました",
            strShowCols: "列を表示",
            strShowRows: "行を表示",
            strTP_aggPH: "集計の列を削除する",
            strTP_aggPane: "集合体",
            strTP_colPH: "列をグループ化するために列を削除します",
            strTP_colPane: "グループ列",
            strTP_pivot: "ピボットモード",
            strTP_rowPH: "列を削除して行をグループ化する",
            strTP_rowPane: "グループ行",
            strTabAdd: "新しいシート",
            strTabClose: "シートを取り除く",
            strTabHide: "シートを非表示にする",
            strTabName: "sheet{0}",
            strTabRemove: "{0} は完全に削除されます。\r\n本気ですか？",
            strTabRename: "シートの名前を変更",
            strTabShow: "シートを表示",
            strTextColor: "文字の色",
            strUnderline: "テキストに下線を引く",
            strUndo: "元に戻す",
            strUnhide: "再表示",
            strUnmerge: "セルの結合を解除する",
            strUpdate: "アップデート",
            strWrap: "テキストの折り返し"
        },
        pager = pq.pqPager.regional.ja = {
            strDisplay: " {2} 項目中 {0} から {1} を 表示",
            strFirstPage: "最初",
            strLastPage: "最後",
            strNextPage: "次",
            strPage: "{0} / {1}  ページ",
            strPrevPage: "前",
            strRefresh: "リフレッシュ",
            strRpp: "１ページあたりのレコード: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);