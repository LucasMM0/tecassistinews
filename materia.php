<?php
/**
 * TecAssist News - Página Individual de Matéria com WCAG AAA e Áudio
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Incrementa visualização
$stmtView = $pdo->prepare("UPDATE materias SET visualizacoes = visualizacoes + 1 WHERE id = ?");
$stmtView->execute([$id]);

// Busca matéria
$stmt = $pdo->prepare("
    SELECT m.*, c.nome AS categoria_nome, c.slug AS categoria_slug, c.cor_tema
    FROM materias m
    INNER JOIN categorias c ON m.categoria_id = c.id
    WHERE m.id = ?
");
$stmt->execute([$id]);
$materia = $stmt->fetch();

if (!$materia) {
    http_response_code(404);
    die("Matéria não encontrada.");
}

// Inserção de novo comentário
$msgComentario = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_comentario'])) {
    $conteudoComentario = trim($_POST['conteudo'] ?? '');
    $usuario = usuarioLogado();

    if (!$usuario) {
        $msgComentario = 'Faça login para publicar um comentário.';
    } elseif ($conteudoComentario !== '') {
        $stmtInsCom = $pdo->prepare("INSERT INTO comentarios (materia_id, usuario_id, conteudo) VALUES (?, ?, ?)");
        $stmtInsCom->execute([$id, $usuario['id'], $conteudoComentario]);
        $msgComentario = 'Comentário publicado com sucesso!';
    }
}

// Busca comentários da matéria
$stmtComentarios = $pdo->prepare("
    SELECT c.*, u.nome AS autor_comentario, u.nivel
    FROM comentarios c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.materia_id = ? AND c.aprovado = 1
    ORDER BY c.criado_em DESC
");
$stmtComentarios->execute([$id]);
$comentarios = $stmtComentarios->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($materia['titulo']); ?> — TecAssist News</title>
    <meta name="description" content="<?php echo htmlspecialchars($materia['resumo']); ?>">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .article-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .tts-player {
            background: #141820;
            border: 1px solid var(--surface-border);
            padding: 16px 20px;
            border-radius: 12px;
            margin: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-tts {
            background: #f59e0b;
            color: #090b0e;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .article-body {
            font-size: 17px;
            line-height: 1.85;
            color: #e2e8f0;
        }
        .article-body p {
            margin-bottom: 24px;
        }
    </style>
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
                <a href="visao.php">Visão</a>
                <a href="auditiva.php">Audição</a>
                <a href="fisica.php">Motora</a>
                <a href="neurodivergencia.php">Neuro</a>
            </nav>
            <a href="index.php" style="font-size: 12px; color: #94a3b8;">← Voltar ao Início</a>
        </div>
    </header>

    <main class="article-container">
        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
            <span style="color: <?php echo htmlspecialchars($materia['cor_tema']); ?>; font-weight: 700;">
                <?php echo htmlspecialchars($materia['categoria_nome']); ?>
            </span>
            <span>· <?php echo date('d/m/Y', strtotime($materia['publicado_em'])); ?></span>
            <span>· <?php echo $materia['tempo_leitura']; ?> min de leitura</span>
        </div>

        <h1 style="font-family: var(--font-serif); font-size: 38px; color: #fff; line-height: 1.2; margin-bottom: 16px;">
            <?php echo htmlspecialchars($materia['titulo']); ?>
        </h1>

        <p style="font-size: 17px; color: #94a3b8; line-height: 1.6; margin-bottom: 24px;">
            <?php echo htmlspecialchars($materia['subtitulo']); ?>
        </p>

        <div style="padding: 12px 0; border-top: 1px solid var(--surface-border); border-bottom: 1px solid var(--surface-border); font-size: 12px; color: #94a3b8; display: flex; justify-content: space-between;">
            <div>
                <strong style="color: #fff;"><?php echo htmlspecialchars($materia['autor_nome']); ?></strong>
                <span>— <?php echo htmlspecialchars($materia['autor_cargo']); ?></span>
            </div>
            <div>
                <?php echo $materia['visualizacoes']; ?> leituras
            </div>
        </div>

        <!-- Player Text-to-Speech e Linguagem Simples -->
        <div class="tts-player">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button id="btnPlayNarrador" class="btn-tts">
                    ▶ Ouvir Matéria (TTS)
                </button>
                <span style="font-size: 12px; color: #94a3b8;">Síntese de voz neural</span>
            </div>
            <?php if (!empty($materia['conteudo_simplificado'])): ?>
                <button id="btnTextoSimples" class="a11y-btn" style="padding: 8px 12px;">
                    Ver em Linguagem Simples
                </button>
            <?php endif; ?>
        </div>

        <figure style="margin-bottom: 32px;">
            <img src="<?php echo htmlspecialchars($materia['imagem_url']); ?>" alt="<?php echo htmlspecialchars($materia['imagem_alt']); ?>" style="width: 100%; border-radius: 12px; border: 1px solid var(--surface-border);">
            <figcaption style="font-size: 12px; color: #64748b; margin-top: 8px; font-style: italic;">
                <?php echo htmlspecialchars($materia['imagem_legenda']); ?>
            </figcaption>
        </figure>

        <!-- Corpo da Notícia (Original e Simplificado) -->
        <div id="conteudoArtigo" class="article-body">
            <div id="conteudoCompleto">
                <?php 
                $paragrafos = explode("\n", $materia['conteudo_completo']);
                foreach ($paragrafos as $p) {
                    $p = trim($p);
                    if ($p !== '') {
                        echo '<p>' . htmlspecialchars($p) . '</p>';
                    }
                }
                ?>
            </div>

            <?php if (!empty($materia['conteudo_simplificado'])): ?>
            <div id="conteudoSimplificado" style="display: none; background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3); padding: 24px; border-radius: 12px;">
                <div style="font-size: 12px; color: #c084fc; font-weight: 700; margin-bottom: 12px; text-transform: uppercase;">
                    Versão Desconstruída em Linguagem Simples (Plain Language)
                </div>
                <?php 
                $paragrafosSimples = explode("\n", $materia['conteudo_simplificado']);
                foreach ($paragrafosSimples as $ps) {
                    $ps = trim($ps);
                    if ($ps !== '') {
                        echo '<p style="color: #e9d5ff; font-size: 16px;">' . htmlspecialchars($ps) . '</p>';
                    }
                }
                ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Seção de Comentários -->
        <section style="margin-top: 60px; padding-top: 30px; border-top: 1px solid var(--surface-border);">
            <h3 style="font-family: var(--font-serif); font-size: 24px; color: #fff; margin-bottom: 20px;">
                Comentários dos Leitores (<?php echo count($comentarios); ?>)
            </h3>

            <?php if ($msgComentario): ?>
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($msgComentario); ?>
                </div>
            <?php endif; ?>

            <?php if ($u = usuarioLogado()): ?>
                <form method="POST" style="margin-bottom: 30px;">
                    <input type="hidden" name="novo_comentario" value="1">
                    <textarea name="conteudo" required placeholder="Deixe sua contribuição técnica ou vivência..." rows="3" style="width: 100%; box-sizing: border-box; background: #141820; border: 1px solid #273142; color: #fff; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 10px;"></textarea>
                    <button type="submit" class="btn-tts">Enviar Comentário</button>
                </form>
            <?php else: ?>
                <div style="background: #141820; border: 1px solid var(--surface-border); padding: 16px; border-radius: 8px; font-size: 13px; margin-bottom: 30px;">
                    <a href="login.php" style="color: #f59e0b; font-weight: 700;">Faça login</a> para participar das discussões da matéria.
                </div>
            <?php endif; ?>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($comentarios as $c): ?>
                    <div style="background: #141820; border: 1px solid var(--surface-border); padding: 16px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px;">
                            <strong style="color: #fff;"><?php echo htmlspecialchars($c['autor_comentario']); ?></strong>
                            <span style="color: #64748b;"><?php echo date('d/m/Y H:i', strtotime($c['criado_em'])); ?></span>
                        </div>
                        <p style="font-size: 13px; color: #cbd5e1; line-height: 1.5;">
                            <?php echo nl2br(htmlspecialchars($c['conteudo'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container" style="text-align: center;">
            TecAssist News — Jornalismo Digital em Tecnologia Assistiva
        </div>
    </footer>

    <script src="assets/main.js"></script>
</body>
</html>
