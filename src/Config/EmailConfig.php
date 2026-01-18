<?php
/**
 * Email Configuration
 */

namespace App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

class EmailConfig
{
    private static ?PHPMailer $mailer = null;
    private static array $settings = [];
    
    /**
     * Load email settings from database
     */
    private static function loadSettings(): array
    {
        if (empty(self::$settings)) {
            try {
                $db = Database::getInstance();
                $keys = ['email_enabled', 'email_host', 'email_port', 'email_username', 'email_password'];
                $placeholders = implode(',', array_fill(0, count($keys), '?'));
                $stmt = $db->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ($placeholders)");
                $stmt->execute($keys);
                self::$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } catch (\Exception $e) {
                error_log("Failed to load email settings: " . $e->getMessage());
                self::$settings = [];
            }
        }
        return self::$settings;
    }
    
    /**
     * Check if email is enabled
     */
    public static function isEnabled(): bool
    {
        $settings = self::loadSettings();
        return (bool)($settings['email_enabled'] ?? false);
    }
    
    public static function getMailer(): PHPMailer
    {
        if (self::$mailer === null) {
            self::$mailer = new PHPMailer(true);
            
            // Load settings from database
            $settings = self::loadSettings();
            
            self::$mailer->isSMTP();
            self::$mailer->Host = $settings['email_host'] ?? 'smtp.gmail.com';
            self::$mailer->SMTPAuth = true;
            self::$mailer->Username = $settings['email_username'] ?? '';
            self::$mailer->Password = $settings['email_password'] ?? '';
            self::$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            self::$mailer->Port = (int)($settings['email_port'] ?? 587);
            self::$mailer->CharSet = 'UTF-8';
        }
        
        return self::$mailer;
    }
    
    public static function sendEmail(string $to, string $subject, string $body, bool $isHTML = true): bool
    {
        // Check if email is enabled
        if (!self::isEnabled()) {
            error_log("Email sending is disabled in settings");
            return false;
        }
        
        $settings = self::loadSettings();
        if (empty($settings['email_username']) || empty($settings['email_host'])) {
            error_log("Email not configured properly. Please set SMTP settings.");
            return false;
        }
        
        try {
            $mailer = self::getMailer();
            $mailer->clearAddresses();
            $mailer->setFrom($mailer->Username, 'Lucky Book Shop POS');
            $mailer->addAddress($to);
            $mailer->Subject = $subject;
            $mailer->Body = $body;
            $mailer->isHTML($isHTML);
            
            return $mailer->send();
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reset mailer instance (useful after settings change)
     */
    public static function reset(): void
    {
        self::$mailer = null;
        self::$settings = [];
    }
}

