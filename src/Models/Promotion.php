<?php
/**
 * Promotion Model
 */

namespace App\Models;

class Promotion
{
    public string $id;
    public ?string $itemId;
    public ?string $categoryId;
    public string $name;
    public ?string $description;
    public string $discountType; // percentage, fixed
    public float $discountValue;
    public string $startDate;
    public string $endDate;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->itemId = $data['item_id'] ?? null;
            $this->categoryId = $data['category_id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? null;
            $this->discountType = $data['discount_type'] ?? 'percentage';
            $this->discountValue = (float)($data['discount_value'] ?? 0);
            $this->startDate = $data['start_date'] ?? '';
            $this->endDate = $data['end_date'] ?? '';
            $this->isActive = (bool)($data['is_active'] ?? true);
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
            $this->deletedAt = $data['deleted_at'] ?? null;
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->itemId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discountType,
            'discount_value' => $this->discountValue,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
    
    public function isActive(): bool
    {
        if (!$this->isActive) {
            return false;
        }
        
        $now = time();
        $start = strtotime($this->startDate);
        $end = strtotime($this->endDate);
        
        return $now >= $start && $now <= $end;
    }
}

