<?php require_once __DIR__ . '/../../../views/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2>Adicionar Categoria</h2>
        <a href="<?= BASE_URL ?>/categories" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>/categories/store" method="POST" class="form" id="categoryForm">
            <div class="form-group">
                <label for="name">Nome *</label>
                <input type="text" id="name" name="name" required placeholder="Nome da categoria">
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3" placeholder="Descrição (opcional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Categoria</button>
                <a href="<?= BASE_URL ?>/categories" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../views/layout/footer.php'; ?>
