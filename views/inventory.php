<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Lucky Book Shop POS</title>
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
    <script src="<?php echo base_url('/js/scanner.js'); ?>"></script>
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Inventory Management</h2>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <select id="scannerSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 pr-10">
                        <option value="">Select Scanner...</option>
                    </select>
                    <div id="scannerStatusIndicator" class="absolute right-3 top-3 w-3 h-3 rounded-full bg-gray-400"></div>
                </div>
                <span id="deviceStatusText" class="text-sm text-gray-600 dark:text-gray-400 hidden sm:inline">Scanner Ready</span>
            </div>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stock In -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Stock In</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scan or Enter PLU/Barcode</label>
                        <input type="text" id="stockInInput" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                               placeholder="Scan barcode or enter PLU code">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                        <input type="number" id="stockInQty" min="1" value="1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                        <textarea id="stockInNotes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                    </div>
                    <button id="stockInBtn" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Add Stock</span>
                    </button>
                </div>
            </div>
            
            <!-- Stock Out -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Stock Out</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scan or Enter PLU/Barcode</label>
                        <input type="text" id="stockOutInput" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                               placeholder="Scan barcode or enter PLU code">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                        <input type="number" id="stockOutQty" min="1" value="1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                        <textarea id="stockOutNotes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                    </div>
                    <button id="stockOutBtn" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        <span>Remove Stock</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Movements -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Stock Movements</h3>
            <div id="movementsList" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody id="movementsBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Movements will be loaded here -->
                    </tbody>
                </table>
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
            
            if (user.role !== 'admin') {
                window.location.href = base + '/dashboard';
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
            
            // Initialize scanner
            scannerHandler = new ScannerHandler({
                inputSelector: '#scanner-input',
                onScan: function(code) {
                    // Auto-fill the active input
                    if ($('#stockInInput').is(':focus') || $('#stockInInput').val() === '') {
                        $('#stockInInput').val(code).focus();
                    } else if ($('#stockOutInput').is(':focus') || $('#stockOutInput').val() === '') {
                        $('#stockOutInput').val(code).focus();
                    }
                }
            });
            
            // Load scanners
            loadScanners();
            loadMovements();
            
            // Stock In
            $('#stockInInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const code = $(this).val().trim();
                    if (code) {
                        findItemAndFill(code, 'stockIn');
                    }
                }
            });
            
            $('#stockInBtn').on('click', function() {
                const code = $('#stockInInput').val().trim();
                const qty = parseInt($('#stockInQty').val()) || 1;
                const notes = $('#stockInNotes').val();
                
                if (!code) {
                    showMessage('Please enter or scan item code', 'error');
                    return;
                }
                
                findItemAndProcess(code, 'in', qty, notes);
            });
            
            // Stock Out
            $('#stockOutInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const code = $(this).val().trim();
                    if (code) {
                        findItemAndFill(code, 'stockOut');
                    }
                }
            });
            
            $('#stockOutBtn').on('click', function() {
                const code = $('#stockOutInput').val().trim();
                const qty = parseInt($('#stockOutQty').val()) || 1;
                const notes = $('#stockOutNotes').val();
                
                if (!code) {
                    showMessage('Please enter or scan item code', 'error');
                    return;
                }
                
                findItemAndProcess(code, 'out', qty, notes);
            });
            
            function findItemAndFill(code, type) {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: { search: code },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            const item = response.data[0];
                            if (type === 'stockIn') {
                                $('#stockInInput').val(item.plu_code);
                            } else {
                                $('#stockOutInput').val(item.plu_code);
                            }
                        }
                    }
                });
            }
            
            function findItemAndProcess(code, movementType, qty, notes) {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: { search: code },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            const item = response.data[0];
                            processStockMovement(item.id, movementType, qty, notes);
                        } else {
                            showMessage('Item not found', 'error');
                        }
                    }
                });
            }
            
            function processStockMovement(itemId, type, qty, notes) {
                const endpoint = type === 'in' ? (BASE_PATH || '') + '/api/inventory/stock-in' : (BASE_PATH || '') + '/api/inventory/stock-out';
                
                $.ajax({
                    url: endpoint,
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        item_id: itemId,
                        quantity: qty,
                        notes: notes
                    }),
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message || 'Stock updated successfully', 'success');
                            if (type === 'in') {
                                $('#stockInInput').val('');
                                $('#stockInQty').val(1);
                                $('#stockInNotes').val('');
                            } else {
                                $('#stockOutInput').val('');
                                $('#stockOutQty').val(1);
                                $('#stockOutNotes').val('');
                            }
                            loadMovements();
                            scannerHandler.focus();
                        } else {
                            showMessage(response.message || 'Failed to update stock', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function loadMovements() {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/inventory',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: { limit: 20 },
                    success: function(response) {
                        if (response.success) {
                            renderMovements(response.data);
                        }
                    }
                });
            }
            
            function renderMovements(movements) {
                const tbody = $('#movementsBody');
                tbody.empty();
                
                if (movements.length === 0) {
                    tbody.append('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No movements found</td></tr>');
                    return;
                }
                
                movements.forEach(movement => {
                    const typeClass = {
                        'in': 'text-green-600 dark:text-green-400',
                        'out': 'text-red-600 dark:text-red-400',
                        'return': 'text-blue-600 dark:text-blue-400',
                        'damage': 'text-orange-600 dark:text-orange-400',
                        'lost': 'text-gray-600 dark:text-gray-400'
                    }[movement.movement_type] || 'text-gray-600 dark:text-gray-400';
                    
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${new Date(movement.movement_date).toLocaleString()}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${movement.item_name || 'N/A'}</td>
                            <td class="px-4 py-3"><span class="${typeClass} font-medium">${movement.movement_type.toUpperCase()}</span></td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${movement.quantity}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${movement.notes || '-'}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
            
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
                
                // Update status text
                const statusText = scannerStatus ? 'Scanner connected' : 'Scanner not connected (manual entry)';
                $('#deviceStatusText').text(statusText);
            }
            
            scannerHandler.focus();
            
            // Update status indicators periodically
            setInterval(updateDeviceStatusIndicators, 5000);
            
            // Initial status update
            setTimeout(updateDeviceStatusIndicators, 1000);
        });
    </script>
</body>
</html>

