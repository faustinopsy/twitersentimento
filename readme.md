# Analisador de Sentimentos com PHP-ML
Este projeto consiste em um analisador de sentimentos desenvolvido em PHP, que utiliza a biblioteca PHP-ML para construir e treinar modelos de classificação (neste caso, utilizando o algoritmo Naive Bayes) para identificar o sentimento de textos – por exemplo, se um tweet é positivo, negativo ou neutro. O sistema foi concebido para funcionar tanto via CLI (linha de comando) quanto com uma interface web, e faz uso de técnicas de processamento em lotes para contornar limitações de hardware ao trabalhar com datasets relativamente grandes.

## Introdução
Análise de Sentimentos é uma técnica de processamento de linguagem natural (NLP) que tenta identificar e extrair informações subjetivas de um texto. Ela é amplamente utilizada em aplicações como:

- Monitoramento de marcas e reputação online;
- Análise de feedback de clientes;
- Monitoramento de redes sociais;
- Pesquisas de mercado;

## Variações e Aplicações
- Modelos Baseados em Regras vs. Aprendizado de Máquina: 
Algumas implementações utilizam regras definidas manualmente, enquanto outras se baseiam em algoritmos de aprendizado supervisionado ou mesmo redes neurais.
- Abordagens Multiclasse: 
Em alguns cenários, além das categorias básicas (positivo, negativo, neutro), podem existir subcategorias ou classificações mais detalhadas.
- Integração com Sistemas de Recomendação e Monitoramento: 
Os resultados da análise de sentimentos podem ser usados para ajustar campanhas de marketing, identificar crises de reputação ou melhorar a experiência do usuário.

## Desafios e Soluções para Grandes Volumes de Dados
Limitações de Hardware e Algoritmos
Ao trabalhar com datasets grandes, é comum encontrar limitações de memória e desempenho, principalmente ao utilizar algoritmos que criam estruturas de dados extensas (como grandes vocabulários em modelos de texto).

- No meu caso:

- Limitações de Memória: 
Treinar o modelo em um único lote poderia extrapolar os limites de memória do PHP, mesmo com um memory_limit alto, tentei umas 5 vezes com único meio aumentando a memoria da aplicação.
- Algoritmo Utilizado: 
Utilizei o Naive Bayes, que é simples e rápido, mas pode não ser o ideal para capturar interações complexas entre os termos. Entretanto, sua simplicidade permitiu uma implementação eficiente.
- Estratégia Adotada: 
Treinamento em Lotes para contornar essas limitações, o projeto adota uma abordagem de treinamento por lotes
O dataset é dividido em 10 partes (chunks).
Cada parte é utilizada para treinar um modelo parcial (um pipeline que engloba vetorização, transformação Tf–Idf e classificação).
Esses submodelos são serializados e salvos individualmente.
Na fase de predição, os submodelos (exceto o que corresponde à parte de teste) são carregados e suas predições são combinadas via votação (ensemble) para produzir a predição final.
Essa estratégia permite reduzir o consumo de memória durante o treinamento, conforme demonstrado pelo log:

## Treinamento por lotes concluído.
Memória utilizada durante o treinamento: 440,528,096 bytes (420.12 MB)
Pico de memória utilizado: 775,715,840 bytes (739.78 MB)
* para rodar a UI precisa ter 5Gb de RAM
logo precisa desse ajuste no ensemble_predict_ui:
ini_set('memory_limit', '5024M');
```
Memória utilizada na predição: 4,367,601,488 bytes (4,165.27 MB)
```

Além disso, testes futuros serão realizados utilizando um memory_limit menor (por exemplo, 1024M) para verificar se os modelos ainda podem ser gerados com sucesso.

## Tecnologias e Bibliotecas
- PHP-ML: 
Biblioteca de machine learning para PHP que oferece uma variedade de algoritmos de classificação, regressão, clustering, entre outros.
- Algoritmos usados neste projeto:
Naive Bayes: Para classificação dos sentimentos.
- Token Count Vectorizer e Tf–Idf Transformer: 
Para pré-processamento e transformação dos textos em vetores de características.

Composer: Gerenciador de dependências para PHP. O projeto foi instalado e gerenciado via Composer.

## Estrutura do Projeto
```
├── datasets
│   ├── Tweets.csv              # Dataset original (ex.: tweets com várias colunas)
│   └── clean_tweets.csv        # Dataset limpo (apenas colunas relevantes: texto e sentimento)
├── models                      # Diretório onde os submodelos treinados são armazenados
├── src
│   └── Classification
│       └── SentimentPipeline.php  # Pipeline que combina pré-processamento e o classificador
├── vendor                      # Dependências instaladas via Composer
├── train_batches.php           # Script CLI para treinamento em lotes
├── ensemble_predict.php        # Script CLI para avaliação do ensemble
├── ensemble_predict_ui.php     # Endpoint web para predição
├── index.php                   # Interface web principal para entrada do texto
└── README.md                   # (Este arquivo)
```

## Instalação
- Clone o repositório:
```
git clone https://github.com/faustinopsy/twitersentimento.git
cd twitersentimento

```

- Instale as dependências via Composer:
```
composer install

```


## Uso
1. Limpeza dos Dados
Utilize o script de limpeza para gerar um dataset reduzido e limpo (apenas com as colunas relevantes):
```
php generateCleanDataset.php

```

2. Treinamento via CLI
O treinamento é realizado em lotes para contornar limitações de memória:
```
php train_batches.php
```

Este script:

Lê o dataset limpo.
Divide o dataset em 10 partes.
Treina um pipeline para cada parte e salva os submodelos em models/.
Exibe o uso de memória durante o treinamento.

3. Predição via CLI
Para avaliar o desempenho do ensemble, utilize:
```
php ensemble_predict.php
```

Este script:

Carrega os submodelos (exceto o 10º lote, reservado para teste).
Realiza a predição no conjunto de teste (10ª parte).
Calcula a acurácia e exibe estatísticas de memória.

4. Predição via Interface Web
Acesse a página index.php no seu navegador. Nela, você encontrará um formulário para inserir um texto e obter a predição do sentimento. O resultado é apresentado em uma interface moderna, com um loading customizado e informações de memória utilizadas durante a predição.

Considerações sobre Memória e Hardware
- Consumo Atual:
Durante o treinamento, o consumo de memória foi aproximadamente 420 MB, com um pico em torno de 740 MB.

- Testes com Menor Memória:
O projeto permitirá realizar novos testes alterando o ini_set('memory_limit', '1024M') para verificar se o treinamento e a geração dos modelos continuam funcionando corretamente com limites de memória mais restritos.

## Limitações:

Grandes volumes de dados podem ainda exigir hardware robusto ou estratégias mais avançadas, como processamento distribuído.
O uso do Naive Bayes e dos transformadores (Token Count e Tf–Idf) pode limitar a capacidade do modelo de capturar interações complexas entre termos, mas sua simplicidade é vantajosa para prototipagem rápida e baixo custo computacional.
Datasets vs. Modelos
- Dataset:
Conjunto de dados de entrada utilizado para treinar e testar o modelo. No projeto, temos:
Tweets.csv: Dataset original com múltiplas colunas.
clean_tweets.csv: Dataset limpo e reduzido para a análise de sentimentos.
- Modelos:
São as representações treinadas que capturam o conhecimento aprendido a partir dos dados. No projeto, cada lote gera um submodelo (um pipeline) que é serializado e salvo na pasta models. Durante a predição, esses submodelos são carregados e combinados (via ensemble) para gerar a predição final.

## Conclusão
Este projeto demonstra uma abordagem prática para análise de sentimentos utilizando PHP e PHP-ML, superando limitações de hardware através do treinamento por lotes e combinando modelos em um ensemble para predição. A integração via CLI e interface web facilita tanto o desenvolvimento quanto o uso do sistema, oferecendo uma base para futuras melhorias e adaptações para datasets maiores ou abordagens mais complexas.

