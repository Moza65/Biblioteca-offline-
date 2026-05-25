<?php
require_once "../Buscas/Gerenciadores.php";
require_once __DIR__ . '/common.php';

$gerenciador = new GerenciadorDevolucoes();
$mensagem    = '';
$tipoAlerta  = '';
$gerenciador = new GerenciadorDevolucoes();
$mensagem = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'devolver') {
    try {
        $gerenciador->marcarComoDevolvido($_POST['id_emprestimo']);
        $mensagem   = "Empréstimo marcado como devolvido com sucesso.";
        $tipoAlerta = 'sucesso';
    } catch (Exception $e) {
        $mensagem   = "Erro: " . $e->getMessage();
        $tipoAlerta = 'erro';
    }
}

$pendentes  = $gerenciador->listarPendentes();
$devolvidos = $gerenciador->listarDevolvidos();
?>
        $mensagem = "Empréstimo marcado como devolvido com sucesso.";
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}

$pendentes = $gerenciador->listarPendentes();
$devolvidos = $gerenciador->listarDevolvidos();
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/devolucao.css">
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
                    <img src="../asset/icones/devolucao.svg" alt="" class="page-title-icon">
                    <h1>Devolução</h1>
                </div>
                <p>Registe e acompanhe devoluções de empréstimos</p>
            </div>
        </header>

        <!-- ALERTA -->
        <?php if ($mensagem): ?>
            <div class="alert <?php echo $tipoAlerta; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- INFO -->
        <div class="info-box">
            Aqui pode ver os empréstimos pendentes e registar devoluções rapidamente.
        </div>

        <!-- TABELA PENDENTES -->
        <div class="section-card">
            <div class="section-card-header">
                <h2>Pendentes para devolução</h2>
                <span><?php echo count($pendentes); ?> pendente<?php echo count($pendentes) !== 1 ? 's' : ''; ?></span>
            </div>
            <table class="devolucao-table">
                <thead>
                    <tr>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data Empréstimo</th>
                        <th>Previsão</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pendentes) > 0): ?>
                        <?php foreach ($pendentes as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                <td><?php echo htmlspecialchars($item['titulo_livro']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_emprestimo'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_prevista'])); ?></td>
                                <td><span class="badge-status pendente">Pendente</span></td>
                                <td>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="action" value="devolver">
                                        <input type="hidden" name="id_emprestimo" value="<?php echo (int)$item['id_emprestimo']; ?>">
                                        <button type="submit" class="btn-action">✓ Registar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="table-empty">Nenhum empréstimo pendente encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TABELA DEVOLVIDOS -->
        <div class="section-card">
            <div class="section-card-header">
                <h2>Devoluções registadas</h2>
                <span><?php echo count($devolvidos); ?> devolução<?php echo count($devolvidos) !== 1 ? 'ões' : ''; ?></span>
            </div>
            <table class="devolucao-table">
                <thead>
                    <tr>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data Devolução</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($devolvidos) > 0): ?>
                        <?php foreach ($devolvidos as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                <td><?php echo htmlspecialchars($item['titulo_livro']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_devolucao'])); ?></td>
                                <td><span class="badge-status devolvido">Devolvido</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="table-empty">Nenhuma devolução registada ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
</body>
</html>
    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .devolucao-table{margin: auto;width:  100%; }
        .devolucao-table, .devolucao-table th, .devolucao-table td { border-collapse: collapse; }
        .devolucao-table th { background: #f3f4f6; padding: 12px;  text-align: center; border-bottom: 2px solid #e5e7eb; }
        .devolucao-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center; }
        .devolucao-table tr:hover { background: #f9fafb; }
        .status {  border-radius: 999px; font-size: 12px; font-weight: 700; display: inline-block; }
        .status.pendente { background: #fef3c7; color: #92400e; }
        .status.devolvido { background: #d1fae5; color: #065f46; }
        .btn-action { padding: 8px 14px; border: none; border-radius: 8px; cursor: pointer; color: white; background: #10b981; }
        .info-box { background:#f3f4f6; border:1px solid #e5e7eb; border-radius:10px; padding:16px; margin-top:20px; color:#111827; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Devolução 🔁</h1>
                    <p>Registre e acompanhe devoluções de empréstimos</p>
                </div>
            </header>
            <?php if ($mensagem): ?>
                <div class="mensagem <?php echo strpos($mensagem, 'Erro') !== false ? 'erro' : 'sucesso'; ?>" style="margin-top: 20px;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            <div class="info-box">
                Aqui você vê empréstimos pendentes e pode registrar devoluções rapidamente.
            </div>

            <section style="margin-top: 24px;">
                <h2>Pendentes para devolução</h2>
                <table class="devolucao-table" style="margin-top: 16px;">
                    <thead>
                        <tr>
                            <th hidden>ID</th>
                            <th>Leitor</th>
                            <th>Livro</th>
                            <th>Empréstimo</th>
                            <th>Previsão</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pendentes) > 0): ?>
                            <?php foreach ($pendentes as $item): ?>
                                <tr>
                                    <td hidden><?php echo htmlspecialchars($item['id_emprestimo']); ?></td>
                                    <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                    <td><?php echo htmlspecialchars($item['titulo_livro']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['data_emprestimo'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['data_prevista'])); ?></td>
                                    <td><span class="status pendente">Pendente</span></td>
                                    <td>
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="action" value="devolver">
                                            <input type="hidden" name="id_emprestimo" value="<?php echo (int)$item['id_emprestimo']; ?>">
                                            <button type="submit" class="btn-action">Registrar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding: 24px;">Nenhum empréstimo pendente encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section style="margin-top: 40px;">
                <h2>Devoluções registradas</h2>
                <table class="devolucao-table" style="margin-top: 16px;">
                    <thead>
                        <tr>
                            <th hidden>ID Empréstimo</th>
                            <th>Leitor</th>
                            <th >Livro</th>
                            <th>Devolução</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($devolvidos) > 0): ?>
                            <?php foreach ($devolvidos as $item): ?>
                                <tr>
                                    <td hidden><?php echo htmlspecialchars($item['id_emprestimo']); ?></td>
                                    <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                    <td><?php echo htmlspecialchars($item['titulo_livro']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['data_devolucao'])); ?></td>
                                    <td><span class="status devolvido">Devolvido</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 24px;">Nenhuma devolução registrada ainda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>

