<?php
/**
 * Customer Model
 */

namespace App\Models;

class Customer
{
    public string $id;
    public string $name;
    public ?string $email;
    public ?string $phone;
    public ?string $address;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? null;
            $this->phone = $data['phone'] ?? null;
            $this->address = $data['address'] ?? null;
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
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

