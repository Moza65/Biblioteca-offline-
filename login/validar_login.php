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

        header("Location: ../adm/adm.php");

    } elseif ($usuario['tipo_usuario'] == 'bibliotecario') {

        header("Location: ../bibliotec/bibliotec.php");

    }

    exit();

} else {

    // Login inválido
    header("Location: login.php?erro=1");
    exit();
}
?>