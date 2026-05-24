<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

$sql = $pdo->prepare("
    SELECT e.*, li.titulo AS titulo_livro, d.data_devolucao
    FROM emprestimo e
    LEFT JOIN livro li     ON e.fk_Livro_id_livro = li.id_livro
    LEFT JOIN devolucao d  ON e.id_emprestimo = d.id_emprestimo
    ORDER BY e.data_emprestimo DESC
");
$sql->execute();
$emprestimos = $sql->fetchAll(PDO::FETCH_ASSOC);
$total = count($emprestimos);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimos - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/emprestimos.css">
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
                    <img src="../asset/icones/book-open.svg" alt="" class="page-title-icon">
                    <h1>Empréstimos</h1>
                </div>
                <p>Visualize todos os empréstimos ativos e concluídos</p>
            </div>
        </header>

        <!-- TABELA -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Lista de Empréstimos</h2>
                <span><?php echo $total; ?> registo<?php echo $total !== 1 ? 's' : ''; ?></span>
            </div>
            <table class="emprestimos-table">
                <thead>
                    <tr>
                        <th>Livro</th>
                        <th>Data do Empréstimo</th>
                        <th>Previsão de Devolução</th>
                        <th>Data de Devolução</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total > 0): ?>
                        <?php foreach ($emprestimos as $emp):
                            $devolvido  = !empty($emp['data_devolucao']);
                            $atrasado   = !$devolvido && strtotime($emp['data_prevista'] ?? 'now') < time();

                            if ($devolvido) {
                                $status    = 'Devolvido';
                                $cssClass  = 'devolvido';
                            } elseif ($atrasado) {
                                $status    = 'Atrasado';
                                $cssClass  = 'atrasado';
                            } else {
                                $status    = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                $cssClass  = strtolower($status);
                            }
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'] ?? 'now')); ?></td>
                                <td class="<?php echo $atrasado ? 'data-atrasada' : ''; ?>">
                                    <?php echo date('d/m/Y', strtotime($emp['data_prevista'] ?? 'now')); ?>
                                </td>
                                <td>
                                    <?php echo $devolvido ? date('d/m/Y', strtotime($emp['data_devolucao'])) : '—'; ?>
                                </td>
                                <td>
                                    <span class="badge-status <?php echo $cssClass; ?>"><?php echo $status; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="table-empty">Nenhum empréstimo registado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
</body>
</html>