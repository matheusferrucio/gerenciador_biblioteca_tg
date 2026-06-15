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
                            <th>Data do Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Livro</th>
                            <th>Usuário</th>
                            <th>Prorrogado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $event): ?>
                            <?php $eventDate = date('Y-m-d', strtotime($event->action_date)); ?>
                            <tr data-book="<?= htmlspecialchars($event->book_title) ?>"
                                data-user="<?= htmlspecialchars($event->user_name) ?>"
                                data-date="<?= $eventDate ?>">
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($event->loan_date)) ?></strong>
                                </td>
                                <td>
                                    <?= $event->action === 'Devolução' ? date('d/m/Y', strtotime($event->action_date)) : '<span class="text-muted">—</span>' ?>
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
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
