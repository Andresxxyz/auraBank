<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME'); 
$port = getenv('DB_PORT') ?: 3306;

$conn = mysqli_init();

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
    error_log('Conexão MySQL falhou: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Erro ao conectar ao banco.');
}
?>