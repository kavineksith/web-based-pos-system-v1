<?php
/**
 * Customer Controller
 */

namespace App\Controllers;

use App\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

class CustomerController extends BaseController
{
    private CustomerRepository $customerRepository;
    
    public function __construct()
    {
        $this->customerRepository = new CustomerRepository();
    }
    
    public function showCustomers(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/customers.php';
        exit;
    }
    
    public function index(): void
    {
        try {
            $filters = $_GET ?? [];
            $customers = $this->customerRepository->findAll($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($customer) => $customer->toArray(), $customers)
            ]);
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
            
            if (!InputValidator::validateNotEmpty($data['name'] ?? '')) {
                $errors['name'] = 'Customer name is required';
            }
            
            if (!empty($data['email']) && !InputValidator::validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email format';
            }
            
            if (!empty($data['phone']) && !InputValidator::validatePhone($data['phone'])) {
                $errors['phone'] = 'Invalid phone format';
            }
            
            if (!empty($errors)) {
                throw new ValidationException("Validation failed", $errors);
            }
            
            $customer = new Customer();
            $customer->id = UuidGenerator::generate();
            $customer->name = InputValidator::sanitizeString($data['name']);
            $customer->email = !empty($data['email']) ? trim($data['email']) : null;
            $customer->phone = !empty($data['phone']) ? trim($data['phone']) : null;
            $customer->address = !empty($data['address']) ? InputValidator::sanitizeText($data['address']) : null;
            $customer->createdAt = date('Y-m-d H:i:s');
            $customer->updatedAt = date('Y-m-d H:i:s');
            $customer->deletedAt = null;
            
            $this->customerRepository->create($customer);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $customer->toArray(),
                'message' => 'Customer created successfully'
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
                'message' => 'An error occurred'
            ], 500);
        }
    }
    
    public function show(string $id): void
    {
        try {
            $customer = $this->customerRepository->findById($id);
            
            if (!$customer) {
                throw new NotFoundException("Customer not found");
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $customer->toArray()
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
    
    public function update(string $id): void
    {
        try {
            $customer = $this->customerRepository->findById($id);
            if (!$customer) {
                throw new NotFoundException("Customer not found");
            }
            
            $data = $this->getRequestData();
            $errors = [];
            
            if (!empty($data['email']) && !InputValidator::validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email format';
            }
            
            if (!empty($data['phone']) && !InputValidator::validatePhone($data['phone'])) {
                $errors['phone'] = 'Invalid phone format';
            }
            
            if (!empty($errors)) {
                throw new ValidationException("Validation failed", $errors);
            }
            
            if (!empty($data['name'])) {
                $customer->name = InputValidator::sanitizeString($data['name']);
            }
            if (isset($data['email'])) {
                $customer->email = !empty($data['email']) ? trim($data['email']) : null;
            }
            if (isset($data['phone'])) {
                $customer->phone = !empty($data['phone']) ? trim($data['phone']) : null;
            }
            if (isset($data['address'])) {
                $customer->address = !empty($data['address']) ? InputValidator::sanitizeText($data['address']) : null;
            }
            
            // Update the updated_at timestamp
            $customer->updatedAt = date('Y-m-d H:i:s');
            
            $this->customerRepository->update($customer);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $customer->toArray(),
                'message' => 'Customer updated successfully'
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
            $customer = $this->customerRepository->findById($id);
            if (!$customer) {
                throw new NotFoundException("Customer not found");
            }
            
            $this->customerRepository->softDelete($id);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Customer deleted successfully'
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

