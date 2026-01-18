<?php
/**
 * Category Controller
 */

namespace App\Controllers;

use App\Services\CategoryService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CategoryController extends BaseController
{
    private CategoryService $categoryService;
    
    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }
    
    public function showCategories(): void
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
        require_once VIEWS_PATH . '/categories.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $categories = $this->categoryService->findAll($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($cat) => $cat->toArray(), $categories)
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
            $category = $this->categoryService->findById($id);
            
            if (!$category) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
                return;
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $category->toArray()
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
            $category = $this->categoryService->create($data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $category->toArray(),
                'message' => 'Category created successfully'
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
            $category = $this->categoryService->update($id, $data);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $category->toArray(),
                'message' => 'Category updated successfully'
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
            $this->categoryService->delete($id, $hardDelete);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Category deleted successfully'
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

