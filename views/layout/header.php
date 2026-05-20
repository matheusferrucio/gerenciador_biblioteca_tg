<?php
/**
 * Layout — Header
 * Variables expected: $title
 */
$flash = $flash ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gerenciamento de Biblioteca - <?= APP_NAME ?>">
    <title><?= htmlspecialchars($title ?? 'Biblioteca') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php require_once __DIR__ . '/sidebar.php'; ?>
        <?php endif; ?>

        <main class="main-content <?= !isset($_SESSION['user_id']) ? 'full-width' : '' ?>">
            <?php if (isset($_SESSION['user_id'])): ?>
            <header class="top-bar">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <span></span><span></span><span></span>
                </button>
                <div class="top-bar-title">
                    <h1><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="top-bar-user">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                </div>
            </header>
            <?php endif; ?>

            <div class="content-area">
                <?php if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?>" id="flashMessage">
                        <span class="alert-icon"><?= $flash['type'] === 'success' ? '✓' : '✕' ?></span>
                        <span><?= htmlspecialchars($flash['message']) ?></span>
                        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endif; ?>
