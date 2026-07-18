(function() {
    let $;
    if (typeof require == "function" && typeof module == "object") {
        $ = typeof jQuery != "undefined" ? jQuery : require("pqgrid").$;
    } else {
        $ = window.jQuery;
    }

    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.es = {
            strAND: "Y",
            strAddColLeft: "Agregar columna a la izquierda",
            strAddColRight: "Agregar columna a la derecha",
            strAddColumn: "Agregar columna",
            strAddRow: "Agregar fila",
            strAddRowAbove: "Añadir fila arriba",
            strAddRowBelow: "Agregar fila a continuación",
            strAddRows: "Agregar {0} filas",
            strAlign: {
                bottom: "Alineación inferior",
                center: "Alinear al centro",
                left: "Alinear a la izquierda",
                right: "alinear a la derecha",
                top: "Alineación superior"
            },
            strAlignH: "Alineación horizontal",
            strAlignV: "Alineación vertical",
            strApply: "Aplicar",
            strBlanks: "( Espacios en blanco )",
            strBold: "Texto en negrita",
            strBorder: "frontera",
            strBorderColor: "Color del borde",
            strBorderStyle: "Estilo de borde",
            strBorders: {
                all: "Todas las fronteras",
                bottom: "Borde inferior",
                horizontal: "Borde Horizontal",
                inner: "Borde interior",
                left: "Borde izquierdo",
                none: "Sin fronteras",
                outer: "Frontera exterior",
                right: "Borde derecho",
                top: "Borde superior",
                vertical: "Borde vertical"
            },
            strCancel: "Cancelar",
            strClear: "Clara",
            strClearColor: "Color claro",
            strClearFilter: "Borrar filtro",
            strClearText: "Borrar texto",
            strCollapse: "Contraer panel",
            strComment: "Comentario",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Ninguno --",
                begin: "comienza con",
                between: "En el medio",
                contain: "Contiene",
                empty: "vacio",
                end: "termina con",
                equal: "igual",
                great: "mayor que",
                gte: "Mayor o igual que",
                less: "menos de",
                lte: "Menor o igual",
                notbegin: "No comienza con",
                notcontain: "No contiene",
                notempty: "No vacío",
                notend: "No termina con",
                notequal: "no es igual",
                range: "[Rango]",
                regexp: "expresión regular"
            },
            strCopy: "Copiar",
            strCut: "cortar",
            strDelete: "borrar",
            strDeleteColumn: "Eliminar columna",
            strDeleteRow: "Eliminar fila",
            strDownloadXlsx: "Descargar archivo Excel",
            strEdit: "editar",
            strExitFullScreen: "Salir de pantalla completa",
            strExpand: "Expandir panel",
            strExport: "Exportar datos",
            strFillColor: "Color de relleno",
            strFontFamily: "Familia de fuentes",
            strFontSize: "Tamaño de fuente",
            strFormat: "Formatear celdas",
            strFormatMenu: {
                Accounting: "contabilidad",
                Currency: "Moneda",
                Custom: "personalizado",
                DateTime: "Fecha Hora",
                Fixed: "decimales",
                Fraction: "fracción",
                FullDate: "Fecha completa",
                General: "generales",
                Int: "Entero",
                LongDate: "cita larga",
                MediumDate: "Fecha media",
                Note: "Nota: Los formatos con * cambian según la configuración regional del usuario.",
                Percent: "Porcentaje",
                Scientific: "Científico",
                ShortDate: "cita corta",
                Standard: "Estándar",
                Text: "Texto",
                Time: "tiempo"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Devuelve el valor absoluto de un número.El valor absoluto de un número es el número sin su signo."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Devuelve el arcocoseno, o coseno inverso, de un número.El arcocoseno es el ángulo cuyo coseno es el número.El ángulo devuelto se da en radianes en el rango de 0 (cero) a pi."
                ],
                AND: [
                    "Y(lógico1, [lógico2], ...)",
                    "Devuelve VERDADERO si todos sus argumentos se evalúan como VERDADERO y devuelve FALSO si uno o más argumentos se evalúan como FALSO."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Devuelve el arcoseno o seno inverso de un número.El arcoseno es el ángulo cuyo seno es número.El ángulo devuelto se da en radianes en el rango de -pi/2 a pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Devuelve el arcotangente o tangente inversa de un número.El arcotangente es el ángulo cuya tangente es el número.El ángulo devuelto se da en radianes en el rango de -pi/2 a pi/2."
                ],
                AVERAGE: [
                    "PROMEDIO(número1, [número2], ...)",
                    "Devuelve el promedio (media aritmética) de los argumentos.Por ejemplo, si el rango A1:A20 contiene números, la fórmula =PROMEDIO(A1:A20) devuelve el promedio de esos números."
                ],
                AVERAGEIF: [
                    "PROMEDIOSI(rango, criterios, [rango_promedio])",
                    "Devuelve el promedio (media aritmética) de todas las celdas de un rango que cumplen un criterio determinado."
                ],
                AVERAGEIFS: [
                    "PROMEDIOIFS(rango_promedio, rango_criterio1, criterio1, [rango_criterio2, criterio2], ...)",
                    "Devuelve el promedio (media aritmética) de todas las celdas que cumplen varios criterios."
                ],
                CEILING: [
                    "TECHO(número, significado)",
                    "Devuelve el número redondeado hacia arriba, desde cero, al múltiplo significativo más cercano.Por ejemplo, si desea evitar el uso de centavos en sus precios y su producto tiene un precio de $4,42, use la fórmula =TECHO(4,42,0,05) para redondear los precios al níquel más cercano."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Devuelve el carácter especificado por un número."
                ],
                CHOOSE: [
                    "ELEGIR(núm_índice, valor1, [valor2], ...)",
                    "Si index_num es 1, ELEGIR devuelve valor1;si es 2, ELEGIR devuelve valor2;etcétera."
                ],
                CODE: [
                    "CODE(text)",
                    "Devuelve un código numérico para el primer carácter de una cadena de texto."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Devuelve la referencia de la celda en la que aparece la función COLUMNA."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Devuelve el número de columnas de una matriz o referencia."
                ],
                CONCATENATE: [
                    "CONCATENAR(texto1, [texto2], ...)",
                    "unir dos o más cadenas de texto en una sola cadena."
                ],
                COS: [
                    "COS(number)",
                    "Devuelve el coseno del ángulo dado (en radianes)."
                ],
                COUNT: [
                    "CONTAR(valor1, [valor2], ...)",
                    "Cuenta la cantidad de celdas que contienen números y cuenta los números dentro de la lista de argumentos."
                ],
                COUNTA: [
                    "CONTARA(valor1, [valor2], ...)",
                    "La función CONTAR cuenta el número de celdas que no están vacías en un rango."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Cuenta celdas vacías en un rango de celdas específico."
                ],
                COUNTIF: [
                    "CONTAR.SI(rango,criterios)",
                    "cuenta el número de celdas que cumplen un criterio;por ejemplo, para contar el número de veces que una ciudad concreta aparece en una lista de clientes."
                ],
                COUNTIFS: [
                    "CONTAR.SI(rango_criterios1, criterio1, [rango_criterio2, criterio2]…)",
                    "Aplica criterios a celdas en múltiples rangos y cuenta el número de veces que se cumplen todos los criterios."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "devuelve el número de serie secuencial que representa una fecha particular."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Calcula el número de días, meses o años entre dos fechas.La unidad puede ser 'Y', 'M' o 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "convierte una fecha almacenada como texto en un número de serie que Excel reconoce como una fecha.Por ejemplo, la fórmula =FECHAVALUE(\"1/1/2008\") devuelve 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Devuelve el día de una fecha, representado por un número de serie."
                ],
                DAYS: [
                    "DÍAS (fecha_finalización, fecha_inicio)",
                    "Devuelve el número de días entre dos fechas."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Convierte radianes en grados."
                ],
                EOMONTH: [
                    "EOMES(fecha_inicio, meses)",
                    "Devuelve el número de serie del último día del mes que es el número de meses indicado antes o después de la fecha_inicio."
                ],
                EXP: [
                    "EXP(number)",
                    "Devuelve e elevado a la potencia del número."
                ],
                FIND: [
                    "ENCONTRAR(buscar_texto, dentro_texto, [núm_inicio])",
                    "Ubica una cadena de texto dentro de una segunda cadena de texto y devuelve el número de la posición inicial de la primera cadena de texto a partir del primer carácter de la segunda cadena de texto."
                ],
                FLOOR: [
                    "PISO(número, significado)",
                    "Redondea el número hacia abajo, hacia cero, al múltiplo significativo más cercano."
                ],
                HLOOKUP: [
                    "BUSCARH(valor_búsqueda, matriz_tabla, núm_índice_fila, [rango_búsqueda])",
                    "Busca un valor en la fila superior de una tabla o una matriz de valores y luego devuelve un valor en la misma columna de una fila que especifique en la tabla o matriz."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Devuelve la hora de un valor de tiempo.La hora se da como un número entero, que va de 0 (12:00 a. m.) a 23 (11:00 p. m.)."
                ],
                HYPERLINK: [
                    "HIPERVÍNCULO(url, [nombre_amigable])",
                    "crea un acceso directo que salta a otra ubicación en Internet cuando haces clic en una celda"
                ],
                IF: [
                    "SI(prueba_lógica, valor_si_verdadero, [valor_si_falso])",
                    "Devuelve un valor si una condición es verdadera y otro valor si es falsa."
                ],
                INDEX: [
                    "ÍNDICE (matriz, núm_fila, [núm_columna])",
                    "Devuelve el valor de un elemento en una tabla o matriz, seleccionado por los índices de número de fila y columna."
                ],
                INDIRECT: [
                    "INDIRECTO(texto_ref, [a1])",
                    "Devuelve la referencia especificada por una cadena de texto.Las referencias se evalúan inmediatamente para mostrar su contenido."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Devuelve VERDADERO si el valor está en blanco"
                ],
                LARGE: [
                    "GRANDE(matriz, k)",
                    "Devuelve el k-ésimo valor más grande de un conjunto de datos."
                ],
                LEFT: [
                    "IZQUIERDA(texto, [núm_caracteres])",
                    "Devuelve el primer carácter o caracteres de una cadena de texto, según el número de caracteres que especifique."
                ],
                LEN: [
                    "LEN(text)",
                    "devuelve el número de caracteres en una cadena de texto."
                ],
                LOOKUP: [
                    "BUSCAR(valor_buscado, vector_buscado, [vector_resultado])",
                    "Busca un valor en un rango de una fila o una columna (conocido como vector) y devuelve un valor de la misma posición en un segundo rango de una fila o una columna."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Convierte todas las letras mayúsculas de una cadena de texto a minúsculas."
                ],
                MATCH: [
                    "COINCIDIR(valor_buscado, matriz_buscada, [tipo_coincidencia])",
                    "Busca un elemento específico en un rango de celdas y luego devuelve la posición relativa de ese elemento en el rango.match_type puede ser: 0 para coincidencia exacta con opción de utilizar comodines;1 (predeterminado) para menos de. Los valores en el argumento lookup_array deben colocarse en orden ascendente;-1 para mayor que, los valores en el argumento lookup_array deben colocarse en orden descendente."
                ],
                MAX: [
                    "MAX(número1, [número2], ...)",
                    "Devuelve el valor más grande de un conjunto de valores."
                ],
                MEDIAN: [
                    "MEDIANA(número1, [número2], ...)",
                    "Devuelve la mediana de los números dados.La mediana es el número que se encuentra en medio de un conjunto de números."
                ],
                MID: [
                    "MID(texto, núm_inicio, núm_caracteres)",
                    "devuelve una cantidad específica de caracteres de una cadena de texto, comenzando en la posición que especifique, según la cantidad de caracteres que especifique."
                ],
                MIN: [
                    "MIN(número1, [número2], ...)",
                    "Devuelve el número más pequeño de un conjunto de valores."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Devuelve el valor que ocurre con más frecuencia o que se repite en una matriz o rango de datos."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Devuelve el mes de una fecha representada por un número de serie.El mes se expresa como un número entero, que va del 1 (enero) al 12 (diciembre)."
                ],
                OR: [
                    "O (lógico1, [lógico2], ...)",
                    "devuelve VERDADERO si alguno de sus argumentos se evalúa como VERDADERO y devuelve FALSO si todos sus argumentos se evalúan como FALSO."
                ],
                PI: [
                    "PI()",
                    "Devuelve el número 3,14159265358979, la constante matemática pi."
                ],
                POWER: [
                    "POTENCIA(número, potencia)",
                    "Devuelve el resultado de un número elevado a una potencia."
                ],
                PRODUCT: [
                    "PRODUCTO(número1, [número2], ...)",
                    "multiplica todos los números dados como argumentos y devuelve el producto.PRODUCTO(A1:A3, C1:C3) es equivalente a =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Pone en mayúscula la primera letra de cada palabra de un valor de texto."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Convierte grados en radianes."
                ],
                RAND: [
                    "RAND()",
                    "Devuelve un número real aleatorio distribuido uniformemente mayor o igual a 0 y menor que 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Devuelve el rango de un número en una lista de números."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "REEMPLAZAR (texto_antiguo, núm_inicio, núm_caracteres, texto_nuevo)",
                    "reemplaza parte de una cadena de texto, según la cantidad de caracteres que especifique, con una cadena de texto diferente."
                ],
                REPT: [
                    "REPT(texto, número_veces)",
                    "Repite el texto un número determinado de veces."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "devuelve el último carácter o caracteres de una cadena de texto, según el número de caracteres que especifique."
                ],
                ROUND: [
                    "REDONDEAR(número, núm_dígitos)",
                    "redondea un número a un número específico de dígitos."
                ],
                ROUNDDOWN: [
                    "REDONDEAR HACIA ABAJO(número, núm_dígitos)",
                    "Redondea un número hacia abajo, hacia cero."
                ],
                ROUNDUP: [
                    "REDONDEAR(número, núm_dígitos)",
                    "Redondea un número hacia arriba, alejándolo de 0 (cero)."
                ],
                ROW: [
                    "ROW()",
                    "Devuelve la referencia de la celda en la que aparece la función FILA."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Devuelve el número de filas de una referencia o matriz."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "ubique una cadena de texto dentro de una segunda cadena de texto y devuelva el número de la posición inicial de la primera cadena de texto a partir del primer carácter de la segunda cadena de texto."
                ],
                SIN: [
                    "SIN(number)",
                    "Devuelve el seno del ángulo dado (en radianes)."
                ],
                SMALL: [
                    "PEQUEÑO(matriz, k)",
                    "Devuelve el k-ésimo valor más pequeño de un conjunto de datos."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Devuelve una raíz cuadrada positiva."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Estima la desviación estándar con base en una muestra.La desviación estándar es una medida de cuán ampliamente se dispersan los valores del valor promedio (la media)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Calcula la desviación estándar basándose en toda la población dada como argumentos."
                ],
                SUBSTITUTE: [
                    "SUSTITUIR(texto, texto_antiguo, texto_nuevo, [núm_instancia])",
                    "Sustituye texto_nuevo por texto_antiguo en una cadena de texto."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Añade sus argumentos.Puede agregar valores individuales, referencias de celdas o rangos o una combinación de los tres."
                ],
                SUMIF: [
                    "SUMAR.SI(rango, criterios, [rango_suma])",
                    "agrega los valores en un rango que cumplen con los criterios que usted especifica"
                ],
                SUMIFS: [
                    "SUMIFS(rango_suma, rango_criterios1, criterios1, [rango_criterios2, criterios2], ...)",
                    "agrega todos sus argumentos que cumplen múltiples criterios."
                ],
                SUMPRODUCT: [
                    "SUMAPRODUCTO(matriz1, [matriz2], [matriz3], ...)",
                    "Multiplica los componentes correspondientes en las matrices dadas y devuelve la suma de esos productos."
                ],
                TAN: [
                    "TAN(number)",
                    "Devuelve la tangente del ángulo dado (en radianes)."
                ],
                TEXT: [
                    "TEXTO(Valor que desea formatear, \"Código de formato que desea aplicar\")",
                    "La función TEXTO le permite cambiar la forma en que aparece un número aplicándole formato con códigos de formato."
                ],
                TIME: [
                    "TIEMPO(hora, minuto, segundo)",
                    "Devuelve el número decimal de un momento determinado."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Devuelve el número decimal del tiempo representado por una cadena de texto.El número decimal es un valor que va de 0 (cero) a 0,99988426, y representa los tiempos desde las 0:00:00 (12:00:00 a. m.) hasta las 23:59:59 (23:59:59 p. m.)."
                ],
                TODAY: [
                    "TODAY()",
                    "Devuelve el número de serie de la fecha actual."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Elimina todos los espacios del texto excepto los espacios simples entre palabras."
                ],
                TRUNC: [
                    "TRUNC(número, [núm_dígitos])",
                    "Trunca un número a un número entero eliminando la parte fraccionaria del número."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Convierte texto a mayúsculas."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Convierte una cadena de texto que representa un número en un número."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Estima la varianza basándose en una muestra."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Calcula la varianza en función de toda la población."
                ],
                VLOOKUP: [
                    "BUSCARV (valor_búsqueda, matriz_tabla, núm_índice_columna, [búsqueda_rango])",
                    "buscar un valor en una tabla o un rango por fila.Por ejemplo, busque el precio de una pieza de automóvil por el número de pieza."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Devuelve el año correspondiente a una fecha.El año se devuelve como un número entero en el rango 1900-9999."
                ]
            },
            strFreezeFirstCol: "Congelar la primera columna",
            strFreezePanes: "Congelar paneles",
            strFreezePanesUndo: "Descongelar paneles",
            strFreezeTopRow: "Congelar la primera fila",
            strFrozenCols: "Columnas congeladas",
            strFrozenRows: "Filas congeladas",
            strFullScreen: "Pantalla completa",
            strGroup_fixCols: "Arreglar columnas",
            strGroup_grandSummary: "Gran resumen",
            strGroup_header: "Eliminar grupo por columna",
            strGroup_merge: "Fusionar celdas",
            strHideCols: "Ocultar columnas",
            strHideRows: "Ocultar filas",
            strImport: "Importar datos",
            strImportPic: "Insertar imagen flotante",
            strImportPicCell: "Insertar imagen en celda",
            strInsertColumn: "Insertar columna",
            strInsertRow: "Insertar fila",
            strInsertRows: "Insertar {0} filas",
            strItalic: "Texto en cursiva",
            strLabel: "Etiqueta",
            strLink: "Enlace",
            strLoading: "Cargando",
            strLocal: "es",
            strLockCells: "Bloquear celdas",
            strMenu: {
                export: "Columnas exportables",
                filter: "Filtrar",
                hideCols: "Columnas visibles"
            },
            strMerge: "Fusionar celdas",
            strName: "Nombre",
            strNextResult: "Resultado siguiente",
            strNoRows: "No hay filas para mostrar.",
            strNothingFound: "no he encontrado nada",
            strOR: "O",
            strOk: "Ok",
            strOpen: "Abierto",
            strPaste: "Pegar",
            strPrevResult: "Resultado Anterior",
            strRedo: "Rehacer",
            strRename: "Cambiar nombre",
            strSearch: "Buscar",
            strSelectAll: "Seleccionar todo",
            strSelectedmatches: "Selección de {0} de {1} partidos",
            strShowCols: "Mostrar columnas",
            strShowRows: "Mostrar filas",
            strTP_aggPH: "Eliminar columnas para agregados",
            strTP_aggPane: "Agregados",
            strTP_colPH: "Eliminar columnas para agrupar columnas",
            strTP_colPane: "Columnas de grupo",
            strTP_pivot: "Modo pivote",
            strTP_rowPH: "Eliminar columnas para agrupar filas",
            strTP_rowPane: "Filas de grupo",
            strTabAdd: "Nueva hoja",
            strTabClose: "Quitar hoja",
            strTabHide: "Ocultar hoja",
            strTabName: "sheet{0}",
            strTabRemove: "{0} se eliminará permanentemente.\r\n¿Está seguro?",
            strTabRename: "Cambiar nombre de hoja",
            strTabShow: "Mostrar hoja",
            strTextColor: "Color del texto",
            strUnderline: "Texto subrayado",
            strUndo: "Deshacer",
            strUnhide: "Mostrar",
            strUnmerge: "Separar celdas",
            strUpdate: "Actualizar",
            strWrap: "Ajustar texto"
        },
        pager = pq.pqPager.regional.es = {
            strDisplay: "Mostrando {0} a {1} de {2} elementos.",
            strFirstPage: "Primera Página",
            strLastPage: "Última página",
            strNextPage: "Página siguiente",
            strPage: "Página {0} de {1}",
            strPrevPage: "Página anterior",
            strRefresh: "refrescar",
            strRpp: "Registros por página: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);

})();