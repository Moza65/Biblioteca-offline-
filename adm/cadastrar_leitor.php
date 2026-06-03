<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Recolhe e limpa os dados do formulário
$nome     = trim($_POST['nome']     ?? '');
$email    = trim($_POST['email']    ?? '');
$nif      = trim($_POST['nif']      ?? '');
$telefone = trim($_POST['telefone'] ?? '');

// ── Validações básicas ───────────────────────────────────────────
if ($nome === '') {
    echo json_encode(['success' => false, 'message' => 'O nome é obrigatório.']);
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Introduza um e-mail válido.']);
    exit;
}

// numero_telefone é INT(9) na BD — aceita só dígitos, máx 9 caracteres
$telefoneLimpo = preg_replace('/\D/', '', $telefone); // remove tudo que não for dígito
if ($telefoneLimpo === '') {
    $telefoneLimpo = null; // permite NULL se o campo for opcional; ajuste conforme a BD
}

// NIF: obrigatório conforme o schema (NOT NULL)
if ($nif === '') {
    $nif = '000000000'; // valor padrão neutro; remova esta linha se quiser tornar obrigatório
}

// ── Verifica duplicados ──────────────────────────────────────────
try {
    $stmtCheck = $pdo->prepare("SELECT id FROM leitor WHERE email = ? OR nif = ?");
    $stmtCheck->execute([$email, $nif]);

    if ($stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Já existe um leitor com este e-mail ou NIF.']);
        exit;
    }
} catch (PDOException $e) {
    error_log('Erro ao verificar duplicados: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao verificar dados existentes.']);
    exit;
}

// ── Insere na tabela leitor ──────────────────────────────────────
// Colunas: id (AUTO), nome, email, nif, numero_telefone, data_leitor
try {
    $sql = $pdo->prepare(
        "INSERT INTO leitor (nome, email, nif, numero_telefone, data_leitor)
         VALUES (:nome, :email, :nif, :telefone, CURDATE())"
    );

    $sql->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':nif'      => $nif,
        ':telefone' => $telefoneLimpo !== null ? (int)$telefoneLimpo : 0,
    ]);

    $novoId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Leitor cadastrado com sucesso!',
        'id'      => $novoId,
    ]);

} catch (PDOException $e) {
    error_log('Erro ao cadastrar leitor: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao guardar o leitor. Tente novamente.']);
}