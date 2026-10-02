<?php
/**
 * TecAssist News - Landing Page Principal (index.php)
 * Com Scrollytelling 3D em Three.js, estações de pesquisa e matérias do MySQL
 */

declare(strict_types=1);
require_once __DIR__ . '/config.php';

// Busca matérias ordenadas por destaque e data
$stmt = $pdo->query("
    SELECT m.*, c.nome AS categoria_nome, c.cor_tema
    FROM materias m
    INNER JOIN categorias c ON m.categoria_id = c.id
    ORDER BY m.destaque DESC, m.publicado_em DESC
");
$todasMaterias = $stmt->fetchAll();
$materiaDestaque = $todasMaterias[0] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TecAssist News — Jornalismo Imersivo em Tecnologia Assistiva</title>
    <meta name="description" content="Portal de jornalismo focado em Tecnologia Assistiva, com laboratório 3D em tempo real e conformidade WCAG 2.1 AAA.">
    <link rel="stylesheet" href="assets/style.css">
    <!-- Three.js CDN para o Scrollytelling 3D -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        #canvas3d-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
        }
        .scrolly-overlay {
            position: relative;
            z-index: 10;
        }
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            padding: 60px 0;
        }
        .hero-box {
            max-width: 650px;
            background: rgba(12, 15, 20, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            padding: 40px;
            border-radius: 20px;
        }
        .station-section {
            min-height: 85vh;
            display: flex;
            align-items: center;
            padding: 60px 0;
        }
        .station-box {
            max-width: 580px;
            background: rgba(20, 24, 32, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid var(--surface-border);
            padding: 32px;
            border-radius: 16px;
        }
    </style>
</head>
<body>
    <!-- Canvas 3D de Fundo com Scrollytelling -->
    <div id="canvas3d-container"></div>

    <!-- Barra de Acessibilidade WCAG 2.1 AAA -->
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

    <!-- Header Principal -->
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="brand-title">TecAssist News</a>
            <nav class="main-nav">
                <a href="visao.php">Visão</a>
                <a href="auditiva.php">Audição</a>
                <a href="fisica.php">Motora</a>
                <a href="neurodivergencia.php">Neuro</a>
            </nav>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="admin/index.php" style="background: #273142; color: #f59e0b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    CMS Admin
                </a>
                <?php if ($u = usuarioLogado()): ?>
                    <span style="font-size: 12px; color: #94a3b8;"><?php echo htmlspecialchars($u['nome']); ?></span>
                    <a href="logout.php" style="font-size: 12px; color: #ef4444;">Sair</a>
                <?php else: ?>
                    <a href="login.php" style="background: #f1f5f9; color: #0f172a; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                        Entrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="scrolly-overlay">
        <!-- Hero: Visão Geral do Laboratório (Lado Esquerdo Reservado) -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-box">
                    <span style="font-family: monospace; font-size: 11px; text-transform: uppercase; color: #f59e0b; font-weight: 700;">
                        Tecnologia Assistiva & Jornalismo Inclusivo
                    </span>
                    <h1 style="font-family: serif; font-size: 44px; color: #fff; line-height: 1.15; margin: 16px 0 20px;">
                        O futuro da autonomia humana começa na acessibilidade radical.
                    </h1>
                    <p style="font-size: 15px; color: #cbd5e1; line-height: 1.7; margin-bottom: 24px;">
                        Explore nosso laboratório interativo através do tour 3D pelas quatro fronteiras da inclusão: visão, audição, motricidade e neurodiversidade.
                    </p>
                    <div style="font-size: 12px; color: #f59e0b; font-weight: 600;">
                        ↓ Role a página para iniciar o tour pelas estações
                    </div>
                </div>
            </div>
        </section>

        <!-- Estação 01: Deficiência Visual -->
        <section class="station-section">
            <div class="container">
                <div class="station-box">
                    <span style="color: #38bdf8; font-family: monospace; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                        Estação 01 · Deficiência Visual
                    </span>
                    <h2 style="font-family: serif; font-size: 28px; color: #fff; margin: 12px 0 16px;">
                        Leitores de Tela, Visores Braille e Visão Computacional
                    </h2>
                    <p style="font-size: 13px; color: #cbd5e1; line-height: 1.6; margin-bottom: 20px;">
                        Bancada equipada com matrizes táteis piezelétricas, leitores de tela em alta velocidade e óculos com processador neural para audiodescrição em tempo real.
                    </p>
                    <a href="visao.php" style="display: inline-block; background: rgba(56, 189, 248, 0.2); border: 1px solid rgba(56, 189, 248, 0.4); color: #38bdf8; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                        Ver Notícias de Deficiência Visual →
                    </a>
                </div>
            </div>
        </section>

        <!-- Estação 02: Deficiência Auditiva -->
        <section class="station-section" style="justify-content: flex-end;">
            <div class="container" style="display: flex; justify-content: flex-end;">
                <div class="station-box">
                    <span style="color: #2dd4bf; font-family: monospace; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                        Estação 02 · Deficiência Auditiva
                    </span>
                    <h2 style="font-family: serif; font-size: 28px; color: #fff; margin: 12px 0 16px;">
                        Tradução em LIBRAS e Avatares 3D Neurais
                    </h2>
                    <p style="font-size: 13px; color: #cbd5e1; line-height: 1.6; margin-bottom: 20px;">
                        Sistemas de captura de movimento óptico que reproduzem microexpressões faciais e parâmetros gramaticais cruciais da Língua Brasileira de Sinais.
                    </p>
                    <a href="auditiva.php" style="display: inline-block; background: rgba(45, 212, 191, 0.2); border: 1px solid rgba(45, 212, 191, 0.4); color: #2dd4bf; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                        Ver Notícias de Deficiência Auditiva →
                    </a>
                </div>
            </div>
        </section>

        <!-- Grade de Notícias do MySQL -->
        <section style="background: #090c10; padding: 80px 0;">
            <div class="container">
                <div style="margin-bottom: 40px;">
                    <span style="font-family: monospace; font-size: 11px; text-transform: uppercase; color: #f59e0b; font-weight: 700;">
                        Repositório Editorial
                    </span>
                    <h2 style="font-family: serif; font-size: 32px; color: #fff; margin-top: 8px;">
                        Últimas Publicações do Portal
                    </h2>
                </div>

                <div class="news-grid">
                    <?php foreach ($todasMaterias as $materia): ?>
                    <article class="article-card">
                        <img src="<?php echo htmlspecialchars($materia['imagem_url']); ?>" alt="<?php echo htmlspecialchars($materia['imagem_alt']); ?>">
                        <div class="article-content">
                            <div class="article-meta">
                                <span style="color: <?php echo htmlspecialchars($materia['cor_tema']); ?>; font-weight: 600;">
                                    <?php echo htmlspecialchars($materia['categoria_nome']); ?>
                                </span>
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
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="container" style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div>
                <strong>TecAssist News</strong> — Jornalismo Digital em Tecnologia Assistiva
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Padrão WCAG 2.1 AAA · Lei Brasileira de Inclusão</div>
            </div>
            <div>
                PHP 8.x / MySQL (PDO) · Executando perfeitamente no XAMPP
            </div>
        </div>
    </footer>

    <!-- Script de Inicialização da Cena 3D Three.js com Rolagem -->
    <script>
    (function() {
        const container = document.getElementById('canvas3d-container');
        if (!container || typeof THREE === 'undefined') return;

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0c0f14);
        scene.fog = new THREE.FogExp2(0x0c0f14, 0.04);

        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
        camera.position.set(7.5, 5.2, 11.5);
        camera.lookAt(0.5, 1.2, 0);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        const ambientLight = new THREE.AmbientLight(0xdde3ea, 0.85);
        scene.add(ambientLight);

        const spotLight = new THREE.SpotLight(0xfff7ed, 3.5, 30);
        spotLight.position.set(0, 10, 5);
        scene.add(spotLight);

        // Grade de chão
        const grid = new THREE.GridHelper(30, 30, 0x273142, 0x141820);
        scene.add(grid);

        // Mesa central holográfica
        const tableGeo = new THREE.CylinderGeometry(2, 2.2, 0.6, 32);
        const tableMat = new THREE.MeshStandardMaterial({ color: 0x1f2733, roughness: 0.5 });
        const table = new THREE.Mesh(tableGeo, tableMat);
        table.position.y = 0.3;
        scene.add(table);

        // Anel holográfico
        const ringGeo = new THREE.TorusGeometry(1.2, 0.03, 16, 64);
        const ringMat = new THREE.MeshBasicMaterial({ color: 0x38bdf8, wireframe: true });
        const ring = new THREE.Mesh(ringGeo, ringMat);
        ring.rotation.x = Math.PI / 2;
        ring.position.y = 1.3;
        scene.add(ring);

        // Waypoints da Câmera (pear.no style)
        const waypoints = [
            { pos: new THREE.Vector3(7.5, 5.2, 11.5), target: new THREE.Vector3(0.5, 1.2, 0) },
            { pos: new THREE.Vector3(-3.2, 2.2, 4.2), target: new THREE.Vector3(-4.0, 1.4, 1.2) },
            { pos: new THREE.Vector3(3.4, 2.3, 3.8), target: new THREE.Vector3(4.2, 1.4, 0.8) },
            { pos: new THREE.Vector3(0.0, 3.5, 8.0), target: new THREE.Vector3(0, 1.0, 0) }
        ];

        let targetPos = waypoints[0].pos.clone();
        let targetLook = waypoints[0].target.clone();

        window.addEventListener('scroll', () => {
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            const progress = maxScroll > 0 ? Math.min(1, Math.max(0, window.scrollY / maxScroll)) : 0;
            const segment = progress * (waypoints.length - 1);
            const idxA = Math.floor(segment);
            const idxB = Math.min(waypoints.length - 1, idxA + 1);
            const fract = segment - idxA;

            targetPos.lerpVectors(waypoints[idxA].pos, waypoints[idxB].pos, fract);
            targetLook.lerpVectors(waypoints[idxA].target, waypoints[idxB].target, fract);
        });

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        function animate() {
            requestAnimationFrame(animate);
            ring.rotation.z += 0.01;
            camera.position.lerp(targetPos, 0.05);
            camera.lookAt(targetLook);
            renderer.render(scene, camera);
        }
        animate();
    })();
    </script>
    <script src="assets/main.js"></script>
</body>
</html>
