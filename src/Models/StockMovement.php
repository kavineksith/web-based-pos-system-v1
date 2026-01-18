<?php
/**
 * Stock Movement Model
 */

namespace App\Models;

class StockMovement
{
    public string $id;
    public string $itemId;
    public string $movementType; // in, out, return, damage, lost
    public int $quantity;
    public ?string $referenceType; // bill, manual, adjustment
    public ?string $referenceId;
    public ?string $notes;
    public string $movementDate;
    public string $createdBy;
    public string $createdAt;
    public string $updatedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->itemId = $data['item_id'] ?? '';
            $this->movementType = $data['movement_type'] ?? '';
            $this->quantity = (int)($data['quantity'] ?? 0);
            $this->referenceType = $data['reference_type'] ?? null;
            $this->referenceId = $data['reference_id'] ?? null;
            $this->notes = $data['notes'] ?? null;
            $this->movementDate = $data['movement_date'] ?? '';
            $this->createdBy = $data['created_by'] ?? '';
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->itemId,
            'movement_type' => $this->movementType,
            'quantity' => $this->quantity,
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'notes' => $this->notes,
            'movement_date' => $this->movementDate,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

