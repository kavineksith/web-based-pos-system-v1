<?php
/**
 * Inventory Service
 */

namespace App\Services;

use App\Repositories\ItemRepository;
use App\Repositories\StockMovementRepository;
use App\Models\StockMovement;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class InventoryService
{
    private ItemRepository $itemRepository;
    private StockMovementRepository $stockMovementRepository;
    
    public function __construct()
    {
        $this->itemRepository = new ItemRepository();
        $this->stockMovementRepository = new StockMovementRepository();
    }
    
    public function stockIn(string $itemId, int $quantity, ?string $notes, string $userId): bool
    {
        $item = $this->itemRepository->findById($itemId);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        if (!InputValidator::validateQuantity($quantity)) {
            throw new ValidationException("Invalid quantity");
        }
        
        // Create stock movement
        $movement = new StockMovement();
        $movement->id = UuidGenerator::generate();
        $movement->itemId = $itemId;
        $movement->movementType = 'in';
        $movement->quantity = $quantity;
        $movement->referenceType = 'manual';
        $movement->referenceId = null;
        $movement->notes = $notes;
        $movement->movementDate = date('Y-m-d H:i:s');
        $movement->createdBy = $userId;
        
        $db = \App\Config\Database::getInstance();
        $db->beginTransaction();
        
        try {
            // Record movement
            $this->stockMovementRepository->create($movement);
            
            // Update item stock
            $item->stockQuantity += $quantity;
            $item->totalQuantity += $quantity;
            $this->itemRepository->update($item);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function stockOut(string $itemId, int $quantity, ?string $notes, string $userId): bool
    {
        $item = $this->itemRepository->findById($itemId);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        if (!InputValidator::validateQuantity($quantity)) {
            throw new ValidationException("Invalid quantity");
        }
        
        if ($item->stockQuantity < $quantity) {
            throw new ValidationException("Insufficient stock");
        }
        
        // Create stock movement
        $movement = new StockMovement();
        $movement->id = UuidGenerator::generate();
        $movement->itemId = $itemId;
        $movement->movementType = 'out';
        $movement->quantity = $quantity;
        $movement->referenceType = 'manual';
        $movement->referenceId = null;
        $movement->notes = $notes;
        $movement->movementDate = date('Y-m-d H:i:s');
        $movement->createdBy = $userId;
        
        $db = \App\Config\Database::getInstance();
        $db->beginTransaction();
        
        try {
            // Record movement
            $this->stockMovementRepository->create($movement);
            
            // Update item stock
            $item->stockQuantity -= $quantity;
            $this->itemRepository->update($item);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function recordDamage(string $itemId, int $quantity, ?string $notes, string $userId): bool
    {
        $item = $this->itemRepository->findById($itemId);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        if ($item->stockQuantity < $quantity) {
            throw new ValidationException("Insufficient stock");
        }
        
        $movement = new StockMovement();
        $movement->id = UuidGenerator::generate();
        $movement->itemId = $itemId;
        $movement->movementType = 'damage';
        $movement->quantity = $quantity;
        $movement->referenceType = 'adjustment';
        $movement->notes = $notes;
        $movement->movementDate = date('Y-m-d H:i:s');
        $movement->createdBy = $userId;
        
        $db = \App\Config\Database::getInstance();
        $db->beginTransaction();
        
        try {
            $this->stockMovementRepository->create($movement);
            $item->stockQuantity -= $quantity;
            $item->damageQuantity += $quantity;
            $this->itemRepository->update($item);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function recordLost(string $itemId, int $quantity, ?string $notes, string $userId): bool
    {
        $item = $this->itemRepository->findById($itemId);
        if (!$item) {
            throw new NotFoundException("Item not found");
        }
        
        if ($item->stockQuantity < $quantity) {
            throw new ValidationException("Insufficient stock");
        }
        
        $movement = new StockMovement();
        $movement->id = UuidGenerator::generate();
        $movement->itemId = $itemId;
        $movement->movementType = 'lost';
        $movement->quantity = $quantity;
        $movement->referenceType = 'adjustment';
        $movement->notes = $notes;
        $movement->movementDate = date('Y-m-d H:i:s');
        $movement->createdBy = $userId;
        
        $db = \App\Config\Database::getInstance();
        $db->beginTransaction();
        
        try {
            $this->stockMovementRepository->create($movement);
            $item->stockQuantity -= $quantity;
            $item->lostQuantity += $quantity;
            $this->itemRepository->update($item);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function getMovements(array $filters = []): array
    {
        return $this->stockMovementRepository->findAll($filters);
    }
    
    public function getLowStockItems(): array
    {
        $items = $this->itemRepository->findAll(['low_stock' => true]);
        return array_map(fn($item) => $item->toArray(), $items);
    }
    
    public function getOutOfStockItems(): array
    {
        $items = $this->itemRepository->findAll(['out_of_stock' => true]);
        return array_map(fn($item) => $item->toArray(), $items);
    }
}

