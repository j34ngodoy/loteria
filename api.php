<?php

// api.php
// Este arquivo atua como um ponto de entrada para todas as requisições AJAX do frontend.

// --- Configurações de Depuração (REMOVER EM PRODUÇÃO APÓS SOLUÇÃO DO PROBLEMA) ---
ini_set('display_errors', 1);             // Exibe erros no navegador
ini_set('display_startup_errors', 1);     // Exibe erros de inicialização do PHP
error_reporting(E_ALL);                   // Reporta todos os tipos de erros

ini_set('log_errors', 1);                 // Habilita o log de erros
ini_set('error_log', __DIR__ . '/php_error.log'); // Define o arquivo de log de erros
// ---------------------------------------------------------------------------------

error_log("DEBUG_FULL: api.php - Início do script.");

header('Content-Type: application/json');

// Inclui o autoloader do Composer o mais cedo possível
$composerAutoloadPath = __DIR__ . '/vendor/autoload.php'; // Usar __DIR__ para caminho absoluto

error_log("DEBUG_FULL: api.php - Verificando se autoload.php existe em " . $composerAutoloadPath);
if (!file_exists($composerAutoloadPath)) {
    error_log("DEBUG_FULL: Erro Fatal: Composer autoload.php NÃO encontrado em " . $composerAutoloadPath . ". Por favor, execute 'composer install'.");
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor: Autoloader do Composer ausente.']);
    exit;
}
require_once $composerAutoloadPath;
error_log("DEBUG_FULL: api.php - autoload.php carregado com sucesso.");

// Classes do Rubix ML - MOVIDAS PARA O TOPO PARA GARANTIR CARREGAMENTO
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\MinMaxNormalizer;
use Rubix\ML\Kernels\Distance\Euclidean; // Necessário para KMeans
use Rubix\ML\Clusterers\Seeders\PlusPlus; // Necessário para KMeans

// --- DIAGNÓSTICO RUBIX ML ---
error_log("DEBUG_FULL: api.php - Iniciando diagnóstico Rubix ML.");
if (class_exists(KMeans::class)) {
    error_log("DEBUG_FULL: Classe Rubix\\ML\\Clusterers\\KMeans ENCONTRADA.");
    try {
        $testKMeans = new KMeans(2);
        error_log("DEBUG_FULL: Instância de Rubix\\ML\\Clusterers\\KMeans CRIADA COM SUCESSO.");
    } catch (Throwable $e) {
        error_log("DEBUG_FULL: ERRO ao instanciar Rubix\\ML\\Clusterers\\KMeans: " . $e->getMessage() . " em " . $e->getFile() . " na linha " . $e->getLine());
    }
} else {
    error_log("DEBUG_FULL: Classe Rubix\\ML\\Clusterers\\KMeans NÃO ENCONTRADA. Verifique a instalação do Rubix ML e suas dependências.");
}
error_log("DEBUG_FULL: api.php - Fim do diagnóstico Rubix ML.");
// --- FIM DO DIAGNÓSTICO RUBIX ML ---


// --- Inclusão de arquivos de funções e manipuladores essenciais ---
error_log("DEBUG_FULL: api.php - Tentando incluir lottery_functions.php.");
if (!file_exists('lottery_functions.php')) {
    error_log("DEBUG_FULL: Erro Fatal: 'lottery_functions.php' não encontrado.");
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor: Arquivo de funções de loteria ausente.']);
    exit;
}
require_once 'lottery_functions.php';
error_log("DEBUG_FULL: api.php - lottery_functions.php carregado com sucesso.");

error_log("DEBUG_FULL: api.php - Tentando incluir stats_handler.php.");
if (!file_exists('stats_handler.php')) {
    error_log("DEBUG_FULL: Erro Fatal: 'stats_handler.php' não encontrado.");
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor: Arquivo de manipulador de estatísticas ausente.']);
    exit;
}
require_once 'stats_handler.php';


// Funções auxiliares (anteriormente em outros arquivos ou integradas)
function loadBetHistory(string $filePath): array
{
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        return json_decode($content, true) ?: [];
    }
    return [];
}

function saveBetHistory(string $filePath, array $history): void
{
    if (!is_dir(dirname($filePath))) {
        mkdir(dirname($filePath), 0777, true);
    }
    file_put_contents($filePath, json_encode($history, JSON_PRETTY_PRINT));
}

function exportData(string $format, array $data, string $filenamePrefix): void
{
    $mimeType = '';
    $content = '';
    $filename = $filenamePrefix;

    switch ($format) {
        case 'json':
            $mimeType = 'application/json';
            $content = json_encode($data, JSON_PRETTY_PRINT);
            $filename .= '.json';
            break;
        case 'csv':
            $mimeType = 'text/csv';
            $content = '';
            if (!empty($data)) {
                $headers = array_keys($data[0]);
                $content .= implode(',', array_map(function($h) { return '"' . str_replace('"', '""', $h) . '"'; }, $headers)) . "\n";
                foreach ($data as $row) {
                    $values = [];
                    foreach ($headers as $header) {
                        $value = $row[$header] ?? '';
                        if (is_array($value) || is_object($value)) {
                            $value = json_encode($value);
                        }
                        $values[] = '"' . str_replace('"', '""', $value) . '"';
                    }
                    $content .= implode(',', $values) . "\n";
                }
            }
            $filename .= '.csv';
            break;
        case 'sql':
            $mimeType = 'text/plain';
            $content = "-- SQL Export for " . $filenamePrefix . "\n\n";
            foreach ($data as $item) {
                $content .= "INSERT INTO " . $filenamePrefix . "_data (data) VALUES ('" . addslashes(json_encode($item)) . "');\n";
            }
            $filename .= '.sql';
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Formato de exportação inválido.']);
            exit;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($content));
    echo $content;
    exit;
}


// Configuração do caminho para o arquivo de histórico de apostas
$betHistoryFilePath = __DIR__ . '/data/betting_history.json';


$action = $_GET['action'] ?? '';

error_log("DEBUG_FULL: api.php - Ação recebida: " . $action);

try {
    switch ($action) {
        case 'generateNumbers':
            error_log("DEBUG_FULL: api.php - Executando case 'generateNumbers'.");
            $lotteryType = $_GET['lottery'] ?? '';
            $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

            if (isset($lotteries[$lotteryType])) {
                $config = $lotteries[$lotteryType];
                $allGeneratedNumbers = [];
                for ($i = 0; $i < $quantity; $i++) {
                    $allGeneratedNumbers[] = generateNumbers($config['count'], $config['min'], $config['max']);
                }
                echo json_encode(['success' => true, 'numbers' => $allGeneratedNumbers]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de loteria inválido.']);
            }
            break;

        case 'getResults':
            error_log("DEBUG_FULL: api.php - Executando case 'getResults'.");
            $lotteryType = $_GET['lottery'] ?? '';
            $type = $_GET['type'] ?? 'current';
            $contestNumber = $_GET['contestNumber'] ?? null;

            $baseUrl = 'https://servicebus2.caixa.gov.br/portaldeloterias/api';
            $fullUrl = '';

            $lotteryApiMap = [
                'megasena' => 'megasena',
                'lotofacil' => 'lotofacil',
                'quina' => 'quina',
                'lotomania' => 'lotomania',
                'timemania' => 'timemania',
                'duplasena' => 'duplasena',
                'federal' => 'federal',
                'loteca' => 'loteca',
                'diadesorte' => 'diadesorte',
                'supersete' => 'supersete',
                'maismilionaria' => 'maismilionaria'
            ];

            if (!isset($lotteryApiMap[$lotteryType])) {
                echo json_encode(['error' => true, 'message' => 'Tipo de loteria inválido.']);
                exit;
            }

            $apiPath = $lotteryApiMap[$lotteryType];

            if ($type === 'current') {
                $fullUrl = "{$baseUrl}/{$apiPath}";
            } elseif ($type === 'specific' && $contestNumber) {
                $fullUrl = "{$baseUrl}/{$apiPath}/{$contestNumber}";
            } else {
                echo json_encode(['error' => true, 'message' => 'Parâmetros de consulta inválidos.']);
                exit;
            }

            error_log("DEBUG_FULL: getResults: Solicitando à API da Caixa: " . $fullUrl);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                error_log("DEBUG_FULL: cURL Error: " . $error_msg);
                echo json_encode(['error' => true, 'message' => 'Erro ao conectar à API da Caixa: ' . $error_msg]);
                curl_close($ch);
                exit;
            }

            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("DEBUG_FULL: JSON Decode Error: " . json_last_error_msg() . " Response: " . $response);
                    echo json_encode(['error' => true, 'message' => 'Erro ao decodificar a resposta da API.']);
                    exit;
                }
                echo json_encode($data);
            } else {
                error_log("DEBUG_FULL: API Response Error: HTTP " . $httpCode . " Response: " . $response);
                echo json_encode(['error' => true, 'message' => 'Erro ao obter resultados da API da Caixa. Código HTTP: ' . $httpCode]);
            }
            break;

        case 'populateHistoricalData':
            error_log("DEBUG_FULL: api.php - Executando case 'populateHistoricalData'.");
            $lotteryType = $_GET['lottery'] ?? '';
            $limit = intval($_GET['limit'] ?? 50);

            $historicalData = loadHistoricalResults($lotteryType);
            $currentContestNumber = null;

            if (!empty($historicalData)) {
                usort($historicalData, function($a, $b) {
                    return ($b['numero'] ?? 0) <=> ($a['numero'] ?? 0);
                });
                $currentContestNumber = $historicalData[0]['numero'] ?? null;
                error_log("DEBUG_FULL: populateHistoricalData: Último concurso conhecido para " . $lotteryType . ": " . $currentContestNumber);
            }

            if ($currentContestNumber === null) {
                $currentResultUrl = "{$_SERVER['SCRIPT_URI']}?action=getResults&lottery={$lotteryType}&type=current";
                $chCurrent = curl_init();
                curl_setopt($chCurrent, CURLOPT_URL, $currentResultUrl);
                curl_setopt($chCurrent, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chCurrent, CURLOPT_SSL_VERIFYPEER, false);
                $currentResultResponse = curl_exec($chCurrent);
                curl_close($chCurrent);
                $currentResult = json_decode($currentResultResponse, true);

                if ($currentResult && isset($currentResult['numero'])) {
                    $currentContestNumber = $currentResult['numero'];
                    error_log("DEBUG_FULL: populateHistoricalData: Concurso atual da API para " . $lotteryType . ": " . $currentContestNumber);
                }
            }

            if ($currentContestNumber === null) {
                echo json_encode(['success' => false, 'message' => 'Não foi possível determinar o concurso inicial para popular o histórico.']);
                exit;
            }

            $newlyFetchedCount = 0;
            $allResults = $historicalData;

            for ($i = 0; $i < $limit; $i++) {
                $contestToFetch = $currentContestNumber - $i;
                if ($contestToFetch <= 0) {
                    break;
                }

                $existsInHistory = false;
                foreach ($allResults as $existingResult) {
                    if (($existingResult['numero'] ?? null) == $contestToFetch) {
                        $existsInHistory = true;
                        break;
                    }
                }

                if ($existsInHistory) {
                    error_log("DEBUG_FULL: populateHistoricalData: Concurso " . $contestToFetch . " já existe no histórico para " . $lotteryType . ". Pulando.");
                    continue;
                }

                error_log("DEBUG_FULL: populateHistoricalData: Buscando concurso " . $contestToFetch . " para " . $lotteryType);
                $specificResultUrl = "{$_SERVER['SCRIPT_URI']}?action=getResults&lottery={$lotteryType}&type=specific&contestNumber={$contestToFetch}";
                $chSpecific = curl_init();
                curl_setopt($chSpecific, CURLOPT_URL, $specificResultUrl);
                curl_setopt($chSpecific, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chSpecific, CURLOPT_SSL_VERIFYPEER, false);
                $specificResultResponse = curl_exec($chSpecific);
                curl_close($chSpecific);
                $result = json_decode($specificResultResponse, true);


                if ($result && isset($result['numero'])) {
                    $allResults[] = $result;
                    $newlyFetchedCount++;
                    error_log("DEBUG_FULL: populateHistoricalData: Adicionado concurso " . $result['numero'] . " ao histórico de " . $lotteryType);
                } else {
                    error_log("DEBUG_FULL: populateHistoricalData: Falha ao buscar concurso " . $contestToFetch . " para " . $lotteryType . ". Resposta: " . json_encode($result));
                }
                usleep(100000);
            }

            $uniqueResults = [];
            $seenContests = [];
            foreach ($allResults as $res) {
                $contestNum = $res['numero'] ?? null;
                if ($contestNum !== null && !isset($seenContests[$contestNum])) {
                    $uniqueResults[] = $res;
                    $seenContests[$contestNum] = true;
                }
            }

            saveHistoricalResults($lotteryType, $uniqueResults);
            echo json_encode(['success' => true, 'message' => "Histórico populado com sucesso. Foram adicionados {$newlyFetchedCount} novos concursos."]);
            break;

        case 'getStatistics':
            error_log("DEBUG_FULL: api.php - Executando case 'getStatistics'.");
            $lotteryType = $_GET['lottery'] ?? '';
            if (!empty($lotteryType)) {
                $stats = analyzeLotteryStatistics($lotteryType);
                echo json_encode(['success' => true, 'stats' => $stats]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de loteria não especificado para estatísticas.']);
            }
            break;

        case 'saveBetHistory':
            error_log("DEBUG_FULL: api.php - Executando case 'saveBetHistory'.");
            $input = json_decode(file_get_contents('php://input'), true);
            $lotteryType = $input['lotteryType'] ?? '';
            $numbers = $input['numbers'] ?? [];

            if (empty($lotteryType) || empty($numbers)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos para salvar o histórico.']);
                exit;
            }

            $history = loadBetHistory($betHistoryFilePath);

            $history[] = [
                'lotteryType' => $lotteryType,
                'generatedNumbers' => $numbers,
                'generationDate' => date('Y-m-d H:i:s')
            ];

            file_put_contents($betHistoryFilePath, json_encode($history, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true, 'message' => 'Aposta salva com sucesso!']);
            break;

        case 'getBetHistory':
            error_log("DEBUG_FULL: api.php - Executando case 'getBetHistory'.");
            $history = loadBetHistory($betHistoryFilePath);
            echo json_encode(['success' => true, 'history' => $history]);
            break;

        case 'clearBetHistory':
            error_log("DEBUG_FULL: api.php - Executando case 'clearBetHistory'.");
            if (file_exists($betHistoryFilePath)) {
                unlink($betHistoryFilePath);
            }
            echo json_encode(['success' => true, 'message' => 'Histórico de apostas limpo com sucesso!']);
            break;

        case 'exportData':
            error_log("DEBUG_FULL: api.php - Executando case 'exportData'.");
            $dataType = $_GET['dataType'] ?? '';
            $format = $_GET['format'] ?? 'json';
            $lotteryType = $_GET['lottery'] ?? '';

            $dataToExport = [];
            $filenamePrefix = "export_" . $dataType;

            switch ($dataType) {
                case 'generated':
                    $dataToExport = loadBetHistory($betHistoryFilePath);
                    $filenamePrefix .= "_generated";
                    break;
                case 'results':
                    if (empty($lotteryType)) {
                         http_response_code(400);
                         echo json_encode(['success' => false, 'message' => 'Tipo de loteria necessário para exportar resultados.']);
                         exit;
                    }
                    $dataToExport = loadHistoricalResults($lotteryType);
                    $filenamePrefix .= "_results_" . $lotteryType;
                    break;
                case 'stats':
                    if (empty($lotteryType)) {
                         http_response_code(400);
                         echo json_encode(['success' => false, 'message' => 'Tipo de loteria necessário para exportar estatísticas.']);
                         exit;
                    }
                    $dataToExport = analyzeLotteryStatistics($lotteryType);
                    $filenamePrefix .= "_stats_" . $lotteryType;
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Tipo de dados para exportação inválido.']);
                    exit;
            }

            exportData($format, $dataToExport, $filenamePrefix);
            break;

        case 'getAIAnalysis':
            error_log("DEBUG_FULL: api.php - Executando case 'getAIAnalysis'.");
            $lotteryType = $_GET['lottery'] ?? '';
            $analysisResults = ['cluster_analysis' => [], 'message' => ''];

            if (!empty($lotteryType)) {
                error_log("DEBUG_FULL: getAIAnalysis: Iniciando análise para loteria: " . $lotteryType);

                // Verifica se a classe KMeans do Rubix ML existe
                if (!class_exists(KMeans::class)) {
                    $analysisResults['message'] = 'A biblioteca Rubix ML (KMeans) não está instalada ou não foi carregada. Por favor, instale-a via Composer (composer require rubix/ml rubix/tensor) e garanta que o autoload.php esteja incluído.';
                    error_log("DEBUG_FULL: getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit;
                }

                $historicalData = loadHistoricalResults($lotteryType);
                error_log("DEBUG_FULL: getAIAnalysis: Dados históricos carregados para " . $lotteryType . ". Total de concursos: " . count($historicalData));

                if (empty($historicalData)) {
                    $analysisResults['message'] = 'Nenhum dado histórico para análise de IA. Por favor, popule o histórico primeiro.';
                    error_log("DEBUG_FULL: getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit;
                }

                $samples = [];
                $minVal = PHP_INT_MAX;
                $maxVal = PHP_INT_MIN;

                // Coleta de dezenas e determinação de min/max para normalização
                foreach ($historicalData as $contest) {
                    $dezenas = [];
                    // Lógica para extrair dezenas (mantida do stats_handler para consistência)
                    if (isset($contest['listaDezenas']) && is_array($contest['listaDezenas'])) {
                        if (!empty($contest['listaDezenas']) && is_array($contest['listaDezenas'][0])) {
                            foreach ($contest['listaDezenas'] as $sorteio) {
                                if (is_array($sorteio)) {
                                    $dezenas = array_merge($dezenas, $sorteio);
                                }
                            }
                        } else {
                            $dezenas = $contest['listaDezenas'];
                        }
                    } else if (isset($contest['dezenasSorteadas']) && is_array($contest['dezenasSorteadas'])) {
                        $dezenas = $contest['dezenasSorteadas'];
                    }

                    // Para Loteca e Federal, a análise de dezenas não se aplica
                    if ($lotteryType === 'loteca' || $lotteryType === 'federal') {
                        $analysisResults['message'] = 'A análise de IA baseada em dezenas não se aplica diretamente a esta loteria (Loteca/Federal).';
                        error_log("DEBUG_FULL: getAIAnalysis: Erro - " . $analysisResults['message']);
                        echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                        exit;
                    }

                    // Converte dezenas para inteiros e remove zeros à esquerda
                    $processedDezenas = array_map(function($d) { return (int)ltrim((string)$d, '0'); }, $dezenas);
                    $processedDezenas = array_filter($processedDezenas); 

                    if (!empty($processedDezenas)) {
                        $samples[] = $processedDezenas;
                        // Atualiza min/max para normalização
                        foreach ($processedDezenas as $num) {
                            if ($num < $minVal) $minVal = $num;
                            if ($num > $maxVal) $maxVal = $num;
                        }
                    }
                }
                error_log("DEBUG_FULL: getAIAnalysis: Total de samples para IA: " . count($samples));

                if (empty($samples)) {
                    $analysisResults['message'] = 'Nenhum dado de dezenas válido encontrado no histórico para análise de IA.';
                    error_log("DEBUG_FULL: getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit;
                }

                try {
                    // Normalizar os dados (Min-Max Scaling)
                    // Rubix ML espera que todos os samples tenham o mesmo número de features (dezenas)
                    // Preencher com 0s se necessário para loterias com número variável de dezenas por sorteio
                    $maxFeatures = 0;
                    foreach ($samples as $sample) {
                        if (count($sample) > $maxFeatures) {
                            $maxFeatures = count($sample);
                        }
                    }
                    $normalizedSamples = [];
                    foreach ($samples as $sample) {
                        $paddedSample = array_pad($sample, $maxFeatures, 0); // Preenche com 0s
                        $normalizedSample = [];
                        foreach ($paddedSample as $value) {
                            // Evitar divisão por zero se minVal == maxVal
                            $normalizedValue = ($maxVal - $minVal) > 0 ? ($value - $minVal) / ($maxVal - $minVal) : 0;
                            $normalizedSample[] = $normalizedValue;
                        }
                        $normalizedSamples[] = $normalizedSample;
                    }
                    
                    error_log("DEBUG_FULL: getAIAnalysis: Criando dataset com " . count($normalizedSamples) . " amostras normalizadas.");
                    $dataset = Unlabeled::fromIterator($normalizedSamples);
                    error_log("DEBUG_FULL: getAIAnalysis: Dataset criado com sucesso.");

                    // Configurar KMeans
                    $k = 3; // Exemplo: 3 clusters
                    error_log("DEBUG_FULL: getAIAnalysis: Instanciando KMeans com k=" . $k);
                    $kmeans = new KMeans($k);
                    error_log("DEBUG_FULL: getAIAnalysis: KMeans instanciado.");

                    // Treinar o modelo
                    error_log("DEBUG_FULL: getAIAnalysis: Iniciando treinamento do modelo KMeans.");
                    $kmeans->train($dataset);
                    error_log("DEBUG_FULL: getAIAnalysis: Treinamento do modelo KMeans concluído.");

                    // Fazer previsões de cluster para cada amostra
                    error_log("DEBUG_FULL: getAIAnalysis: Fazendo previsões de cluster.");
                    $predictions = $kmeans->predict($dataset);
                    error_log("DEBUG_FULL: getAIAnalysis: Previsões de cluster concluídas.");

                    // Analisar os clusters
                    error_log("DEBUG_FULL: getAIAnalysis: Analisando clusters.");
                    $clusterAnalysis = [];
                    for ($i = 0; $i < $k; $i++) {
                        $clusterAnalysis[$i] = [
                            'count' => 0,
                            'numbers_in_cluster' => [],
                            'most_frequent_in_cluster' => []
                        ];
                    }

                    foreach ($predictions as $index => $clusterId) {
                        $clusterAnalysis[$clusterId]['count']++;
                        foreach ($samples[$index] as $number) {
                            $clusterAnalysis[$clusterId]['numbers_in_cluster'][$number] = ($clusterAnalysis[$clusterId]['numbers_in_cluster'][$number] ?? 0) + 1;
                        }
                    }

                    $formattedAnalysis = [];
                    foreach ($clusterAnalysis as $clusterId => $data) {
                        arsort($data['numbers_in_cluster']); // Ordena por frequência
                        $topNumbers = array_slice($data['numbers_in_cluster'], 0, 10, true); // Top 10 por cluster
                        
                        $formattedNumbers = [];
                        foreach ($topNumbers as $num => $freq) {
                            $formattedNumbers[] = "{$num} ({$freq}x)";
                        }

                        $formattedAnalysis[] = "Cluster " . ($clusterId + 1) . " (Concursos: " . $data['count'] . "): " . implode(', ', $formattedNumbers);
                    }

                    $analysisResults['cluster_analysis'] = $formattedAnalysis; // Usar nova chave
                    $analysisResults['message'] = 'Análise de IA (KMeans) concluída com sucesso. Agrupamento de sorteios gerado.';
                    error_log("DEBUG_FULL: getAIAnalysis: Análise de IA concluída com sucesso.");
                    echo json_encode(['success' => true, 'analysis' => $analysisResults]);

                } catch (Exception $e) {
                    error_log("DEBUG_FULL: Erro na análise de IA (Rubix ML) - TRY CATCH: " . $e->getMessage() . " em " . $e->getFile() . " na linha " . $e->getLine());
                    $analysisResults['message'] = 'Ocorreu um erro durante a análise de IA (Rubix ML): ' . $e->getMessage();
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de loteria não especificado para análise de IA.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
            break;
    }
} catch (Throwable $e) {
    // Este bloco captura qualquer exceção não tratada no fluxo principal
    error_log("DEBUG_FULL: Erro fatal no api.php (fora do switch): " . $e->getMessage() . " em " . $e->getFile() . " na linha " . $e->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro interno no servidor. Por favor, tente novamente mais tarde.']);
}

error_log("DEBUG_FULL: api.php - Fim do script.");
