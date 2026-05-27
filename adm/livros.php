<?php
require_once __DIR__ . '/../Buscas/Gerenciadores.php';
require_once __DIR__ . '/common.php';

$gerenciador = new GerenciadorLivros();
$gerenciadorCategorias = new GerenciadorCategorias();
$categorias = $gerenciadorCategorias->listarTodos();
$mensagem = '';
$tipoAlerta = '';

$uploadDir = __DIR__ . '/../uploads/capas';
$uploadFolder = 'uploads/capas';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

function salvarImagem($file, $uploadDir, $uploadFolder) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Erro no envio da imagem.');
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) throw new Exception('O arquivo enviado não é uma imagem válida.');
    $allowedTypes = [IMAGETYPE_JPEG=>'jpg', IMAGETYPE_PNG=>'png', IMAGETYPE_GIF=>'gif', IMAGETYPE_WEBP=>'webp'];
    if (!isset($allowedTypes[$imageInfo[2]])) throw new Exception('Formato inválido. Use JPG, PNG, GIF ou WEBP.');
    $ext = $allowedTypes[$imageInfo[2]];
    $filename = uniqid('capa_', true) . '.' . $ext;
    $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Falha ao salvar a imagem de capa.');
    return $uploadFolder . '/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $imagemPath = null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagemPath = salvarImagem($_FILES['imagem'], $uploadDir, $uploadFolder);
        }

        if ($_POST['action'] === 'adicionar') {
            $gerenciador->adicionar($_POST['titulo'], $_POST['autor'], $_POST['quantidade'], $_POST['editora'], $_POST['edicao'], $imagemPath, !empty($_POST['categoria']) ? $_POST['categoria'] : null);
            $mensagem = 'Livro adicionado com sucesso!';
            $tipoAlerta = 'sucesso';
        } elseif ($_POST['action'] === 'deletar') {
            $gerenciador->deletar($_POST['id_livro']);
            $mensagem = 'Livro removido com sucesso!';
            $tipoAlerta = 'sucesso';
        } elseif ($_POST['action'] === 'atualizar') {
            $gerenciador->atualizar($_POST['id_livro'], $_POST['titulo'], $_POST['autor'], $_POST['quantidade'], $_POST['editora'], $_POST['edicao'], $imagemPath, !empty($_POST['categoria']) ? $_POST['categoria'] : null);
            $mensagem = 'Livro atualizado com sucesso!';
            $tipoAlerta = 'sucesso';
        }
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipoAlerta = 'erro';
    }
}

$livros = $gerenciador->listarTodos();
if (isset($_GET['busca'])) $livros = $gerenciador->buscar($_GET['busca']);

$livro_edicao = null;
if (isset($_GET['editar'])) $livro_edicao = $gerenciador->obterPorId($_GET['editar']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/livros.css">
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
                    <img src="../asset/icones/livro-aberto.svg" alt="" class="page-title-icon">
                    <h1>Livros</h1>
                </div>
                <p>Adicione, edite ou remova livros do acervo</p>
            </div>
        </header>

        <!-- ALERTA -->
        <?php if ($mensagem): ?>
            <div class="alert <?php echo $tipoAlerta; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <button class="btn-primary" onclick="abrirModal()">+ Adicionar Livro</button>
            <form method="GET" class="search-form">
                <img src="../asset/icones/search.svg" alt="">
                <input
                    type="text"
                    name="busca"
                    placeholder="Buscar por título, autor ou editora..."
                    value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>"
                >
                <button type="submit" class="btn-primary" style="border-radius:7px; padding:7px 14px; font-size:13px;">Buscar</button>
                <?php if (!empty($_GET['busca'])): ?>
                    <a href="livros.php" class="btn-secondary" style="border-radius:7px; padding:7px 14px; font-size:13px;">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- TABELA -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Acervo de Livros</h2>
                <span><?php echo count($livros); ?> livro<?php echo count($livros) !== 1 ? 's' : ''; ?></span>
            </div>

            <table class="livros-table">
                <thead>
                    <tr>
                        <th>Capa</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoria</th>
                        <th>Qtd.</th>
                        <th>Editora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($livros) > 0): ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td>
                                    <img
                                        src="<?php echo !empty($livro['imagem']) ? '../' . htmlspecialchars($livro['imagem']) : '../asset/icones/livro-aberto.svg'; ?>"
                                        alt="Capa"
                                        class="capa-thumb"
                                    >
                                </td>
                                <td><strong><?php echo htmlspecialchars($livro['titulo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($livro['categoria_nome'] ?? 'Sem categoria'); ?></td>
                                <td><span class="qty-badge"><?php echo htmlspecialchars($livro['quantidade']); ?></span></td>
                                <td><?php echo htmlspecialchars($livro['editora']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if (!empty($livro['imagem'])): ?>
                                            <button class="btn-acao btn-view" onclick="visualizarCapa('../<?php echo htmlspecialchars($livro['imagem']); ?>', '<?php echo addslashes(htmlspecialchars($livro['titulo'])); ?>')">Ver capa</button>
                                        <?php endif; ?>
                                        <button class="btn-acao btn-edit" onclick="editarLivro(<?php echo (int)$livro['id_livro']; ?>)">Editar</button>
                                        <button class="btn-acao btn-delete" onclick="deletarLivro(<?php echo (int)$livro['id_livro']; ?>)">Deletar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="table-empty">Nenhum livro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<!-- MODAL ADICIONAR / EDITAR -->
<div id="modal" class="modal <?php echo $livro_edicao ? 'show' : ''; ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-titulo"><?php echo $livro_edicao ? 'Editar Livro' : 'Adicionar Novo Livro'; ?></h2>
            <button class="close-btn" onclick="fecharModal()">×</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="form-action" value="<?php echo $livro_edicao ? 'atualizar' : 'adicionar'; ?>">
                <input type="hidden" name="id_livro" id="form-id" value="<?php echo htmlspecialchars($livro_edicao['id_livro'] ?? ''); ?>">
                <input type="hidden" name="imagem_atual" id="imagem-atual" value="<?php echo htmlspecialchars($livro_edicao['imagem'] ?? ''); ?>">

                <div class="modal-form-group">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Ex: Dom Casmurro" required value="<?php echo htmlspecialchars($livro_edicao['titulo'] ?? ''); ?>">
                </div>
                <div class="modal-form-group">
                    <label for="autor">Autor</label>
                    <input type="text" id="autor" name="autor" placeholder="Ex: Machado de Assis" required value="<?php echo htmlspecialchars($livro_edicao['autor'] ?? ''); ?>">
                </div>
                <div class="modal-form-group">
                    <label for="editora">Editora</label>
                    <input type="text" id="editora" name="editora" placeholder="Ex: Companhia das Letras" required value="<?php echo htmlspecialchars($livro_edicao['editora'] ?? ''); ?>">
                </div>
                <div class="modal-form-group">
                    <label for="edicao">Edição</label>
                    <input type="text" id="edicao" name="edicao" placeholder="Ex: 1ª Edição" value="<?php echo htmlspecialchars($livro_edicao['edicao'] ?? ''); ?>">
                </div>
                <div class="modal-form-group">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" min="0" placeholder="Ex: 5" required value="<?php echo htmlspecialchars($livro_edicao['quantidade'] ?? ''); ?>">
                </div>
                <div class="modal-form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria">
                        <option value="">Sem categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id_categoria']; ?>" <?php echo isset($livro_edicao['categoria_id']) && $livro_edicao['categoria_id'] == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label for="imagem">Capa do Livro</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" onchange="mostrarPreviewCapa(this)">
                    <?php if (!empty($livro_edicao['imagem'])): ?>
                        <img src="../<?php echo htmlspecialchars($livro_edicao['imagem']); ?>" alt="Capa actual" class="preview-capa" id="preview-capa">
                    <?php else: ?>
                        <img src="" alt="Pré-visualização" class="preview-capa" id="preview-capa" style="display:none;">
                    <?php endif; ?>
                    <span class="capa-selecionada-aviso" id="capa-selecionada">Nova capa selecionada.</span>
                    <span class="capa-hint">Formatos aceites: JPG, PNG, GIF, WEBP</span>
                </div>

                <div class="modal-actions">
                    <button type="submit" id="btn-salvar-livro" class="btn-primary">
                        <?php echo $livro_edicao ? '✓ Salvar alterações' : '+ Adicionar Livro'; ?>
                    </button>
                    <button type="button" class="btn-secondary" onclick="fecharModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER CAPA -->
<div id="cover-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="cover-modal-title">Capa do Livro</h2>
            <button class="close-btn" onclick="fecharCoverModal()">×</button>
        </div>
        <div class="modal-body" style="text-align:center; padding-top:0;">
            <img id="cover-modal-image" src="" alt="Capa" style="max-width:100%; border-radius:10px; border:1px solid var(--gray-200);">
        </div>
    </div>
</div>

<script>
    function abrirModal() { document.getElementById('modal').classList.add('show'); }
    function fecharModal() { document.getElementById('modal').classList.remove('show'); }
    function editarLivro(id) { window.location.href = '?editar=' + id; }

    function deletarLivro(id) {
        if (confirm('Tem certeza que deseja deletar este livro?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = '<input type="hidden" name="action" value="deletar"><input type="hidden" name="id_livro" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }

    function mostrarPreviewCapa(input) {
        const preview = document.getElementById('preview-capa');
        const aviso   = document.getElementById('capa-selecionada');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src           = e.target.result;
                preview.style.display = 'block';
                if (aviso) aviso.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function visualizarCapa(url, titulo) {
        document.getElementById('cover-modal-image').src = url;
        document.getElementById('cover-modal-title').textContent = 'Capa: ' + titulo;
        document.getElementById('cover-modal').classList.add('show');
    }

    function fecharCoverModal() { document.getElementById('cover-modal').classList.remove('show'); }

    window.addEventListener('click', e => {
        if (e.target === document.getElementById('modal'))       fecharModal();
        if (e.target === document.getElementById('cover-modal')) fecharCoverModal();
    });

    <?php if ($livro_edicao): ?>
    window.addEventListener('DOMContentLoaded', abrirModal);
    <?php endif; ?>
</script>
</body>
</html>