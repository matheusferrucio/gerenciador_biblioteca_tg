<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Histórico de Atividades</h2>
        <p class="text-muted">Registro completo de empréstimos, devoluções e prorrogações.</p>
    </div>
    <div class="card-body">
        <!-- ── Filter Bar ── -->
        <div class="filter-bar" id="historyFilterBar">
            <div class="filter-group">
                <label for="filterBook">Livro</label>
                <select id="filterBook">
                    <option value="">Todos os Livros</option>
                    <?php foreach ($books as $b): ?>
                        <option value="<?= htmlspecialchars($b->title) ?>"><?= htmlspecialchars($b->title) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterUser">Usuário</label>
                <select id="filterUser">
                    <option value="">Todos os Usuários</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= htmlspecialchars($u->name) ?>"><?= htmlspecialchars($u->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterDateFrom">De</label>
                <input type="date" id="filterDateFrom">
            </div>
            <div class="filter-group">
                <label for="filterDateTo">Até</label>
                <input type="date" id="filterDateTo">
            </div>
            <div class="filter-actions">
                <button type="button" class="btn btn-secondary btn-sm" id="clearHistoryFilters">Limpar</button>
            </div>
        </div>
        <p class="filter-count" id="historyFilterCount" style="margin-bottom: 15px;"></p>

        <?php if (empty($history)): ?>
            <div class="empty-state">
                <p>Nenhuma atividade registrada no histórico.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table" id="historyFilterTable">
                    <thead>
                        <tr>
                            <th>Ação</th>
                            <th>Data do Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Livro</th>
                            <th>Usuário</th>
                            <th>Prorrogado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $event): ?>
                            <?php 
                                $eventDate = date('Y-m-d', strtotime($event->action_date)); 
                                // Map action to new terms and badges
                                $rawAction = mb_strtolower($event->action, 'UTF-8');
                                $actionDisplay = match($rawAction) {
                                    'empréstimo', 'emprestado'  => 'emprestado',
                                    'devolução', 'devolvido'    => 'devolvido',
                                    'prorrogação', 'prorrogado' => 'prorrogado',
                                    default => $rawAction
                                };

                                $badgeClass = match($actionDisplay) {
                                    'emprestado' => 'success',
                                    'devolvido'  => 'secondary',
                                    'prorrogado' => 'purple',
                                    default      => 'secondary'
                                };
                            ?>
                            <tr data-book="<?= htmlspecialchars($event->book_title) ?>"
                                data-user="<?= htmlspecialchars($event->user_name) ?>"
                                data-date="<?= $eventDate ?>">
                                <td>
                                    <span class="badge badge-<?= $badgeClass ?>">
                                        <?= ucfirst($actionDisplay) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($event->loan_date)) ?></strong>
                                </td>
                                <td>
                                    <?= ($event->action === 'Devolução' || $event->action === 'devolvido') ? date('d/m/Y', strtotime($event->action_date)) : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td><?= htmlspecialchars($event->book_title) ?></td>
                                <td><?= htmlspecialchars($event->user_name) ?></td>
                                <td>
                                    <?php if ($event->extension_days > 0): ?>
                                        <span style="color: #16a34a; font-weight: 600;">Sim (+<?= $event->extension_days ?> dias)</span>
                                    <?php else: ?>
                                        <span style="color: var(--gray-500);">Sem prorrogação</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ── Pagination ── -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <a href="?page=<?= $currentPage - 1 ?>" class="page-link <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        Anterior
                    </a>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= $currentPage == $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="?page=<?= $currentPage + 1 ?>" class="page-link <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        Próximo
                    </a>
                </div>
            <?php endif; ?>
            
            <p class="text-muted" style="text-align: center; margin-top: 10px; font-size: 12px;">
                Mostrando <?= count($history) ?> de <?= $totalRecords ?> registros totais.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
