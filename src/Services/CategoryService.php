<?php
/**
 * Category Service
 */

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }
    
    public function create(array $data): Category
    {
        $errors = [];
        
        if (!InputValidator::validateNotEmpty($data['name'] ?? '')) {
            $errors['name'] = 'Category name is required';
        }
        
        if (!InputValidator::validateLength($data['name'] ?? '', 2, 255)) {
            $errors['name'] = 'Category name must be between 2 and 255 characters';
        }
        
        if ($this->categoryRepository->findByName($data['name'])) {
            $errors['name'] = 'Category name already exists';
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        $category = new Category();
        $category->id = UuidGenerator::generate();
        $category->name = InputValidator::sanitizeString($data['name']);
        $category->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        $category->isActive = $data['is_active'] ?? true;
        
        if (!$this->categoryRepository->create($category)) {
            throw new \Exception("Failed to create category");
        }
        
        return $category;
    }
    
    public function update(string $id, array $data): Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new NotFoundException("Category not found");
        }
        
        $errors = [];
        
        if (!empty($data['name'])) {
            if (!InputValidator::validateLength($data['name'], 2, 255)) {
                $errors['name'] = 'Category name must be between 2 and 255 characters';
            } else {
                $existing = $this->categoryRepository->findByName($data['name']);
                if ($existing && $existing->id !== $id) {
                    $errors['name'] = 'Category name already exists';
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        if (!empty($data['name'])) {
            $category->name = InputValidator::sanitizeString($data['name']);
        }
        
        if (isset($data['description'])) {
            $category->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        }
        
        if (isset($data['is_active'])) {
            $category->isActive = (bool)$data['is_active'];
        }
        
        if (!$this->categoryRepository->update($category)) {
            throw new \Exception("Failed to update category");
        }
        
        return $category;
    }
    
    public function delete(string $id, bool $hardDelete = false): bool
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new NotFoundException("Category not found");
        }
        
        if ($hardDelete) {
            return $this->categoryRepository->hardDelete($id);
        }
        
        return $this->categoryRepository->softDelete($id);
    }
    
    public function findAll(array $filters = []): array
    {
        return $this->categoryRepository->findAll($filters);
    }
    
    public function findById(string $id): Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new NotFoundException("Category not found");
        }
        
        return $category;
    }
}

