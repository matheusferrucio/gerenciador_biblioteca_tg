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
        $this->db->query('INSERT INTO users (name, email, cpf, password, role, phone) VALUES (:name, :email, :cpf, :password, :role, :phone)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':cpf', !empty($data['cpf']) ? preg_replace('/\D/', '', $data['cpf']) : null);
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
        $sql = 'UPDATE users SET name = :name, email = :email, cpf = :cpf, role = :role, phone = :phone';

        // Only update password if provided
        if (!empty($data['password'])) {
            $sql .= ', password = :password';
        }

        $sql .= ' WHERE id = :id';

        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':cpf', !empty($data['cpf']) ? preg_replace('/\D/', '', $data['cpf']) : null);
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

    /**
     * Check if CPF exists (optionally excluding an ID)
     */
    public function cpfExists(string $cpf, ?int $excludeId = null): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (empty($cpf)) {
            return false;
        }
        $sql = 'SELECT COUNT(*) as total FROM users WHERE cpf = :cpf';
        if ($excludeId) {
            $sql .= ' AND id != :id';
        }
        $this->db->query($sql);
        $this->db->bind(':cpf', $cpf);
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        return (int) $this->db->single()->total > 0;
    }

    /**
     * Get paginated users
     */
    public function getPaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $this->db->query('SELECT * FROM users ORDER BY name ASC LIMIT :offset, :limit');
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
