<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Buscas/buscarDados.php';
require_once __DIR__ . '/common.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../logout.php");
    exit();
}


$callClass = new BuscarDados();
$searchTerm = trim($_GET['busca'] ?? '');
$emprestimoId = isset($_GET['id_emprestimo']) ? (int)$_GET['id_emprestimo'] : 0;


try {
    if ($emprestimoId > 0) {
        $emprestimos = $callClass->GetBroard($emprestimoId);
    } elseif ($searchTerm !== '') {
        $emprestimos = $callClass->GetBroard($searchTerm);
    } else {
        $sql = $pdo->prepare("
            SELECT e.*, li.titulo AS titulo_livro, d.data_devolucao
            FROM emprestimo e
            LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
            LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
            ORDER BY e.data_emprestimo DESC
        ");
        $sql->execute();
        $emprestimos = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $ex) {
    $emprestimos = [];
}

$total = count($emprestimos);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimos - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/emprestimos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/book-copy.svg" alt="" class="page-title-icon">
                    <h1>Empréstimos</h1>
                </div>
                <p>Visualize todos os empréstimos ativos e concluídos</p>
            </div>
        </header>

        <div class="table-card">
            <div class="table-card-header">
                <h2>Lista de Empréstimos</h2>
                <span><?php echo $total; ?> registo<?php echo $total !== 1 ? 's' : ''; ?></span>
            </div>

            <table class="emprestimos-table">
                <thead>
                    <tr>
                        <th hidden>ID</th>
                        <th>Livro</th>
                        <th>Data do Empréstimo</th>
                        <th>Previsão de Devolução</th>
                        <th>Data de Devolução</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total > 0): ?>
                        <?php foreach ($emprestimos as $emp):
                            $devolvido = !empty($emp['data_devolucao']);
                            $data_prevista = $emp['data_prevista'] ?? null;
                            $previsao_ts = $data_prevista ? strtotime($data_prevista) : null;
                            $atrasado = !$devolvido && $previsao_ts !== null && $previsao_ts < time();

                            if ($devolvido) {
                                $status = 'Devolvido';
                                $class_status = 'devolvido';
                            } elseif ($atrasado) {
                                $status = 'Atrasado';
                                $class_status = 'atrasado';
                            } else {
                                $status = !empty($emp['estado']) ? 'Ativo' : 'Pendente';
                                $class_status = strtolower($status);
                            }
                        ?>
                            <tr>
                                <td hidden><?php echo htmlspecialchars($emp['id_emprestimo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($emp['titulo_livro'] ?? '-'); ?></td>
                                <td><?php echo isset($emp['data_emprestimo']) ? date('d/m/Y', strtotime($emp['data_emprestimo'])) : '—'; ?></td>
                                <td class="<?php echo $atrasado ? 'data-atrasada' : ''; ?>"><?php echo $data_prevista ? date('d/m/Y', strtotime($data_prevista)) : '—'; ?></td>
                                <td><?php echo $devolvido ? date('d/m/Y', strtotime($emp['data_devolucao'])) : '—'; ?></td>
                                <td><span class="status <?php echo $class_status; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="table-empty">Nenhum empréstimo registado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
