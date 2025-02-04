<?php
require __DIR__ . '/vendor/autoload.php';

use Phpml\Dataset\CsvDataset;
use PhpmlExercise\Classification\SentimentPipeline;

ini_set('memory_limit', '1024M');
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
error_reporting(E_ALL);

$memoryBefore      = memory_get_usage();
$peakMemoryBefore  = memory_get_peak_usage();

$dataset = new CsvDataset(__DIR__ . '/datasets/clean_tweets.csv', 1);

$samples = [];
foreach ($dataset->getSamples() as $sample) {
    $samples[] = $sample[0];
}
$labels = $dataset->getTargets();

$numBatches   = 10;
$totalSamples = count($samples);
$batchSize    = (int) ceil($totalSamples / $numBatches);

echo "Total de amostras: {$totalSamples}\n";
echo "Dividindo em {$numBatches} lotes (aprox. {$batchSize} amostras cada)...\n";

$samplesBatches = array_chunk($samples, $batchSize);
$labelsBatches  = array_chunk($labels, $batchSize);

$modelsDir = __DIR__ . '/models';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0777, true);
}

for ($i = 0; $i < count($samplesBatches); $i++) {
    echo "Treinando lote " . ($i + 1) . " de " . count($samplesBatches) . "...\n";
    
    $pipeline = new SentimentPipeline();
    $pipeline->train($samplesBatches[$i], $labelsBatches[$i]);
    
    $modelFile = $modelsDir . '/sentiment_batch_' . $i . '.model';
    file_put_contents($modelFile, serialize($pipeline));
    
    echo "Lote " . ($i + 1) . " treinado e salvo em: {$modelFile}\n";
}

$memoryAfter      = memory_get_usage();
$peakMemoryAfter  = memory_get_peak_usage();

$memoryUsed       = $memoryAfter - $memoryBefore;
$peakMemoryUsed   = $peakMemoryAfter;

echo "Treinamento por lotes concluído.\n";
echo "Memória utilizada durante o treinamento: " . number_format($memoryUsed) . " bytes (" . number_format($memoryUsed / 1048576, 2) . " MB)\n";
echo "Pico de memória utilizado: " . number_format($peakMemoryUsed) . " bytes (" . number_format($peakMemoryUsed / 1048576, 2) . " MB)\n";
