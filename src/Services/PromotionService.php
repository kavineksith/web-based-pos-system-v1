<?php
/**
 * Promotion Service
 */

namespace App\Services;

use App\Repositories\PromotionRepository;
use App\Repositories\ItemRepository;
use App\Repositories\CategoryRepository;
use App\Models\Promotion;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class PromotionService
{
    private PromotionRepository $promotionRepository;
    private ItemRepository $itemRepository;
    private CategoryRepository $categoryRepository;
    
    public function __construct()
    {
        $this->promotionRepository = new PromotionRepository();
        $this->itemRepository = new ItemRepository();
        $this->categoryRepository = new CategoryRepository();
    }
    
    public function create(array $data): Promotion
    {
        $errors = [];
        
        if (!InputValidator::validateNotEmpty($data['name'] ?? '')) {
            $errors['name'] = 'Promotion name is required';
        }
        
        if (empty($data['item_id']) && empty($data['category_id'])) {
            $errors['target'] = 'Either item or category must be selected';
        }
        
        if (!empty($data['item_id']) && !$this->itemRepository->findById($data['item_id'])) {
            $errors['item_id'] = 'Item not found';
        }
        
        if (!empty($data['category_id']) && !$this->categoryRepository->findById($data['category_id'])) {
            $errors['category_id'] = 'Category not found';
        }
        
        $discountType = $data['discount_type'] ?? 'percentage';
        if (!in_array($discountType, ['percentage', 'fixed'])) {
            $errors['discount_type'] = 'Invalid discount type';
        }
        
        if (!InputValidator::validatePrice($data['discount_value'] ?? 0)) {
            $errors['discount_value'] = 'Valid discount value is required';
        }
        
        if (empty($data['start_date']) || empty($data['end_date'])) {
            $errors['dates'] = 'Start and end dates are required';
        } elseif (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors['dates'] = 'End date must be after start date';
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        $promotion = new Promotion();
        $promotion->id = UuidGenerator::generate();
        $promotion->itemId = !empty($data['item_id']) ? $data['item_id'] : null;
        $promotion->categoryId = !empty($data['category_id']) ? $data['category_id'] : null;
        $promotion->name = InputValidator::sanitizeString($data['name']);
        $promotion->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        $promotion->discountType = $discountType;
        $promotion->discountValue = (float)$data['discount_value'];
        $promotion->startDate = $data['start_date'];
        $promotion->endDate = $data['end_date'];
        $promotion->isActive = $data['is_active'] ?? true;
        $promotion->createdAt = date('Y-m-d H:i:s');
        $promotion->updatedAt = date('Y-m-d H:i:s');
        $promotion->deletedAt = null;
        
        if (!$this->promotionRepository->create($promotion)) {
            throw new \Exception("Failed to create promotion");
        }
        
        return $promotion;
    }
    
    public function update(string $id, array $data): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new NotFoundException("Promotion not found");
        }
        
        $errors = [];
        
        if (!empty($data['item_id']) && !$this->itemRepository->findById($data['item_id'])) {
            $errors['item_id'] = 'Item not found';
        }
        
        if (!empty($data['category_id']) && !$this->categoryRepository->findById($data['category_id'])) {
            $errors['category_id'] = 'Category not found';
        }
        
        if (!empty($data['end_date']) && !empty($data['start_date']) && 
            strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $errors['dates'] = 'End date must be after start date';
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        if (!empty($data['name'])) {
            $promotion->name = InputValidator::sanitizeString($data['name']);
        }
        if (isset($data['description'])) {
            $promotion->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        }
        if (!empty($data['item_id'])) {
            $promotion->itemId = $data['item_id'];
        }
        if (isset($data['category_id'])) {
            $promotion->categoryId = !empty($data['category_id']) ? $data['category_id'] : null;
        }
        if (!empty($data['discount_type'])) {
            $promotion->discountType = $data['discount_type'];
        }
        if (isset($data['discount_value'])) {
            $promotion->discountValue = (float)$data['discount_value'];
        }
        if (!empty($data['start_date'])) {
            $promotion->startDate = $data['start_date'];
        }
        if (!empty($data['end_date'])) {
            $promotion->endDate = $data['end_date'];
        }
        if (isset($data['is_active'])) {
            $promotion->isActive = (bool)$data['is_active'];
        }
        
        // Update the updated_at timestamp
        $promotion->updatedAt = date('Y-m-d H:i:s');
        
        if (!$this->promotionRepository->update($promotion)) {
            throw new \Exception("Failed to update promotion");
        }
        
        return $promotion;
    }
    
    public function delete(string $id, bool $hardDelete = false): bool
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new NotFoundException("Promotion not found");
        }
        
        if ($hardDelete) {
            return $this->promotionRepository->hardDelete($id);
        }
        
        return $this->promotionRepository->softDelete($id);
    }
    
    public function findAll(array $filters = []): array
    {
        return $this->promotionRepository->findAll($filters);
    }
    
    public function findById(string $id): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new NotFoundException("Promotion not found");
        }
        
        return $promotion;
    }
    
    public function getActivePromotionForItem(string $itemId): ?Promotion
    {
        $promotions = $this->promotionRepository->findActiveForItem($itemId);
        return !empty($promotions) ? $promotions[0] : null;
    }
    
    public function getActivePromotionForCategory(string $categoryId): ?Promotion
    {
        $promotions = $this->promotionRepository->findActiveForCategory($categoryId);
        return !empty($promotions) ? $promotions[0] : null;
    }
    
    public function calculateDiscount(string $itemId, string $categoryId, float $price, int $quantity): float
    {
        // Check item-specific promotion
        $promotion = $this->getActivePromotionForItem($itemId);
        
        // Check category promotion if no item promotion
        if (!$promotion) {
            $promotion = $this->getActivePromotionForCategory($categoryId);
        }
        
        if (!$promotion || !$promotion->isActive()) {
            return 0;
        }
        
        $totalPrice = $price * $quantity;
        
        if ($promotion->discountType === 'percentage') {
            return ($totalPrice * $promotion->discountValue / 100);
        } else {
            // Fixed discount per item
            return $promotion->discountValue * $quantity;
        }
    }
}

