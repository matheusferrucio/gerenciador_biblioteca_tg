<?php

class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * List all categories
     */
    public function index(): void
    {
        $this->requireAdmin();

        $categories = $this->categoryModel->getAll();

        // Add book count to each category
        foreach ($categories as $category) {
            $category->book_count = $this->categoryModel->bookCount($category->id);
        }

        $data = [
            'title'      => 'Gerenciar Categorias',
            'categories' => $categories,
            'flash'      => $this->getFlash(),
        ];

        $this->view('admin/categories/index', $data);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAdmin();

        $data = [
            'title' => 'Adicionar Categoria',
        ];

        $this->view('admin/categories/create', $data);
    }

    /**
     * Store a new category
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
            return;
        }

        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];

        if (empty($data['name'])) {
            $this->setFlash('error', 'O nome da categoria é obrigatório.');
            $this->redirect('categories/create');
            return;
        }

        if ($this->categoryModel->create($data)) {
            $this->setFlash('success', 'Categoria cadastrada com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao cadastrar categoria.');
        }

        $this->redirect('categories');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();

        $category = $this->categoryModel->findById($id);

        if (!$category) {
            $this->setFlash('error', 'Categoria não encontrada.');
            $this->redirect('categories');
            return;
        }

        $data = [
            'title'    => 'Editar Categoria',
            'category' => $category,
        ];

        $this->view('admin/categories/edit', $data);
    }

    /**
     * Update a category
     */
    public function update(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
            return;
        }

        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];

        if (empty($data['name'])) {
            $this->setFlash('error', 'O nome da categoria é obrigatório.');
            $this->redirect('categories/edit/' . $id);
            return;
        }

        if ($this->categoryModel->update($id, $data)) {
            $this->setFlash('success', 'Categoria atualizada com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao atualizar categoria.');
        }

        $this->redirect('categories');
    }

    /**
     * Delete a category
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();

        try {
            if ($this->categoryModel->delete($id)) {
                $this->setFlash('success', 'Categoria excluída com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao excluir categoria.');
            }
        } catch (\PDOException $e) {
            $this->setFlash('error', 'Não é possível excluir: esta categoria possui livros vinculados.');
        }

        $this->redirect('categories');
    }
}
