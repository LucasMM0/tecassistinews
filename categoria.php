<?php
/**
 * Template Genérico de Categoria para o XAMPP
 * Usado por visao.php, auditiva.php, fisica.php e neurodivergencia.php
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

if (!isset($categoriaSlug)) {
    $categoriaSlug = filter_input(INPUT_GET, 'tipo', FILTER_DEFAULT) ?: 'visao';
}

$stmtCat = $pdo->prepare("SELECT * FROM categorias WHERE slug = ? LIMIT 1");
$stmtCat->execute([$categoriaSlug]);
$categoria = $stmtCat->fetch();

if (!$categoria) {
    die("Categoria não encontrada.");
}

$stmtMat = $pdo->prepare("
    SELECT m.*, c.nome AS categoria_nome, c.cor_tema
    FROM materias m
    INNER JOIN categorias c ON m.categoria_id = c.id
    WHERE c.slug = ?
    ORDER BY m.publicado_em DESC
");
$stmtMat->execute([$categoriaSlug]);
$materias = $stmtMat->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoria['nome']); ?> — TecAssist News</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <aside class="a11y-bar">
        <div class="container">
            <div>
                <span style="color: #10b981; font-weight: 700;">● WCAG 2.1 AAA</span>
                <span style="color: #64748b; margin-left: 8px;">Portal Acessível</span>
            </div>
            <div class="a11y-controls">
                <button id="btnDiminuirFonte" class="a11y-btn" aria-label="Diminuir fonte">A-</button>
                <button id="btnAumentarFonte" class="a11y-btn" aria-label="Aumentar fonte">A+</button>
                <button id="btnContraste" class="a11y-btn">Alto Contraste</button>
                <button id="btnDislexia" class="a11y-btn">Fonte Dislexia</button>
            </div>
        </div>
    </aside>

    <header class="main-header">
        <div class="container">
            <a href="index.php" class="brand-title">TecAssist News</a>
            <nav class="main-nav">
                <a href="visao.php" <?php echo $categoriaSlug === 'visao' ? 'class="active"' : ''; ?>>Visão</a>
                <a href="auditiva.php" <?php echo $categoriaSlug === 'auditiva' ? 'class="active"' : ''; ?>>Audição</a>
                <a href="fisica.php" <?php echo $categoriaSlug === 'fisica' ? 'class="active"' : ''; ?>>Motora</a>
                <a href="neurodivergencia.php" <?php echo $categoriaSlug === 'neurodivergencia' ? 'class="active"' : ''; ?>>Neuro</a>
            </nav>
            <a href="index.php" style="font-size: 12px; color: #94a3b8;">← Início</a>
        </div>
    </header>

    <main class="container" style="padding: 50px 20px;">
        <div style="max-width: 800px; margin-bottom: 40px;">
            <span style="color: <?php echo htmlspecialchars($categoria['cor_tema']); ?>; font-family: monospace; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                Editoria Especializada
            </span>
            <h1 style="font-family: var(--font-serif); font-size: 40px; color: #fff; margin: 12px 0;">
                <?php echo htmlspecialchars($categoria['nome']); ?>
            </h1>
            <p style="font-size: 16px; color: #94a3b8; line-height: 1.6;">
                <?php echo htmlspecialchars($categoria['descricao']); ?>
            </p>
        </div>

        <div class="news-grid">
            <?php foreach ($materias as $materia): ?>
            <article class="article-card">
                <img src="<?php echo htmlspecialchars($materia['imagem_url']); ?>" alt="<?php echo htmlspecialchars($materia['imagem_alt']); ?>">
                <div class="article-content">
                    <div class="article-meta">
                        <span><?php echo date('d/m/Y', strtotime($materia['publicado_em'])); ?></span>
                        <span>· <?php echo $materia['tempo_leitura']; ?> min</span>
                    </div>
                    <h3 class="article-title">
                        <a href="materia.php?id=<?php echo $materia['id']; ?>">
                            <?php echo htmlspecialchars($materia['titulo']); ?>
                        </a>
                    </h3>
                    <p class="article-summary">
                        <?php echo htmlspecialchars($materia['subtitulo']); ?>
                    </p>
                    <a href="materia.php?id=<?php echo $materia['id']; ?>" style="color: #f59e0b; font-size: 12px; font-weight: 600;">
                        Ler Reportagem Completa →
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container" style="text-align: center;">
            TecAssist News — Jornalismo Digital em Tecnologia Assistiva
        </div>
    </footer>

    <script src="assets/main.js"></script>
</body>
</html>
