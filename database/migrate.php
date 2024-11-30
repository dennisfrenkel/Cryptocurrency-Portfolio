<?php
require '../app/core/setup.php';

try {
    $db = new PDO('mysql:host=' . DBHOST, DBUSER, DBPASS);
    $db->exec("CREATE DATABASE IF NOT EXISTS crypto_portfolio");
    $db->exec("USE crypto_portfolio");

    // Create users table
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Create transactions table
    $db->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            cryptocurrency VARCHAR(50),
            quantity DECIMAL(18, 8),
            price DECIMAL(18, 8),
            transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Create logs table
    $db->exec("
        CREATE TABLE IF NOT EXISTS logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message TEXT NOT NULL,
            level ENUM('INFO', 'WARNING', 'ERROR') DEFAULT 'ERROR',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo "Database and tables created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
