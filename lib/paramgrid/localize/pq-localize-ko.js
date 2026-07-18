(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.ko = {
            strAND: "그리고",
            strAddColLeft: "왼쪽에 열 추가",
            strAddColRight: "오른쪽에 열 추가",
            strAddColumn: "열 추가",
            strAddRow: "행 추가",
            strAddRowAbove: "위에 행 추가",
            strAddRowBelow: "아래에 행 추가",
            strAddRows: "{0}개 행 추가",
            strAlign: {
                bottom: "하단 정렬",
                center: "중앙 정렬",
                left: "왼쪽 정렬",
                right: "오른쪽 정렬",
                top: "상단 정렬"
            },
            strAlignH: "수평 정렬",
            strAlignV: "수직 정렬",
            strApply: "적용",
            strBlanks: "(공백)",
            strBold: "굵은 텍스트",
            strBorder: "국경",
            strBorderColor: "테두리 색상",
            strBorderStyle: "테두리 스타일",
            strBorders: {
                all: "모든 국경",
                bottom: "하단 테두리",
                horizontal: "가로 테두리",
                inner: "내부 테두리",
                left: "왼쪽 테두리",
                none: "국경 없음",
                outer: "외부 테두리",
                right: "오른쪽 테두리",
                top: "상단 테두리",
                vertical: "세로 테두리"
            },
            strCancel: "취소",
            strClear: "분명한",
            strClearColor: "클리어 컬러",
            strClearFilter: "필터 지우기",
            strClearText: "텍스트 지우기",
            strCollapse: "패널 축소",
            strComment: "댓글",
            strCondition: "조건 :",
            strConditions: {
                "": "-없음-",
                begin: "시작",
                between: "사이",
                contain: "포함",
                empty: "빈",
                end: "끝",
                equal: "같음",
                great: "보다 큼",
                gte: "보다 크거나 같음",
                less: "보다 작음",
                lte: "보다 작거나 같음",
                notbegin: "시작하지 않음",
                notcontain: "포함하지 않음",
                notempty: "비어 있지 않음",
                notend: "로 끝나지 않음",
                notequal: "같지 않음",
                range: "[범위]",
                regexp: "정규 표현식"
            },
            strCopy: "복사",
            strCut: "컷",
            strDelete: "삭제",
            strDeleteColumn: "열 삭제",
            strDeleteRow: "행 삭제",
            strDownloadXlsx: "엑셀 파일 다운로드",
            strEdit: "편집",
            strExitFullScreen: "전체 화면 종료",
            strExpand: "패널 확장",
            strExport: "데이터 내보내기",
            strFillColor: "채우기 색상",
            strFontFamily: "글꼴 계열",
            strFontSize: "글꼴 크기",
            strFormat: "셀 서식 지정",
            strFormatMenu: {
                Accounting: "회계",
                Currency: "통화",
                Custom: "맞춤",
                DateTime: "날짜 시간",
                Fixed: "소수",
                Fraction: "분수",
                FullDate: "전체 날짜",
                General: "일반",
                Int: "정수",
                LongDate: "긴 날짜",
                MediumDate: "중간 날짜",
                Note: "참고: *가 있는 형식은 사용자 로케일에 따라 변경됩니다.",
                Percent: "백분율",
                Scientific: "과학적",
                ShortDate: "짧은 데이트",
                Standard: "표준",
                Text: "텍스트",
                Time: "시간"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "숫자의 절대값을 반환합니다.숫자의 절대값은 부호가 없는 숫자입니다."
                ],
                ACOS: [
                    "ACOS(number)",
                    "숫자의 아크코사인 또는 역코사인을 반환합니다.아크코사인은 코사인이 숫자인 각도입니다.반환된 각도는 0(영)에서 pi까지의 범위에서 라디안으로 제공됩니다."
                ],
                AND: [
                    "AND(논리1, [논리2], ...)",
                    "모든 인수가 TRUE로 평가되면 TRUE를 반환하고, 하나 이상의 인수가 FALSE로 평가되면 FALSE를 반환합니다."
                ],
                ASIN: [
                    "ASIN(number)",
                    "숫자의 아크사인 또는 역사인을 반환합니다.아크사인은 사인이 숫자인 각도입니다.반환된 각도는 -pi/2 ~ pi/2 범위의 라디안으로 제공됩니다."
                ],
                ATAN: [
                    "ATAN(number)",
                    "숫자의 아크탄젠트 또는 역탄젠트를 반환합니다.아크탄젠트는 탄젠트가 숫자인 각도입니다.반환된 각도는 -pi/2 ~ pi/2 범위의 라디안으로 제공됩니다."
                ],
                AVERAGE: [
                    "평균(숫자1, [숫자2], ...)",
                    "인수의 평균(산술 평균)을 반환합니다.예를 들어, A1:A20 범위에 숫자가 포함된 경우 수식 =AVERAGE(A1:A20)는 해당 숫자의 평균을 반환합니다."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(범위, 기준, [평균_범위])",
                    "지정된 기준을 충족하는 범위 내 모든 셀의 평균(산술 평균)을 반환합니다."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(평균_범위, 기준_범위1, 기준1, [기준_범위2, 기준2], ...)",
                    "여러 기준을 충족하는 모든 셀의 평균(산술 평균)을 반환합니다."
                ],
                CEILING: [
                    "CEILING(수, 의미)",
                    "0에서 멀어지는 방향으로 가장 가까운 유효 배수로 반올림된 숫자를 반환합니다.예를 들어 가격에 동전을 사용하지 않으려고 제품 가격이 $4.42인 경우 수식 =CEILING(4.42,0.05)을 사용하여 가격을 가장 가까운 니켈 단위로 반올림합니다."
                ],
                CHAR: [
                    "CHAR(number)",
                    "숫자로 지정된 문자를 반환합니다."
                ],
                CHOOSE: [
                    "CHOOSE(색인_번호, 값1, [값2], ...)",
                    "index_num이 1이면 CHOOSE는 value1을 반환합니다.2인 경우 CHOOSE는 value2를 반환합니다.등."
                ],
                CODE: [
                    "CODE(text)",
                    "텍스트 문자열의 첫 번째 문자에 대한 숫자 코드를 반환합니다."
                ],
                COLUMN: [
                    "COLUMN()",
                    "COLUMN 함수가 나타나는 셀의 참조를 반환합니다."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "배열 또는 참조의 열 수를 반환합니다."
                ],
                CONCATENATE: [
                    "연결(텍스트1, [텍스트2], ...)",
                    "두 개 이상의 텍스트 문자열을 하나의 문자열로 결합합니다."
                ],
                COS: [
                    "COS(number)",
                    "주어진 각도(라디안 단위)의 코사인을 반환합니다."
                ],
                COUNT: [
                    "개수(값1, [값2], ...)",
                    "숫자가 포함된 셀의 수를 계산하고 인수 목록 내의 숫자를 계산합니다."
                ],
                COUNTA: [
                    "COUNTA(값1, [값2], ...)",
                    "COUNTA 함수는 범위에서 비어 있지 않은 셀 수를 계산합니다."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "지정된 셀 범위에서 빈 셀의 개수를 계산합니다."
                ],
                COUNTIF: [
                    "COUNTIF(범위, 기준)",
                    "기준을 충족하는 셀의 수를 계산합니다.예를 들어 특정 도시가 고객 목록에 나타나는 횟수를 계산합니다."
                ],
                COUNTIFS: [
                    "COUNTIFS(기준_범위1, 기준1, [기준_범위2, 기준2]…)",
                    "여러 범위의 셀에 기준을 적용하고 모든 기준이 충족되는 횟수를 계산합니다."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "특정 날짜를 나타내는 순차적 일련 번호를 반환합니다."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "두 날짜 사이의 일, 월, 연도 수를 계산합니다.단위는 'Y', 'M' 또는 'D'일 수 있습니다."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "텍스트로 저장된 날짜를 Excel에서 날짜로 인식하는 일련 번호로 변환합니다.예를 들어 수식 =DATEVALUE(\"2008년 1월 1일\")은 39448을 반환합니다."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "일련번호로 표시되는 날짜의 요일을 반환합니다."
                ],
                DAYS: [
                    "DAYS(종료일, 시작일)",
                    "두 날짜 사이의 일수를 반환합니다."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "라디안을 각도로 변환합니다."
                ],
                EOMONTH: [
                    "EOMONTH(시작_날짜, 개월)",
                    "start_date 전후로 표시된 개월 수에 해당하는 달의 마지막 날에 대한 일련 번호를 반환합니다."
                ],
                EXP: [
                    "EXP(number)",
                    "e의 거듭제곱을 반환합니다."
                ],
                FIND: [
                    "FIND(find_text, inside_text, [start_num])",
                    "두 번째 텍스트 문자열 내에서 하나의 텍스트 문자열을 찾고, 두 번째 텍스트 문자열의 첫 번째 문자에서 첫 번째 텍스트 문자열의 시작 위치 번호를 반환합니다."
                ],
                FLOOR: [
                    "FLOOR(숫자, 의미)",
                    "숫자를 0에 가깝게 내림하여 가장 가까운 유효 배수로 만듭니다."
                ],
                HLOOKUP: [
                    "HLOOKUP(lookup_value, table_array, row_index_num, [range_lookup])",
                    "테이블이나 값 배열의 맨 위 행에서 값을 검색한 다음 테이블이나 배열에서 지정한 행의 동일한 열에 있는 값을 반환합니다."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "시간 값의 시간을 반환합니다.시간은 0(오전 12시)부터 23(오후 11시)까지의 정수로 제공됩니다."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [친숙한_이름])",
                    "셀을 클릭하면 인터넷의 다른 위치로 이동하는 바로가기를 만듭니다."
                ],
                IF: [
                    "IF(논리_테스트, 값_if_true, [값_if_false])",
                    "조건이 true이면 하나의 값을 반환하고, false이면 다른 값을 반환합니다."
                ],
                INDEX: [
                    "INDEX(배열, 행_번호, [열_번호])",
                    "행 및 열 번호 인덱스로 선택된 테이블 또는 배열의 요소 값을 반환합니다."
                ],
                INDIRECT: [
                    "간접(ref_text, [a1])",
                    "텍스트 문자열로 지정된 참조를 반환합니다.참조는 즉시 평가되어 내용을 표시합니다."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "값이 비어 있으면 TRUE를 반환합니다."
                ],
                LARGE: [
                    "LARGE(배열, k)",
                    "데이터 세트에서 k번째로 큰 값을 반환합니다."
                ],
                LEFT: [
                    "LEFT(텍스트, [num_chars])",
                    "지정한 문자 수에 따라 텍스트 문자열의 첫 번째 문자를 반환합니다."
                ],
                LEN: [
                    "LEN(text)",
                    "텍스트 문자열의 문자 수를 반환합니다."
                ],
                LOOKUP: [
                    "LOOKUP(조회_값, 조회_벡터, [결과_벡터])",
                    "1행 또는 1열 범위(벡터라고 함)에서 값을 찾고 두 번째 1행 또는 1열 범위의 동일한 위치에 있는 값을 반환합니다."
                ],
                LOWER: [
                    "LOWER(text)",
                    "텍스트 문자열의 모든 대문자를 소문자로 변환합니다."
                ],
                MATCH: [
                    "MATCH(조회_값, 조회_배열, [일치_유형])",
                    "셀 범위에서 지정된 항목을 검색한 다음 범위에서 해당 항목의 상대적 위치를 반환합니다.match_type은 다음과 같습니다. 와일드카드를 사용하는 옵션과 정확히 일치하는 경우 0;미만인 경우 1(기본값),lookup_array 인수의 값은 오름차순으로 배치되어야 합니다.-1보다 큰 경우,lookup_array 인수의 값은 내림차순으로 배치되어야 합니다."
                ],
                MAX: [
                    "MAX(숫자1, [숫자2], ...)",
                    "값 집합에서 가장 큰 값을 반환합니다."
                ],
                MEDIAN: [
                    "MEDIAN(숫자1, [숫자2], ...)",
                    "주어진 숫자의 중앙값을 반환합니다.중앙값은 숫자 집합의 중간에 있는 숫자입니다."
                ],
                MID: [
                    "MID(텍스트, 시작_번호, 숫자_문자)",
                    "지정한 문자 수에 따라 지정한 위치에서 시작하여 텍스트 문자열의 특정 수의 문자를 반환합니다."
                ],
                MIN: [
                    "MIN(숫자1, [숫자2], ...)",
                    "값 집합에서 가장 작은 숫자를 반환합니다."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "데이터 배열 또는 범위에서 가장 자주 발생하거나 반복되는 값을 반환합니다."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "일련번호가 나타내는 날짜의 월을 반환합니다.월은 1(1월)부터 12(12월)까지의 정수로 제공됩니다."
                ],
                OR: [
                    "OR(논리1, [논리2], ...)",
                    "인수 중 하나라도 TRUE로 평가되면 TRUE를 반환하고, 모든 인수가 FALSE로 평가되면 FALSE를 반환합니다."
                ],
                PI: [
                    "PI()",
                    "수학 상수 pi인 숫자 3.14159265358979를 반환합니다."
                ],
                POWER: [
                    "POWER(숫자, 거듭제곱)",
                    "숫자를 거듭제곱한 결과를 반환합니다."
                ],
                PRODUCT: [
                    "제품(번호1, [번호2], ...)",
                    "인수로 주어진 모든 숫자를 곱하고 결과를 반환합니다.PRODUCT(A1:A3, C1:C3)은 =A1 * A2 * A3 * C1 * C2 * C3과 동일합니다."
                ],
                PROPER: [
                    "PROPER(text)",
                    "텍스트 값의 각 단어의 첫 글자를 대문자로 표시합니다."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "각도를 라디안으로 변환합니다."
                ],
                RAND: [
                    "RAND()",
                    "0보다 크거나 같고 1보다 작은 균등하게 분포된 임의의 실수를 반환합니다."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "숫자 목록에서 숫자의 순위를 반환합니다."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(old_text, start_num, num_chars, new_text)",
                    "지정한 문자 수에 따라 텍스트 문자열의 일부를 다른 텍스트 문자열로 바꿉니다."
                ],
                REPT: [
                    "REPT(텍스트, 숫자_회)",
                    "지정된 횟수만큼 텍스트를 반복합니다."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "지정한 문자 수에 따라 텍스트 문자열의 마지막 문자를 반환합니다."
                ],
                ROUND: [
                    "ROUND(숫자, num_digits)",
                    "숫자를 지정된 자릿수로 반올림합니다."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(숫자, num_digits)",
                    "숫자를 0에 가깝게 내림합니다."
                ],
                ROUNDUP: [
                    "ROUNDUP(숫자, num_digits)",
                    "숫자를 0(영)에서 멀어지도록 반올림합니다."
                ],
                ROW: [
                    "ROW()",
                    "ROW 함수가 나타나는 셀의 참조를 반환합니다."
                ],
                ROWS: [
                    "ROWS(array)",
                    "참조 또는 배열의 행 수를 반환합니다."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "두 번째 텍스트 문자열 내에서 하나의 텍스트 문자열을 찾고, 두 번째 텍스트 문자열의 첫 번째 문자에서 첫 번째 텍스트 문자열의 시작 위치 번호를 반환합니다."
                ],
                SIN: [
                    "SIN(number)",
                    "주어진 각도(라디안 단위)의 사인을 반환합니다."
                ],
                SMALL: [
                    "SMALL(배열, k)",
                    "데이터 세트에서 k번째로 작은 값을 반환합니다."
                ],
                SQRT: [
                    "SQRT(number)",
                    "양의 제곱근을 반환합니다."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "표본을 기반으로 표준 편차를 추정합니다.표준편차는 값이 평균값(평균)에서 얼마나 넓게 분산되어 있는지를 측정한 것입니다."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "인수로 주어진 전체 모집단을 기준으로 표준편차를 계산합니다."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(텍스트, 이전_텍스트, 새_텍스트, [인스턴스_숫자])",
                    "텍스트 문자열에서 old_text를 new_text로 대체합니다."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "인수를 추가합니다.개별 값, 셀 참조 또는 범위를 추가하거나 세 가지를 모두 혼합하여 추가할 수 있습니다."
                ],
                SUMIF: [
                    "SUMIF(범위, 기준, [합계_범위])",
                    "지정한 기준을 충족하는 범위의 값을 추가합니다."
                ],
                SUMIFS: [
                    "SUMIFS(합계_범위, 기준_범위1, 기준1, [기준_범위2, 기준2], ...)",
                    "여러 기준을 충족하는 모든 인수를 추가합니다."
                ],
                SUMPRODUCT: [
                    "SUMPRODUCT(배열1, [배열2], [배열3], ...)",
                    "주어진 배열의 해당 구성 요소를 곱하고 해당 곱의 합계를 반환합니다."
                ],
                TAN: [
                    "TAN(number)",
                    "주어진 각도의 탄젠트(라디안 단위)를 반환합니다."
                ],
                TEXT: [
                    "TEXT(형식을 지정하려는 값, \"적용하려는 코드 형식 지정\")",
                    "TEXT 함수를 사용하면 서식 코드를 사용하여 숫자에 서식을 적용하여 숫자가 표시되는 방식을 변경할 수 있습니다."
                ],
                TIME: [
                    "TIME(시, 분, 초)",
                    "특정 시간의 10진수를 반환합니다."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "텍스트 문자열이 나타내는 시간의 10진수를 반환합니다.십진수는 0(영)부터 0.99988426까지의 값으로, 0:00:00(오전 12:00:00)부터 23:59:59(오후 11:59:59)까지의 시간을 나타냅니다."
                ],
                TODAY: [
                    "TODAY()",
                    "현재 날짜의 일련번호를 반환합니다."
                ],
                TRIM: [
                    "TRIM(text)",
                    "단어 사이의 단일 공백을 제외하고 텍스트에서 모든 공백을 제거합니다."
                ],
                TRUNC: [
                    "TRUNC(숫자, [숫자_자리])",
                    "숫자의 소수 부분을 제거하여 숫자를 정수로 자릅니다."
                ],
                UPPER: [
                    "UPPER(text)",
                    "텍스트를 대문자로 변환합니다."
                ],
                VALUE: [
                    "VALUE(text)",
                    "숫자를 나타내는 텍스트 문자열을 숫자로 변환합니다."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "표본을 기반으로 분산을 추정합니다."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "전체 모집단을 기준으로 분산을 계산합니다."
                ],
                VLOOKUP: [
                    "VLOOKUP(lookup_value, table_array, col_index_num, [range_lookup])",
                    "행별로 테이블이나 범위에서 값을 조회합니다.예를 들어, 부품 번호로 자동차 부품 가격을 찾아보세요."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "날짜에 해당하는 연도를 반환합니다.연도는 1900-9999 범위의 정수로 반환됩니다."
                ]
            },
            strFreezeFirstCol: "첫 번째 열 고정",
            strFreezePanes: "창 고정",
            strFreezePanesUndo: "창 고정 해제",
            strFreezeTopRow: "첫 번째 행 고정",
            strFrozenCols: "동결된 기둥",
            strFrozenRows: "고정된 행",
            strFullScreen: "전체 화면",
            strGroup_fixCols: "열 수정",
            strGroup_grandSummary: "전체 요약",
            strGroup_header: "열을 여기로 끌어 해당 열을 기준으로 그룹화하십시오",
            strGroup_merge: "셀 병합",
            strHideCols: "열 숨기기",
            strHideRows: "행 숨기기",
            strImport: "데이터 가져오기",
            strImportPic: "플로팅 이미지 삽입",
            strImportPicCell: "셀에 이미지 삽입",
            strInsertColumn: "열 삽입",
            strInsertRow: "행 삽입",
            strInsertRows: "{0}개 행 삽입",
            strItalic: "기울임꼴 텍스트",
            strLabel: "라벨",
            strLink: "링크",
            strLoading: "로드 중",
            strLocal: "ko",
            strLockCells: "셀 잠금",
            strMenu: {
                export: "내보낼 수 있는 열",
                filter: "필터",
                hideCols: "보이는 열"
            },
            strMerge: "셀 병합",
            strName: "이름",
            strNextResult: "다음 결과",
            strNoRows: "표시 할 행이 없습니다.",
            strNothingFound: "아무것도 없습니다",
            strOR: "또는",
            strOk: "Ok",
            strOpen: "열기",
            strPaste: "붙여넣기",
            strPrevResult: "이전 결과",
            strRedo: "다시 실행",
            strRename: "이름 바꾸기",
            strSearch: "검색",
            strSelectAll: "모두 선택",
            strSelectedmatches: "{1} 일치 중 {0}을 (를) 선택했습니다.",
            strShowCols: "열 표시",
            strShowRows: "행 표시",
            strTP_aggPH: "집계 값 계산을위한 열 삭제",
            strTP_aggPane: "집계",
            strTP_colPH: "열 또는 x 축을 따라 그룹화하기 위해 여기에 열을 삭제하십시오",
            strTP_colPane: "그룹 열",
            strTP_pivot: "피벗 모드",
            strTP_rowPH: "행 또는 y 축을 따라 그룹화하기 위해 여기에 열을 삭제하십시오",
            strTP_rowPane: "그룹 행",
            strTabAdd: "새 시트",
            strTabClose: "시트 제거",
            strTabHide: "시트 숨기기",
            strTabName: "sheet{0}",
            strTabRemove: "{0}이(가) 영구적으로 삭제됩니다.\r\n확실합니까?",
            strTabRename: "시트 이름 바꾸기",
            strTabShow: "시트 표시",
            strTextColor: "텍스트 색상",
            strUnderline: "텍스트에 밑줄을 긋습니다",
            strUndo: "실행 취소",
            strUnhide: "숨기기 해제",
            strUnmerge: "셀 병합 취소",
            strUpdate: "업데이트",
            strWrap: "텍스트 줄 바꿈"
        },
        pager = pq.pqPager.regional.ko = {
            strDisplay: "{2} 요소 중 {0} ~ {1} 표시",
            strFirstPage: "첫 페이지",
            strLastPage: "마지막 페이지",
            strNextPage: "다음 페이지",
            strPage: "{1}의 {0} 페이지",
            strPrevPage: "이전 페이지",
            strRefresh: "새로 고침",
            strRpp: "페이지 당 레코드 수 : {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();