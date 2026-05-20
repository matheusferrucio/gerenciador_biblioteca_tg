<?php require_once __DIR__ . '/../../views/layout/header.php'; ?>

<div class="stats-grid">
    <div class="stat-card stat-books">
        <div class="stat-icon">📖</div>
        <div class="stat-info">
            <h3><?= $totalBooks ?></h3>
            <p>Livros</p>
        </div>
    </div>
    <div class="stat-card stat-users">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?= $totalUsers ?></h3>
            <p>Usuários</p>
        </div>
    </div>
    <div class="stat-card stat-loans">
        <div class="stat-icon">🔄</div>
        <div class="stat-info">
            <h3><?= $activeLoans ?></h3>
            <p>Empréstimos Ativos</p>
        </div>
    </div>
    <div class="stat-card stat-overdue">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <h3><?= $overdueLoans ?></h3>
            <p>Em Atraso</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Empréstimos Ativos</h2>
        <a href="<?= BASE_URL ?>/loans/create" class="btn btn-primary btn-sm">+ Novo Empréstimo</a>
    </div>
    <div class="card-body">
        <?php if (empty($recentLoans)): ?>
            <div class="empty-state">
                <p>Nenhum empréstimo ativo no momento.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Usuário</th>
                            <th>Empréstimo</th>
                            <th>Devolução</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLoans as $loan): ?>
                            <?php
                                $isOverdue = $loan->status === 'overdue' || 
                                    ($loan->status === 'active' && strtotime($loan->due_date) < time());
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($loan->book_title) ?></strong></td>
                                <td><?= htmlspecialchars($loan->user_name) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->loan_date)) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->due_date)) ?></td>
                                <td>
                                    <span class="badge badge-<?= $isOverdue ? 'danger' : 'success' ?>">
                                        <?= $isOverdue ? 'Em Atraso' : 'Ativo' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/loans/return/<?= $loan->id ?>" 
                                       class="btn btn-success btn-sm btn-confirm"
                                       data-confirm="Confirmar devolução deste livro?">
                                        Devolver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layout/footer.php'; ?>
