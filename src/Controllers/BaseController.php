<?php
namespace App\Controllers;

class BaseController
{
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    protected function showError(int $code, string $title = '', string $message = ''): void
    {
        \App\Config\ErrorHandler::showErrorPage($code, $title, $message);
    }
    
    protected function getRequestData(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return $data ?? [];
    }
    
    protected function validateCsrfToken(): bool
    {
        $data = $this->getRequestData();
        $token = $data['csrf_token'] ?? $_POST['csrf_token'] ?? null;
        
        require_once __DIR__ . '/../Config/CsrfProtection.php';
        return \App\Config\CsrfProtection::validateToken($token);
    }
    
    protected function redirect(string $path): void
    {
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        $url = $basePath . $path;
        header('Location: ' . $url);
        exit;
    }
}
