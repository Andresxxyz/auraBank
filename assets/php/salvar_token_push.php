<?php
session_start();
require __DIR__ . '/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não logado']);
    exit;
}

// Pega o JSON enviado pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? null;

if (empty($token)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Token vazio']);
    exit;
}

$idUsuario = (int)$_SESSION['user_id'];

// Atualiza o token do usuário no banco
$sql = "UPDATE usuario SET token_push = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $token, $idUsuario);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Falha ao salvar no BD']);
}
?>