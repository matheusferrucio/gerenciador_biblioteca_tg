<?php

class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all categories
     */
    public function getAll(): array
    {
        $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        return $this->db->resultSet();
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): mixed
    {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Create a new category
     */
    public function create(array $data): bool
    {
        $this->db->query('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }

    /**
     * Update a category
     */
    public function update(int $id, array $data): bool
    {
        $this->db->query('UPDATE categories SET name = :name, description = :description WHERE id = :id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete a category
     */
    public function delete(int $id): bool
    {
        $this->db->query('DELETE FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count total categories
     */
    public function count(): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM categories');
        return (int) $this->db->single()->total;
    }

    /**
     * Count books in a category
     */
    public function bookCount(int $id): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM books WHERE category_id = :id');
        $this->db->bind(':id', $id);
        return (int) $this->db->single()->total;
    }
}
