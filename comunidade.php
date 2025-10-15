<?php
session_start();
require __DIR__ . '/assets/php/conexao.php';

// Ensure user is logged in (for showing members/request feature)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
} else {
}
// carregar a comunidade
$sqlInfo = 'SELECT idComunidade FROM comunidadeusuario WHERE idUsuario=?';
  $stmt = $conn->prepare($sqlInfo);
  $stmt->bind_param(
    'i',
    $_SESSION['user_id']
  );
$stmt->execute();
$resultadoInfo = $stmt->get_result();

$comunidade = null;
$sqlCom = 'SELECT * FROM comunidade WHERE idComunidade = ? ';
if ($stmtCom = $conn->prepare($sqlCom)) {

    $stmtCom->bind_param('i', $resultadoInfo->fetch_assoc()['idComunidade']);

    $stmtCom->execute();
    
    $result = $stmtCom->get_result();
    if ($result && $result->num_rows === 1) {
        $comunidade = $result->fetch_assoc();
    }
    $stmtCom->close();
}






// Load members
$members = [];






?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Minha Comunidade</title>
    <meta name="description" content="Visualize os dados da comunidade e seus membros.">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        /* Card de Informações da Comunidade */
        .community-info-card {
            background-color: #2c2f33;
            color: #f0f0f0;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #4f545c;
        }
        /* Ajustes na tabela para o tema escuro */
        .table-dark {
            --bs-table-bg: #2c2f33;
            --bs-table-border-color: #4f545c;
        }
    </style>
</head>

<body class="service-details-page">

    <?php include 'assets/php/navbar.php'; ?>

    <main class="main">

        <div class="page-title dark-background" data-aos="fade">
            <div class="container position-relative">
                <h1>Minha Comunidade</h1>
                <p class="mb-0"><?php echo htmlspecialchars($comunidade['nomeComunidade'] ?? 'Comunidade'); ?></p>
            </div>
        </div>

        <section id="service-details" class="service-details section">
            <div class="container">
                <div class="row gy-4">

                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                      <h4>Dados da Comunidade</h4>
                        <div class="community-info-card h-100">
                            <?php if ($comunidade['fotoComunidade']) : ?>
                                <img src="<?php echo htmlspecialchars($comunidade['fotoComunidade']); ?>" class="img-fluid rounded mb-3" alt="Banner da Comunidade">
                            <?php else : ?>
                                <div class="bg-secondary rounded mb-3 d-flex align-items-center justify-content-center" style="height: 180px; color:#fff;">
                                    Sem imagem
                                </div>
                            <?php endif; ?>

                            <ul class="list-group list-group-flush bg-transparent mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-secondary">
                                    Nome <span class="fw-semibold text-end"><?php echo htmlspecialchars($comunidade['nomeComunidade']); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-secondary">
                                    Criador <span class="fw-semibold text-end"><?php echo htmlspecialchars($criadorNome ?? '—'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-secondary">
                                    Membros <span class="badge bg-primary rounded-pill"><?php echo (int) $comunidade['qtdMembros']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-white border-secondary">
                                    Criada em <span class="fw-semibold text-end"><?php echo htmlspecialchars(date('d/m/Y', strtotime($comunidade['data_criacao']))); ?></span>
                                </li>
                            </ul>
                            <a href="searchComunidade.php" class="btn btn-outline-light w-100 mt-auto">Voltar</a>
                        </div>
                    </div>

                    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                        <h4 class="mb-3">Ranking de Membros</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-dark table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 50px;">#</th>
                                        <th scope="col">Usuário</th>
                                        <th scope="col" style="width: 120px;">Aura</th>
                                        <th scope="col" class="text-center" style="width: 180px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($members) === 0) : ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Nenhum membro encontrado.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($members as $key => $m) : ?>
                                            <?php
                                            $fotoPerfil = !empty($m['fotoPerfil']) ? $m['fotoPerfil'] : 'assets/img/fotoPerfil/semFoto.png';
                                            if (strpos($fotoPerfil, 'assets/') !== 0) {
                                                $fotoPerfil = 'assets/' . ltrim($fotoPerfil, '/');
                                            }
                                            ?>
                                            <tr>
                                                <th scope="row" class="fs-5 fw-bold"><?php echo $key + 1; ?></th>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                                                        <span class="fw-semibold"><?php echo htmlspecialchars($m['username']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="d-flex align-items-center">
                                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                                        <span class="fw-bold fs-5"><?php echo (int) $m['aura']; ?></span>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary btn-sm btn-req-aura" data-bs-toggle="modal" data-bs-target="#reqModal" data-user-id="<?php echo (int) $m['id']; ?>" data-user-name="<?php echo htmlspecialchars($m['username']); ?>">
                                                        Fazer Requisição
                                                    </button>
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

    <div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="reqModalLabel">Solicitar Aura</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="assets/php/requisicaoAura.php">
                    <div class="modal-body">
                        <input type="hidden" name="idComunidade" value="<?php echo (int) $idComunidade; ?>">
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
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('click', function(e) {
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