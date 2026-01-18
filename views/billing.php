<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing - Lucky Book Shop POS</title>
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
    <script src="<?php echo base_url('/js/scanner.js'); ?>"></script>
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
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">New Bill</h2>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <select id="scannerSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 pr-10">
                        <option value="">Select Scanner...</option>
                    </select>
                    <div id="scannerStatusIndicator" class="absolute right-3 top-3 w-3 h-3 rounded-full bg-gray-400"></div>
                </div>
                <div class="relative">
                    <select id="printerSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 pr-10">
                        <option value="">Select Printer...</option>
                    </select>
                    <div id="printerStatusIndicator" class="absolute right-3 top-3 w-3 h-3 rounded-full bg-gray-400"></div>
                </div>
                <span id="deviceStatusText" class="text-sm text-gray-600 dark:text-gray-400 hidden lg:inline">Ready</span>
            </div>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Items -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Items</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scan or Enter PLU/Barcode</label>
                        <input type="text" id="itemInput" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Scan barcode or enter PLU code">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Scanner will auto-detect. Press Enter to add item.</p>
                    </div>
                    
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subtotal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Cart items will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Bill Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bill Summary</h3>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>Subtotal:</span>
                            <span id="subtotal">Rs. 0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                            <span>Total Discount:</span>
                            <span id="totalDiscount">Rs. 0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg border-t dark:border-gray-700 pt-2 text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span id="totalAmount">Rs. 0.00</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                        <input type="text" id="customerName" value="Customer" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Email (Optional)</label>
                        <input type="email" id="customerEmail" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center text-gray-700 dark:text-gray-300">
                            <input type="checkbox" id="sendEmail" class="mr-2">
                            <span class="text-sm">Send soft copy via email</span>
                        </label>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paid Amount</label>
                        <input type="number" id="paidAmount" step="0.01" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Balance</label>
                        <input type="number" id="balance" readonly 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100">
                    </div>
                    
                    <button id="createBillBtn" 
                            class="w-full bg-green-600 text-white px-4 py-3 rounded hover:bg-green-700 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Create Bill</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden scanner input -->
    <input type="text" id="scanner-input" autocomplete="off" 
           style="position: absolute; left: -9999px; opacity: 0;">
    
    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
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
            
            let cart = [];
            let itemsCache = {};
            
            // Initialize scanner
            scannerHandler = new ScannerHandler({
                inputSelector: '#scanner-input',
                onScan: function(code) {
                    addItemByCode(code);
                },
                minLength: 3,
                maxLength: 100
            });
            
            // Load scanners and printers
            loadScanners();
            loadPrinters();
            
            // Manual input handler
            $('#itemInput').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const code = $(this).val().trim();
                    if (code) {
                        addItemByCode(code);
                        $(this).val('');
                    }
                }
            });
            
            // Calculate totals
            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                
                cart.forEach(item => {
                    const itemSubtotal = (item.unitPrice * item.quantity) - item.discount;
                    subtotal += itemSubtotal;
                    totalDiscount += item.discount;
                });
                
                $('#subtotal').text('Rs. ' + subtotal.toFixed(2));
                $('#totalDiscount').text('Rs. ' + totalDiscount.toFixed(2));
                $('#totalAmount').text('Rs. ' + subtotal.toFixed(2));
                
                const paidAmount = parseFloat($('#paidAmount').val()) || subtotal;
                $('#paidAmount').val(paidAmount.toFixed(2));
                $('#balance').val((paidAmount - subtotal).toFixed(2));
            }
            
            // Add item by code
            function addItemByCode(code) {
                // Try to find item by PLU or barcode
                $.ajax({
                    url: (BASE_PATH || '') + '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: {
                        search: code
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            const item = response.data[0];
                            addToCart(item);
                        } else {
                            // Try direct lookup
                            $.ajax({
                                url: (BASE_PATH || '') + '/api/items',
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                    data: {
                                        search: code,
                                        plu_code: code,
                                        barcode: code
                                    },
                                success: function(response2) {
                                    if (response2.success && response2.data.length > 0) {
                                        addToCart(response2.data[0]);
                                    } else {
                                        showMessage('Item not found: ' + code, 'error');
                                    }
                                }
                            });
                        }
                    },
                    error: function() {
                        showMessage('Error searching for item', 'error');
                    }
                });
            }
            
            // Add to cart
            function addToCart(item) {
                const existingIndex = cart.findIndex(c => c.itemId === item.id);
                
                if (existingIndex >= 0) {
                    cart[existingIndex].quantity += 1;
                } else {
                    cart.push({
                        itemId: item.id,
                        pluCode: item.plu_code,
                        name: item.name,
                        quantity: 1,
                        unitPrice: item.price,
                        actualPrice: item.price,
                        discount: item.has_discount ? (item.price * item.discount_percentage / 100) : 0,
                        discountPercentage: item.has_discount ? item.discount_percentage : 0,
                        stockQuantity: item.stock_quantity
                    });
                }
                
                renderCart();
                calculateTotals();
                $('#itemInput').val('').focus();
                scannerHandler.focus();
            }
            
            // Render cart
            function renderCart() {
                const tbody = $('#cartItems');
                tbody.empty();
                
                if (cart.length === 0) {
                    tbody.append('<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No items in cart</td></tr>');
                    return;
                }
                
                cart.forEach((item, index) => {
                    const subtotal = (item.unitPrice * item.quantity) - item.discount;
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${item.name}</td>
                            <td class="px-4 py-3">
                                <input type="number" value="${item.quantity}" min="1" max="${item.stockQuantity}"
                                       class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 quantity-input" data-index="${index}">
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rs. ${item.unitPrice.toFixed(2)}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rs. ${item.discount.toFixed(2)}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rs. ${subtotal.toFixed(2)}</td>
                            <td class="px-4 py-3">
                                <button class="remove-item p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" data-index="${index}" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                // Bind events
                $('.quantity-input').on('change', function() {
                    const index = $(this).data('index');
                    const newQty = parseInt($(this).val()) || 1;
                    if (newQty > cart[index].stockQuantity) {
                        $(this).val(cart[index].quantity);
                        showMessage('Insufficient stock', 'error');
                        return;
                    }
                    cart[index].quantity = newQty;
                    cart[index].discount = (cart[index].actualPrice * cart[index].discountPercentage / 100) * newQty;
                    renderCart();
                    calculateTotals();
                });
                
                $('.remove-item').on('click', function() {
                    const index = $(this).data('index');
                    cart.splice(index, 1);
                    renderCart();
                    calculateTotals();
                });
            }
            
            // Create bill
            $('#createBillBtn').on('click', function() {
                if (cart.length === 0) {
                    showMessage('Please add items to cart', 'error');
                    return;
                }
                
                const billData = {
                    items: cart.map(item => ({
                        item_id: item.itemId,
                        quantity: item.quantity,
                        override_price: item.unitPrice,
                        override_discount: item.discountPercentage
                    })),
                    customer_name: $('#customerName').val() || 'Customer',
                    customer_email: $('#customerEmail').val() || null,
                    send_email: $('#sendEmail').is(':checked'),
                    paid_amount: parseFloat($('#paidAmount').val()) || 0
                };
                
                $.ajax({
                    url: (BASE_PATH || '') + '/api/billing/create',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(billData),
                    success: function(response) {
                        if (response.success) {
                            showMessage('Bill created successfully!', 'success');
                            
                            // Ask if user wants to print
                            if (confirm('Bill created successfully! Do you want to print the bill?')) {
                                printBill(response.data.id);
                            }
                            
                            cart = [];
                            renderCart();
                            calculateTotals();
                            $('#customerName').val('Customer');
                            $('#customerEmail').val('');
                            $('#paidAmount').val('');
                        } else {
                            showMessage(response.message || 'Failed to create bill', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            });
            
            // Load scanners
            function loadScanners() {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/scanners?type=scanner',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#scannerSelect');
                            select.empty();
                            select.append('<option value="">Select Scanner...</option>');
                            
                            response.data.forEach(device => {
                                const selected = device.is_default ? 'selected' : '';
                                select.append(`<option value="${device.id}" ${selected}>${device.device_name}</option>`);
                            });
                        }
                    }
                });
            }
            
            // Load printers
            function loadPrinters() {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/printers?type=printer',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#printerSelect');
                            select.empty();
                            select.append('<option value="">Select Printer...</option>');
                            
                            response.data.forEach(device => {
                                const selected = device.is_default ? 'selected' : '';
                                select.append(`<option value="${device.id}" ${selected}>${device.device_name}</option>`);
                            });
                        }
                    }
                });
            }
            
            // Print bill
            function printBill(billId) {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/printers/bill/' + billId,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success && printerHandler) {
                            printerHandler.printBill(billId, response.data).catch(error => {
                                console.error('Print error:', error);
                                showMessage('Failed to print bill', 'error');
                            });
                        }
                    },
                    error: function() {
                        showMessage('Failed to load bill data for printing', 'error');
                    }
                });
            }
            
            // Scanner selection
            $('#scannerSelect').on('change', function() {
                const deviceId = $(this).val();
                if (deviceId && user.role === 'admin') {
                    $.ajax({
                        url: (BASE_PATH || '') + '/api/scanners/set-default',
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify({
                            device_id: deviceId,
                            device_type: 'scanner'
                        })
                    });
                }
            });
            
            // Printer selection
            $('#printerSelect').on('change', function() {
                const deviceId = $(this).val();
                if (deviceId && user.role === 'admin') {
                    $.ajax({
                        url: (BASE_PATH || '') + '/api/printers/set-default',
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
            
            // Paid amount change
            $('#paidAmount').on('input', function() {
                calculateTotals();
            });
            
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
                    url: (BASE_PATH || '') + '/api/auth/logout',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        // Clear any stored auth cookies as well
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (BASE_PATH || '/');
                        window.location.href = (BASE_PATH || '') + '/login';
                    },
                    error: function() {
                        // Even if the logout request fails, clear local storage and redirect
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (BASE_PATH || '/');
                        window.location.href = (BASE_PATH || '') + '/login';
                    },
                    complete: function() {
                        // Re-enable the button in case of error
                        $('#logoutBtn, #logoutBtnMobile').removeClass('disabled opacity-50 cursor-not-allowed');
                    }
                });
            });
            
            // Update device status indicators
            function updateDeviceStatusIndicators() {
                // Update scanner status
                const scannerStatus = isScannerAvailable();
                const scannerIndicator = $('#scannerStatusIndicator');
                scannerIndicator.removeClass('bg-green-500 bg-red-500 bg-gray-400');
                
                if (scannerStatus) {
                    scannerIndicator.addClass('bg-green-500');
                    scannerIndicator.attr('title', 'Scanner Connected');
                } else {
                    scannerIndicator.addClass('bg-red-500');
                    scannerIndicator.attr('title', 'Scanner Not Connected');
                }
                
                // Update printer status
                const printerStatus = isPrinterAvailable();
                const printerIndicator = $('#printerStatusIndicator');
                printerIndicator.removeClass('bg-green-500 bg-red-500 bg-gray-400');
                
                if (printerStatus) {
                    printerIndicator.addClass('bg-green-500');
                    printerIndicator.attr('title', 'Printer Connected');
                } else {
                    printerIndicator.addClass('bg-red-500');
                    printerIndicator.attr('title', 'Printer Not Connected (PDF fallback available)');
                }
                
                // Update status text
                const statusText = scannerStatus && printerStatus ? 'All devices connected' : 
                                  scannerStatus ? 'Scanner connected, printer not connected (PDF fallback)' : 
                                  printerStatus ? 'Printer connected, scanner not connected (manual entry)' : 
                                  'No devices connected (manual entry only)';
                $('#deviceStatusText').text(statusText);
            }
            
            // Initial focus
            scannerHandler.focus();
            
            // Update status indicators periodically
            setInterval(updateDeviceStatusIndicators, 5000);
            
            // Initial status update
            setTimeout(updateDeviceStatusIndicators, 1000);
        });
    </script>
</body>
</html>

