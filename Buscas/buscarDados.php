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
}
?>