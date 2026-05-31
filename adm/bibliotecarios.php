<?php
session_start();

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

        <h1>Bibliotecários</h1>
        <p>Lista de bibliotecários cadastrados.</p>

    </main>

</div>

</body>
</html>