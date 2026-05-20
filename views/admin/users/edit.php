<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Editar Usuário</h2>
        <a href="<?= BASE_URL ?>/users" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>/users/update/<?= $user->id ?>" method="POST" class="form" id="userForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome *</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user->name) ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user->email) ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" minlength="6" placeholder="Deixe em branco para manter">
                </div>
                <div class="form-group">
                    <label for="role">Perfil *</label>
                    <select id="role" name="role" required>
                        <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>Usuário</option>
                        <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Telefone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" placeholder="(00) 00000-0000">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
                <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
