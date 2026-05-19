<?php
// Tenta realizar a conexão com o banco de dados
try {
    // Cria uma nova conexão PDO com o MySQL
    $pdo = new PDO(
        // Dados da conexão:
        // localhost -> servidor local
        // port=3307 -> porta do MySQL
        // dbname=biblioteca -> nome do banco
        // charset=utf8mb4 -> suporta acentos e caracteres especiais
        "mysql:host=localhost;port=3306;dbname=bd_biblioteca;charset=utf8mb4",
         // Usuário do MySQL
        "root",
        // Senha do MySQL
        "",
        // Configuração para mostrar erros do banco
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
// Captura erros caso a conexão falhe
} catch (PDOException $e) {
// Mostra a mensagem de erro
    echo "Erro na conexão: " . $e->getMessage();
}
