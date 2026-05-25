<?php
require_once "../Buscas/Gerenciadores.php";
require_once __DIR__ . '/common.php';
$gerenciador = new GerenciadorCategorias();
$mensagem = '';
$categoria_edicao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'adicionar') {
            $gerenciador->adicionar(trim($_POST['nome']), trim($_POST['descricao']));
            $mensagem = "Categoria adicionada com sucesso!";
        } elseif ($_POST['action'] === 'atualizar') {
            $gerenciador->atualizar($_POST['id_categoria'], trim($_POST['nome']), trim($_POST['descricao']));
            $mensagem = "Categoria atualizada com sucesso!";
        } elseif ($_POST['action'] === 'deletar') {
            $gerenciador->deletar($_POST['id_categoria']);
            $mensagem = "Categoria removida com sucesso!";
        }
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}

if (isset($_GET['editar'])) {
    $categoria_edicao = $gerenciador->obterPorId($_GET['editar']);
}

$categorias = $gerenciador->listarTodos();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/categorias.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Categorias 🗂️</h1>
                    <p>Gerencie as categorias do acervo</p>
                </div>
            </header>
            <?php if ($mensagem): ?>
                <div class="mensagem <?php echo strpos($mensagem, 'Erro') !== false ? 'erro' : 'sucesso'; ?>" style="margin-top: 20px;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="content-grid">
                <div class="card">
                    <h2><?php echo $categoria_edicao ? 'Editar Categoria' : 'Nova Categoria'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $categoria_edicao ? 'atualizar' : 'adicionar'; ?>">
                        <input type="hidden" name="id_categoria" value="<?php echo htmlspecialchars($categoria_edicao['id_categoria'] ?? ''); ?>">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($categoria_edicao['nome'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($categoria_edicao['descricao'] ?? ''); ?></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn-primary"><?php echo $categoria_edicao ? 'Salvar alterações' : 'Criar categoria'; ?></button>
                            <?php if ($categoria_edicao): ?>
                                <a href="categorias.php" class="btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="card">
                    <h2>Categorias existentes</h2>
                    <table class="categorias-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categorias) > 0): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($categoria['id_categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($categoria['descricao']); ?></td>
                                        <td>
                                            <a href="categorias.php?editar=<?php echo (int)$categoria['id_categoria']; ?>" class="btn-action btn-edit">Editar</a>
                                            <form method="POST" style="display:inline-block; margin:0;">
                                                <input type="hidden" name="action" value="deletar">
                                                <input type="hidden" name="id_categoria" value="<?php echo (int)$categoria['id_categoria']; ?>">
                                                <button type="submit" class="btn-action btn-delete" onclick="return confirm('Deseja remover esta categoria?');">Remover</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px;">Nenhuma categoria cadastrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
