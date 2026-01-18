<?php
/**
 * JWT Authentication Configuration
 */

namespace App\Config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth
{
    private const SECRET_KEY = 'lucky_bookshop_pos_secret_key_change_in_production';
    private const ALGORITHM = 'HS256';
    private const EXPIRATION_TIME = 3600; // 1 hour
    
    public static function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + self::EXPIRATION_TIME;
        
        $tokenPayload = [
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $payload
        ];
        
        return JWT::encode($tokenPayload, self::SECRET_KEY, self::ALGORITHM);
    }
    
    public static function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::SECRET_KEY, self::ALGORITHM));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            error_log("JWT Validation Error: " . $e->getMessage());
            return null;
        }
    }
    
    public static function getTokenFromRequest(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        // Check URL query param (for file downloads like export)
        if (isset($_GET['token']) && !empty($_GET['token'])) {
            return $_GET['token'];
        }
        
        return $_COOKIE['auth_token'] ?? null;
    }
}

