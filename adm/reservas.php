<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/common.php';

try {
    $sql = $pdo->prepare(
        "SELECT r.*, li.titulo AS titulo_livro
         FROM reserva r
         LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro
         ORDER BY r.data_reserva DESC"
    );
    $sql->execute();
    $reservas = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    $reservas = [];
}

$totalReservas = count($reservas);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/reservas.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/reservas.svg" alt="" class="page-title-icon">
                    <h1>Reservas</h1>
                </div>
                <p>Visualize todas as reservas de livros</p>
            </div>
        </header>

        <div class="table-card">
            <div class="table-card-header">
                <h2>Lista de Reservas</h2>
                <span><?php echo $totalReservas; ?> reserva<?php echo $totalReservas !== 1 ? 's' : ''; ?></span>
            </div>

            <table class="reservas-table">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data da Reserva</th>
                        <th>Prazo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalReservas > 0): ?>
                        <?php foreach ($reservas as $res):
                            $vencida = isset($res['prazo_reserva']) && strtotime($res['prazo_reserva']) < time();
                            $status = $vencida ? 'Vencida' : 'Ativa';
                            $cssClass = $vencida ? 'vencida' : 'ativa';
                        ?>
                            <tr>
                                <td hidden><?php echo htmlspecialchars($res['id_reserva'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['nome_leitor'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($res['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo isset($res['data_reserva']) ? date('d/m/Y', strtotime($res['data_reserva'])) : '—'; ?></td>
                                <td><?php echo isset($res['prazo_reserva']) ? date('d/m/Y', strtotime($res['prazo_reserva'])) : '—'; ?></td>
                                <td><span class="badge-status <?php echo $cssClass; ?>"><?php echo $status; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="table-empty">Nenhuma reserva registada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

