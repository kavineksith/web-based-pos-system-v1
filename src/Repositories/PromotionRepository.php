<?php
/**
 * Promotion Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\Promotion;
use PDO;

class PromotionRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?Promotion
    {
        $stmt = $this->db->prepare("SELECT * FROM promotions WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Promotion($data) : null;
    }
    
    public function findActiveForItem(string $itemId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM promotions 
            WHERE (item_id = :item_id OR item_id IS NULL)
            AND is_active = TRUE
            AND deleted_at IS NULL
            AND start_date <= NOW()
            AND end_date >= NOW()
            ORDER BY discount_value DESC
        ");
        $stmt->execute(['item_id' => $itemId]);
        
        $promotions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $promotion = new Promotion($row);
            if ($promotion->isActive()) {
                $promotions[] = $promotion;
            }
        }
        
        return $promotions;
    }
    
    public function findActiveForCategory(string $categoryId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM promotions 
            WHERE (category_id = :category_id OR category_id IS NULL)
            AND is_active = TRUE
            AND deleted_at IS NULL
            AND start_date <= NOW()
            AND end_date >= NOW()
            ORDER BY discount_value DESC
        ");
        $stmt->execute(['category_id' => $categoryId]);
        
        $promotions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $promotion = new Promotion($row);
            if ($promotion->isActive()) {
                $promotions[] = $promotion;
            }
        }
        
        return $promotions;
    }
    
    public function create(Promotion $promotion): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO promotions (id, item_id, category_id, name, description, discount_type, 
                                  discount_value, start_date, end_date, is_active, created_at, updated_at)
            VALUES (:id, :item_id, :category_id, :name, :description, :discount_type,
                   :discount_value, :start_date, :end_date, :is_active, :created_at, :updated_at)"
        );
            
        return $stmt->execute([
            'id' => $promotion->id,
            'item_id' => $promotion->itemId,
            'category_id' => $promotion->categoryId,
            'name' => $promotion->name,
            'description' => $promotion->description,
            'discount_type' => $promotion->discountType,
            'discount_value' => $promotion->discountValue,
            'start_date' => $promotion->startDate,
            'end_date' => $promotion->endDate,
            'is_active' => $promotion->isActive,
            'created_at' => $promotion->createdAt,
            'updated_at' => $promotion->updatedAt
        ]);
    }
    
    public function update(Promotion $promotion): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE promotions SET 
                item_id = :item_id,
                category_id = :category_id,
                name = :name,
                description = :description,
                discount_type = :discount_type,
                discount_value = :discount_value,
                start_date = :start_date,
                end_date = :end_date,
                is_active = :is_active,
                updated_at = :updated_at
            WHERE id = :id AND deleted_at IS NULL"
        );
            
        return $stmt->execute([
            'id' => $promotion->id,
            'item_id' => $promotion->itemId,
            'category_id' => $promotion->categoryId,
            'name' => $promotion->name,
            'description' => $promotion->description,
            'discount_type' => $promotion->discountType,
            'discount_value' => $promotion->discountValue,
            'start_date' => $promotion->startDate,
            'end_date' => $promotion->endDate,
            'is_active' => $promotion->isActive,
            'updated_at' => $promotion->updatedAt
        ]);
    }
    
    public function softDelete(string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE promotions SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function hardDelete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM promotions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM promotions WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (!empty($filters['item_id'])) {
            $sql .= " AND item_id = :item_id";
            $params['item_id'] = $filters['item_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        $sql .= " ORDER BY start_date DESC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $promotions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $promotions[] = new Promotion($row);
        }
        
        return $promotions;
    }
}

