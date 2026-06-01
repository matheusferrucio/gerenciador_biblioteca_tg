<?php

class App
{
    private Router $router;

    public function __construct()
    {
        // Load config
        require_once __DIR__ . '/../../config/config.php';

        // Start session
        session_name(SESSION_NAME);
        session_start();

        // Load core classes
        require_once __DIR__ . '/Database.php';
        require_once __DIR__ . '/Controller.php';
        require_once __DIR__ . '/Router.php';
        require_once __DIR__ . '/Validator.php';
        require_once __DIR__ . '/DateCalculator.php';

        // Load models
        require_once __DIR__ . '/../Models/User.php';
        require_once __DIR__ . '/../Models/Book.php';
        require_once __DIR__ . '/../Models/Category.php';
        require_once __DIR__ . '/../Models/Loan.php';

        // Initialize router
        $this->router = new Router();
        $this->registerRoutes();
    }

    /**
     * Register all application routes
     */
    private function registerRoutes(): void
    {
        // ── Auth ──
        $this->router->get('login', 'AuthController', 'loginForm');
        $this->router->post('login', 'AuthController', 'login');
        $this->router->get('logout', 'AuthController', 'logout');

        // ── Dashboard ──
        $this->router->get('dashboard', 'DashboardController', 'index');

        // ── Books (Admin) ──
        $this->router->get('books', 'BookController', 'index');
        $this->router->get('books/create', 'BookController', 'create');
        $this->router->post('books/store', 'BookController', 'store');
        $this->router->get('books/edit/{id}', 'BookController', 'edit');
        $this->router->post('books/update/{id}', 'BookController', 'update');
        $this->router->get('books/delete/{id}', 'BookController', 'delete');

        // ── Categories (Admin) ──
        $this->router->get('categories', 'CategoryController', 'index');
        $this->router->get('categories/create', 'CategoryController', 'create');
        $this->router->post('categories/store', 'CategoryController', 'store');
        $this->router->get('categories/edit/{id}', 'CategoryController', 'edit');
        $this->router->post('categories/update/{id}', 'CategoryController', 'update');
        $this->router->get('categories/delete/{id}', 'CategoryController', 'delete');

        // ── Users (Admin) ──
        $this->router->get('users', 'UserController', 'index');
        $this->router->get('users/create', 'UserController', 'create');
        $this->router->post('users/store', 'UserController', 'store');
        $this->router->get('users/edit/{id}', 'UserController', 'edit');
        $this->router->post('users/update/{id}', 'UserController', 'update');
        $this->router->get('users/delete/{id}', 'UserController', 'delete');

        // ── Loans (Admin) ──
        $this->router->get('loans', 'LoanController', 'index');
        $this->router->get('loans/create', 'LoanController', 'create');
        $this->router->post('loans/store', 'LoanController', 'store');
        $this->router->get('loans/return/{id}', 'LoanController', 'returnLoan');
        $this->router->get('loans/calculate-date', 'LoanController', 'calculateDate');

        // ── User Portal ──
        $this->router->get('catalog', 'DashboardController', 'catalog');
        $this->router->get('my-loans', 'LoanController', 'myLoans');
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $url = $_GET['url'] ?? 'login';
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $this->router->dispatch($url, $httpMethod);
    }
}
