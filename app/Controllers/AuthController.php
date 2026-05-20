<?php

class AuthController extends Controller
{
    /**
     * Display login form
     */
    public function loginForm(): void
    {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
            return;
        }

        $data = [
            'title' => 'Login',
            'flash' => $this->getFlash(),
        ];

        $this->view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validation
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Preencha todos os campos.');
            $this->redirect('login');
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            $this->setFlash('error', 'E-mail ou senha inválidos.');
            $this->redirect('login');
            return;
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set session data
        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;

        $this->setFlash('success', 'Bem-vindo(a), ' . $user->name . '!');
        $this->redirect('dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();

        $this->redirect('login');
    }
}
