<?php
/**
 * Authentication Service
 */

namespace App\Services;

use App\Repositories\UserRepository;
use App\Config\JwtAuth;
use App\Config\EmailConfig;
use App\Config\Database;
use App\Utils\InputValidator;
use App\Utils\UuidGenerator;
use App\Exceptions\ValidationException;
use App\Exceptions\UnauthorizedException;
use PDO;

class AuthService
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    public function login(string $username, string $password): array
    {
        if (!InputValidator::validateNotEmpty($username) || !InputValidator::validateNotEmpty($password)) {
            throw new ValidationException("Username and password are required");
        }
        
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user || !$user->isActive) {
            throw new UnauthorizedException("Invalid credentials");
        }
        
        if (!password_verify($password, $user->password)) {
            throw new UnauthorizedException("Invalid credentials");
        }
        
        // Check if password needs to be changed
        $mustChangePassword = $user->mustChangePassword;
        if ($user->passwordExpiresAt && strtotime($user->passwordExpiresAt) < time()) {
            $mustChangePassword = true;
        }
        
        // Generate JWT token
        $token = JwtAuth::generateToken([
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'email' => $user->email
        ]);
        
        return [
            'token' => $token,
            'user' => $user->toArray(),
            'must_change_password' => $mustChangePassword
        ];
    }
    
    public function changePassword(string $userId, string $currentPassword, string $newPassword, string $confirmPassword): bool
    {
        if ($newPassword !== $confirmPassword) {
            throw new ValidationException("New password and confirm password do not match");
        }
        
        if (!InputValidator::validatePassword($newPassword)) {
            throw new ValidationException("Password must be at least 8 characters with uppercase, lowercase, and number");
        }
        
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UnauthorizedException("User not found");
        }
        
        if (!password_verify($currentPassword, $user->password)) {
            throw new UnauthorizedException("Current password is incorrect");
        }
        
        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->passwordChangedAt = date('Y-m-d H:i:s');
        $user->mustChangePassword = false;
        
        // Set password expiry based on settings (except for admin)
        if ($user->role !== 'admin') {
            $expiryDays = $this->getPasswordExpiryDays();
            $user->passwordExpiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
        }
        
        return $this->userRepository->update($user);
    }
    
    public function generateInitialPassword(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new UnauthorizedException("User not found");
        }
        
        // Generate random password
        $password = bin2hex(random_bytes(8));
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $user->password = $hashedPassword;
        $user->mustChangePassword = true;
        $user->passwordChangedAt = null;
        
        $this->userRepository->update($user);
        
        // Send email
        $subject = "Your POS System Login Credentials";
        $body = "Your temporary password is: {$password}<br>Please change it on first login.";
        EmailConfig::sendEmail($email, $subject, $body);
        
        return $password;
    }
    
    /**
     * Get password expiry days from settings
     */
    private function getPasswordExpiryDays(): int
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'password_expiry_days'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['value'] : 90;
        } catch (\Exception $e) {
            return 90; // Default to 90 days
        }
    }
}

