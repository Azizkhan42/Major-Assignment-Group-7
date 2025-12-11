-- ========================================
-- Personal Budget Dashboard SQL Script
-- ========================================

-- Step 1: Create Database
CREATE DATABASE IF NOT EXISTS `personal_budget_dashboard`;
USE `personal_budget_dashboard`;

-- ========================================
-- Step 2: Create Users Table
-- ========================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 3: Create Income Categories Table
-- ========================================
CREATE TABLE IF NOT EXISTS `income_categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 4: Create Expense Categories Table
-- ========================================
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 5: Create Income Transactions Table
-- ========================================
CREATE TABLE IF NOT EXISTS `income` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `source` VARCHAR(100),
  `description` TEXT,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `income_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 6: Create Expense Transactions Table
-- ========================================
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `expense_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 7: Create Budget Limits Table
-- ========================================
CREATE TABLE IF NOT EXISTS `budget_limits` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `limit_amount` DECIMAL(10,2) NOT NULL,
  `period` ENUM('weekly','monthly') DEFAULT 'monthly',
  `start_date` DATE NOT NULL,
  `end_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `expense_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- Step 8: Insert Default Users
-- ========================================
INSERT INTO `users` (`name`,`email`,`password`,`role`) VALUES
('Admin User','admin@budgetdashboard.com','admin123','admin'),
('Demo User','user@budgetdashboard.com','user123','user');

-- ========================================
-- Step 9: Insert Default Expense Categories
-- ========================================
INSERT INTO `expense_categories` (`user_id`,`name`,`description`) VALUES
(2,'Food','Groceries, dining out'),
(2,'Rent','Monthly rent payment'),
(2,'Entertainment','Movies, games, hobbies'),
(2,'Transportation','Gas, public transport, car maintenance'),
(2,'Utilities','Electricity, water, internet'),
(2,'Healthcare','Medical expenses');

-- ========================================
-- Step 10: Insert Default Income Categories
-- ========================================
INSERT INTO `income_categories` (`user_id`,`name`,`description`) VALUES
(2,'Salary','Monthly salary'),
(2,'Freelance','Freelance projects'),
(2,'Investment','Investment returns');
