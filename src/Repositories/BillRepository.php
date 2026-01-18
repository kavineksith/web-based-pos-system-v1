<?php
/**
 * Bill Repository
 */

namespace App\Repositories;

use App\Config\Database;
use App\Models\Bill;
use PDO;

class BillRepository
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findById(string $id): ?Bill
    {
        $stmt = $this->db->prepare("SELECT * FROM bills WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Bill($data) : null;
    }
    
    public function findByBillNumber(string $billNumber): ?Bill
    {
        $stmt = $this->db->prepare("SELECT * FROM bills WHERE bill_number = :bill_number AND deleted_at IS NULL");
        $stmt->execute(['bill_number' => $billNumber]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data ? new Bill($data) : null;
    }
    
    public function create(Bill $bill): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO bills (id, bill_number, customer_id, customer_name, customer_email,
                             staff_id, staff_name, start_time, end_time, subtotal, total_discount,
                             total_amount, paid_amount, balance, status, remarks, is_printed, is_email_sent)
            VALUES (:id, :bill_number, :customer_id, :customer_name, :customer_email,
                   :staff_id, :staff_name, :start_time, :end_time, :subtotal, :total_discount,
                   :total_amount, :paid_amount, :balance, :status, :remarks, :is_printed, :is_email_sent)
        ");
        
        return $stmt->execute([
            'id' => $bill->id,
            'bill_number' => $bill->billNumber,
            'customer_id' => $bill->customerId,
            'customer_name' => $bill->customerName,
            'customer_email' => $bill->customerEmail,
            'staff_id' => $bill->staffId,
            'staff_name' => $bill->staffName,
            'start_time' => $bill->startTime,
            'end_time' => $bill->endTime,
            'subtotal' => $bill->subtotal,
            'total_discount' => $bill->totalDiscount,
            'total_amount' => $bill->totalAmount,
            'paid_amount' => $bill->paidAmount,
            'balance' => $bill->balance,
            'status' => $bill->status,
            'remarks' => $bill->remarks,
            'is_printed' => $bill->isPrinted,
            'is_email_sent' => $bill->isEmailSent
        ]);
    }
    
    public function update(Bill $bill): bool
    {
        $stmt = $this->db->prepare("
            UPDATE bills SET 
                customer_id = :customer_id,
                customer_name = :customer_name,
                customer_email = :customer_email,
                end_time = :end_time,
                subtotal = :subtotal,
                total_discount = :total_discount,
                total_amount = :total_amount,
                paid_amount = :paid_amount,
                balance = :balance,
                status = :status,
                return_reason = :return_reason,
                return_authorized_by = :return_authorized_by,
                return_authorized_at = :return_authorized_at,
                remarks = :remarks,
                is_printed = :is_printed,
                is_email_sent = :is_email_sent
            WHERE id = :id AND deleted_at IS NULL
        ");
        
        return $stmt->execute([
            'id' => $bill->id,
            'customer_id' => $bill->customerId,
            'customer_name' => $bill->customerName,
            'customer_email' => $bill->customerEmail,
            'end_time' => $bill->endTime,
            'subtotal' => $bill->subtotal,
            'total_discount' => $bill->totalDiscount,
            'total_amount' => $bill->totalAmount,
            'paid_amount' => $bill->paidAmount,
            'balance' => $bill->balance,
            'status' => $bill->status,
            'return_reason' => $bill->returnReason,
            'return_authorized_by' => $bill->returnAuthorizedBy,
            'return_authorized_at' => $bill->returnAuthorizedAt,
            'remarks' => $bill->remarks,
            'is_printed' => $bill->isPrinted,
            'is_email_sent' => $bill->isEmailSent
        ]);
    }
    
    public function generateBillNumber(): string
    {
        $date = date('Ymd');
        $stmt = $this->db->query("
            SELECT COUNT(*) as count FROM bills 
            WHERE DATE(created_at) = CURDATE()
        ");
        $count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'] + 1;
        return 'BILL-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    
    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM bills WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['staff_id'])) {
            $sql .= " AND staff_id = :staff_id";
            $params['staff_id'] = $filters['staff_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (bill_number LIKE :search OR customer_name LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        // Don't bind LIMIT as parameter
        if (!empty($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $bills = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bills[] = new Bill($row);
        }
        
        return $bills;
    }
    
    public function findBetweenDates(string $fromDate, string $toDate, ?string $categoryId = null): array
    {
        $sql = "SELECT b.* FROM bills b ";
        $params = [];
        
        if ($categoryId) {
            $sql .= "JOIN bill_items bi ON b.id = bi.bill_id ";
            $sql .= "JOIN items i ON bi.item_id = i.id ";
            $sql .= "WHERE b.deleted_at IS NULL AND i.category_id = :category_id ";
            $params['category_id'] = $categoryId;
        } else {
            $sql .= "WHERE b.deleted_at IS NULL ";
        }
        
        $sql .= "AND b.created_at >= :from_date AND b.created_at <= :to_date ";
        $params['from_date'] = $fromDate;
        $params['to_date'] = $toDate;
        
        $sql .= "ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $bills = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bills[] = new Bill($row);
        }
        
        return $bills;
    }
}

