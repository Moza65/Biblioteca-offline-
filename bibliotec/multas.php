<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/common.php';

$multas = [];
$mensagem = '';
$tipoAlerta = '';
$valorMultasPorDia = 0.50; // Valor da multa por dia de atraso em €

// Buscar empréstimos atrasados com cálculo de multa
try {
    $sql = $pdo->prepare("
        SELECT 
            e.id_emprestimo,
            e.data_prevista,
            e.data_emprestimo,
            li.titulo AS livro,
            COALESCE(l.nome, 'Sem registro') AS leitor,
            l.id as id_leitor,
            DATEDIFF(CURDATE(), e.data_prevista) as dias_atraso,
            DATEDIFF(CURDATE(), e.data_prevista) * ? as multa_calculada,
            COALESCE(m.valor, 0) as valor_pago,
            m.data_multa,
            m.id_multa,
            d.id_devolucao
        FROM emprestimo e
        LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
        LEFT JOIN leitor l ON e.id_emprestimo_leitor = l.id
        LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
        LEFT JOIN multa m ON d.id_devolucao = m.id_devolucao
        WHERE d.id_devolucao IS NULL AND e.data_prevista < CURDATE()
        ORDER BY e.data_prevista ASC
    ");
    $sql->execute([$valorMultasPorDia]);
    $multas = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $multas = [];
}

// Processar pagamento de multa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar_pagamento') {
    try {
        $id_emprestimo = (int)$_POST['id_emprestimo'];
        $valor_pago = (float)$_POST['valor_pago'];

        if ($valor_pago <= 0) {
            throw new Exception("O valor deve ser maior que zero.");
        }

        // Buscar devolução associada ao empréstimo
        $sqlBuscaDevolucao = $pdo->prepare("SELECT id_devolucao FROM devolucao WHERE id_emprestimo = ?");
        $sqlBuscaDevolucao->execute([$id_emprestimo]);
        $devolucao = $sqlBuscaDevolucao->fetch(PDO::FETCH_ASSOC);

        if (!$devolucao) {
            throw new Exception("Empréstimo não encontrado.");
        }

        $id_devolucao = $devolucao['id_devolucao'];

        // Verificar se já existe multa registrada
        $sqlVerifica = $pdo->prepare("SELECT id_multa, valor FROM multa WHERE id_devolucao = ?");
        $sqlVerifica->execute([$id_devolucao]);
        $multa = $sqlVerifica->fetch(PDO::FETCH_ASSOC);

        if ($multa) {
            // Atualizar multa existente
            $sqlUpdate = $pdo->prepare("
                UPDATE multa SET valor = valor + ?, data_multa = CURDATE()
                WHERE id_devolucao = ?
            ");
            $sqlUpdate->execute([$valor_pago, $id_devolucao]);
            $mensagem = "Pagamento de multa registado com sucesso!";
        } else {
            // Inserir nova multa
            $sqlInsert = $pdo->prepare("
                INSERT INTO multa (id_devolucao, valor, data_multa)
                VALUES (?, ?, CURDATE())
            ");
            $sqlInsert->execute([$id_devolucao, $valor_pago]);
            $mensagem = "Multa criada e pagamento registado com sucesso!";
        }

        $tipoAlerta = 'sucesso';
        
        // Recarregar multas
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipoAlerta = 'erro';
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multas - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/emprestimos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .multa-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin: 32px;
        }

        .summary-card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--danger);
        }

        .summary-card h3 {
            font-size: 12px;
            color: var(--gray-500);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--danger);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            padding: 32px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            margin-bottom: 24px;
        }

        .modal-header h2 {
            color: var(--dark);
            margin-bottom: 8px;
        }

        .modal-header p {
            color: var(--gray-500);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            flex: 1;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .multa-action {
            display: flex;
            gap: 8px;
        }

        .btn-pagar {
            padding: 8px 14px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-pagar:hover {
            background: #059669;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .alert.sucesso {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--secondary);
            color: #059669;
        }

        .alert.erro {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger);
            color: #b91c1c;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                    <img src="../asset/icones/alert-circle.svg" alt="" class="page-title-icon">
                    <h1>Gestão de Multas</h1>
                </div>
                <p>Calcule e registre pagamentos de multas por atraso de empréstimos</p>
            </div>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert <?php echo $tipoAlerta; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- RESUMO -->
        <div class="multa-summary">
            <div class="summary-card">
                <h3>Empréstimos Atrasados</h3>
                <div class="value"><?php echo count($multas); ?></div>
            </div>
            <div class="summary-card">
                <h3>Multa por Dia</h3>
                <div class="value">€<?php echo number_format($valorMultasPorDia, 2); ?></div>
            </div>
            <div class="summary-card">
                <h3>Total em Multas</h3>
                <div class="value">€<?php echo number_format(array_sum(array_column($multas, 'multa_calculada')), 2); ?></div>
            </div>
        </div>

        <!-- TABELA DE MULTAS -->
        <div class="table-card" style="margin: 32px;">
            <div class="table-card-header">
                <h2>Empréstimos Atrasados</h2>
                <span><?php echo count($multas); ?> registro<?php echo count($multas) !== 1 ? 's' : ''; ?></span>
            </div>

            <table class="emprestimos-table">
                <thead>
                    <tr>
                        <th>Leitor</th>
                        <th>Livro</th>
                        <th>Data Prevista</th>
                        <th>Dias de Atraso</th>
                        <th>Multa Calculada</th>
                        <th>Pago</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($multas) > 0): ?>
                        <?php foreach ($multas as $multa): 
                            $multa_pendente = $multa['multa_calculada'] - $multa['valor_pago'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($multa['leitor']); ?></td>
                                <td><?php echo htmlspecialchars($multa['livro']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($multa['data_prevista'])); ?></td>
                                <td style="color: var(--danger); font-weight: 600;"><?php echo $multa['dias_atraso']; ?></td>
                                <td>€<?php echo number_format($multa['multa_calculada'], 2); ?></td>
                                <td>€<?php echo number_format($multa['valor_pago'], 2); ?> <?php echo $multa_pendente > 0 ? '<span style="color: var(--danger);">(€' . number_format($multa_pendente, 2) . ' pendente)</span>' : '<span style="color: var(--secondary);">✓ Pago</span>'; ?></td>
                                <td>
                                    <button class="btn-pagar" onclick="abrirModalPagamento(<?php echo $multa['id_emprestimo']; ?>, <?php echo $multa['multa_calculada']; ?>)">
                                        💰 Pagar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="table-empty">Nenhum empréstimo atrasado encontrado. 🎉</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- MODAL DE PAGAMENTO -->
<div class="modal" id="modalPagamento">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Registrar Pagamento de Multa</h2>
            <p>Indique o valor pago pelo leitor</p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="registrar_pagamento">
            <input type="hidden" name="id_emprestimo" id="modalIdEmprestimo" value="">
            
            <div class="form-group">
                <label for="valor_pago">Valor Pago (€) *</label>
                <input type="number" id="valor_pago" name="valor_pago" step="0.01" min="0" required placeholder="0.00">
                <small style="color: var(--gray-500); font-size: 12px; margin-top: 4px; display: block;">Valor sugerido: <span id="valorSugerido">0.00</span> €</small>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">✓ Confirmar Pagamento</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModalPagamento()">✕ Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalPagamento(idEmprestimo, valorSugerido) {
        document.getElementById('modalIdEmprestimo').value = idEmprestimo;
        document.getElementById('valor_pago').value = '';
        document.getElementById('valorSugerido').textContent = valorSugerido.toFixed(2);
        document.getElementById('modalPagamento').classList.add('active');
        document.getElementById('valor_pago').focus();
    }

    function fecharModalPagamento() {
        document.getElementById('modalPagamento').classList.remove('active');
    }

    // Fechar modal ao clicar fora
    document.getElementById('modalPagamento').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalPagamento();
        }
    });
</script>
</body>
</html>
