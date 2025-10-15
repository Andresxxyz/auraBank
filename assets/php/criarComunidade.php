<?php
session_start();
require('conexao.php');

if (!isset($_SESSION['user_id'])) {
        header("Location: ../../login.php");

}

$nomeComunidade = $_POST['nomeComunidade'];
$idCriador = $_SESSION['user_id']; 

$caminhoFotoFinal = null;

if (isset($_FILES['fotoComunidade']) && $_FILES['fotoComunidade']['error'] === UPLOAD_ERR_OK) {
    
    $arquivo = $_FILES['fotoComunidade'];
    $diretorioUpload = '../img/fotoComunidade/';

    if (!is_dir($diretorioUpload)) {
        mkdir($diretorioUpload, 0777, true);
    }

    $nomeUnico = uniqid() . '_' . basename($arquivo['name']);
    $caminhoFotoNoServidor = $diretorioUpload . $nomeUnico;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoFotoNoServidor)) {
        $caminhoFotoFinal = 'img/fotoComunidade/' . $nomeUnico;
    } else {
        echo "Houve um erro ao fazer o upload da imagem.";
    }
}


$sql = "INSERT INTO comunidade (nomeComunidade, fotoComunidade, qtdMembros, dtCriacao, idCriador) VALUES (?, ?, 1, NOW(), ?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("ssi", $nomeComunidade, $caminhoFotoFinal, $idCriador);

if ($stmt->execute()) {
    $idNovaComunidade = $conn->insert_id;
    $sqlRelacionamento = "INSERT INTO comunidadeUsuario (idComunidade, idUsuario) VALUES (?, ?)";
    $stmtRel = $conn->prepare($sqlRelacionamento);
    $stmtRel->bind_param("ii", $idNovaComunidade, $idCriador);
    $stmtRel->execute();
    $stmtRel->close();

    header("Location: ../../searchComunidade.php");
    exit();

} else {
    echo "Erro ao criar a comunidade: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>