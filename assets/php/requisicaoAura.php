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

// Basic validation
if ($idComunidade <= 0 || $idDestinatario <= 0 || $quantidade <= 0 || $motivo === '') {
  header('Location: ../../comunidade.php?id=' . $idComunidade . '&req=erro');
  exit;
}

// Optional: ensure both users are in this community
$sqlCheck = 'SELECT COUNT(*) FROM comunidadeUsuario WHERE idComunidade = ? AND idUsuario IN (?, ?)';
if ($stmtC = $conn->prepare($sqlCheck)) {
  $stmtC->bind_param('iii', $idComunidade, $idRemetente, $idDestinatario);
  $stmtC->execute();
  $stmtC->bind_result($countIn);
  $stmtC->fetch();
  $stmtC->close();
  if ($countIn < 2) {
    header('Location: ../../comunidade.php?id=' . $idComunidade . '&req=erro');
    exit;
  }
}

// Persist a request record (create table if needed):
// CREATE TABLE IF NOT EXISTS requisicaoAura (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   idComunidade INT NOT NULL,
//   idRemetente INT NOT NULL,
//   idDestinatario INT NOT NULL,
//   quantidade INT NOT NULL,
//   motivo TEXT NOT NULL,
//   status VARCHAR(20) NOT NULL DEFAULT 'pendente',
//   dtCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );

$ok = false;
$sqlIns = 'INSERT INTO requisicaoAura (idComunidade, idRemetente, idDestinatario, quantidade, motivo) VALUES (?, ?, ?, ?, ?)';
if ($stmt = $conn->prepare($sqlIns)) {
  $stmt->bind_param('iiiis', $idComunidade, $idRemetente, $idDestinatario, $quantidade, $motivo);
  $ok = $stmt->execute();
  $stmt->close();
}

header('Location: ../../comunidade.php?id=' . $idComunidade . ($ok ? '&req=ok' : '&req=erro'));
exit;
