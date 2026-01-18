<?php
/**
 * Item Service
 */

namespace App\Services;

use App\Repositories\ItemRepository;
use App\Repositories\CategoryRepository;
use App\Models\Item;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class ItemService
{
    private ItemRepository $itemRepository;
    private CategoryRepository $categoryRepository;
    
    public function __construct()
    {
        $this->itemRepository = new ItemRepository();
        $this->categoryRepository = new CategoryRepository();
    }
    
    public function create(array $data): Item
    {
        $errors = [];
        
        // Validate PLU Code
        if (!InputValidator::validateNotEmpty($data['plu_code'] ?? '')) {
            $errors['plu_code'] = 'PLU code is required';
        } elseif (!InputValidator::validatePluCode($data['plu_code'])) {
            $errors['plu_code'] = 'Invalid PLU code format';
        } elseif ($this->itemRepository->findByPluCode($data['plu_code'])) {
            $errors['plu_code'] = 'PLU code already exists';
        }
        
        // Validate name
        if (!InputValidator::validateNotEmpty($data['name'] ?? '')) {
            $errors['name'] = 'Item name is required';
        }
        
        // Validate category
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Category is required';
        } elseif (!$this->categoryRepository->findById($data['category_id'])) {
            $errors['category_id'] = 'Category not found';
        }
        
        // Validate price
        if (!InputValidator::validatePrice($data['price'] ?? 0)) {
            $errors['price'] = 'Valid price is required';
        }
        
        // Validate barcode uniqueness if provided
        if (!empty($data['barcode']) && $this->itemRepository->findByBarcode($data['barcode'])) {
            $errors['barcode'] = 'Barcode already exists';
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        $item = new Item();
        $item->id = UuidGenerator::generate();
        $item->pluCode = strtoupper(trim($data['plu_code']));
        $item->name = InputValidator::sanitizeString($data['name']);
        $item->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        $item->categoryId = $data['category_id'];
        $item->barcode = !empty($data['barcode']) ? trim($data['barcode']) : null;
        $item->qrCode = !empty($data['qr_code']) ? trim($data['qr_code']) : null;
        $item->price = (float)$data['price'];
        $item->costPrice = (float)($data['cost_price'] ?? $data['price']);
        $item->stockQuantity = (int)($data['stock_quantity'] ?? 0);
        $item->totalQuantity = $item->stockQuantity;
        $item->lowStockThreshold = (int)($data['low_stock_threshold'] ?? 10);
        $item->isActive = $data['is_active'] ?? true;
        $item->hasDiscount = (bool)($data['has_discount'] ?? false);
        $item->discountPercentage = (float)($data['discount_percentage'] ?? 0);
        $item->sellQuantity = 0; // Initialize to 0 for new items
        $item->returnQuantity = 0; // Initialize to 0 for new items
        $item->damageQuantity = 0; // Initialize to 0 for new items
        $item->lostQuantity = 0; // Initialize to 0 for new items
        $item->createdAt = date('Y-m-d H:i:s'); // Set current timestamp
        $item->updatedAt = date('Y-m-d H:i:s'); // Set current timestamp
        $item->deletedAt = null; // Initialize as null
        
        if (!$this->itemRepository->create($item)) {
            throw new \Exception("Failed to create item");
        }
        
        return $item;
    }
    
    public function update(string $id, array $data): Item
    {
        $item = $this->itemRepository->findById($id);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        $errors = [];
        
        // Validate PLU Code if changed
        if (!empty($data['plu_code']) && $data['plu_code'] !== $item->pluCode) {
            if (!InputValidator::validatePluCode($data['plu_code'])) {
                $errors['plu_code'] = 'Invalid PLU code format';
            } elseif ($this->itemRepository->findByPluCode($data['plu_code'])) {
                $errors['plu_code'] = 'PLU code already exists';
            }
        }
        
        // Validate category if changed
        if (!empty($data['category_id']) && $data['category_id'] !== $item->categoryId) {
            if (!$this->categoryRepository->findById($data['category_id'])) {
                $errors['category_id'] = 'Category not found';
            }
        }
        
        // Validate barcode if changed
        if (!empty($data['barcode']) && $data['barcode'] !== $item->barcode) {
            if ($this->itemRepository->findByBarcode($data['barcode'])) {
                $errors['barcode'] = 'Barcode already exists';
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException("Validation failed", $errors);
        }
        
        // Update fields
        if (!empty($data['plu_code'])) {
            $item->pluCode = strtoupper(trim($data['plu_code']));
        }
        if (!empty($data['name'])) {
            $item->name = InputValidator::sanitizeString($data['name']);
        }
        if (isset($data['description'])) {
            $item->description = !empty($data['description']) ? InputValidator::sanitizeText($data['description']) : null;
        }
        if (!empty($data['category_id'])) {
            $item->categoryId = $data['category_id'];
        }
        if (isset($data['barcode'])) {
            $item->barcode = !empty($data['barcode']) ? trim($data['barcode']) : null;
        }
        if (isset($data['qr_code'])) {
            $item->qrCode = !empty($data['qr_code']) ? trim($data['qr_code']) : null;
        }
        if (isset($data['price'])) {
            $item->price = (float)$data['price'];
        }
        if (isset($data['cost_price'])) {
            $item->costPrice = (float)$data['cost_price'];
        }
        // Update quantity fields and recalculate total quantity
        $updateTotalQuantity = false;
        
        if (isset($data['stock_quantity'])) {
            $item->stockQuantity = (int)$data['stock_quantity'];
            $updateTotalQuantity = true;
        }
        if (isset($data['low_stock_threshold'])) {
            $item->lowStockThreshold = (int)$data['low_stock_threshold'];
        }
        if (isset($data['is_active'])) {
            $item->isActive = (bool)$data['is_active'];
        }
        if (isset($data['has_discount'])) {
            $item->hasDiscount = (bool)$data['has_discount'];
        }
        if (isset($data['discount_percentage'])) {
            $item->discountPercentage = (float)$data['discount_percentage'];
        }
        if (isset($data['sell_quantity'])) {
            $item->sellQuantity = (int)$data['sell_quantity'];
            $updateTotalQuantity = true;
        }
        if (isset($data['return_quantity'])) {
            $item->returnQuantity = (int)$data['return_quantity'];
            $updateTotalQuantity = true;
        }
        if (isset($data['damage_quantity'])) {
            $item->damageQuantity = (int)$data['damage_quantity'];
            $updateTotalQuantity = true;
        }
        if (isset($data['lost_quantity'])) {
            $item->lostQuantity = (int)$data['lost_quantity'];
            $updateTotalQuantity = true;
        }
        
        // Update total quantity if any quantity field was modified
        if ($updateTotalQuantity) {
            $item->totalQuantity = $item->stockQuantity + $item->sellQuantity + $item->returnQuantity - $item->damageQuantity - $item->lostQuantity;
        }
        
        // Update the updated_at timestamp
        $item->updatedAt = date('Y-m-d H:i:s');
        
        if (!$this->itemRepository->update($item)) {
            throw new \Exception("Failed to update item");
        }
        
        return $item;
    }
    
    public function delete(string $id, bool $hardDelete = false): bool
    {
        $item = $this->itemRepository->findById($id);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        if ($hardDelete) {
            return $this->itemRepository->hardDelete($id);
        }
        
        return $this->itemRepository->softDelete($id);
    }
    
    public function findAll(array $filters = []): array
    {
        return $this->itemRepository->findAll($filters);
    }
    
    public function findById(string $id): Item
    {
        $item = $this->itemRepository->findById($id);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        return $item;
    }
    
    public function findByPluCode(string $pluCode): Item
    {
        $item = $this->itemRepository->findByPluCode($pluCode);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        return $item;
    }
    
    public function findByBarcode(string $barcode): Item
    {
        $item = $this->itemRepository->findByBarcode($barcode);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        return $item;
    }
}

