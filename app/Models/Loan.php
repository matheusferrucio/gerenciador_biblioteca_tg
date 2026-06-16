<?php

class Loan
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all loans with user and book info
     */
    public function getAll(): array
    {
        $this->db->query('
            SELECT l.*, u.name as user_name, u.email as user_email, 
                   b.title as book_title, b.author as book_author
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            ORDER BY l.created_at DESC
        ');
        return $this->db->resultSet();
    }

    /**
     * Get active loans
     */
    public function getActive(): array
    {
        $this->db->query("
            SELECT l.*, u.name as user_name, b.title as book_title, b.author as book_author
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            WHERE l.status IN ('active', 'overdue')
            ORDER BY l.due_date ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get loans by user ID
     */
    public function getByUser(int $userId): array
    {
        $this->db->query('
            SELECT l.*, b.title as book_title, b.author as book_author
            FROM loans l
            JOIN books b ON l.book_id = b.id
            WHERE l.user_id = :user_id
            ORDER BY l.created_at DESC
        ');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Find loan by ID
     */
    public function findById(int $id): mixed
    {
        $this->db->query('
            SELECT l.*, u.name as user_name, b.title as book_title, b.id as book_id
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            WHERE l.id = :id
        ');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a new loan
     */
    public function create(array $data): bool
    {
        $this->db->query('
            INSERT INTO loans (user_id, book_id, loan_date, due_date, status) 
            VALUES (:user_id, :book_id, :loan_date, :due_date, :status)
        ');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':book_id', $data['book_id']);
        $this->db->bind(':loan_date', $data['loan_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':status', 'active');
        return $this->db->execute();
    }

    /**
     * Process a return
     */
    public function returnLoan(int $id): bool
    {
        $this->db->query("
            UPDATE loans 
            SET status = 'returned', return_date = CURDATE() 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Update overdue loans
     */
    public function updateOverdue(): void
    {
        $this->db->query("
            UPDATE loans 
            SET status = 'overdue' 
            WHERE status = 'active' AND due_date < CURDATE()
        ");
        $this->db->execute();
    }

    /**
     * Count active loans
     */
    public function countActive(): int
    {
        $this->db->query("SELECT COUNT(*) as total FROM loans WHERE status IN ('active', 'overdue')");
        return (int) $this->db->single()->total;
    }

    /**
     * Count overdue loans
     */
    public function countOverdue(): int
    {
        $this->db->query("SELECT COUNT(*) as total FROM loans WHERE status = 'overdue' OR (status = 'active' AND due_date < CURDATE())");
        return (int) $this->db->single()->total;
    }

    /**
     * Count total loans
     */
    public function count(): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM loans');
        return (int) $this->db->single()->total;
    }

    /**
     * Check if user has active loan for a specific book
     */
    public function userHasActiveLoan(int $userId, int $bookId): bool
    {
        $this->db->query("
            SELECT COUNT(*) as total FROM loans 
            WHERE user_id = :user_id AND book_id = :book_id AND status IN ('active', 'overdue')
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':book_id', $bookId);
        return (int) $this->db->single()->total > 0;
    }

    // ========================================================
    // Queries & Filters Module — New methods
    // ========================================================

    /**
     * Get overdue loans with full user and book details
     */
    public function getOverdueWithUsers(): array
    {
        $this->db->query("
            SELECT l.*, u.name as user_name, u.email as user_email, u.phone as user_phone,
                   b.title as book_title, b.author as book_author,
                   DATEDIFF(CURDATE(), l.due_date) as days_overdue
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            WHERE l.status = 'overdue' OR (l.status = 'active' AND l.due_date < CURDATE())
            ORDER BY l.due_date ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get distinct users who have at least one overdue item
     */
    public function getUsersWithOverdue(): array
    {
        $this->db->query("
            SELECT u.id, u.name, u.email, u.phone,
                   COUNT(l.id) as overdue_count,
                   MIN(l.due_date) as oldest_due_date
            FROM users u
            JOIN loans l ON l.user_id = u.id
            WHERE l.status = 'overdue' OR (l.status = 'active' AND l.due_date < CURDATE())
            GROUP BY u.id, u.name, u.email, u.phone
            ORDER BY overdue_count DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get all books currently borrowed (available_copies < total_copies)
     */
    public function getBorrowedBooks(): array
    {
        $this->db->query("
            SELECT b.id, b.title, b.author, b.isbn, b.total_copies, b.available_copies,
                   c.name as category_name,
                   (b.total_copies - b.available_copies) as borrowed_count
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.available_copies < b.total_copies
            ORDER BY borrowed_count DESC
        ");
        return $this->db->resultSet();
    }
    /**
     * Extend a loan return date
     */
    public function extend(int $id, string $newDueDate): bool
    {
        $this->db->query("
            UPDATE loans 
            SET due_date = :due_date, status = 'active' 
            WHERE id = :id
        ");
        $this->db->bind(':due_date', $newDueDate);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get the last inserted ID
     */
    public function getLastInsertId(): int
    {
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get paginated loans with user and book info
     */
    public function getPaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $this->db->query('
            SELECT l.*, u.name as user_name, u.email as user_email, 
                   b.title as book_title, b.author as book_author
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            ORDER BY l.created_at DESC
            LIMIT :offset, :limit
        ');
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
