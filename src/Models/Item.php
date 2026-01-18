<?php
/**
 * Item Model
 */

namespace App\Models;

class Item
{
    public string $id;
    public string $pluCode;
    public string $name;
    public ?string $description;
    public string $categoryId;
    public ?string $barcode;
    public ?string $qrCode;
    public float $price;
    public float $costPrice;
    public int $stockQuantity;
    public int $totalQuantity;
    public int $sellQuantity;        // ← This is causing the error
    public int $returnQuantity;
    public int $damageQuantity;
    public int $lostQuantity;
    public int $lowStockThreshold;
    public bool $isActive;
    public bool $hasDiscount;
    public float $discountPercentage;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->pluCode = $data['plu_code'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? null;
            $this->categoryId = $data['category_id'] ?? '';
            $this->barcode = $data['barcode'] ?? null;
            $this->qrCode = $data['qr_code'] ?? null;
            $this->price = (float)($data['price'] ?? 0);
            $this->costPrice = (float)($data['cost_price'] ?? 0);
            $this->stockQuantity = (int)($data['stock_quantity'] ?? 0);
            $this->totalQuantity = (int)($data['total_quantity'] ?? 0);
            $this->sellQuantity = (int)($data['sell_quantity'] ?? 0);      // ← Add this
            $this->returnQuantity = (int)($data['return_quantity'] ?? 0);  // ← Add this
            $this->damageQuantity = (int)($data['damage_quantity'] ?? 0);  // ← Add this
            $this->lostQuantity = (int)($data['lost_quantity'] ?? 0);      // ← Add this
            $this->lowStockThreshold = (int)($data['low_stock_threshold'] ?? 10);
            $this->isActive = (bool)($data['is_active'] ?? true);
            $this->hasDiscount = (bool)($data['has_discount'] ?? false);
            $this->discountPercentage = (float)($data['discount_percentage'] ?? 0);
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
            $this->deletedAt = $data['deleted_at'] ?? null;
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'plu_code' => $this->pluCode,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'barcode' => $this->barcode,
            'qr_code' => $this->qrCode,
            'price' => $this->price,
            'cost_price' => $this->costPrice,
            'stock_quantity' => $this->stockQuantity,
            'total_quantity' => $this->totalQuantity,
            'sell_quantity' => $this->sellQuantity,      // ← Add this
            'return_quantity' => $this->returnQuantity,  // ← Add this
            'damage_quantity' => $this->damageQuantity,  // ← Add this
            'lost_quantity' => $this->lostQuantity,      // ← Add this
            'low_stock_threshold' => $this->lowStockThreshold,
            'is_active' => $this->isActive,
            'has_discount' => $this->hasDiscount,
            'discount_percentage' => $this->discountPercentage,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}