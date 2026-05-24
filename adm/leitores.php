<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

<<<<<<< HEAD
$sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
$sql->execute();
$leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
$total    = count($leitores);
?>
=======
// Buscar todos os leitores
$sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
$sql->execute();
$leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Leitores - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/leitores.css">
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
                    <img src="../asset/icones/user-cog.svg" alt="" class="page-title-icon">
                    <h1>Leitores</h1>
                </div>
                <p>Visualize todos os leitores cadastrados</p>
            </div>
        </header>

        <!-- TABELA -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Lista de Leitores</h2>
                <span><?php echo $total; ?> leitor<?php echo $total !== 1 ? 'es' : ''; ?> cadastrado<?php echo $total !== 1 ? 's' : ''; ?></span>
            </div>
=======
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

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
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
<<<<<<< HEAD
                    <?php if ($total > 0): ?>
                        <?php foreach ($leitores as $leitor):
                            $iniciais = strtoupper(mb_substr($leitor['nome'] ?? '?', 0, 1));
                        ?>
                            <tr>
                                <td class="col-id">#<?php echo htmlspecialchars($leitor['id']); ?></td>
                                <td>
                                    <div class="leitor-nome-cell">
                                        <span class="leitor-avatar"><?php echo $iniciais; ?></span>
                                        <?php echo htmlspecialchars($leitor['nome']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($leitor['email']); ?></td>
                                <td><?php echo htmlspecialchars($leitor['numero_telefone'] ?? '—'); ?></td>
                                <td class="col-nif"><?php echo htmlspecialchars($leitor['nif'] ?? '—'); ?></td>
=======
                    <?php if (count($leitores) > 0): ?>
                        <?php foreach ($leitores as $leitor): ?>
                            <tr>
                                <td><?php echo $leitor['id']; ?></td>
                                <td><?php echo $leitor['nome']; ?></td>
                                <td><?php echo $leitor['email']; ?></td>
                                <td><?php echo $leitor['numero_telefone']; ?></td>
                                <td><?php echo $leitor['nif']; ?></td>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                                <td><?php echo date('d/m/Y', strtotime($leitor['data_leitor'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
<<<<<<< HEAD
                            <td colspan="6" class="table-empty">Nenhum leitor cadastrado.</td>
=======
                            <td colspan="6" style="text-align: center; padding: 30px;">Nenhum leitor cadastrado</td>
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
