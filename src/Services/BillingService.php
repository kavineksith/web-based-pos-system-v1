<?php
/**
 * Billing Service
 */

namespace App\Services;

use App\Repositories\BillRepository;
use App\Repositories\BillItemRepository;
use App\Repositories\ItemRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\StockMovementRepository;
use App\Repositories\UserRepository;
use App\Services\PromotionService;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\StockMovement;
use App\Config\EmailConfig;
use App\Utils\UuidGenerator;
use App\Utils\InputValidator;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;

class BillingService
{
    private BillRepository $billRepository;
    private BillItemRepository $billItemRepository;
    private ItemRepository $itemRepository;
    private CustomerRepository $customerRepository;
    private StockMovementRepository $stockMovementRepository;
    private UserRepository $userRepository;
    private PromotionService $promotionService;
    
    public function __construct()
    {
        $this->billRepository = new BillRepository();
        $this->billItemRepository = new BillItemRepository();
        $this->itemRepository = new ItemRepository();
        $this->customerRepository = new CustomerRepository();
        $this->stockMovementRepository = new StockMovementRepository();
        $this->userRepository = new UserRepository();
        $this->promotionService = new PromotionService();
    }
    
    public function createBill(array $data, string $staffId): Bill
    {
        $db = \App\Config\Database::getInstance();
        $db->beginTransaction();
        
        try {
            // Get staff info
            $staff = $this->userRepository->findById($staffId);
            if (!$staff) {
                throw new NotFoundException("Staff member not found");
            }
            
            // Handle customer
            $customerId = null;
            $customerName = $data['customer_name'] ?? 'Customer';
            $customerEmail = $data['customer_email'] ?? null;
            
            if (!empty($data['customer_id'])) {
                $customerId = $data['customer_id'];
                $customer = $this->customerRepository->findById($customerId);
                if ($customer) {
                    $customerName = $customer->name;
                    $customerEmail = $customer->email;
                }
            } elseif (!empty($customerEmail) && InputValidator::validateEmail($customerEmail)) {
                // Create or find customer by email
                $customer = $this->customerRepository->findByEmail($customerEmail);
                if (!$customer) {
                    $customer = $this->customerRepository->createFromData([
                        'name' => $customerName,
                        'email' => $customerEmail,
                        'phone' => $data['customer_phone'] ?? null
                    ]);
                }
                $customerId = $customer->id;
            }
            
            // Create bill
            $bill = new Bill();
            $bill->id = UuidGenerator::generate();
            $bill->billNumber = $this->billRepository->generateBillNumber();
            $bill->customerId = $customerId;
            $bill->customerName = $customerName;
            $bill->customerEmail = $customerEmail;
            $bill->staffId = $staffId;
            $bill->staffName = $staff->firstName . ' ' . $staff->lastName;
            $bill->startTime = date('Y-m-d H:i:s');
            $bill->status = 'pending';
            
            $subtotal = 0;
            $totalDiscount = 0;
            
            // Process items
            $items = $data['items'] ?? [];
            if (empty($items)) {
                throw new ValidationException("Bill must have at least one item");
            }
            
            foreach ($items as $itemData) {
                $itemId = $itemData['item_id'] ?? '';
                $quantity = (int)($itemData['quantity'] ?? 1);
                $overridePrice = $itemData['override_price'] ?? null;
                $overrideDiscount = $itemData['override_discount'] ?? null;
                
                $item = $this->itemRepository->findById($itemId);
                if (!$item || !$item->isActive) {
                    throw new NotFoundException("Item not found or inactive: {$itemId}");
                }
                
                if ($item->stockQuantity < $quantity) {
                    throw new ValidationException("Insufficient stock for item: {$item->name}");
                }
                
                // Calculate price
                $unitPrice = $overridePrice !== null ? (float)$overridePrice : $item->price;
                $actualPrice = $unitPrice;
                
                // Calculate discount
                $discountPercentage = 0;
                $promotionDiscount = 0;
                
                if ($overrideDiscount !== null) {
                    $discountPercentage = (float)$overrideDiscount;
                } elseif ($item->hasDiscount) {
                    $discountPercentage = $item->discountPercentage;
                }
                
                // Check for active promotions
                $promotionDiscount = $this->promotionService->calculateDiscount(
                    $item->id,
                    $item->categoryId,
                    $actualPrice,
                    $quantity
                );
                
                // Use promotion discount if available, otherwise use item discount
                $itemDiscount = $promotionDiscount > 0 ? $promotionDiscount : (($actualPrice * $discountPercentage / 100) * $quantity);
                $itemSubtotal = ($actualPrice * $quantity) - $itemDiscount;
                
                // Create bill item
                $billItem = new BillItem();
                $billItem->id = UuidGenerator::generate();
                $billItem->billId = $bill->id;
                $billItem->itemId = $item->id;
                $billItem->pluCode = $item->pluCode;
                $billItem->itemName = $item->name;
                $billItem->quantity = $quantity;
                $billItem->unitPrice = $unitPrice;
                $billItem->actualPrice = $actualPrice;
                $billItem->discount = $itemDiscount;
                $billItem->discountPercentage = $promotionDiscount > 0 ? ($promotionDiscount / ($actualPrice * $quantity) * 100) : $discountPercentage;
                $billItem->subtotal = $itemSubtotal;
                
                $this->billItemRepository->create($billItem);
                
                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
                
                // Update item stock
                $item->stockQuantity -= $quantity;
                $item->sellQuantity += $quantity;
                $this->itemRepository->update($item);
                
                // Record stock movement
                $movement = new StockMovement();
                $movement->id = UuidGenerator::generate();
                $movement->itemId = $item->id;
                $movement->movementType = 'out';
                $movement->quantity = $quantity;
                $movement->referenceType = 'bill';
                $movement->referenceId = $bill->id;
                $movement->movementDate = date('Y-m-d H:i:s');
                $movement->createdBy = $staffId;
                $this->stockMovementRepository->create($movement);
            }
            
            $bill->subtotal = $subtotal;
            $bill->totalDiscount = $totalDiscount;
            $bill->totalAmount = $subtotal;
            $bill->paidAmount = (float)($data['paid_amount'] ?? $subtotal);
            $bill->balance = $bill->paidAmount - $bill->totalAmount;
            $bill->status = 'completed';
            $bill->endTime = date('Y-m-d H:i:s');
            $bill->remarks = $data['remarks'] ?? null;
            
            $this->billRepository->create($bill);
            
            $db->commit();
            
            // Send email if requested
            if (!empty($customerEmail) && ($data['send_email'] ?? false)) {
                $this->sendBillEmail($bill);
            }
            
            return $bill;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function returnBill(string $billId, string $reason, string $authorizedBy, string $password): bool
    {
        $db = \App\Config\Database::getInstance();
        
        // Verify supervisor
        $supervisor = $this->userRepository->findById($authorizedBy);
        if (!$supervisor || $supervisor->role !== 'supervisor') {
            throw new UnauthorizedException("Only supervisors can authorize returns");
        }
        
        if (!password_verify($password, $supervisor->password)) {
            throw new UnauthorizedException("Invalid password");
        }
        
        $bill = $this->billRepository->findById($billId);
        if (!$bill) {
            throw new NotFoundException("Bill not found");
        }
        
        if ($bill->status !== 'completed') {
            throw new ValidationException("Only completed bills can be returned");
        }
        
        $db->beginTransaction();
        
        try {
            // Get bill items
            $billItems = $this->billItemRepository->findByBillId($billId);
            
            // Return stock
            foreach ($billItems as $billItem) {
                $item = $this->itemRepository->findById($billItem->itemId);
                if ($item) {
                    $item->stockQuantity += $billItem->quantity;
                    $item->sellQuantity -= $billItem->quantity;
                    $item->returnQuantity += $billItem->quantity;
                    $this->itemRepository->update($item);
                    
                    // Record movement
                    $movement = new StockMovement();
                    $movement->id = UuidGenerator::generate();
                    $movement->itemId = $item->id;
                    $movement->movementType = 'return';
                    $movement->quantity = $billItem->quantity;
                    $movement->referenceType = 'bill';
                    $movement->referenceId = $billId;
                    $movement->notes = "Return: {$reason}";
                    $movement->movementDate = date('Y-m-d H:i:s');
                    $movement->createdBy = $authorizedBy;
                    $this->stockMovementRepository->create($movement);
                }
            }
            
            // Update bill
            $bill->status = 'returned';
            $bill->returnReason = $reason;
            $bill->returnAuthorizedBy = $authorizedBy;
            $bill->returnAuthorizedAt = date('Y-m-d H:i:s');
            $bill->remarks = $reason;
            
            $this->billRepository->update($bill);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function cancelBill(string $billId, string $reason, string $authorizedBy, string $password): bool
    {
        $db = \App\Config\Database::getInstance();
        
        // Verify supervisor
        $supervisor = $this->userRepository->findById($authorizedBy);
        if (!$supervisor || $supervisor->role !== 'supervisor') {
            throw new UnauthorizedException("Only supervisors can cancel bills");
        }
        
        if (!password_verify($password, $supervisor->password)) {
            throw new UnauthorizedException("Invalid password");
        }
        
        $bill = $this->billRepository->findById($billId);
        if (!$bill) {
            throw new NotFoundException("Bill not found");
        }
        
        if ($bill->status === 'cancelled') {
            throw new ValidationException("Bill is already cancelled");
        }
        
        $db->beginTransaction();
        
        try {
            // Get bill items
            $billItems = $this->billItemRepository->findByBillId($billId);
            
            // Return stock if bill was completed
            if ($bill->status === 'completed') {
                foreach ($billItems as $billItem) {
                    $item = $this->itemRepository->findById($billItem->itemId);
                    if ($item) {
                        $item->stockQuantity += $billItem->quantity;
                        $item->sellQuantity -= $billItem->quantity;
                        $this->itemRepository->update($item);
                    }
                }
            }
            
            // Update bill
            $bill->status = 'cancelled';
            $bill->returnReason = $reason;
            $bill->returnAuthorizedBy = $authorizedBy;
            $bill->returnAuthorizedAt = date('Y-m-d H:i:s');
            $bill->remarks = $reason;
            
            $this->billRepository->update($bill);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function getBillWithItems(string $billId): array
    {
        $bill = $this->billRepository->findById($billId);
        if (!$bill) {
            throw new NotFoundException("Bill not found");
        }
        
        $billItems = $this->billItemRepository->findByBillId($billId);
        
        return [
            'bill' => $bill->toArray(),
            'items' => array_map(fn($item) => $item->toArray(), $billItems)
        ];
    }
    
    private function sendBillEmail(Bill $bill): bool
    {
        if (empty($bill->customerEmail)) {
            return false;
        }
        
        $billItems = $this->billItemRepository->findByBillId($bill->id);
        
        $html = $this->generateBillHtml($bill, $billItems);
        
        $subject = "Your Bill from Lucky Book Shop - {$bill->billNumber}";
        
        if (EmailConfig::sendEmail($bill->customerEmail, $subject, $html)) {
            $bill->isEmailSent = true;
            $this->billRepository->update($bill);
            return true;
        }
        
        return false;
    }
    
    public function getBills(array $filters = []): array
    {
        return $this->billRepository->findAll($filters);
    }
    
    private function generateBillHtml(Bill $bill, array $billItems): string
    {
        $html = "<html><body>";
        $html .= "<h2>Lucky Book Shop</h2>";
        $html .= "<h3>Bill #{$bill->billNumber}</h3>";
        $html .= "<p>Date: {$bill->createdAt}</p>";
        $html .= "<p>Customer: {$bill->customerName}</p>";
        $html .= "<table border='1' cellpadding='5'>";
        $html .= "<tr><th>Item</th><th>Qty</th><th>Price</th><th>Discount</th><th>Subtotal</th></tr>";
        
        foreach ($billItems as $item) {
            $html .= "<tr>";
            $html .= "<td>{$item->itemName}</td>";
            $html .= "<td>{$item->quantity}</td>";
            $html .= "<td>Rs. " . number_format($item->unitPrice, 2) . "</td>";
            $html .= "<td>Rs. " . number_format($item->discount, 2) . "</td>";
            $html .= "<td>Rs. " . number_format($item->subtotal, 2) . "</td>";
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        $html .= "<p><strong>Subtotal: Rs. " . number_format($bill->subtotal, 2) . "</strong></p>";
        $html .= "<p><strong>Total Discount: Rs. " . number_format($bill->totalDiscount, 2) . "</strong></p>";
        $html .= "<p><strong>Total Amount: Rs. " . number_format($bill->totalAmount, 2) . "</strong></p>";
        $html .= "<p><strong>Paid: Rs. " . number_format($bill->paidAmount, 2) . "</strong></p>";
        $html .= "<p><strong>Balance: Rs. " . number_format($bill->balance, 2) . "</strong></p>";
        $html .= "</body></html>";
        
        return $html;
    }
}

