<?php
/**
 * Bill Item Model
 */

namespace App\Models;

class BillItem
{
    public string $id;
    public string $billId;
    public string $itemId;
    public string $pluCode;
    public string $itemName;
    public int $quantity;
    public float $unitPrice;
    public float $actualPrice;
    public float $discount;
    public float $discountPercentage;
    public float $subtotal;
    public string $createdAt;
    public string $updatedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->billId = $data['bill_id'] ?? '';
            $this->itemId = $data['item_id'] ?? '';
            $this->pluCode = $data['plu_code'] ?? '';
            $this->itemName = $data['item_name'] ?? '';
            $this->quantity = (int)($data['quantity'] ?? 1);
            $this->unitPrice = (float)($data['unit_price'] ?? 0);
            $this->actualPrice = (float)($data['actual_price'] ?? 0);
            $this->discount = (float)($data['discount'] ?? 0);
            $this->discountPercentage = (float)($data['discount_percentage'] ?? 0);
            $this->subtotal = (float)($data['subtotal'] ?? 0);
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'bill_id' => $this->billId,
            'item_id' => $this->itemId,
            'plu_code' => $this->pluCode,
            'item_name' => $this->itemName,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'actual_price' => $this->actualPrice,
            'discount' => $this->discount,
            'discount_percentage' => $this->discountPercentage,
            'subtotal' => $this->subtotal,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

