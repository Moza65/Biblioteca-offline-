<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

// Buscar todos os leitores
$sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
$sql->execute();
$leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Leitores - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .leitores-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .leitores-table th { background-color: #f3f4f6; padding: 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
        .leitores-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .leitores-table tr:hover { background-color: #f9fafb; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Gerenciar Leitores 👥</h1>
                    <p>Visualize todos os leitores cadastrados</p>
                </div>
            </header>

            <table class="leitores-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>NIF</th>
                        <th>Data de Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($leitores) > 0): ?>
                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td><?php echo $leitor['id']; ?></td>
                                <td><?php echo $leitor['nome']; ?></td>
                                <td><?php echo $leitor['email']; ?></td>
                                <td><?php echo $leitor['numero_telefone']; ?></td>
                                <td><?php echo $leitor['nif']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($leitor['data_leitor'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">Nenhum leitor cadastrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
