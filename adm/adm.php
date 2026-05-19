
<?php
require_once "../Buscas/buscarDados.php";
$callClass = new BuscarDados();
$put = ($callClass->QuantidadeLivro());
$putEmprestimo = ($callClass->Quantidadeemprestimo());
$putLeitores = ($callClass->QuantidadeLeitores());
$putReserva = ($callClass->QuantidadeReserva());

// Inicia a sessão
session_start();
// Verifica se existe usuário logado
if (!isset($_SESSION['usuario'])) {
    // Se não existir sessão, volta para login
    header("Location: ../login/login.php");
      // Encerra o script para evitar que o código abaixo seja executado
    exit();
}
// Guarda os dados do usuário logado
$usuario = $_SESSION['usuario'];

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca Pandora</title>

    <link rel="stylesheet" href="../asset/style/adm.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="dashboard-container">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div class="sidebar-header">
                <div class="logo">

                    <img src="../asset/icones/book-open.svg" class="icon logo-icon" alt="Logo">

                    <div class="logo-text">
                        <span>Pandora</span>
                        <small>Sistema de Gestão</small>
                    </div>

                </div>
            </div>

            <nav class="sidebar-nav">

                <a href="#" class="nav-item active">
                    <img src="../asset/icones/home.svg" class="icon" alt="">
                    <span>Dashboard</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/book-open.svg" class="icon" alt="">
                    <span>Livros</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/user.svg" class="icon" alt="">
                    <span>Autores</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/folder.svg" class="icon" alt="">
                    <span>Categorias</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/users.svg" class="icon" alt="">
                    <span>Usuários</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/arrow-right-left.svg" class="icon" alt="">
                    <span>Empréstimos</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/rotate-ccw.svg" class="icon" alt="">
                    <span>Devoluções</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/calendar.svg" class="icon" alt="">
                    <span>Reservas</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/file-text.svg" class="icon" alt="">
                    <span>Relatórios</span>
                </a>

                <a href="#" class="nav-item">
                    <img src="../asset/icones/settings.svg" class="icon" alt="">
                    <span>Configurações</span>
                </a>

            </nav>

            <!-- FOOTER -->
            <div class="sidebar-footer">

                <div class="user-profile">

                    <div class="avatar">
                        <img src="../asset/icones/user.svg" class="icon white-icon" alt="">
                    </div>

<div class="user-info">
<!-- Mostra o nome do usuário logado -->
    <span class="user-name">
        <?php echo $usuario['email']; ?>
    </span>
<!-- Mostra o email do usuário logado -->
    <span class="user-email">
        <?php echo $usuario['email']; ?>
    </span>

</div>

                    <img src="../asset/icones/chevron-down.svg" class="icon dropdown-icon" alt="">

                </div>

            </div>

        </aside>

        <!-- MAIN -->
        <main class="main-content">

            <header class="topbar">

                <div class="welcome-text">
                    <h1>Olá, Administrador   👋</h1>
                    <p>Bem-vindo ao sistema de gestão da biblioteca.</p>
                </div>

                <div class="topbar-actions">

                    <div class="search-bar">

                        <img src="../asset/icones/search.svg" class="icon" alt="">

                        <input type="text" placeholder="Buscar livros...">

                    </div>

                    <button class="action-btn">

                        <img src="../asset/icones/bell.svg" class="icon" alt="">

                        <span class="badge">3</span>

                    </button>

                    <button class="action-btn">
                        <img src="../asset/icones/settings.svg" class="icon" alt="">
                    </button>

                </div>

            </header>

            <!-- KPI -->
            <div class="kpi-grid">

                <div class="kpi-card">

                    <div class="kpi-icon blue">
                        <img src="../asset/icones/book-copy.svg" class="icon big-icon" alt="">
                    </div>

                    <div class="kpi-details">
                        <span class="kpi-title">Total de Livros</span>
                        <span class="kpi-value"><?php if(isset($put) and is_array($put)){ echo $put["count(id_livro)"]; } ?> </span>
                        <span class="kpi-trend positive">+32 este mês</span>
                    </div>

                </div>

                <div class="kpi-card">

                    <div class="kpi-icon green">
                        <img src="../asset/icones/users.svg" class="icon big-icon" alt="">
                    </div>

                    <div class="kpi-details">
                        <span class="kpi-title">Leitores Ativos</span>
                        <span class="kpi-value"><?php if(isset($putLeitores) and is_array($putLeitores)){ echo $putLeitores["count(id)"]; } ?></span>
                        <span class="kpi-trend positive">+18 este mês</span>
                    </div>

                </div>

                <div class="kpi-card">

                    <div class="kpi-icon orange">
                        <img src="../asset/icones/arrow-right-left.svg" class="icon big-icon" alt="">
                    </div>

                    <div class="kpi-details">
                        <span class="kpi-title">Empréstimos</span>
                        <span class="kpi-value"><?php if(isset($putEmprestimo) and is_array($putEmprestimo)){ echo $putEmprestimo["count(id_emprestimo)"]; } ?></span>
                        <span class="kpi-trend positive">+12 este mês</span>
                    </div>

                </div>

                <div class="kpi-card">

                    <div class="kpi-icon red">
                        <img src="../asset/icones/calendar.svg" class="icon big-icon" alt="">
                    </div>

                    <div class="kpi-details">
                        <span class="kpi-title">Reservas</span>
                        <span class="kpi-value"><?php if(isset($putReserva) and is_array($putReserva)){ echo $putReserva["count(id_reserva)"]; } ?> </span>
                        <span class="kpi-trend positive">+2 este mês</span>
                    </div>

                </div>

            </div>

        </main>

    </div>

</body>
</html>