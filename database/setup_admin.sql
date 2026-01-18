-- Default Admin User Setup
-- Password: password (bcrypt hash)

USE `lucky_bookshop_pos`;

-- Insert default admin user
INSERT INTO `users` (
    `id`,
    `username`,
    `email`,
    `password`,
    `role`,
    `first_name`,
    `last_name`,
    `is_active`,
    `must_change_password`,
    `created_at`,
    `updated_at`
) VALUES (
    UUID(),
    'admin',
    'admin@luckybookshop.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'admin',
    'Admin',
    'User',
    1,
    1,
    NOW(),
    NOW()
);

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('shop_name', 'Lucky Book Shop', 'Shop name displayed on bills'),
('shop_logo', '', 'Shop logo URL'),
('backup_auto_enabled', '1', 'Enable automatic backups'),
('backup_retention_days', '7', 'Number of days to keep backups'),
('soft_delete_retention_days', '30', 'Number of days before hard delete'),
('email_enabled', '0', 'Enable email notifications'),
('email_host', '', 'SMTP host'),
('email_port', '587', 'SMTP port'),
('email_username', '', 'SMTP username'),
('email_password', '', 'SMTP password'),
('password_expiry_days', '7', 'Password expiry in days'),
('theme_mode', 'light', 'Theme mode: light or dark')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

