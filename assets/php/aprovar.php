<?php
session_start();
include ('conexao.php');

// Ensure user is logged in (for showing members/request feature)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

$idRequisicao = isset($_POST['idRequisicao']) ? (int) $_POST['idRequisicao'] : 0;
if ($idRequisicao <= 0) {
    header('Location: ../../minha_comunidade.php');
    exit;
}

// Prevent duplicate votes from the same user
$checkSql = "SELECT id FROM requisicaousuario WHERE idRequisicao = ? AND idUsuario = ? LIMIT 1";
if ($stmt = $conn->prepare($checkSql)) {
    $stmt->bind_param('ii', $idRequisicao, $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        // already voted, redirect back
        $stmt->close();
        header('Location: ../../minha_comunidade.php');
        exit;
    }
    $stmt->close();
}

$sql = "INSERT INTO requisicaousuario (idUsuario, idRequisicao, votou) VALUES (?, ?, 1)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $userId, $idRequisicao);
    $stmt->execute();
    $stmt->close();
}

$total = 0;
$sqlStatusReq = "SELECT COUNT(*) as total FROM requisicaousuario WHERE idRequisicao = ? AND votou = 1";
if ($stmt = $conn->prepare($sqlStatusReq)) {
    $stmt->bind_param("i", $idRequisicao);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && ($row = $res->fetch_assoc())) {
        $total = (int) $row['total'];
    }
    $stmt->close();
}

$idComunidade = isset($_POST["idComunidade"]) ? (int) $_POST["idComunidade"] : 0;
if ($idComunidade > 0) {
    $sqlMembros = "SELECT qtdMembros FROM comunidade WHERE idComunidade = ?";
    if ($stmt = $conn->prepare($sqlMembros)) {
        $stmt->bind_param("i", $idComunidade);
        $stmt->execute();
        $res = $stmt->get_result();
        $qtdMembros = 0;
        if ($res && ($row = $res->fetch_assoc())) {
            $qtdMembros = (int) $row['qtdMembros'];
        }
        $stmt->close();

        // Approve when >= 50% of members voted yes (ceil to require at least half)
        if ($qtdMembros > 0 && $total >= ceil($qtdMembros * 0.5)) {
            $sqlAprovar = "UPDATE requisicaoaura SET status = 'Aprovada' WHERE id = ?";
            if ($stmt = $conn->prepare($sqlAprovar)) {
                $stmt->bind_param("i", $idRequisicao);
                $stmt->execute();
                $stmt->close();
            }

            $idDestinatario = isset($_POST['idDestinatario']) ? (int) $_POST['idDestinatario'] : 0;
            if ($idDestinatario > 0) {
                // Safely update the user's aura using the requisition quantity
                $sqlMudar = "UPDATE usuario u
                              JOIN requisicaoaura r ON r.id = ?
                              SET u.aura = u.aura + r.quantidade
                              WHERE u.id = ?";
                if ($stmt = $conn->prepare($sqlMudar)) {
                    $stmt->bind_param("ii", $idRequisicao, $idDestinatario);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}

header('Location: ../../minha_comunidade.php');
exit;
?>