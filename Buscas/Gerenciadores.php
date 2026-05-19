<?php
require_once "../config.php";

class GerenciadorLivros {
    public function __construct() {
        $this->garantirColunaImagem();
        $this->garantirColunaCategoria();
    }

    private function garantirColunaImagem() {
        global $pdo;
        $col = $pdo->query("SHOW COLUMNS FROM livro LIKE 'imagem'")->fetch(PDO::FETCH_ASSOC);
        if (!$col) {
            $pdo->exec("ALTER TABLE livro ADD COLUMN imagem VARCHAR(255) DEFAULT NULL");
        }
    }

    private function garantirColunaCategoria() {
        global $pdo;
        $col = $pdo->query("SHOW COLUMNS FROM livro LIKE 'fk_categoria_id'")->fetch(PDO::FETCH_ASSOC);
        if (!$col) {
            $pdo->exec("ALTER TABLE livro ADD COLUMN fk_categoria_id INT DEFAULT NULL");
        }
    }

    public function listarTodos() {
        global $pdo;
        $sql = $pdo->prepare("SELECT l.*, c.nome AS categoria_nome FROM livro l LEFT JOIN categoria c ON l.fk_categoria_id = c.id_categoria ORDER BY l.id_livro DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($id) {
        global $pdo;
        $sql = $pdo->prepare("SELECT l.*, c.nome AS categoria_nome, c.id_categoria AS categoria_id FROM livro l LEFT JOIN categoria c ON l.fk_categoria_id = c.id_categoria WHERE l.id_livro = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function adicionar($titulo, $autor, $quantidade, $editora, $edicao, $imagem = null, $categoriaId = null) {
        global $pdo;
        $sql = $pdo->prepare("INSERT INTO livro (titulo, autor, quantidade, editora, edicao, imagem, fk_categoria_id, data_livro) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $sql->execute([$titulo, $autor, $quantidade, $editora, $edicao, $imagem, $categoriaId, date('Y-m-d')]);
    }

    public function atualizar($id, $titulo, $autor, $quantidade, $editora, $edicao, $imagem = null, $categoriaId = null) {
        global $pdo;
        if ($imagem !== null) {
            $sql = $pdo->prepare("UPDATE livro SET titulo = ?, autor = ?, quantidade = ?, editora = ?, edicao = ?, imagem = ?, fk_categoria_id = ? WHERE id_livro = ?");
            return $sql->execute([$titulo, $autor, $quantidade, $editora, $edicao, $imagem, $categoriaId, $id]);
        }

        $sql = $pdo->prepare("UPDATE livro SET titulo = ?, autor = ?, quantidade = ?, editora = ?, edicao = ?, fk_categoria_id = ? WHERE id_livro = ?");
        return $sql->execute([$titulo, $autor, $quantidade, $editora, $edicao, $categoriaId, $id]);
    }

    public function deletar($id) {
        global $pdo;
        $sql = $pdo->prepare("DELETE FROM livro WHERE id_livro = ?");
        return $sql->execute([$id]);
    }

    public function buscar($termo) {
        global $pdo;
        $termo = "%$termo%";
        $sql = $pdo->prepare("SELECT l.*, c.nome AS categoria_nome FROM livro l LEFT JOIN categoria c ON l.fk_categoria_id = c.id_categoria WHERE l.titulo LIKE ? OR l.autor LIKE ? OR l.editora LIKE ? ORDER BY l.id_livro DESC");
        $sql->execute([$termo, $termo, $termo]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterTotal() {
        global $pdo;
        $sql = $pdo->prepare("SELECT COUNT(*) as total FROM livro");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

class GerenciadorCategorias {
    public function __construct() {
        $this->garantirTabela();
    }

    private function garantirTabela() {
        global $pdo;
        $pdo->exec("CREATE TABLE IF NOT EXISTS categoria (
            id_categoria INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL UNIQUE,
            descricao VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function listarTodos() {
        global $pdo;
        $this->garantirTabela();
        $sql = $pdo->prepare("SELECT * FROM categoria ORDER BY id_categoria DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($id) {
        global $pdo;
        $this->garantirTabela();
        $sql = $pdo->prepare("SELECT * FROM categoria WHERE id_categoria = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function adicionar($nome, $descricao) {
        global $pdo;
        $this->garantirTabela();
        $sql = $pdo->prepare("INSERT INTO categoria (nome, descricao) VALUES (?, ?)");
        return $sql->execute([$nome, $descricao]);
    }

    public function atualizar($id, $nome, $descricao) {
        global $pdo;
        $this->garantirTabela();
        $sql = $pdo->prepare("UPDATE categoria SET nome = ?, descricao = ? WHERE id_categoria = ?");
        return $sql->execute([$nome, $descricao, $id]);
    }

    public function deletar($id) {
        global $pdo;
        $this->garantirTabela();
        $sql = $pdo->prepare("DELETE FROM categoria WHERE id_categoria = ?");
        return $sql->execute([$id]);
    }
}

class GerenciadorDevolucoes {
    public function listarPendentes() {
        global $pdo;
        $sql = $pdo->prepare("SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, e.estado, COALESCE(l.nome, u.nome, '-') AS leitor, li.titulo AS titulo_livro
                             FROM emprestimo e
                             LEFT JOIN leitor l ON e.id_emprestimo_leitor = l.id
                             LEFT JOIN usuario u ON e.fk_Usuario_id_usuario = u.id_usuario
                             LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                             LEFT JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                             WHERE d.id_emprestimo IS NULL
                             ORDER BY e.data_prevista ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarDevolvidos() {
        global $pdo;
        $sql = $pdo->prepare("SELECT e.id_emprestimo, e.data_emprestimo, e.data_prevista, e.estado, COALESCE(l.nome, u.nome, '-') AS leitor, li.titulo AS titulo_livro, d.data_devolucao
                             FROM emprestimo e
                             LEFT JOIN leitor l ON e.id_emprestimo_leitor = l.id
                             LEFT JOIN usuario u ON e.fk_Usuario_id_usuario = u.id_usuario
                             LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro
                             INNER JOIN devolucao d ON e.id_emprestimo = d.id_emprestimo
                             ORDER BY d.data_devolucao DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoDevolvido($id_emprestimo) {
        global $pdo;
        $pdo->beginTransaction();
        try {
            $nextId = $pdo->query("SELECT IFNULL(MAX(id_devolucao), 0) + 1 FROM devolucao")->fetchColumn();
            $sql = $pdo->prepare("INSERT INTO devolucao (id_devolucao, id_emprestimo, data_devolucao) VALUES (?, ?, ?)");
            $sql->execute([$nextId, $id_emprestimo, date('Y-m-d')]);

            $sql = $pdo->prepare("UPDATE emprestimo SET estado = 0 WHERE id_emprestimo = ?");
            $sql->execute([$id_emprestimo]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}

class GerenciadorLeitores {
    public function listarTodos() {
        global $pdo;
        $sql = $pdo->prepare("SELECT * FROM leitor ORDER BY id DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($id) {
        global $pdo;
        $sql = $pdo->prepare("SELECT * FROM leitor WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function obterTotal() {
        global $pdo;
        $sql = $pdo->prepare("SELECT COUNT(*) as total FROM leitor");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

class GerenciadorEmprestimos {
    public function listarTodos() {
        global $pdo;
        $sql = $pdo->prepare("SELECT e.*, li.titulo as titulo_livro FROM emprestimo e LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro ORDER BY e.data_emprestimo DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtivos() {
        global $pdo;
        $sql = $pdo->prepare("SELECT e.*, li.titulo as titulo_livro FROM emprestimo e LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro WHERE e.status = 'ativo' ORDER BY e.data_emprestimo DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtrasados() {
        global $pdo;
        $sql = $pdo->prepare("SELECT e.*, li.titulo as titulo_livro FROM emprestimo e LEFT JOIN livro li ON e.fk_Livro_id_livro = li.id_livro WHERE e.status = 'ativo' AND e.data_prevista < NOW() ORDER BY e.data_prevista ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterTotal() {
        global $pdo;
        $sql = $pdo->prepare("SELECT COUNT(*) as total FROM emprestimo");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

class GerenciadorReservas {
    public function listarTodas() {
        global $pdo;
        $sql = $pdo->prepare("SELECT r.*, li.titulo as titulo_livro FROM reserva r LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro ORDER BY r.data_reserva DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtivas() {
        global $pdo;
        $sql = $pdo->prepare("SELECT r.*, li.titulo as titulo_livro FROM reserva r LEFT JOIN livro li ON r.fk_Livro_id_livro = li.id_livro WHERE r.prazo_reserva >= NOW() ORDER BY r.prazo_reserva ASC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterTotal() {
        global $pdo;
        $sql = $pdo->prepare("SELECT COUNT(*) as total FROM reserva");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
