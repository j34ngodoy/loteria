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

header('Content-Type: application/json');

// --- Inclusão de arquivos de funções e manipuladores essenciais ---
// Verifica e inclui lottery_functions.php
if (!file_exists('lottery_functions.php')) {
    error_log("Erro Fatal: 'lottery_functions.php' não encontrado.");
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor: Arquivo de funções de loteria ausente.']);
    exit;
}
require_once 'lottery_functions.php';

// Verifica e inclui stats_handler.php
if (!file_exists('stats_handler.php')) {
    error_log("Erro Fatal: 'stats_handler.php' não encontrado.");
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor: Arquivo de manipulador de estatísticas ausente.']);
    exit;
}
require_once 'stats_handler.php';

// Inclui o autoloader do Composer para PHP-ML
// Certifique-se de que o Composer e a biblioteca PHP-ML estão instalados
// Execute 'composer install' e 'composer require php-ai/php-ml' na raiz do seu projeto
$composerAutoloadPath = 'vendor/autoload.php';
// Removido o if/else file_exists() conforme sua descoberta, pois estava causando erro 500
require_once $composerAutoloadPath;
use Phpml\Association\Apriori;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
use Phpml\Metric\Accuracy;
use Phpml\ModelManager;
use Phpml\Clustering\KMeans; // Adicionado para compatibilidade, se necessário


// Funções auxiliares (anteriormente em outros arquivos ou integradas)
// Esta função é uma versão simplificada para o propósito de exportação/histórico.
// Se você tinha uma versão mais complexa em `api_handler.php` ou `export_handler.php`,
// por favor, me avise para que possamos integrá-la.
function loadBetHistory(string $filePath): array
{
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        // Retorna um array vazio se o JSON estiver inválido
        return json_decode($content, true) ?: [];
    }
    return [];
}

// Função para exportação (anteriormente em export_handler.php)
function exportData(string $format, array $data, string $filenamePrefix): void
{
    // Removido ob_end_clean() daqui, pois o ob_start() foi removido do início do arquivo.
    // Se o buffer de saída estiver ativo por outro motivo, pode causar problemas.

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
                // Assume que o primeiro item tem todas as chaves para o cabeçalho
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
            $mimeType = 'text/plain'; // Não há um MIME type específico para SQL de exportação simples
            $content = "-- SQL Export for " . $filenamePrefix . "\n\n";
            // Exemplo muito básico de SQL. Para uso real, precisaria de tabelas e estrutura.
            foreach ($data as $item) {
                // Simplificado: apenas JSON string no campo 'data'
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

try {
    switch ($action) {
        case 'getResults':
            $lotteryType = $_GET['lottery'] ?? '';
            $type = $_GET['type'] ?? 'current'; // 'current', 'previous', 'specific'
            $contestNumber = $_GET['contestNumber'] ?? null;

            // URL base da API da Caixa Econômica Federal
            // Usando a URL que você indicou que estava funcionando anteriormente
            $baseUrl = 'https://servicebus2.caixa.gov.br/portaldeloterias/api'; // Revertido para a URL antiga
            $fullUrl = '';

            // Mapeamento de tipos de loteria para os nomes da loteria na API antiga
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
                'maismilionaria' => 'maismilionaria' // +Milionária
            ];

            if (!isset($lotteryApiMap[$lotteryType])) {
                echo json_encode(['error' => true, 'message' => 'Tipo de loteria inválido.']);
                exit; // Removido ob_end_clean()
            }

            $apiPath = $lotteryApiMap[$lotteryType];

            if ($type === 'current') {
                $fullUrl = "{$baseUrl}/{$apiPath}";
            } elseif ($type === 'specific' && $contestNumber) {
                // Para a API antiga, o formato para concurso específico pode ser diferente.
                // Mantendo o formato atual, mas pode precisar de ajuste se a API antiga não suportar.
                $fullUrl = "{$baseUrl}/{$apiPath}/{$contestNumber}";
            } else {
                echo json_encode(['error' => true, 'message' => 'Parâmetros de consulta inválidos.']);
                exit; // Removido ob_end_clean()
            }

            // Log da URL que será solicitada à API da Caixa
            error_log("getResults: Solicitando à API da Caixa: " . $fullUrl);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Apenas para ambiente de desenvolvimento, remover em produção!
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                error_log("cURL Error: " . $error_msg);
                echo json_encode(['error' => true, 'message' => 'Erro ao conectar à API da Caixa: ' . $error_msg]);
                curl_close($ch);
                exit; // Removido ob_end_clean()
            }

            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON Decode Error: " . json_last_error_msg() . " Response: " . $response);
                    echo json_encode(['error' => true, 'message' => 'Erro ao decodificar a resposta da API.']);
                    exit; // Removido ob_end_clean()
                }
                echo json_encode($data);
            } else {
                error_log("API Response Error: HTTP " . $httpCode . " Response: " . $response);
                echo json_encode(['error' => true, 'message' => 'Erro ao obter resultados da API da Caixa. Código HTTP: ' . $httpCode]);
            }
            // Removido ob_end_clean()
            break;

        case 'populateHistoricalData':
            $lotteryType = $_GET['lottery'] ?? '';
            $limit = intval($_GET['limit'] ?? 50);

            $historicalData = loadHistoricalResults($lotteryType);
            $currentContestNumber = null;

            // Obter o último concurso conhecido para iniciar a busca
            if (!empty($historicalData)) {
                // Ordena os dados pelo número do concurso em ordem decrescente para pegar o mais recente
                usort($historicalData, function($a, $b) {
                    return ($b['numero'] ?? 0) <=> ($a['numero'] ?? 0);
                });
                $currentContestNumber = $historicalData[0]['numero'] ?? null;
                error_log("populateHistoricalData: Último concurso conhecido para " . $lotteryType . ": " . $currentContestNumber);
            }

            // Se não há histórico ou queremos popular mais, busca a partir do concurso atual da API
            if ($currentContestNumber === null) {
                // Usando cURL para buscar o concurso atual, mais robusto que file_get_contents
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
                    error_log("populateHistoricalData: Concurso atual da API para " . $lotteryType . ": " . $currentContestNumber);
                }
            }

            if ($currentContestNumber === null) {
                echo json_encode(['success' => false, 'message' => 'Não foi possível determinar o concurso inicial para popular o histórico.']);
                exit; // Removido ob_end_clean()
            }

            $newlyFetchedCount = 0;
            $allResults = $historicalData; // Começa com os dados existentes

            // Loop para buscar concursos anteriores até o limite
            for ($i = 0; $i < $limit; $i++) {
                $contestToFetch = $currentContestNumber - $i;
                if ($contestToFetch <= 0) {
                    break; // Não há mais concursos válidos para buscar
                }

                // Verifica se o concurso já existe no histórico
                $existsInHistory = false;
                foreach ($allResults as $existingResult) {
                    if (($existingResult['numero'] ?? null) == $contestToFetch) {
                        $existsInHistory = true;
                        break;
                    }
                }

                if ($existsInHistory) {
                    error_log("populateHistoricalData: Concurso " . $contestToFetch . " já existe no histórico para " . $lotteryType . ". Pulando.");
                    continue; // Pula se já existe
                }

                error_log("populateHistoricalData: Buscando concurso " . $contestToFetch . " para " . $lotteryType);
                // Usando cURL para buscar concursos específicos, mais robusto que file_get_contents
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
                    error_log("populateHistoricalData: Adicionado concurso " . $result['numero'] . " ao histórico de " . $lotteryType);
                } else {
                    error_log("populateHistoricalData: Falha ao buscar concurso " . $contestToFetch . " para " . $lotteryType . ". Resposta: " . json_encode($result));
                }
                // Pequeno delay para evitar sobrecarregar a API
                usleep(100000); // 100ms
            }

            // Remove duplicatas e salva
            $uniqueResults = [];
            $seenContests = []; // Usando array associativo PHP para rastrear números de concurso vistos
            foreach ($allResults as $res) {
                $contestNum = $res['numero'] ?? null;
                if ($contestNum !== null && !isset($seenContests[$contestNum])) {
                    $uniqueResults[] = $res;
                    $seenContests[$contestNum] = true;
                }
            }

            saveHistoricalResults($lotteryType, $uniqueResults);
            echo json_encode(['success' => true, 'message' => "Histórico populado com sucesso. Foram adicionados {$newlyFetchedCount} novos concursos."]);
            // Removido ob_end_clean()
            break;

        case 'getStatistics':
            $lotteryType = $_GET['lottery'] ?? '';
            if (!empty($lotteryType)) {
                $stats = analyzeLotteryStatistics($lotteryType);
                echo json_encode(['success' => true, 'stats' => $stats]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de loteria não especificado para estatísticas.']);
            }
            // Removido ob_end_clean()
            break;

        case 'saveBetHistory':
            $input = json_decode(file_get_contents('php://input'), true);
            $lotteryType = $input['lotteryType'] ?? '';
            $numbers = $input['numbers'] ?? [];

            if (empty($lotteryType) || empty($numbers)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos para salvar o histórico.']);
                exit; // Removido ob_end_clean()
            }

            // Usando a função loadBetHistory definida acima
            $history = loadBetHistory($betHistoryFilePath);

            $history[] = [
                'lotteryType' => $lotteryType,
                'generatedNumbers' => $numbers,
                'generationDate' => date('Y-m-d H:i:s')
            ];

            file_put_contents($betHistoryFilePath, json_encode($history, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true, 'message' => 'Aposta salva com sucesso!']);
            // Removido ob_end_clean()
            break;

        case 'getBetHistory':
            // Usando a função loadBetHistory definida acima
            $history = loadBetHistory($betHistoryFilePath);
            echo json_encode(['success' => true, 'history' => $history]);
            // Removido ob_end_clean()
            break;

        case 'clearBetHistory':
            if (file_exists($betHistoryFilePath)) {
                unlink($betHistoryFilePath); // Deleta o arquivo
                echo json_encode(['success' => true, 'message' => 'Histórico de apostas limpo com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nenhum histórico para limpar.']);
            }
            // Removido ob_end_clean()
            break;

        case 'exportData':
            $dataType = $_GET['dataType'] ?? '';
            $format = $_GET['format'] ?? 'json';
            $lotteryType = $_GET['lottery'] ?? ''; // Pode ser necessário para 'results' ou 'stats'

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
                         exit; // Removido ob_end_clean()
                    }
                    $dataToExport = loadHistoricalResults($lotteryType);
                    $filenamePrefix .= "_results_" . $lotteryType;
                    break;
                case 'stats':
                    if (empty($lotteryType)) {
                         http_response_code(400);
                         echo json_encode(['success' => false, 'message' => 'Tipo de loteria necessário para exportar estatísticas.']);
                         exit; // Removido ob_end_clean()
                    }
                    $dataToExport = analyzeLotteryStatistics($lotteryType);
                    $filenamePrefix .= "_stats_" . $lotteryType;
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Tipo de dados para exportação inválido.']);
                    exit; // Removido ob_end_clean()
            }

            exportData($format, $dataToExport, $filenamePrefix);
            // ob_end_clean() já é chamado dentro de exportData, então não precisa aqui
            break;

        case 'getAIAnalysis':
            $lotteryType = $_GET['lottery'] ?? '';
            $analysisResults = ['aprioriRules' => [], 'message' => ''];

            if (!empty($lotteryType)) {
                error_log("getAIAnalysis: Iniciando análise para loteria: " . $lotteryType);

                // Verifica se a classe Apriori existe (indicando que PHP-ML foi carregado)
                if (!class_exists(Apriori::class)) {
                    $analysisResults['message'] = 'A biblioteca PHP-ML (Apriori) não está instalada ou não foi carregada. Por favor, instale-a via Composer (composer require php-ai/php-ml) e garanta que o autoload.php esteja incluído.';
                    error_log("getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit; // Removido ob_end_clean()
                }

                $historicalData = loadHistoricalResults($lotteryType);
                error_log("getAIAnalysis: Dados históricos carregados para " . $lotteryType . ". Total de concursos: " . count($historicalData));


                if (empty($historicalData)) {
                    $analysisResults['message'] = 'Nenhum dado histórico para análise de IA. Por favor, popule o histórico primeiro.';
                    error_log("getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit; // Removido ob_end_clean()
                }

                $samples = [];
                $targets = []; // Apriori não usa targets diretamente, mas train espera um segundo argumento

                // Coleta de dezenas para Apriori
                foreach ($historicalData as $contest) {
                    $dezenas = [];
                    if (isset($contest['listaDezenas']) && is_array($contest['listaDezenas'])) {
                        if (!empty($contest['listaDezenas']) && is_array($contest['listaDezenas'][0])) {
                            // Caso Duplasena (array de arrays, 2 sorteios)
                            foreach ($contest['listaDezenas'] as $sorteio) {
                                if (is_array($sorteio)) {
                                    $dezenas = array_merge($dezenas, $sorteio);
                                }
                            }
                        } else {
                            // Outras loterias (listaDezenas simples)
                            $dezenas = $contest['listaDezenas'];
                        }
                    } else if (isset($contest['dezenasSorteadas']) && is_array($contest['dezenasSorteadas'])) {
                        // Algumas APIs podem usar 'dezenasSorteadas'
                        $dezenas = $contest['dezenasSorteadas'];
                    }

                    // Para Loteca e Federal, a análise de dezenas não se aplica
                    if ($lotteryType === 'loteca' || $lotteryType === 'federal') {
                        $analysisResults['message'] = 'A análise de IA baseada em dezenas não se aplica diretamente a esta loteria (Loteca/Federal).';
                        error_log("getAIAnalysis: Erro - " . $analysisResults['message']);
                        echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                        exit; // Removido ob_end_clean()
                    }

                    // Converte dezenas para inteiros e remove zeros à esquerda
                    $processedDezenas = array_map(function($d) { return (int)ltrim((string)$d, '0'); }, $dezenas);
                    $processedDezenas = array_filter($processedDezenas); // Remove valores vazios ou 0 se ltrim resultar em vazio

                    if (!empty($processedDezenas)) {
                        $samples[] = $processedDezenas;
                        $targets[] = ''; // Adiciona um target vazio para cada sample
                    }
                }
                error_log("getAIAnalysis: Total de samples para IA: " . count($samples));

                if (empty($samples)) {
                    $analysisResults['message'] = 'Nenhum dado de dezenas válido encontrado no histórico para análise de IA.';
                    error_log("getAIAnalysis: Erro - " . $analysisResults['message']);
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                    exit; // Removido ob_end_clean()
                }

                try {
                    // Cria um dataset
                    $dataset = new ArrayDataset($samples, $targets);
                    
                    // Para Apriori, geralmente treinamos com todos os dados se o objetivo é encontrar todas as regras
                    $apriori = new Apriori($support = 0.05, $confidence = 0.5); // Ajuste os valores de suporte e confiança conforme necessário

                    // CORREÇÃO: Passando samples e targets para train()
                    $apriori->train($dataset->getSamples(), $dataset->getTargets());

                    $rules = $apriori->getRules();
                    $formattedRules = [];
                    foreach ($rules as $rule) {
                        $antecedent = implode(', ', $rule['antecedent']);
                        $consequent = implode(', ', $rule['consequent']);
                        $support = round($rule['support'], 3);
                        $confidence = round($rule['confidence'], 3);
                        $formattedRules[] = "Se ({$antecedent}) então ({$consequent}) [Suporte: {$support}, Confiança: {$confidence}]";
                    }
                    $analysisResults['aprioriRules'] = $formattedRules;
                    $analysisResults['message'] = 'Análise de IA concluída com sucesso. Regras de associação geradas.';
                    error_log("getAIAnalysis: Análise de IA concluída com sucesso.");
                    echo json_encode(['success' => true, 'analysis' => $analysisResults]);

                } catch (Exception $e) {
                    error_log("Erro na análise de IA (PHP-ML): " . $e->getMessage());
                    $analysisResults['message'] = 'Ocorreu um erro durante a análise de IA: ' . $e->getMessage();
                    echo json_encode(['success' => false, 'analysis' => $analysisResults, 'message' => $analysisResults['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de loteria não especificado para análise de IA.']);
            }
            // Removido ob_end_clean()
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
            // Removido ob_end_clean()
            break;
    }
} catch (Throwable $e) {
    // Este bloco captura qualquer exceção não tratada no fluxo principal
    error_log("Erro fatal no api.php (fora do switch): " . $e->getMessage() . " em " . $e->getFile() . " na linha " . $e->getLine());
    http_response_code(500);
    // Removido ob_end_clean()
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro interno no servidor. Por favor, tente novamente mais tarde.']);
}

exit; // Garante que o script termina aqui
?>
