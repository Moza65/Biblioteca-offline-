<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login/login.php");
    exit();
}

$mensagem   = '';
$tipoAlerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome         = trim($_POST['nome']         ?? '');
    $email        = trim($_POST['email']        ?? '');
    $senha        = trim($_POST['senha']        ?? '');
    $senha_conf   = trim($_POST['senha_conf']   ?? '');
    $contacto     = trim($_POST['contacto']     ?? '');
    $data_usuario = trim($_POST['data_usuario'] ?? '');

    // Validações básicas
    if (!$nome || !$email || !$senha || !$contacto || !$data_usuario) {
        $mensagem   = 'Preencha todos os campos obrigatórios.';
        $tipoAlerta = 'erro';
    } elseif ($senha !== $senha_conf) {
        $mensagem   = 'As senhas não coincidem.';
        $tipoAlerta = 'erro';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem   = 'Endereço de e-mail inválido.';
        $tipoAlerta = 'erro';
    } else {
        try {
            // Verificar e-mail duplicado
            $chk = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
            $chk->execute([$email]);
            if ((int)$chk->fetchColumn() > 0) {
                $mensagem   = 'Este e-mail já está registado no sistema.';
                $tipoAlerta = 'erro';
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $ins = $pdo->prepare("
                    INSERT INTO usuario (nome, email, senha, contacto, tipo_usuario, data_usuario)
                    VALUES (?, ?, ?, ?, 'bibliotecario', ?)
                ");
                $ins->execute([$nome, $email, $senha_hash, $contacto, $data_usuario]);

                $mensagem   = "Bibliotecário \"$nome\" cadastrado com sucesso!";
                $tipoAlerta = 'sucesso';

                // Limpar campos após sucesso
                $nome = $email = $contacto = $data_usuario = '';
            }
        } catch (Exception $e) {
            $mensagem   = 'Erro ao cadastrar: ' . $e->getMessage();
            $tipoAlerta = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Bibliotecário - Biblioteca Pandora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../asset/style/adm/adm.css">
    <link rel="stylesheet" href="../asset/style/cd_empest.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-wrapper">

            <!-- Topbar -->
            <header class="topbar">
                <div class="welcome-text">
                    <div class="page-title-row">
                        <img src="../asset/icones/user-cog.svg" alt="" class="page-title-icon">
                        <h1>Novo Bibliotecário</h1>
                    </div>
                    <p>Preencha os dados para registar um novo bibliotecário no sistema</p>
                </div>
                <a href="bibliotecarios.php" class="back-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </a>
            </header>

            <!-- Alert -->
            <?php if ($mensagem): ?>
                <div class="alert-banner <?php echo $tipoAlerta; ?>">
                    <?php if ($tipoAlerta === 'sucesso'): ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <?php else: ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <!-- Form card -->
            <div class="form-card">
                <div class="form-card-header">
                    <div class="header-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Dados do Bibliotecário</h2>
                        <p>Todos os campos marcados com <span style="color:var(--danger)">*</span> são obrigatórios</p>
                    </div>
                </div>

                <form method="POST" novalidate>

                    <div class="form-body">

                        <!-- Secção: Identificação -->
                        <div class="section-label">Identificação</div>

                        <div class="form-row">
                            <!-- Nome -->
                            <div class="form-field full">
                                <label>Nome completo <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </span>
                                    <input type="text" name="nome" placeholder="Ex: Ana Pereira" required
                                           value="<?php echo htmlspecialchars($nome ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-field">
                                <label>E-mail <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    </span>
                                    <input type="email" name="email" placeholder="exemplo@biblioteca.ao" required
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="form-field">
                                <label>Contacto <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.92 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6.29 6.29l1.17-1.17a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </span>
                                    <input type="tel" name="contacto" placeholder="9XXXXXXXX" maxlength="9" required
                                           value="<?php echo htmlspecialchars($contacto ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Data de registo -->
                            <div class="form-field">
                                <label>Data de Registo <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    </span>
                                    <input type="date" name="data_usuario" required
                                           value="<?php echo htmlspecialchars($data_usuario ?? date('Y-m-d')); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-divider"></div>

                        <!-- Secção: Acesso -->
                        <div class="section-label">Credenciais de Acesso</div>

                        <div class="form-row">
                            <!-- Senha -->
                            <div class="form-field">
                                <label>Senha <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" required>
                                    <button type="button" class="password-toggle" onclick="togglePass('senha', this)" tabindex="-1">
                                        <svg id="eye-senha" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                                <div class="strength-label" id="strength-label">Introduza uma senha</div>
                            </div>

                            <!-- Confirmar senha -->
                            <div class="form-field">
                                <label>Confirmar Senha <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" id="senha_conf" name="senha_conf" placeholder="Repita a senha" required>
                                    <button type="button" class="password-toggle" onclick="togglePass('senha_conf', this)" tabindex="-1">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <div class="strength-label" id="match-label" style="margin-top:5px;"></div>
                            </div>
                        </div>

                    </div><!-- /form-body -->

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Cadastrar Bibliotecário
                        </button>
                        <a href="bibliotecarios.php" class="btn-cancel">Cancelar</a>
                    </div>

                </form>
            </div><!-- /form-card -->

        </div><!-- /page-wrapper -->
    </main>
</div>

<script src="../asset/js/cd_empest.js"></script>
</body>
</html>