<?php
/**
 * Layout — Sidebar
 * Role-based navigation
 */
$currentUrl = $_GET['url'] ?? '';
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <span class="logo-icon">📚</span>
            <span class="logo-text"><?= APP_NAME ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <?php if ($isAdmin): ?>
                <li class="nav-section-title">Gerenciamento</li>
                <li>
                    <a href="<?= BASE_URL ?>/dashboard" class="<?= $currentUrl === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/books" class="<?= str_starts_with($currentUrl, 'book') ? 'active' : '' ?>">
                        <span class="nav-icon">📖</span>
                        <span>Livros</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/categories" class="<?= str_starts_with($currentUrl, 'categor') ? 'active' : '' ?>">
                        <span class="nav-icon">🏷️</span>
                        <span>Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/users" class="<?= str_starts_with($currentUrl, 'user') ? 'active' : '' ?>">
                        <span class="nav-icon">👥</span>
                        <span>Usuários</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/loans" class="<?= str_starts_with($currentUrl, 'loan') ? 'active' : '' ?>">
                        <span class="nav-icon">🔄</span>
                        <span>Empréstimos</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/history" class="<?= $currentUrl === 'history' ? 'active' : '' ?>">
                        <span class="nav-icon">📜</span>
                        <span>Histórico</span>
                    </a>
                </li>

                <li class="nav-section-title">Portal</li>
            <?php endif; ?>

            <li>
                <a href="<?= BASE_URL ?>/catalog" class="<?= $currentUrl === 'catalog' ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span>Catálogo</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/my-loans" class="<?= $currentUrl === 'my-loans' ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span>
                    <span>Meus Empréstimos</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/logout" class="logout-link">
            <span class="nav-icon">🚪</span>
            <span>Sair</span>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
