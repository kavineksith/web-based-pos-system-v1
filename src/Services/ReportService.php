<?php
/**
 * Report Service
 */

namespace App\Services;

use App\Repositories\BillRepository;
use App\Repositories\BillItemRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ItemRepository;
use App\Services\BulkOperationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    private BillRepository $billRepository;
    private BillItemRepository $billItemRepository;
    private CategoryRepository $categoryRepository;
    private ItemRepository $itemRepository;
    private BulkOperationService $bulkOperationService;

    public function __construct()
    {
        $this->billRepository = new BillRepository();
        $this->billItemRepository = new BillItemRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->itemRepository = new ItemRepository();
        $this->bulkOperationService = new BulkOperationService();
    }

    public function getSalesReport(string $reportType, ?string $categoryId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        // Calculate date range based on report type
        $dateRange = $this->getDateRange($reportType, $startDate, $endDate);
        $fromDate = $dateRange['from'];
        $toDate = $dateRange['to'];

        // Get bills within the date range
        $bills = $this->billRepository->findBetweenDates($fromDate, $toDate, $categoryId);

        // Calculate summary statistics
        $summary = $this->calculateSummary($bills);

        // Prepare chart data
        $chartData = $this->prepareChartData($bills, $reportType);

        // Get category breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($bills);

        // Get top selling items
        $topSellingItems = $this->getTopSellingItems($bills);

        return [
            'summary' => $summary,
            'chart_data' => $chartData,
            'category_breakdown' => $categoryBreakdown,
            'top_selling_items' => $topSellingItems
        ];
    }

    public function exportSalesReport(string $reportType, string $format, ?string $categoryId = null, ?string $startDate = null, ?string $endDate = null): string
    {
        // Get report data
        $reportData = $this->getSalesReport($reportType, $categoryId, $startDate, $endDate);

        // Prepare export data
        $exportData = $this->prepareExportData($reportData);

        // Determine correct file extension
        $extMap = [
            'json' => 'json',
            'csv' => 'csv',
            'excel' => 'xlsx',
            'xlsx' => 'xlsx',
            'xls' => 'xls'
        ];
        $ext = $extMap[strtolower($format)] ?? $format;
        $filePath = STORAGE_PATH . '/uploads/sales_report_' . time() . '.' . $ext;

        switch (strtolower($format)) {
            case 'json':
                file_put_contents($filePath, json_encode($exportData, JSON_PRETTY_PRINT));
                break;
            case 'csv':
                $this->exportToCsv($exportData, $filePath);
                break;
            case 'excel':
            case 'xlsx':
                $this->exportToExcel($exportData, $filePath);
                break;
            default:
                throw new \Exception("Unsupported format: {$format}");
        }

        return $filePath;
    }

    private function getDateRange(string $reportType, ?string $startDate = null, ?string $endDate = null): array
    {
        switch ($reportType) {
            case 'daily':
                $fromDate = date('Y-m-d 00:00:00');
                $toDate = date('Y-m-d 23:59:59');
                break;
            case 'weekly':
                $fromDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $toDate = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                break;
            case 'monthly':
                $fromDate = date('Y-m-01 00:00:00');
                $toDate = date('Y-m-t 23:59:59');
                break;
            case 'quarterly':
                $month = date('n');
                $quarter = ceil($month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                
                $fromDate = date('Y-' . sprintf('%02d', $startMonth) . '-01 00:00:00');
                $toDate = date('Y-' . sprintf('%02d', $endMonth) . '-' . date('t', mktime(0, 0, 0, $endMonth, 1)) . ' 23:59:59');
                break;
            case 'yearly':
                $fromDate = date('Y-01-01 00:00:00');
                $toDate = date('Y-12-31 23:59:59');
                break;
            case 'custom':
                $fromDate = $startDate . ' 00:00:00';
                $toDate = $endDate . ' 23:59:59';
                break;
            default:
                $fromDate = date('Y-m-d 00:00:00');
                $toDate = date('Y-m-d 23:59:59');
                break;
        }

        return [
            'from' => $fromDate,
            'to' => $toDate
        ];
    }

    private function calculateSummary(array $bills): array
    {
        $totalSales = 0;
        $totalItemsSold = 0;
        $totalTransactions = count($bills);

        foreach ($bills as $bill) {
            $totalSales += $bill->totalAmount;
            $billItems = $this->billItemRepository->findByBillId($bill->id);
            foreach ($billItems as $item) {
                $totalItemsSold += $item->quantity;
            }
        }

        $avgOrderValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        return [
            'total_sales' => $totalSales,
            'total_items_sold' => $totalItemsSold,
            'total_transactions' => $totalTransactions,
            'avg_order_value' => $avgOrderValue
        ];
    }

    private function prepareChartData(array $bills, string $reportType): array
    {
        $chartData = [
            'labels' => [],
            'values' => []
        ];

        if (empty($bills)) {
            return $chartData;
        }

        switch ($reportType) {
            case 'daily':
                // Group by hour
                $data = [];
                foreach ($bills as $bill) {
                    $hour = date('H', strtotime($bill->createdAt));
                    $hourLabel = $hour . ':00';
                    if (!isset($data[$hourLabel])) {
                        $data[$hourLabel] = 0;
                    }
                    $data[$hourLabel] += $bill->totalAmount;
                }
                ksort($data);
                $chartData['labels'] = array_keys($data);
                $chartData['values'] = array_values($data);
                break;
            case 'weekly':
                // Group by day of week
                $days = [
                    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 
                    'Friday', 'Saturday', 'Sunday'
                ];
                $data = array_fill_keys($days, 0);
                
                foreach ($bills as $bill) {
                    $day = date('l', strtotime($bill->createdAt));
                    $data[$day] += $bill->totalAmount;
                }
                
                $chartData['labels'] = $days;
                $chartData['values'] = array_values($data);
                break;
            case 'monthly':
                // Group by day of month
                $daysInMonth = date('t');
                $data = [];
                
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $day = sprintf('%02d', $i);
                    $data[$day] = 0;
                }
                
                foreach ($bills as $bill) {
                    $day = date('d', strtotime($bill->createdAt));
                    $data[$day] += $bill->totalAmount;
                }
                
                $chartData['labels'] = array_keys($data);
                $chartData['values'] = array_values($data);
                break;
            case 'quarterly':
            case 'yearly':
            case 'custom':
            default:
                // Group by date
                $data = [];
                foreach ($bills as $bill) {
                    $date = date('Y-m-d', strtotime($bill->createdAt));
                    if (!isset($data[$date])) {
                        $data[$date] = 0;
                    }
                    $data[$date] += $bill->totalAmount;
                }
                ksort($data);
                $chartData['labels'] = array_keys($data);
                $chartData['values'] = array_values($data);
                break;
        }

        return $chartData;
    }

    private function getCategoryBreakdown(array $bills): array
    {
        $categoryData = [
            'labels' => [],
            'values' => []
        ];

        if (empty($bills)) {
            return $categoryData;
        }

        $categorySales = [];

        foreach ($bills as $bill) {
            $billItems = $this->billItemRepository->findByBillId($bill->id);
            foreach ($billItems as $item) {
                $itemDetails = $this->itemRepository->findById($item->itemId);
                if ($itemDetails) {
                    $category = $this->categoryRepository->findById($itemDetails->categoryId);
                    if ($category) {
                        $categoryName = $category->name;
                        if (!isset($categorySales[$categoryName])) {
                            $categorySales[$categoryName] = 0;
                        }
                        $categorySales[$categoryName] += ($item->unitPrice * $item->quantity);
                    }
                }
            }
        }

        $categoryData['labels'] = array_keys($categorySales);
        $categoryData['values'] = array_values($categorySales);

        return $categoryData;
    }

    private function getTopSellingItems(array $bills): array
    {
        $itemQuantities = [];
        $itemRevenues = [];

        foreach ($bills as $bill) {
            $billItems = $this->billItemRepository->findByBillId($bill->id);
            foreach ($billItems as $item) {
                $itemDetails = $this->itemRepository->findById($item->itemId);
                if ($itemDetails) {
                    $itemName = $itemDetails->name;
                    $itemId = $itemDetails->id;
                    $categoryId = $itemDetails->categoryId;

                    if (!isset($itemQuantities[$itemId])) {
                        $itemQuantities[$itemId] = [
                            'name' => $itemName,
                            'category_id' => $categoryId,
                            'quantity' => 0,
                            'revenue' => 0
                        ];
                    }

                    $itemQuantities[$itemId]['quantity'] += $item->quantity;
                    $itemQuantities[$itemId]['revenue'] += ($item->unitPrice * $item->quantity);
                }
            }
        }

        // Sort by quantity sold
        uasort($itemQuantities, function($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });

        // Take top 10
        $topItems = array_slice($itemQuantities, 0, 10, true);

        $result = [];
        foreach ($topItems as $item) {
            $result[] = [
                'item_name' => $item['name'],
                'category_id' => $item['category_id'],
                'quantity_sold' => $item['quantity'],
                'revenue' => $item['revenue']
            ];
        }

        return $result;
    }

    private function prepareExportData(array $reportData): array
    {
        $exportData = [
            'summary' => $reportData['summary'],
            'chart_data' => $reportData['chart_data'],
            'category_breakdown' => $reportData['category_breakdown'],
            'top_selling_items' => $reportData['top_selling_items'],
            'generated_at' => date('Y-m-d H:i:s')
        ];

        return $exportData;
    }

    private function exportToCsv(array $data, string $filePath): void
    {
        $handle = fopen($filePath, 'w');

        // Write summary section
        fputcsv($handle, ['SALES REPORT SUMMARY']);
        fputcsv($handle, []);
        fputcsv($handle, ['Total Sales', 'Total Items Sold', 'Total Transactions', 'Average Order Value']);
        fputcsv($handle, [
            $data['summary']['total_sales'],
            $data['summary']['total_items_sold'],
            $data['summary']['total_transactions'],
            $data['summary']['avg_order_value']
        ]);
        fputcsv($handle, []);

        // Write chart data section
        fputcsv($handle, ['CHART DATA']);
        fputcsv($handle, []);
        fputcsv($handle, ['Period', 'Sales Amount']);
        foreach ($data['chart_data']['labels'] as $index => $label) {
            fputcsv($handle, [
                $label,
                isset($data['chart_data']['values'][$index]) ? $data['chart_data']['values'][$index] : 0
            ]);
        }
        fputcsv($handle, []);

        // Write category breakdown section
        fputcsv($handle, ['CATEGORY BREAKDOWN']);
        fputcsv($handle, []);
        fputcsv($handle, ['Category', 'Sales Amount']);
        foreach ($data['category_breakdown']['labels'] as $index => $label) {
            fputcsv($handle, [
                $label,
                isset($data['category_breakdown']['values'][$index]) ? $data['category_breakdown']['values'][$index] : 0
            ]);
        }
        fputcsv($handle, []);

        // Write top selling items section
        fputcsv($handle, ['TOP SELLING ITEMS']);
        fputcsv($handle, []);
        fputcsv($handle, ['Item Name', 'Category ID', 'Quantity Sold', 'Revenue']);
        foreach ($data['top_selling_items'] as $item) {
            fputcsv($handle, [
                $item['item_name'],
                $item['category_id'],
                $item['quantity_sold'],
                $item['revenue']
            ]);
        }

        fclose($handle);
    }

    private function exportToExcel(array $data, string $filePath): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write summary section
        $row = 1;
        $sheet->setCellValue('A' . $row, 'SALES REPORT SUMMARY');
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Total Sales');
        $sheet->setCellValue('B' . $row, $data['summary']['total_sales']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Items Sold');
        $sheet->setCellValue('B' . $row, $data['summary']['total_items_sold']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Transactions');
        $sheet->setCellValue('B' . $row, $data['summary']['total_transactions']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Average Order Value');
        $sheet->setCellValue('B' . $row, $data['summary']['avg_order_value']);
        $row += 2;

        // Write chart data section
        $sheet->setCellValue('A' . $row, 'CHART DATA');
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Period');
        $sheet->setCellValue('B' . $row, 'Sales Amount');
        $row++;

        foreach ($data['chart_data']['labels'] as $index => $label) {
            $value = isset($data['chart_data']['values'][$index]) ? $data['chart_data']['values'][$index] : 0;
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }
        $row += 1;

        // Write category breakdown section
        $sheet->setCellValue('A' . $row, 'CATEGORY BREAKDOWN');
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Category');
        $sheet->setCellValue('B' . $row, 'Sales Amount');
        $row++;

        foreach ($data['category_breakdown']['labels'] as $index => $label) {
            $value = isset($data['category_breakdown']['values'][$index]) ? $data['category_breakdown']['values'][$index] : 0;
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value);
            $row++;
        }
        $row += 1;

        // Write top selling items section
        $sheet->setCellValue('A' . $row, 'TOP SELLING ITEMS');
        $row += 2;

        $sheet->setCellValue('A' . $row, 'Item Name');
        $sheet->setCellValue('B' . $row, 'Category ID');
        $sheet->setCellValue('C' . $row, 'Quantity Sold');
        $sheet->setCellValue('D' . $row, 'Revenue');
        $row++;

        foreach ($data['top_selling_items'] as $item) {
            $sheet->setCellValue('A' . $row, $item['item_name']);
            $sheet->setCellValue('B' . $row, $item['category_id']);
            $sheet->setCellValue('C' . $row, $item['quantity_sold']);
            $sheet->setCellValue('D' . $row, $item['revenue']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}