<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Registrar Empréstimo</h2>
        <a href="<?= BASE_URL ?>/loans" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>/loans/store" method="POST" class="form" id="loanForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="user_id">Usuário *</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Selecione o usuário...</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->id ?>"><?= htmlspecialchars($u->name) ?> (<?= htmlspecialchars($u->email) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="book_id">Livro *</label>
                    <select id="book_id" name="book_id" required>
                        <option value="">Selecione o livro...</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book->id ?>">
                                <?= htmlspecialchars($book->title) ?> — <?= htmlspecialchars($book->author) ?> 
                                (<?= $book->available_copies ?> disponíveis)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="due_date">Data de Devolução *</label>
                    <input type="date" id="due_date" name="due_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                <div class="form-group">
                    <!-- spacer -->
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Empréstimo</button>
                <a href="<?= BASE_URL ?>/loans" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
