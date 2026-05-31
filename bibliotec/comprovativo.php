<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/common.php';

$comprovativo = null;
$erro = '';

// Se há ID de empréstimo na URL, buscar dados
if (isset($_GET['id_emprestimo'])) {
    try {
        $sql = $pdo->prepare("
            SELECT 
                e.id_emprestimo,
                e.data_emprestimo,
                e.data_prevista,
                li.titulo AS livro,
                li.autor,
                li.edicao,
                l.nome AS leitor,
                l.email,
                l.numero_telefone,
                u.nome AS bibliotecario,
                d.data_devolucao,
                DATEDIFF(e.data_prevista, e.data_emprestimo) as dias_emprestimo
            FROM emprestimo e
            LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
            LEFT JOIN leitor l ON e.id_emprestimo_leitor = l.id
            LEFT JOIN usuario u ON e.fk_Usuario_id_usuario = u.id_usuario
            LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
            WHERE e.id_emprestimo = ?
        ");
        $sql->execute([(int)$_GET['id_emprestimo']]);
        $comprovativo = $sql->fetch(PDO::FETCH_ASSOC);
        
        if (!$comprovativo) {
            $erro = "Empréstimo não encontrado.";
        }
    } catch (Exception $e) {
        $erro = "Erro ao buscar comprovativo: " . $e->getMessage();
    }
}

// Lista de empréstimos para escolher
$emprestimos = [];
try {
    $sql = $pdo->prepare("
        SELECT 
            e.id_emprestimo,
            li.titulo,
            l.nome AS leitor,
            e.data_emprestimo
        FROM emprestimo e
        LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
        LEFT JOIN leitor l ON e.id_emprestimo_leitor = l.id
        ORDER BY e.data_emprestimo DESC
        LIMIT 50
    ");
    $sql->execute();
    $emprestimos = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $emprestimos = [];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Empréstimo - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .comprovativo-container {
            margin: 32px;
            max-width: 700px;
        }

        .selector-card {
            background: var(--white);
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 14px;
        }

        .form-group select {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-buscar {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
        }

        .btn-buscar:hover {
            background: #2563eb;
        }

        .comprovativo-document {
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-family: 'Georgia', serif;
            line-height: 1.6;
        }

        .doc-header {
            text-align: center;
            border-bottom: 2px solid var(--dark);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .doc-header h1 {
            font-size: 24px;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .doc-header p {
            color: var(--gray-600);
            font-size: 12px;
            margin: 0;
        }

        .doc-section {
            margin-bottom: 20px;
        }

        .doc-section-title {
            font-weight: bold;
            font-size: 14px;
            color: var(--dark);
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--gray-300);
            padding-bottom: 5px;
        }

        .doc-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .doc-row-label {
            font-weight: 600;
            color: var(--gray-700);
            min-width: 150px;
        }

        .doc-row-value {
            text-align: right;
            flex: 1;
            color: var(--gray-800);
        }

        .separator {
            border-top: 1px dashed var(--gray-300);
            margin: 20px 0;
        }

        .doc-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--dark);
            font-size: 12px;
            color: var(--gray-600);
        }

        .btn-print {
            margin-top: 24px;
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn-print:hover {
            background: #2563eb;
        }

        @media print {
            .selector-card, .btn-print, .topbar {
                display: none;
            }
            .comprovativo-container {
                margin: 0;
                max-width: 100%;
            }
            .comprovativo-document {
                box-shadow: none;
                border: 1px solid #000;
                padding: 20px;
            }
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
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
                    <img src="../asset/icones/file-text.svg" alt="" class="page-title-icon">
                    <h1>Comprovativo de Empréstimo</h1>
                </div>
                <p>Emita comprovativo e recibos de empréstimo</p>
            </div>
        </header>

        <div class="comprovativo-container">
            <?php if ($erro): ?>
                <div class="alert erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <!-- Seletor de Empréstimo -->
            <div class="selector-card">
                <form method="GET">
                    <div class="form-group">
                        <label for="id_emprestimo">Selecione o Empréstimo</label>
                        <select id="id_emprestimo" name="id_emprestimo" onchange="this.form.submit()">
                            <option value="">-- Escolha um empréstimo --</option>
                            <?php foreach ($emprestimos as $emp): ?>
                                <option value="<?php echo (int)$emp['id_emprestimo']; ?>" <?php echo isset($_GET['id_emprestimo']) && $_GET['id_emprestimo'] == $emp['id_emprestimo'] ? 'selected' : ''; ?>>
                                    #<?php echo (int)$emp['id_emprestimo']; ?> - <?php echo htmlspecialchars($emp['leitor']); ?> - <?php echo htmlspecialchars($emp['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Comprovativo -->
            <?php if ($comprovativo): ?>
                <div class="comprovativo-document">
                    <div class="doc-header">
                        <h1>BIBLIOTECA PANDORA</h1>
                        <p>Comprovativo de Empréstimo</p>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">Informações do Empréstimo</div>
                        <div class="doc-row">
                            <span class="doc-row-label">Número:</span>
                            <span class="doc-row-value">#<?php echo str_pad($comprovativo['id_emprestimo'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Data de Empréstimo:</span>
                            <span class="doc-row-value"><?php echo date('d/m/Y', strtotime($comprovativo['data_emprestimo'])); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Duração:</span>
                            <span class="doc-row-value"><?php echo $comprovativo['dias_emprestimo']; ?> dias</span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Data de Devolução Prevista:</span>
                            <span class="doc-row-value"><?php echo date('d/m/Y', strtotime($comprovativo['data_prevista'])); ?></span>
                        </div>
                        <?php if ($comprovativo['data_devolucao']): ?>
                            <div class="doc-row">
                                <span class="doc-row-label">Data de Devolução Real:</span>
                                <span class="doc-row-value" style="color: var(--secondary);">✓ <?php echo date('d/m/Y', strtotime($comprovativo['data_devolucao'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">Informações do Leitor</div>
                        <div class="doc-row">
                            <span class="doc-row-label">Nome:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['leitor']); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Email:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['email']); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Telefone:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['numero_telefone'] ?? '—'); ?></span>
                        </div>
                    </div>

                    <div class="doc-section">
                        <div class="doc-section-title">Informações do Livro</div>
                        <div class="doc-row">
                            <span class="doc-row-label">Título:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['livro']); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Autor:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['autor']); ?></span>
                        </div>
                        <div class="doc-row">
                            <span class="doc-row-label">Edição:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['edicao']); ?></span>
                        </div>
                    </div>

                    <div class="separator"></div>

                    <div class="doc-section">
                        <div class="doc-section-title">Responsável</div>
                        <div class="doc-row">
                            <span class="doc-row-label">Bibliotecário:</span>
                            <span class="doc-row-value"><?php echo htmlspecialchars($comprovativo['bibliotecario'] ?? 'Sistema Pandora'); ?></span>
                        </div>
                    </div>

                    <div class="doc-footer">
                        <p>Este é um documento oficial de registro de empréstimo.<br>
                        Conserve este comprovativo como comprovação da transação.<br><br>
                        Data de Emissão: <?php echo date('d/m/Y H:i'); ?></p>
                    </div>
                </div>

                <button class="btn-print" onclick="window.print()">🖨️ Imprimir Comprovativo</button>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
