<?php
include('conexao.php');
session_start();
if (!isset($_SESSION['user_id'])) {
  header("location: login.php");
}

$userId = $_SESSION["user_id"];
$nomeNovo = $_POST['username'];
$emailNovo = $_POST['email'];
$senhaNova = $_POST['password'];


if($senhaNova == ""){

    $sql = "UPDATE usuario SET username = ?, email = ? WHERE id = ?";
    
}else{
    $sql = "UPDATE usuario SET username = ?, email = ?, senha = ? WHERE id = ?";
    
}
$stmt = $conn->prepare($sql);
if($senhaNova == ""){
    $stmt->bind_param("ssi", $nomeNovo, $emailNovo, $userId);
}else{
    $stmt->bind_param("sssi", $nomeNovo, $emailNovo, $senhaNova, $userId);
}
$stmt->execute();
$stmt->close();
$conn->close();
header("location: ../../meu_perfil.php");
?>