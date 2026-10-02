<?php
/**
 * TecAssist News - Script Seguro de Inicialização do Administrador Raiz
 * Gera hash criptográfico nativo via password_hash() com custo de processamento seguro.
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

$mensagem = '';
$sucesso = false;

// Verifica se já existem administradores
$stmtCheck = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE nivel = 'admin'");
$totalAdmins = (int) $stmtCheck->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (!$nome || !$email || strlen($senha) < 8) {
        $mensagem = "Por favor, preencha todos os campos. A senha deve ter ao menos 8 caracteres.";
    } elseif ($senha !== $confirmarSenha) {
        $mensagem = "A confirmação da senha não confere.";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, nivel) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$nome, $email, $senhaHash]);
            $sucesso = true;
            $mensagem = "Administrador cadastrado com sucesso no banco MySQL! Por recomendação de segurança, apague ou renomeie este arquivo (criar-admin.php).";
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar: este endereço de e-mail já está em uso.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicialização do Administrador — TecAssist News</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="theme-default">
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="max-width: 480px; width: 100%; background: #18181b; border: 1px solid #27272a; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
            <div style="font-family: monospace; font-size: 11px; text-transform: uppercase; color: #f59e0b; margin-bottom: 8px;">
                Script de Inicialização Segura (criar-admin.php)
            </div>
            <h1 style="font-family: serif; font-size: 24px; color: #fff; margin-bottom: 8px;">
                Criar Administrador Raiz
            </h1>
            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.5; margin-bottom: 24px;">
                Gera o primeiro usuário com nível privilegiado para gerenciar matérias e estatísticas no CMS.
            </p>

            <?php if ($totalAdmins > 0 && !$sucesso): ?>
                <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px;">
                    <strong>Aviso:</strong> Já existe(m) <?php echo $totalAdmins; ?> administrador(es) cadastrado(s) no sistema.
                </div>
            <?php endif; ?>

            <?php if ($mensagem): ?>
                <div style="background: <?php echo $sucesso ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; border: 1px solid <?php echo $sucesso ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)'; ?>; color: <?php echo $sucesso ? '#34d399' : '#f87171'; ?>; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if (!$sucesso): ?>
            <form method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">Nome Completo</label>
                    <input type="text" name="nome" value="Editor Responsável" required style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">E-mail do Administrador</label>
                    <input type="email" name="email" value="admin@tecassist.org" required style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">Senha (mínimo 8 dígitos)</label>
                    <input type="password" name="senha" required minlength="8" placeholder="********" style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" required minlength="8" placeholder="********" style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <button type="submit" style="background: #f59e0b; color: #09090b; font-weight: 700; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 13px; margin-top: 8px;">
                    Criar Conta de Administrador
                </button>
            </form>
            <?php else: ?>
                <a href="login.php" style="display: block; text-align: center; background: #f59e0b; color: #09090b; font-weight: 700; text-decoration: none; padding: 12px; border-radius: 8px; font-size: 13px;">
                    Ir para Tela de Login →
                </a>
            <?php endif; ?>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #27272a; text-align: center;">
                <a href="index.php" style="color: #71717a; font-size: 12px; text-decoration: none;">← Voltar à Página Inicial</a>
            </div>
        </div>
    </div>
</body>
</html>
