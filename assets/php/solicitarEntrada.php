<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['user_id'])) {
    die("Erro: Você precisa estar logado para entrar em uma comunidade.");
}
if (!isset($_POST['idComunidade']) || !is_numeric($_POST['idComunidade'])) {
    die("Erro: ID da comunidade inválido.");
}

$idComunidade = (int)$_POST["idComunidade"];
$idUsuario = (int)$_SESSION['user_id'];


$conn->begin_transaction();

try {
    $sql_update = "UPDATE comunidade SET qtdMembros = qtdMembros + 1 WHERE idComunidade = ?";
    $stmt_update = $conn->prepare($sql_update);
    if (!$stmt_update) {
        throw new Exception("Falha no prepare (update): " . $conn->error);
    }
    $stmt_update->bind_param("i", $idComunidade);
    if (!$stmt_update->execute()) {
        throw new Exception("Falha no execute (update): " . $stmt_update->error);
    }
    $stmt_update->close();

    $sql_add = "INSERT INTO comunidadeusuario (idComunidade, idUsuario) VALUES (?, ?)";
    $stmt_add = $conn->prepare($sql_add);
    if (!$stmt_add) {
        throw new Exception("Falha no prepare (insert): " . $conn->error);
    }
    $stmt_add->bind_param("ii", $idComunidade, $idUsuario);
    if (!$stmt_add->execute()) {
        throw new Exception("Falha no execute (insert): " . $stmt_add->error);
    }
    $stmt_add->close();

    $conn->commit();

    header('Location: ../../comunidade.php?idComunidade=' . $idComunidade);
    exit; 

} catch (Exception $e) {

    $conn->rollback();
    echo "Ocorreu um erro ao tentar entrar na comunidade: " . $e->getMessage();

}

$conn->close();
?>