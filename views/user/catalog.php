<?php require_once __DIR__ . '/../../views/layout/header.php'; ?>

<div class="catalog-header">
    <p class="catalog-subtitle">Explore nosso acervo de livros disponíveis para empréstimo.</p>
</div>

<?php if (empty($books)): ?>
    <div class="empty-state">
        <p>Nenhum livro disponível no momento.</p>
    </div>
<?php else: ?>
    <div class="book-grid">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <div class="book-card-header">
                    <span class="book-category"><?= htmlspecialchars($book->category_name) ?></span>
                    <span class="book-availability <?= $book->available_copies > 0 ? 'available' : 'unavailable' ?>">
                        <?= $book->available_copies > 0 ? $book->available_copies . ' disponível(eis)' : 'Indisponível' ?>
                    </span>
                </div>
                <div class="book-card-body">
                    <h3 class="book-title"><?= htmlspecialchars($book->title) ?></h3>
                    <p class="book-author">por <?= htmlspecialchars($book->author) ?></p>
                    <?php if ($book->description): ?>
                        <p class="book-description"><?= htmlspecialchars($book->description) ?></p>
                    <?php endif; ?>
                </div>
                <div class="book-card-footer">
                    <span class="book-isbn">ISBN: <?= htmlspecialchars($book->isbn) ?></span>
                    <span class="book-copies"><?= $book->total_copies ?> cópia(s) total</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../views/layout/footer.php'; ?>
