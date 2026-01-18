<?php
/**
 * Authentication Controller
 */

namespace App\Controllers;

use App\Services\AuthService;
use App\Exceptions\ValidationException;
use App\Exceptions\UnauthorizedException;

class AuthController extends BaseController
{
    private AuthService $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    public function showLogin(): void
    {
        // Check if already logged in
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if ($token && \App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/dashboard');
        }
        
        // Make base path available to view
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/login.php';
        exit;
    }
    
    public function login(): void
    {
        try {
            $data = $this->getRequestData();
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            
            $result = $this->authService->login($username, $password);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (UnauthorizedException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred during login'
            ], 500);
        }
    }
    
    public function logout(): void
    {
        // Get the token from the request to potentially invalidate it
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        
        // Destroy the session
        session_destroy();
        
        $this->jsonResponse([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    
    public function changePassword(): void
    {
        try {
            $data = $this->getRequestData();
            $userId = $_SESSION['user_id'] ?? '';
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';
            $confirmPassword = $data['confirm_password'] ?? '';
            
            $this->authService->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (UnauthorizedException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}

