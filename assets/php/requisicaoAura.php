<?php
session_start();
require __DIR__ . '/conexao.php'; // Inclui a conexão com o BD

// --- INCLUIR FIREBASE ---
// Ajuste este caminho se sua pasta 'vendor' não estiver na raiz do projeto
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig; // Para iOS

// 1. VERIFICAÇÕES INICIAIS
// ---------------------------------

if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para o login
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se não for um POST, redireciona para a busca
    header('Location: ../../searchComunidade.php');
    exit;
}

// 2. PEGAR DADOS DO FORMULÁRIO
// ---------------------------------
$idRemetente = (int)$_SESSION['user_id'];
$idComunidade = isset($_POST['idComunidade']) ? (int)$_POST['idComunidade'] : 0;
$idDestinatario = isset($_POST['idDestinatario']) ? (int)$_POST['idDestinatario'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

$newReqId = 0; // ID da nova requisição (para enviar no push)
$ok = false;    // Flag para saber se o INSERT funcionou

// 3. SALVAR REQUISIÇÃO NO BANCO
// ---------------------------------

// Validação básica
if ($idComunidade <= 0 || $idDestinatario <= 0 || empty($motivo)) {
    // Falha - redireciona de volta
    header('Location: ../../minha_comunidade.php?erro=dados_invalidos');
    exit;
}

$sqlIns = 'INSERT INTO requisicaoAura (idComunidade, idRemetente, idDestinatario, quantidade, motivo) VALUES (?, ?, ?, ?, ?)';
if ($stmt = $conn->prepare($sqlIns)) {
    $stmt->bind_param('iiiis', $idComunidade, $idRemetente, $idDestinatario, $quantidade, $motivo);
    $ok = $stmt->execute();
    
    // Pega o ID da votação que acabamos de criar
    if ($ok) {
        $newReqId = $stmt->insert_id; 
    }
    $stmt->close();
}

// 4. ENVIAR NOTIFICAÇÕES PUSH (SE A REQUISIÇÃO FOI SALVA)
// ---------------------------------

if ($ok && $newReqId > 0 && $idComunidade > 0) {

    try {
        // 4.1. Conectar ao Firebase
        // [IMPORTANTE] Troque 'firebase-admin.json' pelo nome do seu arquivo de chave
        $factory = (new Factory)->withServiceAccount(__DIR__ . '/firebase-admin.json');
        $messaging = $factory->createMessaging();

        // 4.2. Buscar os tokens de TODOS os usuários da comunidade
        //      (Menos o usuário que criou a votação, pois ele não precisa ser notificado)
        $sqlTokens = "SELECT u.token_push 
                      FROM usuario u 
                      JOIN comunidadeusuario cu ON u.id = cu.idUsuario
                      WHERE cu.idComunidade = ? AND u.id != ? AND u.token_push IS NOT NULL AND u.token_push != ''";
        
        $stmtTokens = $conn->prepare($sqlTokens);
        $stmtTokens->bind_param('ii', $idComunidade, $idRemetente);
        $stmtTokens->execute();
        $resultTokens = $stmtTokens->get_result();
        
        $tokens = [];
        while ($row = $resultTokens->fetch_assoc()) {
            $tokens[] = $row['token_push'];
        }
        $stmtTokens->close();

        // Só tenta enviar se encontrou algum token
        if (!empty($tokens)) {
            
            // 4.3. Montar a mensagem
            $remetenteNome = $_SESSION['username'] ?? 'Alguém'; // Pega o nome da sessão
            $titulo = 'Nova Votação no AuraBank!';
            $corpo = "$remetenteNome iniciou uma votação: $motivo";

            $notification = Notification::create($titulo, $corpo);

            // 4.4. Configurar Android (com Canal) e iOS (com Categoria)
            
            // [CORRIGIDO] Configura o Android
            $androidConfig = AndroidConfig::create()
                ->withPriority('high')
                ->withNotification(\Kreait\Firebase\Messaging\Android\Notification::create()
                    ->withChannelId('votacoes_channel') // ID do canal criado no JS
                    ->withClickAction('VOTACAO_TRANSAO')); // ID da categoria de ação
            
            // Configura o iOS
            $apnsConfig = ApnsConfig::create()
                ->withAps(['category' => 'VOTACAO_TRANSAO']); // ID da categoria de ação

            // 4.5. Montar payload final com dados extras
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(['votacaoId' => (string)$newReqId]) // Envia o ID da votação!
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            // 4.6. Enviar a mensagem para todos os tokens encontrados
            $messaging->sendMulticast($message, $tokens);
        }

    } catch (\Exception $e) {
        // Se o Firebase falhar, não quebre a página, apenas registre o erro
        // Isso é importante para que o usuário não veja um erro, mesmo que o push falhe
        error_log("Erro ao enviar push do Firebase: " . $e->getMessage());
    }
}

// 5. REDIRECIONAR DE VOLTA
// ---------------------------------

// Se tudo deu certo (ou mesmo se o push falhou), redireciona o usuário
header('Location: ../../minha_comunidade.php');
exit;

?>