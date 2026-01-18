<?php
/**
 * Authentication Middleware
 */

namespace App\Middleware;

use App\Config\JwtAuth;

class AuthMiddleware
{
    public function handle(): bool
    {
        $token = JwtAuth::getTokenFromRequest();
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
        
        if (!$token) {
            if ($isApiRequest) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required'
                ]);
            } else {
                \App\Config\ErrorHandler::showErrorPage(401, 'Unauthorized', 'You need to be authenticated to access this resource.');
            }
            return false;
        }
        
        $payload = JwtAuth::validateToken($token);
        
        if (!$payload) {
            if ($isApiRequest) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ]);
            } else {
                \App\Config\ErrorHandler::showErrorPage(401, 'Unauthorized', 'Your session has expired. Please log in again.');
            }
            return false;
        }
        
        // Store user data in session for easy access
        $_SESSION['user_id'] = $payload['user_id'] ?? null;
        $_SESSION['role'] = $payload['role'] ?? null;
        $_SESSION['username'] = $payload['username'] ?? null;
        
        return true;
    }
}

