<?php

// lottery_functions.php

// Configurações das loterias
$lotteries = [
    'diadesorte' => ['count' => 7, 'min' => 1, 'max' => 31],
    'duplasena' => ['count' => 6, 'min' => 1, 'max' => 50],
    'federal' => ['count' => 5, 'min' => 1, 'max' => 100000], // Federal é por bilhete, não por dezenas
    'quina' => ['count' => 5, 'min' => 1, 'max' => 80],
    'loteca' => ['count' => 14, 'min' => 1, 'max' => 14], // Loteca é por resultados de jogos, não dezenas
    'lotofacil' => ['count' => 15, 'min' => 1, 'max' => 25],
    'lotomania' => ['count' => 50, 'min' => 1, 'max' => 100],
    'megasena' => ['count' => 6, 'min' => 1, 'max' => 60],
    'supersete' => ['count' => 7, 'min' => 0, 'max' => 9], // 7 colunas, de 0 a 9
    'timemania' => ['count' => 10, 'min' => 1, 'max' => 80],
    'maismilionaria' => ['count' => 6, 'min' => 1, 'max' => 50, 'trevos_count' => 2, 'trevos_min' => 1, 'trevos_max' => 6] // Nova loteria
];

/**
 * Gera um array de números aleatórios únicos para uma loteria.
 * @param int $count Quantidade de números a serem gerados.
 * @param int $min Valor mínimo para os números.
 * @param int $max Valor máximo para os números.
 * @return array Array de números gerados.
 */
function generateNumbers(int $count, int $min, int $max): array
{
    $numbers = [];
    while (count($numbers) < $count) {
        $num = rand($min, $max);
        if (!in_array($num, $numbers)) {
            $numbers[] = $num;
        }
    }
    sort($numbers);
    return $numbers;
}

?>
