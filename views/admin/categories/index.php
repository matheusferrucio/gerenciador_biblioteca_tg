<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Categorias</h2>
        <a href="<?= BASE_URL ?>/categories/create" class="btn btn-primary btn-sm">+ Nova Categoria</a>
    </div>
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <p>Nenhuma categoria cadastrada.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Livros</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($cat->name) ?></strong></td>
                                <td><?= htmlspecialchars($cat->description ?? '—') ?></td>
                                <td><span class="badge badge-info"><?= $cat->book_count ?></span></td>
                                <td class="actions">
                                    <a href="<?= BASE_URL ?>/categories/edit/<?= $cat->id ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="<?= BASE_URL ?>/categories/delete/<?= $cat->id ?>" 
                                       class="btn btn-danger btn-sm btn-confirm"
                                       data-confirm="Excluir a categoria '<?= htmlspecialchars($cat->name) ?>'?">
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
                Mostrando <?= count($categories) ?> de <?= $totalRecords ?> categorias totais.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
