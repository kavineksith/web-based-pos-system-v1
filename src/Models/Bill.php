<?php
/**
 * Bill Model
 */

namespace App\Models;

class Bill
{
    public string $id;
    public string $billNumber;
    public ?string $customerId;
    public string $customerName;
    public ?string $customerEmail;
    public string $staffId;
    public string $staffName;
    public string $startTime;
    public ?string $endTime;
    public float $subtotal;
    public float $totalDiscount;
    public float $totalAmount;
    public float $paidAmount;
    public float $balance;
    public string $status; // pending, completed, returned, cancelled
    public ?string $returnReason;
    public ?string $returnAuthorizedBy;
    public ?string $returnAuthorizedAt;
    public ?string $remarks;
    public bool $isPrinted;
    public bool $isEmailSent;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? '';
            $this->billNumber = $data['bill_number'] ?? '';
            $this->customerId = $data['customer_id'] ?? null;
            $this->customerName = $data['customer_name'] ?? 'Customer';
            $this->customerEmail = $data['customer_email'] ?? null;
            $this->staffId = $data['staff_id'] ?? '';
            $this->staffName = $data['staff_name'] ?? '';
            $this->startTime = $data['start_time'] ?? '';
            $this->endTime = $data['end_time'] ?? null;
            $this->subtotal = (float)($data['subtotal'] ?? 0);
            $this->totalDiscount = (float)($data['total_discount'] ?? 0);
            $this->totalAmount = (float)($data['total_amount'] ?? 0);
            $this->paidAmount = (float)($data['paid_amount'] ?? 0);
            $this->balance = (float)($data['balance'] ?? 0);
            $this->status = $data['status'] ?? 'pending';
            $this->returnReason = $data['return_reason'] ?? null;
            $this->returnAuthorizedBy = $data['return_authorized_by'] ?? null;
            $this->returnAuthorizedAt = $data['return_authorized_at'] ?? null;
            $this->remarks = $data['remarks'] ?? null;
            $this->isPrinted = (bool)($data['is_printed'] ?? false);
            $this->isEmailSent = (bool)($data['is_email_sent'] ?? false);
            $this->createdAt = $data['created_at'] ?? '';
            $this->updatedAt = $data['updated_at'] ?? '';
            $this->deletedAt = $data['deleted_at'] ?? null;
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'bill_number' => $this->billNumber,
            'customer_id' => $this->customerId,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'staff_id' => $this->staffId,
            'staff_name' => $this->staffName,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'subtotal' => $this->subtotal,
            'total_discount' => $this->totalDiscount,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'balance' => $this->balance,
            'status' => $this->status,
            'return_reason' => $this->returnReason,
            'return_authorized_by' => $this->returnAuthorizedBy,
            'return_authorized_at' => $this->returnAuthorizedAt,
            'remarks' => $this->remarks,
            'is_printed' => $this->isPrinted,
            'is_email_sent' => $this->isEmailSent,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}

