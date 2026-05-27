<?php
require_once "../Buscas/buscarDados.php";

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit();
}

if ($_SESSION['usuario']['tipo_usuario'] != 'bibliotecario') {
    header("Location: ../login/login.php");
    exit();
}

$callClass = new BuscarDados();

$put           = $callClass->QuantidadeLivro();
$putEmprestimo = $callClass->Quantidadeemprestimo();
$putLeitores   = $callClass->QuantidadeLeitores();
$putReserva    = $callClass->QuantidadeReserva();

require_once __DIR__ . '/common.php';

$pesquisaEncontrada = [];
if (isset($_POST['pesquisar']) && !empty($_POST['campoPesquisa'])) {
    $pesquisaEncontrada = $callClass->ShowSerach($_POST['campoPesquisa']);
}

$total_atrasados = 0;
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Bibliotecário - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/bibliotec.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <header class="topbar">
            <div class="welcome-text">
                <h1>Olá, <?php echo htmlspecialchars($usuario['nome']); ?>! 👋</h1>
                <p>Painel do Bibliotecário - Gerencie as operações da biblioteca</p>
            </div>
            <div class="topbar-actions">
                <form method="POST" class="search-bar">
                    <img src="../asset/icones/search.svg" class="icon" alt="">
                    <input type="text" name="campoPesquisa" placeholder="Buscar livro, leitor...">
                    <button name="pesquisar" type="submit" style="background:none;border:none;cursor:pointer;display:flex;align-items:center;"></button>
                </form>
                <button class="action-btn">
                    <img src="../asset/icones/bell.svg" class="icon" alt="">
                    <span class="badge">3</span>
                </button>
            </div>
        </header>

        <!-- KPIs (Layout 2x2 para bibliotecário) -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon blue">
                    <img src="../asset/icones/book-copy.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Livros em Catálogo</span>
                    <span class="kpi-value"><?php echo isset($put) && is_array($put) ? $put["count(id_livro)"] : 0; ?></span>
                    <span class="kpi-trend positive">Disponíveis para empréstimo</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <img src="../asset/icones/users.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Leitores Registrados</span>
                    <span class="kpi-value"><?php echo isset($putLeitores) && is_array($putLeitores) ? $putLeitores["count(id)"] : 0; ?></span>
                    <span class="kpi-trend positive">Usuários ativos no sistema</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon orange">
                    <img src="../asset/icones/arrow-right-left.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Empréstimos Ativos</span>
                    <span class="kpi-value"><?php echo isset($putEmprestimo) && is_array($putEmprestimo) ? $putEmprestimo["count(id_emprestimo)"] : 0; ?></span>
                    <span class="kpi-trend positive">Em circulação no momento</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">
                    <img src="../asset/icones/calendar.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Reservas Pendentes</span>
                    <span class="kpi-value"><?php echo isset($putReserva) && is_array($putReserva) ? $putReserva["count(id_reserva)"] : 0; ?></span>
                    <span class="kpi-trend positive">Aguardando disponibilidade</span>
                </div>
            </div>
        </div>

        <!-- ALERTA ATRASADOS -->
        <?php if ($total_atrasados > 0): ?>
            <div class="alert erro" style="margin-top:0;">
                ⚠️ <strong><?php echo $total_atrasados; ?> Empréstimo(s) Atrasado(s)</strong>
                — <a href="emprestimos.php" style="color:var(--danger);">Ver detalhes e enviar notificações</a>
            </div>
        <?php endif; ?>

        <!-- AÇÕES RÁPIDAS DO BIBLIOTECÁRIO -->
        <div style="padding: 32px; padding-top: 0;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 18px;">Operações Frequentes</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                <a href="registrar_emprestimo.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📤</div>
                        <div style="font-weight: 600; font-size: 14px;">Registrar Empréstimo</div>
                    </div>
                </a>
                <a href="devolucao.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📥</div>
                        <div style="font-weight: 600; font-size: 14px;">Registrar Devolução</div>
                    </div>
                </a>
                <a href="verificar_leitor.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(79, 172, 254, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">🔍</div>
                        <div style="font-weight: 600; font-size: 14px;">Verificar Leitor</div>
                    </div>
                </a>
                <a href="multas.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(250, 112, 154, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">💰</div>
                        <div style="font-weight: 600; font-size: 14px;">Gestão de Multas</div>
                    </div>
                </a>
                <a href="comprovativo.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(48, 207, 208, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📄</div>
                        <div style="font-weight: 600; font-size: 14px;">Emitir Comprovativo</div>
                    </div>
                </a>
                <a href="livros.php" style="text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; padding: 20px; color: white; text-align: center; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(67, 233, 123, 0.3);">
                        <div style="font-size: 32px; margin-bottom: 8px;">📚</div>
                        <div style="font-weight: 600; font-size: 14px;">Gerenciar Livros</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- RESULTADOS DA PESQUISA -->
        <?php if (!empty($pesquisaEncontrada) && is_array($pesquisaEncontrada)): ?>
            <div class="table-card" style="margin-top:28px;">
                <div class="table-card-header">
                    <h2>Resultados da pesquisa</h2>
                    <span><?php echo count($pesquisaEncontrada); ?> resultado(s)</span>
                </div>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Livro</th>
                            <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Empréstimo</th>
                            <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Previsão</th>
                            <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesquisaEncontrada as $emp): ?>
                            <?php
                                if (!empty($emp['data_devolucao'])) {
                                    $status = 'Devolvido'; $cls = 'devolvido';
                                } elseif (strtotime($emp['data_prevista'] ?? 'now') < time()) {
                                    $status = 'Atrasado'; $cls = 'atrasado';
                                } else {
                                    $status = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                    $cls = strtolower($status);
                                }
                            ?>
                            <tr>
                                <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></td>
                                <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'] ?? 'now')); ?></td>
                                <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo date('d/m/Y', strtotime($emp['data_prevista'] ?? 'now')); ?></td>
                                <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><span class="status <?php echo $cls; ?>"><?php echo $status; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>
</div>
</body>
</html>