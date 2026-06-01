<?php
// Inicia a sessão
session_start();
// Destroi todos os dados da sessão
session_destroy();
// Redireciona para o login
header("Location: ../login/login.php");
exit();
?>
