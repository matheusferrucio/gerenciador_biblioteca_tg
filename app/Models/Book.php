<?php

class Book
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all books with category names
     */
    public function getAll(): array
    {
        $this->db->query('
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            ORDER BY b.title ASC
        ');
        return $this->db->resultSet();
    }

    /**
     * Get available books (copies > 0)
     */
    public function getAvailable(): array
    {
        $this->db->query('
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE b.available_copies > 0
            ORDER BY b.title ASC
        ');
        return $this->db->resultSet();
    }

    /**
     * Find book by ID
     */
    public function findById(int $id): mixed
    {
        $this->db->query('
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE b.id = :id
        ');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a new book
     */
    public function create(array $data): bool
    {
        $this->db->query('
            INSERT INTO books (title, author, isbn, category_id, total_copies, available_copies, description) 
            VALUES (:title, :author, :isbn, :category_id, :total_copies, :available_copies, :description)
        ');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':author', $data['author']);
        $this->db->bind(':isbn', $data['isbn']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':total_copies', $data['total_copies']);
        $this->db->bind(':available_copies', $data['total_copies']); // initially all copies are available
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }

    /**
     * Update a book
     */
    public function update(int $id, array $data): bool
    {
        $this->db->query('
            UPDATE books 
            SET title = :title, author = :author, isbn = :isbn, 
                category_id = :category_id, total_copies = :total_copies, 
                description = :description
            WHERE id = :id
        ');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':author', $data['author']);
        $this->db->bind(':isbn', $data['isbn']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':total_copies', $data['total_copies']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete a book
     */
    public function delete(int $id): bool
    {
        $this->db->query('DELETE FROM books WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Decrement available copies (on borrow)
     */
    public function decrementCopies(int $id): bool
    {
        $this->db->query('UPDATE books SET available_copies = available_copies - 1 WHERE id = :id AND available_copies > 0');
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Increment available copies (on return)
     */
    public function incrementCopies(int $id): bool
    {
        $this->db->query('UPDATE books SET available_copies = available_copies + 1 WHERE id = :id AND available_copies < total_copies');
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Count total books
     */
    public function count(): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM books');
        return (int) $this->db->single()->total;
    }

    /**
     * Check if ISBN exists (optionally excluding an ID)
     */
    public function isbnExists(string $isbn, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) as total FROM books WHERE isbn = :isbn';
        if ($excludeId) {
            $sql .= ' AND id != :id';
        }
        $this->db->query($sql);
        $this->db->bind(':isbn', $isbn);
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        return (int) $this->db->single()->total > 0;
    }

    /**
     * Get paginated books with category names
     */
    public function getPaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $this->db->query('
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            ORDER BY b.title ASC
            LIMIT :offset, :limit
        ');
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
