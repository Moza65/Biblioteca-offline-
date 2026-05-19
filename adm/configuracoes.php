<?php
require_once __DIR__ . '/common.php';
$configPath = __DIR__ . '/../config/settings.json';
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents($configPath), true) ?: [];
    $data['library_name'] = trim($_POST['library_name'] ?? $data['library_name'] ?? '');
    $data['contact_email'] = trim($_POST['contact_email'] ?? $data['contact_email'] ?? '');
    $data['opening_hours'] = trim($_POST['opening_hours'] ?? $data['opening_hours'] ?? '');
    $data['default_loan_days'] = (int)($_POST['default_loan_days'] ?? $data['default_loan_days'] ?? 7);
    $data['fine_per_day'] = floatval(str_replace(',', '.', ($_POST['fine_per_day'] ?? $data['fine_per_day'] ?? 0.0)));

    if (empty($data['library_name'])) $errors[] = 'O nome da biblioteca é obrigatório.';
    if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email de contato inválido.';

    if (empty($errors)) {
        file_put_contents($configPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $success = true;
    }
}

$settings = json_decode(file_get_contents($configPath), true) ?: [];
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurações - Pandora</title>
    <link rel="stylesheet" href="../asset/style/adm.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="topbar">
                <div class="welcome-text">
                    <h1>Configurações ⚙️</h1>
                    <p>Defina as configurações gerais da biblioteca</p>
                </div>
            </header>

            <section class="card">
                <?php if ($success): ?>
                    <div class="alert success">Configurações salvas com sucesso.</div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert error"><?php echo implode('<br>', $errors); ?></div>
                <?php endif; ?>

                <form method="post" class="form-settings">
                    <label>Nome da Biblioteca</label>
                    <input type="text" name="library_name" value="<?php echo htmlspecialchars($settings['library_name'] ?? ''); ?>">

                    <label>Email de Contato</label>
                    <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">

                    <label>Horário de Funcionamento</label>
                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($settings['opening_hours'] ?? ''); ?>">

                    <label>Prazo Padrão de Empréstimo (dias)</label>
                    <input type="number" name="default_loan_days" min="1" value="<?php echo htmlspecialchars($settings['default_loan_days'] ?? 7); ?>">

                    <label>Multa por Dia (R$)</label>
                    <input type="text" name="fine_per_day" value="<?php echo htmlspecialchars($settings['fine_per_day'] ?? '0.00'); ?>">

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Salvar</button>
                        <a href="adm.php" class="btn">Voltar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
