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
        $sql = $pdo->prepare("SELECT * FROM livro WHERE titulo = ? || autor = ?  ");
        $sql->execute([$value,$value]);
        $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(isset($sql) and !empty($sql)){
            return $sql;
        }else{
            return "Nada";
        }
    }
    public function GetReady($value){
        global $pdo;
        $sql = $pdo->prepare("SELECT * FROM leitor WHERE nome  = ? ");
        $sql->execute([$value]);
        $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(isset($sql) and !empty($sql)){
            return $sql;
        }
    }
    public function GetBroard(){
        global $pdo;
        $sql  = "SELECT leitor.*,livro.*,emprestimo.*,usuario.* FROM emprestimo 
        join leitor on emprestimo.id_emprestimo_leitor = leitor.id
        join livro on emprestimo.fk_Livro_id_livro = livro.id_livro
        join usuario on emprestimo.fk_Usuario_id_usuario = usuario.id_usuario
        WHERE 1 ";
        $sql = $pdo->prepare($sql);
        $sql->execute();
        $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(isset($sql) and !empty($sql)){
            return $sql;
        }
    }

    //CRIAR A FUNÇÁO QUE RESPONDE A PESQUISA DO USUÁRIO
    public function ShowSerach($value){
        if(mb_strtolower($value) == "empréstimo" ||  mb_strtolower($value) == "emprestimo" || mb_strtolower($value) == "impréstimo" || mb_strtolower($value) == "imprestimo"){
            if($this->GetBroard() and is_array($this->GetBroard())){
                return $this->GetBroard();
                exit();
            }else{
                return "Pesquisa de empréstino não encontrado";
                exit();
            }
        }else{
            if($this->GetReady($value) and !empty( $this->GetReady($value))){
                return $this->GetReady($value);
                exit();
            }elseif($this->GetBooks($value) and !empty( $this->GetBooks($value))) {
                return $this->GetBooks($value);
                exit();
            }else{
                return "Pesquisa não encontrada ";
                exit();
            }
        }
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