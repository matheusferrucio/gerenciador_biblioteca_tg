<?php

/**
 * History Model — Handles loan event logging
 */
class History
{
    private Database $db;

    /**
     * Initialize database connection
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Log a new loan event
     * 
     * @param array $data Event details (loan_id, action, user_name, book_title, etc.)
     * @return bool
     */
    public function log(array $data): bool
    {
        $this->db->query('
            INSERT INTO loan_history 
            (loan_id, action, user_name, book_title, loan_date, due_date, old_due_date, new_due_date, extension_days, details) 
            VALUES 
            (:loan_id, :action, :user_name, :book_title, :loan_date, :due_date, :old_due_date, :new_due_date, :extension_days, :details)
        ');

        $this->db->bind(':loan_id', $data['loan_id']);
        $this->db->bind(':action', $data['action']);
        $this->db->bind(':user_name', $data['user_name']);
        $this->db->bind(':book_title', $data['book_title']);
        $this->db->bind(':loan_date', $data['loan_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':old_due_date', $data['old_due_date'] ?? null);
        $this->db->bind(':new_due_date', $data['new_due_date'] ?? null);
        $this->db->bind(':extension_days', $data['extension_days'] ?? 0);
        $this->db->bind(':details', $data['details'] ?? null);

        return $this->db->execute();
    }

    /**
     * Get all history records sorted by date descending
     * 
     * @return array
     */
    public function getAll(): array
    {
        $this->db->query('SELECT * FROM loan_history ORDER BY action_date DESC');
        return $this->db->resultSet();
    }
}
