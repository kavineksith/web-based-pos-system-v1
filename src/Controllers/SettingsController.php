<?php
/**
 * Settings Controller
 */

namespace App\Controllers;

use App\Config\Database;
use PDO;

class SettingsController extends BaseController
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
        
        // Check admin role
        $payload = \App\Config\JwtAuth::validateToken($token);
        if ($payload && $payload['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/settings.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $stmt = $this->db->query("SELECT `key`, `value`, `type` FROM settings");
            $settings = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $value = $row['value'];
                
                // Convert based on type
                switch ($row['type']) {
                    case 'boolean':
                        $value = (bool)$value;
                        break;
                    case 'integer':
                        $value = (int)$value;
                        break;
                    case 'json':
                        $value = json_decode($value, true);
                        break;
                }
                
                $settings[$row['key']] = $value;
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function update(): void
    {
        try {
            $data = $this->getRequestData();
            
            foreach ($data as $key => $value) {
                // Determine the type based on the value
                $type = 'string';
                if (is_bool($value) || in_array($key, ['backup_auto_enabled', 'email_enabled', 'stock_alerts_enabled'])) {
                    $type = 'boolean';
                } elseif (is_numeric($value) && strpos($value, '.') === false && in_array($key, ['backup_retention_days', 'soft_delete_retention_days', 'email_port', 'password_expiry_days', 'low_stock_threshold'])) {
                    $type = 'integer';
                } elseif (is_numeric($value) && strpos($value, '.') !== false) {
                    $type = 'float';
                } elseif (is_array($value) || is_object($value)) {
                    $type = 'json';
                }
                
                // Convert value to string for storage
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                } else {
                    $value = (string)$value;
                }
                
                // Use INSERT ... ON DUPLICATE KEY UPDATE (upsert)
                $stmt = $this->db->prepare("
                    INSERT INTO settings (`key`, `value`, `type`) 
                    VALUES (:key, :value, :type)
                    ON DUPLICATE KEY UPDATE `value` = :value2, `type` = :type2
                ");
                
                $stmt->execute([
                    'key' => $key,
                    'value' => $value,
                    'type' => $type,
                    'value2' => $value,
                    'type2' => $type
                ]);
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}

