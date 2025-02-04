<?php
require __DIR__ . '/vendor/autoload.php';

use PhpmlExercise\Classification\SentimentPipeline;

ini_set('memory_limit', '5024M');
set_time_limit(0);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
error_reporting(E_ALL);


$memoryBefore = memory_get_usage();
$peakBefore   = memory_get_peak_usage();

$modelsDir = __DIR__ . '/models';
$modelFiles = glob($modelsDir . '/sentiment_batch_*.model');
if (empty($modelFiles)) {
    die("Nenhum modelo encontrado. Execute o treinamento por lotes primeiro.");
}

$models = [];
foreach ($modelFiles as $file) {
    $models[] = unserialize(file_get_contents($file));
}

function ensemblePredict(array $models, array $samples) {
    $votes = [];
    foreach ($models as $model) {
        $prediction = $model->predict($samples);
        $votes[] = $prediction[0];
    }
    $tally = array_count_values($votes);
    arsort($tally);
    return key($tally);
}

if (!isset($_POST['text']) || empty($_POST['text'])) {
    die("Por favor, insira um texto para análise.");
}

$inputText = $_POST['text'];
$samplesForPrediction = [$inputText];
$finalPrediction = ensemblePredict($models, $samplesForPrediction);

$memoryAfter = memory_get_usage();
$peakAfter   = memory_get_peak_usage();

$memoryUsed = $memoryAfter - $memoryBefore;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Resultado da Análise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="card-title mb-0">Resultado da Análise de Sentimentos</h2>
            </div>
            <div class="card-body">
                <p class="lead"><strong>Texto:</strong> <?php echo htmlspecialchars($inputText, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="lead"><strong>Sentimento Predito (ensemble):</strong> <?php echo htmlspecialchars($finalPrediction, ENT_QUOTES, 'UTF-8'); ?></p>
                <hr>
                <p><strong>Memória utilizada na predição:</strong> <?php echo number_format($memoryUsed) . " bytes (" . number_format($memoryUsed / 1048576, 2) . " MB)"; ?></p>
                <a href="index.php" class="btn btn-outline-primary mt-3">Analisar outro texto</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
