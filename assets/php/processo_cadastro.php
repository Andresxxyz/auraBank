<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Arquivo de log para debug
file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Início da execução\n", FILE_APPEND);

try {
    // Log antes de requerer o arquivo de conexão
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Tentando incluir conexao.php\n", FILE_APPEND);
    
    require ('conexao.php');
    
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Conexão incluída com sucesso\n", FILE_APPEND);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Método não é POST\n", FILE_APPEND);
        die('Método não permitido');
    }

    // Log dos dados recebidos
    $debug_data = "Dados recebidos:\n";
    foreach ($_POST as $key => $value) {
        $debug_data .= "$key: $value\n";
    }
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - " . $debug_data, FILE_APPEND);

    $username = $_POST["usernameInput"];
    $email = $_POST["exampleInputEmail1"];
    $senha = $_POST["exampleInputPassword1"];
    $confirmarSenha = $_POST["exampleInputConfirmPassword1"];

    // Verificar variáveis de ambiente
    $debug_env = "Variáveis de ambiente:\n";
    $debug_env .= "DB_HOST: " . (getenv('DB_HOST') ? 'definido' : 'não definido') . "\n";
    $debug_env .= "DB_USER: " . (getenv('DB_USER') ? 'definido' : 'não definido') . "\n";
    $debug_env .= "DB_NAME: " . (getenv('DB_NAME') ? 'definido' : 'não definido') . "\n";
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - " . $debug_env, FILE_APPEND);

    $sql = "INSERT INTO usuario (username, email, senha, aura, fotoPerfil) values (?, ?, ?, 0, '../img/fotoPerfil/semFoto.png')";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Erro no prepare: " . $conn->error . "\n", FILE_APPEND);
        die('Erro na preparação da query: ' . $conn->error);
    }

    $stmt->bind_param("sss", $username, $email, $senha);
    
    if($stmt->execute()){
        file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Cadastro realizado com sucesso\n", FILE_APPEND);
        header("location: ../../login.php");
        exit();
    } else {
        file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Erro no execute: " . $stmt->error . "\n", FILE_APPEND);
        die('Erro ao cadastrar usuário: ' . $stmt->error);
    }
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Exceção: " . $e->getMessage() . "\n", FILE_APPEND);
    die('Erro: ' . $e->getMessage());
}
?>