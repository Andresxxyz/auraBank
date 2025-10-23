<?php
session_start();
require __DIR__ . '/conexao.php';

// --- [NOVO] INCLUIR FIREBASE ---
// Isso assume que sua pasta 'vendor' está um nível acima de 'assets/php/'
// Ajuste o caminho se você rodou o composer em outro lugar.
require dirname(__DIR__, 2) . '/vendor/autoload.php'; 

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig; // Para iOS

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../searchComunidade.php');
    exit;
}

// --- SEU CÓDIGO EXISTENTE ---
$idRemetente = (int)$_SESSION['user_id'];
$idComunidade = isset($_POST['idComunidade']) ? (int)$_POST['idComunidade'] : 0;
$idDestinatario = isset($_POST['idDestinatario']) ? (int)$_POST['idDestinatario'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

$newReqId = 0;
$ok = false;

$sqlIns = 'INSERT INTO requisicaoAura (idComunidade, idRemetente, idDestinatario, quantidade, motivo) VALUES (?, ?, ?, ?, ?)';
if ($stmt = $conn->prepare($sqlIns)) {
    $stmt->bind_param('iiiis', $idComunidade, $idRemetente, $idDestinatario, $quantidade, $motivo);
    $ok = $stmt->execute();
    // [NOVO] Pegar o ID da votação que acabamos de criar
    $newReqId = $stmt->insert_id; 
    $stmt->close();
}

// --- [NOVO] SEÇÃO DE NOTIFICAÇÃO ---
// Se a votação foi criada com sucesso, envie as notificações
if ($ok && $newReqId > 0 && $idComunidade > 0) {

    try {
        // 1. Conectar ao Firebase
        // Coloque o caminho para o arquivo .json que você baixou
        $factory = (new Factory)->withServiceAccount(__DIR__ . '/firebase-admin.json');
        $messaging = $factory->createMessaging();

        // 2. Buscar os tokens de TODOS os usuários da comunidade
        //    (Menos o usuário que criou a votação)
        $sqlTokens = "SELECT u.token_push 
                      FROM usuario u 
                      JOIN comunidadeusuario cu ON u.id = cu.idUsuario
                      WHERE cu.idComunidade = ? AND u.id != ? AND u.token_push IS NOT NULL";
        
        $stmtTokens = $conn->prepare($sqlTokens);
        $stmtTokens->bind_param('ii', $idComunidade, $idRemetente);
        $stmtTokens->execute();
        $resultTokens = $stmtTokens->get_result();
        
        $tokens = [];
        while ($row = $resultTokens->fetch_assoc()) {
            $tokens[] = $row['token_push'];
        }
        $stmtTokens->close();

        if (!empty($tokens)) {
            // 3. Montar a mensagem
            $remetenteNome = $_SESSION['username'] ?? 'Alguém'; // Pega o nome da sessão
            $titulo = 'Nova Votação no AuraBank!';
            $corpo = "$remetenteNome iniciou uma votação: $motivo";

            $notification = Notification::create($titulo, $corpo);

            // 4. Configurar botões (deve ser IGUAL ao ID do JS)
            $androidConfig = AndroidConfig::create()
                ->withPriority('high')
                ->withNotification(\Kreait\Firebase\Messaging\Android\Notification::create()->withClickAction('VOTACAO_TRANSAO'));
            
            $apnsConfig = ApnsConfig::create()->withAps(['category' => 'VOTACAO_TRANSAO']);

            // 5. Montar payload final com dados extras
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(['votacaoId' => (string)$newReqId]) // Envia o ID da votação!
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            // 6. Enviar!
            $messaging->sendMulticast($message, $tokens);
        }

    } catch (\Exception $e) {
        // Se o Firebase falhar, não quebre a página, apenas registre o erro
        error_log($e->getMessage());
    }
}
// --- [FIM DA SEÇÃO] ---

// --- SEU CÓDIGO EXISTENTE ---
header('Location: ../../minha_comunidade.php?id=');
exit;
?>
