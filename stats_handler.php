<?php

// stats_handler.php
error_log("DEBUG_FULL: stats_handler.php - Início do script.");

/**
 * Carrega os resultados históricos de uma loteria a partir de um arquivo JSON.
 * @param string $lotteryType O tipo de loteria.
 * @return array Um array de resultados históricos, ou um array vazio se o arquivo não existir ou estiver vazio/malformado.
 */
function loadHistoricalResults(string $lotteryType): array
{
    $filePath = __DIR__ . '/data/' . strtolower($lotteryType) . '_history.json';
    error_log("DEBUG_FULL: loadHistoricalResults: Tentando carregar histórico de: " . $filePath);
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("DEBUG_FULL: loadHistoricalResults: Erro ao decodificar JSON de " . $filePath . ": " . json_last_error_msg());
            return [];
        }
        error_log("DEBUG_FULL: loadHistoricalResults: Histórico carregado para " . $lotteryType . ". Total de concursos: " . count($data));
        return $data ?: [];
    }
    error_log("DEBUG_FULL: loadHistoricalResults: Arquivo de histórico não encontrado para " . $lotteryType . ": " . $filePath);
    return [];
}

/**
 * Salva os resultados históricos de uma loteria em um arquivo JSON.
 * @param string $lotteryType O tipo de loteria.
 * @param array $results O array de resultados a ser salvo.
 */
function saveHistoricalResults(string $lotteryType, array $results): void
{
    $filePath = __DIR__ . '/data/' . strtolower($lotteryType) . '_history.json';
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true); // Cria a pasta 'data' se não existir
    }
    file_put_contents($filePath, json_encode($results, JSON_PRETTY_PRINT));
    error_log("DEBUG_FULL: saveHistoricalResults: Histórico salvo para " . $lotteryType . " em " . $filePath . ". Total de concursos: " . count($results));
}

/**
 * Analisa estatísticas de números sorteados para uma dada loteria.
 * Adapta-se a diferentes estruturas de dados da API da Caixa.
 * @param string $lotteryType O tipo de loteria.
 * @return array Um array contendo as estatísticas de dezenas, e outras estatísticas específicas da loteria.
 */
function analyzeLotteryStatistics(string $lotteryType): array
{
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Iniciando análise para " . $lotteryType);
    $historicalData = loadHistoricalResults($lotteryType);
    $numberCounts = [];
    $trevoCounts = []; // Para +Milionária
    $monthCounts = []; // Para Dia de Sorte
    $teamCounts = [];  // Para Timemania
    $message = '';

    error_log("DEBUG_FULL: analyzeLotteryStatistics: Dados históricos carregados: " . count($historicalData) . " concursos.");

    if (empty($historicalData)) {
        return [
            'mostFrequent' => [],
            'leastFrequent' => [],
            'mostFrequentMonths' => [], 
            'mostFrequentTeams' => [],  
            'mostFrequentTrevos' => [],
            'leastFrequentTrevos' => [],
            'message' => 'Nenhum dado histórico para analisar. Consulte alguns concursos atuais ou use "Popular Histórico".'
        ];
    }

    foreach ($historicalData as $contestIndex => $contest) {
        $dezenas = [];
        $trevos = [];
        error_log("DEBUG_FULL: analyzeLotteryStatistics: Processando concurso " . ($contest['numero'] ?? 'N/A') . " (Index: " . $contestIndex . ")");

        switch (strtolower($lotteryType)) {
            case 'megasena':
            case 'duplasena':
            case 'supersete':
            case 'diadesorte':
            case 'quina': 
            case 'lotofacil': 
            case 'lotomania': 
            case 'timemania': 
            case 'maismilionaria':
                if (isset($contest['listaDezenas']) && is_array($contest['listaDezenas'])) {
                    if (!empty($contest['listaDezenas']) && is_array($contest['listaDezenas'][0])) {
                        foreach ($contest['listaDezenas'] as $sorteioIndex => $sorteio) {
                            if (is_array($sorteio)) {
                                $dezenas = array_merge($dezenas, $sorteio);
                                error_log("DEBUG_FULL: analyzeLotteryStatistics: " . $lotteryType . " - Sorteio " . $sorteioIndex . " de 'listaDezenas' (array de arrays): " . json_encode($sorteio));
                            }
                        }
                    } else {
                        $dezenas = $contest['listaDezenas'];
                        error_log("DEBUG_FULL: analyzeLotteryStatistics: " . $lotteryType . " - Dezenas encontradas em 'listaDezenas' (array simples): " . json_encode($dezenas));
                    }
                } else {
                    error_log("DEBUG_FULL: analyzeLotteryStatistics: " . $lotteryType . " - 'listaDezenas' não encontrado ou não é array para concurso " . ($contest['numero'] ?? 'N/A') . ". Conteúdo do concurso: " . json_encode($contest));
                }

                if (strtolower($lotteryType) === 'maismilionaria' && isset($contest['trevosSorteados']) && is_array($contest['trevosSorteados'])) {
                    $trevos = $contest['trevosSorteados'];
                    error_log("DEBUG_FULL: analyzeLotteryStatistics: +Milionária - Trevos encontrados em 'trevosSorteados': " . json_encode($trevos));
                }

                if (strtolower($lotteryType) === 'diadesorte' && isset($contest['nomeTimeCoracaoMesSorte']) && !empty($contest['nomeTimeCoracaoMesSorte'])) {
                    $month = $contest['nomeTimeCoracaoMesSorte'];
                    $monthCounts[$month] = ($monthCounts[$month] ?? 0) + 1;
                    error_log("DEBUG_FULL: analyzeLotteryStatistics: Dia de Sorte - Mês da Sorte encontrado: " . $month);
                }

                if (strtolower($lotteryType) === 'timemania' && isset($contest['nomeTimeCoracaoMesSorte']) && !empty($contest['nomeTimeCoracaoMesSorte'])) {
                    $team = $contest['nomeTimeCoracaoMesSorte'];
                    $teamCounts[$team] = ($teamCounts[$team] ?? 0) + 1;
                    error_log("DEBUG_FULL: analyzeLotteryStatistics: Timemania - Time do Coração encontrado: " . $team);
                }
                break;
            case 'federal':
            case 'loteca':
                $message = 'A análise estatística de dezenas não se aplica diretamente a esta loteria. Verifique os resultados por bilhete/jogo.';
                error_log("DEBUG_FULL: analyzeLotteryStatistics: " . $lotteryType . " - Análise de dezenas não se aplica. Mensagem: " . $message);
                break;
            default:
                $message = 'Tipo de loteria desconhecido para análise estatística.';
                error_log("DEBUG_FULL: analyzeLotteryStatistics: Tipo de loteria desconhecido: " . $lotteryType);
                break;
        }

        if (!empty($dezenas)) {
            foreach ($dezenas as $number) {
                $number = (int)ltrim((string)$number, '0'); 
                $numberCounts[$number] = ($numberCounts[$number] ?? 0) + 1;
                error_log("DEBUG_FULL: analyzeLotteryStatistics: Processando número: " . $number . ". Contagem atual: " . $numberCounts[$number]);
            }
        } else {
            error_log("DEBUG_FULL: analyzeLotteryStatistics: Nenhuma dezena válida encontrada para contagem no concurso " . ($contest['numero'] ?? 'N/A') . " de " . $lotteryType);
        }

        if (!empty($trevos)) {
            foreach ($trevos as $trevo) {
                $trevo = (int)ltrim((string)$trevo, '0'); 
                $trevoCounts[$trevo] = ($trevoCounts[$trevo] ?? 0) + 1;
                error_log("DEBUG_FULL: analyzeLotteryStatistics: Processando trevo: " . $trevo . ". Contagem atual: " . $trevoCounts[$trevo]);
            }
        }
    }

    if (empty($numberCounts) && empty($message)) {
        $message = 'Nenhum número sorteado encontrado no histórico para análise. Verifique se os dados históricos contêm dezenas válidas.';
    }

    arsort($numberCounts);
    $mostFrequent = array_slice($numberCounts, 0, 12, true); // Top 12

    asort($numberCounts);
    $leastFrequent = array_slice($numberCounts, 0, 6, true); // Bottom 6

    arsort($monthCounts);
    $mostFrequentMonths = array_slice($monthCounts, 0, 5, true);

    arsort($teamCounts);
    $mostFrequentTeams = array_slice($teamCounts, 0, 5, true);

    arsort($trevoCounts);
    $mostFrequentTrevos = array_slice($trevoCounts, 0, 5, true);

    asort($trevoCounts);
    $leastFrequentTrevos = array_slice($trevoCounts, 0, 5, true);

    error_log("DEBUG_FULL: analyzeLotteryStatistics: Análise concluída para " . $lotteryType);
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Números Mais Sorteados: " . json_encode($mostFrequent));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Números Menos Sorteados: " . json_encode($leastFrequent));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Meses Mais Sorteados: " . json_encode($mostFrequentMonths));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Times Mais Sorteados: " . json_encode($mostFrequentTeams));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Trevos Mais Sorteados: " . json_encode($mostFrequentTrevos));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Trevos Menos Sorteados: " . json_encode($leastFrequentTrevos));
    error_log("DEBUG_FULL: analyzeLotteryStatistics: Mensagem final: " . $message);

    return [
        'mostFrequent' => $mostFrequent,
        'leastFrequent' => $leastFrequent,
        'mostFrequentMonths' => $mostFrequentMonths, 
        'mostFrequentTeams' => $mostFrequentTeams,   
        'mostFrequentTrevos' => $mostFrequentTrevos, 
        'leastFrequentTrevos' => $leastFrequentTrevos, 
        'allFrequencies' => $numberCounts,
        'message' => $message 
    ];

} // Fim da função analyzeLotteryStatistics

error_log("DEBUG_FULL: stats_handler.php - Fim do script.");
?>
