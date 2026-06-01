<?php
require_once "../config.php"; 
class BuscarDados{
    public function QuantidadeLivro(){
        global $pdo;
        $sql = $pdo->prepare("SELECT count(id_livro) FROM livro WHERE 1");
        if($sql->execute()){
            $sql = $sql->fetch(PDO::FETCH_ASSOC);
            return $sql;
        }else{
            return "Erro ao buscar a quantidade dos livros!";
        }

    }
    
    public function QuantidadeLeitores(){
        global $pdo;
        $sql = $pdo->prepare("SELECT count(id) FROM leitor WHERE 1");
        if($sql->execute()){
            $sql = $sql->fetch(PDO::FETCH_ASSOC);
            return $sql;
        }else{
            return "Erro ao buscar a quantidade dos leitores!";
        }

    }
    public function Quantidadeemprestimo(){
        global $pdo;
        $sql = $pdo->prepare("SELECT count(id_emprestimo) FROM emprestimo WHERE 1");
        if($sql->execute()){
            $sql = $sql->fetch(PDO::FETCH_ASSOC);
            return $sql;
        }else{
            return "Erro ao buscar a quantidade dos emprestimos!";
        }

    }

    public function QuantidadeReserva(){
        global $pdo;
        $sql = $pdo->prepare("SELECT count(id_reserva) FROM  reserva WHERE 1");
        if($sql->execute()){
            $sql = $sql->fetch(PDO::FETCH_ASSOC);
            return $sql;
        }else{
            return "Erro ao buscar a quantidade dos emprestimos!";
        }

    }

    public function GetBooks($value){
        global $pdo;
        $value = '%' . $value . '%';
        $sql = $pdo->prepare("SELECT * FROM livro WHERE titulo LIKE ? OR autor LIKE ? OR editora LIKE ?");
        $sql->execute([$value, $value, $value]);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return !empty($result) ? $result : [];
    }

    public function GetReady($value){
        global $pdo;
        $value = '%' . $value . '%';
        $sql = $pdo->prepare("SELECT * FROM leitor WHERE nome LIKE ? OR email LIKE ? OR numero_telefone LIKE ? OR nif LIKE ?");
        $sql->execute([$value, $value, $value, $value]);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return !empty($result) ? $result : [];
    }

    public function GetBroard($value = null){
        global $pdo;
        $query = "SELECT emprestimo.id_emprestimo, emprestimo.data_emprestimo, emprestimo.data_prevista, livro.titulo AS titulo_livro, leitor.nome AS leitor, usuario.nome AS bibliotecario, emprestimo.estado
                  FROM emprestimo
                  LEFT JOIN leitor ON emprestimo.id_emprestimo_leitor = leitor.id
                  LEFT JOIN livro ON emprestimo.fk_Livro_id_livro = livro.id_livro
                  LEFT JOIN usuario ON emprestimo.fk_Usuario_id_usuario = usuario.id_usuario
                  WHERE 1";
        $params = [];

        if ($value !== null) {
            $term = '%' . $value . '%';
            $query .= " AND (livro.titulo LIKE ? OR leitor.nome LIKE ? OR usuario.nome LIKE ? OR emprestimo.estado LIKE ? OR CAST(emprestimo.id_emprestimo AS CHAR) LIKE ? )";
            $params = [$term, $term, $term, $term, $term];
        }

        $sql = $pdo->prepare($query);
        $sql->execute($params);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return !empty($result) ? $result : [];
    }

    //CRIAR A FUNÇÁO QUE RESPONDE A PESQUISA DO USUÁRIO
    public function ShowSerach($value){
        $value = trim($value);
        if ($value === '') {
            return ['type' => 'empty', 'items' => []];
        }

        $lower = mb_strtolower($value);
        $isLoanSearch = str_contains($lower, 'emprest') || str_contains($lower, 'devol') || str_contains($lower, 'reserva');

        if ($isLoanSearch) {
            $loans = $this->GetBroard($value);
            if (!empty($loans)) {
                return ['type' => 'emprestimo', 'items' => $loans];
            }
        }

        $readers = $this->GetReady($value);
        if (!empty($readers)) {
            return ['type' => 'leitor', 'items' => $readers];
        }

        $books = $this->GetBooks($value);
        if (!empty($books)) {
            return ['type' => 'livro', 'items' => $books];
        }

        $loans = $this->GetBroard($value);
        if (!empty($loans)) {
            return ['type' => 'emprestimo', 'items' => $loans];
        }

        return ['type' => 'notfound', 'message' => 'Pesquisa não encontrada.'];
    }
}

//Chamar a classe
/*
$callClass = new BuscarDados();
echo "<pre>";
print_r($callClass->GetBroard());
echo "</pre>";
exit();
*/
?>