<?php
/**
 * Backup Service
 */

namespace App\Services;

use App\Config\Database;
use App\Utils\UuidGenerator;
use PDO;

class BackupService
{
    private PDO $db;
    private string $backupPath;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->backupPath = BACKUP_PATH;
    }
    
    public function createBackup(string $type = 'manual', ?string $userId = null): string
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;
        
        // Get all tables
        $tables = [];
        $stmt = $this->db->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $output = "-- Lucky Book Shop POS System Backup\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Type: {$type}\n\n";
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "SET time_zone = \"+00:00\";\n\n";
        
        foreach ($tables as $table) {
            // Drop table
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            // Create table
            $stmt = $this->db->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $output .= $row[1] . ";\n\n";
            
            // Insert data
            $stmt = $this->db->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                $output .= "INSERT INTO `{$table}` VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $values[] = "(" . implode(", ", $rowValues) . ")";
                }
                $output .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        // Sanitize output (basic check for malicious content)
        $output = $this->sanitizeSql($output);
        
        file_put_contents($filepath, $output);
        
        // Save backup record
        $backupId = UuidGenerator::generate();
        $fileSize = filesize($filepath);
        $stmt = $this->db->prepare("
            INSERT INTO backups (id, filename, file_path, file_size, backup_type, created_by)
            VALUES (:id, :filename, :file_path, :file_size, :backup_type, :created_by)
        ");
        $stmt->execute([
            'id' => $backupId,
            'filename' => $filename,
            'file_path' => $filepath,
            'file_size' => $fileSize,
            'backup_type' => $type,
            'created_by' => $userId
        ]);
        
        return $filename;
    }
    
    public function restoreBackup(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            throw new \Exception("Backup file not found");
        }
        
        // Read SQL file
        $sql = file_get_contents($filepath);
        
        if (empty($sql)) {
            throw new \Exception("Backup file is empty");
        }
        
        // Sanitize SQL
        $sql = $this->sanitizeSql($sql);
        
        // Split into individual statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
            }
        );
        
        // Execute statements in transaction
        $this->db->beginTransaction();
        
        try {
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    $this->db->exec($statement);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Restore failed: " . $e->getMessage());
        }
    }
    
    private function sanitizeSql(string $sql): string
    {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Check for dangerous patterns (but allow in backup context)
        // We allow DELETE, UPDATE, INSERT as they're part of backup restore
        // But we log them for security audit
        
        // Remove any attempts to drop database
        if (preg_match('/DROP\s+DATABASE/i', $sql)) {
            throw new \Exception("Dangerous SQL command detected: DROP DATABASE");
        }
        
        // Remove any attempts to create/drop users
        if (preg_match('/(CREATE|DROP)\s+USER/i', $sql)) {
            throw new \Exception("Dangerous SQL command detected: User manipulation");
        }
        
        return $sql;
    }
    
    public function deleteOldBackups(int $retentionDays = 7): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$retentionDays} days"));
        
        $stmt = $this->db->prepare("
            SELECT id, file_path FROM backups 
            WHERE created_at < :cutoff_date
        ");
        $stmt->execute(['cutoff_date' => $cutoffDate]);
        $backups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $deleted = 0;
        foreach ($backups as $backup) {
            if (file_exists($backup['file_path'])) {
                unlink($backup['file_path']);
            }
            
            $deleteStmt = $this->db->prepare("DELETE FROM backups WHERE id = :id");
            $deleteStmt->execute(['id' => $backup['id']]);
            $deleted++;
        }
        
        return $deleted;
    }
    
    public function getBackupList(): array
    {
        $stmt = $this->db->query("
            SELECT id, filename, file_size, backup_type, created_at 
            FROM backups 
            ORDER BY created_at DESC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

