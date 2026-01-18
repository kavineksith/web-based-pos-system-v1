<?php
/**
 * Customer Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\Customer;
use App\Utils\UuidGenerator;
use PDO;

class CustomerRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?Customer
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Customer($data) : null;
    }
    
    public function findByEmail(string $email): ?Customer
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Customer($data) : null;
    }
    
    public function create(Customer $customer): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO customers (id, name, email, phone, address, created_at, updated_at)
            VALUES (:id, :name, :email, :phone, :address, :created_at, :updated_at)"
        );
            
        return $stmt->execute([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'created_at' => $customer->createdAt,
            'updated_at' => $customer->updatedAt
        ]);
    }
    
    public function createFromData(array $data): Customer
    {
        $customer = new Customer();
        $customer->id = UuidGenerator::generate();
        $customer->name = $data['name'] ?? '';
        $customer->email = $data['email'] ?? null;
        $customer->phone = $data['phone'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->createdAt = date('Y-m-d H:i:s');
        $customer->updatedAt = date('Y-m-d H:i:s');
        $customer->deletedAt = null;
        
        $this->create($customer);
        return $customer;
    }
    
    public function update(Customer $customer): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE customers SET 
                name = :name,
                email = :email,
                phone = :phone,
                address = :address,
                updated_at = :updated_at
            WHERE id = :id AND deleted_at IS NULL"
        );
            
        return $stmt->execute([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'updated_at' => $customer->updatedAt
        ]);
    }
    
    public function softDelete(string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE customers SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM customers WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY name ASC";
        
        // Don't bind LIMIT as parameter
        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $customers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = new Customer($row);
        }
        
        return $customers;
    }
}

