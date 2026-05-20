<?php require_once __DIR__ . '/../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Meus Empréstimos</h2>
    </div>
    <div class="card-body">
        <?php if (empty($loans)): ?>
            <div class="empty-state">
                <p>Você não possui empréstimos registrados.</p>
                <a href="<?= BASE_URL ?>/catalog" class="btn btn-primary btn-sm">Explorar Catálogo</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Autor</th>
                            <th>Empréstimo</th>
                            <th>Devolução Prevista</th>
                            <th>Devolvido em</th>
                            <th>Status</th>
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
                                <td><strong><?= htmlspecialchars($loan->book_title) ?></strong></td>
                                <td><?= htmlspecialchars($loan->book_author) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->loan_date)) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->due_date)) ?></td>
                                <td><?= $loan->return_date ? date('d/m/Y', strtotime($loan->return_date)) : '—' ?></td>
                                <td><span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layout/footer.php'; ?>
