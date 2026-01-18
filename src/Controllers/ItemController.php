<?php
/**
 * Item Controller
 */

namespace App\Controllers;

use App\Services\ItemService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class ItemController extends BaseController
{
    private ItemService $itemService;
    
    public function __construct()
    {
        $this->itemService = new ItemService();
    }
    
    public function showItems(): void
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
        require_once VIEWS_PATH . '/items.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $items = $this->itemService->findAll($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($item) => $item->toArray(), $items)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function show(string $id): void
    {
        try {
            $item = $this->itemService->findById($id);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $item->toArray()
            ]);
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
    
    public function create(): void
    {
        try {
            $data = $this->getRequestData();
            $item = $this->itemService->create($data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $item->toArray(),
                'message' => 'Item created successfully'
            ], 201);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function update(string $id): void
    {
        try {
            $data = $this->getRequestData();
            $item = $this->itemService->update($id, $data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $item->toArray(),
                'message' => 'Item updated successfully'
            ]);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function delete(string $id): void
    {
        try {
            $hardDelete = ($_GET['hard'] ?? 'false') === 'true';
            $this->itemService->delete($id, $hardDelete);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
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
    
    public function bulkDelete(): void
    {
        try {
            $data = $this->getRequestData();
            $ids = $data['ids'] ?? [];
            
            if (empty($ids) || !is_array($ids)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid item IDs'
                ], 400);
                return;
            }
            
            $deleted = 0;
            foreach ($ids as $id) {
                try {
                    $this->itemService->delete($id, false);
                    $deleted++;
                } catch (\Exception $e) {
                    // Continue with other items
                }
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => "Deleted {$deleted} item(s) successfully"
            ]);
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
            $format = $_GET['format'] ?? 'json';
            
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
            
            $service = new \App\Services\BulkOperationService();
            $result = $service->importItems($filePath, $format);
            
            $this->jsonResponse([
                'success' => true,
                'message' => "Imported {$result['success']} items successfully",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function export(): void
    {
        try {
            $format = $_GET['format'] ?? 'json';
            $filters = $_GET ?? [];
            unset($filters['format'], $filters['token']);
            
            $service = new \App\Services\BulkOperationService();
            $filePath = $service->exportItems($format, $filters);
            
            $extensions = [
                'json' => 'json',
                'csv' => 'csv',
                'excel' => 'xlsx',
                'xlsx' => 'xlsx',
                'xls' => 'xls'
            ];
            $ext = $extensions[strtolower($format)] ?? 'json';
            $fileName = 'items_export_' . date('Y-m-d_H-i-s') . '.' . $ext;
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            unlink($filePath);
            exit;
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

