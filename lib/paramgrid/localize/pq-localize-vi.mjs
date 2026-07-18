import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.vi = {
            strAND: "VÀ",
            strAddColLeft: "Thêm cột bên trái",
            strAddColRight: "Thêm cột bên phải",
            strAddColumn: "Thêm cột",
            strAddRow: "Thêm hàng",
            strAddRowAbove: "Thêm hàng ở trên",
            strAddRowBelow: "Thêm hàng bên dưới",
            strAddRows: "Thêm {0} hàng",
            strAlign: {
                bottom: "Căn dưới",
                center: "Căn giữa",
                left: "Căn trái",
                right: "Căn phải",
                top: "Căn chỉnh trên cùng"
            },
            strAlignH: "Căn chỉnh theo chiều ngang",
            strAlignV: "Căn dọc",
            strApply: "Áp dụng",
            strBlanks: "( Khoảng trống )",
            strBold: "Văn bản in đậm",
            strBorder: "Biên giới",
            strBorderColor: "Màu viền",
            strBorderStyle: "Kiểu viền",
            strBorders: {
                all: "Tất cả các biên giới",
                bottom: "Đường viền dưới",
                horizontal: "Đường viền ngang",
                inner: "Đường viền trong",
                left: "Viền trái",
                none: "Không viền",
                outer: "Biên giới ngoài",
                right: "Đường viền phải",
                top: "Đường viền trên cùng",
                vertical: "Đường viền dọc"
            },
            strCancel: "Hủy bỏ",
            strClear: "Thông thoáng",
            strClearColor: "Màu sắc rõ ràng",
            strClearFilter: "Xóa bộ lọc",
            strClearText: "Xóa văn bản",
            strCollapse: "Thu gọn bảng điều khiển",
            strComment: "Bình luận",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Không có --",
                begin: "Bắt đầu với",
                between: "Ở giữa",
                contain: "Chứa",
                empty: "trống",
                end: "Kết thúc bằng",
                equal: "Bằng",
                great: "Lớn hơn",
                gte: "Lớn hơn hoặc bằng",
                less: "Ít hơn",
                lte: "Nhỏ hơn hoặc bằng",
                notbegin: "Không bắt đầu bằng",
                notcontain: "Không chứa",
                notempty: "Không trống",
                notend: "Không kết thúc bằng",
                notequal: "Không bằng",
                range: "[ Phạm vi ]",
                regexp: "Biểu thức chính quy"
            },
            strCopy: "Sao chép",
            strCut: "Cắt",
            strDelete: "Xóa",
            strDeleteColumn: "Xóa cột",
            strDeleteRow: "Xóa hàng",
            strDownloadXlsx: "Tải xuống tệp Excel",
            strEdit: "Chỉnh sửa",
            strExitFullScreen: "Thoát toàn màn hình",
            strExpand: "Bảng mở rộng",
            strExport: "Xuất dữ liệu",
            strFillColor: "Tô màu",
            strFontFamily: "Họ phông chữ",
            strFontSize: "Cỡ chữ",
            strFormat: "Định dạng ô",
            strFormatMenu: {
                Accounting: "Kế toán",
                Currency: "Tiền tệ",
                Custom: "tùy chỉnh",
                DateTime: "Ngày Giờ",
                Fixed: "thập phân",
                Fraction: "Phân số",
                FullDate: "Ngày đầy đủ",
                General: "chung",
                Int: "số nguyên",
                LongDate: "Ngày dài",
                MediumDate: "Ngày trung bình",
                Note: "Lưu ý: Các định dạng có * thay đổi tùy theo ngôn ngữ của người dùng.",
                Percent: "Tỷ lệ phần trăm",
                Scientific: "khoa học",
                ShortDate: "Ngày ngắn",
                Standard: "Tiêu chuẩn",
                Text: "văn bản",
                Time: "thời gian"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Trả về giá trị tuyệt đối của một số.Giá trị tuyệt đối của một số là số không có dấu."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Trả về arccosine hoặc cosin nghịch đảo của một số.Arccosine là góc có cosin bằng số.Góc trả về được tính bằng radian trong phạm vi từ 0 (không) đến pi."
                ],
                AND: [
                    "VÀ(logic1, [logic2], ...)",
                    "Trả về TRUE nếu tất cả đối số của nó đánh giá là TRUE và trả về FALSE nếu một hoặc nhiều đối số đánh giá là FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Trả về arcsine hoặc sin nghịch đảo của một số.Arcsine là góc có sin bằng số.Góc trả về được tính bằng radian trong phạm vi -pi/2 đến pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Trả về arctang hoặc tang nghịch đảo của một số.Arctang là góc có tiếp tuyến bằng số.Góc trả về được tính bằng radian trong phạm vi -pi/2 đến pi/2."
                ],
                AVERAGE: [
                    "TRUNG BÌNH(số1, [số2], ...)",
                    "Trả về giá trị trung bình (trung bình số học) của các đối số.Ví dụ: nếu phạm vi A1:A20 chứa các số thì công thức =AVERAGE(A1:A20) trả về giá trị trung bình của các số đó."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(phạm vi, tiêu chí, [phạm vi_trung bình])",
                    "Trả về giá trị trung bình (trung bình số học) của tất cả các ô trong một phạm vi đáp ứng tiêu chí nhất định."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(phạm vi trung bình, phạm vi tiêu chí1, tiêu chí1, [dải_tiêu chí2, tiêu chí2], ...)",
                    "Trả về giá trị trung bình (trung bình số học) của tất cả các ô đáp ứng nhiều tiêu chí."
                ],
                CEILING: [
                    "TRẦN(số, ý nghĩa)",
                    "Trả về số được làm tròn, cách xa số 0, đến bội số có nghĩa gần nhất.Ví dụ: nếu bạn muốn tránh sử dụng đồng xu trong giá và sản phẩm của bạn có giá 4,42 USD, hãy sử dụng công thức =CEILING(4,42,0,05) để làm tròn giá lên đến niken gần nhất."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Trả về ký tự được chỉ định bởi một số."
                ],
                CHOOSE: [
                    "CHỌN(index_num, value1, [value2], ...)",
                    "Nếu chỉ số_num là 1, CHỌN trả về giá trị1;nếu là 2, CHOOSE trả về giá trị2;và vân vân."
                ],
                CODE: [
                    "CODE(text)",
                    "Trả về mã số cho ký tự đầu tiên trong chuỗi văn bản."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Trả về tham chiếu của ô nơi hàm COLUMN xuất hiện."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Trả về số cột trong một mảng hoặc tham chiếu."
                ],
                CONCATENATE: [
                    "CONCATENATE(văn bản1, [văn bản2], ...)",
                    "nối hai hoặc nhiều chuỗi văn bản thành một chuỗi."
                ],
                COS: [
                    "COS(number)",
                    "Trả về cosin của một góc đã cho (tính bằng radian)."
                ],
                COUNT: [
                    "ĐẾM(giá trị1, [giá trị2], ...)",
                    "Đếm số ô chứa số và đếm số trong danh sách đối số."
                ],
                COUNTA: [
                    "COUNTA(giá trị1, [giá trị2], ...)",
                    "Hàm COUNTA đếm số ô không trống trong một phạm vi."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Đếm các ô trống trong một phạm vi ô được chỉ định."
                ],
                COUNTIF: [
                    "COUNTIF(phạm vi, tiêu chí)",
                    "đếm số ô đáp ứng tiêu chí;ví dụ: để đếm số lần một thành phố cụ thể xuất hiện trong danh sách khách hàng."
                ],
                COUNTIFS: [
                    "COUNTIFS(tiêu chí_phạm vi1, tiêu chí1, [tiêu chí_phạm vi2, tiêu chí2]…)",
                    "Áp dụng tiêu chí cho các ô trên nhiều phạm vi và đếm số lần tất cả các tiêu chí được đáp ứng."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "trả về số sê-ri tuần tự đại diện cho một ngày cụ thể."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Tính số ngày, tháng hoặc năm giữa hai ngày.Đơn vị có thể là 'Y', 'M' hoặc 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "chuyển đổi ngày được lưu dưới dạng văn bản thành số sê-ri mà Excel nhận dạng là ngày.Ví dụ: công thức =DATEVALUE(\"1/1/2008\") trả về 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Trả về ngày trong ngày, được biểu thị bằng số sê-ri."
                ],
                DAYS: [
                    "DAYS(ngày_kết thúc, ngày_bắt đầu)",
                    "Trả về số ngày giữa hai ngày."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Chuyển đổi radian thành độ."
                ],
                EOMONTH: [
                    "EOMONTH(ngày_bắt đầu, tháng)",
                    "Trả về số sê-ri của ngày cuối cùng của tháng là số tháng được chỉ định trước hoặc sau ngày_bắt đầu."
                ],
                EXP: [
                    "EXP(number)",
                    "Trả về e lũy thừa của số."
                ],
                FIND: [
                    "TÌM(find_text, Inside_text, [start_num])",
                    "Xác định vị trí một chuỗi văn bản trong chuỗi văn bản thứ hai và trả về số vị trí bắt đầu của chuỗi văn bản đầu tiên tính từ ký tự đầu tiên của chuỗi văn bản thứ hai."
                ],
                FLOOR: [
                    "TẦNG(số, ý nghĩa)",
                    "Làm tròn số xuống, về 0, đến bội số có ý nghĩa gần nhất."
                ],
                HLOOKUP: [
                    "HLOOKUP(giá trị tra cứu, mảng_bảng, số_chỉ mục hàng, [dải_tra cứu])",
                    "Tìm kiếm một giá trị ở hàng trên cùng của bảng hoặc một mảng giá trị, sau đó trả về một giá trị trong cùng một cột từ một hàng bạn chỉ định trong bảng hoặc mảng."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Trả về giờ của một giá trị thời gian.Giờ được đưa ra dưới dạng số nguyên, từ 0 (12:00 A.M.) đến 23 (11:00 P.M.)."
                ],
                HYPERLINK: [
                    "HYPERLINK(url, [tên_thân thiện])",
                    "tạo một lối tắt nhảy đến một vị trí khác trên Internet khi bạn bấm vào một ô"
                ],
                IF: [
                    "IF(kiểm tra logic, value_if_true, [value_if_false])",
                    "trả về một giá trị nếu điều kiện đúng và giá trị khác nếu điều kiện sai."
                ],
                INDEX: [
                    "INDEX(mảng, row_num, [column_num])",
                    "Trả về giá trị của một phần tử trong bảng hoặc mảng, được chọn theo chỉ mục số hàng và cột."
                ],
                INDIRECT: [
                    "GIÁN TIẾP(ref_text, [a1])",
                    "Trả về tham chiếu được chỉ định bởi một chuỗi văn bản.Tài liệu tham khảo được đánh giá ngay lập tức để hiển thị nội dung của chúng."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Trả về TRUE nếu giá trị trống"
                ],
                LARGE: [
                    "LỚN(mảng, k)",
                    "Trả về giá trị lớn thứ k trong tập dữ liệu."
                ],
                LEFT: [
                    "TRÁI(văn bản, [num_chars])",
                    "trả về ký tự hoặc các ký tự đầu tiên trong chuỗi văn bản, dựa trên số ký tự bạn chỉ định."
                ],
                LEN: [
                    "LEN(text)",
                    "trả về số ký tự trong một chuỗi văn bản."
                ],
                LOOKUP: [
                    "LOOKUP(lookup_value, lookup_vector, [result_vector])",
                    "Tìm kiếm một giá trị trong phạm vi một hàng hoặc một cột (được gọi là vectơ) và trả về một giá trị từ cùng một vị trí trong phạm vi một hàng hoặc một cột thứ hai."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Chuyển đổi tất cả các chữ hoa trong chuỗi văn bản thành chữ thường."
                ],
                MATCH: [
                    "MATCH(giá trị tra cứu, mảng tra cứu, [loại_kết hợp])",
                    "Tìm kiếm một mục được chỉ định trong một phạm vi ô, sau đó trả về vị trí tương đối của mục đó trong phạm vi.match_type có thể là: 0 để khớp chính xác với tùy chọn sử dụng ký tự đại diện;1 ( mặc định ) cho ít hơn, Các giá trị trong đối số lookup_array phải được đặt theo thứ tự tăng dần;-1 nếu lớn hơn, các giá trị trong đối số lookup_array phải được đặt theo thứ tự giảm dần."
                ],
                MAX: [
                    "MAX(số1, [số2], ...)",
                    "Trả về giá trị lớn nhất trong một tập hợp các giá trị."
                ],
                MEDIAN: [
                    "MEDIAN(số1, [số2], ...)",
                    "Trả về giá trị trung bình của các số đã cho.Số trung vị là số ở giữa một tập hợp số."
                ],
                MID: [
                    "MID(văn bản, số bắt đầu, số_ký tự)",
                    "trả về một số ký tự cụ thể từ một chuỗi văn bản, bắt đầu từ vị trí bạn chỉ định, dựa trên số ký tự bạn chỉ định."
                ],
                MIN: [
                    "MIN(số1, [số2], ...)",
                    "Trả về số nhỏ nhất trong một tập hợp các giá trị."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Trả về giá trị xảy ra thường xuyên nhất hoặc lặp đi lặp lại trong một mảng hoặc phạm vi dữ liệu."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Trả về tháng của một ngày được biểu thị bằng số sê-ri.Tháng được đưa ra dưới dạng số nguyên, từ 1 (tháng 1) đến 12 (tháng 12)."
                ],
                OR: [
                    "HOẶC(logic1, [logic2], ...)",
                    "trả về TRUE nếu bất kỳ đối số nào của nó đánh giá là TRUE và trả về FALSE nếu tất cả các đối số của nó đánh giá là FALSE."
                ],
                PI: [
                    "PI()",
                    "Trả về số 3.14159265358979, hằng số toán học pi."
                ],
                POWER: [
                    "SỨC MẠNH(số, sức mạnh)",
                    "Trả về kết quả của một số được lũy thừa."
                ],
                PRODUCT: [
                    "SẢN PHẨM(số1, [số2], ...)",
                    "nhân tất cả các số đã cho làm đối số và trả về kết quả.SẢN PHẨM(A1:A3, C1:C3) tương đương với =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Viết hoa chữ cái đầu tiên trong mỗi từ của giá trị văn bản."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Chuyển đổi độ thành radian."
                ],
                RAND: [
                    "RAND()",
                    "Trả về một số thực ngẫu nhiên được phân bổ đều lớn hơn hoặc bằng 0 và nhỏ hơn 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Trả về thứ hạng của một số trong danh sách các số."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REPLACE(văn bản cũ, số bắt đầu, ký tự số, văn bản mới)",
                    "thay thế một phần của chuỗi văn bản, dựa trên số ký tự bạn chỉ định, bằng một chuỗi văn bản khác."
                ],
                REPT: [
                    "REPT(văn bản, số_lần)",
                    "Lặp lại văn bản một số lần nhất định."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "trả về ký tự cuối cùng hoặc các ký tự trong chuỗi văn bản, dựa trên số ký tự bạn chỉ định."
                ],
                ROUND: [
                    "VÒNG(số, num_chữ số)",
                    "làm tròn một số đến một số chữ số xác định."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(số, num_chữ số)",
                    "Làm tròn một số xuống, về 0."
                ],
                ROUNDUP: [
                    "ROUNDUP(số, num_chữ số)",
                    "Làm tròn một số lên, cách xa 0 (không)."
                ],
                ROW: [
                    "ROW()",
                    "Trả về tham chiếu của ô nơi hàm ROW xuất hiện."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Trả về số hàng trong một tham chiếu hoặc mảng."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "định vị một chuỗi văn bản trong chuỗi văn bản thứ hai và trả về số vị trí bắt đầu của chuỗi văn bản đầu tiên tính từ ký tự đầu tiên của chuỗi văn bản thứ hai."
                ],
                SIN: [
                    "SIN(number)",
                    "Trả về sin của một góc đã cho (tính bằng radian)."
                ],
                SMALL: [
                    "NHỎ(mảng, k)",
                    "Trả về giá trị nhỏ thứ k trong tập dữ liệu."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Trả về căn bậc hai dương."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Ước tính độ lệch chuẩn dựa trên một mẫu.Độ lệch chuẩn là thước đo mức độ phân tán của các giá trị so với giá trị trung bình (giá trị trung bình)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Tính toán độ lệch chuẩn dựa trên toàn bộ tập hợp được đưa ra dưới dạng đối số."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(văn bản, văn bản cũ, văn bản mới, [instance_num])",
                    "Thay thế new_text cho old_text trong chuỗi văn bản."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Thêm đối số của nó.Bạn có thể thêm các giá trị riêng lẻ, tham chiếu ô hoặc phạm vi ô hoặc kết hợp cả ba."
                ],
                SUMIF: [
                    "SUMIF(phạm vi, tiêu chí, [tổng_phạm vi])",
                    "thêm các giá trị trong một phạm vi đáp ứng tiêu chí mà bạn chỉ định"
                ],
                SUMIFS: [
                    "SUMIFS(phạm vi tổng, phạm vi tiêu chí1, tiêu chí1, [dải_tiêu chí2, tiêu chí2], ...)",
                    "thêm tất cả các đối số đáp ứng nhiều tiêu chí của nó."
                ],
                SUMPRODUCT: [
                    "TỔNG HỢP(mảng1, [mảng2], [mảng3], ...)",
                    "Nhân các thành phần tương ứng trong các mảng đã cho và trả về tổng của các tích đó."
                ],
                TAN: [
                    "TAN(number)",
                    "Trả về tang của một góc đã cho (tính bằng radian)."
                ],
                TEXT: [
                    "TEXT(Giá trị bạn muốn định dạng, \"Mã định dạng bạn muốn áp dụng\")",
                    "Hàm TEXT cho phép bạn thay đổi cách hiển thị số bằng cách áp dụng định dạng cho số đó bằng mã định dạng."
                ],
                TIME: [
                    "THỜI GIAN (giờ, phút, giây)",
                    "Trả về số thập phân cho một thời gian cụ thể."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Trả về số thập phân của thời gian được biểu thị bằng một chuỗi văn bản.Số thập phân là một giá trị nằm trong khoảng từ 0 (không) đến 0,99988426, biểu thị thời gian từ 0:00:00 (12:00:00 AM) đến 23:59:59 (11:59:59 P.M.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Trả về số sê-ri của ngày hiện tại."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Xóa tất cả khoảng trắng khỏi văn bản ngoại trừ khoảng trắng đơn giữa các từ."
                ],
                TRUNC: [
                    "TRUNC(số, [num_chữ số])",
                    "Cắt bớt một số thành số nguyên bằng cách loại bỏ phần phân số của số đó."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Chuyển đổi văn bản thành chữ hoa."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Chuyển đổi một chuỗi văn bản đại diện cho một số thành một số."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Ước tính phương sai dựa trên một mẫu."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Tính toán phương sai dựa trên toàn bộ tập hợp."
                ],
                VLOOKUP: [
                    "VLOOKUP (lookup_value, table_array, col_index_num, [range_lookup])",
                    "tra cứu một giá trị trong một bảng hoặc một phạm vi theo hàng.Ví dụ: tra cứu giá của một bộ phận ô tô theo số bộ phận."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Trả về năm tương ứng với một ngày.Năm được trả về dưới dạng số nguyên trong khoảng 1900-9999."
                ]
            },
            strFreezeFirstCol: "Cố định cột đầu tiên",
            strFreezePanes: "Tấm đóng băng",
            strFreezePanesUndo: "Giải phóng các ngăn",
            strFreezeTopRow: "Cố định hàng đầu tiên",
            strFrozenCols: "Cột đông lạnh",
            strFrozenRows: "Hàng đông lạnh",
            strFullScreen: "Toàn màn hình",
            strGroup_fixCols: "Sửa cột",
            strGroup_grandSummary: "Tóm tắt lớn",
            strGroup_header: "Thả nhóm theo cột",
            strGroup_merge: "Hợp nhất các ô",
            strHideCols: "Ẩn cột",
            strHideRows: "Ẩn hàng",
            strImport: "Nhập dữ liệu",
            strImportPic: "Chèn hình ảnh nổi",
            strImportPicCell: "Chèn hình ảnh vào ô",
            strInsertColumn: "Chèn cột",
            strInsertRow: "Chèn hàng",
            strInsertRows: "Chèn {0} hàng",
            strItalic: "Văn bản in nghiêng",
            strLabel: "Nhãn",
            strLink: "liên kết",
            strLoading: "Đang tải",
            strLocal: "vi",
            strLockCells: "Khóa ô",
            strMenu: {
                export: "Cột có thể xuất",
                filter: "Lọc",
                hideCols: "Cột hiển thị"
            },
            strMerge: "Hợp nhất các ô",
            strName: "Tên",
            strNextResult: "Kết quả tiếp theo",
            strNoRows: "Không có hàng để hiển thị.",
            strNothingFound: "Không tìm thấy gì",
            strOR: "HOẶC",
            strOk: "Được rồi",
            strOpen: "Mở",
            strPaste: "Dán",
            strPrevResult: "Kết quả trước đó",
            strRedo: "Làm lại",
            strRename: "Đổi tên",
            strSearch: "Tìm kiếm",
            strSelectAll: "Chọn tất cả",
            strSelectedmatches: "Đã chọn {0} trong số {1} kết quả phù hợp",
            strShowCols: "Hiển thị cột",
            strShowRows: "Hiển thị hàng",
            strTP_aggPH: "Thả cột cho tổng hợp",
            strTP_aggPane: "Tập hợp",
            strTP_colPH: "Thả cột để nhóm cột",
            strTP_colPane: "Nhóm cột",
            strTP_pivot: "Chế độ xoay",
            strTP_rowPH: "Thả cột để nhóm hàng",
            strTP_rowPane: "Nhóm hàng",
            strTabAdd: "Trang tính mới",
            strTabClose: "Xóa trang tính",
            strTabHide: "Ẩn trang tính",
            strTabName: "sheet{0}",
            strTabRemove: "{0} sẽ bị xóa vĩnh viễn.\r\nBạn có chắc không?",
            strTabRename: "Đổi tên trang tính",
            strTabShow: "Hiển thị trang tính",
            strTextColor: "Màu văn bản",
            strUnderline: "Gạch chân văn bản",
            strUndo: "Hoàn tác",
            strUnhide: "Bỏ ẩn",
            strUnmerge: "Hủy hợp nhất các ô",
            strUpdate: "cập nhật",
            strWrap: "Gói văn bản"
        },
        pager = pq.pqPager.regional.vi = {
            strDisplay: "Hiển thị {0} đến {1} trong số {2} mục.",
            strFirstPage: "Trang đầu tiên",
            strLastPage: "Trang cuối cùng",
            strNextPage: "Trang tiếp theo",
            strPage: "Trang {0} trên {1}",
            strPrevPage: "Trang trước",
            strRefresh: "Làm mới",
            strRpp: "Bản ghi trên mỗi trang: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);