            const modal = document.getElementById('modalNovoLeitor');

            function abrirModal() {
                modal.classList.add('show');
                document.getElementById('formNovoLeitor').reset();
                setTimeout(() => document.getElementById('inp-nome').focus(), 200);
            }

            function fecharModal() {
                modal.classList.remove('show');
            }

            // Fechar clicando fora do card
            modal.addEventListener('click', e => {
                if (e.target === modal) fecharModal();
            });

            // Fechar com ESC
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') fecharModal();
            });

            async function salvarLeitor(e) {
                e.preventDefault();
                const btn = document.getElementById('btn-salvar');
                btn.disabled = true;
                btn.innerHTML = '<span class="spin">↻</span> A salvar…';

                const data = new FormData(document.getElementById('formNovoLeitor'));
                data.append('action', 'cadastrar_leitor');

                try {
                    const res  = await fetch(window.location.href, { method: 'POST', body: data });
                    const json = await res.json();

                    if (json.success) {
                        fecharModal();
                        mostrarToast(json.message, 'success');
                        setTimeout(() => location.reload(), 1300);
                    } else {
                        mostrarToast(json.message, 'error');
                        btn.disabled = false;
                        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 3v4H8V3M12 12v5m-2-2h4"/>
                            </svg> Salvar Leitor`;
                    }
                } catch {
                    mostrarToast('Erro de comunicação com o servidor.', 'error');
                    btn.disabled = false;
                }
            }

            function mostrarToast(msg, tipo) {
                const icons = {
                    success: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
                    error:   `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
                };
                const t = document.getElementById('toast');
                t.className = `toast ${tipo}`;
                t.innerHTML = icons[tipo] + msg;
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 3500);
            }