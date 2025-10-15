<?php
session_start();
require __DIR__ . '/assets/php/conexao.php';

// Ensure user is logged in (for showing members/request feature)
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Resolve community id from GET or fallback to user's first community
$idComunidade = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$idComunidade && isset($_SESSION['user_id'])) {
  $sqlFirst = 'SELECT idComunidade FROM comunidadeUsuario WHERE idUsuario = ? LIMIT 1';
  if ($stmtFirst = $conn->prepare($sqlFirst)) {
    $stmtFirst->bind_param('i', $_SESSION['user_id']);
    $stmtFirst->execute();
    $stmtFirst->bind_result($cid);
    if ($stmtFirst->fetch()) {
      $idComunidade = (int)$cid;
    }
    $stmtFirst->close();
  }
}


// Load community details
$comunidade = null;
$sqlCom = 'SELECT id, nomeComunidade, fotoComunidade, qtdMembros, dtCriacao, idCriador FROM comunidade WHERE id = ? LIMIT 1';
if ($stmtCom = $conn->prepare($sqlCom)) {
  $stmtCom->bind_param('i', $idComunidade);
  $stmtCom->execute();
  $result = $stmtCom->get_result();
  if ($result && $result->num_rows === 1) {
    $comunidade = $result->fetch_assoc();
  }
  $stmtCom->close();
}



// Try to get creator username (best effort)
$criadorNome = null;
$sqlCreator = 'SELECT username FROM usuario WHERE id = ? LIMIT 1';
if ($stmtCr = $conn->prepare($sqlCreator)) {
  $stmtCr->bind_param('i', $comunidade['idCriador']);
  $stmtCr->execute();
  $stmtCr->bind_result($uname);
  if ($stmtCr->fetch()) {
    $criadorNome = $uname;
  }
  $stmtCr->close();
}

// Load members
$members = [];
$sqlMem = 'SELECT u.id, u.username, u.aura, u.fotoPerfil FROM usuario u INNER JOIN comunidadeUsuario cu ON cu.idUsuario = u.id WHERE cu.idComunidade = ? ORDER BY u.username ASC';
if ($stmtMem = $conn->prepare($sqlMem)) {
  $stmtMem->bind_param('i', $idComunidade);
  $stmtMem->execute();
  $resMem = $stmtMem->get_result();
  while ($row = $resMem->fetch_assoc()) {
    $members[] = $row;
  }
  $stmtMem->close();
}


if (!empty($fotoCom)) {
  // Stored relative to assets/, so prefix it
  $fotoComUrl = 'assets/' . ltrim($fotoCom, '/');
}

$reqStatus = isset($_GET['req']) ? $_GET['req'] : null; // feedback from request handler
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Minha Comunidade</title>
  <meta name="description" content="Visualize os dados da comunidade e seus membros.">
  <meta name="keywords" content="comunidade, membros, aura">

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="service-details-page">

  <?php include 'assets/php/navbar.php'; ?>

  <main class="main">

    <div class="page-title dark-background" data-aos="fade">
      <div class="container position-relative">
        <h1>Minha Comunidade</h1>
        <p class="mb-0"><?php echo htmlspecialchars($comunidade['nomeComunidade']); ?></p>
      </div>
    </div>

    <section id="service-details" class="service-details section">
      <div class="container">
        <?php if ($reqStatus === 'ok'): ?>
          <div class="alert alert-success">Solicitação enviada com sucesso.</div>
        <?php elseif ($reqStatus === 'erro'): ?>
          <div class="alert alert-danger">Não foi possível enviar a solicitação.</div>
        <?php endif; ?>
        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <h4>Dados da Comunidade</h4>
            <?php if ($fotoComUrl): ?>
              <img src="<?php echo htmlspecialchars($fotoComUrl); ?>" class="img-fluid rounded mb-3" alt="Banner da Comunidade">
            <?php else: ?>
              <div class="bg-secondary rounded mb-3" style="height: 180px; display:flex; align-items:center; justify-content:center; color:#fff;">
                Sem imagem cadastrada
              </div>
            <?php endif; ?>

            <ul class="list-group mb-3">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Nome
                <span class="fw-semibold"><?php echo htmlspecialchars($comunidade['nomeComunidade']); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Criador
                <span class="fw-semibold"><?php echo htmlspecialchars($criadorNome ?? '—'); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Membros
                <span class="badge bg-primary rounded-pill"><?php echo (int)$comunidade['qtdMembros']; ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Criada em
                <span class="fw-semibold"><?php echo htmlspecialchars(date('d/m/Y', strtotime($comunidade['dtCriacao']))); ?></span>
              </li>
            </ul>

            <a href="searchComunidade.php" class="btn btn-outline-light w-100">Voltar para comunidades</a>
          </div>

          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h4 class="mb-0">Membros</h4>
            </div>

            <div class="table-responsive">
              <table class="table table-dark table-striped align-middle">
                <thead>
                  <tr>
                    <th scope="col">Usuário</th>
                    <th scope="col" style="width:120px;">Aura</th>
                    <th scope="col" style="width:180px;">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($members) === 0): ?>
                    <tr>
                      <td colspan="3" class="text-center">Nenhum membro encontrado.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($members as $m): ?>
                      <?php
                        $fotoPerfil = !empty($m['fotoPerfil']) ? $m['fotoPerfil'] : 'assets/img/fotoPerfil/semFoto.png';
                        // If value doesn't start with assets, prefix
                        if (strpos($fotoPerfil, 'assets/') !== 0) {
                          $fotoPerfil = 'assets/' . ltrim($fotoPerfil, '/');
                        }
                      ?>
                      <tr>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto" class="rounded-circle" style="width:36px; height:36px; object-fit:cover;">
                            <span class="fw-semibold"><?php echo htmlspecialchars($m['username']); ?></span>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-info text-dark"><?php echo (int)$m['aura']; ?></span>
                        </td>
                        <td>
                          <?php if (isset($_SESSION['user_id'])): ?>
                            <button type="button" class="btn btn-sm btn-primary btn-req-aura" data-bs-toggle="modal" data-bs-target="#reqModal" data-user-id="<?php echo (int)$m['id']; ?>" data-user-name="<?php echo htmlspecialchars($m['username']); ?>">
                              Fazer uma requisição
                            </button>
                          <?php else: ?>
                            <a class="btn btn-sm btn-outline-light" href="login.php">Entrar para requisitar</a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <?php require __DIR__ . '/assets/php/rodape.php'; ?>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- Modal: Requisição de Aura -->
  <div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reqModalLabel">Solicitar Aura</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="assets/php/requisicaoAura.php">
          <div class="modal-body">
            <input type="hidden" name="idComunidade" value="<?php echo (int)$idComunidade; ?>">
            <input type="hidden" id="idDestinatario" name="idDestinatario" value="">
            <div class="mb-3">
              <label class="form-label">Usuário</label>
              <input type="text" id="nomeDestinatario" class="form-control" value="" disabled>
            </div>
            <div class="mb-3">
              <label for="quantidade" class="form-label">Quantidade de Aura</label>
              <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" required>
            </div>
            <div class="mb-3">
              <label for="motivo" class="form-label">Motivo</label>
              <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Fill modal with selected user
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-req-aura');
      if (!btn) return;
      const uid = btn.getAttribute('data-user-id');
      const uname = btn.getAttribute('data-user-name');
      document.getElementById('idDestinatario').value = uid;
      document.getElementById('nomeDestinatario').value = uname;
    });
  </script>
</body>
</html>
