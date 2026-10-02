<?php
/**
 * Script de Diagnóstico e Teste Direto de Conexão MySQL para XAMPP
 * Acesse no navegador: http://localhost/tecassist/teste-conexao.php
 */

declare(strict_types=1);

echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Teste de Conexão MySQL</title>";
echo "<style>
    body { font-family: monospace; background: #0c0f14; color: #f1f5f9; padding: 30px; font-size: 14px; line-height: 1.6; }
    .box { max-width: 700px; margin: 0 auto; background: #141820; border: 1px solid #273142; border-radius: 12px; padding: 24px; }
    .ok { color: #34d399; font-weight: bold; }
    .fail { color: #f87171; font-weight: bold; }
    .info { color: #38bdf8; }
    h2 { color: #f59e0b; margin-top: 0; }
    hr { border: 0; border-top: 1px solid #273142; margin: 16px 0; }
</style></head><body><div class='box'>";

echo "<h2>Relatório de Diagnóstico do XAMPP</h2>";
echo "<div>Versão do PHP: <span class='info'>" . PHP_VERSION . "</span></div>";
echo "<div>Extensão PDO MySQL: " . (extension_loaded('pdo_mysql') ? "<span class='ok'>Instalada e Ativa</span>" : "<span class='fail'>NÃO INSTALADA</span>") . "</div>";
echo "<hr>";

$configuracoes = [
    ['host' => '127.0.0.1', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'port' => 3306, 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'port' => 3307, 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'port' => 3307, 'user' => 'root', 'pass' => ''],
];

echo "<h3>1. Testando conexão com o Servidor MySQL:</h3>";

$conexaoSucesso = null;

foreach ($configuracoes as $idx => $cfg) {
    echo "Teste #" . ($idx + 1) . " (Host: {$cfg['host']}, Porta: {$cfg['port']}): ";
    try {
        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};charset=utf8mb4";
        $testePdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 2,
        ]);
        echo "<span class='ok'>CONECTOU COM SUCESSO!</span><br>";
        $conexaoSucesso = $cfg;
        break;
    } catch (PDOException $e) {
        echo "<span class='fail'>FALHOU</span> &rarr; " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}

echo "<hr>";

if ($conexaoSucesso) {
    echo "<h3>2. Verificando o Banco de Dados 'tecassist_news':</h3>";
    try {
        $dsn = "mysql:host={$conexaoSucesso['host']};port={$conexaoSucesso['port']};dbname=tecassist_news;charset=utf8mb4";
        $bancoPdo = new PDO($dsn, $conexaoSucesso['user'], $conexaoSucesso['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        echo "<span class='ok'>O banco 'tecassist_news' EXISTE e está pronto!</span><br>";

        // Contar matérias
        $stmt = $bancoPdo->query("SELECT COUNT(*) FROM materias");
        $total = $stmt->fetchColumn();
        echo "<div>Total de matérias encontradas na tabela: <span class='ok'>{$total}</span></div>";

        echo "<hr><div style='margin-top: 15px;'>";
        echo "<a href='index.php' style='background:#f59e0b; color:#000; padding:10px 16px; text-decoration:none; border-radius:6px; font-weight:bold;'>Ir para a Página Inicial &rarr;</a>";
        echo "</div>";

    } catch (PDOException $e) {
        echo "<span class='fail'>O banco de dados 'tecassist_news' NÃO FOI ENCONTRADO.</span><br>";
        echo "Mensagem: " . htmlspecialchars($e->getMessage()) . "<br><br>";
        echo "<span class='info'>Solução:</span> No phpMyAdmin, certifique-se de criar o banco com o nome exato <strong>tecassist_news</strong> e importe o arquivo <code>database.sql</code>.";
    }
} else {
    echo "<h3 class='fail'>O MySQL não está respondendo em nenhuma porta padrão.</h3>";
    echo "<p>Possíveis causas:</p>";
    echo "<ol>";
    echo "<li>No painel do XAMPP, o <strong>MySQL</strong> não está com status verde (Started). Clique no botão <strong>Start</strong> ao lado de MySQL.</li>";
    echo "<li>Se o MySQL tiver uma senha (diferente de vazia), edite o arquivo <code>config.php</code> e preencha a senha.</li>";
    echo "</ol>";
}

echo "</div></body></html>";
