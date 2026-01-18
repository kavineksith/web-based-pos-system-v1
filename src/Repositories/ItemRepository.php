<?php
/**
 * Item Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\Item;
use PDO;

class ItemRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?Item
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Item($data) : null;
    }
    
    public function findByPluCode(string $pluCode): ?Item
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE plu_code = :plu_code AND deleted_at IS NULL");
        $stmt->execute(['plu_code' => $pluCode]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Item($data) : null;
    }
    
    public function findByBarcode(string $barcode): ?Item
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE barcode = :barcode AND deleted_at IS NULL");
        $stmt->execute(['barcode' => $barcode]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Item($data) : null;
    }
    
    public function create(Item $item): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO items (id, plu_code, name, description, category_id, barcode, qr_code, 
                             price, cost_price, stock_quantity, total_quantity, sell_quantity, return_quantity,
                             damage_quantity, lost_quantity, low_stock_threshold, is_active, has_discount, discount_percentage,
                             created_at, updated_at)
            VALUES (:id, :plu_code, :name, :description, :category_id, :barcode, :qr_code,
                   :price, :cost_price, :stock_quantity, :total_quantity, :sell_quantity, :return_quantity,
                   :damage_quantity, :lost_quantity, :low_stock_threshold, :is_active, :has_discount, :discount_percentage,
                   :created_at, :updated_at)"
        );
            
        return $stmt->execute([
            'id' => $item->id,
            'plu_code' => $item->pluCode,
            'name' => $item->name,
            'description' => $item->description,
            'category_id' => $item->categoryId,
            'barcode' => $item->barcode,
            'qr_code' => $item->qrCode,
            'price' => $item->price,
            'cost_price' => $item->costPrice,
            'stock_quantity' => $item->stockQuantity,
            'total_quantity' => $item->stockQuantity,
            'sell_quantity' => $item->sellQuantity,
            'return_quantity' => $item->returnQuantity,
            'damage_quantity' => $item->damageQuantity,
            'lost_quantity' => $item->lostQuantity,
            'low_stock_threshold' => $item->lowStockThreshold,
            'is_active' => $item->isActive,
            'has_discount' => $item->hasDiscount,
            'discount_percentage' => $item->discountPercentage,
            'created_at' => $item->createdAt,
            'updated_at' => $item->updatedAt
        ]);
    }
    
    public function update(Item $item): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE items SET 
                plu_code = :plu_code,
                name = :name,
                description = :description,
                category_id = :category_id,
                barcode = :barcode,
                qr_code = :qr_code,
                price = :price,
                cost_price = :cost_price,
                stock_quantity = :stock_quantity,
                sell_quantity = :sell_quantity,
                return_quantity = :return_quantity,
                damage_quantity = :damage_quantity,
                lost_quantity = :lost_quantity,
                low_stock_threshold = :low_stock_threshold,
                is_active = :is_active,
                has_discount = :has_discount,
                discount_percentage = :discount_percentage,
                updated_at = :updated_at
            WHERE id = :id AND deleted_at IS NULL"
        );
            
        return $stmt->execute([
            'id' => $item->id,
            'plu_code' => $item->pluCode,
            'name' => $item->name,
            'description' => $item->description,
            'category_id' => $item->categoryId,
            'barcode' => $item->barcode,
            'qr_code' => $item->qrCode,
            'price' => $item->price,
            'cost_price' => $item->costPrice,
            'stock_quantity' => $item->stockQuantity,
            'sell_quantity' => $item->sellQuantity,
            'return_quantity' => $item->returnQuantity,
            'damage_quantity' => $item->damageQuantity,
            'lost_quantity' => $item->lostQuantity,
            'low_stock_threshold' => $item->lowStockThreshold,
            'is_active' => $item->isActive,
            'has_discount' => $item->hasDiscount,
            'discount_percentage' => $item->discountPercentage,
            'updated_at' => $item->updatedAt
        ]);
    }
    
    public function softDelete(string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE items SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function hardDelete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM items WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM items WHERE deleted_at IS NULL";
        $params = [];
        
        // Filter by specific IDs (for export selected items)
        if (!empty($filters['ids']) && is_array($filters['ids'])) {
            $placeholders = [];
            foreach ($filters['ids'] as $index => $id) {
                $key = 'id_' . $index;
                $placeholders[] = ':' . $key;
                $params[$key] = $id;
            }
            $sql .= " AND id IN (" . implode(',', $placeholders) . ")";
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (!empty($filters['has_discount'])) {
            $sql .= " AND has_discount = :has_discount";
            $params['has_discount'] = $filters['has_discount'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['low_stock'])) {
            $sql .= " AND stock_quantity <= low_stock_threshold";
        }
        
        if (!empty($filters['out_of_stock'])) {
            $sql .= " AND stock_quantity = 0";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR plu_code LIKE :search OR barcode LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY name ASC";

        // IMPORTANT: Don't bind LIMIT as a parameter
        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit']; // Cast to int for safety
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new Item($row);
        }
        
        return $items;
    }
}

