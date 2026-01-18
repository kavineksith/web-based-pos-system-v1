<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <?php
    $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
    echo "<script>const BASE_PATH = " . json_encode($basePath) . ";</script>\n";
    function base_url($path = '') {
        global $basePath;
        return htmlspecialchars($basePath . $path);
    }
    ?>
    <script src="<?php echo base_url('/js/icons.js'); ?>"></script>
    <script src="<?php echo base_url('/js/theme.js'); ?>"></script>
    <style>
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .bg-gray-50 { background-color: #374151 !important; }
        .dark .bg-gray-100 { background-color: #111827 !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500 { color: #d1d5db !important; }
        .dark .border-gray-300, .dark .border-gray-200 { border-color: #4b5563 !important; }
        .dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #4b5563 !important; }
        .dark input, .dark select, .dark textarea { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <nav class="bg-white dark:bg-gray-800 shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white hidden sm:block">Lucky Book Shop POS</h1>
                    </a>
                </div>
                
                <div class="flex items-center md:hidden">
                    <button id="mobileMenuBtn" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900">Dashboard</a>
                    <button id="themeToggleBtn" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="Toggle Theme">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <span id="userName" class="text-gray-700 dark:text-gray-300"></span>
                    <button id="logoutBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </div>
            </div>
            
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 py-2">Dashboard</a>
                    <span id="mobileUserName" class="text-gray-700 dark:text-gray-300 py-2"></span>
                    <button id="mobileThemeToggleBtn" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 py-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <span>Toggle Theme</span>
                    </button>
                    <button id="mobileLogoutBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</button>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sales Reports</h2>
            <div class="flex space-x-2">
                <button id="exportBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </button>
            </div>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Report Type</label>
                    <select id="reportType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="daily">Today</option>
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="quarterly">This Quarter</option>
                        <option value="yearly">This Year</option>
                        <option value="custom">Custom Date Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chart Type</label>
                    <select id="chartType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="bar">Bar Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="line">Line Chart</option>
                        <option value="doughnut">Doughnut Chart</option>
                        <option value="polarArea">Polar Area Chart</option>
                        <option value="radar">Radar Chart</option>
                    </select>
                </div>
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Export Format</label>
                    <select id="exportFormat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="json">JSON (.json)</option>
                    </select>
                </div> -->
            </div>
            
            <div id="customDateRange" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
                <button id="generateReportBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Generate Report
                </button>
                <button id="clearFiltersBtn" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Clear
                </button>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</div>
                <div id="totalSales" class="text-2xl font-bold text-gray-900 dark:text-white">Rs. 0.00</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Items Sold</div>
                <div id="totalItemsSold" class="text-2xl font-bold text-gray-900 dark:text-white">0</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transactions</div>
                <div id="totalTransactions" class="text-2xl font-bold text-gray-900 dark:text-white">0</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Order Value</div>
                <div id="avgOrderValue" class="text-2xl font-bold text-gray-900 dark:text-white">Rs. 0.00</div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sales Chart</h3>
            <div class="h-96">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <!-- Category Breakdown -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Category Breakdown</h3>
            <div class="h-96">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        
        <!-- Top Selling Items -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Selling Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity Sold</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody id="topSellingItems" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Top selling items will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Export Format Modal -->
    <div id="exportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Export Report</h3>
                <button id="closeExportModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose export format:</p>
            
            <div class="space-y-3">
                <button class="format-option w-full flex items-center p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all" data-format="excel">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="font-medium text-gray-900 dark:text-white">Excel (.xlsx)</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Microsoft Excel format</div>
                    </div>
                </button>
                
                <button class="format-option w-full flex items-center p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all" data-format="csv">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="font-medium text-gray-900 dark:text-white">CSV (.csv)</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Comma-separated values</div>
                    </div>
                </button>
                
                <button class="format-option w-full flex items-center p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all" data-format="json">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="font-medium text-gray-900 dark:text-white">JSON (.json)</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">JavaScript Object Notation</div>
                    </div>
                </button>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button id="cancelExportBtn" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            
            const base = (typeof BASE_PATH !== 'undefined') ? BASE_PATH : '';
            
            if (!token) {
                window.location.href = base + '/login';
                return;
            }
            
            $('#userName').text(user.username || 'User');
            
            // Store categories map for name lookup
            let categoriesMap = {};
            
            // Initialize charts
            let salesChart = null;
            let categoryChart = null;
            
            // Load categories
            loadCategories();
            
            // Event listeners
            $('#reportType').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#customDateRange').removeClass('hidden');
                } else {
                    $('#customDateRange').addClass('hidden');
                }
            });
            
            $('#generateReportBtn').on('click', function() {
                generateReport();
            });
            
            $('#clearFiltersBtn').on('click', function() {
                $('#reportType').val('daily');
                $('#categoryFilter').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                $('#customDateRange').addClass('hidden');
                generateReport();
            });
            
            $('#exportBtn').on('click', function() {
                $('#exportModal').removeClass('hidden');
            });
            
            $('#closeExportModal, #cancelExportBtn').on('click', function() {
                $('#exportModal').addClass('hidden');
            });
            
            $('.format-option').on('click', function() {
                const format = $(this).data('format');
                exportReport(format);
                $('#exportModal').addClass('hidden');
            });
            
            // Logout button handlers
            $('#logoutBtn').on('click', function() {
                // Prevent multiple clicks
                if ($(this).hasClass('disabled')) return;
                
                $(this).addClass('disabled opacity-50 cursor-not-allowed');
                
                $.ajax({
                    url: base + '/api/auth/logout',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        // Clear any stored auth cookies as well
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (base || '/');
                        window.location.href = base + '/login';
                    },
                    error: function() {
                        // Even if the logout request fails, clear local storage and redirect
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (base || '/');
                        window.location.href = base + '/login';
                    },
                    complete: function() {
                        // Re-enable the button in case of error
                        $('#logoutBtn').removeClass('disabled opacity-50 cursor-not-allowed');
                    }
                });
            });
            
            // Function to load categories
            function loadCategories() {
                $.ajax({
                    url: base + '/api/categories',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#categoryFilter');
                            // Clear existing options except first
                            select.find('option:not(:first)').remove();
                            // Build categories map and populate dropdown
                            categoriesMap = {};
                            response.data.forEach(cat => {
                                categoriesMap[cat.id] = cat.name;
                                select.append(`<option value="${cat.id}">${cat.name}</option>`);
                            });
                        }
                    }
                });
            }
            
            // Function to generate report
            function generateReport() {
                const reportType = $('#reportType').val();
                const categoryId = $('#categoryFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                const params = {
                    type: reportType,
                    category_id: categoryId
                };
                
                if (reportType === 'custom' && startDate && endDate) {
                    params.start_date = startDate;
                    params.end_date = endDate;
                }
                
                $.ajax({
                    url: base + '/api/reports/sales',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: params,
                    success: function(response) {
                        if (response.success) {
                            displayReportData(response.data);
                        } else {
                            showMessage(response.message || 'Failed to load report', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred while loading report', 'error');
                    }
                });
            }
            
            // Function to display report data
            function displayReportData(data) {
                // Update summary cards
                $('#totalSales').text('Rs. ' + (data.summary.total_sales || 0).toFixed(2));
                $('#totalItemsSold').text(data.summary.total_items_sold || 0);
                $('#totalTransactions').text(data.summary.total_transactions || 0);
                $('#avgOrderValue').text('Rs. ' + (data.summary.avg_order_value || 0).toFixed(2));
                
                // Render charts
                renderSalesChart(data.chart_data);
                renderCategoryChart(data.category_breakdown);
                
                // Render top selling items
                renderTopSellingItems(data.top_selling_items);
            }
            
            // Function to render sales chart
            function renderSalesChart(chartData) {
                const ctx = document.getElementById('salesChart').getContext('2d');
                
                if (salesChart) {
                    salesChart.destroy();
                }
                
                const chartType = $('#chartType').val();
                
                salesChart = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: chartData.labels || [],
                        datasets: [{
                            label: 'Sales Amount',
                            data: chartData.values || [],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(255, 205, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rs. ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Function to render category chart
            function renderCategoryChart(categoryData) {
                const ctx = document.getElementById('categoryChart').getContext('2d');
                
                if (categoryChart) {
                    categoryChart.destroy();
                }
                
                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryData.labels || [],
                        datasets: [{
                            data: categoryData.values || [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 205, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
            
            // Function to render top selling items
            function renderTopSellingItems(items) {
                const tbody = $('#topSellingItems');
                tbody.empty();
                
                if (!items || items.length === 0) {
                    tbody.append('<tr><td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No data available</td></tr>');
                    return;
                }
                
                items.forEach(item => {
                    const row = `
                        <tr>
                            <td class="px-4 py-3">${item.item_name || 'N/A'}</td>
                            <td class="px-4 py-3">${categoriesMap[item.category_id] || 'N/A'}</td>
                            <td class="px-4 py-3">${item.quantity_sold || 0}</td>
                            <td class="px-4 py-3">Rs. ${parseFloat(item.revenue || 0).toFixed(2)}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
            
            // Function to export report
            function exportReport(format) {
                const reportType = $('#reportType').val();
                const categoryId = $('#categoryFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                const params = new URLSearchParams({
                    type: reportType,
                    format: format
                });
                
                if (categoryId) params.append('category_id', categoryId);
                if (reportType === 'custom' && startDate && endDate) {
                    params.append('start_date', startDate);
                    params.append('end_date', endDate);
                }
                
                showMessage('Preparing export...', 'success');
                window.location.href = base + '/api/reports/sales/export?' + params.toString() + '&token=' + encodeURIComponent(token);
            }
            
            // Function to show message
            function showMessage(message, type) {
                const $msg = $('#message');
                $msg.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800');
                $msg.addClass(type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                $msg.text(message);
                $msg.show();
                setTimeout(() => $msg.addClass('hidden'), 5000);
            }
            
            // Generate initial report
            generateReport();
        });
    </script>
</body>
</html>