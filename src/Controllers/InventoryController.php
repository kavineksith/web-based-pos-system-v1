<?php
/**
 * Inventory Controller
 */

namespace App\Controllers;

use App\Services\InventoryService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class InventoryController extends BaseController
{
    private InventoryService $inventoryService;
    
    public function __construct()
    {
        $this->inventoryService = new InventoryService();
    }
    
    public function showInventory(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        // Check admin role
        $payload = \App\Config\JwtAuth::validateToken($token);
        if ($payload && $payload['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/inventory.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $movements = $this->inventoryService->getMovements($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $movements
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function stockIn(): void
    {
        try {
            $data = $this->getRequestData();
            $itemId = $data['item_id'] ?? '';
            $quantity = (int)($data['quantity'] ?? 0);
            $notes = $data['notes'] ?? null;
            $userId = $_SESSION['user_id'] ?? '';
            
            $this->inventoryService->stockIn($itemId, $quantity, $notes, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Stock added successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function stockOut(): void
    {
        try {
            $data = $this->getRequestData();
            $itemId = $data['item_id'] ?? '';
            $quantity = (int)($data['quantity'] ?? 0);
            $notes = $data['notes'] ?? null;
            $userId = $_SESSION['user_id'] ?? '';
            
            $this->inventoryService->stockOut($itemId, $quantity, $notes, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Stock removed successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function recordDamage(): void
    {
        try {
            $data = $this->getRequestData();
            $itemId = $data['item_id'] ?? '';
            $quantity = (int)($data['quantity'] ?? 0);
            $notes = $data['notes'] ?? null;
            $userId = $_SESSION['user_id'] ?? '';
            
            $this->inventoryService->recordDamage($itemId, $quantity, $notes, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Damage recorded successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function recordLost(): void
    {
        try {
            $data = $this->getRequestData();
            $itemId = $data['item_id'] ?? '';
            $quantity = (int)($data['quantity'] ?? 0);
            $notes = $data['notes'] ?? null;
            $userId = $_SESSION['user_id'] ?? '';
            
            $this->inventoryService->recordLost($itemId, $quantity, $notes, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Lost item recorded successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function lowStock(): void
    {
        try {
            $items = $this->inventoryService->getLowStockItems();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function outOfStock(): void
    {
        try {
            $items = $this->inventoryService->getOutOfStockItems();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}

