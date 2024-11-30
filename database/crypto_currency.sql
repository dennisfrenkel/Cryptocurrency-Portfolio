-- Create the database
CREATE DATABASE IF NOT EXISTS crypto_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- database
USE crypto_portfolio;

-- users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NULL, -- Allow NULL for username
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cryptocurrency VARCHAR(50) NOT NULL,
    quantity DECIMAL(18, 8) NOT NULL,
    price DECIMAL(18, 2) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- logs table
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    level ENUM('INFO', 'WARNING', 'ERROR') DEFAULT 'ERROR',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
