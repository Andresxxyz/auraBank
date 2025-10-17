<?php if (session_status() === PHP_SESSION_NONE) {
  session_start();

}

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
        <?php
        if ($comunidade) {
          echo ("<li><a href='comunidade.php'>Minha Comunidade</a></li>");
        }
        if ($logado) {
          echo ("<li><a href='meu_perfil.php'>Meu Perfil</a></li>");

        }

        ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>
    <?php
    if (!$logado) {
      echo ("<a class='cta-btn' href='login.php'>Entre</a>
          <a href='cadastro.php' class='cta-btn'>Cadastre-se</a>");
    } else {
      echo ("<a class='cta-btn' href='assets/php/logout.php'>Sair</a>");
    }
    ?>

  </div>
</header>