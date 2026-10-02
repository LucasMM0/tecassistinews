<?php
/**
 * TecAssist News - Criação de Nova Matéria com Upload e Acessibilidade (WCAG AAA)
 */

declare(strict_types=1);
require_once __DIR__ . '/../config.php';
verificarAcessoAdmin();

$erro = '';

// Busca categorias
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY id ASC")->fetchAll();

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
        // Gera slug simples
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($titulo));
        $slug = trim($slug, '-') . '-' . time();

        if (empty($imagemUrl)) {
            $imagemUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&q=80';
        }
        if (empty($imagemAlt)) {
            $imagemAlt = 'Fotografia jornalística ilustrativa da matéria: ' . $titulo;
        }

        $resumo = substr(strip_tags($conteudoCompleto), 0, 180) . '...';

        $stmt = $pdo->prepare("
            INSERT INTO materias 
            (categoria_id, titulo, subtitulo, slug, autor_nome, autor_cargo, imagem_url, imagem_alt, imagem_legenda, resumo, conteudo_completo, conteudo_simplificado, tempo_leitura)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $categoriaId, $titulo, $subtitulo, $slug, $autorNome, $autorCargo,
            $imagemUrl, $imagemAlt, $imagemLegenda, $resumo, $conteudoCompleto, $conteudoSimplificado, $tempoLeitura
        ]);

        header("Location: index.php?msg=sucesso");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Matéria — TecAssist News</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            color: #d4d4d8;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            box-sizing: border-box;
            background: #090d14;
            border: 1px solid #273142;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
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
            Cadastrar Nova Matéria
        </h1>
        <p style="font-size: 13px; color: #94a3b8; margin-bottom: 24px;">
            Preencha os campos editoriais e o texto alternativo para acessibilidade WCAG AAA.
        </p>

        <?php if ($erro): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Título da Notícia</label>
                <input type="text" name="titulo" required class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Subtítulo / Linha Fina</label>
                <input type="text" name="subtitulo" class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Estação / Editoria</label>
                    <select name="categoria_id" class="form-select">
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tempo de Leitura (minutos)</label>
                    <input type="number" name="tempo_leitura" value="5" min="1" class="form-input">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Nome do Autor</label>
                    <input type="text" name="autor_nome" value="Redação TecAssist" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo ou Especialidade</label>
                    <input type="text" name="autor_cargo" value="Especialista em Acessibilidade" class="form-input">
                </div>
            </div>

            <!-- Mídia e Acessibilidade -->
            <div style="background: #141820; border: 1px solid var(--surface-border); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                <div style="font-size: 11px; font-family: monospace; color: #f59e0b; text-transform: uppercase; margin-bottom: 12px;">
                    Acessibilidade da Imagem (WCAG 2.1 AAA)
                </div>
                <div class="form-group">
                    <label class="form-label">URL da Imagem de Capa</label>
                    <input type="text" name="imagem_url" placeholder="https://..." class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Texto Alternativo Minucioso (alt) — Obrigatório para Leitores de Tela</label>
                    <textarea name="imagem_alt" rows="2" class="form-textarea" placeholder="Descreva os elementos físicos, visuais e contextuais da imagem..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Legenda da Foto</label>
                    <input type="text" name="imagem_legenda" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Conteúdo Completo (separe parágrafos pressionando Enter)</label>
                <textarea name="conteudo_completo" rows="8" required class="form-textarea"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Versão em Linguagem Simples (Plain Language - Opcional)</label>
                <textarea name="conteudo_simplificado" rows="4" class="form-textarea" placeholder="Frases curtas em ordem direta para pessoas com sobrecarga sensorial ou dislexia..."></textarea>
            </div>

            <button type="submit" style="background: #f59e0b; color: #090b0e; font-weight: 700; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; cursor: pointer;">
                Publicar Matéria no Portal
            </button>
        </form>
    </main>
</body>
</html>
