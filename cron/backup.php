<?php
/**
 * Automatic Backup Cron Job
 * Run this script every 24 hours using cron
 * Example: 0 0 * * * php /path/to/cron/backup.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/bootstrap.php';

use App\Services\BackupService;
use App\Config\Database;
use PDO;

try {
    $db = Database::getInstance();
    
    // Check if auto backup is enabled
    $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'backup_auto_enabled'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $autoBackupEnabled = $result ? (bool)$result['value'] : true;
    
    if (!$autoBackupEnabled) {
        error_log("Auto backup is disabled. Skipping.");
        exit(0);
    }
    
    $backupService = new BackupService();
    
    // Create automatic backup
    $filename = $backupService->createBackup('automatic');
    
    // Delete old backups based on retention setting
    $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'backup_retention_days'");
    $retentionDays = (int)($stmt->fetch(PDO::FETCH_ASSOC)['value'] ?? 7);
    
    $deleted = $backupService->deleteOldBackups($retentionDays);
    
    // Log success
    error_log("Backup created: {$filename}, Old backups deleted: {$deleted}");
    
    // Clean up soft deleted items
    $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'soft_delete_retention_days'");
    $softDeleteDays = (int)($stmt->fetch(PDO::FETCH_ASSOC)['value'] ?? 30);
    $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$softDeleteDays} days"));
    
    // Hard delete old soft-deleted items
    $tables = ['users', 'categories', 'items', 'bills', 'customers'];
    foreach ($tables as $table) {
        $stmt = $db->prepare("DELETE FROM {$table} WHERE deleted_at IS NOT NULL AND deleted_at < :cutoff");
        $stmt->execute(['cutoff' => $cutoffDate]);
    }
    
    echo "Backup completed successfully\n";
} catch (Exception $e) {
    error_log("Backup failed: " . $e->getMessage());
    echo "Backup failed: " . $e->getMessage() . "\n";
    exit(1);
}

