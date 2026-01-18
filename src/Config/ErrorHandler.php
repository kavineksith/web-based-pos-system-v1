<?php
/**
 * Error Handler Configuration
 */

namespace App\Config;

class ErrorHandler
{
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $logMessage = sprintf(
            "[%s] Error: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $message,
            $file,
            $line
        );
        
        error_log($logMessage, 3, STORAGE_PATH . '/logs/error.log');
        
        return true;
    }
    
    public static function handleException(\Throwable $exception): void
    {
        $logMessage = sprintf(
            "[%s] Exception: %s in %s on line %d\nStack Trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        
        error_log($logMessage, 3, STORAGE_PATH . '/logs/error.log');
        
        // Check if it's an API request
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
        
        if ($isApiRequest) {
            // Clear any output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            
            // Show error details (set to false in production)
            $showDetails = true;
            
            if ($showDetails) {
                echo json_encode([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'An error occurred. Please contact administrator.'
                ]);
            }
        } else {
            self::showErrorPage(500, 'Internal Server Error', 'An error occurred. Please try again later.');
        }
        
        exit;
    }
    
    public static function showErrorPage(int $code, string $title = '', string $message = ''): void
    {
        http_response_code($code);
        
        $errorPages = [
            400 => '400',
            401 => '401',
            403 => '403',
            404 => '404',
            500 => '500'
        ];
        
        $errorFile = $errorPages[$code] ?? 'generic';
        $errorPath = VIEWS_PATH . "/errors/{$errorFile}.php";
        
        if (file_exists($errorPath)) {
            $errorCode = $code;
            $errorTitle = $title ?: self::getDefaultTitle($code);
            $errorMessage = $message ?: self::getDefaultMessage($code);
            
            require $errorPath;
        } else {
            // Fallback to generic error page
            $errorCode = $code;
            $errorTitle = $title ?: self::getDefaultTitle($code);
            $errorMessage = $message ?: self::getDefaultMessage($code);
            
            require VIEWS_PATH . '/errors/generic.php';
        }
        
        exit;
    }
    
    private static function getDefaultTitle(int $code): string
    {
        $titles = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Page Not Found',
            500 => 'Internal Server Error'
        ];
        
        return $titles[$code] ?? 'Error';
    }
    
    private static function getDefaultMessage(int $code): string
    {
        $messages = [
            400 => 'The request you sent was invalid or malformed.',
            401 => 'You need to be authenticated to access this resource.',
            403 => 'You don\'t have permission to access this resource.',
            404 => 'The page you\'re looking for could not be found.',
            500 => 'Something went wrong on our end. We\'re working to fix it.'
        ];
        
        return $messages[$code] ?? 'An error occurred. Please try again later.';
    }
    
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleException(new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }
}

