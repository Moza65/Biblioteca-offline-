<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/common.php';

// Processar cadastro via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar_leitor') {
    header('Content-Type: application/json');
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nif   = trim($_POST['nif'] ?? '');
    $tel   = trim($_POST['telefone'] ?? '');

    if (!$nome || !$email) {
        echo json_encode(['success' => false, 'message' => 'Nome e e-mail são obrigatórios.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO leitor (nome, email, nif, numero_telefone, data_leitor) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $nif ?: null, $tel ?: null]);
        echo json_encode(['success' => true, 'message' => 'Leitor cadastrado com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $e->getMessage()]);
    }
    exit;
}

try {
    $sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
    $sql->execute();
    $leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $ex) {
    $leitores = [];
}

$total = count($leitores);
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Leitores - Biblioteca Pandora</title>
        <link rel="stylesheet" href="../asset/style/adm/adm.css">
        <link rel="stylesheet" href="../asset/style/adm/leitores.css">
        <link rel="stylesheet" href="../asset/style/adm/modal_leitore.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="dashboard-container">
            <?php include 'sidebar.php'; ?>

            <main class="main-content">
                <header class="topbar">
                    <div class="welcome-text">
                        <div class="page-title-row">
                            <img src="../asset/icones/user-cog.svg" alt="" class="page-title-icon">
                            <h1>Gerenciar Leitores</h1>
                        </div>
                        <p>Visualize todos os leitores cadastrados</p>
                    </div>

                    <!-- Botão Novo Leitor -->
                    <div class="topbar-actions">
                        <button class="btn-primary" onclick="abrirModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Novo Leitor
                        </button>
                    </div>
                </header>

                <table class="leitores-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>NIF</th>
                            <th>Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total > 0): ?>
                            <?php foreach ($leitores as $leitor):
                                $iniciais = strtoupper(mb_substr($leitor['nome'] ?? '?', 0, 1));
                            ?>
                                <tr>
                                    <td class="col-id">#<?php echo htmlspecialchars($leitor['id']); ?></td>
                                    <td>
                                        <div class="leitor-nome-cell">
                                            <span class="leitor-avatar"><?php echo $iniciais; ?></span>
                                            <?php echo htmlspecialchars($leitor['nome']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($leitor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($leitor['numero_telefone'] ?? '—'); ?></td>
                                    <td class="col-nif"><?php echo htmlspecialchars($leitor['nif'] ?? '—'); ?></td>
                                    <td><?php echo !empty($leitor['data_leitor']) ? date('d/m/Y', strtotime($leitor['data_leitor'])) : '—'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="table-empty">Nenhum leitor cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>

        <!-- ══════════════ MODAL NOVO LEITOR ══════════════ -->
        <div class="modal" id="modalNovoLeitor">
            <div class="modal-content">

                <div class="modal-header">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                             style="vertical-align:-3px; margin-right:6px; color:var(--primary)">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3M13 7a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Novo Leitor
                    </h2>
                    <button class="close-btn" onclick="fecharModal()" aria-label="Fechar">×</button>
                </div>

                <div class="modal-body">
                    <form id="formNovoLeitor" onsubmit="salvarLeitor(event)">

                        <div class="modal-form-group">
                            <label for="inp-nome">Nome completo <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="inp-nome" name="nome"
                                   placeholder="Ex: Maria da Silva" required>
                        </div>

                        <div class="modal-form-group">
                            <label for="inp-email">E-mail <span style="color:var(--danger)">*</span></label>
                            <input type="email" id="inp-email" name="email"
                                   placeholder="exemplo@email.com" required>
                        </div>

                        <div class="modal-form-row">
                            <div class="modal-form-group" style="margin-bottom:0">
                                <label for="inp-nif">NIF</label>
                                <input type="text" id="inp-nif" name="nif"
                                       placeholder="000000000" maxlength="20">
                            </div>
                            <div class="modal-form-group" style="margin-bottom:0">
                                <label for="inp-tel">Telefone</label>
                                <input type="tel" id="inp-tel" name="telefone"
                                       placeholder="+244 900 000 000">
                            </div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-secondary" onclick="fecharModal()">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-primary" id="btn-salvar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 3v4H8V3M12 12v5m-2-2h4"/>
                                </svg>
                                Salvar Leitor
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>

        <!-- Toast de feedback -->
        <div class="toast" id="toast"></div>
        <script src="../asset/js/modal_leitor.js"></script>
    </body>
</html>