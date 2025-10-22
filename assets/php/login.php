<?php
    $tempo_de_vida = 60 * 60 * 24 * 30;
    session_set_cookie_params($tempo_de_vida, "/", ".aurabank-dcf3eabkf8cdg3be.canadacentral-01.azurewebsites.net", true, true);
    session_start();
    include ('conexao.php');

    $email = $_POST["exampleInputEmail1"];
    $senha = $_POST["exampleInputPassword1"];
    $sql = "SELECT id FROM usuario WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt -> execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows>0){
        $usuario = $resultado->fetch_assoc();
        $_SESSION['user_id'] = $usuario["id"];
        header("location: ../../comunidade.php");
    } else{
        echo "usuario nao encontrado";
    }

?>