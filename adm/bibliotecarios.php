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
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    
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
            + Novo Bibliotecário
        </a>

    </header>

    <div class="table-card">

        <div class="table-header">
            <h2>Lista de Bibliotecários</h2>
        </div>

        <table class="bibliotecario-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th>Ações</th>
                </tr>

            </thead>

            <tbody>

            <?php foreach($bibliotecarios as $bibliotecario): ?>

                <tr>

                    <td>
                        <?php echo $bibliotecario['id_usuario']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($bibliotecario['nome']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($bibliotecario['email']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($bibliotecario['contacto']); ?>
                    </td>

                    <td>

                        <button class="editar-btn">
                            Editar
                        </button>

                        <button class="excluir-btn">
                            Excluir
                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</main>

</div>

</body>
</html>