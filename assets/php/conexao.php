<?php
$host = getenv('DB_HOST') 
$user = getenv('DB_USER') 
$pass = getenv('DB_PASS') 
$db   = getenv('DB_NAME')  
$port = getenv('DB_PORT') ?: 3306;


$certPath = __DIR__ . '/certs/BaltimoreCyberTrustRoot.crt.pem';

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, $certPath, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL)) {
    error_log('Conexão MySQL falhou: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Erro ao conectar ao banco.');
}
?>