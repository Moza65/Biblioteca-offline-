<?php
require_once "../config.php";
<<<<<<< HEAD
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
=======

require_once __DIR__ . '/common.php';

$sql = $pdo->prepare("SELECT e.*, li.titulo as titulo_livro, d.data_devolucao FROM emprestimo e 
                     LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro 
                     LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                     ORDER BY e.data_emprestimo DESC");
$sql->execute();
$emprestimos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
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
=======
    <title>Gerenciar Empréstimos - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .emprestimos-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .emprestimos-table th { background-color: #f3f4f6; padding: 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
        .emprestimos-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .emprestimos-table tr:hover { background-color: #f9fafb; }
        .status { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status.ativo { background-color: #d1fae5; color: #065f46; }
        .status.devolvido { background-color: #dbeafe; color: #0c4a6e; }
        .status.atrasado { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Gerenciar Empréstimos 📖</h1>
                    <p>Visualize todos os empréstimos ativos e concluídos</p>
                </div>
            </header>
            <table class="emprestimos-table">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Livro</th>
                        <th>Data do Empréstimo</th>
                        <th>Previsão de Devolução</th>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
<<<<<<< HEAD
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
=======
                    <?php if (count($emprestimos) > 0): ?>
                        <?php foreach ($emprestimos as $emp): ?>
                            <tr>
                                <td hidden><?php echo htmlspecialchars($emp['id_emprestimo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'] ?? 'now')); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['data_prevista'] ?? 'now')); ?></td>
                                <td>
                                    <?php
                                        if (!empty($emp['data_devolucao'])) {
                                            $status = 'Devolvido';
                                            $class_status = 'devolvido';
                                        } elseif (strtotime($emp['data_prevista'] ?? 'now') < time()) {
                                            $status = 'Atrasado';
                                            $class_status = 'atrasado';
                                        } else {
                                            $status = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                            $class_status = strtolower($status);
                                        }
                                    ?>
                                    <span class="status <?php echo $class_status; ?>"><?php echo htmlspecialchars($status); ?></span>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
<<<<<<< HEAD
                            <td colspan="5" class="table-empty">Nenhum empréstimo registado.</td>
=======
                            <td colspan="5" style="text-align: center; padding: 30px;">Nenhum empréstimo registrado</td>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
<<<<<<< HEAD
        </div>

    </main>
</div>
</body>
</html>
=======
        </main>
    </div>
</body>
</html>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
