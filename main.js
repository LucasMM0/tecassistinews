// Scripts Globais de Acessibilidade WCAG 2.1 AAA para TecAssist News
document.addEventListener('DOMContentLoaded', () => {
    // 1. Alternador de Alto Contraste
    const btnContraste = document.getElementById('btnContraste');
    if (btnContraste) {
        btnContraste.addEventListener('click', () => {
            document.body.classList.toggle('theme-high-contrast');
            const ativo = document.body.classList.contains('theme-high-contrast');
            localStorage.setItem('tecassist_alto_contraste', ativo ? '1' : '0');
        });

        if (localStorage.getItem('tecassist_alto_contraste') === '1') {
            document.body.classList.add('theme-high-contrast');
        }
    }

    // 2. Redimensionador de Fonte
    let fontStep = parseInt(localStorage.getItem('tecassist_font_step') || '0', 10);
    const fontScales = ['100%', '115%', '130%', '145%'];
    
    function aplicarFonte() {
        document.documentElement.style.fontSize = fontScales[fontStep] || '100%';
        localStorage.setItem('tecassist_font_step', fontStep.toString());
    }

    const btnAumentar = document.getElementById('btnAumentarFonte');
    const btnDiminuir = document.getElementById('btnDiminuirFonte');

    if (btnAumentar && btnDiminuir) {
        btnAumentar.addEventListener('click', () => {
            if (fontStep < 3) {
                fontStep++;
                aplicarFonte();
            }
        });

        btnDiminuir.addEventListener('click', () => {
            if (fontStep > 0) {
                fontStep--;
                aplicarFonte();
            }
        });

        aplicarFonte();
    }

    // 3. Fonte para Dislexia
    const btnDislexia = document.getElementById('btnDislexia');
    if (btnDislexia) {
        btnDislexia.addEventListener('click', () => {
            document.body.classList.toggle('font-dyslexia');
            const ativo = document.body.classList.contains('font-dyslexia');
            localStorage.setItem('tecassist_dislexia', ativo ? '1' : '0');
        });

        if (localStorage.getItem('tecassist_dislexia') === '1') {
            document.body.classList.add('font-dyslexia');
        }
    }

    // 4. Síntese de Voz (Text-to-Speech nativo em Português)
    const btnTTS = document.getElementById('btnPlayNarrador');
    if (btnTTS && 'speechSynthesis' in window) {
        let narrando = false;
        btnTTS.addEventListener('click', () => {
            if (narrando) {
                window.speechSynthesis.cancel();
                btnTTS.textContent = '▶ Ouvir Matéria (TTS)';
                narrando = false;
            } else {
                const titulo = document.querySelector('h1')?.textContent || '';
                const corpo = document.querySelector('#conteudoArtigo')?.textContent || '';
                const textoCompleto = `${titulo}. ${corpo}`;

                const utterance = new SpeechSynthesisUtterance(textoCompleto);
                utterance.lang = 'pt-BR';
                utterance.rate = 1.0;

                utterance.onstart = () => {
                    narrando = true;
                    btnTTS.textContent = '⏹ Pausar Narração';
                };

                utterance.onend = utterance.onerror = () => {
                    narrando = false;
                    btnTTS.textContent = '▶ Ouvir Matéria (TTS)';
                };

                window.speechSynthesis.speak(utterance);
            }
        });
    }

    // 5. Alternador de Linguagem Simples (Plain Language)
    const btnSimples = document.getElementById('btnTextoSimples');
    const conteudoCompleto = document.getElementById('conteudoCompleto');
    const conteudoSimplificado = document.getElementById('conteudoSimplificado');

    if (btnSimples && conteudoCompleto && conteudoSimplificado) {
        btnSimples.addEventListener('click', () => {
            const visivel = conteudoSimplificado.style.display !== 'none';
            if (visivel) {
                conteudoSimplificado.style.display = 'none';
                conteudoCompleto.style.display = 'block';
                btnSimples.textContent = 'Ver em Linguagem Simples';
            } else {
                conteudoSimplificado.style.display = 'block';
                conteudoCompleto.style.display = 'none';
                btnSimples.textContent = 'Ver Texto Original';
            }
        });
    }
});
