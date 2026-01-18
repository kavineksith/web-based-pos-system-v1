<?php
/**
 * Stock Movement Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\StockMovement;
use PDO;

class StockMovementRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(StockMovement $movement): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO stock_movements (id, item_id, movement_type, quantity, reference_type, 
                                       reference_id, notes, movement_date, created_by)
            VALUES (:id, :item_id, :movement_type, :quantity, :reference_type, 
                   :reference_id, :notes, :movement_date, :created_by)
        ");
        
        return $stmt->execute([
            'id' => $movement->id,
            'item_id' => $movement->itemId,
            'movement_type' => $movement->movementType,
            'quantity' => $movement->quantity,
            'reference_type' => $movement->referenceType,
            'reference_id' => $movement->referenceId,
            'notes' => $movement->notes,
            'movement_date' => $movement->movementDate,
            'created_by' => $movement->createdBy
        ]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT sm.*, i.name as item_name, i.plu_code, u.username as created_by_name
                FROM stock_movements sm
                LEFT JOIN items i ON sm.item_id = i.id
                LEFT JOIN users u ON sm.created_by = u.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['item_id'])) {
            $sql .= " AND sm.item_id = :item_id";
            $params['item_id'] = $filters['item_id'];
        }
        
        if (!empty($filters['movement_type'])) {
            $sql .= " AND sm.movement_type = :movement_type";
            $params['movement_type'] = $filters['movement_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(sm.movement_date) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(sm.movement_date) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY sm.movement_date DESC, sm.created_at DESC";
        
        // Don't bind LIMIT as parameter
        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $movements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movements[] = $row;
        }
        
        return $movements;
    }
    
    public function findById(string $id): ?StockMovement
    {
        $stmt = $this->db->prepare("SELECT * FROM stock_movements WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new StockMovement($data) : null;
    }
}

