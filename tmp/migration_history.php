<?php
/**
 * Database Migration — Create loan_history table
 */

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS loan_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        loan_id INT NOT NULL,
        action VARCHAR(50) NOT NULL, -- 'Empréstimo', 'Devolução', 'Prorrogação'
        user_name VARCHAR(255) NOT NULL,
        book_title VARCHAR(255) NOT NULL,
        loan_date DATE NOT NULL,
        due_date DATE NOT NULL,
        old_due_date DATE NULL,
        new_due_date DATE NULL,
        extension_days INT DEFAULT 0,
        action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        details TEXT NULL,
        INDEX (loan_id),
        INDEX (action_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->query($sql);
    if ($db->execute()) {
        echo "Table 'loan_history' created successfully.\n";
    } else {
        echo "Error creating table.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
