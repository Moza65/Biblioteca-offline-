<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

<<<<<<< HEAD
$sql = $pdo->prepare("
    SELECT r.*, li.titulo AS titulo_livro
    FROM reserva r
    LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro
    ORDER BY r.data_reserva DESC
");
$sql->execute();
$reservas = $sql->fetchAll(PDO::FETCH_ASSOC);
$totalReservas = count($reservas);
?>
=======
$sql = $pdo->prepare("SELECT r.*, li.titulo as titulo_livro FROM reserva r 
                     LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro 
                     ORDER BY r.data_reserva DESC");
$sql->execute();
$reservas = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Reservas - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/reservas.css">
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
                    <img src="../asset/icones/reservas.svg" alt="" class="page-title-icon">
                    <h1>Reservas</h1>
                </div>
                <p>Visualize todas as reservas de livros</p>
            </div>
        </header>

        <!-- TABELA -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Lista de Reservas</h2>
                <span><?php echo $totalReservas; ?> reserva<?php echo $totalReservas !== 1 ? 's' : ''; ?> registada<?php echo $totalReservas !== 1 ? 's' : ''; ?></span>
            </div>

=======
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
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
            <table class="reservas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data da Reserva</th>
<<<<<<< HEAD
                        <th>Prazo</th>
=======
                        <th>Prazo da Reserva</th>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
<<<<<<< HEAD
                    <?php if ($totalReservas > 0): ?>
                        <?php foreach ($reservas as $res):
                            $vencida  = strtotime($res['prazo_reserva'] ?? 'now') < time();
                            $status   = $vencida ? 'Vencida' : 'Ativa';
                            $cssClass = $vencida ? 'vencida' : 'ativa';
                        ?>
                            <tr>
                                <td class="col-id">#<?php echo htmlspecialchars($res['id_reserva'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['nome_leitor'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($res['data_reserva'] ?? 'now')); ?></td>
                                <td class="<?php echo $vencida ? 'prazo-vencido' : ''; ?>">
                                    <?php echo date('d/m/Y', strtotime($res['prazo_reserva'] ?? 'now')); ?>
                                </td>
                                <td>
                                    <span class="badge-status <?php echo $cssClass; ?>"><?php echo $status; ?></span>
=======
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
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
<<<<<<< HEAD
                            <td colspan="6" class="table-empty">
                                <span class="table-empty-icon">📋</span>
                                Nenhuma reserva registada
                            </td>
=======
                            <td colspan="6" style="text-align: center; padding: 30px;">Nenhuma reserva registrada</td>
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
