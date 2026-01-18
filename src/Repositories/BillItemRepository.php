<?php
/**
 * Bill Item Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\BillItem;
use PDO;

class BillItemRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(BillItem $billItem): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO bill_items (id, bill_id, item_id, plu_code, item_name, quantity,
                                   unit_price, actual_price, discount, discount_percentage, subtotal)
            VALUES (:id, :bill_id, :item_id, :plu_code, :item_name, :quantity,
                   :unit_price, :actual_price, :discount, :discount_percentage, :subtotal)
        ");
        
        return $stmt->execute([
            'id' => $billItem->id,
            'bill_id' => $billItem->billId,
            'item_id' => $billItem->itemId,
            'plu_code' => $billItem->pluCode,
            'item_name' => $billItem->itemName,
            'quantity' => $billItem->quantity,
            'unit_price' => $billItem->unitPrice,
            'actual_price' => $billItem->actualPrice,
            'discount' => $billItem->discount,
            'discount_percentage' => $billItem->discountPercentage,
            'subtotal' => $billItem->subtotal
        ]);
    }
    
    public function findByBillId(string $billId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM bill_items WHERE bill_id = :bill_id");
        $stmt->execute(['bill_id' => $billId]);
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new BillItem($row);
        }
        
        return $items;
    }
    
    public function deleteByBillId(string $billId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bill_items WHERE bill_id = :bill_id");
        return $stmt->execute(['bill_id' => $billId]);
    }
}

