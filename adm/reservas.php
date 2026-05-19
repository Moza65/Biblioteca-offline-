<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

$sql = $pdo->prepare("SELECT r.*, li.titulo as titulo_livro FROM reserva r 
                     LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro 
                     ORDER BY r.data_reserva DESC");
$sql->execute();
$reservas = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Reservas - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .reservas-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .reservas-table th { background-color: #f3f4f6; padding: 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
        .reservas-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .reservas-table tr:hover { background-color: #f9fafb; }
        .status { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status.ativo { background-color: #d1fae5; color: #065f46; }
        .status.vencido { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Gerenciar Reservas 🗓️</h1>
                    <p>Visualize todas as reservas de livros</p>
                </div>
            </header>
            <table class="reservas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data da Reserva</th>
                        <th>Prazo da Reserva</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reservas) > 0): ?>
                        <?php foreach ($reservas as $res): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res['id_reserva'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['nome_leitor'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($res['data_reserva'] ?? 'now')); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($res['prazo_reserva'] ?? 'now')); ?></td>
                                <td>
                                    <?php
                                        $status = (strtotime($res['prazo_reserva'] ?? 'now') >= time()) ? 'Ativa' : 'Vencida';
                                        $class_status = strtolower($status);
                                    ?>
                                    <span class="status <?php echo $class_status; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">Nenhuma reserva registrada</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
