<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Usuários Cadastrados</h2>
        <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary btn-sm">+ Adicionar Usuário</a>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="empty-state"><p>Nenhum usuário cadastrado.</p></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Perfil</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($u->name) ?></strong></td>
                                <td><?= htmlspecialchars($u->email) ?></td>
                                <td><?= htmlspecialchars($u->phone ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-<?= $u->role === 'admin' ? 'warning' : 'info' ?>">
                                        <?= $u->role === 'admin' ? 'Admin' : 'Usuário' ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="<?= BASE_URL ?>/users/edit/<?= $u->id ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <?php if ($u->id !== (int)$_SESSION['user_id']): ?>
                                        <a href="<?= BASE_URL ?>/users/delete/<?= $u->id ?>" 
                                           class="btn btn-danger btn-sm btn-confirm"
                                           data-confirm="Excluir o usuário '<?= htmlspecialchars($u->name) ?>'?">
                                            Excluir
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
