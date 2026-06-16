<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Livros Cadastrados</h2>
        <a href="<?= BASE_URL ?>/books/create" class="btn btn-primary btn-sm">+ Adicionar Livro</a>
    </div>
    <div class="card-body">
        <?php if (empty($books)): ?>
            <div class="empty-state">
                <p>Nenhum livro cadastrado.</p>
            </div>
        <?php else: ?>
            <!-- ── Filter Bar ── -->
            <div class="filter-bar" id="bookFilterBar">
                <div class="filter-group">
                    <label for="bookSearch">Busca</label>
                    <input type="text" id="bookSearch" placeholder="Título, autor ou ISBN...">
                </div>
                <div class="filter-group">
                    <label for="bookCategory">Categoria</label>
                    <select id="bookCategory">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="bookAvailability">Disponibilidade</label>
                    <select id="bookAvailability">
                        <option value="">Todas</option>
                        <option value="available">Disponíveis</option>
                        <option value="unavailable">Indisponíveis</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="button" id="clearBookFilters" class="btn-clear">Limpar</button>
                </div>
            </div>

            <div id="bookFilterCount" class="filter-count"></div>

            <div class="table-responsive">
                <table class="table" id="booksFilterTable">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Categoria</th>
                            <th>Cópias</th>
                            <th>Disponível</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr data-title="<?= htmlspecialchars(strtolower($book->title)) ?>" 
                                data-author="<?= htmlspecialchars(strtolower($book->author)) ?>" 
                                data-isbn="<?= htmlspecialchars($book->isbn) ?>"
                                data-cat="<?= $book->category_id ?>"
                                data-available="<?= $book->available_copies > 0 ? 'available' : 'unavailable' ?>">
                                <td><strong><?= htmlspecialchars($book->title) ?></strong></td>
                                <td><?= htmlspecialchars($book->author) ?></td>
                                <td><code><?= htmlspecialchars($book->isbn) ?></code></td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($book->category_name) ?></span>
                                </td>
                                <td><?= $book->total_copies ?></td>
                                <td>
                                    <span class="badge badge-<?= $book->available_copies > 0 ? 'success' : 'danger' ?>">
                                        <?= $book->available_copies ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="<?= BASE_URL ?>/books/edit/<?= $book->id ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="<?= BASE_URL ?>/books/delete/<?= $book->id ?>" 
                                       class="btn btn-danger btn-sm btn-confirm"
                                       data-confirm="Excluir o livro '<?= htmlspecialchars($book->title) ?>'?">
                                        Excluir
                                    </a>
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
                Mostrando <?= count($books) ?> de <?= $totalRecords ?> livros totais.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
