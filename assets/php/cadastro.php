<?php
    require ('conexao.php');

    $username = $_POST["usernameInput"];
    $email = $_POST["exampleInputEmail1"];
    $senha = $_POST["exampleInputPassword1"];
    $confirmarSenha = $_POST["exampleInputConfirmPassword1"];


    // ver se email ja cadastrado.
    //hashear a senha

    $sql = "INSERT INTO usuario (username, email, senha, aura, fotoPerfil) values (?, ?, ?, 0, '../img/fotoPerfil/semFoto.png')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $senha);
    if($stmt->execute()){
        header("location: ../../login.php");
    }



?>