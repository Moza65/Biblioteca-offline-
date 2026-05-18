<?php

session_start();

require '../config.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuario 
WHERE email = ? AND senha = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$email, $senha]);

$usuario = $stmt->fetch();

if ($usuario) {

    $_SESSION['usuario'] = $usuario;

    header("Location: ../adm/adm.php");
    exit();

} else {

    echo "Email ou senha incorretos";

}
?>