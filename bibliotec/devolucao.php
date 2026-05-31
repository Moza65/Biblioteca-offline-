<?php
require_once "../Buscas/Gerenciadores.php";
require_once __DIR__ . '/common.php';

$gerenciador = new GerenciadorDevolucoes();
$mensagem    = '';
$tipoAlerta  = '';
$valorMultaDia = 0.50;

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

        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/devolucao.svg" alt="" class="page-title-icon">
                    <h1>Devolução</h1>
                </div>
                <p>Registe e acompanhe devoluções de empréstimos</p>
            </div>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert <?php echo $tipoAlerta; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            Aqui pode ver os empréstimos pendentes, verificar atrasos e calcular multas.
            Multa diária: <strong>€<?php echo number_format($valorMultaDia, 2); ?></strong> por dia de atraso.
        </div>

        <!-- PENDENTES -->
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
                        <th>Dias de Atraso</th>
                        <th>Multa Estimada</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pendentes) > 0): ?>
                        <?php foreach ($pendentes as $item):
                            $dataPrevista = strtotime($item['data_prevista']);
                            $diasAtraso = max(0, floor((time() - $dataPrevista) / 86400));
                            $multaEstimativa = $diasAtraso * $valorMultaDia;
                            $statusLabel = $diasAtraso > 0 ? 'Atrasado' : 'Pendente';
                            $statusClass = $diasAtraso > 0 ? 'atrasado' : 'pendente';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['leitor']); ?></td>
                                <td><?php echo htmlspecialchars($item['titulo_livro']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_emprestimo'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_prevista'])); ?></td>
                                <td><?php echo $diasAtraso; ?></td>
                                <td>€<?php echo number_format($multaEstimativa, 2); ?></td>
                                <td><span class="badge-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
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
                            <td colspan="8" class="table-empty">Nenhum empréstimo pendente encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- DEVOLVIDOS -->
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