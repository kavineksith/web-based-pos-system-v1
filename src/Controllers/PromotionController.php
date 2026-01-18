<?php
/**
 * Promotion Controller
 */

namespace App\Controllers;

use App\Services\PromotionService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class PromotionController extends BaseController
{
    private PromotionService $promotionService;
    
    public function __construct()
    {
        $this->promotionService = new PromotionService();
    }
    
    public function showPromotions(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        // Check role
        $payload = \App\Config\JwtAuth::validateToken($token);
        if ($payload && !in_array($payload['role'], ['admin', 'supervisor'])) {
            $this->redirect('/dashboard');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/promotions.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $promotions = $this->promotionService->findAll($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($promo) => $promo->toArray(), $promotions)
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
            $promotion = $this->promotionService->findById($id);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $promotion->toArray()
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
            $promotion = $this->promotionService->create($data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $promotion->toArray(),
                'message' => 'Promotion created successfully'
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
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(string $id): void
    {
        try {
            $data = $this->getRequestData();
            $promotion = $this->promotionService->update($id, $data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $promotion->toArray(),
                'message' => 'Promotion updated successfully'
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
            $this->promotionService->delete($id, $hardDelete);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Promotion deleted successfully'
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
}

