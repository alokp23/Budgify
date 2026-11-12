-- ============================================================================
-- Budgify - Simple Personal Expense Tracker Database
-- Only 3 Tables: users, categories, transactions
-- Compatible with MySQL / MariaDB in XAMPP
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `budgify`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `budgify`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `budgets`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE `categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `type` ENUM('income', 'expense', 'both') NOT NULL DEFAULT 'expense',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Transactions Table
CREATE TABLE `transactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `type` ENUM('income', 'expense') NOT NULL,
  `transaction_date` DATE NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_date` (`user_id`, `transaction_date`),
  CONSTRAINT `fk_transactions_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transactions_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert the 7 Default Categories
INSERT INTO `categories` (`id`, `name`, `type`) VALUES
  (1, 'Food', 'expense'),
  (2, 'Travel', 'expense'),
  (3, 'Shopping', 'expense'),
  (4, 'Education', 'expense'),
  (5, 'Bills', 'expense'),
  (6, 'Salary', 'income'),
  (7, 'Others', 'both');

-- Insert Demo User (Email: student@budgify.com | Password: Student@123)
INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
  (1, 'Rahul Sharma', 'student@budgify.com', '__PENDING_DEMO_HASH__');

-- Insert Sample Transactions for Demo User
SET @month_start = DATE_FORMAT(CURDATE(), '%Y-%m-01');

INSERT INTO `transactions` (`user_id`, `category_id`, `title`, `amount`, `type`, `transaction_date`, `description`) VALUES
  (1, 6, 'Monthly Internship Stipend', 15000.00, 'income',  @month_start, 'Monthly stipend credited to bank account'),
  (1, 7, 'Freelance Web Design',        4500.00,  'income',  GREATEST(@month_start, DATE_SUB(CURDATE(), INTERVAL 5 DAY)), 'College club website project'),
  (1, 1, 'Canteen & Mess Meals',        2200.00,  'expense', GREATEST(@month_start, DATE_SUB(CURDATE(), INTERVAL 4 DAY)), 'Weekly lunch and snacks'),
  (1, 2, 'Metro & Bus Pass',             850.00,  'expense', GREATEST(@month_start, DATE_SUB(CURDATE(), INTERVAL 3 DAY)), 'Monthly student travel pass'),
  (1, 4, 'PHP & MySQL Reference Book',   650.00,  'expense', GREATEST(@month_start, DATE_SUB(CURDATE(), INTERVAL 2 DAY)), 'Semester exam preparation'),
  (1, 5, 'Mobile Recharge & Wi-Fi',      999.00,  'expense', GREATEST(@month_start, DATE_SUB(CURDATE(), INTERVAL 1 DAY)), 'Monthly internet bill'),
  (1, 3, 'Backpack & Stationery',       1250.00,  'expense', CURDATE(), 'College supplies'),
  (1, 6, 'Previous Month Stipend',     15000.00,  'income',  DATE_SUB(@month_start, INTERVAL 1 MONTH), 'Last month stipend'),
  (1, 1, 'Groceries & Dining',          3100.00,  'expense', DATE_ADD(DATE_SUB(@month_start, INTERVAL 1 MONTH), INTERVAL 10 DAY), 'Monthly food expenses'),
  (1, 5, 'Electricity & Water Bill',    1400.00,  'expense', DATE_ADD(DATE_SUB(@month_start, INTERVAL 1 MONTH), INTERVAL 15 DAY), 'Hostel room utilities');

