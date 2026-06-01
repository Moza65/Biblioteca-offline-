<?php
require_once "../Buscas/buscarDados.php";

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../logout.php");
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
    return 'bibliotec.php';
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
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/bibliotecarios.css">
    <link rel="stylesheet" href="../asset/style/biblio.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <header class="topbar">
            <div class="welcome-text">
                <h1>Olá, <?php echo htmlspecialchars($usuario['nome']); ?></h1>
                <p>Painel do Bibliotecário - Gerencie as operações da biblioteca</p>
            </div>
            <div class="topbar-actions">
                <form method="POST" class="search-bar">
                    <input type="text" name="campoPesquisa" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Buscar livro, leitor, empréstimo...">
                    <button name="pesquisar" type="submit" style="background:none;border:none;cursor:pointer;display:flex;align-items:center;">
                        <img src="../asset/icones/search.svg" class="icon" alt="Buscar">
                    </button>
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
                    <?php if ($pesquisaEncontrada['type'] === 'emprestimo'): ?>
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Livro</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Leitor</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Data Empréstimo</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Previsão</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $item):
                                    $rowUrl = buildSearchUrl($pesquisaEncontrada['type'], $item, $searchTerm);
                                    if (!empty($item['data_prevista']) && strtotime($item['data_prevista']) < time()) {
                                        $status = 'Atrasado';
                                        $cls = 'atrasado';
                                    } else {
                                        $status = !empty($item['estado']) ? 'Ativo' : 'Pendente';
                                        $cls = strtolower($status);
                                    }
                                ?>
                                    <tr class="clickable-row" onclick="window.location.href='<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>'">
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><a class="result-link" href="<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['titulo_livro'] ?? '-'); ?></a></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['leitor'] ?? '-'); ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo !empty($item['data_emprestimo']) ? date('d/m/Y', strtotime($item['data_emprestimo'])) : '-'; ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo !empty($item['data_prevista']) ? date('d/m/Y', strtotime($item['data_prevista'])) : '-'; ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><span class="status <?php echo $cls; ?>"><?php echo $status; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($pesquisaEncontrada['type'] === 'leitor'): ?>
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Nome</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Email</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Telefone</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">NIF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $item):
                                    $rowUrl = buildSearchUrl($pesquisaEncontrada['type'], $item, $searchTerm);
                                ?>
                                    <tr class="clickable-row" onclick="window.location.href='<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>'">
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><a class="result-link" href="<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['nome'] ?? '-'); ?></a></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['email'] ?? '-'); ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['numero_telefone'] ?? '-'); ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['nif'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($pesquisaEncontrada['type'] === 'livro'): ?>
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Título</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Autor</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Quantidade</th>
                                    <th style="background:var(--gray-50);padding:12px 18px;text-align:left;font-size:12px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Editora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $item):
                                    $rowUrl = buildSearchUrl($pesquisaEncontrada['type'], $item, $searchTerm);
                                ?>
                                    <tr class="clickable-row" onclick="window.location.href='<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>'">
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><a class="result-link" href="<?php echo htmlspecialchars($rowUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['titulo'] ?? '-'); ?></a></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['autor'] ?? '-'); ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['quantidade'] ?? '-'); ?></td>
                                        <td style="padding:12px 18px;font-size:14px;border-bottom:1px solid var(--gray-200);"><?php echo htmlspecialchars($item['editora'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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