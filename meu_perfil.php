<?php
include('assets/php/conexao.php');
session_start();
if (!isset($_SESSION['user_id'])) {
  header("location: login.php");
}
$sql = "SELECT username, email, aura, fotoPerfil, cu.idComunidade, c.nomeComunidade FROM usuario u LEFT JOIN comunidadeusuario cu ON u.id = cu.idUsuario LEFT JOIN comunidade c ON cu.idComunidade = c.idComunidade WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$username = $usuario["username"];
$email = $usuario["email"];
$aura = $usuario["aura"];
$fotoPerfil = $usuario["fotoPerfil"];
$comunidade = $usuario["nomeComunidade"]


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
    body{
      overflow-x: hidden;
      scrollbar-width: none; 
      scroll-padding: 0;
      
    }
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

    @media (max-width: 575px) {

            .cadastro{
                padding: 0;
            }

            .container{
                padding: 0;
            }
            .profile-card{
                border-radius: 0;
                border: none;
            }
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

    .community-name {
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

    /* popup editar perfil (novo) */
    .popup-editar-perfil {
      display: none;
      position: fixed;
      z-index: 1002;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background-color: #0b0f15;
      border-radius: 8px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
      width: 38%;
      max-width: 720px;
      align-items: center;
      justify-content: center;
      padding: 20px;
      color: #fff;
    }

    .popup-editar-perfil .popup-content {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .popup-editar-perfil label {
      font-size: 0.9rem;
      color: #cfd8dc;
    }

    .popup-editar-perfil input[type="text"],
    .popup-editar-perfil input[type="email"],
    .popup-editar-perfil input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #39424a;
      background: #0f1418;
      color: #fff;
    }

    .popup-editar-perfil .form-row {
      display: flex;
      gap: 12px;
    }

    .popup-editar-perfil .form-row .col {
      flex: 1;
    }

    .popup-editar-perfil .image-preview-small {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      overflow: hidden;
      border: 1px solid #333;
      background: #111;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .popup-editar-perfil .image-preview-small img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .popup-editar-perfil .popup-footer {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 6px;
    }

    .popup-editar-perfil .btn-salvar,
    .popup-editar-perfil .btn-cancelar {
      padding: 10px 16px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }

    .popup-editar-perfil .btn-salvar {
      background: var(--accent-color);
      color: #fff;
    }

    .popup-editar-perfil .btn-cancelar {
      background: #26292d;
      color: #ddd;
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

    @media (max-width: 768px) {
      .popup-editar-foto {
        width: 90%;
      }

      .popup-editar-perfil {
        width: 80%;
      }
      .popup-editar-perfil .btn-salvar, .popup-editar-perfil .btn-cancelar {
        width: 80%;
        height: 80%;
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
        <p>Vizualize e edite o seu perfil.</p>
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
            <div class="popup-footer"> <button type="submit" class="btn-salvar">Salvar</button> <button
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
              <p class="community-info"><?php if($comunidade){
                echo "Membro de <span class'community-name' style='font-weight: 600; color: #fff;'>" . htmlspecialchars($comunidade) . "</span>";
              } else{
                echo "Sem Comunidade";
              }  ?></p>
            </div>

            <!-- alterado: transformar link em botão com id para abrir popup editar perfil -->
            <button id="edit-profile-btn" class="btn-edit-profile" type="button">
              <i class="bi bi-pencil-square"></i> Editar Perfil
            </button>

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

      <!-- popup editar perfil (novo) -->
      <div class="popup-editar-perfil" id="popupEditarPerfil" aria-hidden="true">
        <button type="button" class="fechar-popup"
                style="position:absolute;right:8px;top:4px;font-size:28px;background:none;border:none;color:#888"
                onclick="fecharPopups()">×</button>
        <form class="popup-content" id="formEditarPerfil" action="assets/php/editarPerfil.php" method="POST" enctype="multipart/form-data">
          
          <input type="hidden" name="action" value="update_profile">
          <div style="display:flex;gap:12px;align-items:center;">
            
            <div style="flex:1;">
              <label for="usernameInput">Nome de usuário</label>
              <input id="usernameInput" name="username" type="text" value="<?php echo htmlspecialchars($username) ?>" >
              <label for="emailInput" style="margin-top:8px;">Email</label>
              <input id="emailInput" name="email" type="email" value="<?php echo htmlspecialchars($email) ?>" >
            </div>
          </div>

          <div class="form-row">
            <div class="col">
              <label for="senhaInput">Nova senha (opcional)</label>
              <input id="senhaInput" name="password" type="password" placeholder="Deixe em branco para manter">
            </div>
          </div>

          <div class="popup-footer">
            <div class="popup-footer">
              <button type="submit" class="btn-salvar">Salvar</button>
              <button type="button" class="btn-cancelar" onclick="fecharPopups()">Cancelar</button>
            </div>
          </div>
        </form>
      </div>

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

  <script>
    // Controle de popups e preview de arquivos
    (function(){
      const overlay = document.getElementById('overlay');
      const popupFoto = document.getElementById('popupEditarFoto');
      const popupPerfil = document.getElementById('popupEditarPerfil');
      const btnEditPhoto = document.getElementById('edit-photo-btn');
      const btnEditProfile = document.getElementById('edit-profile-btn');

      // inputs de preview/nome
      const inputFotoPopup = document.getElementById('inputFotoPopup');
      const previewFotoPopup = document.getElementById('previewFotoPopup');
      const fileNamePopup = document.getElementById('fileNamePopup');

      const inputFotoPerfil = document.getElementById('inputFotoPerfil');
      const previewFotoPerfil = document.getElementById('previewFotoPerfil');
      const fileNamePerfil = document.getElementById('fileNamePerfil');

      function showOverlay() { overlay.style.display = 'block'; }
      function hideOverlay() { overlay.style.display = 'none'; }

      // abre popup editar foto
      if(btnEditPhoto){
        btnEditPhoto.addEventListener('click', function(){
          popupFoto.style.display = 'flex';
          showOverlay();
        });
      }

      // abre popup editar perfil
      if(btnEditProfile){
        btnEditProfile.addEventListener('click', function(){
          popupPerfil.style.display = 'flex';
          showOverlay();
        });
      }

      // fechar todos os popups
      window.fecharPopups = function(){
        if(popupFoto) popupFoto.style.display = 'none';
        if(popupPerfil) popupPerfil.style.display = 'none';
        hideOverlay();
      };

      // preview e nome do arquivo (popup foto)
      if(inputFotoPopup){
        inputFotoPopup.addEventListener('change', function(e){
          const file = e.target.files[0];
          if(file){
            fileNamePopup.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(ev){ previewFotoPopup.src = ev.target.result; };
            reader.readAsDataURL(file);
          } else {
            fileNamePopup.textContent = 'Nenhum arquivo escolhido';
            previewFotoPopup.src = "<?php echo htmlspecialchars($usuario['fotoPerfil']) ?>";
          }
        });
      }

      // preview e nome do arquivo (popup perfil)
      if(inputFotoPerfil){
        inputFotoPerfil.addEventListener('change', function(e){
          const file = e.target.files[0];
          if(file){
            fileNamePerfil.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(ev){ previewFotoPerfil.src = ev.target.result; };
            reader.readAsDataURL(file);
          } else {
            fileNamePerfil.textContent = 'Nenhum arquivo escolhido';
            previewFotoPerfil.src = "<?php echo htmlspecialchars($fotoPerfil) ?>";
          }
        });
      }

      // validação simples antes de enviar (exemplo)
      const formEditarPerfil = document.getElementById('formEditarPerfil');
      if(formEditarPerfil){
        formEditarPerfil.addEventListener('submit', function(e){
          const username = document.getElementById('usernameInput').value.trim();
          if(!username){
            e.preventDefault();
            alert('O nome de usuário não pode ficar vazio.');
            return false;
          }
          // outras validações podem ser adicionadas aqui
        });
      }

      // fechar popup clicando no overlay
      if(overlay){
        overlay.addEventListener('click', fecharPopups);
      }
    })();
  </script>

</body>

</html>