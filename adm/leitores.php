<?php
require_once "../config.php";
require_once __DIR__ . '/common.php';

$sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
$sql->execute();
$leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
$total    = count($leitores);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                                <td><?php echo date('d/m/Y', strtotime($leitor['data_leitor'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="table-empty">Nenhum leitor cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
</body>
</html>