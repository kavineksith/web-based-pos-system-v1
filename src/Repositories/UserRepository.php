<?php
/**
 * User Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\User;
use App\Exceptions\NotFoundException;
use PDO;

class UserRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new User($data) : null;
    }
    
    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND deleted_at IS NULL");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new User($data) : null;
    }
    
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new User($data) : null;
    }
    
    public function create(User $user): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (id, username, email, password, role, first_name, last_name, is_active, 
                             password_changed_at, password_expires_at, must_change_password)
            VALUES (:id, :username, :email, :password, :role, :first_name, :last_name, :is_active,
                   :password_changed_at, :password_expires_at, :must_change_password)
        ");
        
        return $stmt->execute([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'is_active' => $user->isActive,
            'password_changed_at' => $user->passwordChangedAt,
            'password_expires_at' => $user->passwordExpiresAt,
            'must_change_password' => $user->mustChangePassword
        ]);
    }
    
    public function update(User $user): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET 
                username = :username,
                email = :email,
                password = :password,
                role = :role,
                first_name = :first_name,
                last_name = :last_name,
                is_active = :is_active,
                password_changed_at = :password_changed_at,
                password_expires_at = :password_expires_at,
                must_change_password = :must_change_password
            WHERE id = :id AND deleted_at IS NULL
        ");
        
        return $stmt->execute([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'is_active' => $user->isActive,
            'password_changed_at' => $user->passwordChangedAt,
            'password_expires_at' => $user->passwordExpiresAt,
            'must_change_password' => $user->mustChangePassword
        ]);
    }
    
    public function softDelete(string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function hardDelete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM users WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
            $params['role'] = $filters['role'];
        }
        
        if (!empty($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (username LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        
        return $users;
    }
}

