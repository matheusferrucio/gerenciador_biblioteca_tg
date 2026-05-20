<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Todos os Empréstimos</h2>
        <a href="<?= BASE_URL ?>/loans/create" class="btn btn-primary btn-sm">+ Novo Empréstimo</a>
    </div>
    <div class="card-body">
        <?php if (empty($loans)): ?>
            <div class="empty-state"><p>Nenhum empréstimo registrado.</p></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Livro</th>
                            <th>Usuário</th>
                            <th>Empréstimo</th>
                            <th>Devolução Prevista</th>
                            <th>Devolvido em</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <?php
                                $isOverdue = $loan->status === 'overdue' || 
                                    ($loan->status === 'active' && strtotime($loan->due_date) < time());
                                $statusLabel = match(true) {
                                    $loan->status === 'returned' => 'Devolvido',
                                    $isOverdue => 'Em Atraso',
                                    default => 'Ativo',
                                };
                                $statusClass = match(true) {
                                    $loan->status === 'returned' => 'secondary',
                                    $isOverdue => 'danger',
                                    default => 'success',
                                };
                            ?>
                            <tr>
                                <td><?= $loan->id ?></td>
                                <td><strong><?= htmlspecialchars($loan->book_title) ?></strong></td>
                                <td><?= htmlspecialchars($loan->user_name) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->loan_date)) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->due_date)) ?></td>
                                <td><?= $loan->return_date ? date('d/m/Y', strtotime($loan->return_date)) : '—' ?></td>
                                <td><span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                <td>
                                    <?php if ($loan->status !== 'returned'): ?>
                                        <a href="<?= BASE_URL ?>/loans/return/<?= $loan->id ?>" 
                                           class="btn btn-success btn-sm btn-confirm"
                                           data-confirm="Confirmar devolução do livro '<?= htmlspecialchars($loan->book_title) ?>'?">
                                            Devolver
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
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
