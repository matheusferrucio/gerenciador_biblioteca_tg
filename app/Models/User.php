<?php

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): mixed
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): mixed
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get all users
     */
    public function getAll(): array
    {
        $this->db->query('SELECT * FROM users ORDER BY name ASC');
        return $this->db->resultSet();
    }

    /**
     * Get all non-admin users
     */
    public function getAllMembers(): array
    {
        $this->db->query("SELECT * FROM users WHERE role = 'user' ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Create a new user
     */
    public function create(array $data): bool
    {
        $this->db->query('INSERT INTO users (name, email, password, role, phone) VALUES (:name, :email, :password, :role, :phone)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        return $this->db->execute();
    }

    /**
     * Update a user
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE users SET name = :name, email = :email, role = :role, phone = :phone';

        // Only update password if provided
        if (!empty($data['password'])) {
            $sql .= ', password = :password';
        }

        $sql .= ' WHERE id = :id';

        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':id', $id);

        if (!empty($data['password'])) {
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }

        return $this->db->execute();
    }

    /**
     * Delete a user
     */
    public function delete(int $id): bool
    {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count total users
     */
    public function count(): int
    {
        $this->db->query('SELECT COUNT(*) as total FROM users');
        return (int) $this->db->single()->total;
    }

    /**
     * Check if email exists (optionally excluding an ID)
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) as total FROM users WHERE email = :email';
        if ($excludeId) {
            $sql .= ' AND id != :id';
        }
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        return (int) $this->db->single()->total > 0;
    }
}
