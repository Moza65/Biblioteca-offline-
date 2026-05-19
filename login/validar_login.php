<?php
// Inicia a sessão do usuário
session_start();
// Importa a conexão com o banco de dados
require_once '../config.php';
// Recebe os dados enviados pelo formulário
$email = $_POST['email'];
$senha = $_POST['senha'];
// Consulta SQL para procurar usuário com email e senha correspondentes
$sql = "SELECT * FROM usuario 
WHERE email = ? AND senha = ?";
// Prepara a consulta SQL
$stmt = $pdo->prepare($sql);
// Executa a consulta substituindo os ? pelos valores reais
$stmt->execute([$email, $senha]);
// Busca o usuário encontrado
$usuario = $stmt->fetch();
// Verifica se encontrou usuário
if ($usuario) {
 // Guarda os dados do usuário na sessão
    $_SESSION['usuario'] = $usuario;
 // Redireciona para o painel administrativo
    header("Location: ../adm/adm.php");
     // Encerra o script para evitar que o código abaixo seja executado
    exit();

} else {
    
        // Mostra mensagem de erro caso login falhe

  header("Location: login.php?erro=1");
    exit();
}
?>