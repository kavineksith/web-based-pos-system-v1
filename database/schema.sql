-- Lucky Book Shop POS System Database Schema
-- MySQL Database with UUID v4 Primary Keys

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS `lucky_bookshop_pos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lucky_bookshop_pos`;

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` CHAR(36) PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'cashier', 'supervisor') NOT NULL DEFAULT 'cashier',
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `password_changed_at` DATETIME NULL,
  `password_expires_at` DATETIME NULL,
  `must_change_password` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` CHAR(36) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_name` (`name`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items Table
CREATE TABLE IF NOT EXISTS `items` (
  `id` CHAR(36) PRIMARY KEY,
  `plu_code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `category_id` CHAR(36) NOT NULL,
  `barcode` VARCHAR(100) NULL UNIQUE,
  `qr_code` VARCHAR(100) NULL UNIQUE,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `cost_price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `total_quantity` INT NOT NULL DEFAULT 0,
  `sell_quantity` INT NOT NULL DEFAULT 0,
  `return_quantity` INT NOT NULL DEFAULT 0,
  `damage_quantity` INT NOT NULL DEFAULT 0,
  `lost_quantity` INT NOT NULL DEFAULT 0,
  `low_stock_threshold` INT DEFAULT 10,
  `is_active` BOOLEAN DEFAULT TRUE,
  `has_discount` BOOLEAN DEFAULT FALSE,
  `discount_percentage` DECIMAL(5, 2) DEFAULT 0.00,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  INDEX `idx_plu_code` (`plu_code`),
  INDEX `idx_barcode` (`barcode`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Promotions Table
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` CHAR(36) PRIMARY KEY,
  `item_id` CHAR(36) NULL,
  `category_id` CHAR(36) NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `discount_type` ENUM('percentage', 'fixed') NOT NULL,
  `discount_value` DECIMAL(10, 2) NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  INDEX `idx_item` (`item_id`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_dates` (`start_date`, `end_date`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers Table
CREATE TABLE IF NOT EXISTS `customers` (
  `id` CHAR(36) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bills Table
CREATE TABLE IF NOT EXISTS `bills` (
  `id` CHAR(36) PRIMARY KEY,
  `bill_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` CHAR(36) NULL,
  `customer_name` VARCHAR(255) NOT NULL DEFAULT 'Customer',
  `customer_email` VARCHAR(255) NULL,
  `staff_id` CHAR(36) NOT NULL,
  `staff_name` VARCHAR(255) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `total_discount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending', 'completed', 'returned', 'cancelled') DEFAULT 'pending',
  `return_reason` TEXT NULL,
  `return_authorized_by` CHAR(36) NULL,
  `return_authorized_at` DATETIME NULL,
  `remarks` TEXT NULL,
  `is_printed` BOOLEAN DEFAULT FALSE,
  `is_email_sent` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`staff_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`return_authorized_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_bill_number` (`bill_number`),
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_staff` (`staff_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bill Items Table
CREATE TABLE IF NOT EXISTS `bill_items` (
  `id` CHAR(36) PRIMARY KEY,
  `bill_id` CHAR(36) NOT NULL,
  `item_id` CHAR(36) NOT NULL,
  `plu_code` VARCHAR(50) NOT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10, 2) NOT NULL,
  `actual_price` DECIMAL(10, 2) NOT NULL,
  `discount` DECIMAL(10, 2) DEFAULT 0.00,
  `discount_percentage` DECIMAL(5, 2) DEFAULT 0.00,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`bill_id`) REFERENCES `bills`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT,
  INDEX `idx_bill` (`bill_id`),
  INDEX `idx_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock Movements Table
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` CHAR(36) PRIMARY KEY,
  `item_id` CHAR(36) NOT NULL,
  `movement_type` ENUM('in', 'out', 'return', 'damage', 'lost') NOT NULL,
  `quantity` INT NOT NULL,
  `reference_type` ENUM('bill', 'manual', 'adjustment') NULL,
  `reference_id` CHAR(36) NULL,
  `notes` TEXT NULL,
  `movement_date` DATETIME NOT NULL,
  `created_by` CHAR(36) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_item` (`item_id`),
  INDEX `idx_movement_type` (`movement_type`),
  INDEX `idx_movement_date` (`movement_date`),
  INDEX `idx_reference` (`reference_type`, `reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` CHAR(36) PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT NULL,
  `type` ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
  `description` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backups Table
CREATE TABLE IF NOT EXISTS `backups` (
  `id` CHAR(36) PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` BIGINT NOT NULL,
  `backup_type` ENUM('automatic', 'manual') NOT NULL,
  `created_by` CHAR(36) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_type` (`backup_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (password: Admin@123 - should be changed on first login)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `first_name`, `last_name`, `must_change_password`) 
VALUES (
  UUID(),
  'admin',
  'admin@luckybookshop.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: Admin@123
  'admin',
  'System',
  'Administrator',
  TRUE
) ON DUPLICATE KEY UPDATE `username`=`username`;

-- Insert Default Categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(UUID(), 'Stationary', 'Stationary items'),
(UUID(), 'Mobile Reload', 'Mobile credit and data reload services'),
(UUID(), 'Fancy Items', 'Fancy and decorative items'),
(UUID(), 'Photocopy', 'Photocopy services'),
(UUID(), 'Printout', 'Printout services (Black & White / Color)'),
(UUID(), 'Scanning', 'Scanning services')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Insert Default Settings
INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`) VALUES
(UUID(), 'shop_name', 'Lucky Book Shop', 'string', 'Shop name'),
(UUID(), 'shop_logo', '', 'string', 'Shop logo path'),
(UUID(), 'backup_auto_enabled', '1', 'boolean', 'Enable automatic backups'),
(UUID(), 'backup_retention_days', '7', 'integer', 'Backup retention period in days'),
(UUID(), 'soft_delete_retention_days', '30', 'integer', 'Soft delete retention period in days'),
(UUID(), 'theme_mode', 'light', 'string', 'Theme mode (light/dark)'),
(UUID(), 'email_enabled', '0', 'boolean', 'Enable email notifications'),
(UUID(), 'email_host', '', 'string', 'SMTP host'),
(UUID(), 'email_port', '587', 'integer', 'SMTP port'),
(UUID(), 'email_username', '', 'string', 'SMTP username'),
(UUID(), 'email_password', '', 'string', 'SMTP password'),
(UUID(), 'password_expiry_days', '7', 'integer', 'Password expiry period in days')
ON DUPLICATE KEY UPDATE `key`=`key`;

