-- 1. Create database whose name is Image
CREATE DATABASE IF NOT EXISTS `Image`;
USE `Image`;

-- 2. Users table for authentication (password_hash / password_verify)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Create ImageTable: Id primary key auto_increment, Image blob, Date
CREATE TABLE IF NOT EXISTS `ImageTable` (
    `Id` INT AUTO_INCREMENT PRIMARY KEY,
    `Image` LONGBLOB NOT NULL,
    `Date` DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Create ImageBackupTable to store deleted images (Preserving original Id)
CREATE TABLE IF NOT EXISTS `ImageBackupTable` (
    `Id` INT PRIMARY KEY,
    `Image` LONGBLOB NOT NULL,
    `Date` DATETIME NOT NULL,
    `BackupDate` DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Seed default administrator user for evaluator/lecturer
-- Password: Password123 (hashed with password_hash)
INSERT IGNORE INTO `users` (`username`, `password`) 
VALUES ('admin', '$2y$10$wE9iI1JmZqE8h.eG30.HvefAknzYk0w8e6tIovs2qT/b1QnS9L0.2');