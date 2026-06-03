/* ══════════════════════════════════════════════
   modal_leitor.js  –  Novo Leitor
   ══════════════════════════════════════════════ */

// ── Abrir / Fechar modal ─────────────────────
function abrirModal() {
    document.getElementById('modalNovoLeitor').style.display = 'flex';
    document.getElementById('inp-nome').focus();
}

function fecharModal() {
    document.getElementById('modalNovoLeitor').style.display = 'none';
    document.getElementById('formNovoLeitor').reset();
}

// Fecha ao clicar fora do conteúdo
document.getElementById('modalNovoLeitor').addEventListener('click', function (e) {
    if (e.target === this) fecharModal();
});

// ── Toast de feedback ────────────────────────
function mostrarToast(msg, tipo = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = 'toast ' + tipo + ' show';
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// ── Submit do formulário ─────────────────────
async function salvarLeitor(event) {
    event.preventDefault();

    const btn = document.getElementById('btn-salvar');
    btn.disabled = true;
    btn.textContent = 'A guardar…';

    const form = document.getElementById('formNovoLeitor');
    const dados = new FormData(form);

    try {
        const resp = await fetch('cadastrar_leitores.php', {
            method: 'POST',
            body: dados,
        });

        const json = await resp.json();

        if (json.success) {
            mostrarToast(json.message, 'success');
            fecharModal();

            // Recarrega a página para exibir o novo leitor na tabela
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarToast(json.message || 'Erro desconhecido.', 'error');
        }
    } catch (err) {
        mostrarToast('Falha na comunicação com o servidor.', 'error');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 3v4H8V3M12 12v5m-2-2h4"/>
            </svg>
            Salvar Leitor`;
    }
}