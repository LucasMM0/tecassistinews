-- ========================================================
-- TecAssist News - Estrutura do Banco de Dados MySQL (PDO)
-- Compatível com XAMPP / MariaDB / MySQL 8.x
-- ========================================================

CREATE DATABASE IF NOT EXISTS tecassist_news
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tecassist_news;

-- Remove tabelas pré-existentes para garantir recriação limpa sem conflitos
DROP TABLE IF EXISTS comentarios;
DROP TABLE IF EXISTS materias;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS categorias;

-- 1. Tabela de Categorias (Tipos de Deficiência)
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(50) NOT NULL UNIQUE,
  nome VARCHAR(100) NOT NULL,
  subtitulo VARCHAR(255),
  descricao TEXT,
  cor_tema VARCHAR(20) DEFAULT '#2563eb',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Usuários (Sessões e Níveis de Acesso)
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  nivel ENUM('leitor', 'admin') DEFAULT 'leitor',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Matérias Jornalísticas (CRUD Completo e Acessibilidade)
CREATE TABLE materias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  subtitulo VARCHAR(350),
  slug VARCHAR(191) NOT NULL UNIQUE,
  autor_nome VARCHAR(150) NOT NULL,
  autor_cargo VARCHAR(150),
  imagem_url VARCHAR(500) NOT NULL,
  imagem_alt TEXT NOT NULL,
  imagem_legenda VARCHAR(255),
  resumo TEXT NOT NULL,
  conteudo_completo LONGTEXT NOT NULL,
  conteudo_simplificado LONGTEXT,
  tempo_leitura INT DEFAULT 5,
  destaque TINYINT(1) DEFAULT 0,
  visualizacoes INT DEFAULT 0,
  curtidas INT DEFAULT 0,
  tags_acessibilidade VARCHAR(255),
  publicado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela de Comentários de Leitores
CREATE TABLE comentarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  materia_id INT NOT NULL,
  usuario_id INT NOT NULL,
  conteudo TEXT NOT NULL,
  aprovado TINYINT(1) DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Inserção das Categorias das 4 Estações de Pesquisa
INSERT INTO categorias (id, slug, nome, subtitulo, descricao, cor_tema) VALUES
(1, 'visao', 'Deficiência Visual', 'Leitores de tela, visores braille e visão computacional', 'Avanços em displays táteis matriciais, inteligência artificial multimodal para descrição de cena e autonomia em mobilidade urbana.', '#2563eb'),
(2, 'auditiva', 'Deficiência Auditiva', 'Tradução em LIBRAS, avatares 3D e legendagem viva', 'Tecnologias que rompem barreiras de comunicação através de processamento de linguagem natural para línguas de sinais e síntese háptica de áudio.', '#0d9488'),
(3, 'fisica', 'Deficiência Física & Motora', 'Rastreamento ocular, acionadores e interfaces adaptadas', 'Soluções de hardware ergonômico, eletromiografia superficial e sistemas de eye-tracking que garantem controle total de computadores e cadeiras de rodas.', '#d97706'),
(4, 'neurodivergencia', 'Neurodivergência & Cognição', 'Leitura simplificada, tipografia para dislexia e foco', 'Ferramentas de sintetização visual, filtros sensoriais, máscaras de leitura e interfaces adaptativas pensadas para autismo, TDAH e dislexia.', '#7c3aed');

-- 6. Inserção de Matérias Iniciais Prontas
INSERT INTO materias 
(id, categoria_id, titulo, subtitulo, slug, autor_nome, autor_cargo, imagem_url, imagem_alt, imagem_legenda, resumo, conteudo_completo, conteudo_simplificado, tempo_leitura, destaque, visualizacoes, curtidas, tags_acessibilidade)
VALUES
(1, 1, 
'Visores Hápticos de Matriz Completa: A Transição do Braille de Linha Única para a Representação Gráfica Tátil',
'Pesquisadores validam nova geração de tablets dinâmicos capazes de renderizar gráficos táteis, mapas e fórmulas matemáticas em milissegundos.',
'visores-hapticos-matriz-completa-braille-grafico',
'Dra. Helena Moreira', 'Especialista em Ergonomia Tátil e Engenharia Biomédica',
'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&q=80',
'Fotografia em plano detalhe de um terminal braille eletrônico de última geração sobre mesa de madeira clara, com pinos táteis erguidos formando equações e gráficos táteis com precisão geométrica.',
'Terminal de pinos microatuados permite a estudantes e profissionais cegos navegar por matrizes matemáticas e plantas arquitetônicas em tempo real.',
'A substituição de displays braille lineares tradicionais por superfícies contínuas de microatuadores piezelétricos inaugura uma nova era de autonomia.',
'Durante décadas, os usuários de tecnologia assistiva para deficiência visual enfrentaram uma limitação severa na computação pessoal: os displays braille comerciais ofereciam apenas uma linha de 20 a 80 células. Isso permitia a leitura sequencial de textos, mas tornava praticamente impossível a apreensão instantânea de diagramas, mapas celestes, organogramas ou fórmulas científicas bidimensionais.\n\nA virada de paradigma apresentada nesta semana por consórcios de pesquisa reside na padronização de matrizes táteis densas com mais de 3.000 pontos independentes. Cada ponto possui modulação de altura variável e sensibilidade a pressão táctil com feedback de vibração, permitindo ao leitor interagir como em uma tela sensível ao toque.\n\nAlém do hardware mecânico miniaturizado, o grande diferencial do sistema é o pipeline de inteligência artificial de bordo, capaz de traduzir gráficos vetoriais em relevos compreensíveis com diferentes frequências de vibração.',
'Visores de braille antigos só mostravam uma linha de texto por vez.\nNovos aparelhos têm milhares de pinos móveis que formam desenhos inteiros.\nAgora pessoas cegas podem sentir mapas, gráficos e fórmulas com a ponta dos dedos.',
6, 1, 3420, 284, 'Braille Gráfico, Audiodescrição Nativa, WCAG AAA'),

(2, 2,
'Avatares 3D Neurais Redefinem a Tradução de LIBRAS com Microexpressões e Gramática Espacial',
'Superando a rigidez robótica dos primeiros tradutores virtuais, novas redes neurais capturam nuances faciais cruciais para a concordância gramatical na Língua Brasileira de Sinais.',
'avatares-3d-neurais-libras-microexpressoes',
'Thiago Nogueira', 'Jornalista e Intérprete Certificado Prolibras',
'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=1200&q=80',
'Monitor de estúdio exibindo um modelo tridimensional expressivo de avatar de LIBRAS realizando sinais manuais e marcações faciais gramaticais simultâneas com luzes destacadas.',
'Sistema analisa entonação de voz e pontuação escrita para modular o levantamento de sobrancelhas e movimentos de cabeça do intérprete 3D.',
'Na Língua de Sinais, expressões faciais e corporais correspondem a elementos gramaticais essenciais como advérbios e tipos de oração.',
'Quem não é fluente em LIBRAS frequentemente comete o erro de supor que a língua de sinais é composta unicamente por gestos com as mãos. Na realidade linguística, as expressões não manuais (movimentos de sobrancelhas, abertura dos olhos, inclinação de cabeça e tensão labial) funcionam como parâmetros gramaticais obrigatórios.\n\nPor anos, avatares tridimensionais genéricos foram criticados pela comunidade surda por apresentarem rostos estáticos, o que provocava perda substancial de sentido em orações interrogativas, exclamativas e condicionais.\n\nO novo software adota modelos generativos treinados em captura de movimento óptico de alta definição, atingindo 94% de compreensão em testes com comunidades surdas.',
'A Língua de Sinais não usa apenas as mãos: o rosto e a cabeça mudam o significado das frases.\nBonecos 3D antigos tinham rosto duro e não mostravam perguntas ou sentimentos.\nO novo sistema usa inteligência artificial para fazer expressões humanas perfeitas.',
5, 1, 2890, 195, 'LIBRAS Nativo, Expressões Não-Manuais, Legendas Sincronizadas'),

(3, 3,
'Eye-Tracking de Baixa Latência e Código Aberto Democratiza Autonomia para Pacientes com ELA',
'Sensores de infravermelho de 120Hz acoplados a softwares open-source eliminam a barreira de custo e permitem digitação de até 35 palavras por minuto com piscar voluntário.',
'eye-tracking-codigo-aberto-autonomia-ela',
'Clara Vasconcellos', 'Pesquisadora em Tecnologias de Reabilitação Neurofuncional',
'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1200&q=80',
'Barra discreta de rastreamento ocular instalada na borda inferior de um monitor, com indicadores ópticos de foco no olhar do usuário e teclado virtual de alta velocidade na tela.',
'Dispositivo custa menos de um quinto dos equipamentos comerciais importados e opera com qualquer computador modesto.',
'Pacientes com Esclerose Lateral Amiotrófica (ELA) e lesões medulares altas ganham alternativa econômica para comunicação independente.',
'A perda progressiva da motricidade voluntária decorrente de doenças neuromusculares historicamente colocava pacientes em isolamento comunicativo severo. Embora existissem equipamentos comerciais de controle ocular, seus preços proibitivos frequentemente ultrapassavam R$ 40 mil reais.\n\nA iniciativa de código aberto reverteu esse cenário com uma barra de infravermelho de baixo custo combinada com algoritmos de rastreamento pupilar que monitoram o vetor de reflexo corneano com margem de erro inferior a 0,4 graus.\n\nO sistema permite escrever frases inteiras por predição semântica e controlar lâmpadas e portas da residência via automação conectada.',
'Pessoas com doenças motoras graves muitas vezes só conseguem mexer os olhos.\nAparelhos importados de controle pelos olhos eram extremamente caros.\nUm projeto aberto criou um aparelho acessível que permite falar, escrever e acender luzes apenas com o olhar.',
7, 1, 4110, 340, 'Eye-Tracking, Hardware Aberto, Controle Motor Adaptativo'),

(4, 4,
'Interfaces com Foco Cognitivo e Tipografia Fluida Reduzem Sobrecarga Sensorial no Jornalismo Digital',
'Estudo com leitores neurodivergentes demonstra que réguas de foco, fontes com ancoragem inferior e eliminação de estímulos concorrentes aumentam a retenção em 78%.',
'interfaces-foco-cognitivo-tipografia-dislexia-neurodivergencia',
'Prof. Marcos Andrade', 'Doutor em Ciências Cognitivas e Designer de Interação',
'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200&q=80',
'Ambiente de leitura sereno com tela de tinta eletrônica exibindo texto diagramado com espaçamento arejado, régua de foco cinza suave destacando duas linhas e ausência total de anúncios invasivos.',
'A eliminação de banners piscantes e o controle da densidade de palavras por linha são princípios chave do web design neuroinclusivo.',
'A web moderna tornou-se um campo minado de sobrecarga sensorial para pessoas no espectro autista, com TDAH ou dislexia.',
'Páginas de notícias convencionais estão saturadas de vídeos automáticos, banners que piscam e pop-ups irritantes. Para quem tem processamento sensorial atípico, ler nesse ambiente exige um esforço mental desproporcional.\n\nPesquisas com 400 voluntários comprovaram que o uso de réguas de foco que escurecem linhas adjacentes reduz o salto acidental de linhas em 84% entre disléxicos.\n\nAlém disso, o uso de fontes com ancoragem de base e textos em linguagem simples garante compreensão imediata e respeitosa para todos os tipos de mente.',
'Muitos sites de notícias têm coisas piscando e se mexendo, o que cansa o cérebro rapidamente.\nPara quem tem TDAH, autismo ou dislexia, a leitura fica muito difícil.\nUsar fontes claras, sem propagandas invasivas e com guias de foco ajuda a ler com calma e clareza.',
5, 1, 3820, 290, 'Plain Language, Tipografia para Dislexia, Régua de Foco, Baixo Estímulo');
