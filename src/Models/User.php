<?php
/**
 * User Model
 */

namespace App\Models;

class User
{
    public string $id;
    public string $username;
    public string $email;
    public string $password;
    public string $role;
    public string $firstName;
    public string $lastName;
    public bool $isActive;
    public ?string $passwordChangedAt;
    public ?string $passwordExpiresAt;
    public bool $mustChangePassword;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->username = $data['username'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->role = $data['role'] ?? 'cashier';
            $this->firstName = $data['first_name'] ?? '';
            $this->lastName = $data['last_name'] ?? '';
            $this->isActive = (bool)($data['is_active'] ?? true);
            $this->passwordChangedAt = $data['password_changed_at'] ?? null;
            $this->passwordExpiresAt = $data['password_expires_at'] ?? null;
            $this->mustChangePassword = (bool)($data['must_change_password'] ?? true);
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
            $this->deletedAt = $data['deleted_at'] ?? null;
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'is_active' => $this->isActive,
            'password_changed_at' => $this->passwordChangedAt,
            'password_expires_at' => $this->passwordExpiresAt,
            'must_change_password' => $this->mustChangePassword,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

