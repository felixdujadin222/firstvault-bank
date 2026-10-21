-- FirstVault Bank Database Schema
CREATE DATABASE IF NOT EXISTS firstvault_bank;
USE firstvault_bank;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password CHAR(32) NOT NULL, -- MD5 hash
    email VARCHAR(100),
    phone VARCHAR(20),
    account_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(10,2) NOT NULL DEFAULT 1000.00,
    role VARCHAR(20) NOT NULL DEFAULT 'customer'
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_account VARCHAR(50) NOT NULL,
    to_account VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial seed accounts
INSERT INTO users (username, password, email, phone, account_number, balance, role) VALUES
('alice', MD5('alice123'), 'alice@example.com', '1234567890', '0000000001', 1000.00, 'customer'),
('bob',   MD5('bob123'),   'bob@example.com',   '0987654321', '0000000002', 500.00,  'customer'),
('admin', MD5('admin123'), 'admin@example.com', '1112223333', '0000000003', 9999.00, 'admin')
ON DUPLICATE KEY UPDATE id=id;
