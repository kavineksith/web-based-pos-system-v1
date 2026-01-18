



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `backups`;
CREATE TABLE `backups` (
  `id` char(36) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `backup_type` enum('automatic','manual') NOT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type` (`backup_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `backups` VALUES
('738b22bf-41ac-4c36-807e-2207b4a3654c', 'backup_2026-01-09_21-12-48.sql', 'C:\\xampp\\htdocs\\pos-system/storage/backups/backup_2026-01-09_21-12-48.sql', '18926', 'manual', '1be7e6fe-e9fb-11f0-b538-089798c3b7e7', '2026-01-09 21:12:48'),
('beedcf03-add1-4430-aeee-860d8a76e922', 'backup_2026-01-09_21-12-47.sql', 'C:\\xampp\\htdocs\\pos-system/storage/backups/backup_2026-01-09_21-12-47.sql', '18658', 'manual', '1be7e6fe-e9fb-11f0-b538-089798c3b7e7', '2026-01-09 21:12:47'),
('fd04b32c-de37-4bbc-af6c-1219909fb9d1', 'backup_2026-01-09_21-12-51.sql', 'C:\\xampp\\htdocs\\pos-system/storage/backups/backup_2026-01-09_21-12-51.sql', '19164', 'manual', '1be7e6fe-e9fb-11f0-b538-089798c3b7e7', '2026-01-09 21:12:51');

DROP TABLE IF EXISTS `bill_items`;
CREATE TABLE `bill_items` (
  `id` char(36) NOT NULL,
  `bill_id` char(36) NOT NULL,
  `item_id` char(36) NOT NULL,
  `plu_code` varchar(50) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `actual_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_bill` (`bill_id`),
  KEY `idx_item` (`item_id`),
  CONSTRAINT `bill_items_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bill_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `bills`;
CREATE TABLE `bills` (
  `id` char(36) NOT NULL,
  `bill_number` varchar(50) NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL DEFAULT 'Customer',
  `customer_email` varchar(255) DEFAULT NULL,
  `staff_id` char(36) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','returned','cancelled') DEFAULT 'pending',
  `return_reason` text DEFAULT NULL,
  `return_authorized_by` char(36) DEFAULT NULL,
  `return_authorized_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_printed` tinyint(1) DEFAULT 0,
  `is_email_sent` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `tax_name` varchar(50) DEFAULT 'Tax',
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_number` (`bill_number`),
  KEY `return_authorized_by` (`return_authorized_by`),
  KEY `idx_bill_number` (`bill_number`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_staff` (`staff_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bills_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  CONSTRAINT `bills_ibfk_3` FOREIGN KEY (`return_authorized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_name` (`name`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` VALUES
('1bea5b78-e9fb-11f0-b538-089798c3b7e7', 'Stationary', 'Stationary items', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL),
('1bea756c-e9fb-11f0-b538-089798c3b7e7', 'Mobile Reload', 'Mobile credit and data reload services', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL),
('1bea7760-e9fb-11f0-b538-089798c3b7e7', 'Fancy Items', 'Fancy and decorative items', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL),
('1bea77cd-e9fb-11f0-b538-089798c3b7e7', 'Photocopy', 'Photocopy services', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL),
('1bea7834-e9fb-11f0-b538-089798c3b7e7', 'Printout', 'Printout services (Black & White / Color)', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL),
('1bea78a7-e9fb-11f0-b538-089798c3b7e7', 'Scanning', 'Scanning services', '1', '2026-01-05 05:55:16', '2026-01-05 05:55:16', NULL);

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_phone` (`phone`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` VALUES
('4f74f202-723a-4975-8d5e-f2555ee05d89', 'Cory Chase', 'corychase@gmail.com', '1234567890', 'None', '2026-01-06 11:31:55', '2026-01-06 15:31:17', NULL),
('79502dfe-a8d5-4dfa-91c5-71729d7e245d', 'Reena Sky', 'reenasky@gmail.com', '1234567890', 'None', '2026-01-06 15:31:36', '2026-01-06 15:31:36', NULL),
('a726a018-cf8e-4b80-be17-bb3e14b6b36a', 'Melanie Hicks', 'melanie@info.co', '1234567890', 'None', '2026-01-06 11:41:34', '2026-01-06 11:41:34', NULL);

DROP TABLE IF EXISTS `device_settings`;
CREATE TABLE `device_settings` (
  `id` char(36) NOT NULL,
  `device_identifier` varchar(255) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_type` enum('scanner','printer') NOT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_identifier` (`device_identifier`),
  KEY `idx_identifier` (`device_identifier`),
  KEY `idx_type` (`device_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `device_settings` VALUES
('0223548a-ca9d-4bf5-bc76-52b7e58c2586', 'browser_plxzsw', 'Firefox on Windows', 'scanner', 'browser_hid', '{\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko\\/20100101 Firefox\\/146.0\",\"platform\":\"Win32\",\"language\":\"en-US\"}', '0', '2026-01-06 15:49:56', '2026-01-06 16:02:29');

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_identifier` varchar(255) NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `device_type` enum('scanner','printer') NOT NULL,
  `driver` varchar(100) NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_identifier` (`device_identifier`),
  KEY `idx_device_type` (`device_type`),
  KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `devices` VALUES
('1', 'browser_k5b1i2', 'Firefox on Windows', 'scanner', 'browser_hid', '{\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko\\/20100101 Firefox\\/146.0\",\"platform\":\"Win32\",\"language\":\"en-US\"}', '1', '2026-01-06 21:05:45', '2026-01-06 21:13:12'),
('2', 'browser_c5rpxb', 'Firefox on Windows', 'scanner', 'browser_hid', '{\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko\\/20100101 Firefox\\/146.0\",\"platform\":\"Win32\",\"language\":\"en-US\"}', '0', '2026-01-06 22:14:59', '2026-01-06 23:59:45');

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` char(36) NOT NULL,
  `plu_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` char(36) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `qr_code` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `total_quantity` int(11) NOT NULL DEFAULT 0,
  `sell_quantity` int(11) NOT NULL DEFAULT 0,
  `return_quantity` int(11) NOT NULL DEFAULT 0,
  `damage_quantity` int(11) NOT NULL DEFAULT 0,
  `lost_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 10,
  `is_active` tinyint(1) DEFAULT 1,
  `has_discount` tinyint(1) DEFAULT 0,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plu_code` (`plu_code`),
  UNIQUE KEY `barcode` (`barcode`),
  UNIQUE KEY `qr_code` (`qr_code`),
  KEY `idx_plu_code` (`plu_code`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_category` (`category_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `items` VALUES
('27fb57d1-90f1-46a5-9464-32904a5361ca', 'ES123', 'Test', NULL, '1bea7760-e9fb-11f0-b538-089798c3b7e7', '1234567', NULL, '3456.00', '3456.00', '20', '20', '0', '0', '0', '0', '10', '1', '0', '0.00', '2026-01-05 20:29:54', '2026-01-05 20:31:09', '2026-01-05 20:31:09'),
('527ad3e8-a543-402e-af9d-6b7df23691f5', '34354354', 'Nemo', NULL, '1bea77cd-e9fb-11f0-b538-089798c3b7e7', '5y56565', NULL, '34.00', '34.00', '78', '78', '0', '0', '0', '0', '10', '1', '0', '0.00', '2026-01-06 11:17:13', '2026-01-06 11:17:13', NULL),
('7cbb8eeb-eaeb-4ab8-9f9d-9e43c4b1320e', '12345', 'Test Product', NULL, '1bea756c-e9fb-11f0-b538-089798c3b7e7', '12345', NULL, '34.00', '34.00', '18', '20', '0', '0', '0', '0', '10', '1', '0', '0.00', '2026-01-06 10:57:49', '2026-01-06 11:02:41', NULL),
('afac2229-48dd-4e25-858b-bce851b6b7d7', '65756756', 'Sample Product', NULL, '1bea5b78-e9fb-11f0-b538-089798c3b7e7', '4549045', NULL, '467.00', '467.00', '8', '10', '0', '0', '0', '0', '10', '1', '0', '0.00', '2026-01-06 11:03:19', '2026-01-06 11:16:49', NULL),
('e2f4fe77-50ad-492c-ba09-b5311778e2cc', '4T4T46445', 'grgrtgrtrtr', NULL, '1bea7760-e9fb-11f0-b538-089798c3b7e7', 'fgfgfgf', NULL, '44.00', '44.00', '76', '76', '0', '0', '0', '0', '10', '1', '0', '0.00', '2026-01-06 11:23:44', '2026-01-06 11:23:54', '2026-01-06 11:23:54');

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id` char(36) NOT NULL,
  `item_id` char(36) DEFAULT NULL,
  `category_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_item` (`item_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_dates` (`start_date`,`end_date`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `promotions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `promotions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `promotions` VALUES
('87f9546d-226e-4b29-8f8b-f453a655252f', '527ad3e8-a543-402e-af9d-6b7df23691f5', NULL, 'P1', NULL, 'percentage', '5.00', '2026-01-07 00:00:00', '2026-01-07 00:00:00', '1', '2026-01-06 12:40:53', '2026-01-06 12:40:53', NULL),
('aacc84ba-d1a0-4d08-b374-edef05dbf9f0', NULL, '1bea77cd-e9fb-11f0-b538-089798c3b7e7', 'P1', NULL, 'percentage', '7.00', '2026-01-07 00:00:00', '2026-01-08 00:00:00', '1', '2026-01-06 12:41:03', '2026-01-06 12:51:48', NULL),
('badc07d5-032c-4f7d-aea2-4e177cd6c669', '527ad3e8-a543-402e-af9d-6b7df23691f5', NULL, 'P5', NULL, 'fixed', '50.00', '2026-01-08 00:00:00', '2026-01-14 23:59:59', '1', '2026-01-06 13:42:37', '2026-01-06 13:42:52', '2026-01-06 13:42:52');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` char(36) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` VALUES
('', 'shop_address', 'light', 'string', NULL, '2026-01-06 23:16:36', '2026-01-06 23:17:15'),
('1bedad5c-e9fb-11f0-b538-089798c3b7e7', 'shop_name', 'Lucky Book Shop', 'string', 'Shop name', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedc72d-e9fb-11f0-b538-089798c3b7e7', 'shop_logo', '', 'string', 'Shop logo path', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedc85c-e9fb-11f0-b538-089798c3b7e7', 'backup_auto_enabled', '1', 'boolean', 'Enable automatic backups', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedc89a-e9fb-11f0-b538-089798c3b7e7', 'backup_retention_days', '7', 'integer', 'Backup retention period in days', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedc8d7-e9fb-11f0-b538-089798c3b7e7', 'soft_delete_retention_days', '30', 'integer', 'Soft delete retention period in days', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedc926-e9fb-11f0-b538-089798c3b7e7', 'theme_mode', 'light', 'string', 'Theme mode (light/dark)', '2026-01-05 05:55:16', '2026-01-06 23:16:10'),
('1bedc9e9-e9fb-11f0-b538-089798c3b7e7', 'email_enabled', '0', 'boolean', 'Enable email notifications', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedca1e-e9fb-11f0-b538-089798c3b7e7', 'email_host', '', 'string', 'SMTP host', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedca5f-e9fb-11f0-b538-089798c3b7e7', 'email_port', '587', 'integer', 'SMTP port', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedca94-e9fb-11f0-b538-089798c3b7e7', 'email_username', '', 'string', 'SMTP username', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedcafa-e9fb-11f0-b538-089798c3b7e7', 'email_password', '', 'string', 'SMTP password', '2026-01-05 05:55:16', '2026-01-05 05:55:16'),
('1bedcb5c-e9fb-11f0-b538-089798c3b7e7', 'password_expiry_days', '7', 'integer', 'Password expiry period in days', '2026-01-05 05:55:16', '2026-01-05 05:55:16');

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements` (
  `id` char(36) NOT NULL,
  `item_id` char(36) NOT NULL,
  `movement_type` enum('in','out','return','damage','lost') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_type` enum('bill','manual','adjustment') DEFAULT NULL,
  `reference_id` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `movement_date` datetime NOT NULL,
  `created_by` char(36) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_item` (`item_id`),
  KEY `idx_movement_type` (`movement_type`),
  KEY `idx_movement_date` (`movement_date`),
  KEY `idx_reference` (`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier','supervisor') NOT NULL DEFAULT 'cashier',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `password_changed_at` datetime DEFAULT NULL,
  `password_expires_at` datetime DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES
('16cc158c-adc3-4ade-9016-edfb464deb95', 'test_1', 'test_re@gmail.com', '$2y$10$tMLw/LHSUCraaa2Twlj13ueFLtgDN71N.lpFLfpawgjBAsNaJesRm', 'cashier', 'Test', 'Edward', '1', NULL, '2026-01-13 10:56:28', '1', '2026-01-05 20:25:44', '2026-01-06 10:56:28', NULL),
('1be7e6fe-e9fb-11f0-b538-089798c3b7e7', 'admin', 'admin@luckybookshop.com', '$2y$10$pkyji9X0wUuTkDaih3gNt.6RF65FMRotVlHbfcxxYWOMxgJEOj2Uy', 'admin', 'System', 'Administrator', '1', '2026-01-06 22:40:57', NULL, '0', '2026-01-05 05:55:16', '2026-01-06 22:40:57', NULL),
('2cf8e70c-437f-47a4-9eaf-ad7661f0d786', 'reena_sky', 'reenasky@example.com', '$2y$10$O7CJTcmKwI36szbjlhF5ve3V4pidge8In2BfTgiDT54Ggc8TPFDUK', 'cashier', 'Reena', 'Sky', '1', NULL, '2026-01-12 20:06:44', '1', '2026-01-05 20:06:44', '2026-01-05 20:06:44', NULL),
('542e4c12-fa54-4d8a-aec2-7ec954f85c11', 'test_user', 'testuser@try.com', '$2y$10$vG0VlJwheZEtsNKOs5u3s.PNzry7x3GN8KpLLoU5cnlSh.Dwy85ju', 'supervisor', 'Test', 'User', '1', NULL, '2026-01-12 20:16:00', '1', '2026-01-05 20:16:00', '2026-01-05 20:16:00', NULL);

