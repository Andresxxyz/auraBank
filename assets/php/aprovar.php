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
header('Location: ' . $_SERVER['HTTP_REFERER']);
?>