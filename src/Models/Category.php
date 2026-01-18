<?php
/**
 * Category Model
 */

namespace App\Models;

class Category
{
    public string $id;
    public string $name;
    public ?string $description;
    public bool $isActive;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? null;
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
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

