<?php
/**
 * Device Controller
 * Handles API endpoints for device management (scanners and printers)
 */

namespace App\Controllers;

use App\Models\Device;
use App\Config\Database;

class DeviceController extends BaseController
{
    private Device $deviceModel;
    
    
    public function __construct()
    {
        $pdo = \App\Config\Database::getInstance();
        $this->deviceModel = new Device($pdo);
        // Create table if it doesn't exist
        $this->deviceModel->createTable();
    }
    
    /**
     * Get devices by type (scanner or printer)
     */
    public function getDevices(): void
    {
        try {
            $type = $_GET['type'] ?? null;
            
            if (!$type || !in_array($type, ['scanner', 'printer'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid device type. Use "scanner" or "printer".'
                ], 400);
                return;
            }
            
            $devices = $this->deviceModel->getDevicesByType($type);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $devices
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error fetching devices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get default device by type
     */
    public function getDefaultDevice(): void
    {
        try {
            $type = $_GET['type'] ?? null;
            
            if (!$type || !in_array($type, ['scanner', 'printer'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid device type. Use "scanner" or "printer".'
                ], 400);
                return;
            }
            
            $device = $this->deviceModel->getDefaultDevice($type);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $device
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error fetching default device: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Register a new device
     */
    public function registerDevice(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON input.'
                ], 400);
                return;
            }
            
            $requiredFields = ['device_identifier', 'device_name', 'device_type', 'driver'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ], 400);
                    return;
                }
            }
            
            $deviceType = $input['device_type'];
            if (!in_array($deviceType, ['scanner', 'printer'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid device type. Use "scanner" or "printer".'
                ], 400);
                return;
            }
            
            $device = $this->deviceModel->findOrCreateDevice(
                $input['device_identifier'],
                $input['device_name'],
                $deviceType,
                $input['driver'],
                $input['settings'] ?? []
            );
            
            $this->jsonResponse([
                'success' => true,
                'data' => $device
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error registering device: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Set default device
     */
    public function setDefaultDevice(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON input.'
                ], 400);
                return;
            }
            
            $requiredFields = ['device_id', 'device_type'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => "Missing required field: $field"
                    ], 400);
                    return;
                }
            }
            
            $deviceType = $input['device_type'];
            if (!in_array($deviceType, ['scanner', 'printer'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid device type. Use "scanner" or "printer".'
                ], 400);
                return;
            }
            
            $result = $this->deviceModel->setDefaultDevice($input['device_id'], $deviceType);
            
            if ($result) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Default device set successfully.'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to set default device.'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error setting default device: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get bill data for printing
     */
    public function getBillForPrinting($billId = null): void
    {
        try {
            // Extract billId from route parameters if not provided
            if ($billId === null) {
                // Extract from route - this should be handled by router
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
                if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
                    $requestUri = substr($requestUri, strlen($basePath));
                }
                    
                // Extract bill ID from URL
                preg_match('#/api/printers/bill/([^/]+)#', $_SERVER['REQUEST_URI'], $matches);
                $billId = $matches[1] ?? null;
            }
                
            if (!$billId || !is_numeric($billId)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid bill ID.'
                ], 400);
                return;
            }
                
            $billId = (int)$billId;
                
            // Get bill data from existing billing system
            $billController = new \App\Controllers\BillingController();
                
            // This is a workaround to get the bill data - we'll need to access the model directly
            $pdo = \App\Config\Database::getInstance();
                
            // Get bill details
            $stmt = $pdo->prepare(
                "SELECT b.*, u.username as staff_name \n                FROM bills b \n                LEFT JOIN users u ON b.user_id = u.id \n                WHERE b.id = ?"
            );
            $stmt->execute([$billId]);
            $bill = $stmt->fetch();
                
            if (!$bill) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Bill not found.'
                ], 404);
                return;
            }
                
            // Get bill items
            $stmt = $pdo->prepare(
                "SELECT bi.*, i.name as item_name \n                FROM bill_items bi \n                LEFT JOIN items i ON bi.item_id = i.id \n                WHERE bi.bill_id = ?"
            );
            $stmt->execute([$billId]);
            $items = $stmt->fetchAll();
                
            // Get shop settings from individual settings keys
            $shopKeys = ['shop_name', 'shop_address', 'shop_phone', 'receipt_footer'];
            $placeholders = implode(',', array_fill(0, count($shopKeys), '?'));
            $stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ($placeholders)");
            $stmt->execute($shopKeys);
            $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
            
            $shop = [
                'name' => $settings['shop_name'] ?? 'Lucky Book Shop',
                'address' => $settings['shop_address'] ?? '',
                'phone' => $settings['shop_phone'] ?? '',
                'receipt_footer' => $settings['receipt_footer'] ?? 'Thank you for shopping with us!'
            ];
                
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'bill' => $bill,
                    'items' => $items,
                    'shop' => $shop
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error fetching bill data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark bill as printed
     */
    public function markBillAsPrinted($billId = null): void
    {
        try {
            // Extract billId from route parameters if not provided
            if ($billId === null) {
                // Extract from route - this should be handled by router
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
                if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
                    $requestUri = substr($requestUri, strlen($basePath));
                }
                
                // Extract bill ID from URL
                preg_match('#/api/bills/([^/]+)/mark-printed#', $_SERVER['REQUEST_URI'], $matches);
                $billId = $matches[1] ?? null;
            }
            
            if (!$billId || !is_numeric($billId)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid bill ID.'
                ], 400);
                return;
            }
            
            $billId = (int)$billId;
            
            $pdo = \App\Config\Database::getInstance();
            
            // Update bill to mark as printed (add is_printed column if needed)
            $stmt = $pdo->prepare("UPDATE bills SET is_printed = 1 WHERE id = ?");
            $result = $stmt->execute([$billId]);
            
            if ($result) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Bill marked as printed.'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Failed to mark bill as printed.'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error marking bill as printed: ' . $e->getMessage()
            ], 500);
        }
    }
}