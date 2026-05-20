<?php

class Controller
{
    /**
     * Render a view with data
     */
    protected function view(string $viewPath, array $data = []): void
    {
        // Extract data so variables are accessible in the view
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            die("View '{$viewPath}' not found.");
        }

        require_once $viewFile;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Require authentication — redirect to login if not logged in
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Você precisa fazer login para acessar esta página.');
            $this->redirect('login');
        }
    }

    /**
     * Require admin role
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Acesso restrito a administradores.');
            $this->redirect('dashboard');
        }
    }

    /**
     * Set a flash message
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
