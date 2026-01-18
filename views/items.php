<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Management - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <h2 class="text-2xl font-bold text-gray-800">Items Management</h2>
            <div class="flex space-x-2">
                <button id="addItemBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Item
                </button>
                <button id="importBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Import
                </button>
                <button id="exportBtn" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Export
                </button>
            </div>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" id="searchInput" placeholder="Search items..." 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div>
                    <input type="number" id="minPrice" placeholder="Min Price" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <input type="number" id="maxPrice" placeholder="Max Price" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <button id="applyFiltersBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Apply Filters
                </button>
                <button id="clearFiltersBtn" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Clear
                </button>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PLU Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Items will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <button id="bulkDeleteBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Delete Selected
            </button>
        </div>
    </div>
    
    <!-- Add/Edit Item Modal -->
    <div id="itemModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="modalTitle">Add Item</h3>
                <form id="itemForm" class="space-y-4">
                    <input type="hidden" id="itemId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PLU Code *</label>
                        <input type="text" id="pluCode" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" id="itemName" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                        <select id="itemCategory" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode</label>
                        <input type="text" id="barcode"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price *</label>
                        <input type="number" id="price" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Quantity</label>
                        <input type="number" id="stockQuantity" value="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Save
                        </button>
                        <button type="button" id="cancelBtn" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Import/Export Format Modal -->
    <div id="formatModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="formatModalTitle">Select Format</h3>
                <button id="closeFormatModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" id="formatModalDesc">Choose a file format:</p>
            
            <div class="space-y-3" id="formatOptions">
                <!-- Excel Option -->
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
                
                <!-- CSV Option -->
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
                
                <!-- JSON Option -->
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
                <button id="cancelFormatBtn" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
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
            
            if (user.role !== 'admin') {
                window.location.href = base + '/dashboard';
                return;
            }
            
            $('#userName').text(user.username || 'User');
            
            // Store categories map for name lookup
            let categoriesMap = {};
            let currentItemsCount = 0; // Track number of items in table
            
            loadCategories().then(() => {
                loadItems();
            });
            
            $('#addItemBtn').on('click', function() {
                $('#modalTitle').text('Add Item');
                $('#itemForm')[0].reset();
                $('#itemId').val('');
                $('#itemModal').removeClass('hidden');
            });
            
            $('#cancelBtn').on('click', function() {
                $('#itemModal').addClass('hidden');
            });
            
            $('#itemForm').on('submit', function(e) {
                e.preventDefault();
                saveItem();
            });
            
            $('#applyFiltersBtn').on('click', function() {
                loadItems();
            });
            
            $('#clearFiltersBtn').on('click', function() {
                $('#searchInput').val('');
                $('#categoryFilter').val('');
                $('#minPrice').val('');
                $('#maxPrice').val('');
                loadItems();
            });
            
            // Select All checkbox functionality
            $('#selectAll').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.item-checkbox').prop('checked', isChecked);
            });
            
            // Update Select All when individual checkboxes change
            $(document).on('change', '.item-checkbox', function() {
                const totalCheckboxes = $('.item-checkbox').length;
                const checkedCheckboxes = $('.item-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            });
            
            // Format Modal handling
            let currentOperation = null; // 'import' or 'export'
            
            function showFormatModal(operation) {
                currentOperation = operation;
                
                // Check if there are items to export
                if (operation === 'export' && currentItemsCount === 0) {
                    showErrorAlert('No Items', 'There are no items to export. Please add some items first.');
                    return;
                }
                
                if (operation === 'import') {
                    $('#formatModalTitle').text('Import Items');
                    $('#formatModalDesc').text('Select the format of your import file:');
                } else {
                    $('#formatModalTitle').text('Export Items');
                    $('#formatModalDesc').text('Choose your export format:');
                }
                $('#formatModal').removeClass('hidden');
            }
            
            function hideFormatModal() {
                $('#formatModal').addClass('hidden');
                currentOperation = null;
            }
            
            // Close format modal
            $('#closeFormatModal, #cancelFormatBtn').on('click', hideFormatModal);
            
            // Format selection
            $('.format-option').on('click', function() {
                const format = $(this).data('format');
                const operation = currentOperation; // Save operation before hideFormatModal resets it
                hideFormatModal();
                
                if (operation === 'import') {
                    handleImport(format);
                } else if (operation === 'export') {
                    handleExport(format);
                }
            });
            
            // Import functionality
            $('#importBtn').on('click', function() {
                showFormatModal('import');
            });
            
            function handleImport(selectedFormat) {
                const acceptMap = {
                    'excel': '.xlsx,.xls',
                    'csv': '.csv',
                    'json': '.json'
                };
                const input = $('<input type="file" accept="' + (acceptMap[selectedFormat] || '.xlsx,.xls,.csv,.json') + '">');
                input.on('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    showMessage('Importing items...', 'success');
                    
                    $.ajax({
                        url: base + '/api/items/import?format=' + selectedFormat,
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                showMessage(`Imported ${response.data.success} items successfully. Failed: ${response.data.failed}`, 'success');
                                loadItems();
                            } else {
                                showMessage(response.message || 'Import failed', 'error');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON || {};
                            showMessage(response.message || 'Import failed', 'error');
                        }
                    });
                });
                input.click();
            }
            
            // Export functionality
            $('#exportBtn').on('click', function() {
                // Check if any items are selected
                const selectedIds = $('.item-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();
                
                if (selectedIds.length === 0) {
                    showErrorAlert('No Selection', 'Please select at least one item to export.');
                    return;
                }
                
                showFormatModal('export');
            });
            
            function handleExport(selectedFormat) {
                // Get selected item IDs
                const selectedIds = $('.item-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();
                
                // Build URL with selected IDs
                const params = new URLSearchParams();
                params.append('format', selectedFormat);
                selectedIds.forEach(id => params.append('ids[]', id));
                
                const url = base + '/api/items/export?' + params.toString();
                
                showMessage('Preparing export...', 'success');
                window.location.href = url + '&token=' + encodeURIComponent(token);
            }
            
            $('#bulkDeleteBtn').on('click', function() {
                const selected = $('.item-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();
                
                if (selected.length === 0) {
                    showErrorAlert('No Selection', 'Please select at least one item to delete.');
                    return;
                }
                
                if (!confirm(`Delete ${selected.length} item(s)?`)) {
                    return;
                }
                
                $.ajax({
                    url: base + '/api/items/bulk-delete',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ ids: selected }),
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            $('#selectAll').prop('checked', false);
                            loadItems();
                        } else {
                            showMessage(response.message || 'Delete failed', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred while deleting', 'error');
                    }
                });
            });
            
            function loadCategories() {
                return $.ajax({
                    url: base + '/api/categories',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#itemCategory');
                            const filter = $('#categoryFilter');
                            // Clear existing options except first
                            select.find('option:not(:first)').remove();
                            filter.find('option:not(:first)').remove();
                            // Build categories map and populate dropdowns
                            categoriesMap = {};
                            response.data.forEach(cat => {
                                categoriesMap[cat.id] = cat.name;
                                select.append(`<option value="${cat.id}">${cat.name}</option>`);
                                filter.append(`<option value="${cat.id}">${cat.name}</option>`);
                            });
                        }
                    }
                });
            }
            
            function loadItems() {
                const filters = {};
                if ($('#searchInput').val()) filters.search = $('#searchInput').val();
                if ($('#categoryFilter').val()) filters.category_id = $('#categoryFilter').val();
                if ($('#minPrice').val()) filters.min_price = $('#minPrice').val();
                if ($('#maxPrice').val()) filters.max_price = $('#maxPrice').val();
                
                $.ajax({
                    url: base + '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: filters,
                    success: function(response) {
                        if (response.success) {
                            renderItems(response.data);
                        }
                    }
                });
            }
            
            function renderItems(items) {
                const tbody = $('#itemsTableBody');
                tbody.empty();
                
                // Reset select all checkbox when items are reloaded
                $('#selectAll').prop('checked', false);
                
                // Update items count
                currentItemsCount = items.length;
                
                if (items.length === 0) {
                    tbody.append('<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No items found</td></tr>');
                    return;
                }
                
                items.forEach(item => {
                    const row = `
                        <tr>
                            <td class="px-4 py-3">
                                <input type="checkbox" class="item-checkbox" data-id="${item.id}">
                            </td>
                            <td class="px-4 py-3">${item.plu_code}</td>
                            <td class="px-4 py-3">${item.name}</td>
                            <td class="px-4 py-3">${categoriesMap[item.category_id] || 'Unknown'}</td>
                            <td class="px-4 py-3">Rs. ${parseFloat(item.price).toFixed(2)}</td>
                            <td class="px-4 py-3 ${item.stock_quantity <= item.low_stock_threshold ? 'text-red-600' : ''}">
                                ${item.stock_quantity}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded ${item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${item.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-1">
                                    <button class="edit-item p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900 rounded-lg transition-colors" data-id="${item.id}" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="delete-item p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition-colors" data-id="${item.id}" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                $('.edit-item').on('click', function() {
                    const id = $(this).data('id');
                    editItem(id);
                });
                
                $('.delete-item').on('click', function() {
                    const id = $(this).data('id');
                    if (confirm('Delete this item?')) {
                        deleteItem(id);
                    }
                });
            }
            
            function editItem(id) {
                $.ajax({
                    url: base + '/api/items/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const item = response.data;
                            $('#itemId').val(item.id);
                            $('#pluCode').val(item.plu_code);
                            $('#itemName').val(item.name);
                            $('#itemCategory').val(item.category_id);
                            $('#barcode').val(item.barcode || '');
                            $('#price').val(item.price);
                            $('#stockQuantity').val(item.stock_quantity);
                            $('#modalTitle').text('Edit Item');
                            $('#itemModal').removeClass('hidden');
                        }
                    }
                });
            }
            
            function saveItem() {
                const data = {
                    plu_code: $('#pluCode').val(),
                    name: $('#itemName').val(),
                    category_id: $('#itemCategory').val(),
                    barcode: $('#barcode').val(),
                    price: $('#price').val(),
                    stock_quantity: $('#stockQuantity').val()
                };
                
                const id = $('#itemId').val();
                const url = id ? base + '/api/items/' + id : base + '/api/items';
                const method = id ? 'PUT' : 'POST';
                
                $.ajax({
                    url: url,
                    method: method,
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(data),
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            $('#itemModal').addClass('hidden');
                            loadItems();
                        } else {
                            showMessage(response.message || 'Failed to save item', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function deleteItem(id) {
                $.ajax({
                    url: base + '/api/items/' + id,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Item deleted successfully', 'success');
                            loadItems();
                        }
                    }
                });
            }
            
            function showMessage(message, type) {
                const $msg = $('#message');
                $msg.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800');
                $msg.addClass(type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                $msg.text(message);
                $msg.show();
                setTimeout(() => $msg.addClass('hidden'), 5000);
            }
            
            // Error Alert Modal
            function showErrorAlert(title, message) {
                // Create modal if it doesn't exist
                if ($('#errorAlertModal').length === 0) {
                    const modal = `
                        <div id="errorAlertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
                            <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="errorAlertTitle"></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" id="errorAlertMessage"></p>
                                    </div>
                                </div>
                                <button id="closeErrorAlert" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    OK
                                </button>
                            </div>
                        </div>
                    `;
                    $('body').append(modal);
                    
                    $(document).on('click', '#closeErrorAlert', function() {
                        $('#errorAlertModal').remove();
                    });
                }
                
                $('#errorAlertTitle').text(title);
                $('#errorAlertMessage').text(message);
                $('#errorAlertModal').show();
            }
            
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
        });
    </script>
</body>
</html>

