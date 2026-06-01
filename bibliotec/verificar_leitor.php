<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/common.php';

$leitorSelecionado = null;
$emprestimos = [];
$pendencias = [];
$atrasados = [];

// Se há ID de leitor na URL
if (isset($_GET['id_leitor'])) {
    try {
        // Buscar dados do leitor
        $sqlLeitor = $pdo->prepare("SELECT * FROM leitor WHERE id = ?");
        $sqlLeitor->execute([(int)$_GET['id_leitor']]);
        $leitorSelecionado = $sqlLeitor->fetch(PDO::FETCH_ASSOC);

        if ($leitorSelecionado) {
            // Buscar empréstimos do leitor
            $sqlEmprestimos = $pdo->prepare("
                SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, li.titulo, d.data_devolucao
                FROM emprestimo e
                LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                WHERE e.id_emprestimo_leitor = ?
                ORDER BY e.data_emprestimo DESC
            ");
            $sqlEmprestimos->execute([(int)$_GET['id_leitor']]);
            $emprestimos = $sqlEmprestimos->fetchAll(PDO::FETCH_ASSOC);

            // Buscar pendências (empréstimos não devolvidos)
            $sqlPendencias = $pdo->prepare("
                SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, li.titulo,
                       DATEDIFF(CURDATE(), e.data_prevista) as dias_atraso
                FROM emprestimo e
                LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL
                ORDER BY e.data_prevista ASC
            ");
            $sqlPendencias->execute([(int)$_GET['id_leitor']]);
            $pendencias = $sqlPendencias->fetchAll(PDO::FETCH_ASSOC);

            // Buscar atrasados
            $sqlAtrasados = $pdo->prepare("
                SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, li.titulo,
                       DATEDIFF(CURDATE(), e.data_prevista) as dias_atraso,
                       DATEDIFF(CURDATE(), e.data_prevista) * 0.50 as multa_estimada
                FROM emprestimo e
                LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL 
                  AND e.data_prevista < CURDATE()
                ORDER BY e.data_prevista ASC
            ");
            $sqlAtrasados->execute([(int)$_GET['id_leitor']]);
            $atrasados = $sqlAtrasados->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Silenciar erro
    }
}

// Buscar lista de leitores
$leitores = [];
try {
    $sql = $pdo->prepare("SELECT id, nome, email FROM leitor ORDER BY nome");
    $sql->execute();
    $leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $leitores = [];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Leitor - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/leitores.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .leitor-selector {
            background: var(--white);
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin: 32px;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 14px;
        }

        .form-group select {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 14px;
        }

        .leitor-info {
            background: var(--white);
            border-radius: 8px;
            padding: 24px;
            margin: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
        }

        .leitor-info h2 {
            color: var(--dark);
            margin-bottom: 16px;
            font-size: 20px;
        }

        .leitor-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .detail-item {
            padding: 12px;
            background: var(--gray-50);
            border-radius: 6px;
            border-left: 3px solid var(--primary);
        }

        .detail-label {
            font-size: 12px;
            color: var(--gray-500);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            color: var(--gray-800);
            font-weight: 500;
        }

        .status-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 24px 0;
            padding: 16px;
            background: var(--gray-50);
            border-radius: 6px;
        }

        .status-box {
            text-align: center;
            padding: 16px;
            background: var(--white);
            border-radius: 6px;
            border-left: 4px solid;
        }

        .status-box.success {
            border-left-color: var(--secondary);
        }

        .status-box.warning {
            border-left-color: var(--warning);
        }

        .status-box.danger {
            border-left-color: var(--danger);
        }

        .status-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }

        .status-label {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .section-card {
            background: var(--white);
            border-radius: 8px;
            padding: 24px;
            margin: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-ativo {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .badge-atrasado {
            background: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
        }

        .badge-devolvido {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: var(--gray-50);
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--gray-200);
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
        }

        table tbody tr:hover {
            background: var(--gray-50);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-500);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/users.svg" alt="" class="page-title-icon">
                    <h1>Verificar Leitor</h1>
                </div>
                <p>Consulte informações, pendências e atrasos do leitor</p>
            </div>
        </header>

        <!-- Seletor de Leitor -->
        <div class="leitor-selector">
            <form method="GET">
                <div class="form-group">
                    <label for="id_leitor">Selecione o Leitor</label>
                    <select id="id_leitor" name="id_leitor" onchange="this.form.submit()">
                        <option value="">-- Escolha um leitor --</option>
                        <?php foreach ($leitores as $leitor): ?>
                            <option value="<?php echo (int)$leitor['id']; ?>" <?php echo isset($_GET['id_leitor']) && $_GET['id_leitor'] == $leitor['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($leitor['nome']); ?> (<?php echo htmlspecialchars($leitor['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($leitorSelecionado): ?>
            <!-- Informações do Leitor -->
            <div class="leitor-info">
                <h2><?php echo htmlspecialchars($leitorSelecionado['nome']); ?></h2>
                <div class="leitor-details">
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($leitorSelecionado['email']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Telefone</div>
                        <div class="detail-value"><?php echo htmlspecialchars($leitorSelecionado['numero_telefone'] ?? '—'); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">NIF</div>
                        <div class="detail-value"><?php echo htmlspecialchars($leitorSelecionado['nif'] ?? '—'); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Data de Cadastro</div>
                        <div class="detail-value"><?php echo date('d/m/Y', strtotime($leitorSelecionado['data_leitor'])); ?></div>
                    </div>
                </div>
            </div>

            <!-- Resumo de Status -->
            <div class="status-row">
                <div class="status-box success">
                    <div class="status-number"><?php echo count($emprestimos); ?></div>
                    <div class="status-label">Total de Empréstimos</div>
                </div>
                <div class="status-box <?php echo count($pendencias) > 0 ? 'warning' : 'success'; ?>">
                    <div class="status-number"><?php echo count($pendencias); ?></div>
                    <div class="status-label">Pendências</div>
                </div>
                <div class="status-box <?php echo count($atrasados) > 0 ? 'danger' : 'success'; ?>">
                    <div class="status-number"><?php echo count($atrasados); ?></div>
                    <div class="status-label">Atrasados</div>
                </div>
            </div>

            <!-- Empréstimos Atrasados -->
            <?php if (count($atrasados) > 0): ?>
                <div class="section-card">
                    <div class="section-title">
                        ⚠️ Empréstimos Atrasados
                        <span class="badge badge-atrasado"><?php echo count($atrasados); ?></span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Data Prevista</th>
                                <th>Dias de Atraso</th>
                                <th>Multa Estimada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atrasados as $atraso): ?>
                                <tr style="background: rgba(239, 68, 68, 0.05);">
                                    <td><?php echo htmlspecialchars($atraso['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($atraso['data_prevista'])); ?></td>
                                    <td style="color: var(--danger); font-weight: 600;"><?php echo $atraso['dias_atraso']; ?> dias</td>
                                    <td>€<?php echo number_format($atraso['multa_estimada'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Empréstimos Ativos/Pendentes -->
            <?php if (count($pendencias) > count($atrasados)): ?>
                <div class="section-card">
                    <div class="section-title">
                        📖 Empréstimos Ativos
                        <span class="badge badge-ativo"><?php echo count($pendencias) - count($atrasados); ?></span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Data do Empréstimo</th>
                                <th>Data Prevista de Devolução</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendencias as $pend):
                                $atrasado_check = $pend['dias_atraso'] > 0;
                            ?>
                                <tr <?php echo $atrasado_check ? 'style="background: rgba(239, 68, 68, 0.05);"' : ''; ?>>
                                    <td><?php echo htmlspecialchars($pend['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($pend['data_emprestimo'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($pend['data_prevista'])); ?></td>
                                    <td>
                                        <?php if ($atrasado_check): ?>
                                            <span class="badge badge-atrasado">Atrasado</span>
                                        <?php else: ?>
                                            <span class="badge badge-ativo">Ativo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Histórico de Empréstimos Devolvidos -->
            <?php 
                $devolvidos = array_filter($emprestimos, function($e) { return !empty($e['data_devolucao']); });
                if (count($devolvidos) > 0):
            ?>
                <div class="section-card">
                    <div class="section-title">
                        ✓ Empréstimos Devolvidos
                        <span class="badge badge-devolvido"><?php echo count($devolvidos); ?></span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th>Data do Empréstimo</th>
                                <th>Data de Devolução</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($devolvidos as $dev): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dev['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($dev['data_emprestimo'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($dev['data_devolucao'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (empty($emprestimos)): ?>
                <div class="section-card">
                    <div class="empty-state">
                        <div class="empty-state-icon">📚</div>
                        <p>Este leitor não possui empréstimos registados.</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>
</div>
</body>
</html>
