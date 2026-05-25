<?php
require_once "../Buscas/buscarDados.php";

session_start();

// Verifica se existe sessão
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verifica se é bibliotecário
if ($_SESSION['usuario']['tipo_usuario'] != 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$callClass = new BuscarDados();

$put = ($callClass->QuantidadeLivro());
$putEmprestimo = ($callClass->Quantidadeemprestimo());
$putLeitores = ($callClass->QuantidadeLeitores());
$putReserva = ($callClass->QuantidadeReserva());
//$putSearch = $callClass->ShowSerach();
require_once __DIR__ . '/common.php';
if(isset($_POST["pesquisa"]) and !empty($_POST["campoPesquisa"])){
    $pesquisaEncontrada = $callClass->ShowSerach($_POST["campoPesquisa"]);
}
$total_atrasados = 0;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca Pandora</title>

    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Olá, Administrador 👋</h1>
                    <p>Bem-vindo ao sistema de gestão da biblioteca.</p>
                </div>
                <div class="topbar-actions">
                    <div class="search-bar">
                        <form action="" method="post" >
                            <input type="text" name="campoPesquisa" placeholder="Buscar livros...">
                            <button name="pesquisar" type="submit">
                                <img src="../asset/icones/search.svg" class="icon" alt="">
                            </button> 
                        </form>
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
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon blue">
                        <img src="../asset/icones/book-copy.svg" class="icon big-icon" alt="">
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-title">Total de Livros</span>
                        <span class="kpi-value"><?php if(isset($put) && is_array($put)){ echo $put["count(id_livro)"]; } ?></span>
                        <span class="kpi-trend positive">+32 este mês</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <img src="../asset/icones/users.svg" class="icon big-icon" alt="">
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-title">Leitores Ativos</span>
                        <span class="kpi-value"><?php if(isset($putLeitores) && is_array($putLeitores)){ echo $putLeitores["count(id)"]; } ?></span>
                        <span class="kpi-trend positive">+18 este mês</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange">
                        <img src="../asset/icones/arrow-right-left.svg" class="icon big-icon" alt="">
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-title">Empréstimos</span>
                        <span class="kpi-value"><?php if(isset($putEmprestimo) && is_array($putEmprestimo)){ echo $putEmprestimo["count(id_emprestimo)"]; } ?></span>
                        <span class="kpi-trend positive">+12 este mês</span>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon red">
                        <img src="../asset/icones/calendar.svg" class="icon big-icon" alt="">
                    </div>
                    <div class="kpi-details">
                        <span class="kpi-title">Reservas</span>
                        <span class="kpi-value"><?php if(isset($putReserva) && is_array($putReserva)){ echo $putReserva["count(id_reserva)"]; } ?></span>
                        <span class="kpi-trend positive">+2 este mês</span>
                    </div>
                </div>
            </div>
            <?php if ($total_atrasados > 0): ?>
            <div style="background-color: #fee2e2; border: 1px solid #fca5a5; padding: 20px; border-radius: 8px; margin-top: 30px;">
                <div style="display: flex; align-items: center; gap: 15px; color: #991b1b;">
                    <span style="font-size: 24px;">⚠️</span>
                    <div>
                        <strong><?php echo $total_atrasados; ?> Empréstimo(s) Atrasado(s)</strong>
                        <p style="margin: 5px 0 0 0; font-size: 14px;">Clique <a href="emprestimos.php" style="color: #dc2626; text-decoration: underline;">aqui</a> para visualizar os detalhes.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php
            if(isset($pesquisaEncontrada) and !empty($pesquisaEncontrada) and is_array($pesquisaEncontrada)){
                if(isset($pesquisaEncontrada["0"]["id_emprestimo"])){ ?>
                    //AQUÍ O USUÁRIO PESQUISOU EMPRÉSTIMO
                    <h2>Informações da pesquisa </h2>
                    <table class="emprestimos-table">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Livro</th>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Livro</th>
                        <th>Livro</th>
                        <th>Livro</th>
                        <th>Data do Empréstimo</th>
                        <th>Previsão de Devolução</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pesquisaEncontrada) > 0): ?>
                        <?php foreach ($pesquisaEncontrada as $emp): ?>
                            <tr>
                                <td hidden><?php echo htmlspecialchars($emp['id_emprestimo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'] ?? 'now')); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($emp['data_prevista'] ?? 'now')); ?></td>
                                <td>
                                    <?php
                                        if (!empty($emp['data_devolucao'])) {
                                            $status = 'Devolvido';
                                            $class_status = 'devolvido';
                                        } elseif (strtotime($emp['data_prevista'] ?? 'now') < time()) {
                                            $status = 'Atrasado';
                                            $class_status = 'atrasado';
                                        } else {
                                            $status = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                            $class_status = strtolower($status);
                                        }
                                    ?>
                                    <span class="status <?php echo $class_status; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">Nenhum empréstimo registrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
               <?php }
            }
            ?>
        </main>
    </div>
</body>
</html>
