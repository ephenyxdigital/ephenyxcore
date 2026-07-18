(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.zh_tw = {
            strAND: "和",
            strAddColLeft: "新增左側列",
            strAddColRight: "新增右側列",
            strAddColumn: "新增列",
            strAddRow: "新增行",
            strAddRowAbove: "在上方加入行",
            strAddRowBelow: "加入下面的行",
            strAddRows: "新增 {0} 行",
            strAlign: {
                bottom: "底部對齊",
                center: "居中對齊",
                left: "左對齊",
                right: "右對齊",
                top: "頂部對齊"
            },
            strAlignH: "水平對齊",
            strAlignV: "垂直對齊",
            strApply: "申請",
            strBlanks: "（空白）",
            strBold: "粗體文字",
            strBorder: "邊框",
            strBorderColor: "邊框顏色",
            strBorderStyle: "邊框樣式",
            strBorders: {
                all: "所有邊框",
                bottom: "下邊框",
                horizontal: "水平邊框",
                inner: "內邊框",
                left: "左邊框",
                none: "無國界",
                outer: "外邊框",
                right: "右邊框",
                top: "頂部邊框",
                vertical: "垂直邊框"
            },
            strCancel: "取消",
            strClear: "清除",
            strClearColor: "色彩清晰",
            strClearFilter: "清除過濾器",
            strClearText: "清晰的文本",
            strCollapse: "折疊面板",
            strComment: "評論",
            strCondition: "Condition:",
            strConditions: {
                "": "-- 無 --",
                begin: "開始於",
                between: "介於兩者之間",
                contain: "包含",
                empty: "空",
                end: "結束於",
                equal: "等於",
                great: "大於",
                gte: "大於或等於",
                less: "小於",
                lte: "小於或等於",
                notbegin: "不開始於",
                notcontain: "不包含",
                notempty: "不為空",
                notend: "不以以下結尾",
                notequal: "不等於",
                range: "[ 範圍 ]",
                regexp: "正規表示式"
            },
            strCopy: "複製",
            strCut: "切",
            strDelete: "刪除",
            strDeleteColumn: "刪除列",
            strDeleteRow: "刪除行",
            strDownloadXlsx: "下載Excel文件",
            strEdit: "編輯",
            strExitFullScreen: "退出全螢幕",
            strExpand: "展開面板",
            strExport: "匯出數據",
            strFillColor: "填滿顏色",
            strFontFamily: "字體家族",
            strFontSize: "字體大小",
            strFormat: "設定單元格格式",
            strFormatMenu: {
                Accounting: "會計",
                Currency: "貨幣",
                Custom: "客製化",
                DateTime: "日期時間",
                Fixed: "十進位",
                Fraction: "分數",
                FullDate: "完整日期",
                General: "一般",
                Int: "整數",
                LongDate: "長日期",
                MediumDate: "中日期",
                Note: "注意：帶有 * 的格式會根據使用者區域設定而變化。",
                Percent: "百分比",
                Scientific: "科學的",
                ShortDate: "短日期",
                Standard: "標準型",
                Text: "文字",
                Time: "時間"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "傳回數字的絕對值。數字的絕對值是不帶符號的數字。"
                ],
                ACOS: [
                    "ACOS(number)",
                    "傳回數字的反餘弦或反餘弦。反餘弦是餘弦為數字的角度。傳回的角度以弧度為單位，範圍為 0（零）到 pi。"
                ],
                AND: [
                    "AND(邏輯 1, [邏輯 2], ...)",
                    "如果所有參數的計算結果都為 TRUE，則傳回 TRUE；如果一個或多個參數的計算結果為 FALSE，則傳回 FALSE。"
                ],
                ASIN: [
                    "ASIN(number)",
                    "傳回數字的反正弦或反正弦。反正弦是正弦為數字的角度。傳回的角度以弧度給出，範圍為 -pi/2 到 pi/2。"
                ],
                ATAN: [
                    "ATAN(number)",
                    "傳回數字的反正切或反正切。反正切是正切為數字的角度。傳回的角度以弧度給出，範圍為 -pi/2 到 pi/2。"
                ],
                AVERAGE: [
                    "平均數（數字 1，[數字 2]，...）",
                    "傳回參數的平均值（算術平均值）。例如，如果範圍 A1:A20 包含數字，則公式 =AVERAGE(A1:A20) 傳回這些數字的平均值。"
                ],
                AVERAGEIF: [
                    "AVERAGEIF（範圍，標準，[平均範圍]）",
                    "傳回滿足給定條件的範圍內所有單元格的平均值（算術平均值）。"
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(平均範圍, 標準範圍1, 標準1, [標準範圍2, 標準2], ...)",
                    "傳回滿足多個條件的所有儲存格的平均值（算術平均值）。"
                ],
                CEILING: [
                    "CEILING(數字，意義)",
                    "傳回從零向上舍入到最接近的有效倍數的數字。例如，如果您想避免在價格中使用便士，並且您的產品定價為 4.42 美元，請使用公式 =CEILING(4.42,0.05) 將價格四捨五入到最接近的鎳幣。"
                ],
                CHAR: [
                    "CHAR(number)",
                    "傳回由數字指定的字元。"
                ],
                CHOOSE: [
                    "選擇（index_num，值1，[值2]，...）",
                    "如果index_num為1，則CHOOSE返回value1；如果是2，則CHOOSE返回value2；等等。"
                ],
                CODE: [
                    "CODE(text)",
                    "傳回文字字串中第一個字元的數字代碼。"
                ],
                COLUMN: [
                    "COLUMN()",
                    "傳回出現 COLUMN 函數的儲存格的參考。"
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "傳回數組或引用中的列數。"
                ],
                CONCATENATE: [
                    "連接（文本1，[文本2]，...）",
                    "將兩個或多個文字字串連接成一個字串。"
                ],
                COS: [
                    "COS(number)",
                    "傳回給定角度的餘弦（以弧度為單位）。"
                ],
                COUNT: [
                    "計數（值 1，[值 2]，...）",
                    "計算包含數字的儲存格數量，並計算參數清單中的數字。"
                ],
                COUNTA: [
                    "COUNTA(值 1, [值 2], ...)",
                    "COUNTA 函數計算區域中非空單元格的數量。"
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "計算指定儲存格範圍內的空白儲存格數量。"
                ],
                COUNTIF: [
                    "COUNTIF（範圍，條件）",
                    "計算符合標準的細胞數量；例如，計算特定城市出現在客戶名單中的次數。"
                ],
                COUNTIFS: [
                    "COUNTIFS(criteria_range1, criteria1, [criteria_range2, criteria2]...)",
                    "將條件套用於多個範圍的儲存格，並計算滿足所有條件的次數。"
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "傳回代表特定日期的連續序號。"
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "計算兩個日期之間的天數、月數或年數。單位可以是“Y”、“M”或“D”。"
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "將儲存為文字的日期轉換為 Excel 識別為日期的序號。例如，公式 =DATEVALUE(\"1/1/2008\") 傳回 39448。"
                ],
                DAY: [
                    "DAY(serial_number)",
                    "傳回日期中的天數，以序號表示。"
                ],
                DAYS: [
                    "天（結束日期，開始日期）",
                    "傳回兩個日期之間的天數。"
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "將弧度轉換為度數。"
                ],
                EOMONTH: [
                    "EOMONTH（開始日期，月份）",
                    "傳回指定的 start_date 之前或之後月份數的月份最後一天的序號。"
                ],
                EXP: [
                    "EXP(number)",
                    "傳回 e 的數字次方。"
                ],
                FIND: [
                    "FIND（尋找文本，文本內，[開始編號]）",
                    "在第二個文字字串中定位一個文字字串，並傳回從第二個文字字串的第一個字元到第一個文字字串的起始位置的編號。"
                ],
                FLOOR: [
                    "FLOOR（數字，意義）",
                    "將數字向下舍入到零，直到最接近的顯著性倍數。"
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "在表或值數組的頂行中搜尋值，然後傳回表或數組中指定行的同一列中的值。"
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "傳回時間值的小時。小時以整數形式給出，範圍從 0 (12:00 A.M.) 到 23 (11:00 P.M.)。"
                ],
                HYPERLINK: [
                    "超連結（網址，[友善名稱]）",
                    "建立一個快捷方式，當您按一下某個儲存格時，該捷徑會跳到 Internet 中的另一個位置"
                ],
                IF: [
                    "IF(邏輯測試, value_if_true, [value_if_false])",
                    "如果條件為 true，則傳回一個值；如果條件為 false，則傳回另一個值。"
                ],
                INDEX: [
                    "INDEX(數組, 行數, [列數])",
                    "傳回表格或陣列中按行號和列號索引選擇的元素的值。"
                ],
                INDIRECT: [
                    "間接（ref_text，[a1]）",
                    "傳回由文字字串指定的引用。立即評估引用以顯示其內容。"
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "如果值為空則回傳 TRUE"
                ],
                LARGE: [
                    "大（數組，k）",
                    "傳回資料集中的第 k 個最大值。"
                ],
                LEFT: [
                    "左（文本，[num_chars]）",
                    "根據您指定的字元數傳回文字字串中的第一個或多個字元。"
                ],
                LEN: [
                    "LEN(text)",
                    "傳回文字字串中的字元數。"
                ],
                LOOKUP: [
                    "LOOKUP（找出值，找出向量，[結果向量]）",
                    "在單行或一列範圍（稱為向量）中尋找值，並從第二個單行或一列範圍中的相同位置傳回值。"
                ],
                LOWER: [
                    "LOWER(text)",
                    "將文字字串中的所有大寫字母轉換為小寫。"
                ],
                MATCH: [
                    "MATCH（尋找值，尋找數組，[匹配類型]）",
                    "在儲存格區域中搜尋指定項目，然後傳回該項目在該區域中的相對位置。match_type 可以是： 0 表示精確匹配，並可選擇使用通配符； 1（預設）小於，lookup_array 參數中的值必須按升序排列； -1 表示大於，lookup_array 參數中的值必須按降序排列。"
                ],
                MAX: [
                    "MAX(數字 1, [數字 2], ...)",
                    "傳回一組值中的最大值。"
                ],
                MEDIAN: [
                    "MEDIAN(數字 1, [數字 2], ...)",
                    "傳回給定數字的中位數。中位數是一組數字中位於中間的數字。"
                ],
                MID: [
                    "MID(文字、起始編號、字元數)",
                    "根據您指定的字符數，從文字字串中返回特定數量的字符，從您指定的位置開始。"
                ],
                MIN: [
                    "MIN(數字 1, [數字 2], ...)",
                    "傳回一組值中的最小數字。"
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "傳回數組或資料範圍中最常出現或重複的值。"
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "傳回由序號表示的日期的月份。月份以整數形式給出，範圍從 1（一月）到 12（十二月）。"
                ],
                OR: [
                    "OR(邏輯 1, [邏輯 2], ...)",
                    "如果其任何參數計算結果為 TRUE，則傳回 TRUE；如果其所有參數計算結果為 FALSE，則傳回 FALSE。"
                ],
                PI: [
                    "PI()",
                    "傳回數字 3.14159265358979，即數學常數 pi。"
                ],
                POWER: [
                    "POWER(數量,功率)",
                    "傳回數字的冪結果。"
                ],
                PRODUCT: [
                    "產品（數字 1，[數字 2]，...）",
                    "將作為參數給出的所有數字相乘並返回乘積。Product(A1:A3, C1:C3) 相當於 =A1 * A2 * A3 * C1 * C2 * C3。"
                ],
                PROPER: [
                    "PROPER(text)",
                    "將文字值的每個單字的第一個字母大寫。"
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "將度數轉換為弧度。"
                ],
                RAND: [
                    "RAND()",
                    "傳回大於或等於 0 且小於 1 的均勻分佈的隨機實數。"
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "傳回數字清單中數字的排名。"
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE（舊文本，開始編號，字元數，新文本）",
                    "根據您指定的字元數，用不同的文字字串替換部分文字字串。"
                ],
                REPT: [
                    "REPT(文本, 次數)",
                    "重複文字指定次數。"
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "根據指定的字元數傳回文字字串中的最後一個或多個字元。"
                ],
                ROUND: [
                    "ROUND(數字, 數字位數)",
                    "將數字四捨五入到指定的位數。"
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN（數字，數字位數）",
                    "將數字向下舍入到零。"
                ],
                ROUNDUP: [
                    "ROUNDUP(數字, num_digits)",
                    "將數字向上舍入，遠離 0（零）。"
                ],
                ROW: [
                    "ROW()",
                    "傳回 ROW 函數所在單元格的參考。"
                ],
                ROWS: [
                    "ROWS(array)",
                    "傳回引用或數組中的行數。"
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "在第二個文字字串中定位一個文字字串，並傳回從第二個文字字串的第一個字元到第一個文字字串的起始位置的編號。"
                ],
                SIN: [
                    "SIN(number)",
                    "傳回給定角度的正弦值（以弧度為單位）。"
                ],
                SMALL: [
                    "小（數組，k）",
                    "傳回資料集中第 k 個最小值。"
                ],
                SQRT: [
                    "SQRT(number)",
                    "傳回正平方根。"
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "根據樣本估計標準差。標準差是衡量數值與平均值（平均值）的離散程度的指標。"
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "根據作為參數給出的整個總體計算標準差。"
                ],
                SUBSTITUTE: [
                    "替換（文本，舊文本，新文本，[instance_num]）",
                    "在文字字串中用 new_text 取代 old_text。"
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "新增其參數。您可以新增單一值、儲存格參考或範圍或三者的組合。"
                ],
                SUMIF: [
                    "SUMIF(範圍, 標準, [sum_range])",
                    "新增滿足您指定條件的範圍內的值"
                ],
                SUMIFS: [
                    "SUMIFS(sum_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)",
                    "新增滿足多個條件的所有參數。"
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(數組1, [數組2], [數組3], ...)",
                    "將給定數組中的相應分量相乘，並傳回這些乘積的總和。"
                ],
                TAN: [
                    "TAN(number)",
                    "傳回給定角度（以弧度為單位）的正切值。"
                ],
                TEXT: [
                    "TEXT（您要格式化的值，「格式化您要套用的程式碼」）",
                    "TEXT 函數可讓您透過使用格式代碼套用格式來變更數字的顯示方式。"
                ],
                TIME: [
                    "TIME（時、分、秒）",
                    "傳回特定時間的十進制數。"
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "傳回文字字串表示的時間的十進制數。十進制數的值範圍為 0（零）到 0.99988426，表示從 0:00:00 (12:00:00 AM) 到 23:59:59 (11:59:59 P.M.) 的時間。"
                ],
                TODAY: [
                    "TODAY()",
                    "傳回目前日期的序號。"
                ],
                TRIM: [
                    "TRIM(text)",
                    "刪除文字中的所有空格（單字之間的單一空格除外）。"
                ],
                TRUNC: [
                    "TRUNC(數字, [num_digits])",
                    "透過刪除數字的小數部分將數字截斷為整數。"
                ],
                UPPER: [
                    "UPPER(text)",
                    "將文字轉換為大寫。"
                ],
                VALUE: [
                    "VALUE(text)",
                    "將表示數字的文字字串轉換為數字。"
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "根據樣本估計變異數。"
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "基於整個總體計算變異數。"
                ],
                VLOOKUP: [
                    "VLOOKUP（查找值，表數組，列索引號，[範圍查找]）",
                    "按行查找表或範圍中的值。例如，透過零件編號找出汽車零件的價格。"
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "傳回與日期對應的年份。年份以 1900-9999 範圍內的整數形式回傳。"
                ]
            },
            strFreezeFirstCol: "凍結第一列",
            strFreezePanes: "凍結窗格",
            strFreezePanesUndo: "解凍窗格",
            strFreezeTopRow: "凍結第一行",
            strFrozenCols: "冷凍柱",
            strFrozenRows: "凍結行",
            strFullScreen: "全螢幕",
            strGroup_fixCols: "固定列",
            strGroup_grandSummary: "大總結",
            strGroup_header: "按列刪除群組",
            strGroup_merge: "合併儲存格",
            strHideCols: "隱藏列",
            strHideRows: "隱藏行",
            strImport: "導入數據",
            strImportPic: "插入浮動影像",
            strImportPicCell: "在單元格中插入影像",
            strInsertColumn: "插入列",
            strInsertRow: "插入行",
            strInsertRows: "插入 {0} 行",
            strItalic: "斜體文本",
            strLabel: "標籤",
            strLink: "連結",
            strLoading: "載入中",
            strLocal: "zh_tw",
            strLockCells: "鎖定細胞",
            strMenu: {
                export: "可匯出的列",
                filter: "過濾器",
                hideCols: "可見列"
            },
            strMerge: "合併儲存格",
            strName: "名稱",
            strNextResult: "下一筆",
            strNoRows: "沒有可以顯示的結果",
            strNothingFound: "沒有符合的結果",
            strOR: "或",
            strOk: "好的",
            strOpen: "打開",
            strPaste: "貼上",
            strPrevResult: "上一筆",
            strRedo: "重做",
            strRename: "重新命名",
            strSearch: "搜尋",
            strSelectAll: "選擇全部",
            strSelectedmatches: "從 {1} 符合的結果中選擇了 {0} 項",
            strShowCols: "顯示欄目",
            strShowRows: "顯示行",
            strTP_aggPH: "刪除聚合列",
            strTP_aggPane: "骨材",
            strTP_colPH: "刪除列以進行列分組",
            strTP_colPane: "分組列",
            strTP_pivot: "旋轉模式",
            strTP_rowPH: "刪除行分組的列",
            strTP_rowPane: "分組行",
            strTabAdd: "新表",
            strTabClose: "移除片材",
            strTabHide: "隱藏表",
            strTabName: "sheet{0}",
            strTabRemove: "{0} 將永久刪除。\r\n你確定嗎？",
            strTabRename: "重新命名工作表",
            strTabShow: "顯示表",
            strTextColor: "文字顏色",
            strUnderline: "底線文字",
            strUndo: "撤銷",
            strUnhide: "取消隱藏",
            strUnmerge: "取消合併儲存格",
            strUpdate: "更新",
            strWrap: "文字換行"
        },
        pager = pq.pqPager.regional.zh_tw = {
            strDisplay: "顯示 {0} 到 {1} 幾個項目 (共 {2} 個)",
            strFirstPage: "第一頁",
            strLastPage: "最後一頁",
            strNextPage: "下一頁",
            strPage: "第 {0} 頁（共 {1} 頁）",
            strPrevPage: "上一頁",
            strRefresh: "重新整理",
            strRpp: "每頁顯示的記錄數量: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();