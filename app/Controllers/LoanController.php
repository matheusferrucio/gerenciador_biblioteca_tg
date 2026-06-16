<?php

class LoanController extends Controller
{
    private Loan $loanModel;
    private Book $bookModel;
    private User $userModel;
    private History $historyModel;

    public function __construct()
    {
        $this->loanModel    = new Loan();
        $this->bookModel    = new Book();
        $this->userModel    = new User();
        $this->historyModel = new History();
    }

    /**
     * List all loans (Admin)
     */
    public function index(): void
    {
        $this->requireAdmin();

        // Auto-update overdue loans
        $this->loanModel->updateOverdue();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 30;
        
        $totalRecords = $this->loanModel->count();
        $totalPages = ceil($totalRecords / $limit);
        $loans = $this->loanModel->getPaginated($page, $limit);

        $data = [
            'title'        => 'Gerenciar Empréstimos',
            'loans'        => $loans,
            'flash'        => $this->getFlash(),
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords
        ];

        $this->view('admin/loans/index', $data);
    }

    /**
     * Show create loan form (Admin)
     * Passes auto-calculated suggested due date
     */
    public function create(): void
    {
        $this->requireAdmin();

        // Auto-calculate due date: 10 business days from today
        $suggestedDueDate = DateCalculator::calculateDueDate(
            date('Y-m-d'), 10, true, true
        );

        $data = [
            'title'            => 'Registrar Empréstimo',
            'users'            => $this->userModel->getAllMembers(),
            'books'            => $this->bookModel->getAvailable(),
            'suggestedDueDate' => $suggestedDueDate,
        ];

        $this->view('admin/loans/create', $data);
    }

    /**
     * Store a new loan (Admin)
     */
    public function store(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loans');
            return;
        }

        $data = [
            'user_id'   => (int)($_POST['user_id'] ?? 0),
            'book_id'   => (int)($_POST['book_id'] ?? 0),
            'loan_date' => date('Y-m-d'),
            'due_date'  => trim($_POST['due_date'] ?? ''),
        ];

        // Fallback: If no due date provided, use default 10 business days
        if (empty($data['due_date'])) {
            $data['due_date'] = DateCalculator::calculateDueDate($data['loan_date'], 10, true, true);
        }

        // Validation
        if ($data['user_id'] === 0 || $data['book_id'] === 0) {
            $this->setFlash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('loans/create');
            return;
        }

        // Check due date is in the future
        if (strtotime($data['due_date']) <= time()) {
            $this->setFlash('error', 'A data de devolução deve ser posterior à data atual.');
            $this->redirect('loans/create');
            return;
        }

        // Check if book has available copies
        $book = $this->bookModel->findById($data['book_id']);
        if (!$book || $book->available_copies <= 0) {
            $this->setFlash('error', 'Este livro não possui exemplares disponíveis.');
            $this->redirect('loans/create');
            return;
        }

        // Check if user already has an active loan for this book
        if ($this->loanModel->userHasActiveLoan($data['user_id'], $data['book_id'])) {
            $this->setFlash('error', 'Este usuário já possui um empréstimo ativo deste livro.');
            $this->redirect('loans/create');
            return;
        }

        // Create loan and decrement copies
        if ($this->loanModel->create($data) && $this->bookModel->decrementCopies($data['book_id'])) {
            // Log History
            $loanId = $this->loanModel->getLastInsertId();
            $user   = $this->userModel->findById($data['user_id']);
            $book   = $this->bookModel->findById($data['book_id']);
            
            $this->historyModel->log([
                'loan_id'   => $loanId,
                'action'    => 'emprestado',
                'user_name' => $user->name ?? 'Usuário',
                'book_title'=> $book->title ?? 'Livro',
                'loan_date' => $data['loan_date'],
                'due_date'  => $data['due_date']
            ]);

            $this->setFlash('success', 'Empréstimo registrado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao registrar empréstimo.');
        }

        $this->redirect('loans');
    }

    /**
     * Process a return (Admin)
     */
    public function returnLoan(int $id): void
    {
        $this->requireAdmin();

        $loan = $this->loanModel->findById($id);

        if (!$loan) {
            $this->setFlash('error', 'Empréstimo não encontrado.');
            $this->redirect('loans');
            return;
        }

        if ($loan->status === 'returned') {
            $this->setFlash('error', 'Este empréstimo já foi devolvido.');
            $this->redirect('loans');
            return;
        }

        // Return loan and increment copies
        if ($this->loanModel->returnLoan($id) && $this->bookModel->incrementCopies($loan->book_id)) {
            // Log History
            $this->historyModel->log([
                'loan_id'   => $id,
                'action'    => 'devolvido',
                'user_name' => $loan->user_name,
                'book_title'=> $loan->book_title,
                'loan_date' => $loan->loan_date,
                'due_date'  => $loan->due_date,
                'details'   => 'Devolução realizada em ' . date('d/m/Y')
            ]);

            $this->setFlash('success', 'Devolução registrada com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao registrar devolução.');
        }

        $this->redirect('loans');
    }

    /**
     * Extend a loan (Prorrogar)
     */
    public function extend(int $id): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loans');
            return;
        }

        $newDueDate = trim($_POST['new_due_date'] ?? '');
        $loan = $this->loanModel->findById($id);

        if (!$loan) {
            $this->setFlash('error', 'Empréstimo não encontrado.');
            $this->redirect('loans');
            return;
        }

        // Validation: Must be a valid date and > current due date
        if (empty($newDueDate) || strtotime($newDueDate) <= strtotime($loan->due_date)) {
            $this->setFlash('error', 'A nova data de devolução deve ser superior à data original.');
            $this->redirect('loans');
            return;
        }

        if ($this->loanModel->extend($id, $newDueDate)) {
            // Log History
            $diffDays = (strtotime($newDueDate) - strtotime($loan->due_date)) / (60 * 60 * 24);
            
            $this->historyModel->log([
                'loan_id'        => $id,
                'action'         => 'prorrogado',
                'user_name'      => $loan->user_name,
                'book_title'     => $loan->book_title,
                'loan_date'      => $loan->loan_date,
                'due_date'       => $newDueDate,
                'old_due_date'   => $loan->due_date,
                'new_due_date'   => $newDueDate,
                'extension_days' => max(0, (int)$diffDays),
                'details'        => 'Prazo estendido em ' . (int)$diffDays . ' dias.'
            ]);

            $this->setFlash('success', 'Prazo prorrogado com sucesso!');
        } else {
            $this->setFlash('error', 'Erro ao prorrogar prazo.');
        }

        $this->redirect('loans');
    }

    /**
     * AJAX endpoint — Calculate due date based on loan period
     * Responds with JSON
     */
    public function calculateDate(): void
    {
        $this->requireAdmin();

        $days = (int)($_GET['days'] ?? 10);
        $skipWeekends = ($_GET['skipWeekends'] ?? '1') === '1';
        $skipHolidays = ($_GET['skipHolidays'] ?? '1') === '1';
        $startDate = date('Y-m-d');

        // Clamp days to valid range
        $days = max(1, min(90, $days));

        $dueDate = DateCalculator::calculateDueDate($startDate, $days, $skipWeekends, $skipHolidays);

        header('Content-Type: application/json');
        echo json_encode([
            'success'   => true,
            'due_date'  => $dueDate,
            'formatted' => date('d/m/Y', strtotime($dueDate)),
            'days'      => $days,
            'skipWeekends' => $skipWeekends,
            'skipHolidays' => $skipHolidays,
        ]);
        exit;
    }

    /**
     * My Loans — User portal
     */
    public function myLoans(): void
    {
        $this->requireAuth();

        // Auto-update overdue loans
        $this->loanModel->updateOverdue();

        $data = [
            'title' => 'Meus Empréstimos',
            'loans' => $this->loanModel->getByUser($_SESSION['user_id']),
            'flash' => $this->getFlash(),
        ];

        $this->view('user/my_loans', $data);
    }
}
