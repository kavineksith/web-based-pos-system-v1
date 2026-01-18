<?php
/**
 * Category Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\Category;
use PDO;

class CategoryRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Category($data) : null;
    }
    
    public function findByName(string $name): ?Category
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE name = :name AND deleted_at IS NULL");
        $stmt->execute(['name' => $name]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Category($data) : null;
    }
    
    public function create(Category $category): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (id, name, description, is_active)
            VALUES (:id, :name, :description, :is_active)
        ");
        
        return $stmt->execute([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive
        ]);
    }
    
    public function update(Category $category): bool
    {
        $stmt = $this->db->prepare("
            UPDATE categories SET 
                name = :name,
                description = :description,
                is_active = :is_active
            WHERE id = :id AND deleted_at IS NULL
        ");
        
        return $stmt->execute([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive
        ]);
    }
    
    public function softDelete(string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function hardDelete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM categories WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row);
        }
        
        return $categories;
    }
}

