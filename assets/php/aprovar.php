<?php
session_start();
include ('conexao.php');

// Ensure user is logged in (for showing members/request feature)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$idRequisicao = $_POST['idRequisicao'];


$sql = "INSERT INTO requisicaousuario (idUsuario, idRequisicao, votou) VALUES (?, ?, 1)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $idRequisicao);
$stmt->execute();

$stmt->close();

    $total = 0;
    $sqlStatusReq = "SELECT count(votou) FROM requisicaousuario WHERE idRequisicao = ? AND votou = 1";
    if ($stmt = $conn->prepare($sqlStatusReq)) {
        
        $total = 0;
        $stmt->bind_param("i", $idRequisicao);
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();
        
    }

    $idComunidade = $_POST["idComunidade"] ?? 0;
    if ($idComunidade > 0) {
        $sqlMembros = "SELECT qtdMembros FROM comunidade WHERE idComunidade = ?";
        if ($stmt = $conn->prepare($sqlMembros)) {
            $qtdMembros = 0;
            
            $stmt->bind_param("i", $idComunidade);
            $stmt->execute();
            $stmt->bind_result($qtdMembros);
            $stmt->fetch();
            $stmt->close();

            if ($total >= $qtdMembros * 0.5) {
                $sqlAprovar = "UPDATE requisicaoaura SET status = 'Aprovada' WHERE id = ?";
                if ($stmt = $conn->prepare($sqlAprovar)) {
                    
                    $stmt->bind_param("i", $idRequisicao);
                    $stmt->execute();
                    $stmt->close();
                }

                $idDestinatario = $_POST['idDestinatario'] ?? 0;
                $sqlMudar = "UPDATE usuario SET aura = aura + (SELECT quantidade FROM requisicaoaura WHERE id = ?) WHERE id = ?";
                if ($stmt = $conn->prepare($sqlMudar)) {
                    $stmt->bind_param("ii", $idRequisicao, $idDestinatario);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }


header('Location: ../../comunidade.php');
?>