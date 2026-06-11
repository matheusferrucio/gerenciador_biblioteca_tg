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
                    <label for="name">Nome Completo *</label>
                    <input type="text" id="name" name="name" required placeholder="Ex: João Silva">
                </div>
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" required placeholder="email@exemplo.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14">
                </div>
                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="text" id="phone" name="phone" placeholder="(00) 00000-0000" maxlength="15">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Senha *</label>
                    <input type="password" id="password" name="password" required 
                           aria-describedby="passwordHelp" autocomplete="new-password">
                    
                    <div class="password-requirements-container" style="margin-top: 15px;">
                        <p class="password-instructions">Requisitos de Segurança:</p>
                        <ul class="password-requirements" id="passwordRequirements">
                            <li id="req-length"><span class="requirement-icon"></span>Mínimo 8 caracteres</li>
                            <li id="req-upper"><span class="requirement-icon"></span>Pelo menos 1 letra maiúscula</li>
                            <li id="req-lower"><span class="requirement-icon"></span>Pelo menos 1 letra minúscula</li>
                            <li id="req-number"><span class="requirement-icon"></span>Pelo menos 1 número</li>
                            <li id="req-special"><span class="requirement-icon"></span>Pelo menos 1 caractere especial (!@#$...)</li>
                        </ul>
                    </div>
                </div>
                <div class="form-group">
                    <label for="role">Perfil de Acesso *</label>
                    <select id="role" name="role" required>
                        <option value="user">Usuário / Aluno</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="btnSubmitUser">Salvar Usuário</button>
                <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
