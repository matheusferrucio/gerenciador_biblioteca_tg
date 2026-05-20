<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Adicionar Usuário</h2>
        <a href="<?= BASE_URL ?>/users" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>/users/store" method="POST" class="form" id="userForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome *</label>
                    <input type="text" id="name" name="name" required placeholder="Nome completo">
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required placeholder="email@exemplo.com">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Senha *</label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group">
                    <label for="role">Perfil *</label>
                    <select id="role" name="role" required>
                        <option value="user">Usuário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Telefone</label>
                <input type="text" id="phone" name="phone" placeholder="(00) 00000-0000">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Usuário</button>
                <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
