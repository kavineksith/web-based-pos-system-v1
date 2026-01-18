<?php
/**
 * Billing Controller
 */

namespace App\Controllers;

use App\Services\BillingService;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;

class BillingController extends BaseController
{
    private BillingService $billingService;
    
    public function __construct()
    {
        $this->billingService = new BillingService();
    }
    
    public function showBilling(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/billing.php';
        exit;
    }
    
    public function showBills(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }
        
        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/bills.php';
        exit;
    }
    
    public function create(): void
    {
        try {
            $data = $this->getRequestData();
            $staffId = $_SESSION['user_id'] ?? '';
            
            $bill = $this->billingService->createBill($data, $staffId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $bill->toArray(),
                'message' => 'Bill created successfully'
            ], 201);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (NotFoundException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(string $id): void
    {
        try {
            $data = $this->billingService->getBillWithItems($id);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $data
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
    
    public function return(string $id): void
    {
        try {
            $data = $this->getRequestData();
            $reason = $data['reason'] ?? '';
            $authorizedBy = $_SESSION['user_id'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($reason)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Return reason is required'
                ], 400);
                return;
            }
            
            $this->billingService->returnBill($id, $reason, $authorizedBy, $password);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Bill returned successfully'
            ]);
        } catch (UnauthorizedException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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
    
    public function cancel(string $id): void
    {
        try {
            $data = $this->getRequestData();
            $reason = $data['reason'] ?? '';
            $authorizedBy = $_SESSION['user_id'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($reason)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Cancellation reason is required'
                ], 400);
                return;
            }
            
            $this->billingService->cancelBill($id, $reason, $authorizedBy, $password);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Bill cancelled successfully'
            ]);
        } catch (UnauthorizedException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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
    
    public function list(): void
    {
        try {
            $filters = $_GET ?? [];
            $bills = $this->billingService->getBills($filters);
            
            $this->jsonResponse([
                'success' => true,
                'data' => array_map(fn($bill) => $bill->toArray(), $bills)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}


