<?php
/**
 * Bulk Operation Service
 */

namespace App\Services;

use App\Services\ItemService;
use App\Services\CategoryService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BulkOperationService
{
    private ItemService $itemService;
    private CategoryService $categoryService;
    
    public function __construct()
    {
        $this->itemService = new ItemService();
        $this->categoryService = new CategoryService();
    }
    
    public function importItems(string $filePath, string $format): array
    {
        $items = [];
        
        switch (strtolower($format)) {
            case 'json':
                $items = $this->importFromJson($filePath);
                break;
            case 'csv':
                $items = $this->importFromCsv($filePath);
                break;
            case 'excel':
            case 'xlsx':
            case 'xls':
                $items = $this->importFromExcel($filePath);
                break;
            default:
                throw new \Exception("Unsupported format: {$format}");
        }
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($items as $index => $itemData) {
            try {
                $this->itemService->create($itemData);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        return [
            'success' => $success,
            'failed' => $failed,
            'total' => count($items),
            'errors' => $errors
        ];
    }
    
    public function exportItems(string $format, array $filters = []): string
    {
        $items = $this->itemService->findAll($filters);
        $categories = $this->categoryService->findAll();
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat->id] = $cat->name;
        }
        
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'PLU Code' => $item->pluCode,
                'Name' => $item->name,
                'Category' => $categoryMap[$item->categoryId] ?? '',
                'Barcode' => $item->barcode ?? '',
                'Price' => $item->price,
                'Cost Price' => $item->costPrice,
                'Stock Quantity' => $item->stockQuantity,
                'Low Stock Threshold' => $item->lowStockThreshold,
                'Has Discount' => $item->hasDiscount ? 'Yes' : 'No',
                'Discount Percentage' => $item->discountPercentage,
                'Description' => $item->description ?? ''
            ];
        }
        
        // Determine correct file extension
        $extMap = [
            'json' => 'json',
            'csv' => 'csv',
            'excel' => 'xlsx',
            'xlsx' => 'xlsx',
            'xls' => 'xls'
        ];
        $ext = $extMap[strtolower($format)] ?? $format;
        $filePath = STORAGE_PATH . '/uploads/export_' . time() . '.' . $ext;
        
        switch (strtolower($format)) {
            case 'json':
                file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
                break;
            case 'csv':
                $this->exportToCsv($data, $filePath);
                break;
            case 'excel':
            case 'xlsx':
                $this->exportToExcel($data, $filePath);
                break;
            default:
                throw new \Exception("Unsupported format: {$format}");
        }
        
        return $filePath;
    }
    
    private function importFromJson(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON format: " . json_last_error_msg());
        }
        
        return is_array($data) ? $data : [$data];
    }
    
    private function importFromCsv(string $filePath): array
    {
        $items = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception("Failed to open CSV file");
        }
        
        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            throw new \Exception("Empty CSV file");
        }
        
        // Normalize headers
        $headers = array_map('trim', $headers);
        $headerMap = [];
        foreach ($headers as $index => $header) {
            $headerMap[strtolower(str_replace(' ', '_', $header))] = $index;
        }
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }
            
            $item = [];
            if (isset($headerMap['plu_code'])) {
                $item['plu_code'] = $row[$headerMap['plu_code']] ?? '';
            }
            if (isset($headerMap['name'])) {
                $item['name'] = $row[$headerMap['name']] ?? '';
            }
            if (isset($headerMap['category'])) {
                // Find category by name
                $categoryName = $row[$headerMap['category']] ?? '';
                $categories = $this->categoryService->findAll();
                foreach ($categories as $cat) {
                    if ($cat->name === $categoryName) {
                        $item['category_id'] = $cat->id;
                        break;
                    }
                }
            }
            if (isset($headerMap['barcode'])) {
                $item['barcode'] = $row[$headerMap['barcode']] ?? '';
            }
            if (isset($headerMap['price'])) {
                $item['price'] = $row[$headerMap['price']] ?? 0;
            }
            if (isset($headerMap['cost_price'])) {
                $item['cost_price'] = $row[$headerMap['cost_price']] ?? 0;
            }
            if (isset($headerMap['stock_quantity'])) {
                $item['stock_quantity'] = $row[$headerMap['stock_quantity']] ?? 0;
            }
            
            if (!empty($item)) {
                $items[] = $item;
            }
        }
        
        fclose($handle);
        return $items;
    }
    
    private function importFromExcel(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            throw new \Exception("Empty Excel file");
        }
        
        $headers = array_shift($rows);
        $headers = array_map('trim', $headers);
        $headerMap = [];
        foreach ($headers as $index => $header) {
            $headerMap[strtolower(str_replace(' ', '_', $header))] = $index;
        }
        
        $items = [];
        foreach ($rows as $row) {
            if (empty(array_filter($row))) {
                continue;
            }
            
            $item = [];
            if (isset($headerMap['plu_code'])) {
                $item['plu_code'] = $row[$headerMap['plu_code']] ?? '';
            }
            if (isset($headerMap['name'])) {
                $item['name'] = $row[$headerMap['name']] ?? '';
            }
            if (isset($headerMap['category'])) {
                $categoryName = $row[$headerMap['category']] ?? '';
                $categories = $this->categoryService->findAll();
                foreach ($categories as $cat) {
                    if ($cat->name === $categoryName) {
                        $item['category_id'] = $cat->id;
                        break;
                    }
                }
            }
            if (isset($headerMap['barcode'])) {
                $item['barcode'] = $row[$headerMap['barcode']] ?? '';
            }
            if (isset($headerMap['price'])) {
                $item['price'] = $row[$headerMap['price']] ?? 0;
            }
            if (isset($headerMap['cost_price'])) {
                $item['cost_price'] = $row[$headerMap['cost_price']] ?? 0;
            }
            if (isset($headerMap['stock_quantity'])) {
                $item['stock_quantity'] = $row[$headerMap['stock_quantity']] ?? 0;
            }
            
            if (!empty($item)) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    private function exportToCsv(array $data, string $filePath): void
    {
        $handle = fopen($filePath, 'w');
        
        if (empty($data)) {
            fclose($handle);
            return;
        }
        
        // Write headers
        fputcsv($handle, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
    }
    
    private function exportToExcel(array $data, string $filePath): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        if (empty($data)) {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            return;
        }
        
        // Write headers
        $headers = array_keys($data[0]);
        $sheet->fromArray([$headers], null, 'A1');
        
        // Write data
        $row = 2;
        foreach ($data as $item) {
            $sheet->fromArray([array_values($item)], null, "A{$row}");
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}

