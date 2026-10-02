<?php
/**
 * TecAssist News - Conexão Flexível com Diagnóstico Automático para XAMPP
 * Testa automaticamente 'localhost', '127.0.0.1' e porta 3306/3307
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

$db_name = getenv('DB_NAME') ?: 'tecassist_news';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

// Tentativas de conexão padrão do XAMPP no Windows/Mac
$tentativas = [
    ['host' => '127.0.0.1', 'port' => '3306'],
    ['host' => 'localhost', 'port' => '3306'],
    ['host' => '127.0.0.1', 'port' => '3307'], // Porta alternativa se o MySQL padrão mudar
    ['host' => 'localhost', 'port' => '3307'],
];

$pdo = null;
$ultimoErro = '';

foreach ($tentativas as $t) {
    try {
        $dsn = "mysql:host={$t['host']};port={$t['port']};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        break; // Conexão estabelecida com sucesso
    } catch (PDOException $e) {
        $ultimoErro = $e->getMessage();
    }
}

if (!$pdo) {
    // Diagnóstico visual amigável com a causa exata
    echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Diagnóstico de Conexão — TecAssist News</title>";
    echo "<style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0c0f14; color: #f1f5f9; padding: 40px 20px; line-height: 1.6; }
        .card { max-width: 600px; margin: 0 auto; background: #141820; border: 1px solid #273142; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h2 { color: #f59e0b; margin-top: 0; font-size: 22px; }
        .code-box { background: #090d14; border: 1px solid #1e2530; color: #f87171; padding: 12px 16px; border-radius: 8px; font-family: monospace; font-size: 13px; margin: 16px 0; word-break: break-all; }
        ol { padding-left: 20px; color: #cbd5e1; font-size: 14px; }
        li { margin-bottom: 12px; }
        .btn { display: inline-block; background: #f59e0b; color: #000; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; margin-top: 10px; }
    </style></head><body>";
    
    echo "<div class='card'>";
    echo "<h2>Diagnóstico de Conexão MySQL (XAMPP)</h2>";
    echo "<p>O PHP não conseguiu se conectar ao banco de dados MySQL com a configuração padrão.</p>";
    
    echo "<div class='code-box'><strong>Mensagem do MySQL:</strong><br>" . htmlspecialchars($ultimoErro) . "</div>";
    
    echo "<h3>Como resolver:</h3>";
    echo "<ol>";
    if (strpos($ultimoErro, 'Unknown database') !== false) {
        echo "<li><strong>O banco não foi selecionado:</strong> O MySQL está rodando, mas o banco chamado <code>tecassist_news</code> ainda não existe. Abra o <a href='http://localhost/phpmyadmin/' target='_blank' style='color:#38bdf8;'>phpMyAdmin</a>, clique em <strong>SQL</strong> e execute o script <code>database.sql</code>.</li>";
    } elseif (strpos($ultimoErro, 'Access denied') !== false) {
        echo "<li><strong>Senha do MySQL:</strong> Seu XAMPP tem uma senha configurada no usuário <code>root</code>. Abra o arquivo <code>config.php</code> e preencha a variável <code>\$db_pass = 'sua_senha';</code>.</li>";
    } else {
        echo "<li><strong>Verifique o MySQL no XAMPP:</strong> Certifique-se de que o botão <strong>MySQL</strong> no painel de controle do XAMPP está com status verde (Started).</li>";
        echo "<li><strong>Porta diferente:</strong> Se o MySQL estiver na porta 3307 ou outra, verifique no painel do XAMPP a porta listada ao lado do MySQL.</li>";
    }
    echo "</ol>";
    
    echo "<p style='margin-top: 24px;'><a href='index.php' class='btn'>Tentar Novamente (F5)</a></p>";
    echo "</div></body></html>";
    exit;
}

function usuarioLogado(): ?array {
    return $_SESSION['usuario'] ?? null;
}

function verificarAcessoAdmin(): void {
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'admin') {
        header("Location: ../login.php?erro=acesso_negado");
        exit;
    }
}
