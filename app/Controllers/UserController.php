<?php

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * List all users
     */
    public function index(): void
    {
        $this->requireAdmin();

        $data = [
            'title' => 'Gerenciar Usuários',
            'users' => $this->userModel->getAll(),
            'flash' => $this->getFlash(),
        ];

        $this->view('admin/users/index', $data);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAdmin();

        $data = [
            'title' => 'Adicionar Usuário',
        ];

        $this->view('admin/users/create', $data);
    }

    /**
     * Store a new user
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('users');
            return;
        }

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'role'     => trim($_POST['role'] ?? 'user'),
            'phone'    => trim($_POST['phone'] ?? ''),
        ];

        // Validation
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $this->setFlash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('users/create');
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'E-mail inválido.');
            $this->redirect('users/create');
            return;
        }

        if ($this->userModel->emailExists($data['email'])) {
            $this->setFlash('error', 'Este e-mail já está cadastrado.');
            $this->redirect('users/create');
            return;
        }

        if (strlen($data['password']) < 6) {
            $this->setFlash('error', 'A senha deve ter no mínimo 6 caracteres.');
            $this->redirect('users/create');
            return;
        }

        if ($this->userModel->create($data)) {
            $this->setFlash('success', 'Usuário cadastrado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao cadastrar usuário.');
        }

        $this->redirect('users');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();

        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->setFlash('error', 'Usuário não encontrado.');
            $this->redirect('users');
            return;
        }

        $data = [
            'title' => 'Editar Usuário',
            'user'  => $user,
        ];

        $this->view('admin/users/edit', $data);
    }

    /**
     * Update a user
     */
    public function update(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('users');
            return;
        }

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'role'     => trim($_POST['role'] ?? 'user'),
            'phone'    => trim($_POST['phone'] ?? ''),
        ];

        // Validation
        if (empty($data['name']) || empty($data['email'])) {
            $this->setFlash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('users/edit/' . $id);
            return;
        }

        if ($this->userModel->emailExists($data['email'], $id)) {
            $this->setFlash('error', 'Este e-mail já está cadastrado por outro usuário.');
            $this->redirect('users/edit/' . $id);
            return;
        }

        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $this->setFlash('error', 'A senha deve ter no mínimo 6 caracteres.');
            $this->redirect('users/edit/' . $id);
            return;
        }

        if ($this->userModel->update($id, $data)) {
            $this->setFlash('success', 'Usuário atualizado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao atualizar usuário.');
        }

        $this->redirect('users');
    }

    /**
     * Delete a user
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();

        // Prevent self-deletion
        if ((int)$_SESSION['user_id'] === $id) {
            $this->setFlash('error', 'Você não pode excluir sua própria conta.');
            $this->redirect('users');
            return;
        }

        try {
            if ($this->userModel->delete($id)) {
                $this->setFlash('success', 'Usuário excluído com sucesso!');
            } else {
                $this->setFlash('error', 'Erro ao excluir usuário.');
            }
        } catch (\PDOException $e) {
            $this->setFlash('error', 'Não é possível excluir: este usuário possui empréstimos vinculados.');
        }

        $this->redirect('users');
    }
}
