<?php 
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">

    <a href="index.php" class="logo d-flex align-items-center me-auto">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <!-- <img src="assets/img/logo.png" alt=""> -->
      <img src="assets/img/aurabank_logo.png" alt="">
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#footer">Contato</a></li>

        <li><a href="searchComunidade.php">Comunidades</a></li>
        <li><a href="#footer">Contato</a></li>
        <?php
        if (isset($_SESSION["user_id"])) {
            require('assets/php/conexao.php');
            $sql_navbar = 'SELECT * FROM comunidadeusuario WHERE idUsuario=?';
            $stmt_navbar = $conn->prepare($sql_navbar);
            $stmt_navbar->bind_param(
              'i',
              $_SESSION['user_id']
            );
            $stmt_navbar->execute();
            $resultado_navbar = $stmt_navbar->get_result();
            if ($resultado_navbar->num_rows > 0) {
              echo ("<li><a href='minha_comunidade.php'>Minha Comunidade</a></li>");
            }
        } 
        if (isset($_SESSION["user_id"])) {
          echo ("<li><a href='meu_perfil.php'>Meu Perfil</a></li>");
        }
        ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>
    <?php
    if (!isset($_SESSION["user_id"])) {
      echo ("<a class='cta-btn' href='login.php'>Entre</a>
          <a href='cadastro.php' class='cta-btn'>Cadastre-se</a>");
    } else{
      echo("<a class='cta-btn' href='assets/php/logout.php'>Sair</a>");
    }
    ?>

  </div>
</header>