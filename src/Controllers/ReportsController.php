<?php
/**
 * Reports Controller
 */

namespace App\Controllers;

use App\Services\ReportService;
use App\Exceptions\ValidationException;

class ReportsController extends BaseController
{
    private ReportService $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    public function showReports(): void
    {
        // Check authentication
        $token = \App\Config\JwtAuth::getTokenFromRequest();
        if (!$token || !\App\Config\JwtAuth::validateToken($token)) {
            $this->redirect('/login');
        }

        $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
        require_once VIEWS_PATH . '/reports.php';
        exit;
    }

    public function getSalesReport(): void
    {
        try {
            $filters = $_GET ?? [];
            
            // Validate required parameters
            $reportType = $filters['type'] ?? 'daily';
            $allowedTypes = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'];
            if (!in_array($reportType, $allowedTypes)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid report type'
                ], 400);
                return;
            }

            $categoryId = $filters['category_id'] ?? null;
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;

            // For custom date range, validate dates
            if ($reportType === 'custom') {
                if (empty($startDate) || empty($endDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Start date and end date are required for custom date range'
                    ], 400);
                    return;
                }
                
                // Validate date format
                if (!strtotime($startDate) || !strtotime($endDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Invalid date format'
                    ], 400);
                    return;
                }
                
                // Ensure end date is not before start date
                if (strtotime($endDate) < strtotime($startDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'End date cannot be before start date'
                    ], 400);
                    return;
                }
            }

            $data = $this->reportService->getSalesReport($reportType, $categoryId, $startDate, $endDate);

            $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred while generating report'
            ], 500);
        }
    }

    public function exportSalesReport(): void
    {
        try {
            $filters = $_GET ?? [];
            
            // Validate required parameters
            $reportType = $filters['type'] ?? 'daily';
            $allowedTypes = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'];
            if (!in_array($reportType, $allowedTypes)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid report type'
                ], 400);
                return;
            }

            $format = $filters['format'] ?? 'excel';
            $allowedFormats = ['excel', 'csv', 'json'];
            if (!in_array($format, $allowedFormats)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid export format'
                ], 400);
                return;
            }

            $categoryId = $filters['category_id'] ?? null;
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;

            // For custom date range, validate dates
            if ($reportType === 'custom') {
                if (empty($startDate) || empty($endDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Start date and end date are required for custom date range'
                    ], 400);
                    return;
                }
                
                // Validate date format
                if (!strtotime($startDate) || !strtotime($endDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Invalid date format'
                    ], 400);
                    return;
                }
                
                // Ensure end date is not before start date
                if (strtotime($endDate) < strtotime($startDate)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'End date cannot be before start date'
                    ], 400);
                    return;
                }
            }

            $filePath = $this->reportService->exportSalesReport($reportType, $format, $categoryId, $startDate, $endDate);

            // Determine file extension and content type
            $extensions = [
                'json' => 'json',
                'csv' => 'csv',
                'excel' => 'xlsx',
                'xlsx' => 'xlsx',
                'xls' => 'xls'
            ];
            $ext = $extensions[strtolower($format)] ?? 'json';
            $fileName = 'sales_report_' . date('Y-m-d_H-i-s') . '.' . $ext;

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            unlink($filePath);
            exit;
        } catch (ValidationException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ], 400);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'An error occurred while generating export'
            ], 500);
        }
    }
}