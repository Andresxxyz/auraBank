<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require ('conexao.php');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die('Método não permitido');
    }

    $username = $_POST["usernameInput"];
    $email = $_POST["exampleInputEmail1"];
    $senha = $_POST["exampleInputPassword1"];
    $confirmarSenha = $_POST["exampleInputConfirmPassword1"];

    // Log para debug
    error_log("Tentativa de cadastro - Username: $username, Email: $email");

    $sql = "INSERT INTO usuario (username, email, senha, aura, fotoPerfil) values (?, ?, ?, 0, '../img/fotoPerfil/semFoto.png')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $senha);
    
    if($stmt->execute()){
        error_log("Cadastro realizado com sucesso para o usuário: $username");
        header("location: ../../login.php");
        exit();
    } else {
        error_log("Erro no cadastro: " . $stmt->error);
        die('Erro ao cadastrar usuário: ' . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Erro no processo de cadastro: " . $e->getMessage());
    die('Erro: ' . $e->getMessage());
}
?>