<?php
require_once "../Buscas/buscarDados.php";

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit();
}

if ($_SESSION['usuario']['tipo_usuario'] != 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$callClass = new BuscarDados();

$put           = $callClass->QuantidadeLivro();
$putEmprestimo = $callClass->Quantidadeemprestimo();
$putLeitores   = $callClass->QuantidadeLeitores();
$putReserva    = $callClass->QuantidadeReserva();

require_once __DIR__ . '/common.php';

$pesquisaEncontrada = ['type' => 'empty', 'items' => []];
$searchResults = [];
$searchMessage = '';
$searchTerm = '';

if (isset($_POST['pesquisar'])) {
    $searchTerm = trim($_POST['campoPesquisa'] ?? '');
    if ($searchTerm !== '') {
        $pesquisaEncontrada = $callClass->ShowSerach($searchTerm);
        if (!empty($pesquisaEncontrada['items'])) {
            $searchResults = $pesquisaEncontrada['items'];
        } else {
            if (!empty($pesquisaEncontrada['message'])) {
                $searchMessage = $pesquisaEncontrada['message'];
            } elseif ($pesquisaEncontrada['type'] === 'empty') {
                $searchMessage = 'Digite algum termo para pesquisar.';
            } else {
                $searchMessage = 'Nenhum resultado encontrado.';
            }
        }
    } else {
        $searchMessage = 'Digite algum termo para pesquisar.';
    }
}

function buildSearchUrl($type, $item, $searchTerm) {
    $term = urlencode($searchTerm);
    if ($type === 'livro') {
        return 'livros.php?busca=' . $term;
    }
    if ($type === 'leitor') {
        return !empty($item['id']) ? 'leitores.php?id_leitor=' . (int)$item['id'] : 'leitores.php?busca=' . $term;
    }
    if ($type === 'emprestimo') {
        return !empty($item['id_emprestimo']) ? 'emprestimos.php?id_emprestimo=' . (int)$item['id_emprestimo'] : 'emprestimos.php?busca=' . $term;
    }
    return 'adm.php';
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
    <style>
        .clickable-row { cursor: pointer; }
        .clickable-row:hover { background: rgba(59, 130, 246, 0.06); }
        .result-link { color: inherit; text-decoration: none; }
        .table-card .alert { margin: 0; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <header class="topbar">
            <div class="welcome-text">
                <h1>Olá, Administrador</h1>
                <p>Bem-vindo ao sistema de gestão da biblioteca.</p>
            </div>
            <div class="topbar-actions">
                <form method="POST" class="search-bar">
                    <img src="../asset/icones/search.svg" class="icon" alt="">
                    <input type="text" name="campoPesquisa" placeholder="Buscar livro, leitor, empréstimo..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button name="pesquisar" type="submit" style="background:none;border:none;cursor:pointer;display:flex;align-items:center;"></button>
                </form>
                <button class="action-btn">
                    <img src="../asset/icones/bell.svg" class="icon" alt="">
                    <span class="badge">3</span>
                </button>
                <button class="action-btn">
                    <a href="">
                        <img src="../asset/icones/settings.svg" class="icon" alt="">
                    </a>
                </button>
            </div>
        </header>

        <!-- KPIs -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon blue">
                    <img src="../asset/icones/book-copy.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Total de Livros</span>
                    <span class="kpi-value"><?php echo isset($put) && is_array($put) ? $put["count(id_livro)"] : 0; ?></span>
                    <span class="kpi-trend positive">+32 este mês</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">
                    <img src="../asset/icones/users.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Leitores Ativos</span>
                    <span class="kpi-value"><?php echo isset($putLeitores) && is_array($putLeitores) ? $putLeitores["count(id)"] : 0; ?></span>
                    <span class="kpi-trend positive">+18 este mês</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon orange">
                    <img src="../asset/icones/arrow-right-left.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Empréstimos</span>
                    <span class="kpi-value"><?php echo isset($putEmprestimo) && is_array($putEmprestimo) ? $putEmprestimo["count(id_emprestimo)"] : 0; ?></span>
                    <span class="kpi-trend positive">+12 este mês</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">
                    <img src="../asset/icones/calendar.svg" class="icon big-icon" alt="">
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Reservas</span>
                    <span class="kpi-value"><?php echo isset($putReserva) && is_array($putReserva) ? $putReserva["count(id_reserva)"] : 0; ?></span>
                    <span class="kpi-trend positive">+2 este mês</span>
                </div>
            </div>
        </div>

        <!-- ALERTA ATRASADOS -->
        <?php if ($total_atrasados > 0): ?>
            <div class="alert erro" style="margin-top:24px;">
                ⚠️ <strong><?php echo $total_atrasados; ?> Empréstimo(s) Atrasado(s)</strong>
                — <a href="emprestimos.php" style="color:var(--danger);">Ver detalhes</a>
            </div>
        <?php endif; ?>

        <!-- RESULTADOS DA PESQUISA -->
        <?php if (!empty($searchResults) || $searchMessage): ?>
            <div class="table-card" style="margin-top:28px;">
                <div class="table-card-header">
                    <h2>Resultados da pesquisa</h2>
                    <?php if (!empty($searchResults)): ?>
                        <span><?php echo count($searchResults); ?> resultado(s)</span>
                    <?php else: ?>
                        <span>Nenhum resultado</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($searchResults)): ?>
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
                            <?php foreach ($searchResults as $emp):
                                $rowUrl = buildSearchUrl($pesquisaEncontrada['type'], $emp, $searchTerm);
                                if (!empty($emp['data_devolucao'])) {
                                    $status = 'Devolvido'; $cls = 'devolvido';
                                } elseif (strtotime($emp['data_prevista'] ?? 'now') < time()) {
                                    $status = 'Atrasado'; $cls = 'atrasado';
                                } else {
                                    $status = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                    $cls = strtolower($status);
                                }
                            ?>
                                <tr class="clickable-row" onclick="window.location.href='<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>'">
                                    <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);">
                                        <a class="result-link" href="<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></a>
                                    </td>
                                    <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'] ?? 'now')); ?></td>
                                    <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo date('d/m/Y', strtotime($emp['data_prevista'] ?? 'now')); ?></td>
                                    <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><span class="status <?php echo $cls; ?>"><?php echo $status; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert erro" style="margin:0;">
                        <?php echo htmlspecialchars($searchMessage ?: 'Nenhum resultado encontrado.'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>
</body>
</html>