<?php
/**
 * User Controller
 */

namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Config\Database;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Config\EmailConfig;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use PDO;

class UserController extends BaseController
{
    private UserRepository $userRepository;
    private AuthService $authService;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->authService = new AuthService();
    }
    
    public function showUsers(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        // Check admin role
        $payload = \App\Config\JwtAuth::validateToken($token);
        if ($payload && $payload['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/users.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $users = $this->userRepository->findAll($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($user) => $user->toArray(), $users)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function show(string $id): void
    {
        try {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                throw new NotFoundException("User not found");
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $user->toArray()
            ]);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function create(): void
    {
        try {
            $data = $this->getRequestData();
            $errors = [];
            
            // Validate username
            if (!InputValidator::validateNotEmpty($data['username'] ?? '')) {
                $errors['username'] = 'Username is required';
            } elseif (!InputValidator::validateLength($data['username'], 3, 50)) {
                $errors['username'] = 'Username must be between 3 and 50 characters';
            } elseif ($this->userRepository->findByUsername($data['username'])) {
                $errors['username'] = 'Username already exists';
            }
            
            // Validate email
            if (!InputValidator::validateNotEmpty($data['email'] ?? '')) {
                $errors['email'] = 'Email is required';
            } elseif (!InputValidator::validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email format';
            } elseif ($this->userRepository->findByEmail($data['email'])) {
                $errors['email'] = 'Email already exists';
            }
            
            // Validate role
            $validRoles = ['admin', 'cashier', 'supervisor'];
            if (empty($data['role']) || !in_array($data['role'], $validRoles)) {
                $errors['role'] = 'Valid role is required';
            }
            
            // Validate name
            if (!InputValidator::validateNotEmpty($data['first_name'] ?? '')) {
                $errors['first_name'] = 'First name is required';
            }
            if (!InputValidator::validateNotEmpty($data['last_name'] ?? '')) {
                $errors['last_name'] = 'Last name is required';
            }
            
            if (!empty($errors)) {
                throw new ValidationException("Validation failed", $errors);
            }
            
            // Create user
            $user = new User();
            $user->id = UuidGenerator::generate();
            $user->username = InputValidator::sanitizeString($data['username']);
            $user->email = trim($data['email']);
            $user->role = $data['role'];
            $user->firstName = InputValidator::sanitizeString($data['first_name']);
            $user->lastName = InputValidator::sanitizeString($data['last_name']);
            $user->isActive = $data['is_active'] ?? true;
            $user->mustChangePassword = true;
            $user->passwordChangedAt = null;
            
            // Generate initial password
            $initialPassword = bin2hex(random_bytes(8));
            $user->password = password_hash($initialPassword, PASSWORD_BCRYPT);
            
            // Set password expiry (7 days for non-admin)
            if ($user->role !== 'admin') {
                $db = Database::getInstance();
                $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'password_expiry_days'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $expiryDays = $result ? (int)$result['value'] : 7;
                $user->passwordExpiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
            }
            
            if (!$this->userRepository->create($user)) {
                throw new \Exception("Failed to create user");
            }
            
            // Send email with initial password
            $subject = "Your POS System Login Credentials";
            $body = "Hello {$user->firstName},<br><br>";
            $body .= "Your account has been created in Lucky Book Shop POS System.<br><br>";
            $body .= "<strong>Username:</strong> {$user->username}<br>";
            $body .= "<strong>Temporary Password:</strong> {$initialPassword}<br><br>";
            $body .= "Please change your password on first login.<br><br>";
            $body .= "Thank you!";
            
            EmailConfig::sendEmail($user->email, $subject, $body);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $user->toArray(),
                'message' => 'User created successfully. Initial password sent to email.',
                'initial_password' => $initialPassword // Only for admin reference
            ], 201);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(string $id): void
    {
        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                throw new NotFoundException("User not found");
            }
            
            $data = $this->getRequestData();
            $errors = [];
            
            // Validate email if changed
            if (!empty($data['email']) && $data['email'] !== $user->email) {
                if (!InputValidator::validateEmail($data['email'])) {
                    $errors['email'] = 'Invalid email format';
                } elseif ($this->userRepository->findByEmail($data['email'])) {
                    $errors['email'] = 'Email already exists';
                }
            }
            
            // Validate role
            if (!empty($data['role'])) {
                $validRoles = ['admin', 'cashier', 'supervisor'];
                if (!in_array($data['role'], $validRoles)) {
                    $errors['role'] = 'Invalid role';
                }
            }
            
            if (!empty($errors)) {
                throw new ValidationException("Validation failed", $errors);
            }
            
            // Update fields
            if (!empty($data['email'])) {
                $user->email = trim($data['email']);
            }
            if (!empty($data['first_name'])) {
                $user->firstName = InputValidator::sanitizeString($data['first_name']);
            }
            if (!empty($data['last_name'])) {
                $user->lastName = InputValidator::sanitizeString($data['last_name']);
            }
            if (isset($data['role'])) {
                $user->role = $data['role'];
            }
            if (isset($data['is_active'])) {
                $user->isActive = (bool)$data['is_active'];
            }
            
            if (!$this->userRepository->update($user)) {
                throw new \Exception("Failed to update user");
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $user->toArray(),
                'message' => 'User updated successfully'
            ]);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function delete(string $id): void
    {
        try {
            $currentUserId = $_SESSION['user_id'] ?? '';
            
            // Prevent self-deletion
            if ($id === $currentUserId) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 400);
                return;
            }
            
            $user = $this->userRepository->findById($id);
            if (!$user) {
                throw new NotFoundException("User not found");
            }
            
            $hardDelete = ($_GET['hard'] ?? 'false') === 'true';
            $result = $hardDelete ? $this->userRepository->hardDelete($id) : $this->userRepository->softDelete($id);
            
            if (!$result) {
                throw new \Exception("Failed to delete user");
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function resetPassword(string $id): void
    {
        try {
            $user = $this->userRepository->findById($id);
            if (!$user) {
                throw new NotFoundException("User not found");
            }
            
            // Generate new password
            $newPassword = bin2hex(random_bytes(8));
            $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->mustChangePassword = true;
            $user->passwordChangedAt = null;
            
            // Set password expiry
            if ($user->role !== 'admin') {
                $db = Database::getInstance();
                $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'password_expiry_days'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $expiryDays = $result ? (int)$result['value'] : 7;
                $user->passwordExpiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
            }
            
            if (!$this->userRepository->update($user)) {
                throw new \Exception("Failed to reset password");
            }
            
            // Send email with new password
            $subject = "Your Password Has Been Reset";
            $body = "Hello {$user->firstName},<br><br>";
            $body .= "Your password has been reset by administrator.<br><br>";
            $body .= "<strong>Username:</strong> {$user->username}<br>";
            $body .= "<strong>New Password:</strong> {$newPassword}<br><br>";
            $body .= "Please change your password on next login.<br><br>";
            $body .= "Thank you!";
            
            EmailConfig::sendEmail($user->email, $subject, $body);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Password reset successfully. New password sent to email.',
                'new_password' => $newPassword // Only for admin reference
            ]);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}

