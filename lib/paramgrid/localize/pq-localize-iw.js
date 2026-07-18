(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.iw = {
            strAND: "ו",
            strAddColLeft: "הוסף עמודה משמאל",
            strAddColRight: "הוסף עמודה מימין",
            strAddColumn: "הוסף עמודה",
            strAddRow: "הוסף שורה",
            strAddRowAbove: "הוסף שורה למעלה",
            strAddRowBelow: "הוסף שורה למטה",
            strAddRows: "הוסף {0} שורות",
            strAlign: {
                bottom: "יישור למטה",
                center: "יישור למרכז",
                left: "יישור לשמאל",
                right: "יישור ימינה",
                top: "יישור למעלה"
            },
            strAlignH: "יישור אופקי",
            strAlignV: "יישור אנכי",
            strApply: "החל",
            strBlanks: "(ריקות)",
            strBold: "טקסט מודגש",
            strBorder: "גבול",
            strBorderColor: "צבע גבול",
            strBorderStyle: "סגנון גבול",
            strBorders: {
                all: "כל הגבולות",
                bottom: "גבול תחתון",
                horizontal: "גבול אופקי",
                inner: "גבול פנימי",
                left: "גבול שמאל",
                none: "אין גבול",
                outer: "גבול חיצוני",
                right: "גבול ימני",
                top: "גבול עליון",
                vertical: "גבול אנכי"
            },
            strCancel: "בטל",
            strClear: "בָּרוּר",
            strClearColor: "צבע ברור",
            strClearFilter: "נקה מסנן",
            strClearText: "טקסט נקה",
            strCollapse: "כווץ לוח",
            strComment: "הערה",
            strCondition: "Condition:",
            strConditions: {
                "": "-- אין --",
                begin: "מתחיל ב",
                between: "בין לבין",
                contain: "מכיל",
                empty: "ריק",
                end: "מסתיים עם",
                equal: "שווה",
                great: "גדול מ",
                gte: "גדול או שווה",
                less: "פחות מ",
                lte: "פחות או שווה",
                notbegin: "לא מתחיל ב",
                notcontain: "לא מכיל",
                notempty: "לא ריק",
                notend: "לא מסתיים ב",
                notequal: "לא שווה",
                range: "[טווח]",
                regexp: "ביטוי רגיל"
            },
            strCopy: "העתק",
            strCut: "גזור",
            strDelete: "מחק",
            strDeleteColumn: "מחק עמודה",
            strDeleteRow: "מחק שורה",
            strDownloadXlsx: "הורד קובץ אקסל",
            strEdit: "ערוך",
            strExitFullScreen: "צא ממסך מלא",
            strExpand: "הרחב את הלוח",
            strExport: "ייצוא נתונים",
            strFillColor: "צבע מילוי",
            strFontFamily: "משפחת גופנים",
            strFontSize: "גודל גופן",
            strFormat: "עיצוב תאים",
            strFormatMenu: {
                Accounting: "הנהלת חשבונות",
                Currency: "מטבע",
                Custom: "מותאם אישית",
                DateTime: "תאריך שעה",
                Fixed: "עשרוני",
                Fraction: "שבר",
                FullDate: "תאריך מלא",
                General: "כללי",
                Int: "מספר שלם",
                LongDate: "דייט ארוך",
                MediumDate: "תאריך בינוני",
                Note: "הערה: פורמטים עם * משתנים בהתאם לאזור המשתמש.",
                Percent: "אחוז",
                Scientific: "מדעי",
                ShortDate: "דייט קצר",
                Standard: "סטנדרטי",
                Text: "טקסט",
                Time: "זמן"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "מחזירה את הערך המוחלט של מספר.הערך המוחלט של מספר הוא המספר ללא הסימן שלו."
                ],
                ACOS: [
                    "ACOS(number)",
                    "מחזירה את arccosine, או קוסינוס הפוך, של מספר.הארקוסינוס הוא הזווית שהקוסינוס שלה הוא מספר.הזווית המוחזרת נתונה ברדיאנים בטווח 0 (אפס) עד פי."
                ],
                AND: [
                    "AND(logical1, [logical2], ...)",
                    "מחזירה TRUE אם כל הארגומנטים שלו מוערכים ל-TRUE, ומחזירה FALSE אם ארגומנט אחד או יותר מוערך ל-FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "מחזירה את הקשת, או הסינוס ההפוך, של מספר.הקשת היא הזווית שהסינוס שלה הוא מספר.הזווית המוחזרת נתונה ברדיאנים בטווח -pi/2 עד pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "מחזירה את הארקטנג'נט, או הטנגנס ההפוך, של מספר.הארקטנג'נט הוא הזווית שהמשיק שלה הוא מספר.הזווית המוחזרת נתונה ברדיאנים בטווח -pi/2 עד pi/2."
                ],
                AVERAGE: [
                    "AVERAGE(מספר1, [מספר2], ...)",
                    "מחזירה את הממוצע (ממוצע אריתמטי) של הארגומנטים.לדוגמה, אם הטווח A1:A20 מכיל מספרים, הנוסחה =AVERAGE(A1:A20) מחזירה את הממוצע של המספרים הללו."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(טווח, קריטריונים, [טווח_ממוצע])",
                    "מחזירה את הממוצע (ממוצע אריתמטי) של כל התאים בטווח העומדים בקריטריונים נתון."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(average_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)",
                    "מחזירה את הממוצע (ממוצע אריתמטי) של כל התאים העומדים בקריטריונים מרובים."
                ],
                CEILING: [
                    "CEILING(מספר, מובהקות)",
                    "מחזירה מספר מעוגל כלפי מעלה, הרחק מאפס, לכפולת המובהקות הקרובה ביותר.לדוגמה, אם אתה רוצה להימנע משימוש בפרוטות במחירים שלך והמוצר שלך מתומחר ב-$4.42, השתמש בנוסחה =CEILING(4.42,0.05) כדי לעגל את המחירים עד לניקל הקרוב ביותר."
                ],
                CHAR: [
                    "CHAR(number)",
                    "מחזירה את התו שצוין במספר."
                ],
                CHOOSE: [
                    "CHOOSE(index_num, value1, [value2], ...)",
                    "אם index_num הוא 1, CHOOSE מחזירה value1;אם הוא 2, CHOOSE מחזירה value2;וְכֵן הָלְאָה."
                ],
                CODE: [
                    "CODE(text)",
                    "מחזירה קוד מספרי עבור התו הראשון במחרוזת טקסט."
                ],
                COLUMN: [
                    "COLUMN()",
                    "מחזירה הפניה של התא שבו מופיעה הפונקציה COLUMN."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "מחזירה את מספר העמודות במערך או בהפניה."
                ],
                CONCATENATE: [
                    "CONCATENATE(text1, [text2], ...)",
                    "חבר שתי מחרוזות טקסט או יותר למחרוזת אחת."
                ],
                COS: [
                    "COS(number)",
                    "מחזירה את הקוסינוס של הזווית הנתונה (ברדיאנים)."
                ],
                COUNT: [
                    "COUNT(value1, [value2], ...)",
                    "סופרת את מספר התאים המכילים מספרים וסופרת מספרים ברשימת הארגומנטים."
                ],
                COUNTA: [
                    "COUNTA(value1, [value2], ...)",
                    "הפונקציה COUNTA סופרת את מספר התאים שאינם ריקים בטווח."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "סופר תאים ריקים בטווח מוגדר של תאים."
                ],
                COUNTIF: [
                    "COUNTIF(טווח, קריטריונים)",
                    "סופר את מספר התאים העומדים בקריטריון;לדוגמה, לספור את מספר הפעמים שעיר מסוימת מופיעה ברשימת לקוחות."
                ],
                COUNTIFS: [
                    "COUNTIFS(criteria_range1, criteria1, [criteria_range2, criteria2]...)",
                    "מחיל קריטריונים על תאים בטווחים מרובים וסופר את מספר הפעמים שכל הקריטריונים מתקיימים."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "מחזירה את המספר הסידורי הרציף המייצג תאריך מסוים."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "מחשב את מספר הימים, החודשים או השנים בין שני תאריכים.היחידה יכולה להיות 'Y', 'M' או 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "ממירה תאריך המאוחסן כטקסט למספר סידורי ש-Excel מזהה כתאריך.לדוגמה, הנוסחה =DATEVALUE(\"1/1/2008\") מחזירה 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "מחזירה את היום של תאריך, המיוצג על ידי מספר סידורי."
                ],
                DAYS: [
                    "DAYS(end_date, start_date)",
                    "מחזירה את מספר הימים בין שני תאריכים."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "ממיר רדיאנים למעלות."
                ],
                EOMONTH: [
                    "EOMONTH(תאריך_התחלה, חודשים)",
                    "מחזירה את המספר הסידורי של היום האחרון של החודש שהוא מספר החודשים המצוין לפני או אחרי start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "מחזירה e מורם בחזקת מספר."
                ],
                FIND: [
                    "FIND(find_text, within_text, [start_num])",
                    "מאתר מחרוזת טקסט אחת בתוך מחרוזת טקסט שנייה, ומחזיר את המספר של מיקום ההתחלה של מחרוזת הטקסט הראשונה מהתו הראשון של מחרוזת הטקסט השנייה."
                ],
                FLOOR: [
                    "FLOOR(מספר, מובהקות)",
                    "עיגול מספר כלפי מטה, לעבר אפס, לכפולה הקרובה ביותר של המשמעות."
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "מחפש ערך בשורה העליונה של טבלה או מערך של ערכים, ולאחר מכן מחזיר ערך באותה עמודה משורה שאתה מציין בטבלה או במערך."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "מחזירה את השעה של ערך זמן.השעה ניתנת כמספר שלם, הנע בין 0 (12:00 בבוקר) ל-23 (23:00 בצהריים)."
                ],
                HYPERLINK: [
                    "HYPERLINK(כתובת אתר, [שם_ידידותי])",
                    "יוצר קיצור דרך שקופץ למיקום אחר באינטרנט בעת לחיצה על תא"
                ],
                IF: [
                    "IF(logical_test, value_if_true, [value_if_false])",
                    "מחזיר ערך אחד אם תנאי הוא אמת וערך אחר אם הוא שקר."
                ],
                INDEX: [
                    "INDEX(array, row_num, [column_num])",
                    "מחזירה את הערך של אלמנט בטבלה או במערך, שנבחר על ידי אינדקס מספרי השורות והעמודות."
                ],
                INDIRECT: [
                    "INDIRECT(ref_text, [a1])",
                    "מחזירה את ההפניה שצוינה במחרוזת טקסט.הפניות מוערכות מיד כדי להציג את תוכנן."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "מחזירה TRUE אם הערך ריק"
                ],
                LARGE: [
                    "LARGE(מערך, k)",
                    "מחזירה את הערך הק' בגודלו בקבוצת נתונים."
                ],
                LEFT: [
                    "LEFT(טקסט, [מספר_תווים])",
                    "מחזירה את התו או התווים הראשונים במחרוזת טקסט, בהתבסס על מספר התווים שאתה מציין."
                ],
                LEN: [
                    "LEN(text)",
                    "מחזירה את מספר התווים במחרוזת טקסט."
                ],
                LOOKUP: [
                    "LOOKUP(lookup_value, lookup_vector, [result_vector])",
                    "מחפש ערך בטווח של שורה אחת או עמודה אחת (המכונה וקטור) ומחזיר ערך מאותו מיקום בטווח שני של שורה אחת או עמודה אחת."
                ],
                LOWER: [
                    "LOWER(text)",
                    "ממירה את כל האותיות הגדולות במחרוזת טקסט לאותיות קטנות."
                ],
                MATCH: [
                    "MATCH(lookup_value, lookup_array, [match_type])",
                    "מחפש פריט שצוין בטווח של תאים, ולאחר מכן מחזיר את המיקום היחסי של פריט זה בטווח.match_type יכול להיות: 0 עבור התאמה מדויקת עם אפשרות להשתמש בתווים כלליים;1 (ברירת מחדל) עבור פחות מ-, יש למקם את הערכים בארגומנט lookup_array בסדר עולה;-1 עבור גדול מ-, יש למקם את הערכים בארגומנט lookup_array בסדר יורד."
                ],
                MAX: [
                    "MAX(מספר1, [מספר2], ...)",
                    "מחזירה את הערך הגדול ביותר בקבוצת ערכים."
                ],
                MEDIAN: [
                    "MEDIAN(מספר1, [מספר2], ...)",
                    "מחזירה את החציון של המספרים הנתונים.החציון הוא המספר באמצע קבוצת מספרים."
                ],
                MID: [
                    "MID(טקסט, start_num, num_chars)",
                    "מחזירה מספר מסוים של תווים ממחרוזת טקסט, החל מהמיקום שאתה מציין, בהתבסס על מספר התווים שאתה מציין."
                ],
                MIN: [
                    "MIN(מספר1, [מספר2], ...)",
                    "מחזירה את המספר הקטן ביותר בקבוצת ערכים."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "מחזירה את הערך השכיח ביותר, או החוזר על עצמו, במערך או בטווח של נתונים."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "מחזירה את החודש של תאריך המיוצג על ידי מספר סידורי.החודש ניתן כמספר שלם, הנע בין 1 (ינואר) ל-12 (דצמבר)."
                ],
                OR: [
                    "OR(logical1, [logical2], ...)",
                    "מחזירה TRUE אם אחד מהארגומנטים שלו מוערך ל-TRUE, ומחזירה FALSE אם כל הארגומנטים שלו מוערכים ל-FALSE."
                ],
                PI: [
                    "PI()",
                    "מחזירה את המספר 3.14159265358979, הקבוע המתמטי pi."
                ],
                POWER: [
                    "POWER(מספר, הספק)",
                    "מחזירה את התוצאה של מספר שהועלה לחזקה."
                ],
                PRODUCT: [
                    "PRODUCT(מספר1, [מספר2], ...)",
                    "מכפיל את כל המספרים שניתנו כארגומנטים ומחזיר את המוצר.PRODUCT(A1:A3, C1:C3) שווה ערך ל-=A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "שימוש באות רישיות באות הראשונה בכל מילה של ערך טקסט."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "ממירה מעלות לרדיאנים."
                ],
                RAND: [
                    "RAND()",
                    "מחזירה מספר ממשי אקראי המחולק באופן שווה הגדול מ-0 או שווה ל-0 וקטן מ-1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "מחזירה את הדירוג של מספר ברשימת מספרים."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(old_text, start_num, num_chars, new_text)",
                    "מחליף חלק ממחרוזת טקסט, בהתבסס על מספר התווים שאתה מציין, במחרוזת טקסט שונה."
                ],
                REPT: [
                    "REPT(טקסט, מספר_פעמים)",
                    "חוזר על טקסט מספר נתון של פעמים."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "מחזירה את התו או התווים האחרונים במחרוזת טקסט, בהתבסס על מספר התווים שאתה מציין."
                ],
                ROUND: [
                    "ROUND(מספר, מספר_ספרות)",
                    "מעגל מספר למספר מוגדר של ספרות."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(מספר, מספר_ספרות)",
                    "עיגול מספר כלפי מטה, לעבר אפס."
                ],
                ROUNDUP: [
                    "ROUNDUP(מספר, מספר_ספרות)",
                    "עיגול מספר למעלה, הרחק מ-0 (אפס)."
                ],
                ROW: [
                    "ROW()",
                    "מחזירה את ההפניה של התא שבו מופיעה הפונקציה ROW."
                ],
                ROWS: [
                    "ROWS(array)",
                    "מחזירה את מספר השורות בהפניה או במערך."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "אתר מחרוזת טקסט אחת בתוך מחרוזת טקסט שנייה, והחזר את מספר מיקום ההתחלה של מחרוזת הטקסט הראשונה מהתו הראשון של מחרוזת הטקסט השנייה."
                ],
                SIN: [
                    "SIN(number)",
                    "מחזירה את הסינוס של הזווית הנתונה (ברדיאנים)."
                ],
                SMALL: [
                    "SMALL(מערך, k)",
                    "מחזירה את הערך הקטן ביותר בקבוצת נתונים."
                ],
                SQRT: [
                    "SQRT(number)",
                    "מחזירה שורש ריבועי חיובי."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "אומדן סטיית תקן על סמך מדגם.סטיית התקן היא מדד למידת התפזרות הרחבה של הערכים מהערך הממוצע (הממוצע)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "מחשב את סטיית התקן בהתבסס על כל האוכלוסייה שניתנה כטיעונים."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(text, old_text, new_text, [instance_num])",
                    "מחליף new_text עבור old_text במחרוזת טקסט."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "מוסיף את טיעוניו.אתה יכול להוסיף ערכים בודדים, הפניות לתאים או טווחים או שילוב של שלושתם."
                ],
                SUMIF: [
                    "SUMIF(טווח, קריטריון, [טווח_סכום])",
                    "מוסיף את הערכים בטווח העומד בקריטריונים שאתה מציין"
                ],
                SUMIFS: [
                    "SUMIFS(sum_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)",
                    "מוסיף את כל הטיעונים שלו העומדים במספר קריטריונים."
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(מערך1, [מערך2], [מערך3], ...)",
                    "מכפיל את הרכיבים המתאימים במערכים הנתונים, ומחזיר את סכום המוצרים הללו."
                ],
                TAN: [
                    "TAN(number)",
                    "מחזירה את הטנגנס של הזווית הנתונה (ברדיאנים)."
                ],
                TEXT: [
                    "TEXT(ערך שאתה רוצה לעצב, \"פורמט קוד שאתה רוצה להחיל\")",
                    "הפונקציה TEXT מאפשרת לך לשנות את האופן שבו מספר מופיע על ידי החלת עיצוב עליו עם קודי עיצוב."
                ],
                TIME: [
                    "TIME(שעה, דקה, שנייה)",
                    "מחזירה את המספר העשרוני לזמן מסוים."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "מחזירה את המספר העשרוני של הזמן המיוצג על ידי מחרוזת טקסט.המספר העשרוני הוא ערך שנע בין 0 (אפס) ל-0.99988426, המייצג את הזמנים מ-0:00:00 (12:00:00 בבוקר) עד 23:59:59 (11:59:59 בצהריים)."
                ],
                TODAY: [
                    "TODAY()",
                    "מחזירה את המספר הסידורי של התאריך הנוכחי."
                ],
                TRIM: [
                    "TRIM(text)",
                    "מסיר את כל הרווחים מהטקסט מלבד רווחים בודדים בין מילים."
                ],
                TRUNC: [
                    "TRUNC(מספר, [מספר_ספרות])",
                    "חותך מספר למספר שלם על ידי הסרת החלק השברי של המספר."
                ],
                UPPER: [
                    "UPPER(text)",
                    "ממיר טקסט לאותיות רישיות."
                ],
                VALUE: [
                    "VALUE(text)",
                    "ממירה מחרוזת טקסט המייצגת מספר למספר."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "הערכת שונות על סמך מדגם."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "מחשבת שונות על סמך כלל האוכלוסייה."
                ],
                VLOOKUP: [
                    "VLOOKUP (ערך_חיפוש, מערך_טבלה, מספר_אינדקס_קול, [חיפוש_טווח])",
                    "חפש ערך בטבלה או טווח אחר שורה.לדוגמה, חפש מחיר של חלק לרכב לפי מספר החלק."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "מחזירה את השנה המתאימה לתאריך.השנה מוחזרת כמספר שלם בטווח 1900-9999."
                ]
            },
            strFreezeFirstCol: "הקפאת העמודה הראשונה",
            strFreezePanes: "הקפאת חלוניות",
            strFreezePanesUndo: "בטל את הקפאת החלונות",
            strFreezeTopRow: "הקפאת שורה ראשונה",
            strFrozenCols: "עמודות קפואות",
            strFrozenRows: "שורות קפואות",
            strFullScreen: "מסך מלא",
            strGroup_fixCols: "תקן עמודות",
            strGroup_grandSummary: "סיכום גדול",
            strGroup_header: "שחרר קבוצה לפי עמודה",
            strGroup_merge: "מיזוג תאים",
            strHideCols: "הסתר עמודות",
            strHideRows: "הסתר שורות",
            strImport: "ייבוא נתונים",
            strImportPic: "הוסף תמונה צפה",
            strImportPicCell: "הוסף תמונה לתא",
            strInsertColumn: "הוסף עמודה",
            strInsertRow: "הכנס שורה",
            strInsertRows: "הוסף {0} שורות",
            strItalic: "טקסט נטוי",
            strLabel: "תווית",
            strLink: "קישור",
            strLoading: "טוען",
            strLocal: "iw",
            strLockCells: "נעילת תאים",
            strMenu: {
                export: "עמודות שניתנות לייצוא",
                filter: "מסנן",
                hideCols: "עמודות גלויות"
            },
            strMerge: "מיזוג תאים",
            strName: "שם",
            strNextResult: "התוצאה הבאה",
            strNoRows: "אין שורות להצגה.",
            strNothingFound: "שום דבר לא נמצא",
            strOR: "או",
            strOk: "בסדר",
            strOpen: "פתוח",
            strPaste: "הדבק",
            strPrevResult: "תוצאה קודמת",
            strRedo: "בצע מחדש",
            strRename: "שנה שם",
            strSearch: "חפש",
            strSelectAll: "בחר הכל",
            strSelectedmatches: "Selected {0} of {1} matches",
            strShowCols: "הצג עמודות",
            strShowRows: "הצג שורות",
            strTP_aggPH: "שחרר עמודות עבור אגרגטים",
            strTP_aggPane: "אגרגטים",
            strTP_colPH: "שחרר עמודות לקיבוץ עמודות",
            strTP_colPane: "קבוצות עמודות",
            strTP_pivot: "מצב ציר",
            strTP_rowPH: "שחרר עמודות לקיבוץ שורות",
            strTP_rowPane: "שורות קבוצתיות",
            strTabAdd: "גיליון חדש",
            strTabClose: "הסר גיליון",
            strTabHide: "הסתר גיליון",
            strTabName: "sheet{0}",
            strTabRemove: "{0} יימחק לצמיתות.\r\nאתה בטוח?",
            strTabRename: "שנה את שם הגיליון",
            strTabShow: "הצג גיליון",
            strTextColor: "צבע טקסט",
            strUnderline: "טקסט בקו תחתון",
            strUndo: "בטל",
            strUnhide: "הצג",
            strUnmerge: "בטל מיזוג תאים",
            strUpdate: "עדכון",
            strWrap: "גלישת טקסט"
        },
        pager = pq.pqPager.regional.iw = {
            strDisplay: "מציג {0} עד {1} מתוך {2} פריטים.",
            strFirstPage: "עמוד ראשון",
            strLastPage: "עמוד אחרון",
            strNextPage: "העמוד הבא",
            strPage: "עמוד {0} מתוך {1}",
            strPrevPage: "העמוד הקודם",
            strRefresh: "רענן",
            strRpp: "רשומות לכל דף: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();