<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../asset/icones/book-open.svg" class="icon logo-icon" alt="Logo">
            <div class="logo-text">
                <span>Pandora</span>
                <small>Sistema de Gestão</small>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php
        // Escolhe o arquivo do dashboard conforme o tipo de usuário (fallback para adm.php)
        $dashboardFile = 'adm.php';
        if (isset($usuario['tipo_usuario']) && $usuario['tipo_usuario'] === 'bibliotecario') {
            $dashboardFile = 'bibliotec.php';
        }

        $items = [
            ['file' => $dashboardFile, 'icon' => 'home.svg', 'label' => 'Dashboard'],
            ['file' => 'categorias.php', 'icon' => 'file-text.svg', 'label' => 'Categorias'],
            ['file' => 'livros.php', 'icon' => 'book-open.svg', 'label' => 'Livros'],
            ['file' => 'leitores.php', 'icon' => 'users.svg', 'label' => 'Leitores'],
            ['file' => 'emprestimos.php', 'icon' => 'arrow-right-left.svg', 'label' => 'Empréstimos'],

           
            ['file' => 'devolucao.php', 'icon' => 'rotate-ccw.svg', 'label' => 'Devolução'],
            ['file' => 'reservas.php', 'icon' => 'calendar.svg', 'label' => 'Reservas'],

            ['file' => 'relatorios.php', 'icon' => 'relatorios.svg', 'label' => 'Relatórios'],
            ['file' => 'configuracoes.php', 'icon' => 'settings.svg', 'label' => 'Configurações'],
            ['file' => 'logout.php', 'icon' => 'logout.svg', 'label' => 'Sair'],

        ];

        foreach ($items as $it) {
            $active = ($currentPage === $it['file']) ? 'nav-item active' : 'nav-item';

            echo "<a href=\"{$it['file']}\" class=\"{$active}\">";
            echo "<img src=\"../asset/icones/{$it['icon']}\" class=\"icon\" alt=\"\">";
            echo "<span>{$it['label']}</span></a>";
        }
        ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar">
                <img src="../asset/icones/user.svg" class="icon white-icon" alt="">
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo isset($usuario['email']) ? htmlspecialchars($usuario['email']) : 'Administrador'; ?></span>
                <span class="user-email">Administrador</span>
            </div>
        </div>
    </div>
</aside>
