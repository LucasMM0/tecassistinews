<?php
/**
 * TecAssist News - Autenticação de Usuários e Início de Sessão
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

$erro = '';

if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado') {
    $erro = 'Acesso restrito: faça login com uma conta de administrador para acessar o painel.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Por favor, informe seu e-mail e sua senha.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            // Sessão autenticada
            $_SESSION['usuario'] = [
                'id' => (int) $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'nivel' => $usuario['nivel'],
            ];

            if ($usuario['nivel'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — TecAssist News</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="theme-default">
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="max-width: 420px; width: 100%; background: #18181b; border: 1px solid #27272a; border-radius: 16px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
            <div style="font-family: monospace; font-size: 11px; text-transform: uppercase; color: #f59e0b; margin-bottom: 8px;">
                Portal TecAssist News
            </div>
            <h1 style="font-family: serif; font-size: 24px; color: #fff; margin-bottom: 8px;">
                Identificação de Usuário
            </h1>
            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.5; margin-bottom: 24px;">
                Acesse sua conta para comentar em matérias ou gerenciar o CMS editorial.
            </p>

            <?php if ($erro): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">E-mail Cadastrado</label>
                    <input type="email" name="email" required placeholder="seu.email@exemplo.com" style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px;">Senha de Acesso</label>
                    <input type="password" name="senha" required placeholder="********" style="width: 100%; box-sizing: border-box; background: #09090b; border: 1px solid #3f3f46; color: #fff; padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                </div>
                <button type="submit" style="background: #f59e0b; color: #09090b; font-weight: 700; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 13px; margin-top: 8px;">
                    Entrar no Portal
                </button>
            </form>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #27272a; display: flex; justify-content: space-between; font-size: 12px;">
                <a href="cadastro.php" style="color: #f59e0b; text-decoration: none;">Cadastrar Leitor</a>
                <a href="criar-admin.php" style="color: #a1a1aa; text-decoration: none;">Criar Admin</a>
            </div>
            <div style="margin-top: 12px; text-align: center;">
                <a href="index.php" style="color: #71717a; font-size: 12px; text-decoration: none;">← Voltar à Página Inicial</a>
            </div>
        </div>
    </div>
</body>
</html>
