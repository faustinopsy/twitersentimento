<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Análise de Sentimentos - Ensemble</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    #loadingOverlay {
      display: none;
      position: fixed;
      z-index: 9999;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
      align-items: center;
      justify-content: center;
    }
    #loadingOverlay .spinner-border {
      width: 3rem;
      height: 3rem;
    }
  </style>
</head>
<body>
  <div id="loadingOverlay">
    <div class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
      </div>
      <div class="mt-2">
        <h5>Carregando, por favor aguarde...</h5>
      </div>
    </div>
  </div>
  
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0">Análise de Sentimentos</h2>
          </div>
          <div class="card-body">
            <form method="post" action="ensemble_predict_ui.php">
              <div class="mb-3">
                <label for="text" class="form-label">Insira o texto:</label>
                <textarea id="text" name="text" rows="5" class="form-control" placeholder="Digite o texto aqui..."></textarea>
              </div>
              <button id="botao" type="submit" class="btn btn-primary w-100">Analisar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.querySelector("form").addEventListener("submit", function() {
      document.getElementById("loadingOverlay").style.display = "flex";
    });
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
