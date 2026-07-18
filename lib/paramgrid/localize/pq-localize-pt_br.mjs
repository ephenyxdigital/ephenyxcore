import {
    $
} from 'pqgrid';

(function($) {
    var pq = $.paramquery,
        grid = pq.strings = pq.pqGrid.regional.pt_br = {
            strAND: "E",
            strAddColLeft: "Adicionar coluna à esquerda",
            strAddColRight: "Adicionar coluna à direita",
            strAddColumn: "Adicionar coluna",
            strAddRow: "Adicionar linha",
            strAddRowAbove: "Adicionar linha acima",
            strAddRowBelow: "Adicionar linha abaixo",
            strAddRows: "Adicionar {0} linhas",
            strAlign: {
                bottom: "Alinhamento inferior",
                center: "Alinhamento central",
                left: "Alinhar à esquerda",
                right: "Alinhar à direita",
                top: "Alinhamento superior"
            },
            strAlignH: "Alinhamento horizontal",
            strAlignV: "Alinhamento vertical",
            strApply: "Aplicar",
            strBlanks: "( Espaços em branco )",
            strBold: "Texto em negrito",
            strBorder: "Fronteira",
            strBorderColor: "Cor da borda",
            strBorderStyle: "Estilo de borda",
            strBorders: {
                all: "Todas as fronteiras",
                bottom: "Borda Inferior",
                horizontal: "Borda horizontal",
                inner: "Fronteira Interna",
                left: "Borda Esquerda",
                none: "Sem fronteira",
                outer: "Fronteira Externa",
                right: "Borda Direita",
                top: "Borda superior",
                vertical: "Borda vertical"
            },
            strCancel: "Cancelar",
            strClear: "Clara",
            strClearColor: "Cor clara",
            strClearFilter: "Limpar filtro",
            strClearText: "Texto claro",
            strCollapse: "Recolher painel",
            strComment: "Comentário",
            strCondition: "Condition:",
            strConditions: {
                "": "-- Nenhum --",
                begin: "Começa com",
                between: "Entre",
                contain: "Contém",
                empty: "Vazio",
                end: "Termina com",
                equal: "Igual",
                great: "Maior que",
                gte: "Maior ou igual",
                less: "Menos que",
                lte: "Menor ou igual",
                notbegin: "Não começa com",
                notcontain: "Não contém",
                notempty: "Não está vazio",
                notend: "Não termina com",
                notequal: "Não é igual",
                range: "[Alcance]",
                regexp: "Expressão regular"
            },
            strCopy: "Copiar",
            strCut: "Cortar",
            strDelete: "Excluir",
            strDeleteColumn: "Excluir coluna",
            strDeleteRow: "Excluir linha",
            strDownloadXlsx: "Baixe o arquivo Excel",
            strEdit: "Editar",
            strExitFullScreen: "Sair da tela inteira",
            strExpand: "Expandir painel",
            strExport: "Exportar dados",
            strFillColor: "Cor de preenchimento",
            strFontFamily: "Família de fontes",
            strFontSize: "Tamanho da fonte",
            strFormat: "Formatar células",
            strFormatMenu: {
                Accounting: "Contabilidade",
                Currency: "Moeda",
                Custom: "Personalizado",
                DateTime: "Data Hora",
                Fixed: "Decimais",
                Fraction: "Fração",
                FullDate: "Data Completa",
                General: "Geral",
                Int: "Inteiro",
                LongDate: "Data Longa",
                MediumDate: "Data média",
                Note: "Nota: Os formatos com * mudam de acordo com a localidade do usuário.",
                Percent: "Porcentagem",
                Scientific: "Científico",
                ShortDate: "Data curta",
                Standard: "Padrão",
                Text: "Texto",
                Time: "Hora"
            },
            strFormulas: {
                ABS: [
                    "ABS(number)",
                    "Retorna o valor absoluto de um número.O valor absoluto de um número é o número sem sinal."
                ],
                ACOS: [
                    "ACOS(number)",
                    "Retorna o arco cosseno, ou cosseno inverso, de um número.O arco cosseno é o ângulo cujo cosseno é número.O ângulo retornado é dado em radianos no intervalo de 0 (zero) a pi."
                ],
                AND: [
                    "E(lógico1, [lógico2], ...)",
                    "Retorna TRUE se todos os seus argumentos forem avaliados como TRUE e retorna FALSE se um ou mais argumentos forem avaliados como FALSE."
                ],
                ASIN: [
                    "ASIN(number)",
                    "Retorna o arco seno, ou seno inverso, de um número.O arco seno é o ângulo cujo seno é número.O ângulo retornado é dado em radianos no intervalo de -pi/2 a pi/2."
                ],
                ATAN: [
                    "ATAN(number)",
                    "Retorna o arco tangente, ou tangente inversa, de um número.O arco tangente é o ângulo cuja tangente é o número.O ângulo retornado é dado em radianos no intervalo de -pi/2 a pi/2."
                ],
                AVERAGE: [
                    "MÉDIA(número1, [número2], ...)",
                    "Retorna a média (média aritmética) dos argumentos.Por exemplo, se o intervalo A1:A20 contiver números, a fórmula =MÉDIA(A1:A20) retornará a média desses números."
                ],
                AVERAGEIF: [
                    "AVERAGEIF(intervalo, critérios, [intervalo_médio])",
                    "Retorna a média (média aritmética) de todas as células em um intervalo que atendem a um determinado critério."
                ],
                AVERAGEIFS: [
                    "AVERAGEIFS(intervalo_médio, intervalo_de critérios1, critérios1, [intervalo_de critérios2, critérios2], ...)",
                    "Retorna a média (média aritmética) de todas as células que atendem a vários critérios."
                ],
                CEILING: [
                    "TETO(número, significado)",
                    "Retorna o número arredondado, longe de zero, para o múltiplo significativo mais próximo.Por exemplo, se você quiser evitar o uso de centavos em seus preços e seu produto custar US$ 4,42, use a fórmula =CEILING(4,42,0,05) para arredondar os preços para o níquel mais próximo."
                ],
                CHAR: [
                    "CHAR(number)",
                    "Retorna o caractere especificado por um número."
                ],
                CHOOSE: [
                    "ESCOLHER(núm_índice, valor1, [valor2], ...)",
                    "Se núm_índice for 1, CHOOSE retornará valor1;se for 2, CHOOSE retornará valor2;e assim por diante."
                ],
                CODE: [
                    "CODE(text)",
                    "Retorna um código numérico para o primeiro caractere de uma string de texto."
                ],
                COLUMN: [
                    "COLUMN()",
                    "Retorna a referência da célula onde aparece a função COLUMN."
                ],
                COLUMNS: [
                    "COLUMNS(array)",
                    "Retorna o número de colunas em uma matriz ou referência."
                ],
                CONCATENATE: [
                    "CONCATENAR(texto1, [texto2], ...)",
                    "junte duas ou mais strings de texto em uma string."
                ],
                COS: [
                    "COS(number)",
                    "Retorna o cosseno do ângulo fornecido (em radianos)."
                ],
                COUNT: [
                    "CONTAR(valor1, [valor2], ...)",
                    "Conta o número de células que contêm números e conta os números na lista de argumentos."
                ],
                COUNTA: [
                    "CONTAR(valor1, [valor2], ...)",
                    "A função COUNTA conta o número de células que não estão vazias em um intervalo."
                ],
                COUNTBLANK: [
                    "COUNTBLANK(range)",
                    "Conta células vazias em um intervalo especificado de células."
                ],
                COUNTIF: [
                    "CONT.SE(intervalo, critérios)",
                    "conta o número de células que atendem a um critério;por exemplo, para contar o número de vezes que uma determinada cidade aparece em uma lista de clientes."
                ],
                COUNTIFS: [
                    "COUNTIFS(intervalo_critérios1, critérios1, [intervalo_critérios2, critérios2]…)",
                    "Aplica critérios a células em vários intervalos e conta o número de vezes que todos os critérios são atendidos."
                ],
                DATE: [
                    "DATE(year,month,day)",
                    "retorna o número de série sequencial que representa uma data específica."
                ],
                DATEDIF: [
                    "DATEDIF(start_date,end_date,unit)",
                    "Calcula o número de dias, meses ou anos entre duas datas.A unidade pode ser 'Y', 'M' ou 'D'."
                ],
                DATEVALUE: [
                    "DATEVALUE(date_text)",
                    "converte uma data armazenada como texto em um número de série que o Excel reconhece como uma data.Por exemplo, a fórmula =DATEVALUE(\"1/1/2008\") retorna 39448."
                ],
                DAY: [
                    "DAY(serial_number)",
                    "Retorna o dia de uma data, representado por um número de série."
                ],
                DAYS: [
                    "DIAS(data_final, data_inicial)",
                    "Retorna o número de dias entre duas datas."
                ],
                DEGREES: [
                    "DEGREES(angle)",
                    "Converte radianos em graus."
                ],
                EOMONTH: [
                    "EOMONTH(data_início, meses)",
                    "Retorna o número de série do último dia do mês que é o número indicado de meses antes ou depois de start_date."
                ],
                EXP: [
                    "EXP(number)",
                    "Retorna e elevado à potência do número."
                ],
                FIND: [
                    "ENCONTRAR(encontrar_texto, dentro do_texto, [núm_inicial])",
                    "Localiza uma sequência de texto em uma segunda sequência de texto e retorna o número da posição inicial da primeira sequência de texto a partir do primeiro caractere da segunda sequência de texto."
                ],
                FLOOR: [
                    "FLOOR(número, significado)",
                    "Arredonda o número para baixo, em direção a zero, para o múltiplo significativo mais próximo."
                ],
                HLOOKUP: [
                    "PROCH(valor_procurado, matriz_tabela, núm_índice_linha, [procura_intervalo])",
                    "Pesquisa um valor na linha superior de uma tabela ou matriz de valores e, em seguida, retorna um valor na mesma coluna de uma linha especificada na tabela ou matriz."
                ],
                HOUR: [
                    "HOUR(serial_number)",
                    "Retorna a hora de um valor de tempo.A hora é dada como um número inteiro, variando de 0 (12h00) a 23 (23h00)."
                ],
                HYPERLINK: [
                    "HIPERLINK(url, [nome_amigável])",
                    "cria um atalho que salta para outro local na Internet quando você clica em uma célula"
                ],
                IF: [
                    "SE(teste_lógico, valor_se_verdadeiro, [valor_se_falso])",
                    "retorna um valor se uma condição for verdadeira e outro valor se for falsa."
                ],
                INDEX: [
                    "ÍNDICE(matriz, núm_linha, [núm_coluna])",
                    "Retorna o valor de um elemento em uma tabela ou array, selecionado pelos índices numéricos de linha e coluna."
                ],
                INDIRECT: [
                    "INDIRETO(texto_ref, [a1])",
                    "Retorna a referência especificada por uma string de texto.As referências são avaliadas imediatamente para exibir seu conteúdo."
                ],
                ISBLANK: [
                    "ISBLANK(value)",
                    "Retorna TRUE se o valor estiver em branco"
                ],
                LARGE: [
                    "GRANDE(matriz, k)",
                    "Retorna o k-ésimo maior valor em um conjunto de dados."
                ],
                LEFT: [
                    "ESQUERDA(texto, [num_caracteres])",
                    "retorna o primeiro caractere ou caracteres em uma sequência de texto, com base no número de caracteres especificado."
                ],
                LEN: [
                    "LEN(text)",
                    "retorna o número de caracteres em uma string de texto."
                ],
                LOOKUP: [
                    "LOOKUP(valor_procurado, vetor_procurado, [vetor_resultado])",
                    "Procura um valor em um intervalo de uma linha ou de uma coluna (conhecido como vetor) e retorna um valor da mesma posição em um segundo intervalo de uma linha ou de uma coluna."
                ],
                LOWER: [
                    "LOWER(text)",
                    "Converte todas as letras maiúsculas de uma sequência de texto em minúsculas."
                ],
                MATCH: [
                    "MATCH(valor_procurado, array_procurado, [tipo_correspondência])",
                    "Pesquisa um item especificado em um intervalo de células e retorna a posição relativa desse item no intervalo.match_type pode ser: 0 para correspondência exata com opção de usar curingas;1 (padrão) para menos que, Os valores no argumento lookup_array devem ser colocados em ordem crescente;-1 para valores maiores que, os valores no argumento lookup_array devem ser colocados em ordem decrescente."
                ],
                MAX: [
                    "MAX(número1, [número2], ...)",
                    "Retorna o maior valor em um conjunto de valores."
                ],
                MEDIAN: [
                    "MEDIANA(número1, [número2], ...)",
                    "Retorna a mediana dos números fornecidos.A mediana é o número no meio de um conjunto de números."
                ],
                MID: [
                    "MID(texto, num_inicial, num_chars)",
                    "retorna um número específico de caracteres de uma sequência de texto, começando na posição especificada, com base no número de caracteres especificado."
                ],
                MIN: [
                    "MIN(número1, [número2], ...)",
                    "Retorna o menor número em um conjunto de valores."
                ],
                MODE: [
                    "MODE(number1,[number2],...)",
                    "Retorna o valor que ocorre com mais frequência ou é repetitivo em uma matriz ou intervalo de dados."
                ],
                MONTH: [
                    "MONTH(serial_number)",
                    "Retorna o mês de uma data representada por um número de série.O mês é dado como um número inteiro, variando de 1 (janeiro) a 12 (dezembro)."
                ],
                OR: [
                    "OU(lógico1, [lógico2], ...)",
                    "retorna TRUE se algum de seus argumentos for avaliado como TRUE e retorna FALSE se todos os seus argumentos forem avaliados como FALSE."
                ],
                PI: [
                    "PI()",
                    "Retorna o número 3,14159265358979, a constante matemática pi."
                ],
                POWER: [
                    "POTÊNCIA(número, potência)",
                    "Retorna o resultado de um número elevado a uma potência."
                ],
                PRODUCT: [
                    "PRODUTO(número1, [número2], ...)",
                    "multiplica todos os números dados como argumentos e retorna o produto.PRODUTO(A1:A3, C1:C3) é equivalente a =A1 * A2 * A3 * C1 * C2 * C3."
                ],
                PROPER: [
                    "PROPER(text)",
                    "Coloca em maiúscula a primeira letra de cada palavra de um valor de texto."
                ],
                RADIANS: [
                    "RADIANS(angle)",
                    "Converte graus em radianos."
                ],
                RAND: [
                    "RAND()",
                    "Retorna um número real aleatório distribuído uniformemente maior ou igual a 0 e menor que 1."
                ],
                RANK: [
                    "RANK(number,ref,[order])",
                    "Retorna a classificação de um número em uma lista de números."
                ],
                RATE: [
                    "",
                    ""
                ],
                REPLACE: [
                    "SUBSTITUIR(texto_antigo, num_inicial, num_chars, novo_texto)",
                    "substitui parte de uma sequência de texto, com base no número de caracteres especificado, por uma sequência de texto diferente."
                ],
                REPT: [
                    "REPT(texto, número_vezes)",
                    "Repete o texto um determinado número de vezes."
                ],
                RIGHT: [
                    "RIGHT(text,[num_chars])",
                    "retorna o último caractere ou caracteres de uma sequência de texto, com base no número de caracteres especificado."
                ],
                ROUND: [
                    "ROUND(número, num_dígitos)",
                    "arredonda um número para um número especificado de dígitos."
                ],
                ROUNDDOWN: [
                    "ROUNDDOWN(número, num_dígitos)",
                    "Arredonda um número para baixo, em direção a zero."
                ],
                ROUNDUP: [
                    "ROUNDUP(número, num_dígitos)",
                    "Arredonda um número para cima, afastando-se de 0 (zero)."
                ],
                ROW: [
                    "ROW()",
                    "Retorna a referência da célula na qual aparece a função ROW."
                ],
                ROWS: [
                    "ROWS(array)",
                    "Retorna o número de linhas em uma referência ou array."
                ],
                SEARCH: [
                    "SEARCH(find_text,within_text,[start_num])",
                    "localize uma sequência de texto em uma segunda sequência de texto e retorne o número da posição inicial da primeira sequência de texto a partir do primeiro caractere da segunda sequência de texto."
                ],
                SIN: [
                    "SIN(number)",
                    "Retorna o seno do ângulo fornecido (em radianos)."
                ],
                SMALL: [
                    "PEQUENO(matriz, k)",
                    "Retorna o k-ésimo menor valor em um conjunto de dados."
                ],
                SQRT: [
                    "SQRT(number)",
                    "Retorna uma raiz quadrada positiva."
                ],
                STDEV: [
                    "STDEV(number1,[number2],...)",
                    "Estima o desvio padrão com base em uma amostra.O desvio padrão é uma medida de quão amplamente os valores estão dispersos do valor médio (a média)."
                ],
                STDEVP: [
                    "STDEVP(number1,[number2],...)",
                    "Calcula o desvio padrão com base em toda a população dada como argumentos."
                ],
                SUBSTITUTE: [
                    "SUBSTITUTE(texto, texto_antigo, texto_novo, [núm_instância])",
                    "Substitui texto_novo por texto_antigo em uma sequência de texto."
                ],
                SUM: [
                    "SUM(number1,[number2],...)",
                    "Adiciona seus argumentos.Você pode adicionar valores individuais, referências de células ou intervalos ou uma combinação dos três."
                ],
                SUMIF: [
                    "SOMASE(intervalo, critérios, [intervalo_soma])",
                    "adiciona os valores em um intervalo que atende aos critérios especificados por você"
                ],
                SUMIFS: [
                    "SUMIFS(intervalo_soma, intervalo_critérios1, critérios1, [intervalo_critérios2, critérios2], ...)",
                    "adiciona todos os seus argumentos que atendem a vários critérios."
                ],
                SUMPRODUCT: [
                    "SOMARPRODUTO(matriz1, [matriz2], [matriz3], ...)",
                    "Multiplica os componentes correspondentes nas matrizes fornecidas e retorna a soma desses produtos."
                ],
                TAN: [
                    "TAN(number)",
                    "Retorna a tangente do ângulo fornecido (em radianos)."
                ],
                TEXT: [
                    "TEXT(Valor que deseja formatar, \"Formatar código que deseja aplicar\")",
                    "A função TEXTO permite alterar a forma como um número aparece aplicando-lhe formatação com códigos de formato."
                ],
                TIME: [
                    "TEMPO(hora, minuto, segundo)",
                    "Retorna o número decimal de um horário específico."
                ],
                TIMEVALUE: [
                    "TIMEVALUE(time_text)",
                    "Retorna o número decimal da hora representada por uma sequência de texto.O número decimal é um valor que varia de 0 (zero) a 0,99988426, representando os horários de 0:00:00 (12:00:00 AM) a 23:59:59 (23:59:59 PM)."
                ],
                TODAY: [
                    "TODAY()",
                    "Retorna o número de série da data atual."
                ],
                TRIM: [
                    "TRIM(text)",
                    "Remove todos os espaços do texto, exceto espaços simples entre palavras."
                ],
                TRUNC: [
                    "TRUNC(número, [num_dígitos])",
                    "Trunca um número para um inteiro removendo a parte fracionária do número."
                ],
                UPPER: [
                    "UPPER(text)",
                    "Converte texto em maiúsculas."
                ],
                VALUE: [
                    "VALUE(text)",
                    "Converte uma sequência de texto que representa um número em um número."
                ],
                VAR: [
                    "VAR(number1,[number2],...)",
                    "Estima a variação com base em uma amostra."
                ],
                VARP: [
                    "VARP(number1,[number2],...)",
                    "Calcula a variação com base em toda a população."
                ],
                VLOOKUP: [
                    "VLOOKUP (valor_procurado, matriz_tabela, núm_índice_coluna, [procura_intervalo])",
                    "procure um valor em uma tabela ou um intervalo por linha.Por exemplo, procure o preço de uma peça automotiva pelo número da peça."
                ],
                YEAR: [
                    "YEAR(serial_number)",
                    "Retorna o ano correspondente a uma data.O ano é retornado como um número inteiro no intervalo 1900-9999."
                ]
            },
            strFreezeFirstCol: "Congelar a primeira coluna",
            strFreezePanes: "Congelar painéis",
            strFreezePanesUndo: "Descongelar painéis",
            strFreezeTopRow: "Congelar a primeira linha",
            strFrozenCols: "Colunas Congeladas",
            strFrozenRows: "Linhas congeladas",
            strFullScreen: "Tela cheia",
            strGroup_fixCols: "Corrigir colunas",
            strGroup_grandSummary: "Grande resumo",
            strGroup_header: "Eliminar grupo por coluna",
            strGroup_merge: "Mesclar células",
            strHideCols: "Ocultar colunas",
            strHideRows: "Ocultar linhas",
            strImport: "Importar dados",
            strImportPic: "Inserir imagem flutuante",
            strImportPicCell: "Inserir imagem na célula",
            strInsertColumn: "Inserir coluna",
            strInsertRow: "Inserir linha",
            strInsertRows: "Inserir {0} linhas",
            strItalic: "Texto em itálico",
            strLabel: "Etiqueta",
            strLink: "Ligação",
            strLoading: "Carregando",
            strLocal: "pt_br",
            strLockCells: "Bloquear células",
            strMenu: {
                export: "Colunas exportáveis",
                filter: "Filtro",
                hideCols: "Colunas visíveis"
            },
            strMerge: "Mesclar células",
            strName: "Nome",
            strNextResult: "Próximo Resultado",
            strNoRows: "Nenhuma linha para exibir.",
            strNothingFound: "Não encontrado",
            strOR: "OU",
            strOk: "OK",
            strOpen: "Abrir",
            strPaste: "Colar",
            strPrevResult: "Resultado Anterior",
            strRedo: "Refazer",
            strRename: "Renomear",
            strSearch: "Pesquisar",
            strSelectAll: "Selecionar tudo",
            strSelectedmatches: "Selecionado {0} de {1} resultados",
            strShowCols: "Mostrar colunas",
            strShowRows: "Mostrar linhas",
            strTP_aggPH: "Eliminar colunas para agregados",
            strTP_aggPane: "Agregados",
            strTP_colPH: "Eliminar colunas para agrupamento de colunas",
            strTP_colPane: "Colunas de grupo",
            strTP_pivot: "Modo pivô",
            strTP_rowPH: "Eliminar colunas para agrupamento de linhas",
            strTP_rowPane: "Linhas de grupo",
            strTabAdd: "Nova planilha",
            strTabClose: "Remover planilha",
            strTabHide: "Ocultar planilha",
            strTabName: "sheet{0}",
            strTabRemove: "{0} seria excluído permanentemente.\r\nTem certeza?",
            strTabRename: "Renomear planilha",
            strTabShow: "Mostrar planilha",
            strTextColor: "Cor do texto",
            strUnderline: "Sublinhar texto",
            strUndo: "Desfazer",
            strUnhide: "Mostrar",
            strUnmerge: "Desfazer mesclagem de células",
            strUpdate: "Atualizar",
            strWrap: "Quebrar texto"
        },
        pager = pq.pqPager.regional.pt_br = {
            strDisplay: "Mostrando {0} a {1} de {2} itens.",
            strFirstPage: "Primera Página",
            strLastPage: "Última Página",
            strNextPage: "Próxima Página",
            strPage: "Página {0} de {1}",
            strPrevPage: "Página Anterior",
            strRefresh: "Atualizar",
            strRpp: "Registros por página: {0}"
        };

    $.extend(pq.pqGrid.defaults, grid);
    $.extend(pq.pqPager.defaults, pager);
})($);