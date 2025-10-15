<?php
  session_start();
  if(!isset($_SESSION["user_id"])){
    header("location: login.php");
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Criar Comunidade</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Dewi
  * Template URL: https://bootstrapmade.com/dewi-free-multi-purpose-html-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
    <style>
    #previewContainer {
      margin-top: 20px;
      text-align: center;
    }
    #imagePreview {
      max-width: 100%;
      max-height: 250px; /* Altura máxima da pré-visualização */
      border-radius: 8px;
      border: 2px solid #4f545c; /* Borda no estilo do site */
      object-fit: cover;
      background-color: #2c2f33;
    }
  </style>
</head>

<body class="starter-page-page">

  <?php include 'assets/php/navbar.php'?>


  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade">
      <div class="container position-relative">
        <h1>Criar Comunidade</h1>
        <p>prove que tem mais aura que seus amigos.</p>
      </div>
    </div><!-- End Page Title -->

    <!-- Cadastro Section -->
    <section id="cadastro" class="cadastro section">

      <div class="container" data-aos="fade-up">
        <div class="form-cadastro-container">
          <div class="container section-title px-0 pb-3" data-aos="fade-up">
            <h2>para farmar aura</h2>
            <p>Crie uma comunidade<br></p>
          </div>
          <form method="post" action="assets/php/criarComunidade.php" id="comunidade-form" enctype="multipart/form-data">
            
            <div class="mb-3">
              <label for="nomeComunidade" class="form-label">Nome da Comunidade</label>
              <input type="text" class="form-control" id="nomeComunidade" name="nomeComunidade" placeholder="Ex: Aventureiros da Aura" required>
            </div>
            
            <div class="mb-3">
              <label for="fotoComunidade" class="form-label">Foto da Comunidade (Opcional)</label>
              <input type="file" class="form-control" id="fotoComunidade" name="fotoComunidade" accept="image/png, image/jpeg, image/gif">
              <div class="form-text">Esta imagem será usada como o banner da sua comunidade.</div>
            </div>
            
            <div class="mb-3" id="previewContainer" style="display: none;">
                <label class="form-label">Pré-visualização:</label>
                <br>
                <img id="imagePreview" src="#" alt="Pré-visualização da imagem">
            </div>
            
            <button type="submit" class="btn btn-primary">Criar Comunidade</button>
          </form>
        </div>
      </div>
      <!-- formulario de cadastro -->

    </section><!-- /Starter Section Section -->

  </main>

  <?php require ("assets/php/rodape.php")?>


  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Pega os elementos do HTML
    const fotoInput = document.getElementById('fotoComunidade');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // Adiciona um "escutador" que percebe quando um arquivo é selecionado
    fotoInput.addEventListener('change', function() {
        const file = this.files[0]; // Pega o primeiro arquivo selecionado

        // Verifica se um arquivo foi realmente selecionado
        if (file) {
            const reader = new FileReader(); // Cria um leitor de arquivos

            // Mostra o container da pré-visualização
            previewContainer.style.display = 'block';

            // Define o que acontece quando o leitor terminar de ler o arquivo
            reader.onload = function(event) {
                // Coloca o resultado da leitura (a imagem) dentro da tag <img>
                imagePreview.setAttribute('src', event.target.result);
            }

            // Manda o leitor ler o arquivo como uma URL de dados
            reader.readAsDataURL(file);
        } else {
            // Se nenhum arquivo for selecionado, esconde a pré-visualização
            previewContainer.style.display = 'none';
        }
    });
  </script>

</body>

</html>