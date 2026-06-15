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

        $data = [
            'title'   => 'Histórico de Empréstimos',
            'history' => $this->historyModel->getAll(),
            'flash'   => $this->getFlash(),
        ];

        $this->view('admin/history/index', $data);
    }
}
