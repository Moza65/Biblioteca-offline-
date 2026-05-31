<?php 
// Inicia a sessão do usuário
session_start();

// Importa a conexão com o banco de dados
require_once '../config.php';

// Recebe os dados enviados pelo formulário
$email = $_POST['email'];
$senha = $_POST['senha'];

// Consulta SQL
$sql = "SELECT * FROM usuario 
WHERE email = ? AND senha = ?";

// Prepara a consulta
$stmt = $pdo->prepare($sql);

// Executa
$stmt->execute([$email, $senha]);
// Busca usuário
$usuario = $stmt->fetch();

// Verifica se encontrou
if ($usuario) {

    // Guarda dados na sessão
    $_SESSION['usuario'] = $usuario;

    // Verifica tipo de usuário
    if ($usuario['tipo_usuario'] == 'admin') {
        $_SESSION["id_usuario"] =   $usuario["id_usuario"];
        $_SESSION["nome"] =   $usuario["nome"];
        $_SESSION["cantato"] =   $usuario["contato"];
        $_SESSION["tipo_usuario"] =   $usuario["tipo_usuario"];
        $_SESSION["email"] =   $usuario["email"];
        header("Location: ../adm/adm.php");

    } elseif ($usuario['tipo_usuario'] == 'bibliotecario') {
        $_SESSION["id_usuario"] =   $usuario["id_usuario"];
        $_SESSION["nome"] =   $usuario["nome"];
        $_SESSION["cantato"] =   $usuario["contato"];
        $_SESSION["tipo_usuario"] =   $usuario["tipo_usuario"];
        $_SESSION["email"] =   $usuario["email"];
        header("Location: ../bibliotec/bibliotec.php");

    }

    exit();

} else {

    // Login inválido
    header("Location: login.php?erro=1");
    exit();
}
?>