<?php
require __DIR__ . '/vendor/autoload.php';

use Phpml\Dataset\CsvDataset;
use PhpmlExercise\Classification\SentimentPipeline;

ini_set('memory_limit', '15012M');
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
error_reporting(E_ALL);

$memoryBefore = memory_get_usage();
$peakBefore   = memory_get_peak_usage();

$modelsDir = __DIR__ . '/models';
$pattern = $modelsDir . '/sentiment_batch_*.model';

$modelFiles = glob($pattern);
if (empty($modelFiles)) {
    die("Nenhum modelo encontrado. Execute o treinamento por lotes primeiro.\n");
}

sort($modelFiles);
array_pop($modelFiles);

$models = [];
foreach ($modelFiles as $file) {
    $models[] = unserialize(file_get_contents($file));
}
echo "Carregados " . count($models) . " submodelos (excluindo o 10º lote).\n";

function ensemblePredict(array $models, string $sample): string {
    $votes = [];
    foreach ($models as $model) {
        $prediction = $model->predict([$sample]);
        $votes[] = $prediction[0];
    }
    $tally = array_count_values($votes);
    arsort($tally);
    return key($tally);
}

$dataset = new CsvDataset(__DIR__ . '/datasets/clean_tweets.csv', 1);

$samples = [];
foreach ($dataset->getSamples() as $sample) {
    $samples[] = $sample[0];
}
$allLabels = $dataset->getTargets();

$numChunks = 10;
$chunkSize = (int) ceil(count($samples) / $numChunks);
$samplesChunks = array_chunk($samples, $chunkSize);
$labelsChunks  = array_chunk($allLabels, $chunkSize);

$testSamples = end($samplesChunks);
$testLabels  = end($labelsChunks);

echo "Número de amostras do conjunto de teste (10ª parte): " . count($testSamples) . "\n";

$total = count($testSamples);
$correct = 0;
$incorrect = 0;

foreach ($testSamples as $index => $sample) {
    $predicted = ensemblePredict($models, $sample);
    
    if ($predicted === $testLabels[$index]) {
        $correct++;
    } else {
        $incorrect++;
    }
}

$accuracy = ($total > 0) ? ($correct / $total) * 100 : 0;

echo "Total de amostras testadas: {$total}\n";
echo "Acertos: {$correct}\n";
echo "Erros: {$incorrect}\n";
echo "Acurácia: " . number_format($accuracy, 2) . "%\n";

$memoryAfter = memory_get_usage();
$peakAfter   = memory_get_peak_usage();

$memoryUsed = $memoryAfter - $memoryBefore;
$peakUsed   = $peakAfter;

echo "\nMemória utilizada na predição: " . number_format($memoryUsed) . " bytes (" . number_format($memoryUsed / 1048576, 2) . " MB)\n";
echo "Pico de memória: " . number_format($peakUsed) . " bytes (" . number_format($peakUsed / 1048576, 2) . " MB)\n";
