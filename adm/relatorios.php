<?php
require_once __DIR__ . '/../Buscas/Gerenciadores.php';
require_once __DIR__ . '/common.php';

$gerenciadorLivros      = new GerenciadorLivros();
$gerenciadorLeitores    = new GerenciadorLeitores();
$gerenciadorEmprestimos = new GerenciadorEmprestimos();
$gerenciadorReservas    = new GerenciadorReservas();

$totalLivros      = $gerenciadorLivros->obterTotal();
$totalLeitores    = $gerenciadorLeitores->obterTotal();
$totalEmprestimos = $gerenciadorEmprestimos->obterTotal();
$totalReservas    = $gerenciadorReservas->obterTotal();

global $pdo;
$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal   = $_GET['data_final'] ?? '';
$periodCondition = '';
$reservaPeriodCondition = '';
$paramsPeriodo = [];

if ($dataInicial !== '' && $dataFinal !== '') {
    $periodCondition = ' AND e.data_emprestimo BETWEEN ? AND ? ';
    $reservaPeriodCondition = ' AND r.data_reserva BETWEEN ? AND ? ';
    $paramsPeriodo = [$dataInicial, $dataFinal];
}

try {
    $sqlEmprestimosPeriodo = $pdo->prepare("SELECT COUNT(*) FROM emprestimo e WHERE 1=1 {$periodCondition}");
    $sqlEmprestimosPeriodo->execute($paramsPeriodo);
    $emprestimosPeriodo = (int)$sqlEmprestimosPeriodo->fetchColumn();

    $sqlReservasPeriodo = $pdo->prepare("SELECT COUNT(*) FROM reserva r WHERE 1=1 {$reservaPeriodCondition}");
    $sqlReservasPeriodo->execute($paramsPeriodo);
    $reservasPeriodo = (int)$sqlReservasPeriodo->fetchColumn();

    $sqlAtrasados = $pdo->prepare("SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista,
           li.titulo AS livro,
           COALESCE(l.nome, u.nome, '-') AS leitor,
           'Ativo' AS status
    FROM emprestimo e
    LEFT JOIN livro li    ON e.fk_Livro_id_livro = li.id_livro
    LEFT JOIN leitor l    ON e.id_emprestimo_leitor = l.id
    LEFT JOIN usuario u   ON e.fk_Usuario_id_usuario = u.id_usuario
    LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
    WHERE d.id_devolucao IS NULL
      AND e.data_prevista < NOW() {$periodCondition}
    ORDER BY e.data_prevista ASC");
    $sqlAtrasados->execute($paramsPeriodo);
    $atrasados = $sqlAtrasados->fetchAll(PDO::FETCH_ASSOC);
    $totalAtrasados = count($atrasados);

    $sqlTopLivros = $pdo->prepare("SELECT COALESCE(li.titulo, 'Sem título') AS livro, COUNT(*) AS total
    FROM emprestimo e
    LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
    WHERE 1=1 {$periodCondition}
    GROUP BY li.id_livro, li.titulo
    ORDER BY total DESC LIMIT 5");
    $sqlTopLivros->execute($paramsPeriodo);
    $topLivros = $sqlTopLivros->fetchAll(PDO::FETCH_ASSOC);

    $sqlTopReservados = $pdo->prepare("SELECT COALESCE(li.titulo, 'Sem título') AS livro, COUNT(*) AS total
    FROM reserva r
    LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro
    WHERE 1=1 {$reservaPeriodCondition}
    GROUP BY li.id_livro, li.titulo
    ORDER BY total DESC LIMIT 5");
    $sqlTopReservados->execute($paramsPeriodo);
    $topReservados = $sqlTopReservados->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $ex) {
    $emprestimosPeriodo = 0;
    $reservasPeriodo = 0;
    $atrasados = [];
    $totalAtrasados = 0;
    $topLivros = [];
    $topReservados = [];
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Biblioteca Pandora</title>

    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/relatorios.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/relatorios.svg" alt="" class="page-title-icon">
                    <h1>Relatórios</h1>
                </div>
                <p>Resumo detalhado de desempenho, atrasados e livros mais emprestados.</p>
            </div>
        </header>

        <!-- KPIs -->
        <div class="relatorios-grid">
            <div class="report-card">
                <span class="report-label">Total de Livros</span>
                <span class="report-value"><?php echo (int)$totalLivros; ?></span>
                <span class="report-note">Livros registados no sistema</span>
            </div>
            <div class="report-card">
                <span class="report-label">Total de Leitores</span>
                <span class="report-value"><?php echo (int)$totalLeitores; ?></span>
                <span class="report-note">Leitores cadastrados</span>
            </div>
            <div class="report-card">
                <span class="report-label">Empréstimos no período</span>
                <span class="report-value"><?php echo $emprestimosPeriodo; ?></span>
                <span class="report-note">Total dentro do período filtrado</span>
            </div>
            <div class="report-card">
                <span class="report-label">Reservas no período</span>
                <span class="report-value"><?php echo $reservasPeriodo; ?></span>
                <span class="report-note">Reservas realizadas no período</span>
            </div>
            <div class="report-card">
                <span class="report-label">Atrasados</span>
                <span class="report-value" style="color: var(--danger);"><?php echo $totalAtrasados; ?></span>
                <span class="report-note">Devoluções em atraso</span>
            </div>
        </div>

        <!-- FILTRO -->
        <div class="filter-card">
            <strong>Filtrar por período</strong>
            <form method="GET" class="filter-form">
                <input
                    class="filter-input"
                    type="date"
                    name="data_inicial"
                    value="<?php echo htmlspecialchars($dataInicial); ?>"
                >
                <span class="filter-divider">até</span>
                <input
                    class="filter-input"
                    type="date"
                    name="data_final"
                    value="<?php echo htmlspecialchars($dataFinal); ?>"
                >
                <button type="submit" class="btn-primary">Aplicar</button>
                <?php if ($dataInicial !== '' || $dataFinal !== ''): ?>
                    <a href="relatorios.php" class="btn-secondary">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABELA ATRASADOS -->
        <div class="report-section" style="margin-bottom: 20px;">
            <div class="report-card-table">
                <span class="report-label">Empréstimos atrasados no período</span>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Livro</th>
                            <th>Leitor</th>
                            <th>Data Empréstimo</th>
                            <th>Previsão</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($atrasados) > 0): ?>
                            <?php foreach ($atrasados as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['id_emprestimo']); ?></td>
                                    <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                    <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['data_emprestimo'] ?? 'now')); ?></td>
                                    <td class="data-atrasada"><?php echo date('d/m/Y', strtotime($item['data_prevista'] ?? 'now')); ?></td>
                                    <td><span class="badge-status atrasado">Atrasado</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="table-empty">Nenhum empréstimo atrasado encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP LIVROS EMPRESTADOS E RESERVADOS -->
        <div class="report-section two-col">

            <div class="report-card-table">
                <span class="report-label">Top livros mais emprestados</span>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Livro</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($topLivros) > 0): ?>
                            <?php foreach ($topLivros as $i => $item): ?>
                                <tr>
                                    <td><span class="rank-num"><?php echo $i + 1; ?></span></td>
                                    <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                    <td><strong><?php echo (int)$item['total']; ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="table-empty">Sem dados para o período.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-card-table">
                <span class="report-label">Top livros mais reservados</span>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Livro</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($topReservados) > 0): ?>
                            <?php foreach ($topReservados as $i => $item): ?>
                                <tr>
                                    <td><span class="rank-num"><?php echo $i + 1; ?></span></td>
                                    <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                    <td><strong><?php echo (int)$item['total']; ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="table-empty">Sem reservas no período.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</div>
</body>
</html>

    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Relatórios 📊</h1>
                    <p>Resumo detalhado de desempenho, atrasados e livros mais emprestados.</p>
                </div>
            </header>

            <div class="relatorios-grid">
                <div class="report-card">
                    <span class="report-label">Total de Livros</span>
                    <span class="report-value"><?php echo (int)$totalLivros; ?></span>
                    <span class="report-note">Livros registrados no sistema</span>
                </div>
                <div class="report-card">
                    <span class="report-label">Total de Leitores</span>
                    <span class="report-value"><?php echo (int)$totalLeitores; ?></span>
                    <span class="report-note">Leitores cadastrados</span>
                </div>
                <div class="report-card">
                    <span class="report-label">Empréstimos no período</span>
                    <span class="report-value"><?php echo $emprestimosPeriodo; ?></span>
                    <span class="report-note">Total dentro do período filtrado.</span>
                </div>
                <div class="report-card">
                    <span class="report-label">Reservas no período</span>
                    <span class="report-value"><?php echo $reservasPeriodo; ?></span>
                    <span class="report-note">Reservas realizadas no período.</span>
                </div>
                <div class="report-card">
                    <span class="report-label">Atrasados</span>
                    <span class="report-value"><?php echo $totalAtrasados; ?></span>
                    <span class="report-note">Empréstimos ativos com devolução em atraso.</span>
                </div>
            </div>

            <div class="mensagem-info">
                <strong>Filtrar por período</strong>
                <div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; width:100%; align-items:center;">
                        <input type="date" name="data_inicial" value="<?php echo htmlspecialchars($dataInicial); ?>" style="padding:10px; border:1px solid #e5e7eb; border-radius:10px; min-width:170px;">
                        <input type="date" name="data_final" value="<?php echo htmlspecialchars($dataFinal); ?>" style="padding:10px; border:1px solid #e5e7eb; border-radius:10px; min-width:170px;">
                        <button type="submit" class="btn-primary" style="padding:10px 16px;">Aplicar</button>
                        <?php if ($dataInicial !== '' || $dataFinal !== ''): ?>
                            <a href="relatorios.php" class="btn-secondary" style="padding:10px 16px; text-decoration:none;">Limpar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="report-section">
                <div class="report-card">
                    <div class="report-label">Atrasados no período</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>ID Empréstimo</th>
                                <th>Livro</th>
                                <th>Leitor</th>
                                <th>Data Empréstimo</th>
                                <th>Previsão</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($atrasados) > 0): ?>
                                <?php foreach ($atrasados as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['id_emprestimo']); ?></td>
                                        <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                        <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($item['data_emprestimo'] ?? 'now'))); ?></td>
                                        <td style="color:#991b1b; font-weight:700;"><?php echo htmlspecialchars(date('d/m/Y', strtotime($item['data_prevista'] ?? 'now'))); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($item['status'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:18px; color:#6b7280;">Nenhum empréstimo atrasado encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="report-card">
                    <div class="report-label">Top livros mais emprestados</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($topLivros) > 0): ?>
                                <?php foreach ($topLivros as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                        <td><?php echo (int)$item['total']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align:center; padding:18px; color:#6b7280;">Sem dados para o período selecionado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="report-section">
                <div class="report-card">
                    <div class="report-label">Top livros mais reservados</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($topReservados) > 0): ?>
                                <?php foreach ($topReservados as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['livro']); ?></td>
                                        <td><?php echo (int)$item['total']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align:center; padding:18px; color:#6b7280;">Sem reservas no período selecionado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

