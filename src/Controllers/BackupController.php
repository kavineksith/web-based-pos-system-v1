<?php
/**
 * Backup Controller
 */

namespace App\Controllers;

use App\Services\BackupService;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;

class BackupController extends BaseController
{
    private BackupService $backupService;
    
    public function __construct()
    {
        $this->backupService = new BackupService();
    }
    
    public function create(): void
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $filename = $this->backupService->createBackup('manual', $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Backup created successfully',
                'data' => ['filename' => $filename]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function list(): void
    {
        try {
            $backups = $this->backupService->getBackupList();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $backups
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function download(string $filename): void
    {
        try {
            $filepath = BACKUP_PATH . '/' . $filename;
            
            if (!file_exists($filepath)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
                return;
            }
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function import(): void
    {
        try {
            if (!isset($_FILES['file'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
                return;
            }
            
            $file = $_FILES['file'];
            $filePath = $file['tmp_name'];
            $fileName = $file['name'];
            
            // Validate file extension
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($extension !== 'sql') {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Only SQL files are allowed'
                ], 400);
                return;
            }
            
            // Validate file size (max 50MB)
            if ($file['size'] > 50 * 1024 * 1024) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'File size exceeds 50MB limit'
                ], 400);
                return;
            }
            
            // Sanitize and restore
            $this->backupService->restoreBackup($filePath);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Database imported successfully'
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
