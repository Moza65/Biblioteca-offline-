<?php
require_once __DIR__ . '/common.php';
$configPath = __DIR__ . '/../config/settings.json';
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents($configPath), true) ?: [];
    $data['library_name']      = trim($_POST['library_name']      ?? $data['library_name']      ?? '');
    $data['contact_email']     = trim($_POST['contact_email']     ?? $data['contact_email']     ?? '');
    $data['opening_hours']     = trim($_POST['opening_hours']     ?? $data['opening_hours']     ?? '');
    $data['default_loan_days'] = (int)($_POST['default_loan_days'] ?? $data['default_loan_days'] ?? 7);
    $data['fine_per_day']      = floatval(str_replace(',', '.', ($_POST['fine_per_day'] ?? $data['fine_per_day'] ?? 0.0)));

    if (empty($data['library_name'])) {
        $errors[] = 'O nome da biblioteca é obrigatório.';
    }
    if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email de contato inválido.';
    }

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
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/adm/config.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="welcome-text">
                <div class="page-title-row">
                        <img src="../asset/icones/settings.svg" alt="" class="page-title-icon">
                        <h1>Configurações</h1>
                    </div>
                <p>Defina as configurações gerais da biblioteca</p>
            </div>
        </header>

        <div class="settings-wrapper">

            <!-- ALERTAS -->
            <?php if ($success): ?>
                <div class="alert success">Configurações salvas com sucesso!</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert error"><?php echo implode('<br>', $errors); ?></div>
            <?php endif; ?>

            <form method="post">

                <!-- SECÇÃO 1: Informações da Biblioteca -->
                <div class="settings-section">
                    <div class="settings-section-header">
                        <div class="settings-section-icon">
                            <img src="../asset/icones/livro-aberto.svg" alt="">
                        </div>
                        <div class="settings-section-title">
                            <h2>Informações da Biblioteca</h2>
                            <p>Dados de identificação e contacto público</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-field full">
                            <label for="library_name">Nome da Biblioteca</label>
                            <input
                                type="text"
                                id="library_name"
                                name="library_name"
                                placeholder="Ex: Biblioteca Municipal Pandora"
                                value="<?php echo htmlspecialchars($settings['library_name'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-field">
                            <label for="contact_email">Email de Contato</label>
                            <input
                                type="email"
                                id="contact_email"
                                name="contact_email"
                                placeholder="contato@biblioteca.com"
                                value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-field">
                            <label for="opening_hours">Horário de Funcionamento</label>
                            <input
                                type="text"
                                id="opening_hours"
                                name="opening_hours"
                                placeholder="Ex: Seg-Sex 08:00-18:00"
                                value="<?php echo htmlspecialchars($settings['opening_hours'] ?? ''); ?>"
                            >
                        </div>
                    </div>
                </div>

                <!-- SECÇÃO 2: Regras de Empréstimo -->
                <div class="settings-section">
                    <div class="settings-section-header">
                        <div class="settings-section-icon">
                            <img src="../asset/icones/file-warning.svg" alt="">
                        </div>
                        <div class="settings-section-title">
                            <h2>Regras de Empréstimo</h2>
                            <p>Prazos e valores de multa por atraso</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="default_loan_days">Prazo Padrão de Empréstimo</label>
                            <div class="input-prefix-wrapper">
                                <span class="input-prefix">dias</span>
                                <input
                                    type="number"
                                    id="default_loan_days"
                                    name="default_loan_days"
                                    min="1"
                                    max="365"
                                    value="<?php echo htmlspecialchars($settings['default_loan_days'] ?? 7); ?>"
                                >
                            </div>
                            <span class="field-hint">Número de dias antes da devolução obrigatória</span>
                        </div>

                        <div class="form-field">
                            <label for="fine_per_day">Multa por Dia de Atraso</label>
                            <div class="input-prefix-wrapper">
                                <span class="input-prefix">KZ</span>
                                <input
                                    type="text"
                                    id="fine_per_day"
                                    name="fine_per_day"
                                    placeholder="0,00"
                                    value="<?php echo htmlspecialchars(number_format($settings['fine_per_day'] ?? 0, 2, ',', '')); ?>"
                                >
                            </div>
                            <span class="field-hint">Valor cobrado por cada dia de atraso na devolução</span>
                        </div>
                    </div>
                </div>

                <!-- ACÇÕES -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        ✓ &nbsp;Salvar Configurações
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>
</body>
</html>
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
