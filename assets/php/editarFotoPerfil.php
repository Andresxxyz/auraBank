<?php
    session_start();
    require ('conexao.php');

    $caminho_final_foto_fisico = null;

    $conn->begin_transaction();

    try {
        $sql= "SELECT fotoPerfil FROM usuario WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION["user_id"]);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 0) {
            throw new Exception("Usuário não encontrado.");
        }
        $usuario = $resultado->fetch_assoc();
        $caminho_foto_antiga = $usuario['fotoPerfil'];
        $stmt->close();

        $caminho_para_db = $caminho_foto_antiga;

        if (isset($_FILES["foto-perfil"]) && $_FILES["foto-perfil"]["error"] == UPLOAD_ERR_OK) {
            $target_dir = "../img/fotoPerfil/";
            $foto_perfil = $_FILES["foto-perfil"];
            $extensao_arquivo = strtolower(pathinfo($foto_perfil["name"], PATHINFO_EXTENSION));
            $tipos_permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($extensao_arquivo, $tipos_permitidos)) {
                throw new Exception("Tipo de arquivo não permitido.");
            }

            $novo_nome_arquivo = "user_" . $_SESSION["user_id"] . "_" . uniqid() . "." . $extensao_arquivo;
            $caminho_final_foto_fisico = $target_dir . $novo_nome_arquivo;

            if (!move_uploaded_file($foto_perfil["tmp_name"], $caminho_final_foto_fisico)) {
                throw new Exception("Erro ao mover o arquivo. Verifique as permissões da pasta 'fotoPerfil'.");
            }
            $caminho_para_db = "assets/img/fotoPerfil/" . $novo_nome_arquivo;
        }

        if ($caminho_para_db !== $caminho_foto_antiga) {
            $sql_update = "UPDATE usuario SET fotoPerfil = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            if (!$stmt_update) {
                throw new Exception("Erro ao preparar atualização: " . $conn->error);
            }
            $stmt_update->bind_param("ss", $caminho_para_db, $_SESSION["user_id"]);
            $stmt_update->execute();
            $stmt_update->close();
        }
        
        $conn->commit();
        header("Location: ../../meu_perfil.php?status=sucesso");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        
        if ($caminho_final_foto_fisico && file_exists($caminho_final_foto_fisico)) {
            unlink($caminho_final_foto_fisico);
        }
        die("Erro ao atualizar o perfil: " . $e->getMessage());
    }
?>