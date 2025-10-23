<?php
session_start();
require __DIR__ . '/assets/php/conexao.php';

// Ensure user is logged in (for showing members/request feature)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
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
    $idComunidade = $resultadoInfo->fetch_assoc()['idComunidade'] ?? 0;
    if ($idComunidade > 0) {
        $stmtCom->bind_param('i', $idComunidade);
        $stmtCom->execute();
        $result = $stmtCom->get_result();
        if ($result && $result->num_rows === 1) {
            $comunidade = $result->fetch_assoc();
        }
        $stmtCom->close();
    }
}

if ($comunidade) {
    $sqlCriador = 'SELECT username FROM usuario WHERE id = ?';
    if ($stmtCriador = $conn->prepare($sqlCriador)) {
        $stmtCriador->bind_param('i', $comunidade['idCriador']);
        $stmtCriador->execute();
        $result = $stmtCriador->get_result();
        if ($result && $result->num_rows === 1) {
            $comunidade['criador'] = $result->fetch_assoc()['username'];
        }
        $stmtCriador->close();
    }
}


function carregarMembros($conn, $idComunidade)
{
    if (!$idComunidade)
        return [];
    $sql = 'SELECT u.id, u.username, u.fotoPerfil, u.aura 
            FROM usuario u
            JOIN comunidadeusuario cu ON u.id = cu.idUsuario
            WHERE cu.idComunidade = ?
            ORDER BY u.aura DESC, u.username ASC';
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $idComunidade);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
        $stmt->close();
        return $members;
    }
    return [];
}


function carregarRequisicoes($conn, $idComunidade)
{
    if (!$idComunidade)
        return [];
    // Alias r.id as idRequisicao so templates can always access the request id as 'idRequisicao'
    $sql = 'SELECT r.*, r.id AS idRequisicao, 
             sr.username AS remetenteNome, 
             sd.username AS destinatarioNome
       FROM requisicaoAura r
       JOIN usuario sr ON r.idRemetente = sr.id
       JOIN usuario sd ON r.idDestinatario = sd.id
       WHERE r.idComunidade = ?
       ORDER BY r.dtCriacao DESC
       LIMIT 10';
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $idComunidade);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        $stmt->close();
        return $requests;
    }
    return [];
}

$requisicoes = carregarRequisicoes($conn, $comunidade['idComunidade'] ?? 0);
$members = carregarMembros($conn, $comunidade['idComunidade'] ?? 0);

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
        .main-content-card {
            background-color: #2c2f33;
            color: #f0f0f0;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #4f545c;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .main-content-card h4 {
            color: #fff;
        }

        .div-form-aceitar-negar {
            display: flex;
            flex-direction: row;

        }

        @media (max-width: 768px) {

            #service-details {
                padding: 0;
            }

            .container {
                padding: 0;
            }

            .container,
            .main-content-card {
                width: 100%;
                border-radius: 0;
                border: none;
            }

            .div-form-aceitar-negar {

                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }



        .btn-login,
        .btn-cancelar {
            border: 1px solid var(--accent-color);
            color: color-mix(in srgb, var(--accent-color), transparent 20%);
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 10px 30px;
            border-radius: 4px;


            border: none;
            transition: 0.3s !important;
        }

        .btn-login:hover,
        .btn-cancelar:hover {
            background: color-mix(in srgb, var(--accent-color), transparent 20%);
            color: #fff;
        }


        .community-info-card,
        .requests-card {
            background-color: transparent;
            border: none;
            padding-left: calc(var(--bs-gutter-x) * .5);
            box-shadow: none;
        }


        .table-dark {
            --bs-table-bg: transparent;

            --bs-table-border-color: #4f545c;
        }


        .community-info-card .list-group-item {
            background: transparent;
            color: #f0f0f0;
            border-color: #4f545c !important;

        }

        /* Card de requisições */
        .requests-card .list-group-item {
            background: transparent;
            color: #f0f0f0;
            border-color: #4f545c;
        }

        .requests-card .request-meta {
            color: #b7bcc4;
            font-size: 0.9rem;
        }

        .btn-get-started,
        .btn-salvar {
            background: var(--accent-color);
            color: var(--contrast-color);
            font-weight: 400;
            font-size: 14px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            transition: 0.3s;
            text-transform: uppercase;
            font-weight: BOLD;
            border: none;
            transition: 0.3s !important;
        }

        .btn-get-started:hover,
        .btn-salvar:hover {
            background: color-mix(in srgb, var(--accent-color), transparent 20%);
        }

        .pill {
            border-radius: 20px;
            padding: 5px 5px;
            color: white;
            margin: 0px 0px 5px 0px;
        }



        .requests-card .list-group-item {}

        .transaction-info {
            flex-grow: 1;
            padding-right: 15px;
        }


        .transaction-actions {
            text-align: left;
            min-width: 0;
            flex-shrink: 1;
            margin-top: 15px;


            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
        }


        .transaction-actions .badge {
            margin-bottom: 0;
            display: inline-block;
        }


        .transaction-actions .div-form-aceitar-negar {
            flex-direction: row;
            justify-content: flex-start;
            gap: 8px;
            margin-top: 0;
        }


        .vote-info-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .vote-info-box {
            border: 1px solid #4f545c;
            border-radius: 5px;
            padding: 8px 12px;
            text-align: center;
            min-width: 110px;
            background-color: transparent;
        }


        .vote-number {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--accent-color);
            line-height: 1.2;
        }


        .vote-label {
            font-size: 0.8rem;
            color: #b7bcc4;
        }


        .transaction-footer {
            font-size: 0.9rem;
            color: #b7bcc4;
            margin-top: 10px;
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
                <div class="main-content-card" data-aos="fade-up">
                    <div class="row gy-5">
                        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <h4>Dados da Comunidade</h4>
                            <div class="community-info-card h-100">
                                <?php if (!empty($comunidade['fotoComunidade'])): ?>
                                    <img src="<?php echo htmlspecialchars($comunidade['fotoComunidade']); ?>"
                                        class="img-fluid rounded mb-3" alt="Banner da Comunidade">
                                <?php else: ?>
                                    <div class="bg-secondary rounded mb-3 d-flex align-items-center justify-content-center"
                                        style="height: 180px; color:#fff; background-color: #4f545c !important;">
                                        Sem imagem
                                    </div>
                                <?php endif; ?>

                                <ul class="list-group list-group-flush bg-transparent mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Nome <span
                                            class="fw-semibold text-end"><?php echo htmlspecialchars($comunidade['nomeComunidade']); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Criador <span
                                            class="fw-semibold text-end"><?php echo htmlspecialchars($comunidade['criador'] ?? '—'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Membros <span
                                            class="badge bg-primary rounded-pill"><?php echo (int) ($comunidade['qtdMembros'] ?? 0); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Criada em <span
                                            class="fw-semibold text-end"><?php echo htmlspecialchars(date('d/m/Y', strtotime($comunidade['data_criacao']))); ?></span>
                                    </li>
                                </ul>
                                <a href="searchComunidade.php" class="btn btn-login w-100 mt-auto">Voltar</a>
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
                                        <?php if (empty($members)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Nenhum membro encontrado.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($members as $key => $m): ?>
                                                <?php
                                                $fotoPerfil = !empty($m['fotoPerfil']) ? $m['fotoPerfil'] : 'assets/img/fotoPerfil/semFoto.png';
                                                ?>
                                                <tr>
                                                    <th scope="row" class="fs-5 fw-bold"
                                                        style="color: var(--accent-color); text-shadow: 0 4px 15px var(--accent-color);">
                                                        <?php echo $key + 1; ?>
                                                    </th>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto"
                                                                class="rounded-circle"
                                                                style="width:40px; height:40px; object-fit:cover;">
                                                            <span class="fw-semibold"
                                                                style="color: var(--accent-color ); text-shadow: 0 4px 15px var(--accent-color);"><?php echo htmlspecialchars($m['username']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="d-flex align-items-center">
                                                            <i class="bi bi-star-fill text-warning me-2"></i>
                                                            <span class="fw-bold fs-5"
                                                                style="color: var(--accent-color); text-shadow: 0 4px 15px var(--accent-color);"><?php echo (int) $m['aura']; ?></span>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ((int) $m['id'] !== (int) $_SESSION['user_id']): ?>
                                                            <button type="button" class="btn-get-started" data-bs-toggle="modal"
                                                                data-bs-target="#reqModal"
                                                                data-user-id="<?php echo (int) $m['id']; ?>"
                                                                data-user-name="<?php echo htmlspecialchars($m['username']); ?>">
                                                                Dar/Tirar Aura
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <hr class="my-4 opacity-25">
                        <h4 class="mb-3 mt-0">Transações Recentes</h4>
                        <div class="requests-card mt-1">
                            <ul class="list-group list-group-flush">
                                <?php if (empty($requisicoes)): ?>
                                    <li class="list-group-item text-center py-4"
                                        style="background:transparent; border-color: #4f545c;">
                                        <span class="text-secondary">Nenhuma transação recente.</span>
                                    </li>
                                <?php else: ?>
                                    <?php foreach ($requisicoes as $req): ?>

                                        <li class="list-group-item py-3">
                                            <div class="transaction-info">

                                                <div class="fw-semibold">
                                                    <?php echo htmlspecialchars($req['remetenteNome'] ?? 'Usuário'); ?>
                                                    →
                                                    <?php echo htmlspecialchars($req['destinatarioNome'] ?? 'Usuário'); ?>
                                                </div>

                                                <div class="request-meta mt-1">
                                                    <div><b>Aura:</b> <?php echo (int) ($req['quantidade'] ?? 0); ?></div>
                                                    <div><b>Motivo:</b> <?php echo htmlspecialchars($req['motivo'] ?? '...'); ?>
                                                    </div>
                                                </div>

                                                <div class="vote-info-container">

                                                    <div class="vote-info-box">
                                                        <div class="vote-number">
                                                            <?php
                                                            // Contar votos a favor
                                                            $sqlVotos = "SELECT COUNT(*) AS votosFavor FROM requisicaousuario WHERE idRequisicao = ? AND votou = 1";
                                                            $stmtVotos = $conn->prepare($sqlVotos);
                                                            $reqIdForCount = isset($req['idRequisicao']) ? (int) $req['idRequisicao'] : (int) ($req['id'] ?? 0);
                                                            $stmtVotos->bind_param("i", $reqIdForCount);
                                                            $stmtVotos->execute();
                                                            $resultadoVotos = $stmtVotos->get_result();
                                                            $votos = $resultadoVotos ? $resultadoVotos->fetch_assoc() : null;
                                                            echo (int) ($votos['votosFavor'] ?? 0);
                                                            ?>
                                                        </div>
                                                        <div class="vote-label">Votos a favor</div>
                                                    </div>

                                                    <div class="vote-info-box">
                                                        <div class="vote-number">
                                                            <?php
                                                            // Recuperar qtdMembros da comunidade para calcular votos necessários
                                                            $qtdMembros = 0;
                                                            $sqlQtd = "SELECT qtdMembros FROM comunidade WHERE idComunidade = ?";
                                                            if ($stmtQtd = $conn->prepare($sqlQtd)) {
                                                                $comId = (int) ($comunidade['idComunidade'] ?? 0);
                                                                $stmtQtd->bind_param("i", $comId);
                                                                $stmtQtd->execute();
                                                                $resQtd = $stmtQtd->get_result();
                                                                $qtdMembros = (int) ($resQtd->fetch_assoc()['qtdMembros'] ?? 0);
                                                                $stmtQtd->close();
                                                            }
                                                            $votosNecessarios = $qtdMembros > 0 ? (int) ceil($qtdMembros * 0.5) : 0;
                                                            ?>
                                                            <?php echo $votosNecessarios; ?>
                                                        </div>
                                                        <div class="vote-label">Votos necessários</div>
                                                    </div>
                                                </div>

                                                <div class="transaction-actions">
                                                    <?php if ($req['status'] === 'Aprovada'): ?>
                                                        <span class="badge bg-success">Aprovada</span>
                                                    <?php elseif ($req['status'] === 'Negada'): ?>
                                                        <span class="badge bg-danger">Negada</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Pendente</span>
                                                    <?php endif; ?>

                                                    <?php
                                                    $sqlVotou = "SELECT votou FROM requisicaousuario WHERE idRequisicao = ? AND idUsuario = ? LIMIT 1";
                                                    $stmtVotou = $conn->prepare($sqlVotou);
                                                    $userId = (int) ($_SESSION['user_id'] ?? 0);
                                                    $reqId = isset($req['idRequisicao']) ? (int) $req['idRequisicao'] : (int) ($req['id'] ?? 0);
                                                    $stmtVotou->bind_param("ii", $reqId, $userId);
                                                    $stmtVotou->execute();
                                                    $resVotou = $stmtVotou->get_result();
                                                    $rowVotou = $resVotou ? $resVotou->fetch_assoc() : null;
                                                    $votou = isset($rowVotou['votou']) ? (int) $rowVotou['votou'] : null;

                                                    if (strtolower($req['status']) === 'pendente' && $votou === null) {
                                                        ?>
                                                        <div class="div-form-aceitar-negar">
                                                            <form action="assets/php/aprovar.php" method="post" style="margin: 0;">
                                                                <input type="hidden" name="idRequisicao"
                                                                    value="<?php echo (int) ($req['idRequisicao'] ?? 0); ?>">
                                                                <input type="hidden" name="idDestinatario"
                                                                    value="<?php echo (int) ($req['idDestinatario'] ?? 0); ?>">
                                                                <input type="hidden" name="idComunidade"
                                                                    value="<?php echo (int) ($comunidade['idComunidade'] ?? 0); ?>">
                                                                <button class="btn btn-success btn-sm">Aprovar</button>
                                                            </form>
                                                            <form action="assets/php/negar.php" method="post" style="margin: 0;">
                                                                <input type="hidden" name="idRequisicao"
                                                                    value="<?php echo (int) ($req['idRequisicao'] ?? 0); ?>">
                                                                <input type="hidden" name="idComunidade"
                                                                    value="<?php echo (int) ($comunidade['idComunidade'] ?? 0); ?>">
                                                                <button class="btn btn-outline-danger btn-sm">Negar</button>
                                                            </form>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="transaction-footer d-flex justify-content-between">
                                                    <span>
                                                        <?php
                                                        echo "Você ";
                                                        if ($votou === 1) {
                                                            echo "aprovou.";
                                                        } elseif ($votou === 0) {
                                                            echo "negou.";
                                                        } elseif ($votou === null) {
                                                            echo "não votou.";
                                                        }
                                                        ?>
                                                    </span>
                                                    <span>
                                                        <b>Data:</b>
                                                        <?php echo htmlspecialchars(date('d/m/Y', strtotime($req['dtCriacao']))); ?>
                                                    </span>
                                                </div>
                                            </div>

                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
        </section>
    </main>

    <?php require __DIR__ . '/assets/php/rodape.php'; ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="reqModalLabel">Faça uma requisição de Aura</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="post" action="assets/php/requisicaoAura.php">
                    <div class="modal-body">
                        <input type="hidden" name="idComunidade"
                            value="<?php echo (int) ($comunidade['idComunidade'] ?? 0); ?>">
                        <input type="hidden" id="idDestinatario" name="idDestinatario" value="">
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" id="nomeDestinatario" class="form-control" value="" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantidade de Aura</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" required>
                        </div>
                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class=" btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class=" btn-login">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-req-aura, .btn-get-started, [data-bs-target="#reqModal"]');
            if (!btn) return;
            const uid = btn.getAttribute('data-user-id') || '';
            const uname = btn.getAttribute('data-user-name') || '';
            const idInput = document.getElementById('idDestinatario');
            const nameInput = document.getElementById('nomeDestinatario');
            if (idInput) idInput.value = uid;
            if (nameInput) nameInput.value = uname;
        });
    </script>
    <script>
        // Só executa se estivermos dentro do app Capacitor
        if (typeof capacitor !== 'undefined' && capacitor.isNativePlatform()) {

            const { PushNotifications } = capacitor.Plugins;

            // Função principal para iniciar tudo
            async function iniciarNotificacoes() {
                console.log('Iniciando sistema de notificações...');

                // 1. Pedir permissão ao usuário
                let permStatus = await PushNotifications.checkPermissions();
                if (permStatus.receive === 'prompt') {
                    permStatus = await PushNotifications.requestPermissions();
                }
                if (permStatus.receive !== 'granted') {
                    console.error('Permissão de notificação negada!');
                    return;
                }

                // 2. Registrar os botões de ação (Aprovar/Negar)
                await PushNotifications.registerActionTypes({
                    types: [
                        {
                            id: 'VOTACAO_TRANSAO', // ID do tipo de notificação
                            actions: [
                                { id: 'aprovar_voto', title: 'Aprovar' },
                                { id: 'negar_voto', title: 'Negar', destructive: true }
                            ]
                        }
                    ]
                });

                // 3. Registrar o dispositivo no Firebase
                await PushNotifications.register();

                // 4. Ouvinte: Pegar o token e salvar no BD
                PushNotifications.addListener('registration', (token) => {
                    console.log('Token do dispositivo:', token.value);

                    // Envia o token para seu novo script PHP
                    fetch('assets/php/salvar_token_push.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: token.value })
                    })
                        .then(res => res.json())
                        .then(data => console.log('Token salvo:', data))
                        .catch(err => console.error('Erro ao salvar token:', err));
                });

                // 5. Ouvinte: Lida com a resposta do usuário
                PushNotifications.addListener('pushNotificationActionPerformed', (action) => {
                    console.log('Ação da notificação:', action);
                    const acaoID = action.actionId;
                    const votacaoId = action.notification.data.votacaoId;

                    if (!votacaoId) return;

                    // Define a ação (aprovar ou negar)
                    let voto = (acaoID === 'aprovar_voto') ? 'aprovar' : 'negar';

                    // Envia o voto para um script PHP
                    // (Você precisará criar este script 'registrar_voto.php')
                    // fetch(`assets/php/registrar_voto.php?id=${votacaoId}&voto=${voto}`);

                    // Por enquanto, vamos apenas redirecionar para a comunidade
                    window.location.href = 'minha_comunidade.php';
                });
            }

            iniciarNotificacoes();
        }
    </script>
</body>

</html>