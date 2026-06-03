    /* ── Toggle visibilidade da senha ──────────────── */
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.style.color = isText ? '' : 'var(--primary)';
    }

    /* ── Força da senha ────────────────────────────── */
    document.getElementById('senha').addEventListener('input', function () {
        const val   = this.value;
        const fill  = document.getElementById('strength-fill');
        const label = document.getElementById('strength-label');

        let score = 0;
        if (val.length >= 6)                      score++;
        if (val.length >= 10)                     score++;
        if (/[A-Z]/.test(val))                    score++;
        if (/[0-9]/.test(val))                    score++;
        if (/[^A-Za-z0-9]/.test(val))             score++;

        const levels = [
            { w: '0%',   bg: 'var(--gray-200)', txt: 'Introduza uma senha' },
            { w: '25%',  bg: '#e57373',          txt: 'Fraca' },
            { w: '50%',  bg: '#ffb74d',          txt: 'Razoável' },
            { w: '75%',  bg: '#fff176',          txt: 'Boa' },
            { w: '90%',  bg: '#81c784',          txt: 'Forte' },
            { w: '100%', bg: '#4caf50',          txt: 'Muito forte' },
        ];

        const l = levels[score] ?? levels[0];
        fill.style.width      = val ? l.w  : '0%';
        fill.style.background = l.bg;
        label.textContent     = val ? l.txt : 'Introduza uma senha';
        label.style.color     = val ? l.bg  : 'var(--gray-400)';

        checkMatch();
    });

    /* ── Verificar correspondência ─────────────────── */
    function checkMatch() {
        const s1    = document.getElementById('senha').value;
        const s2    = document.getElementById('senha_conf').value;
        const lbl   = document.getElementById('match-label');

        if (!s2) { lbl.textContent = ''; return; }

        if (s1 === s2) {
            lbl.textContent  = '✓ As senhas coincidem';
            lbl.style.color  = '#2d6a4f';
        } else {
            lbl.textContent  = '✕ As senhas não coincidem';
            lbl.style.color  = '#7c2d2d';
        }
    }

    document.getElementById('senha_conf').addEventListener('input', checkMatch);

    /* ── Definir data de hoje como padrão ──────────── */
    const dateInput = document.querySelector('input[name="data_usuario"]');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }