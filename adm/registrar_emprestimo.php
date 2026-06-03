<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Buscas/Gerenciadores.php';
require_once __DIR__ . '/common.php';

$gerenciador = new GerenciadorEmprestimos();
$gerenciadorLivros = new GerenciadorLivros();
$gerenciadorLeitores = new GerenciadorLeitores();
$mensagem = '';
$tipoAlerta = '';
$ultimoEmprestimoId = null;
$limiteEmprestimoPorLeitor = 5;

$livrosDisponiveis = [];
$livrosQuantidade = [];
try {
    $sql = $pdo->prepare("SELECT id_livro, titulo, autor, quantidade FROM livro ORDER BY titulo");
    $sql->execute();
    $livrosDisponiveis = $sql->fetchAll(PDO::FETCH_ASSOC);
    foreach ($livrosDisponiveis as $livro) {
        $livrosQuantidade[$livro['id_livro']] = (int)$livro['quantidade'];
    }
} catch (Exception $e) {
    $livrosDisponiveis = [];
}

$leitores = [];
$emprestimosAtivosPorLeitor = [];
try {
    $sql = $pdo->prepare("SELECT id, nome, email FROM leitor ORDER BY nome");
    $sql->execute();
    $leitores = $sql->fetchAll(PDO::FETCH_ASSOC);

    $sql = $pdo->prepare("SELECT e.id_emprestimo_leitor, COUNT(*) AS total FROM emprestimo e LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo WHERE d.id_devolucao IS NULL GROUP BY e.id_emprestimo_leitor");
    $sql->execute();
    $ativos = $sql->fetchAll(PDO::FETCH_ASSOC);
    foreach ($ativos as $item) {
        $emprestimosAtivosPorLeitor[$item['id_emprestimo_leitor']] = (int)$item['total'];
    }
} catch (Exception $e) {
    $leitores = [];
    $emprestimosAtivosPorLeitor = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {
    try {
        $id_leitor = (int)$_POST['id_leitor'];
        $id_livro = (int)$_POST['id_livro'];
        $dias_duracao = (int)($_POST['dias_duracao'] ?? 14);

        $sqlVerificaLimite = $pdo->prepare("
            SELECT COUNT(*) FROM emprestimo e
            LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
            WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL
        ");
        $sqlVerificaLimite->execute([$id_leitor]);
        $emprestimosAtivos = (int)$sqlVerificaLimite->fetchColumn();

        if ($emprestimosAtivos >= $limiteEmprestimoPorLeitor) {
            throw new Exception("Leitor atingiu o limite de $limiteEmprestimoPorLeitor empréstimos simultâneos.");
        }

        $data_emprestimo = date('Y-m-d');
        $data_prevista = date('Y-m-d', strtotime("+$dias_duracao days"));

        $sqlInsert = $pdo->prepare("
            INSERT INTO emprestimo (fk_Usuario_id_usuario, fk_Livro_id_livro, id_emprestimo_leitor, estado, data_prevista, data_emprestimo)
            VALUES (?, ?, ?, 1, ?, ?)
        ");

        $usuario_id = $_SESSION['usuario']['id_usuario'] ?? 1;
        $sqlInsert->execute([$usuario_id, $id_livro, $id_leitor, $data_prevista, $data_emprestimo]);
        $ultimoEmprestimoId = $pdo->lastInsertId();

        $sqlUpdateLivro = $pdo->prepare("UPDATE livro SET quantidade = quantidade - 1 WHERE id_livro = ?");
        $sqlUpdateLivro->execute([$id_livro]);

        $mensagem = "Empréstimo registado com sucesso!";
        $tipoAlerta = 'sucesso';
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipoAlerta = 'erro';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empréstimo - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/rg_empest.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-wrapper">

            <!-- Topbar -->
            <header class="topbar">
                <div class="welcome-text">
                    <div class="page-title-row">
                        <img src="../asset/icones/file-text.svg" alt="" class="page-title-icon">
                        <h1>Registar Empréstimo</h1>
                    </div>
                    <p>Associe um livro a um leitor e defina o prazo de devolução</p>
                </div>
                <a href="emprestimos.php" class="back-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    Voltar
                </a>
            </header>

            <!-- Alert -->
            <?php if ($mensagem): ?>
                <div class="alert-banner <?php echo $tipoAlerta; ?>">
                    <span class="alert-icon"><?php echo $tipoAlerta === 'sucesso' ? '✓' : '✕'; ?></span>
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Info strip -->
            <div class="info-strip">
                <div class="info-chip">
                    <span class="chip-label">Limite por leitor</span>
                    <span class="chip-value"><?php echo $limiteEmprestimoPorLeitor; ?> empréstimos</span>
                </div>
                <div class="info-chip">
                    <span class="chip-label">Duração padrão</span>
                    <span class="chip-value">14 dias</span>
                </div>
                <div class="info-chip">
                    <span class="chip-label">Máximo permitido</span>
                    <span class="chip-value">30 dias</span>
                </div>
            </div>

            <!-- Form card -->
            <div class="form-card">
                <div class="form-card-header">
                    <div>
                        <div class="form-step-badge">①</div>
                    </div>
                    <div>
                        <h2>Dados do Empréstimo</h2>
                        <p>Preencha todos os campos obrigatórios para concluir o registo</p>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="registrar">

                    <div class="form-body">

                        <!-- Reader & Book row -->
                        <div class="form-row">
                            <div class="form-field">
                                <label>Leitor <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select id="id_leitor" name="id_leitor" required>
                                        <option value="">Escolha um leitor…</option>
                                        <?php foreach ($leitores as $leitor): ?>
                                            <option value="<?php echo (int)$leitor['id']; ?>">
                                                <?php echo htmlspecialchars($leitor['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Loan meter -->
                                <div class="loan-meter" id="loan-meter">
                                    <div class="loan-meter-track">
                                        <div class="loan-meter-fill" id="loan-fill"></div>
                                    </div>
                                    <div class="loan-meter-label">
                                        <span id="loan-label-left">— empréstimos ativos</span>
                                        <strong id="loan-label-right">— / <?php echo $limiteEmprestimoPorLeitor; ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-field">
                                <label>Livro <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select id="id_livro" name="id_livro" required>
                                        <option value="">Escolha um livro…</option>
                                        <?php foreach ($livrosDisponiveis as $livro): ?>
                                            <option value="<?php echo (int)$livro['id_livro']; ?>" data-quantidade="<?php echo (int)$livro['quantidade']; ?>">
                                                <?php echo htmlspecialchars($livro['titulo']); ?> — <?php echo htmlspecialchars($livro['autor']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <span class="status-pill" id="book-status">
                                    <span class="status-dot"></span>
                                    <span id="book-status-text"></span>
                                </span>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <!-- Duration -->
                        <div class="form-field">
                            <label>Duração do Empréstimo</label>
                            <div class="duration-row">
                                <input type="number" id="dias_duracao" name="dias_duracao" value="14" min="1" max="30">
                                <div class="duration-tags">
                                    <span class="duration-tag" data-days="7">7 dias</span>
                                    <span class="duration-tag active" data-days="14">14 dias</span>
                                    <span class="duration-tag" data-days="21">21 dias</span>
                                    <span class="duration-tag" data-days="30">30 dias</span>
                                </div>
                            </div>
                        </div>

                    </div><!-- /form-body -->

                    <div class="form-actions">
                        <button type="submit" class="btn-register">
                            <span class="btn-check">✓</span>
                            Registar Empréstimo
                        </button>
                    </div>
                </form>

                <?php if (!empty($ultimoEmprestimoId)): ?>
                    <div style="padding: 0 28px 28px;">
                        <div class="receipt-card">
                            <div class="receipt-info">
                                <div class="receipt-icon-wrap">📄</div>
                                <div class="receipt-text">
                                    <h3>Comprovativo disponível</h3>
                                    <p>Empréstimo #<?php echo $ultimoEmprestimoId; ?> registado com sucesso</p>
                                </div>
                            </div>
                            <a href="comprovativo.php?id_emprestimo=<?php echo $ultimoEmprestimoId; ?>" class="btn-receipt">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Gerar comprovativo
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- /form-card -->

        </div><!-- /page-wrapper -->
    </main>
</div>

<script>
    const readerLoanCounts = <?php echo json_encode($emprestimosAtivosPorLeitor); ?>;
    const bookQuantities   = <?php echo json_encode($livrosQuantidade); ?>;
    const limitPerReader   = <?php echo $limiteEmprestimoPorLeitor; ?>;

    /* ── Reader select → loan meter ──────────────── */
    document.getElementById('id_leitor').addEventListener('change', function () {
        const id      = parseInt(this.value, 10);
        const meter   = document.getElementById('loan-meter');
        const fill    = document.getElementById('loan-fill');
        const lblLeft = document.getElementById('loan-label-left');
        const lblRight= document.getElementById('loan-label-right');

        if (!id) {
            meter.classList.remove('visible');
            return;
        }

        const active = readerLoanCounts[id] ?? 0;
        const pct    = Math.min((active / limitPerReader) * 100, 100);

        meter.classList.add('visible');
        fill.style.width = pct + '%';

        fill.classList.remove('half', 'full');
        if (active >= limitPerReader)      fill.classList.add('full');
        else if (active >= limitPerReader * 0.6) fill.classList.add('half');

        lblLeft.textContent  = `${active} empréstimo(s) ativo(s)`;
        lblRight.innerHTML   = `<strong>${active} / ${limitPerReader}</strong>`;
    });

    /* ── Book select → availability pill ────────── */
    document.getElementById('id_livro').addEventListener('change', function () {
        const id  = parseInt(this.value, 10);
        const pill = document.getElementById('book-status');
        const txt  = document.getElementById('book-status-text');

        if (!id) {
            pill.classList.remove('visible', 'available', 'warning', 'unavailable');
            return;
        }

        const qty = bookQuantities[id] ?? 0;
        pill.classList.remove('available', 'warning', 'unavailable');
        pill.classList.add('visible');

        if (qty === 0) {
            pill.classList.add('unavailable');
            txt.textContent = 'Sem exemplares disponíveis';
        } else if (qty <= 2) {
            pill.classList.add('warning');
            txt.textContent = `Último${qty > 1 ? 's' : ''} ${qty} exemplar${qty > 1 ? 'es' : ''} disponível`;
        } else {
            pill.classList.add('available');
            txt.textContent = `${qty} exemplares disponíveis`;
        }
    });

    /* ── Duration quick tags ─────────────────────── */
    const input = document.getElementById('dias_duracao');
    document.querySelectorAll('.duration-tag').forEach(tag => {
        tag.addEventListener('click', function () {
            document.querySelectorAll('.duration-tag').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            input.value = this.dataset.days;
        });
    });

    input.addEventListener('input', function () {
        document.querySelectorAll('.duration-tag').forEach(t => {
            t.classList.toggle('active', parseInt(t.dataset.days) === parseInt(this.value));
        });
    });
</script>
</body>
</html>