<?php
session_start();

require_once "../config.php";

$sql = "SELECT * FROM usuario
        WHERE tipo_usuario = 'bibliotecario'
        ORDER BY nome ASC";

$stmt = $pdo->query($sql);

$bibliotecarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Bibliotecários</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/bibliotecarios.css">
</head>
<body>

<div class="dashboard-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <header class="topbar">

            <div class="welcome-text">
                <h1>Bibliotecários</h1>
                <p>Gerencie os bibliotecários do sistema.</p>
            </div>

            <a href="cadastrar_bibliotecario.php" class="novo-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Novo Bibliotecário
            </a>

        </header>

        <div class="table-card">

            <div class="table-header">
                <h2>Lista de Bibliotecários</h2>
                <span style="font-size:13px;color:var(--gray-500);font-weight:500;">
                    <?php echo count($bibliotecarios); ?> registos
                </span>
            </div>

            <?php if (empty($bibliotecarios)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--gray-400)" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5.196-3.796M9 20H4v-2a4 4 0 015.196-3.796M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0zM3 10a3 3 0 116 0 3 3 0 01-6 0z"/>
                    </svg>
                    <p>Nenhum bibliotecário encontrado.</p>
                </div>
            <?php else: ?>
                <table class="bibliotecario-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Contacto</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach($bibliotecarios as $bibliotecario): ?>

                        <tr>

                            <td><?php echo $bibliotecario['id_usuario']; ?></td>

                            <td><?php echo htmlspecialchars($bibliotecario['nome']); ?></td>

                            <td><?php echo htmlspecialchars($bibliotecario['email']); ?></td>

                            <td><?php echo htmlspecialchars($bibliotecario['contacto']); ?></td>

                            <td>
                                <button class="editar-btn" onclick="window.location='editar_bibliotecario.php?id=<?php echo $bibliotecario['id_usuario']; ?>'">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Editar
                                </button>

                                <button class="excluir-btn" onclick="confirmarExclusao(<?php echo $bibliotecario['id_usuario']; ?>, '<?php echo htmlspecialchars($bibliotecario['nome']); ?>')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                    </svg>
                                    Excluir
                                </button>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>
            <?php endif; ?>

        </div>

    </main>

</div>

<script>
function confirmarExclusao(id, nome) {
    if (confirm(`Tem a certeza que deseja excluir o bibliotecário "${nome}"?`)) {
        window.location = `excluir_bibliotecario.php?id=${id}`;
    }
}
</script>

</body>
</html>