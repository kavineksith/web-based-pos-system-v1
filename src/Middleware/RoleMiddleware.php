<?php
/**
 * Role-Based Access Control Middleware
 */

namespace App\Middleware;

class RoleMiddleware
{
    private string $requiredRole;
    
    public function handle(string $role): bool
    {
        $this->requiredRole = $role;
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
        
        $userRole = $_SESSION['role'] ?? null;
        
        if (!$userRole) {
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
        
        $roleHierarchy = [
            'cashier' => 1,
            'supervisor' => 2,
            'admin' => 3
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$this->requiredRole] ?? 0;
        
        if ($userLevel < $requiredLevel) {
            if ($isApiRequest) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ]);
            } else {
                \App\Config\ErrorHandler::showErrorPage(403, 'Forbidden', 'You don\'t have permission to access this resource.');
            }
            return false;
        }
        
        return true;
    }
}

