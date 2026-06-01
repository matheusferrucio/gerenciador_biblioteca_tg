<?php

/**
 * Validator — Input validation and sanitization utility
 * Prevents SQL Injection (via sanitization) and XSS (via htmlspecialchars).
 * PDO prepared statements handle SQL injection at the DB layer.
 */
class Validator
{
    private array $errors = [];

    /**
     * Sanitize a single string value against XSS
     */
    public static function sanitize(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return $value;
    }

    /**
     * Sanitize all string values in an associative array
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = self::sanitize($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * Validate required fields — checks that each listed field is non-empty
     */
    public function validateRequired(array $data, array $fields, array $labels = []): self
    {
        foreach ($fields as $field) {
            $value = $data[$field] ?? '';
            if (is_string($value) && trim($value) === '') {
                $label = $labels[$field] ?? $field;
                $this->errors[] = "O campo '{$label}' é obrigatório.";
            } elseif (is_numeric($value) && (int)$value === 0 && $field !== 'phone') {
                $label = $labels[$field] ?? $field;
                $this->errors[] = "O campo '{$label}' é obrigatório.";
            }
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function validateEmail(string $email): self
    {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'O e-mail informado é inválido.';
        }
        return $this;
    }

    /**
     * Validate strong password:
     * - Minimum 8 characters
     * - At least 1 uppercase letter
     * - At least 1 lowercase letter
     * - At least 1 digit
     * - At least 1 special character
     */
    public function validateStrongPassword(string $password): self
    {
        if (empty($password)) {
            return $this;
        }

        $issues = [];

        if (strlen($password) < 8) {
            $issues[] = 'mínimo 8 caracteres';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $issues[] = '1 letra maiúscula';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $issues[] = '1 letra minúscula';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $issues[] = '1 número';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $issues[] = '1 caractere especial (!@#$%...)';
        }

        if (!empty($issues)) {
            $this->errors[] = 'A senha não atende aos requisitos: ' . implode(', ', $issues) . '.';
        }

        return $this;
    }

    /**
     * Validate Brazilian CPF (Cadastro de Pessoas Físicas)
     * Validates check digits using the official algorithm
     */
    public function validateCPF(string $cpf): self
    {
        // Remove non-digits
        $cpf = preg_replace('/\D/', '', $cpf);

        if (empty($cpf)) {
            return $this; // CPF is optional
        }

        // Must be 11 digits
        if (strlen($cpf) !== 11) {
            $this->errors[] = 'O CPF deve conter 11 dígitos.';
            return $this;
        }

        // Reject known invalid sequences (all same digit)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $this->errors[] = 'O CPF informado é inválido.';
            return $this;
        }

        // Validate first check digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ((int)$cpf[9] !== $digit1) {
            $this->errors[] = 'O CPF informado é inválido.';
            return $this;
        }

        // Validate second check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ((int)$cpf[10] !== $digit2) {
            $this->errors[] = 'O CPF informado é inválido.';
            return $this;
        }

        return $this;
    }

    /**
     * Add a custom error message
     */
    public function addError(string $message): self
    {
        $this->errors[] = $message;
        return $this;
    }

    /**
     * Check if validation has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all error messages
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function getFirstError(): string
    {
        return $this->errors[0] ?? '';
    }

    /**
     * Get all errors as a single string (separated by line breaks)
     */
    public function getErrorString(string $separator = ' | '): string
    {
        return implode($separator, $this->errors);
    }

    /**
     * Reset errors
     */
    public function reset(): self
    {
        $this->errors = [];
        return $this;
    }
}
