<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login/login.php');
    exit();
}

$usuario = $_SESSION['usuario'];

$currentPage = basename($_SERVER['PHP_SELF']);
?>