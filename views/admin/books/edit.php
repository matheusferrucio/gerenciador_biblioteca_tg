<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Editar Livro</h2>
        <a href="<?= BASE_URL ?>/books" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>/books/update/<?= $book->id ?>" method="POST" class="form" id="bookForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Título *</label>
                    <input type="text" id="title" name="title" required value="<?= htmlspecialchars($book->title) ?>">
                </div>
                <div class="form-group">
                    <label for="author">Autor *</label>
                    <input type="text" id="author" name="author" required value="<?= htmlspecialchars($book->author) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="isbn">ISBN *</label>
                    <input type="text" id="isbn" name="isbn" required value="<?= htmlspecialchars($book->isbn) ?>">
                </div>
                <div class="form-group">
                    <label for="category_id">Categoria *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= $cat->id == $book->category_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="total_copies">Total de Cópias *</label>
                    <input type="number" id="total_copies" name="total_copies" min="1" value="<?= $book->total_copies ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($book->description ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar Livro</button>
                <a href="<?= BASE_URL ?>/books" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
