<?php
require_once "../config.php";

require_once __DIR__ . '/common.php';

$sql = $pdo->prepare("SELECT e.*, li.titulo as titulo_livro, d.data_devolucao FROM emprestimo e 
                     LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro 
                     LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                     ORDER BY e.data_emprestimo DESC");
$sql->execute();
$emprestimos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">Nenhum empréstimo registrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
