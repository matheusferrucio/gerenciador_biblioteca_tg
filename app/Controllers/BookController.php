<?php

class BookController extends Controller
{
    private Book $bookModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->bookModel = new Book();
        $this->categoryModel = new Category();
    }

    /**
     * List all books
     */
    public function index(): void
    {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 30;
        
        $totalRecords = $this->bookModel->count();
        $totalPages = ceil($totalRecords / $limit);
        $books = $this->bookModel->getPaginated($page, $limit);

        $data = [
            'title'        => 'Gerenciar Livros',
            'books'        => $books,
            'categories'   => $this->categoryModel->getAll(),
            'flash'        => $this->getFlash(),
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords
        ];

        $this->view('admin/books/index', $data);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAdmin();

        $data = [
            'title'      => 'Adicionar Livro',
            'categories' => $this->categoryModel->getAll(),
        ];

        $this->view('admin/books/create', $data);
    }

    /**
     * Store a new book
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('books');
            return;
        }

        $data = [
            'title'        => trim($_POST['title'] ?? ''),
            'author'       => trim($_POST['author'] ?? ''),
            'isbn'         => trim($_POST['isbn'] ?? ''),
            'category_id'  => (int)($_POST['category_id'] ?? 0),
            'total_copies' => (int)($_POST['total_copies'] ?? 1),
            'description'  => trim($_POST['description'] ?? ''),
        ];

        // Validation
        if (empty($data['title']) || empty($data['author']) || empty($data['isbn']) || $data['category_id'] === 0) {
            $this->setFlash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('books/create');
            return;
        }

        if ($this->bookModel->isbnExists($data['isbn'])) {
            $this->setFlash('error', 'Este ISBN já está cadastrado.');
            $this->redirect('books/create');
            return;
        }

        if ($this->bookModel->create($data)) {
            $this->setFlash('success', 'Livro cadastrado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao cadastrar livro.');
        }

        $this->redirect('books');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();

        $book = $this->bookModel->findById($id);

        if (!$book) {
            $this->setFlash('error', 'Livro não encontrado.');
            $this->redirect('books');
            return;
        }

        $data = [
            'title'      => 'Editar Livro',
            'book'       => $book,
            'categories' => $this->categoryModel->getAll(),
        ];

        $this->view('admin/books/edit', $data);
    }

    /**
     * Update a book
     */
    public function update(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('books');
            return;
        }

        $data = [
            'title'        => trim($_POST['title'] ?? ''),
            'author'       => trim($_POST['author'] ?? ''),
            'isbn'         => trim($_POST['isbn'] ?? ''),
            'category_id'  => (int)($_POST['category_id'] ?? 0),
            'total_copies' => (int)($_POST['total_copies'] ?? 1),
            'description'  => trim($_POST['description'] ?? ''),
        ];

        // Validation
        if (empty($data['title']) || empty($data['author']) || empty($data['isbn']) || $data['category_id'] === 0) {
            $this->setFlash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('books/edit/' . $id);
            return;
        }

        if ($this->bookModel->isbnExists($data['isbn'], $id)) {
            $this->setFlash('error', 'Este ISBN já está cadastrado por outro livro.');
            $this->redirect('books/edit/' . $id);
            return;
        }

        if ($this->bookModel->update($id, $data)) {
            $this->setFlash('success', 'Livro atualizado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao atualizar livro.');
        }

        $this->redirect('books');
    }

    /**
     * Delete a book
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();

        $book = $this->bookModel->findById($id);

        if (!$book) {
            $this->setFlash('error', 'Livro não encontrado.');
            $this->redirect('books');
            return;
        }

        try {
            if ($this->bookModel->delete($id)) {
                $this->setFlash('success', 'Livro excluído com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao excluir livro.');
            }
        } catch (\PDOException $e) {
            $this->setFlash('error', 'Não é possível excluir: este livro possui empréstimos vinculados.');
        }

        $this->redirect('books');
    }
}
