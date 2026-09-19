-- CREATE DATABASE IF NOT EXISTS if0_41839551_furniture_db;
-- USE if0_41839551_furniture_db;
CREATE DATABASE IF NOT EXISTS furniture_db;
USE furniture_db;
-- ==========================================
-- 1. Products Table
-- Inayos para magkaroon ng 3 LONGBLOB columns 
-- para sa high-quality images
-- ==========================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    image LONGBLOB,   -- Primary Image
    image2 LONGBLOB,  -- Second Image
    image3 LONGBLOB,  -- Third Image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. Locations Table
-- ==========================================
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    contact VARCHAR(50)
);

-- Initial Showroom Data
INSERT INTO locations (region, address, contact) VALUES 
('South Luzon', '123 Main Avenue, Batangas City', '+63 912 345 6789'),
('Odiongan, Romblon', 'Odiongan Commercial Center, Tablas Island', '+63 919 876 5432'),
('Dapawan', 'Main St. Commercial Complex', '+63 920 111 2233');

-- ==========================================
-- 3. Admins Table
-- ==========================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin Account
INSERT INTO admins (username, password) VALUES ('admin', 'GinsU091277');