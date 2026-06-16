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

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 30;
        
        $totalRecords = $this->userModel->count();
        $totalPages = ceil($totalRecords / $limit);
        $users = $this->userModel->getPaginated($page, $limit);

        $data = [
            'title'        => 'Gerenciar Usuários',
            'users'        => $users,
            'flash'        => $this->getFlash(),
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords
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
     * Store a new user — with full Validator integration
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('users');
            return;
        }

        // Sanitize all inputs against XSS
        $raw = [
            'name'     => $_POST['name'] ?? '',
            'email'    => $_POST['email'] ?? '',
            'cpf'      => $_POST['cpf'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role'     => $_POST['role'] ?? 'user',
            'phone'    => $_POST['phone'] ?? '',
        ];

        $data = Validator::sanitizeArray($raw);
        // Keep raw password for hashing (sanitization would mangle special chars)
        $data['password'] = trim($raw['password']);

        // ── Validation ──
        $validator = new Validator();
        $validator->validateRequired($data, ['name', 'email', 'password'], [
            'name'     => 'Nome',
            'email'    => 'E-mail',
            'password' => 'Senha',
        ]);
        $validator->validateEmail($data['email']);
        $validator->validateStrongPassword($data['password']);

        // CPF validation (optional field)
        if (!empty($data['cpf'])) {
            $validator->validateCPF($data['cpf']);
        }

        // Validate role is allowed value
        if (!in_array($data['role'], ['admin', 'user'])) {
            $validator->addError('Perfil inválido.');
        }

        // Data integrity: unique email
        if (!$validator->hasErrors() && $this->userModel->emailExists($data['email'])) {
            $validator->addError('Este e-mail já está cadastrado.');
        }

        // Data integrity: unique CPF
        if (!$validator->hasErrors() && !empty($data['cpf']) && $this->userModel->cpfExists($data['cpf'])) {
            $validator->addError('Este CPF já está cadastrado.');
        }

        if ($validator->hasErrors()) {
            $this->setFlash('error', $validator->getFirstError());
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
     * Update a user — with full Validator integration
     */
    public function update(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('users');
            return;
        }

        // Sanitize all inputs
        $raw = [
            'name'     => $_POST['name'] ?? '',
            'email'    => $_POST['email'] ?? '',
            'cpf'      => $_POST['cpf'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role'     => $_POST['role'] ?? 'user',
            'phone'    => $_POST['phone'] ?? '',
        ];

        $data = Validator::sanitizeArray($raw);
        $data['password'] = trim($raw['password']);

        // ── Validation ──
        $validator = new Validator();
        $validator->validateRequired($data, ['name', 'email'], [
            'name'  => 'Nome',
            'email' => 'E-mail',
        ]);
        $validator->validateEmail($data['email']);

        // Password is optional on edit, but if provided must be strong
        if (!empty($data['password'])) {
            $validator->validateStrongPassword($data['password']);
        }

        if (!empty($data['cpf'])) {
            $validator->validateCPF($data['cpf']);
        }

        if (!in_array($data['role'], ['admin', 'user'])) {
            $validator->addError('Perfil inválido.');
        }

        // Unique email check (excluding current user)
        if (!$validator->hasErrors() && $this->userModel->emailExists($data['email'], $id)) {
            $validator->addError('Este e-mail já está cadastrado por outro usuário.');
        }

        // Unique CPF check
        if (!$validator->hasErrors() && !empty($data['cpf']) && $this->userModel->cpfExists($data['cpf'], $id)) {
            $validator->addError('Este CPF já está cadastrado por outro usuário.');
        }

        if ($validator->hasErrors()) {
            $this->setFlash('error', $validator->getFirstError());
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
