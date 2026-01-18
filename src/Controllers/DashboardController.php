<?php
/**
 * Dashboard Controller
 */

namespace App\Controllers;

use App\Config\Database;
use PDO;

class DashboardController extends BaseController
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function show(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        // Make base path available to view
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/dashboard.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            // Today's sales
            $stmt = $this->db->query("
                SELECT COALESCE(SUM(total_amount), 0) as today_sales
                FROM bills
                WHERE DATE(created_at) = CURDATE()
                AND status = 'completed'
                AND deleted_at IS NULL
            ");
            $todaySales = $stmt->fetch(PDO::FETCH_ASSOC)['today_sales'] ?? 0;
            
            // Low stock items
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM items
                WHERE stock_quantity <= low_stock_threshold
                AND stock_quantity > 0
                AND deleted_at IS NULL
            ");
            $lowStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Out of stock items
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM items
                WHERE stock_quantity = 0
                AND deleted_at IS NULL
            ");
            $outOfStockCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Total items
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM items
                WHERE deleted_at IS NULL
            ");
            $totalItems = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Total categories
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM categories
                WHERE deleted_at IS NULL
            ");
            $totalCategories = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'today_sales' => (float)$todaySales,
                    'low_stock_count' => (int)$lowStockCount,
                    'out_of_stock_count' => (int)$outOfStockCount,
                    'total_items' => (int)$totalItems,
                    'total_categories' => (int)$totalCategories
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}

