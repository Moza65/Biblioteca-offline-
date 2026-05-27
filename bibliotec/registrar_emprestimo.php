<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Buscas/Gerenciadores.php';
require_once __DIR__ . '/common.php';

$gerenciador = new GerenciadorEmprestimos();
$gerenciadorLivros = new GerenciadorLivros();
$gerenciadorLeitores = new GerenciadorLeitores();
$mensagem = '';
$tipoAlerta = '';
$limiteEmprestimoPorLeitor = 5; // Limite máximo de empréstimos simultâneos

// Buscar livros disponíveis
$livrosDisponiveis = [];
try {
    $sql = $pdo->prepare("SELECT id_livro, titulo, autor, quantidade FROM livro WHERE quantidade > 0 ORDER BY titulo");
    $sql->execute();
    $livrosDisponiveis = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $livrosDisponiveis = [];
}

// Buscar leitores
$leitores = [];
try {
    $sql = $pdo->prepare("SELECT id, nome, email FROM leitor ORDER BY nome");
    $sql->execute();
    $leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $leitores = [];
}

// Processar formulário de registrar empréstimo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar') {
    try {
        $id_leitor = (int)$_POST['id_leitor'];
        $id_livro = (int)$_POST['id_livro'];
        $dias_duracao = (int)($_POST['dias_duracao'] ?? 14);

        // Validar limite de empréstimos
        $sqlVerificaLimite = $pdo->prepare("
            SELECT COUNT(*) FROM emprestimo e
            LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
            WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL
        ");
        $sqlVerificaLimite->execute([$id_leitor]);
        $emprestimosAtivos = (int)$sqlVerificaLimite->fetchColumn();

        if ($emprestimosAtivos >= $limiteEmprestimoPorLeitor) {
            throw new Exception("Leitor atingiu o limite de $limiteEmprestimoPorLeitor empréstimos simultâneos.");
        }

        // Registrar empréstimo
        $data_emprestimo = date('Y-m-d');
        $data_prevista = date('Y-m-d', strtotime("+$dias_duracao days"));
        
        $sqlInsert = $pdo->prepare("
            INSERT INTO emprestimo (fk_Usuario_id_usuario, fk_Livro_id_livro, id_emprestimo_leitor, estado, data_prevista, data_emprestimo)
            VALUES (?, ?, ?, 1, ?, ?)
        ");
        
        $usuario_id = $_SESSION['usuario']['id_usuario'] ?? 1;
        $sqlInsert->execute([$usuario_id, $id_livro, $id_leitor, $data_prevista, $data_emprestimo]);
        
        // Reduzir quantidade de livros
        $sqlUpdateLivro = $pdo->prepare("UPDATE livro SET quantidade = quantidade - 1 WHERE id_livro = ?");
        $sqlUpdateLivro->execute([$id_livro]);

        $mensagem = "Empréstimo registado com sucesso!";
        $tipoAlerta = 'sucesso';
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
    <title>Registrar Empréstimo - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/emprestimos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .form-card {
            background: var(--white);
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin: 32px;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin: 32px;
            margin-bottom: 0;
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

        .info-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(79, 172, 254, 0.1));
            border-left: 4px solid var(--primary);
            padding: 16px;
            margin: 32px;
            border-radius: 6px;
            font-size: 14px;
            color: var(--gray-700);
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
                    <img src="../asset/icones/arrow-right-left.svg" alt="" class="page-title-icon">
                    <h1>Registrar Empréstimo</h1>
                </div>
                <p>Registre um novo empréstimo de livro para um leitor</p>
            </div>
        </header>

        <?php if ($mensagem): ?>
            <div class="alert <?php echo $tipoAlerta; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            💡 Limite máximo de empréstimos simultâneos por leitor: <strong><?php echo $limiteEmprestimoPorLeitor; ?></strong> | Duração padrão: <strong>14 dias</strong>
        </div>

        <div class="form-card">
            <form method="POST">
                <input type="hidden" name="action" value="registrar">

                <div class="form-group">
                    <label for="id_leitor">Selecione o Leitor *</label>
                    <select id="id_leitor" name="id_leitor" required>
                        <option value="">-- Escolha um leitor --</option>
                        <?php foreach ($leitores as $leitor): ?>
                            <option value="<?php echo (int)$leitor['id']; ?>">
                                <?php echo htmlspecialchars($leitor['nome']); ?> (<?php echo htmlspecialchars($leitor['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_livro">Selecione o Livro *</label>
                    <select id="id_livro" name="id_livro" required>
                        <option value="">-- Escolha um livro --</option>
                        <?php foreach ($livrosDisponiveis as $livro): ?>
                            <option value="<?php echo (int)$livro['id_livro']; ?>">
                                <?php echo htmlspecialchars($livro['titulo']); ?> - <?php echo htmlspecialchars($livro['autor']); ?> (<?php echo (int)$livro['quantidade']; ?> disp.)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dias_duracao">Duração em Dias</label>
                    <input type="number" id="dias_duracao" name="dias_duracao" value="14" min="1" max="30">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">✓ Registrar Empréstimo</button>
                    <a href="emprestimos.php" class="btn btn-secondary" style="text-decoration: none;">← Voltar</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
