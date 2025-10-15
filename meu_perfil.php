<?php
include('assets/php/conexao.php');
session_start();
if (!isset($_SESSION['user_id'])) {
  header("location: login.php");
}
$sql = "SELECT username, email, aura, fotoPerfil FROM usuario WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$username = $usuario["username"];
$email = $usuario["email"];
$aura = $usuario["aura"];
$fotoPerfil = $usuario["fotoPerfil"];
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Meu Perfil</title>
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
    .container {
      width: 100%;
    }

    .usuario-container {
      width: 100%;
    }

    /* O Card Principal */
    .profile-card {
      background-color: #2c2f33;
      /* Cor de fundo do card */
      color: #f0f0f0;
      /* Cor do texto principal */
      border-radius: 20px;
      /* Bordas arredondadas */
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      /* Sombra para dar profundidade */
      width: 100%;
      padding: 30px;
      text-align: center;
      border: 1px solid #4f545c;
      display: flex;
      flex-direction: column;
      gap: 20px;
      /* Espaçamento entre os elementos */
    }

    /* Container da Foto de Perfil */
    .profile-picture-container {
      position: relative;
      margin: 0 auto;
      /* Centraliza o container */
      width: 150px;
      height: 150px;
    }

    .profile-picture {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      /* Deixa a imagem redonda */
      border: 5px solid #4f545c;
      /* Borda na foto */
      object-fit: cover;
      /* Garante que a imagem não distorça */
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    /* Botão de Editar Foto (ícone de câmera) */
    #edit-photo-btn {
      position: absolute;
      bottom: 5px;
      right: 5px;
      background-color: var(--accent-color);
      /* Cor do botão */
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      font-size: 18px;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    #edit-photo-btn:hover {
      transform: scale(1.1);
    }

    /* Informações do Usuário */
    .profile-info .username {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 0;
      color: #ffffff;
    }

    .profile-info .community-info {
      font-size: 1rem;
      color: #adb5bd;
      /* Cor mais suave para a comunidade */
      margin-top: 5px;
    }

    .profile-info .community-info .community-name {
      font-weight: 600;
      color: #e9ecef;
    }

    /* Stats da Aura */
    .aura-stats {
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 12px;
      padding: 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
    }

    .aura-stats .aura-label {
      font-size: 0.9rem;
      color: #adb5bd;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .aura-stats .aura-value {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--accent-color);
      /* Destaque em dourado para a aura */
      display: flex;
      align-items: center;
      gap: 10px;
      text-shadow: 0 4px 15px var(--accent-color);
    }

    .aura-stats .aura-value .bi-star-fill {
      margin-bottom: 5px;
    }

    /* Botão Principal de Ação */
    .btn-edit-profile {
      width: 100%;
      font-weight: bold;
      background-color: var(--accent-color);
      border: none;
      opacity: 0.9;
      transition: 0.3s;
      color: #fff;
      font-weight: bold;
      border-radius: 5px;
      padding: 1rem 2rem;
      width: 300px;
      margin: 0 auto;
      opacity: 0.9;
      transition: 0.5s;
    }

    .btn-edit-profile:hover {
      opacity: 1;
      color: #fff;
    }

    /* popup editar foto */

    .popup-editar-foto {
      display: none;
      position: fixed;
      z-index: 1001;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background-color: #000910;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      width: 30%;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .popup-editar-foto .popup-content {
      padding: 20px 25px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .popup-editar-foto .popup-header {
      width: 100%;
      display: flex;
      justify-content: center;
      position: relative;
      margin-bottom: 20px;
    }

    .popup-editar-foto .popup-header h3 {
      margin: 0;
      font-size: 22px;
      font-weight: 500;
      color: #fff;
    }

    .popup-editar-foto .fechar-popup {
      position: absolute;
      top: -10px;
      right: 10px;
      font-size: 48px;
      font-weight: normal;
      color: #666;
      background: none;
      border: none;
      cursor: pointer;
    }

    .popup-editar-foto .popup-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
    }

    .popup-editar-foto .image-preview-container {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background-color: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-bottom: 25px;
      border: 1px solid #ddd;
    }

    .popup-editar-foto #previewFotoPopup {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .popup-editar-foto .file-input-wrapper {
      display: flex;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
      overflow: hidden;
      margin-bottom: 25px;
      font-size: 14px;
      align-items: center;
    }

    .popup-editar-foto .file-input-label {
      background-color: #f7f7f7;
      padding: 8px 12px;
      cursor: pointer;
      border-right: 1px solid #ccc;
      color: #555;
      white-space: nowrap;
    }

    .popup-editar-foto #fileNamePopup {
      padding: 8px 12px;
      color: #777;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex-grow: 1;
    }

    .popup-editar-foto .popup-footer {
      display: flex;
      gap: 15px;
      justify-content: center;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      z-index: 999;
      display: none;
    }
  </style>
</head>

<body class="starter-page-page">

  <?php include 'assets/php/navbar.php' ?>


  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade">
      <div class="container position-relative">
        <h1>Meu Perfil</h1>
      </div>
    </div><!-- End Page Title -->

    <!-- Cadastro Section -->
    <section id="cadastro" class="cadastro section">

      <div class="container" data-aos="fade-up">
        <div class="overlay" id="overlay"></div>
        <div class="popup-editar-foto" id="popupEditarFoto">
          <button type="button" class="fechar-popup"
                onclick="fecharPopups()">×</button>
          <form action="assets/php/editarFotoPerfil.php" method="POST" enctype="multipart/form-data"
            class="popup-content">
            <input type="hidden" name="action" value="update_photo">
            <div class="popup-header">
              <h3>Editar Foto de Perfil</h3> 
            </div>
            <div class="popup-body">
              <div class="image-preview-container"> <img id="previewFotoPopup"
                  src="<?php echo htmlspecialchars($usuario['fotoPerfil']) ?>" alt="Pré-visualização da foto de perfil">
              </div>
              <div class="file-input-wrapper"> <label for="inputFotoPopup" class="file-input-label">Escolher
                  arquivo</label>
                <input type="file" id="inputFotoPopup" name="foto-perfil" accept="image/png, image/jpeg, image/webp"
                  style="display: none;"> <span id="fileNamePopup">Nenhum arquivo escolhido</span>
              </div>
            </div>
            <div class="popup-footer"> <button type="submit" class="btn-salvar">Salvar Foto</button> <button
                type="button" class="btn-cancelar" onclick="fecharPopups()">Cancelar</button> </div>
          </form>
        </div>
        <div class="usuario-container">
          <div class="profile-card">

            <div class="profile-picture-container">
              <img src="<?php echo htmlspecialchars($fotoPerfil) ?>" alt="Foto de Perfil" class="profile-picture">
              <button id="edit-photo-btn" title="Editar foto de perfil">
                <i class="bi bi-camera-fill"></i>
              </button>
            </div>

            <div class="profile-info">
              <h2 class="username"><?php echo htmlspecialchars($username) ?></h2>
              <p><?php echo htmlspecialchars($email) ?></p>
              <p class="community-info">Membro de <span class="community-name">Comunidade Alpha</span></p>
            </div>

            <a href="#" class="btn-edit-profile">
              <i class="bi bi-pencil-square"></i> Editar Perfil
            </a>

            <div class="aura-stats">
              <span class="aura-label">Aura Farmada</span>
              <div class="aura-value">
                <i class="bi bi-star-fill"></i>
                <span><?php echo htmlspecialchars($aura) ?></span>
              </div>
            </div>

          </div>
        </div>
      </div>
      <!-- formulario de cadastro -->

    </section><!-- /Starter Section Section -->

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
  <script src="assets/js/scripts.js"></script>

</body>

</html>