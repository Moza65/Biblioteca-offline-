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
    <link rel="stylesheet" href="../asset/style/adm/modal_livros.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

    </style>
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
            <div class="topbar-actions">
                <button class="btn-primary" onclick="abrirModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar Livro
                </button>
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
                <span class="count-badge">
                    <?php echo count($livros); ?> livro<?php echo count($livros) !== 1 ? 's' : ''; ?>
                </span>
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
                                            <button class="btn-acao btn-view"
                                                onclick="visualizarCapa('../<?php echo htmlspecialchars($livro['imagem']); ?>', '<?php echo addslashes(htmlspecialchars($livro['titulo'])); ?>')">
                                                Ver capa
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-acao btn-edit"
                                            onclick="editarLivro(<?php echo (int)$livro['id_livro']; ?>)">
                                            Editar
                                        </button>
                                        <button class="btn-acao btn-delete"
                                            onclick="deletarLivro(<?php echo (int)$livro['id_livro']; ?>)">
                                            Deletar
                                        </button>
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

<!-- ══════════════ MODAL ADICIONAR / EDITAR ══════════════ -->
<div id="modal" class="modal <?php echo $livro_edicao ? 'show' : ''; ?>">
    <div class="modal-content">

        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-header-icon">
                    <?php if ($livro_edicao): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="modal-header-text">
                    <h2><?php echo $livro_edicao ? 'Editar Livro' : 'Adicionar Novo Livro'; ?></h2>
                    <p><?php echo $livro_edicao ? 'Atualize as informações do livro' : 'Preencha os dados para adicionar ao acervo'; ?></p>
                </div>
            </div>
            <button class="close-btn" onclick="fecharModal()" aria-label="Fechar">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action"       id="form-action"   value="<?php echo $livro_edicao ? 'atualizar' : 'adicionar'; ?>">
                <input type="hidden" name="id_livro"     id="form-id"       value="<?php echo htmlspecialchars($livro_edicao['id_livro'] ?? ''); ?>">
                <input type="hidden" name="imagem_atual" id="imagem-atual"  value="<?php echo htmlspecialchars($livro_edicao['imagem'] ?? ''); ?>">

                <!-- Secção: Identificação -->
                <div class="form-section">
                    <div class="form-section-title">Identificação</div>
                    <div class="modal-form-row">
                        <div class="modal-form-group">
                            <label for="titulo">Título <span class="req">*</span></label>
                            <input type="text" id="titulo" name="titulo"
                                   placeholder="Ex: Dom Casmurro" required
                                   value="<?php echo htmlspecialchars($livro_edicao['titulo'] ?? ''); ?>">
                        </div>
                        <div class="modal-form-group">
                            <label for="autor">Autor <span class="req">*</span></label>
                            <input type="text" id="autor" name="autor"
                                   placeholder="Ex: Machado de Assis" required
                                   value="<?php echo htmlspecialchars($livro_edicao['autor'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label for="categoria">Categoria</label>
                        <select id="categoria" name="categoria">
                            <option value="">— Sem categoria —</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo (int)$cat['id_categoria']; ?>"
                                    <?php echo isset($livro_edicao['categoria_id']) && $livro_edicao['categoria_id'] == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Secção: Publicação -->
                <div class="form-section">
                    <div class="form-section-title">Publicação</div>
                    <div class="modal-form-row">
                        <div class="modal-form-group">
                            <label for="editora">Editora <span class="req">*</span></label>
                            <input type="text" id="editora" name="editora"
                                   placeholder="Ex: Companhia das Letras" required
                                   value="<?php echo htmlspecialchars($livro_edicao['editora'] ?? ''); ?>">
                        </div>
                        <div class="modal-form-group">
                            <label for="edicao">Edição</label>
                            <input type="text" id="edicao" name="edicao"
                                   placeholder="Ex: 1ª Edição"
                                   value="<?php echo htmlspecialchars($livro_edicao['edicao'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="modal-form-group" style="max-width:180px;">
                        <label for="quantidade">Quantidade em acervo <span class="req">*</span></label>
                        <input type="number" id="quantidade" name="quantidade"
                               min="0" placeholder="0" required
                               value="<?php echo htmlspecialchars($livro_edicao['quantidade'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Secção: Capa -->
                <div class="form-section">
                    <div class="form-section-title">Capa do Livro</div>
                    <div class="upload-area" id="upload-area">
                        <input type="file" id="imagem" name="imagem"
                               accept="image/*" onchange="mostrarPreviewCapa(this)">
                        <img src="" alt="" class="preview-capa" id="preview-capa" style="display:none;">
                        <?php if (!empty($livro_edicao['imagem'])): ?>
                            <img src="../<?php echo htmlspecialchars($livro_edicao['imagem']); ?>"
                                 alt="Capa atual" class="preview-capa" id="preview-capa-atual">
                        <?php endif; ?>
                        <div class="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="upload-text">
                            <strong id="upload-label">
                                <?php echo !empty($livro_edicao['imagem']) ? 'Clique para alterar a capa' : 'Clique para carregar a capa'; ?>
                            </strong>
                            <span>JPG, PNG, GIF ou WEBP</span>
                        </div>
                    </div>
                    <span class="capa-selecionada-aviso" id="capa-selecionada">✓ Nova capa selecionada</span>
                </div>

                <!-- Ações -->
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 3v4H8V3M12 12v5m-2-2h4"/>
                        </svg>
                        <?php echo $livro_edicao ? 'Salvar alterações' : 'Adicionar Livro'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════ MODAL VER CAPA ══════════════ -->
<div id="cover-modal" class="modal">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="modal-header-text">
                    <h2 id="cover-modal-title">Capa do Livro</h2>
                    <p>Visualização da imagem de capa</p>
                </div>
            </div>
            <button class="close-btn" onclick="fecharCoverModal()" aria-label="Fechar">×</button>
        </div>
        <div class="modal-body" style="padding-top:0; text-align:center;">
            <img id="cover-modal-image" src="" alt="Capa">
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
    function abrirModal() { document.getElementById('modal').classList.add('show'); }
    function fecharModal() { document.getElementById('modal').classList.remove('show'); }
    function editarLivro(id) { window.location.href = '?editar=' + id; }

    function deletarLivro(id) {
        if (confirm('Tem certeza que deseja deletar este livro?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML =
                '<input type="hidden" name="action"   value="deletar">' +
                '<input type="hidden" name="id_livro" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }

    function mostrarPreviewCapa(input) {
        const preview = document.getElementById('preview-capa');
        const aviso   = document.getElementById('capa-selecionada');
        const label   = document.getElementById('upload-label');
        const atual   = document.getElementById('preview-capa-atual');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src           = e.target.result;
                preview.style.display = 'block';
                if (atual)  atual.style.display = 'none';
                if (aviso)  aviso.style.display  = 'block';
                if (label)  label.textContent    = input.files[0].name;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function visualizarCapa(url, titulo) {
        document.getElementById('cover-modal-image').src = url;
        document.getElementById('cover-modal-title').textContent = titulo;
        document.getElementById('cover-modal').classList.add('show');
    }

    function fecharCoverModal() {
        document.getElementById('cover-modal').classList.remove('show');
    }

    window.addEventListener('click', e => {
        if (e.target === document.getElementById('modal'))       fecharModal();
        if (e.target === document.getElementById('cover-modal')) fecharCoverModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { fecharModal(); fecharCoverModal(); }
    });

    <?php if ($livro_edicao): ?>
    window.addEventListener('DOMContentLoaded', abrirModal);
    <?php endif; ?>
</script>
</body>
</html>