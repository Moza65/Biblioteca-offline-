<?php
// config.php
// Conexão com o banco MySQL (XAMPP)

$pdo = new PDO(
  "mysql:host=localhost;dbname=biblioteca;charset=utf8mb4",
  "root",
  "",
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
