(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.zh = {
            strAND: "和",
            strAddColLeft: "添加左侧列",
            strAddColRight: "添加右侧列",
            strAddColumn: "添加列",
            strAddRow: "添加行",
            strAddRowAbove: "在上方添加行",
            strAddRowBelow: "添加下面的行",
            strAddRows: "添加 {0} 行",
            strAlign: {
                bottom: "底部对齐",
                center: "居中对齐",
                left: "左对齐",
                right: "右对齐",
                top: "顶部对齐"
            },
            strAlignH: "水平对齐",
            strAlignV: "垂直对齐",
            strApply: "申请",
            strBlanks: "（空白）",
            strBold: "粗体文字",
            strBorder: "边框",
            strBorderColor: "边框颜色",
            strBorderStyle: "边框样式",
            strBorders: {
                all: "所有边框",
                bottom: "下边框",
                horizontal: "水平边框",
                inner: "内边框",
                left: "左边框",
                none: "无国界",
                outer: "外边框",
                right: "右边框",
                top: "顶部边框",
                vertical: "垂直边框"
            },
            strCancel: "取消",
            strClear: "清除",
            strClearColor: "颜色清晰",
            strClearFilter: "清除过滤器",
            strClearText: "清晰的文本",
            strCollapse: "折叠面板",
            strComment: "评论",
            strCondition: "Condition:",
            strConditions: {
                "": "-- 无 --",
                begin: "开始于",
                between: "介于两者之间",
                contain: "包含",
                empty: "空",
                end: "结束于",
                equal: "等于",
                great: "大于",
                gte: "大于或等于",
                less: "小于",
                lte: "小于或等于",
                notbegin: "不开始于",
                notcontain: "不包含",
                notempty: "不为空",
                notend: "不以以下结尾",
                notequal: "不等于",
                range: "[ 范围 ]",
                regexp: "正则表达式"
            },
            strCopy: "复制",
            strCut: "切",
            strDelete: "删除",
            strDeleteColumn: "删除列",
            strDeleteRow: "删除行",
            strDownloadXlsx: "下载Excel文件",
            strEdit: "编辑",
            strExitFullScreen: "退出全屏",
            strExpand: "展开面板",
            strExport: "导出数据",
            strFillColor: "填充颜色",
            strFontFamily: "字体家族",
            strFontSize: "字体大小",
            strFormat: "设置单元格格式",
            strFormatMenu: {
                Accounting: "会计",
                Currency: "货币",
                Custom: "定制",
                DateTime: "日期时间",
                Fixed: "十进制",
                Fraction: "分数",
                FullDate: "完整日期",
                General: "一般",
                Int: "整数",
                LongDate: "长日期",
                MediumDate: "中日期",
                Note: "注意：带 * 的格式根据用户区域设置而变化。",
                Percent: "百分比",
                Scientific: "科学的",
                ShortDate: "短日期",
                Standard: "标准型",
                Text: "文字",
                Time: "时间"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "返回数字的绝对值。数字的绝对值是不带符号的数字。"
                ],
                ACOS: [
                    "ACOS(number)",
                    "返回数字的反余弦或反余弦。反余弦是余弦为数字的角度。返回的角度以弧度为单位，范围为 0（零）到 pi。"
                ],
                AND: [
                    "AND(逻辑 1, [逻辑 2], ...)",
                    "如果所有参数的计算结果都为 TRUE，则返回 TRUE；如果一个或多个参数的计算结果为 FALSE，则返回 FALSE。"
                ],
                ASIN: [
                    "ASIN(number)",
                    "返回数字的反正弦或反正弦。反正弦是正弦为数字的角度。返回的角度以弧度给出，范围为 -pi/2 到 pi/2。"
                ],
                ATAN: [
                    "ATAN(number)",
                    "返回数字的反正切或反正切。反正切是正切为数字的角度。返回的角度以弧度给出，范围为 -pi/2 到 pi/2。"
                ],
                AVERAGE: [
                    "平均值（数字 1，[数字 2]，...）",
                    "返回参数的平均值（算术平均值）。例如，如果范围 A1:A20 包含数字，则公式 =AVERAGE(A1:A20) 返回这些数字的平均值。"
                ],
                AVERAGEIF: [
                    "AVERAGEIF（范围，标准，[平均范围]）",
                    "返回满足给定条件的范围内所有单元格的平均值（算术平均值）。"
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(平均范围, 标准范围1, 标准1, [标准范围2, 标准2], ...)",
                    "返回满足多个条件的所有单元格的平均值（算术平均值）。"
                ],
                CEILING: [
                    "CEILING(数字，意义)",
                    "返回从零向上舍入到最接近的有效倍数的数字。例如，如果您想避免在价格中使用便士，并且您的产品定价为 4.42 美元，请使用公式 =CEILING(4.42,0.05) 将价格四舍五入到最接近的镍币。"
                ],
                CHAR: [
                    "CHAR(number)",
                    "返回由数字指定的字符。"
                ],
                CHOOSE: [
                    "选择（index_num，值1，[值2]，...）",
                    "如果index_num为1，则CHOOSE返回value1；如果是2，则CHOOSE返回value2；等等。"
                ],
                CODE: [
                    "CODE(text)",
                    "返回文本字符串中第一个字符的数字代码。"
                ],
                COLUMN: [
                    "COLUMN()",
                    "返回出现 COLUMN 函数的单元格的引用。"
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "返回数组或引用中的列数。"
                ],
                CONCATENATE: [
                    "连接（文本1，[文本2]，...）",
                    "将两个或多个文本字符串连接成一个字符串。"
                ],
                COS: [
                    "COS(number)",
                    "返回给定角度的余弦（以弧度为单位）。"
                ],
                COUNT: [
                    "计数（值 1，[值 2]，...）",
                    "计算包含数字的单元格数量，并计算参数列表中的数字。"
                ],
                COUNTA: [
                    "COUNTA(值 1, [值 2], ...)",
                    "COUNTA 函数计算区域中非空单元格的数量。"
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "计算指定单元格范围内的空单元格数量。"
                ],
                COUNTIF: [
                    "COUNTIF（范围，条件）",
                    "计算满足标准的细胞数量；例如，计算特定城市出现在客户列表中的次数。"
                ],
                COUNTIFS: [
                    "COUNTIFS(criteria_range1, criteria1, [criteria_range2, criteria2]...)",
                    "将条件应用于多个范围的单元格，并计算满足所有条件的次数。"
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "返回代表特定日期的连续序列号。"
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "计算两个日期之间的天数、月数或年数。单位可以是“Y”、“M”或“D”。"
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "将存储为文本的日期转换为 Excel 识别为日期的序列号。例如，公式 =DATEVALUE(\"1/1/2008\") 返回 39448。"
                ],
                DAY: [
                    "DAY(serial_number)",
                    "返回日期中的天数，用序列号表示。"
                ],
                DAYS: [
                    "天（结束日期，开始日期）",
                    "返回两个日期之间的天数。"
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "将弧度转换为度数。"
                ],
                EOMONTH: [
                    "EOMONTH（开始日期，月份）",
                    "返回指定的 start_date 之前或之后月份数的月份最后一天的序列号。"
                ],
                EXP: [
                    "EXP(number)",
                    "返回 e 的数字次方。"
                ],
                FIND: [
                    "FIND（查找文本，文本内，[开始编号]）",
                    "在第二个文本字符串中定位一个文本字符串，并返回从第二个文本字符串的第一个字符到第一个文本字符串的起始位置的编号。"
                ],
                FLOOR: [
                    "FLOOR（数字，意义）",
                    "将数字向下舍入到零，直至最接近的显着性倍数。"
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "在表或值数组的顶行中搜索值，然后返回表或数组中指定行的同一列中的值。"
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "返回时间值的小时。小时以整数形式给出，范围从 0 (12:00 A.M.) 到 23 (11:00 P.M.)。"
                ],
                HYPERLINK: [
                    "超链接（网址，[友好名称]）",
                    "创建一个快捷方式，当您单击某个单元格时，该快捷方式会跳转到 Internet 中的另一个位置"
                ],
                IF: [
                    "IF(逻辑测试, value_if_true, [value_if_false])",
                    "如果条件为 true，则返回一个值；如果条件为 false，则返回另一个值。"
                ],
                INDEX: [
                    "INDEX(数组, 行数, [列数])",
                    "返回表或数组中按行号和列号索引选择的元素的值。"
                ],
                INDIRECT: [
                    "间接（ref_text，[a1]）",
                    "返回由文本字符串指定的引用。立即评估引用以显示其内容。"
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "如果值为空则返回 TRUE"
                ],
                LARGE: [
                    "大（数组，k）",
                    "返回数据集中的第 k 个最大值。"
                ],
                LEFT: [
                    "左（文本，[num_chars]）",
                    "根据您指定的字符数返回文本字符串中的第一个或多个字符。"
                ],
                LEN: [
                    "LEN(text)",
                    "返回文本字符串中的字符数。"
                ],
                LOOKUP: [
                    "LOOKUP（查找值，查找向量，[结果向量]）",
                    "在单行或一列范围（称为向量）中查找值，并从第二个单行或一列范围中的相同位置返回值。"
                ],
                LOWER: [
                    "LOWER(text)",
                    "将文本字符串中的所有大写字母转换为小写。"
                ],
                MATCH: [
                    "MATCH（查找值，查找数组，[匹配类型]）",
                    "在单元格区域中搜索指定项目，然后返回该项目在该区域中的相对位置。match_type 可以是： 0 表示精确匹配，并可选择使用通配符；1（默认）小于，lookup_array 参数中的值必须按升序排列；-1 表示大于，lookup_array 参数中的值必须按降序排列。"
                ],
                MAX: [
                    "MAX(数字 1, [数字 2], ...)",
                    "返回一组值中的最大值。"
                ],
                MEDIAN: [
                    "MEDIAN(数字 1, [数字 2], ...)",
                    "返回给定数字的中位数。中位数是一组数字中位于中间的数字。"
                ],
                MID: [
                    "MID(文本、起始编号、字符数)",
                    "根据您指定的字符数，从文本字符串中返回特定数量的字符，从您指定的位置开始。"
                ],
                MIN: [
                    "MIN(数字 1, [数字 2], ...)",
                    "返回一组值中的最小数字。"
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "返回数组或数据范围中最常出现或重复的值。"
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "返回由序列号表示的日期的月份。月份以整数形式给出，范围从 1（一月）到 12（十二月）。"
                ],
                OR: [
                    "OR(逻辑 1, [逻辑 2], ...)",
                    "如果其任何参数计算结果为 TRUE，则返回 TRUE；如果其所有参数计算结果为 FALSE，则返回 FALSE。"
                ],
                PI: [
                    "PI()",
                    "返回数字 3.14159265358979，即数学常数 pi。"
                ],
                POWER: [
                    "POWER(数量,功率)",
                    "返回数字的幂结果。"
                ],
                PRODUCT: [
                    "产品（数字 1，[数字 2]，...）",
                    "将作为参数给出的所有数字相乘并返回乘积。Product(A1:A3, C1:C3) 相当于 =A1 * A2 * A3 * C1 * C2 * C3。"
                ],
                PROPER: [
                    "PROPER(text)",
                    "将文本值的每个单词的第一个字母大写。"
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "将度数转换为弧度。"
                ],
                RAND: [
                    "RAND()",
                    "返回大于或等于 0 且小于 1 的均匀分布的随机实数。"
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "返回数字列表中数字的排名。"
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE（旧文本，开始编号，字符数，新文本）",
                    "根据您指定的字符数，用不同的文本字符串替换部分文本字符串。"
                ],
                REPT: [
                    "REPT(文本, 次数)",
                    "重复文本指定次数。"
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "根据指定的字符数返回文本字符串中的最后一个或多个字符。"
                ],
                ROUND: [
                    "ROUND(数字, 数字位数)",
                    "将数字四舍五入到指定的位数。"
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN（数字，数字位数）",
                    "将数字向下舍入到零。"
                ],
                ROUNDUP: [
                    "ROUNDUP(数字, num_digits)",
                    "将数字向上舍入，远离 0（零）。"
                ],
                ROW: [
                    "ROW()",
                    "返回 ROW 函数所在单元格的引用。"
                ],
                ROWS: [
                    "ROWS(array)",
                    "返回引用或数组中的行数。"
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "在第二个文本字符串中定位一个文本字符串，并返回从第二个文本字符串的第一个字符到第一个文本字符串的起始位置的编号。"
                ],
                SIN: [
                    "SIN(number)",
                    "返回给定角度的正弦值（以弧度为单位）。"
                ],
                SMALL: [
                    "小（数组，k）",
                    "返回数据集中第 k 个最小值。"
                ],
                SQRT: [
                    "SQRT(number)",
                    "返回正平方根。"
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "根据样本估计标准差。标准差是衡量值与平均值（均值）的离散程度的指标。"
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "根据作为参数给出的整个总体计算标准差。"
                ],
                SUBSTITUTE: [
                    "替换（文本，旧文本，新文本，[instance_num]）",
                    "在文本字符串中用 new_text 替换 old_text。"
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "添加其参数。您可以添加单个值、单元格引用或范围或三者的组合。"
                ],
                SUMIF: [
                    "SUMIF(范围, 标准, [sum_range])",
                    "添加满足您指定条件的范围内的值"
                ],
                SUMIFS: [
                    "SUMIFS(sum_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)",
                    "添加满足多个条件的所有参数。"
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(数组1, [数组2], [数组3], ...)",
                    "将给定数组中的相应分量相乘，并返回这些乘积的总和。"
                ],
                TAN: [
                    "TAN(number)",
                    "返回给定角度（以弧度为单位）的正切值。"
                ],
                TEXT: [
                    "TEXT（您要格式化的值，“格式化您要应用的代码”）",
                    "TEXT 函数允许您通过使用格式代码应用格式来更改数字的显示方式。"
                ],
                TIME: [
                    "TIME（时、分、秒）",
                    "返回特定时间的十进制数。"
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "返回文本字符串表示的时间的十进制数。十进制数的值范围为 0（零）到 0.99988426，表示从 0:00:00 (12:00:00 AM) 到 23:59:59 (11:59:59 P.M.) 的时间。"
                ],
                TODAY: [
                    "TODAY()",
                    "返回当前日期的序列号。"
                ],
                TRIM: [
                    "TRIM(text)",
                    "删除文本中的所有空格（单词之间的单个空格除外）。"
                ],
                TRUNC: [
                    "TRUNC(数字, [num_digits])",
                    "通过删除数字的小数部分将数字截断为整数。"
                ],
                UPPER: [
                    "UPPER(text)",
                    "将文本转换为大写。"
                ],
                VALUE: [
                    "VALUE(text)",
                    "将表示数字的文本字符串转换为数字。"
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "根据样本估计方差。"
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "基于整个总体计算方差。"
                ],
                VLOOKUP: [
                    "VLOOKUP（查找值，表数组，列索引号，[范围查找]）",
                    "按行查找表或范围中的值。例如，通过零件编号查找汽车零件的价格。"
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "返回与日期对应的年份。年份以 1900-9999 范围内的整数形式返回。"
                ]
            },
            strFreezeFirstCol: "冻结第一列",
            strFreezePanes: "冻结窗格",
            strFreezePanesUndo: "解冻窗格",
            strFreezeTopRow: "冻结第一行",
            strFrozenCols: "冷冻柱",
            strFrozenRows: "冻结行",
            strFullScreen: "全屏",
            strGroup_fixCols: "固定列",
            strGroup_grandSummary: "大总结",
            strGroup_header: "按列删除组",
            strGroup_merge: "合并单元格",
            strHideCols: "隐藏列",
            strHideRows: "隐藏行",
            strImport: "导入数据",
            strImportPic: "插入浮动图像",
            strImportPicCell: "在单元格中插入图像",
            strInsertColumn: "插入列",
            strInsertRow: "插入行",
            strInsertRows: "插入 {0} 行",
            strItalic: "斜体文本",
            strLabel: "标签",
            strLink: "链接",
            strLoading: "加载中",
            strLocal: "zh",
            strLockCells: "锁定细胞",
            strMenu: {
                export: "可导出的列",
                filter: "过滤器",
                hideCols: "可见列"
            },
            strMerge: "合并单元格",
            strName: "名称",
            strNextResult: "下一个结果",
            strNoRows: "没有行显示",
            strNothingFound: "没有找到任何内容",
            strOR: "或",
            strOk: "好的",
            strOpen: "打开",
            strPaste: "粘贴",
            strPrevResult: "上一结果",
            strRedo: "重做",
            strRename: "重命名",
            strSearch: "搜索",
            strSelectAll: "选择全部",
            strSelectedmatches: "选择{0}{1}匹配",
            strShowCols: "显示栏目",
            strShowRows: "显示行",
            strTP_aggPH: "删除聚合列",
            strTP_aggPane: "骨料",
            strTP_colPH: "删除列以进行列分组",
            strTP_colPane: "分组列",
            strTP_pivot: "旋转模式",
            strTP_rowPH: "删除行分组的列",
            strTP_rowPane: "分组行",
            strTabAdd: "新表",
            strTabClose: "移除片材",
            strTabHide: "隐藏表",
            strTabName: "sheet{0}",
            strTabRemove: "{0} 将被永久删除。\r\n你确定吗？",
            strTabRename: "重命名工作表",
            strTabShow: "显示表",
            strTextColor: "文字颜色",
            strUnderline: "下划线文本",
            strUndo: "撤消",
            strUnhide: "取消隐藏",
            strUnmerge: "取消合并单元格",
            strUpdate: "更新",
            strWrap: "文字换行"
        },
        pager = pq.pqPager.regional.zh = {
            strDisplay: "显示 {0} 到 {1} {2} 个项目",
            strFirstPage: "第一页",
            strLastPage: "尾页",
            strNextPage: "下一页",
            strPage: "第 {0} 页（共 {1} 页）",
            strPrevPage: "上一页",
            strRefresh: "刷新",
            strRpp: "每页记录: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();