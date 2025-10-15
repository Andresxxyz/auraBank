<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Iniciando conexão\n", FILE_APPEND);

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME'); 
$port = getenv('DB_PORT') ?: 3306;

$conn = mysqli_init();

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
    $error = mysqli_connect_error();
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Erro na conexão: $error\n", FILE_APPEND);
    error_log('Conexão MySQL falhou: ' . $error);
    http_response_code(500);
    die('Erro ao conectar ao banco: ' . $error);
}

file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Conexão estabelecida com sucesso\n", FILE_APPEND);
?>