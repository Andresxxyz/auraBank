<?php
session_start();
require('assets/php/conexao.php');
$logado = false;
$comunidade = false;


if (isset($_SESSION["user_id"])) {
  $logado = true;
  $sql = 'SELECT * FROM comunidadeusuario WHERE idUsuario=?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    'i',
    $_SESSION['user_id']
  );
  $stmt->execute();
  $resultado = $stmt->get_result();
  if ($resultado->num_rows > 0) {
    $comunidade = true;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Página Inicial</title>
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
</head>

<body class="index-page">

  <?php include 'assets/php/navbar.php' ?>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div class="container d-flex flex-column align-items-center">
        <img src="assets/img/aurabank_logo.png" alt="logo do aura bank">
        <br>
        <br>
        <h2 data-aos="fade-up" data-aos-delay="100">FARME SUA AURA</h2>
        <p data-aos="fade-up" data-aos-delay="200">Somos o maior banco de aura do mundo!</p>
        <div class="d-flex mt-4" style="gap: 2rem;" data-aos="fade-up" data-aos-delay="300">
        <?php
        if ($logado) {

          if ($comunidade) {
            echo '<a href="minha_comunidade.php" class="btn-get-started" data-aos="fade-up" data-aos-delay="300">Minha Comunidade</a>';

          } else {
            
            echo '<a href="searchComunidade.php" class="btn-get-started" data-aos="fade-up" data-aos-delay="300">Procurar ou Criar Comunidade</a>';
          }
        } else {
          echo '
          <a href="cadastro.php" class="btn-get-started">Cadastre-se</a>
          <a href="login.php" class="btn-login">Entre</a>';
        }
        ?>
        </div>
        
          
       
      </div>

    </section><!-- /Hero Section -->


  </main>

  <?php require("assets/php/rodape.php") ?>


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

</body>



</html>