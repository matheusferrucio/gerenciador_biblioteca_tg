<?php
/**
 * Login Page
 */
$flash = $flash ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">📚</div>
                <h1><?= APP_NAME ?></h1>
                <p>Sistema de Gerenciamento de Biblioteca</p>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <span class="alert-icon"><?= $flash['type'] === 'success' ? '✓' : '✕' ?></span>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST" class="login-form" id="loginForm">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="seu@email.com" 
                        required 
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required 
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                    Entrar
                </button>
            </form>

            <!-- <div class="login-footer">
                <p>Admin: <code>admin@biblioteca.com</code> / <code>admin123</code></p>
                <p>Usuário: <code>joao@email.com</code> / <code>user123</code></p>
            </div> -->
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
