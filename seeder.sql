
-- Drop existing tables
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS transactions;

-- Create `users` table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NULL, -- Allow NULL for username
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (email, created_at, updated_at)
VALUES
    ('dennisfrenkel18@gmail.com', NOW(), NOW()),
    ('johndoe@example.com', NOW(), NOW()),
    ('janedoe@example.com', NOW(), NOW()),
    ('alice@example.com', NOW(), NOW());

-- Create `transactions` table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cryptocurrency VARCHAR(50) NOT NULL,
    quantity DECIMAL(18, 8) NOT NULL,
    price DECIMAL(18, 2) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample transactions
INSERT INTO transactions (user_id, cryptocurrency, quantity, price, transaction_date)
VALUES
    (1, 'BTC', 0.5, 30000, NOW()),
    (1, 'ETH', 2.0, 2000, NOW()),
    (2, 'BTC', 1.0, 28000, NOW()),
    (2, 'ADA', 500, 1.25, NOW()),
    (3, 'SOL', 100, 150, NOW()),
    (4, 'BNB', 50, 250, NOW());
