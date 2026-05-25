<?php
require_once "../Buscas/Gerenciadores.php";
require_once __DIR__ . '/common.php';
<<<<<<< HEAD

$gerenciador           = new GerenciadorLivros();
$gerenciadorCategorias = new GerenciadorCategorias();
$categorias            = $gerenciadorCategorias->listarTodos();
$mensagem              = '';
$tipoAlerta            = '';

$uploadDir    = __DIR__ . '/../uploads/capas';
$uploadFolder = 'uploads/capas';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

function salvarImagem($file, $uploadDir, $uploadFolder) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Erro no envio da imagem.');
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) throw new Exception('O arquivo enviado não é uma imagem válida.');
    $allowedTypes = [IMAGETYPE_JPEG=>'jpg', IMAGETYPE_PNG=>'png', IMAGETYPE_GIF=>'gif', IMAGETYPE_WEBP=>'webp'];
    if (!isset($allowedTypes[$imageInfo[2]])) throw new Exception('Formato inválido. Use JPG, PNG, GIF ou WEBP.');
    $ext      = $allowedTypes[$imageInfo[2]];
    $filename = uniqid('capa_', true) . '.' . $ext;
    $dest     = $uploadDir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Falha ao salvar a imagem de capa.');
=======
$gerenciador = new GerenciadorLivros();
$gerenciadorCategorias = new GerenciadorCategorias();
$categorias = $gerenciadorCategorias->listarTodos();
$mensagem = '';

$uploadDir = __DIR__ . '/../uploads/capas';
$uploadFolder = 'uploads/capas';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function salvarImagem($file, $uploadDir, $uploadFolder) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no envio da imagem.');
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('O arquivo enviado não é uma imagem válida.');
    }

    $allowedTypes = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];

    if (!isset($allowedTypes[$imageInfo[2]])) {
        throw new Exception('Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.');
    }

    $ext = $allowedTypes[$imageInfo[2]];
    $filename = uniqid('capa_', true) . '.' . $ext;
    $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Falha ao salvar a imagem de capa.');
    }

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
    return $uploadFolder . '/' . $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $imagemPath = null;
<<<<<<< HEAD
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE)
            $imagemPath = salvarImagem($_FILES['imagem'], $uploadDir, $uploadFolder);

        if ($_POST['action'] === 'adicionar') {
            $gerenciador->adicionar($_POST['titulo'], $_POST['autor'], $_POST['quantidade'], $_POST['editora'], $_POST['edicao'], $imagemPath, !empty($_POST['categoria']) ? $_POST['categoria'] : null);
            $mensagem   = "Livro adicionado com sucesso!";
            $tipoAlerta = 'sucesso';
        } elseif ($_POST['action'] === 'deletar') {
            $gerenciador->deletar($_POST['id_livro']);
            $mensagem   = "Livro removido com sucesso!";
            $tipoAlerta = 'sucesso';
        } elseif ($_POST['action'] === 'atualizar') {
            $gerenciador->atualizar($_POST['id_livro'], $_POST['titulo'], $_POST['autor'], $_POST['quantidade'], $_POST['editora'], $_POST['edicao'], $imagemPath, !empty($_POST['categoria']) ? $_POST['categoria'] : null);
            $mensagem   = "Livro atualizado com sucesso!";
            $tipoAlerta = 'sucesso';
        }
    } catch (Exception $e) {
        $mensagem   = "Erro: " . $e->getMessage();
        $tipoAlerta = 'erro';
=======
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagemPath = salvarImagem($_FILES['imagem'], $uploadDir, $uploadFolder);
        }

        if ($_POST['action'] === 'adicionar') {
            $gerenciador->adicionar(
                $_POST['titulo'],
                $_POST['autor'],
                $_POST['quantidade'],
                $_POST['editora'],
                $_POST['edicao'],
                $imagemPath,
                !empty($_POST['categoria']) ? $_POST['categoria'] : null
            );
            $mensagem = "Livro adicionado com sucesso!";
        } elseif ($_POST['action'] === 'deletar') {
            $gerenciador->deletar($_POST['id_livro']);
            $mensagem = "Livro removido com sucesso!";
        } elseif ($_POST['action'] === 'atualizar') {
            $gerenciador->atualizar(
                $_POST['id_livro'],
                $_POST['titulo'],
                $_POST['autor'],
                $_POST['quantidade'],
                $_POST['editora'],
                $_POST['edicao'],
                $imagemPath,
                !empty($_POST['categoria']) ? $_POST['categoria'] : null
            );
            $mensagem = "Livro atualizado com sucesso!";
        }
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
    }
}

$livros = $gerenciador->listarTodos();
<<<<<<< HEAD
if (isset($_GET['busca'])) $livros = $gerenciador->buscar($_GET['busca']);

$livro_edicao = null;
if (isset($_GET['editar'])) $livro_edicao = $gerenciador->obterPorId($_GET['editar']);
?>
=======
if (isset($_GET['busca'])) {
    $livros = $gerenciador->buscar($_GET['busca']);
}

$livro_edicao = null;
if (isset($_GET['editar'])) {
    $livro_edicao = $gerenciador->obterPorId($_GET['editar']);
}
?>

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
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
=======
    <title>Gerenciar Livros - Biblioteca Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background-color: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 520px; max-height: calc(100vh - 60px); overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close-btn { background: none; border: none; font-size: 28px; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-grupo { display: flex; gap: 10px; margin-top: 20px; }
        .btn-grupo button { flex: 1; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .btn-primary { background-color: #6366f1; color: white; }
        .btn-primary:hover { background-color: #4f46e5; }
        .btn-secondary { background-color: #e5e7eb; color: #333; }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; }
        .btn-view { background-color: #10b981; color: white; }
        .btn-view:hover { background-color: #059669; }
        .livros-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .livros-table th { background-color: #f3f4f6; padding: 12px; text-align: left; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
        .livros-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        .livros-table tr:hover { background-color: #f9fafb; }
        .btn-acao { padding: 6px 12px; margin-right: 5px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-edit { background-color: #3b82f6; color: white; }
        .btn-delete { background-color: #ef4444; color: white; }
        .mensagem { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .mensagem.sucesso { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .mensagem.erro { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .btn-novo { background-color: #6366f1; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; margin-bottom: 20px; }
        .capa-thumb { width: 60px; height: 90px; object-fit: cover; border-radius: 6px; border: 1px solid #d1d5db; }
        .preview-capa { display: block; width: 100%; max-width: 180px; margin-top: 10px; border-radius: 8px; border: 1px solid #d1d5db; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Gerenciar Livros 📚</h1>
                    <p>Adicione, edite ou remova livros do acervo</p>
                </div>
            </header>

            <?php if ($mensagem): ?>
                <div class="mensagem <?php echo strpos($mensagem, 'Erro') !== false ? 'erro' : 'sucesso'; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <button class="btn-novo" onclick="abrirModal()">+ Adicionar Novo Livro</button>
            <div class="search-bar" style="margin-bottom: 20px;">
                <form method="GET" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="busca" placeholder="Buscar por título, autor ou editora..." 
                           value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">Buscar</button>
                    <?php if (isset($_GET['busca']) && $_GET['busca'] !== ''): ?>
                        <a href="livros.php" class="btn-secondary" style="padding: 10px 20px; text-decoration: none;">Limpar</a>
                    <?php endif; ?>
                </form>
            </div>

>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
            <table class="livros-table">
                <thead>
                    <tr>
                        <th>Capa</th>
<<<<<<< HEAD
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoria</th>
                        <th>Qtd.</th>
=======
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoria</th>
                        <th>Quantidade</th>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
                        <th>Editora</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
<<<<<<< HEAD
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
=======
                    <?php foreach ($livros as $livro): ?>
                        <tr>
                            <td>
                                <?php if (!empty($livro['imagem'])): ?>
                                    <img src="../<?php echo htmlspecialchars($livro['imagem']); ?>" alt="Capa" class="capa-thumb">
                                <?php else: ?>
                                    <img src="../asset/icones/book-open.svg" alt="Sem capa" class="capa-thumb">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($livro['id_livro']); ?></td>
                            <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                            <td><?php echo htmlspecialchars($livro['categoria_nome'] ?? 'Sem categoria'); ?></td>
                            <td><?php echo htmlspecialchars($livro['quantidade']); ?></td>
                            <td><?php echo htmlspecialchars($livro['editora']); ?></td>
                            <td>
                                <?php if (!empty($livro['imagem'])): ?>
                                    <button class="btn-acao btn-view" onclick="visualizarCapa('../<?php echo htmlspecialchars($livro['imagem']); ?>', '<?php echo addslashes(htmlspecialchars($livro['titulo'])); ?>')">Ver capa</button>
                                <?php endif; ?>
                                <button class="btn-acao btn-edit" onclick="editarLivro(<?php echo (int)$livro['id_livro']; ?>)">Editar</button>
                                <button class="btn-acao btn-delete" onclick="deletarLivro(<?php echo (int)$livro['id_livro']; ?>)">Deletar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>

    <div id="modal" class="modal <?php echo $livro_edicao ? 'show' : ''; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-titulo"><?php echo $livro_edicao ? 'Editar Livro' : 'Adicionar Novo Livro'; ?></h2>
                <button class="close-btn" onclick="fecharModal()">&times;</button>
            </div>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="form-action" value="<?php echo $livro_edicao ? 'atualizar' : 'adicionar'; ?>">
                <input type="hidden" name="id_livro" id="form-id" value="<?php echo htmlspecialchars($livro_edicao['id_livro'] ?? ''); ?>">
                <input type="hidden" name="imagem_atual" id="imagem-atual" value="<?php echo htmlspecialchars($livro_edicao['imagem'] ?? ''); ?>">
<<<<<<< HEAD

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
        document.getElementById('cover-modal-image').src   = url;
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
=======
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($livro_edicao['titulo'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="autor">Autor:</label>
                    <input type="text" id="autor" name="autor" required value="<?php echo htmlspecialchars($livro_edicao['autor'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" required value="<?php echo htmlspecialchars($livro_edicao['quantidade'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="editora">Editora:</label>
                    <input type="text" id="editora" name="editora" required value="<?php echo htmlspecialchars($livro_edicao['editora'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edicao">Edição:</label>
                    <input type="text" id="edicao" name="edicao" value="<?php echo htmlspecialchars($livro_edicao['edicao'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="categoria">Categoria:</label>
                    <select id="categoria" name="categoria">
                        <option value="">Sem categoria</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo (int)$categoria['id_categoria']; ?>" <?php echo isset($livro_edicao['categoria_id']) && $livro_edicao['categoria_id'] == $categoria['id_categoria'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($categoria['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="imagem">Capa do Livro:</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*" onchange="mostrarPreviewCapa(this)" />

                    <div id="preview-capa-container" style="margin-top:10px;">
                        <?php if (!empty($livro_edicao['imagem'])): ?>
                            <img src="../<?php echo htmlspecialchars($livro_edicao['imagem']); ?>" alt="Capa atual" class="preview-capa" id="preview-capa" />
                        <?php else: ?>
                            <img src="" alt="Pré-visualização" class="preview-capa" id="preview-capa" style="display:none;" />
                        <?php endif; ?>
                    </div>

                    <div id="capa-selecionada" style="margin-top:10px; font-size: 13px; color: #1d4ed8; display: none;" >
                        Nova capa selecionada.
                    </div>
                    <div style="margin-top:10px; font-size: 13px; color: #374151;">
                        Selecione uma nova capa e clique em "<?php echo $livro_edicao ? 'Salvar alterações' : 'Salvar'; ?>" para confirmar.
                    </div>
                </div>

                <div class="btn-grupo">
                    <button type="submit" id="btn-salvar-livro" class="btn-primary"><?php echo $livro_edicao ? 'Salvar alterações' : 'Salvar'; ?></button>
                    <button type="button" class="btn-secondary" onclick="fecharModal()">Cancelar</button>
                </div>

            </form>
        </div>
    </div>

    <div id="cover-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="cover-modal-title">Visualizar Capa</h2>
                <button class="close-btn" onclick="fecharCoverModal()">&times;</button>
            </div>
            <div style="text-align: center;">
                <img id="cover-modal-image" src="" alt="Capa do livro" style="max-width: 100%; height: auto; border-radius: 8px;" />
            </div>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById('modal').classList.add('show');
        }

        function fecharModal() {
            document.getElementById('modal').classList.remove('show');
        }

        function editarLivro(id) {
            window.location.href = '?editar=' + id;
        }

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
            const aviso = document.getElementById('capa-selecionada');
            const botaoSalvar = document.getElementById('btn-salvar-livro');

            if (!input || !preview) return;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (aviso) {
                        aviso.textContent = 'Capa selecionada. Clique em "<?php echo $livro_edicao ? 'Salvar alterações' : 'Salvar'; ?>" para confirmar.';
                        aviso.style.display = 'block';
                    }
                    if (botaoSalvar) {
                        botaoSalvar.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }


        window.onclick = function(event) {
            const modal = document.getElementById('modal');
            const coverModal = document.getElementById('cover-modal');
            if (event.target === modal) {
                fecharModal();
            }
            if (event.target === coverModal) {
                fecharCoverModal();
            }
        }

        function visualizarCapa(url, titulo) {
            const modal = document.getElementById('cover-modal');
            const image = document.getElementById('cover-modal-image');
            const title = document.getElementById('cover-modal-title');
            if (!modal || !image || !title) return;
            image.src = url;
            title.textContent = 'Capa: ' + titulo;
            modal.classList.add('show');
        }

        function fecharCoverModal() {
            const modal = document.getElementById('cover-modal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        window.addEventListener('load', function() {
            const modal = document.getElementById('modal');
            if (modal && modal.classList.contains('show')) {
                const content = modal.querySelector('.modal-content');
                if (content) {
                    content.scrollTop = 0;
                }
            }
        });


        <?php if ($livro_edicao): ?>
            window.addEventListener('DOMContentLoaded', function() {
                abrirModal();
            });
        <?php endif; ?>
    </script>
</body>
</html>
>>>>>>> 19f5af7ac05a31e6793070d3ef8bceaf6dc13b3c
