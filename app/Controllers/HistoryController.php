<?php

/**
 * History Controller — Admin only
 */
class HistoryController extends Controller
{
    private History $historyModel;
    private Book $bookModel;
    private User $userModel;

    public function __construct()
    {
        $this->historyModel = new History();
        $this->bookModel    = new Book();
        $this->userModel    = new User();
    }

    /**
     * Show loan history (Admin)
     */
    public function index(): void
    {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 30;
        
        $totalRecords = $this->historyModel->countAll();
        $totalPages = ceil($totalRecords / $limit);
        $history = $this->historyModel->getPaginated($page, $limit);

        $data = [
            'title'        => 'Histórico de Empréstimos',
            'history'      => $history,
            'books'        => $this->bookModel->getAll(),
            'users'        => $this->userModel->getAll(),
            'flash'        => $this->getFlash(),
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords
        ];

        $this->view('admin/history/index', $data);
    }
}
