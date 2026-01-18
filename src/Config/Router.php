<?php
/**
 * Router Configuration
 */

namespace App\Config;

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\ItemController;
use App\Controllers\BillingController;
use App\Controllers\InventoryController;
use App\Controllers\DashboardController;
use App\Controllers\SettingsController;
use App\Controllers\BackupController;
use App\Controllers\CustomerController;
use App\Controllers\UserController;
use App\Controllers\PromotionController;
use App\Controllers\DeviceController;
use App\Controllers\ReportsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class Router
{
    private array $routes = [];
    
    public function __construct()
    {
        $this->registerRoutes();
    }
    
    private function registerRoutes(): void
    {
        // View Routes
        $this->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
        $this->addRoute('GET', '/dashboard', [DashboardController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/billing', [BillingController::class, 'showBilling'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/inventory', [InventoryController::class, 'showInventory'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/items', [ItemController::class, 'showItems'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/categories', [CategoryController::class, 'showCategories'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/customers', [CustomerController::class, 'showCustomers'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/bills', [BillingController::class, 'showBills'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/reports', [ReportsController::class, 'showReports'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/users', [UserController::class, 'showUsers'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/promotions', [PromotionController::class, 'showPromotions'], [AuthMiddleware::class, RoleMiddleware::class . ':supervisor']);
        $this->addRoute('GET', '/settings', [SettingsController::class, 'show'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        // Root route: go to login (login page will redirect to dashboard if already authenticated)
        $this->addRoute('GET', '/', [AuthController::class, 'showLogin']);
        
        // Auth Routes
        $this->addRoute('POST', '/api/auth/login', [AuthController::class, 'login']);
        $this->addRoute('POST', '/api/auth/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/auth/change-password', [AuthController::class, 'changePassword'], [AuthMiddleware::class]);
        
        // User Routes (Admin only)
        $this->addRoute('GET', '/api/users', [UserController::class, 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/users/{id}', [UserController::class, 'show'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/users', [UserController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('PUT', '/api/users/{id}', [UserController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('DELETE', '/api/users/{id}', [UserController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/users/{id}/reset-password', [UserController::class, 'resetPassword'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Category Routes
        $this->addRoute('GET', '/api/categories', [CategoryController::class, 'index'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/categories/{id}', [CategoryController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/categories', [CategoryController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('PUT', '/api/categories/{id}', [CategoryController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('DELETE', '/api/categories/{id}', [CategoryController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Item Routes (specific routes MUST come before parameterized routes)
        $this->addRoute('GET', '/api/items', [ItemController::class, 'index'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/items', [ItemController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/items/bulk-delete', [ItemController::class, 'bulkDelete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/items/import', [ItemController::class, 'import'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/items/export', [ItemController::class, 'export'], [AuthMiddleware::class]);
        // Parameterized routes must come AFTER specific routes
        $this->addRoute('GET', '/api/items/{id}', [ItemController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('PUT', '/api/items/{id}', [ItemController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('DELETE', '/api/items/{id}', [ItemController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Billing Routes (specific routes first)
        $this->addRoute('GET', '/api/billing', [BillingController::class, 'list'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/billing/create', [BillingController::class, 'create'], [AuthMiddleware::class]);
        // Parameterized routes after
        $this->addRoute('GET', '/api/billing/{id}', [BillingController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/billing/{id}/return', [BillingController::class, 'return'], [AuthMiddleware::class, RoleMiddleware::class . ':supervisor']);
        $this->addRoute('POST', '/api/billing/{id}/cancel', [BillingController::class, 'cancel'], [AuthMiddleware::class, RoleMiddleware::class . ':supervisor']);
        
        // Inventory Routes
        $this->addRoute('GET', '/api/inventory', [InventoryController::class, 'index'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/inventory/stock-in', [InventoryController::class, 'stockIn'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/inventory/stock-out', [InventoryController::class, 'stockOut'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/inventory/damage', [InventoryController::class, 'recordDamage'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/inventory/lost', [InventoryController::class, 'recordLost'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/inventory/low-stock', [InventoryController::class, 'lowStock'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/inventory/out-of-stock', [InventoryController::class, 'outOfStock'], [AuthMiddleware::class]);
        
        // Dashboard Routes
        $this->addRoute('GET', '/api/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);
        
        // Settings Routes
        $this->addRoute('GET', '/api/settings', [SettingsController::class, 'index'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('PUT', '/api/settings', [SettingsController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Backup Routes
        $this->addRoute('POST', '/api/backup/create', [BackupController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/backup/list', [BackupController::class, 'list'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/backup/download/{filename}', [BackupController::class, 'download'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/backup/import', [BackupController::class, 'import'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Customer Routes
        $this->addRoute('GET', '/api/customers', [CustomerController::class, 'index'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/customers', [CustomerController::class, 'create'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/customers/{id}', [CustomerController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('PUT', '/api/customers/{id}', [CustomerController::class, 'update'], [AuthMiddleware::class]);
        $this->addRoute('DELETE', '/api/customers/{id}', [CustomerController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Promotion Routes
        $this->addRoute('GET', '/api/promotions', [PromotionController::class, 'index'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/promotions/{id}', [PromotionController::class, 'show'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/promotions', [PromotionController::class, 'create'], [AuthMiddleware::class, RoleMiddleware::class . ':supervisor']);
        $this->addRoute('PUT', '/api/promotions/{id}', [PromotionController::class, 'update'], [AuthMiddleware::class, RoleMiddleware::class . ':supervisor']);
        $this->addRoute('DELETE', '/api/promotions/{id}', [PromotionController::class, 'delete'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        
        // Device Routes (Scanners and Printers)
        $this->addRoute('GET', '/api/scanners', [DeviceController::class, 'getDevices'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/printers', [DeviceController::class, 'getDevices'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/printers/default', [DeviceController::class, 'getDefaultDevice'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/scanners/register', [DeviceController::class, 'registerDevice'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/printers/register', [DeviceController::class, 'registerDevice'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/scanners/set-default', [DeviceController::class, 'setDefaultDevice'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('POST', '/api/printers/set-default', [DeviceController::class, 'setDefaultDevice'], [AuthMiddleware::class, RoleMiddleware::class . ':admin']);
        $this->addRoute('GET', '/api/printers/bill/{id}', [DeviceController::class, 'getBillForPrinting'], [AuthMiddleware::class]);
        $this->addRoute('POST', '/api/bills/{id}/mark-printed', [DeviceController::class, 'markBillAsPrinted'], [AuthMiddleware::class]);
        
        // Report Routes
        $this->addRoute('GET', '/api/reports/sales', [ReportsController::class, 'getSalesReport'], [AuthMiddleware::class]);
        $this->addRoute('GET', '/api/reports/sales/export', [ReportsController::class, 'exportSalesReport'], [AuthMiddleware::class]);
    }
    
    private function addRoute(string $method, string $path, array $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Strip base path if project is in subdirectory
        // Use BASE_URL_PATH if defined (fixed as /pos-system in bootstrap)
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Ensure request URI starts with /
        if ($requestUri === '' || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        // Apply rate limiting
        $clientId = $_SERVER['REMOTE_ADDR'] . ($_SESSION['user_id'] ?? '');
        if (!RateLimiter::checkLimit($clientId)) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.'
            ]);
            exit;
        }
        
        foreach ($this->routes as $route) {
            $pattern = $this->convertPathToRegex($route['path']);
            
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                // Execute middleware
                foreach ($route['middleware'] as $middleware) {
                    if (is_string($middleware) && strpos($middleware, ':') !== false) {
                        [$middlewareClass, $param] = explode(':', $middleware, 2);
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle($param)) {
                            return;
                        }
                    } elseif (is_string($middleware)) {
                        $middlewareInstance = new $middleware();
                        if (!$middlewareInstance->handle()) {
                            return;
                        }
                    }
                }
                
                // Extract route parameters
                array_shift($matches);
                $params = array_values($matches);
                
                // Execute handler
                [$controllerClass, $method] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $method], $params);
                
                return;
            }
        }
        
        // 404 Not Found
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
        
        if ($isApiRequest) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Route not found'
            ]);
        } else {
            \App\Config\ErrorHandler::showErrorPage(404, 'Page Not Found', 'The page you\'re looking for could not be found.');
        }
    }
    
    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}

