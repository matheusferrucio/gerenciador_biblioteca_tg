-- ============================================================
-- Biblioteca TG — Library Management System
-- Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS biblioteca_tg
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE biblioteca_tg;

-- ------------------------------------------------------------
-- Categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Books
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    category_id INT NOT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Loans
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Administrador', 'admin@biblioteca.com', '$2y$10$owisQxQ7Uv233ymeb2bz7ePDbiMO8iIOLqNrwLqzEBAuIT/vxTHEO', 'admin');

-- Sample regular user (password: user123)
INSERT INTO users (name, email, password, role, phone) VALUES
('João Silva', 'joao@email.com', '$2y$10$b6ftWMQbqOdMSYHKRlBtnuNHUfUAd2maUJo/6olq5aOOTMlLjXtpWq', 'user', '(11) 99999-0001'),
('Maria Santos', 'maria@email.com', '$2y$10$b6ftWMQbqOdMSYHKRlBtnuNHUfUAd2maUJo/6olq5aOOTMlLjXtpWq', 'user', '(11) 99999-0002');

-- Sample categories
INSERT INTO categories (name, description) VALUES
('Ficção', 'Livros de ficção, romances e contos'),
('Ciência', 'Livros científicos e de divulgação'),
('Tecnologia', 'Programação, TI e engenharia'),
('História', 'Livros de história e biografias'),
('Educação', 'Material didático e pedagógico');

-- Sample books
INSERT INTO books (title, author, isbn, category_id, total_copies, available_copies, description) VALUES
('Dom Casmurro', 'Machado de Assis', '978-8535902778', 1, 5, 5, 'Clássico da literatura brasileira'),
('O Alquimista', 'Paulo Coelho', '978-8573021479', 1, 3, 3, 'Romance filosófico'),
('Cosmos', 'Carl Sagan', '978-8535929881', 2, 2, 2, 'Exploração do universo'),
('Clean Code', 'Robert C. Martin', '978-0132350884', 3, 4, 4, 'Guia de boas práticas em programação'),
('O Povo Brasileiro', 'Darcy Ribeiro', '978-8535921267', 4, 3, 3, 'Formação e sentido do Brasil'),
('PHP & MySQL', 'Jon Duckett', '978-1118008188', 3, 2, 2, 'Desenvolvimento web com PHP'),
('Sapiens', 'Yuval Noah Harari', '978-8525432186', 4, 3, 3, 'Uma breve história da humanidade'),
('Pedagogia do Oprimido', 'Paulo Freire', '978-8577531646', 5, 4, 4, 'Obra fundamental da educação');
