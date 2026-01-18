<?php
/**
 * CSRF Protection
 */

namespace App\Config;

class CsrfProtection
{
    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken(?string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token ?? '');
    }
    
    public static function getTokenField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::generateToken() . '">';
    }
}

