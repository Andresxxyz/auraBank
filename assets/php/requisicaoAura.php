<?php
session_start();
require __DIR__ . '/conexao.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../../searchComunidade.php');
  exit;
}

$idRemetente = (int)$_SESSION['user_id'];
$idComunidade = isset($_POST['idComunidade']) ? (int)$_POST['idComunidade'] : 0;
$idDestinatario = isset($_POST['idDestinatario']) ? (int)$_POST['idDestinatario'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';




$sqlIns = 'INSERT INTO requisicaoAura (idComunidade, idRemetente, idDestinatario, quantidade, motivo) VALUES (?, ?, ?, ?, ?)';
if ($stmt = $conn->prepare($sqlIns)) {
  $stmt->bind_param('iiiis', $idComunidade, $idRemetente, $idDestinatario, $quantidade, $motivo);
  $ok = $stmt->execute();
  $stmt->close();
}

header('Location: ../../minha_comunidade.php?id=');
exit;
