<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregar .env no ambiente local
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    $env_path = __DIR__ . '/../../.env';
    if (file_exists($env_path)) {
        $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
                putenv(trim($key) . "=" . trim($value));
            }
        }
    }
}

$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'];
$user = getenv('DB_USER') ?: $_ENV['DB_USER'];
$pass = getenv('DB_PASS') ?: $_ENV['DB_PASS'];
$db = getenv('DB_NAME') ?: $_ENV['DB_NAME'];
$port = getenv('DB_PORT') ?: 3306;

$conn = mysqli_init();

// Desabilitar SSL explicitamente
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
    $error = mysqli_connect_error();
    error_log('Conexão MySQL falhou: ' . $error);
    http_response_code(500);
    die('Erro ao conectar ao banco: ' . $error);
}

?>