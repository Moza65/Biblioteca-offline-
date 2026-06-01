<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Buscas/buscarDados.php';
require_once __DIR__ . '/common.php';

$callClass = new BuscarDados();
$searchTerm = trim($_GET['busca'] ?? '');

try {
    if ($searchTerm !== '') {
        $leitores = $callClass->GetReady($searchTerm);
    } else {
        $sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
        $sql->execute();
        $leitores = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $ex) {
    $leitores = [];
}

$total = count($leitores);

$leitorSelecionado = null;
$pendencias = [];
$atrasados = [];
$emprestimosAtivos = 0;

if (isset($_GET['id_leitor'])) {
    $idLeitor = (int)$_GET['id_leitor'];
    try {
        $sql = $pdo->prepare("SELECT * FROM leitor WHERE id = ?");
        $sql->execute([$idLeitor]);
        $leitorSelecionado = $sql->fetch(PDO::FETCH_ASSOC);

        if ($leitorSelecionado) {
            $sqlPendencias = $pdo->prepare(
                "SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, li.titulo,
                        DATEDIFF(CURDATE(), e.data_prevista) AS dias_atraso
                 FROM emprestimo e
                 LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                 LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                 WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL
                 ORDER BY e.data_prevista ASC"
            );
            $sqlPendencias->execute([$idLeitor]);
            $pendencias = $sqlPendencias->fetchAll(PDO::FETCH_ASSOC);

            $sqlAtrasados = $pdo->prepare(
                "SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, li.titulo,
                        DATEDIFF(CURDATE(), e.data_prevista) AS dias_atraso
                 FROM emprestimo e
                 LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                 LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                 WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL AND e.data_prevista < CURDATE()
                 ORDER BY e.data_prevista ASC"
            );
            $sqlAtrasados->execute([$idLeitor]);
            $atrasados = $sqlAtrasados->fetchAll(PDO::FETCH_ASSOC);

            $sqlAtivos = $pdo->prepare(
                "SELECT COUNT(*) FROM emprestimo e
                 LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                 WHERE e.id_emprestimo_leitor = ? AND d.id_devolucao IS NULL"
            );
            $sqlAtivos->execute([$idLeitor]);
            $emprestimosAtivos = (int)$sqlAtivos->fetchColumn();
        }
    } catch (Exception $ex) {
        $leitorSelecionado = null;
    }
}
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
                            <th>Ações</th>
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
                                    <td>
                                        <a href="?id_leitor=<?php echo (int)$leitor['id']; ?>" class="btn btn-primary" style="font-size:12px;padding:8px 10px;">Ver pendências</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="table-empty">Nenhum leitor cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>



            <?php if ($leitorSelecionado): ?>
                <div class="section-card" style="margin: 32px;">
                    <div class="section-card-header">
                        <h2>Verificação de Leitor</h2>
                        <span>Detalhes e pendências</span>
                    </div>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($leitorSelecionado['nome']); ?> | <strong>Email:</strong> <?php echo htmlspecialchars($leitorSelecionado['email']); ?></p>
                    <p><strong>Empréstimos ativos:</strong> <?php echo $emprestimosAtivos; ?> | <strong>Pendências:</strong> <?php echo count($pendencias); ?> | <strong>Atrasados:</strong> <?php echo count($atrasados); ?></p>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-top:20px;">
                        <div style="background:var(--gray-50);padding:18px;border-radius:10px;">
                            <strong>Verificar leitor</strong>
                            <p style="margin-top:10px;">Consulte os detalhes do leitor e seus empréstimos no sistema.</p>
                        </div>
                        <div style="background:var(--gray-50);padding:18px;border-radius:10px;">
                            <strong>Verificar pendências</strong>
                            <p style="margin-top:10px;">Este leitor possui <?php echo count($pendencias); ?> pendência<?php echo count($pendencias) !== 1 ? 's' : ''; ?>.</p>
                        </div>
                    </div>

                    <?php if (count($pendencias) > 0): ?>
                        <div style="margin-top:24px;">
                            <h3 style="margin-bottom:12px;">Pendências em aberto</h3>
                            <table class="leitores-table">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th>Data Empréstimo</th>
                                        <th>Previsão</th>
                                        <th>Dias em atraso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendencias as $pend): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($pend['titulo']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($pend['data_emprestimo'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($pend['data_prevista'])); ?></td>
                                            <td><?php echo max(0, (int)$pend['dias_atraso']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if (count($atrasados) > 0): ?>
                        <div style="margin-top:24px;">
                            <h3 style="margin-bottom:12px;">Atrasos</h3>
                            <table class="leitores-table">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th>Previsão</th>
                                        <th>Dias de atraso</th>
                                        <th>Multa estimada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($atrasados as $atraso): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($atraso['titulo']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($atraso['data_prevista'])); ?></td>
                                            <td><?php echo max(0, (int)$atraso['dias_atraso']); ?></td>
                                            <td>€<?php echo number_format(max(0, (int)$atraso['dias_atraso']) * 0.50, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

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