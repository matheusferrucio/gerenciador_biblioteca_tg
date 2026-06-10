<?php

class DashboardController extends Controller
{
    /**
     * Admin dashboard or user catalog redirect
     */
    public function index(): void
    {
        $this->requireAuth();

        if ($this->isAdmin()) {
            $bookModel = new Book();
            $userModel = new User();
            $loanModel = new Loan();

            // Auto-update overdue loans
            $loanModel->updateOverdue();

            $data = [
                'title'          => 'Dashboard',
                'totalBooks'     => $bookModel->count(),
                'totalUsers'     => $userModel->count(),
                'activeLoans'    => $loanModel->countActive(),
                'overdueLoans'   => $loanModel->countOverdue(),
                'recentLoans'    => $loanModel->getAll(),
                'overdueList'    => $loanModel->getOverdueWithUsers(),
                'borrowedBooks'  => $loanModel->getBorrowedBooks(),
                'usersOverdue'   => $loanModel->getUsersWithOverdue(),
                'flash'          => $this->getFlash(),
            ];

            $this->view('admin/dashboard', $data);
        } else {
            $this->redirect('catalog');
        }
    }

    /**
     * User catalog — browse available books
     */
    public function catalog(): void
    {
        $this->requireAuth();

        $bookModel = new Book();
        $categoryModel = new Category();

        $data = [
            'title'      => 'Catálogo de Livros',
            'books'      => $bookModel->getAll(),
            'categories' => $categoryModel->getAll(),
            'flash'      => $this->getFlash(),
        ];

        $this->view('user/catalog', $data);
    }
}
