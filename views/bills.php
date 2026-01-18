<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php
    // Base path support for subdirectory deployments (e.g. /pos-system)
    $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
    echo "<script>const BASE_PATH = " . json_encode($basePath) . ";</script>\n";
    function base_url($path = '') {
        global $basePath;
        return htmlspecialchars($basePath . $path);
    }
    ?>
    <script src="<?php echo base_url('/js/theme.js'); ?>"></script>
    <script src="<?php echo base_url('/js/icons.js'); ?>"></script>
    <script src="<?php echo base_url('/js/printer.js'); ?>"></script>
    <script>
        // Ensure icons object is available
        if (typeof icons === 'undefined') {
            window.icons = {
                editSmall: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                deleteSmall: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>',
                view: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>',
                print: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>',
                resetPassword: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>',
            };
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <nav class="bg-white dark:bg-gray-800 shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <button id="mobileMenuBtn" class="md:hidden mr-2 p-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">Lucky Book Shop POS</h1>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
                    <a href="<?php echo base_url('/billing'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">New Bill</a>
                    <span id="userName" class="text-gray-700 dark:text-gray-300"></span>
                    <button id="themeToggleBtn" class="p-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="Toggle Theme">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <button id="logoutBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-2 py-1">Dashboard</a>
                    <a href="<?php echo base_url('/billing'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-2 py-1">New Bill</a>
                    <div class="flex items-center justify-between px-2 py-1">
                        <span id="userNameMobile" class="text-gray-700 dark:text-gray-300"></span>
                        <button id="themeToggleBtnMobile" class="p-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="Toggle Theme">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </button>
                    </div>
                    <button id="logoutBtnMobile" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bills History</h2>
            <!-- Printer Selection -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <select id="mainPrinterSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 pr-10">
                        <option value="">Select Printer...</option>
                    </select>
                    <div id="mainPrinterStatusIndicator" class="absolute right-3 top-3 w-3 h-3 rounded-full bg-gray-400"></div>
                </div>
                <span id="mainDeviceStatusText" class="text-sm text-gray-600 dark:text-gray-400 hidden sm:inline">Ready</span>
            </div>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" id="searchInput" placeholder="Search by bill number or customer..." 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="returned">Returned</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div class="mt-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <button id="applyFiltersBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Apply Filters</span>
                </button>
                <button id="clearFiltersBtn" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Clear
                </button>
            </div>
        </div>
        
        <!-- Bills Table (Desktop) -->
        <div class="hidden md:block bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Bill #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Staff</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="billsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Bills will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Bills Cards (Mobile) -->
        <div id="billsCards" class="md:hidden space-y-4">
            <!-- Bill cards will be loaded here -->
        </div>
    </div>
    
    <!-- Bill Details Modal -->
    <div id="billModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Bill Details</h3>
                <div id="billDetails">
                    <!-- Bill details will be loaded here -->
                </div>
                <div class="mt-4 flex flex-col space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <div class="relative">
                            <select id="modalPrinterSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 pr-10">
                                <option value="">Select Printer...</option>
                            </select>
                            <div id="modalPrinterStatusIndicator" class="absolute right-3 top-3 w-3 h-3 rounded-full bg-gray-400"></div>
                        </div>
                        <span id="modalDeviceStatusText" class="text-sm text-gray-600 dark:text-gray-400">Printer not connected (PDF fallback)</span>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <button id="printBillBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Print Bill</span>
                        </button>
                        <button id="closeBillBtn" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            let currentBillId = null;
            
            const base = (typeof BASE_PATH !== 'undefined') ? BASE_PATH : '';
            
            if (!token) {
                window.location.href = base + '/login';
                return;
            }
            
            $('#userName, #userNameMobile').text(user.username || 'User');
            
            // Theme toggle
            $('#themeToggleBtn, #themeToggleBtnMobile').on('click', function() {
                ThemeManager.toggle();
            });
            
            // Mobile menu toggle
            $('#mobileMenuBtn').on('click', function() {
                $('#mobileMenu').toggleClass('hidden');
            });
            
            loadBills();
            loadMainPrinters();
            loadModalPrinters();
            
            $('#applyFiltersBtn').on('click', function() {
                loadBills();
            });
            
            $('#clearFiltersBtn').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#dateFrom').val('');
                $('#dateTo').val('');
                loadBills();
            });
            
            $('#closeBillBtn').on('click', function() {
                $('#billModal').addClass('hidden');
            });
            
            $('#printBillBtn').on('click', function() {
                if (currentBillId && printerHandler) {
                    printerHandler.printBill(currentBillId, null).catch(error => {
                        console.error('Print error:', error);
                        showMessage('Failed to print bill', 'error');
                    });
                }
            });
            
            function loadBills() {
                const filters = {};
                if ($('#searchInput').val()) filters.search = $('#searchInput').val();
                if ($('#statusFilter').val()) filters.status = $('#statusFilter').val();
                if ($('#dateFrom').val()) filters.date_from = $('#dateFrom').val();
                if ($('#dateTo').val()) filters.date_to = $('#dateTo').val();
                
                $.ajax({
                    url: base + '/api/billing',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: filters,
                    success: function(response) {
                        if (response.success) {
                            renderBills(response.data);
                        }
                    }
                });
            }
            
            function renderBills(bills) {
                const tbody = $('#billsTableBody');
                const cards = $('#billsCards');
                tbody.empty();
                cards.empty();
                
                if (bills.length === 0) {
                    tbody.append('<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No bills found</td></tr>');
                    cards.append('<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 text-center text-gray-500 dark:text-gray-400">No bills found</div>');
                    return;
                }
                
                bills.forEach(bill => {
                    const statusClass = {
                        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'returned': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    }[bill.status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                    
                    const date = new Date(bill.created_at).toLocaleString();
                    
                    // Desktop table row
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${bill.bill_number}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${date}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${bill.customer_name}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${bill.staff_name}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rs. ${parseFloat(bill.total_amount).toFixed(2)}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded ${statusClass}">
                                    ${bill.status.toUpperCase()}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button class="view-bill p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded" data-id="${bill.id}" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button class="print-bill-quick p-1.5 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded" data-id="${bill.id}" title="Print">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </button>
                                    ${bill.status === 'completed' && (user.role === 'supervisor' || user.role === 'admin') ? `
                                        <button class="return-bill p-1.5 text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded" data-id="${bill.id}" title="Return">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                    
                    // Mobile card
                    const card = `
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${bill.bill_number}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">${date}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs ${statusClass}">
                                    ${bill.status.toUpperCase()}
                                </span>
                            </div>
                            <div class="space-y-1 mb-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Customer:</span> ${bill.customer_name}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Staff:</span> ${bill.staff_name}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">Rs. ${parseFloat(bill.total_amount).toFixed(2)}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="view-bill flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm" data-id="${bill.id}">View</button>
                                <button class="print-bill-quick flex-1 bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 text-sm" data-id="${bill.id}">Print</button>
                                ${bill.status === 'completed' && (user.role === 'supervisor' || user.role === 'admin') ? `
                                    <button class="return-bill flex-1 bg-orange-600 text-white px-3 py-2 rounded hover:bg-orange-700 text-sm" data-id="${bill.id}">Return</button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    cards.append(card);
                });
                
                $('.view-bill').on('click', function() {
                    const id = $(this).data('id');
                    viewBill(id);
                });
                
                $('.print-bill-quick').on('click', function() {
                    const id = $(this).data('id');
                    if (printerHandler) {
                        printerHandler.printBill(id, null).catch(error => {
                            console.error('Print error:', error);
                            showMessage('Failed to print bill', 'error');
                        });
                    }
                });
                
                $('.return-bill').on('click', function() {
                    const id = $(this).data('id');
                    if (confirm('Return this bill? You will need to enter your password.')) {
                        returnBill(id);
                    }
                });
            }
            
            function viewBill(id) {
                currentBillId = id;
                $.ajax({
                    url: base + '/api/billing/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            renderBillDetails(response.data);
                            $('#billModal').removeClass('hidden');
                        }
                    }
                });
            }
            
            function renderBillDetails(data) {
                const { bill, items } = data;
                const date = new Date(bill.created_at).toLocaleString();
                
                let html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p><strong>Bill Number:</strong> ${bill.bill_number}</p>
                                <p><strong>Date:</strong> ${date}</p>
                                <p><strong>Customer:</strong> ${bill.customer_name}</p>
                            </div>
                            <div>
                                <p><strong>Staff:</strong> ${bill.staff_name}</p>
                                <p><strong>Status:</strong> ${bill.status.toUpperCase()}</p>
                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Item</th>
                                    <th class="px-4 py-2 text-left">Qty</th>
                                    <th class="px-4 py-2 text-left">Price</th>
                                    <th class="px-4 py-2 text-left">Discount</th>
                                    <th class="px-4 py-2 text-left">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
`;
                
                items.forEach(item => {
                    const subtotal = (item.unit_price * item.quantity) - item.discount;
                    html += `
                                <tr>
                                    <td class="px-4 py-2">${item.item_name}</td>
                                    <td class="px-4 py-2">${item.quantity}</td>
                                    <td class="px-4 py-2">Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td class="px-4 py-2">Rs. ${parseFloat(item.discount).toFixed(2)}</td>
                                    <td class="px-4 py-2">Rs. ${subtotal.toFixed(2)}</td>
                                </tr>
`;
                });
                
                html += `
                            </tbody>
                        </table>
                        <div class="border-t pt-4">
                            <div class="flex justify-between mb-2">
                                <span>Subtotal:</span>
                                <span>Rs. ${parseFloat(bill.subtotal).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Total Discount:</span>
                                <span>Rs. ${parseFloat(bill.total_discount).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg mb-2">
                                <span>Total Amount:</span>
                                <span>Rs. ${parseFloat(bill.total_amount).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Paid Amount:</span>
                                <span>Rs. ${parseFloat(bill.paid_amount).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Balance:</span>
                                <span>Rs. ${parseFloat(bill.balance).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
`;
                
                $('#billDetails').html(html);
            }
            
            function returnBill(id) {
                const reason = prompt('Enter return reason:');
                if (!reason) return;
                
                const password = prompt('Enter your password to authorize:');
                if (!password) return;
                
                $.ajax({
                    url: base + '/api/billing/' + id + '/return',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        reason: reason,
                        password: password
                    }),
                    success: function(response) {
                        if (response.success) {
                            showMessage('Bill returned successfully', 'success');
                            loadBills();
                        } else {
                            showMessage(response.message || 'Failed to return bill', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function showMessage(message, type) {
                const $msg = $('#message');
                $msg.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800 dark:bg-green-900 dark:text-green-200 dark:bg-red-900 dark:text-red-200');
                if (type === 'success') {
                    $msg.addClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200');
                } else {
                    $msg.addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200');
                }
                $msg.text(message);
                setTimeout(() => $msg.addClass('hidden'), 5000);
            }
            
            $('#logoutBtn, #logoutBtnMobile').on('click', function() {
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
                        $('#logoutBtn, #logoutBtnMobile').removeClass('disabled opacity-50 cursor-not-allowed');
                    }
                });
            });
            
            // Load printers for main view
            function loadMainPrinters() {
                $.ajax({
                    url: base + '/api/printers?type=printer',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#mainPrinterSelect');
                            select.empty();
                            select.append('<option value="">Select Printer...</option>');
                            
                            response.data.forEach(device => {
                                const selected = device.is_default ? 'selected' : '';
                                select.append('<option value="' + device.id + '" ' + selected + '>' + device.device_name + '</option>');
                            });
                        }
                    }
                });
            }
            
            // Load printers for modal
            loadModalPrinters();
            
            // Update modal device status indicators
            function updateModalDeviceStatusIndicators() {
                // Update printer status
                const printerStatus = isPrinterAvailable();
                const printerIndicator = $('#modalPrinterStatusIndicator');
                printerIndicator.removeClass('bg-green-500 bg-red-500 bg-gray-400');
                
                if (printerStatus) {
                    printerIndicator.addClass('bg-green-500');
                    printerIndicator.attr('title', 'Printer Connected');
                } else {
                    printerIndicator.addClass('bg-red-500');
                    printerIndicator.attr('title', 'Printer Not Connected (PDF fallback available)');
                }
                
                // Update status text
                const statusText = printerStatus ? 'Printer connected' : 'Printer not connected (PDF fallback)';
                $('#modalDeviceStatusText').text(statusText);
            }
            
            // Load available printers in modal
            function loadModalPrinters() {
                $.ajax({
                    url: base + '/api/printers?type=printer',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#modalPrinterSelect');
                            select.empty();
                            select.append('<option value="">Select Printer...</option>');
                            
                            response.data.forEach(device => {
                                const selected = device.is_default ? 'selected' : '';
                                select.append('<option value="' + device.id + '" ' + selected + '>' + device.device_name + '</option>');
                            });
                        }
                    }
                });
            }
            
            // Main printer selection
            $('#mainPrinterSelect').on('change', function() {
                const deviceId = $(this).val();
                if (deviceId && user.role === 'admin') {
                    $.ajax({
                        url: base + '/api/printers/set-default',
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify({
                            device_id: deviceId,
                            device_type: 'printer'
                        })
                    });
                }
            });
            
            // Update main view device status indicators
            function updateMainDeviceStatusIndicators() {
                // Update printer status
                const printerStatus = isPrinterAvailable();
                const printerIndicator = $('#mainPrinterStatusIndicator');
                printerIndicator.removeClass('bg-green-500 bg-red-500 bg-gray-400');
                
                if (printerStatus) {
                    printerIndicator.addClass('bg-green-500');
                    printerIndicator.attr('title', 'Printer Connected');
                } else {
                    printerIndicator.addClass('bg-red-500');
                    printerIndicator.attr('title', 'Printer Not Connected (PDF fallback available)');
                }
                
                // Update status text
                const statusText = printerStatus ? 'Printer connected' : 'Printer not connected (PDF fallback)';
                $('#mainDeviceStatusText').text(statusText);
            }
            
            // Printer selection in modal
            $('#modalPrinterSelect').on('change', function() {
                const deviceId = $(this).val();
                if (deviceId && user.role === 'admin') {
                    $.ajax({
                        url: base + '/api/printers/set-default',
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify({
                            device_id: deviceId,
                            device_type: 'printer'
                        })
                    });
                }
            });
            
            // Update status indicators periodically
            setInterval(updateModalDeviceStatusIndicators, 5000);
            setInterval(updateMainDeviceStatusIndicators, 5000);
            
            // Initial status update
            setTimeout(updateModalDeviceStatusIndicators, 1000);
            setTimeout(updateMainDeviceStatusIndicators, 1000);
        });
    </script>
</body>
</html>

