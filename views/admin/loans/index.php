<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Todos os Empréstimos</h2>
        <a href="<?= BASE_URL ?>/loans/create" class="btn btn-primary btn-sm">+ Novo Empréstimo</a>
    </div>
    <div class="card-body">
        <!-- ── Filter Bar ── -->
        <div class="filter-bar" id="loanFilterBar">
            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus">
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="returned">Devolvido</option>
                    <option value="overdue">Em Atraso</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterLoanFrom">Empréstimo de</label>
                <input type="date" id="filterLoanFrom">
            </div>
            <div class="filter-group">
                <label for="filterLoanTo">Empréstimo até</label>
                <input type="date" id="filterLoanTo">
            </div>
            <div class="filter-group">
                <label for="filterDueFrom">Devolução de</label>
                <input type="date" id="filterDueFrom">
            </div>
            <div class="filter-group">
                <label for="filterDueTo">Devolução até</label>
                <input type="date" id="filterDueTo">
            </div>
            <div class="filter-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="clearFilters">Limpar</button>
            </div>
        </div>
        <p class="filter-count" id="filterCount"></p>

        <?php if (empty($loans)): ?>
            <div class="empty-state"><p>Nenhum empréstimo registrado.</p></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table" id="loansFilterTable">
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
                                $statusKey = match(true) {
                                    $loan->status === 'returned' => 'returned',
                                    $isOverdue => 'overdue',
                                    default => 'active',
                                };
                                $statusLabel = match($statusKey) {
                                    'returned' => 'Devolvido',
                                    'overdue' => 'Em Atraso',
                                    default => 'Ativo',
                                };
                                $statusClass = match($statusKey) {
                                    'returned' => 'secondary',
                                    'overdue' => 'danger',
                                    default => 'success',
                                };
                            ?>
                            <tr data-status="<?= $statusKey ?>"
                                data-loan-date="<?= $loan->loan_date ?>"
                                data-due-date="<?= $loan->due_date ?>">
                                <td><?= $loan->id ?></td>
                                <td><strong><?= htmlspecialchars($loan->book_title) ?></strong></td>
                                <td><?= htmlspecialchars($loan->user_name) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->loan_date)) ?></td>
                                <td><?= date('d/m/Y', strtotime($loan->due_date)) ?></td>
                                <td><?= $loan->return_date ? date('d/m/Y', strtotime($loan->return_date)) : '—' ?></td>
                                <td><span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                <td>
                                    <?php if ($loan->status !== 'returned'): ?>
                                        <div class="actions" style="display: flex; gap: 5px;">
                                            <a href="<?= BASE_URL ?>/loans/return/<?= $loan->id ?>" 
                                               class="btn btn-success btn-sm btn-return"
                                               data-title="<?= htmlspecialchars($loan->book_title) ?>">
                                                Devolver
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm btn-extend"
                                                    data-id="<?= $loan->id ?>"
                                                    data-due="<?= $loan->due_date ?>"
                                                    data-title="<?= htmlspecialchars($loan->book_title) ?>">
                                                Prorrogar
                                            </button>
                                        </div>
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
