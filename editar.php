<?php
/**
 * TecAssist News - Edição de Matéria
 */

declare(strict_types=1);
require_once __DIR__ . '/../config.php';
verificarAcessoAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM materias WHERE id = ?");
$stmt->execute([$id]);
$materia = $stmt->fetch();

if (!$materia) {
    die("Matéria não encontrada.");
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id ASC")->fetchAll();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoriaId = (int) $_POST['categoria_id'];
    $titulo = trim($_POST['titulo'] ?? '');
    $subtitulo = trim($_POST['subtitulo'] ?? '');
    $autorNome = trim($_POST['autor_nome'] ?? '');
    $autorCargo = trim($_POST['autor_cargo'] ?? '');
    $imagemUrl = trim($_POST['imagem_url'] ?? '');
    $imagemAlt = trim($_POST['imagem_alt'] ?? '');
    $imagemLegenda = trim($_POST['imagem_legenda'] ?? '');
    $conteudoCompleto = trim($_POST['conteudo_completo'] ?? '');
    $conteudoSimplificado = trim($_POST['conteudo_simplificado'] ?? '');
    $tempoLeitura = (int) ($_POST['tempo_leitura'] ?? 5);

    if (!$titulo || !$conteudoCompleto) {
        $erro = 'Título e conteúdo completo são obrigatórios.';
    } else {
        $resumo = substr(strip_tags($conteudoCompleto), 0, 180) . '...';

        $stmtUp = $pdo->prepare("
            UPDATE materias SET 
                categoria_id = ?, titulo = ?, subtitulo = ?, autor_nome = ?, autor_cargo = ?, 
                imagem_url = ?, imagem_alt = ?, imagem_legenda = ?, resumo = ?, 
                conteudo_completo = ?, conteudo_simplificado = ?, tempo_leitura = ?
            WHERE id = ?
        ");
        $stmtUp->execute([
            $categoriaId, $titulo, $subtitulo, $autorNome, $autorCargo,
            $imagemUrl, $imagemAlt, $imagemLegenda, $resumo,
            $conteudoCompleto, $conteudoSimplificado, $tempoLeitura, $id
        ]);

        header("Location: index.php?msg=atualizado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Matéria — TecAssist News</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12px; color: #d4d4d8; margin-bottom: 6px; font-weight: 600; }
        .form-input, .form-textarea, .form-select {
            width: 100%; box-sizing: border-box; background: #090d14; border: 1px solid #273142;
            color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="brand-title">TecAssist News — CMS</a>
            <a href="index.php" style="font-size: 12px; color: #94a3b8;">← Voltar à Lista</a>
        </div>
    </header>

    <main class="container" style="max-width: 800px; padding: 40px 20px;">
        <h1 style="font-family: var(--font-serif); font-size: 32px; color: #fff; margin-bottom: 8px;">
            Editar Matéria #<?php echo $materia['id']; ?>
        </h1>

        <?php if ($erro): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Título da Notícia</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($materia['titulo']); ?>" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Subtítulo / Linha Fina</label>
                <input type="text" name="subtitulo" value="<?php echo htmlspecialchars($materia['subtitulo']); ?>" class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Estação / Editoria</label>
                    <select name="categoria_id" class="form-select">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $materia['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tempo de Leitura (minutos)</label>
                    <input type="number" name="tempo_leitura" value="<?php echo $materia['tempo_leitura']; ?>" min="1" class="form-input">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nome do Autor</label>
                    <input type="text" name="autor_nome" value="<?php echo htmlspecialchars($materia['autor_nome']); ?>" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo ou Especialidade</label>
                    <input type="text" name="autor_cargo" value="<?php echo htmlspecialchars($materia['autor_cargo']); ?>" class="form-input">
                </div>
            </div>

            <div style="background: #141820; border: 1px solid var(--surface-border); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <div style="font-size: 11px; font-family: monospace; color: #f59e0b; text-transform: uppercase; margin-bottom: 12px;">
                    Acessibilidade da Imagem (WCAG 2.1 AAA)
                </div>
                <div class="form-group">
                    <label class="form-label">URL da Imagem de Capa</label>
                    <input type="text" name="imagem_url" value="<?php echo htmlspecialchars($materia['imagem_url']); ?>" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Texto Alternativo Minucioso (alt)</label>
                    <textarea name="imagem_alt" rows="2" class="form-textarea"><?php echo htmlspecialchars($materia['imagem_alt']); ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Legenda da Foto</label>
                    <input type="text" name="imagem_legenda" value="<?php echo htmlspecialchars($materia['imagem_legenda']); ?>" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Conteúdo Completo</label>
                <textarea name="conteudo_completo" rows="8" required class="form-textarea"><?php echo htmlspecialchars($materia['conteudo_completo']); ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Versão em Linguagem Simples (Plain Language)</label>
                <textarea name="conteudo_simplificado" rows="4" class="form-textarea"><?php echo htmlspecialchars($materia['conteudo_simplificado'] ?? ''); ?></textarea>
            </div>

            <button type="submit" style="background: #f59e0b; color: #090b0e; font-weight: 700; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; cursor: pointer;">
                Salvar Alterações
            </button>
        </form>
    </main>
</body>
</html>
