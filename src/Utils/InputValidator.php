<?php
/**
 * Input Validation Utility
 */

namespace App\Utils;

class InputValidator
{
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^[0-9+\-\s()]{7,20}$/', $phone) === 1;
    }
    
    public static function validatePluCode(string $plu): bool
    {
        return preg_match('/^[A-Z0-9]{3,50}$/i', $plu) === 1;
    }
    
    public static function validatePrice(string|float $price): bool
    {
        return is_numeric($price) && $price >= 0;
    }
    
    public static function validateQuantity(int $quantity): bool
    {
        return $quantity > 0 && $quantity <= 999999;
    }
    
    public static function sanitizeString(string $input, int $maxLength = 255): string
    {
        $sanitized = trim($input);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return substr($sanitized, 0, $maxLength);
    }
    
    public static function sanitizeText(string $input, int $maxLength = 65535): string
    {
        $sanitized = trim($input);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return substr($sanitized, 0, $maxLength);
    }
    
    public static function validateNotEmpty(string $input): bool
    {
        return !empty(trim($input));
    }
    
    public static function validateLength(string $input, int $min, int $max): bool
    {
        $length = mb_strlen($input);
        return $length >= $min && $length <= $max;
    }
    
    public static function validatePassword(string $password): bool
    {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password) === 1;
    }
}

