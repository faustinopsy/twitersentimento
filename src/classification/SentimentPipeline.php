<?php
namespace PhpmlExercise\Classification;

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Classification\NaiveBayes;

class SentimentPipeline
{
    /** @var TokenCountVectorizer */
    protected $vectorizer;

    /** @var TfIdfTransformer */
    protected $tfidfTransformer;

    /** @var NaiveBayes */
    protected $classifier;

    public function __construct()
    {
        $this->vectorizer = new TokenCountVectorizer(new WordTokenizer());
        $this->tfidfTransformer = new TfIdfTransformer();
        $this->classifier = new NaiveBayes();
    }

    /**
     * Treina todas as etapas do pipeline.
     *
     * @param string[] $samples Textos de entrada
     * @param array    $labels  Rótulos correspondentes
     */
    public function train(array $samples, array $labels)
    {
        // 1. Vetorização: cria o vocabulário com base nos textos
        $this->vectorizer->fit($samples);
        $this->vectorizer->transform($samples);

        // 2. Transformação Tf–Idf
        $this->tfidfTransformer->fit($samples);
        $this->tfidfTransformer->transform($samples);

        // 3. Treina o classificador
        $this->classifier->train($samples, $labels);
    }

    /**
     * Realiza a predição para um conjunto de textos.
     *
     * @param string[] $samples Textos para previsão
     *
     * @return array Predições geradas pelo classificador
     */
    public function predict(array $samples)
    {
        // Aqui usamos os mesmos objetos de pré-processamento treinados
        $this->vectorizer->transform($samples);
        $this->tfidfTransformer->transform($samples);

        return $this->classifier->predict($samples);
    }
}
