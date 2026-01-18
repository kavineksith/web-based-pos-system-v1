<?php
/**
 * Device Model
 * Handles device registration and management for scanners and printers
 */

namespace App\Models;

use PDO;
use App\Config\Database;

class Device
{
    private $pdo;
    
    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Database::getInstance();
    }
    
    /**
     * Find or create device by identifier
     */
    public function findOrCreateDevice(string $deviceIdentifier, string $deviceName, string $deviceType, string $driver, array $settings = []): array
    {
        // First try to find existing device
        $stmt = $this->pdo->prepare("SELECT * FROM devices WHERE device_identifier = ?");
        $stmt->execute([$deviceIdentifier]);
        $device = $stmt->fetch();
        
        if ($device) {
            // Update existing device
            $updateStmt = $this->pdo->prepare("
                UPDATE devices 
                SET device_name = ?, device_type = ?, driver = ?, settings = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE device_identifier = ?
            ");
            $updateStmt->execute([$deviceName, $deviceType, $driver, json_encode($settings), $deviceIdentifier]);
            
            return [
                'id' => $device['id'],
                'device_identifier' => $device['device_identifier'],
                'device_name' => $deviceName,
                'device_type' => $deviceType,
                'driver' => $driver,
                'settings' => $settings,
                'is_default' => $device['is_default'],
                'created_at' => $device['created_at'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } else {
            // Create new device
            $stmt = $this->pdo->prepare("
                INSERT INTO devices (device_identifier, device_name, device_type, driver, settings, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$deviceIdentifier, $deviceName, $deviceType, $driver, json_encode($settings)]);
            
            $deviceId = $this->pdo->lastInsertId();
            
            return [
                'id' => $deviceId,
                'device_identifier' => $deviceIdentifier,
                'device_name' => $deviceName,
                'device_type' => $deviceType,
                'driver' => $driver,
                'settings' => $settings,
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
    }
    
    /**
     * Get all devices of a specific type
     */
    public function getDevicesByType(string $deviceType): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, device_identifier, device_name, device_type, driver, settings, is_default, created_at, updated_at 
            FROM devices 
            WHERE device_type = ?
            ORDER BY is_default DESC, device_name ASC
        ");
        $stmt->execute([$deviceType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get default device for a specific type
     */
    public function getDefaultDevice(string $deviceType): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, device_identifier, device_name, device_type, driver, settings, is_default, created_at, updated_at 
            FROM devices 
            WHERE device_type = ? AND is_default = 1
            LIMIT 1
        ");
        $stmt->execute([$deviceType]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }
    
    /**
     * Set default device
     */
    public function setDefaultDevice(int $deviceId, string $deviceType): bool
    {
        // First, unset all default devices of this type
        $stmt = $this->pdo->prepare("UPDATE devices SET is_default = 0 WHERE device_type = ?");
        $stmt->execute([$deviceType]);
        
        // Then set the specific device as default
        $stmt = $this->pdo->prepare("UPDATE devices SET is_default = 1 WHERE id = ?");
        $result = $stmt->execute([$deviceId]);
        
        return $result;
    }
    
    /**
     * Create the devices table if it doesn't exist
     */
    public function createTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS devices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_identifier VARCHAR(255) NOT NULL UNIQUE,
                device_name VARCHAR(255) NOT NULL,
                device_type ENUM('scanner', 'printer') NOT NULL,
                driver VARCHAR(100) NOT NULL,
                settings JSON,
                is_default BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_device_type (device_type),
                INDEX idx_is_default (is_default)
            )
        ";
        
        $this->pdo->exec($sql);
    }
}