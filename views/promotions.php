<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions - Lucky Book Shop POS</title>
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Promotions Management</h2>
            <button id="addPromotionBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Add Promotion</span>
            </button>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Promotions Table (Desktop) -->
        <div class="hidden md:block bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Target</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="promotionsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Promotions will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Promotions Cards (Mobile) -->
        <div id="promotionsCards" class="md:hidden space-y-4">
            <!-- Promotion cards will be loaded here -->
        </div>
    </div>
    
    <!-- Add/Edit Promotion Modal -->
    <div id="promotionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="modalTitle">Add Promotion</h3>
                <form id="promotionForm" class="space-y-4">
                    <input type="hidden" id="promotionId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" id="promotionName" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Type *</label>
                        <select id="targetType" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Select Target</option>
                            <option value="item">Specific Item</option>
                            <option value="category">Category</option>
                        </select>
                    </div>
                    <div id="itemSelectDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item</label>
                        <select id="promotionItem" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Select Item</option>
                        </select>
                    </div>
                    <div id="categorySelectDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                        <select id="promotionCategory" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Type *</label>
                        <select id="discountType" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Value *</label>
                        <input type="number" id="discountValue" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date *</label>
                            <input type="date" id="startDate"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date *</label>
                            <input type="date" id="endDate"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
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
    
    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const base = (typeof BASE_PATH !== 'undefined') ? BASE_PATH : '';
            
            if (!token) {
                window.location.href = base + '/login';
                return;
            }
            
            if (user.role !== 'admin' && user.role !== 'supervisor') {
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
            
            loadCategories();
            loadItems();
            loadPromotions();
            
            $('#addPromotionBtn').on('click', function() {
                $('#modalTitle').text('Add Promotion');
                $('#promotionForm')[0].reset();
                $('#promotionId').val('');
                $('#promotionModal').removeClass('hidden');
            });
            
            $('#cancelBtn').on('click', function() {
                $('#promotionModal').addClass('hidden');
            });
            
            $('#targetType').on('change', function() {
                const type = $(this).val();
                $('#itemSelectDiv').addClass('hidden');
                $('#categorySelectDiv').addClass('hidden');
                if (type === 'item') {
                    $('#itemSelectDiv').removeClass('hidden');
                } else if (type === 'category') {
                    $('#categorySelectDiv').removeClass('hidden');
                }
            });
            
            $('#promotionForm').on('submit', function(e) {
                e.preventDefault();
                savePromotion();
            });
            
            function loadCategories() {
                $.ajax({
                    url: base + '/api/categories',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#promotionCategory');
                            response.data.forEach(cat => {
                                select.append(`<option value="${cat.id}">${cat.name}</option>`);
                            });
                        }
                    }
                });
            }
            
            function loadItems() {
                $.ajax({
                    url: base + '/api/items',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const select = $('#promotionItem');
                            response.data.forEach(item => {
                                select.append(`<option value="${item.id}">${item.name}</option>`);
                            });
                        }
                    }
                });
            }
            
            function loadPromotions() {
                $.ajax({
                    url: base + '/api/promotions',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            renderPromotions(response.data);
                        }
                    }
                });
            }
            
            function renderPromotions(promotions) {
                const tbody = $('#promotionsTableBody');
                const cards = $('#promotionsCards');
                tbody.empty();
                cards.empty();
                
                if (promotions.length === 0) {
                    tbody.append('<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No promotions found</td></tr>');
                    cards.append('<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 text-center text-gray-500 dark:text-gray-400">No promotions found</div>');
                    return;
                }
                
                promotions.forEach(promo => {
                    const target = promo.item_id ? 'Item' : (promo.category_id ? 'Category' : 'All');
                    const discount = promo.discount_type === 'percentage' ? 
                        promo.discount_value + '%' : 
                        'Rs. ' + parseFloat(promo.discount_value).toFixed(2);
                    const startDate = new Date(promo.start_date).toLocaleDateString();
                    const endDate = new Date(promo.end_date).toLocaleDateString();
                    const isActive = new Date() >= new Date(promo.start_date) && new Date() <= new Date(promo.end_date) && promo.is_active;
                    
                    // Desktop table row
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${promo.name}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${target}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${discount}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${startDate} - ${endDate}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded ${isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'}">
                                    ${isActive ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button class="edit-promotion p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded" data-id="${promo.id}" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="delete-promotion p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" data-id="${promo.id}" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
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
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${promo.name}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Target: ${target}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs ${isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'}">
                                    ${isActive ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                            <div class="space-y-1 mb-3">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">${discount}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${startDate} - ${endDate}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="edit-promotion flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm" data-id="${promo.id}">Edit</button>
                                <button class="delete-promotion flex-1 bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-sm" data-id="${promo.id}">Delete</button>
                            </div>
                        </div>
                    `;
                    cards.append(card);
                });
                
                $('.edit-promotion').on('click', function() {
                    const id = $(this).data('id');
                    editPromotion(id);
                });
                
                $('.delete-promotion').on('click', function() {
                    const id = $(this).data('id');
                    if (confirm('Delete this promotion?')) {
                        deletePromotion(id);
                    }
                });
            }
            
            function editPromotion(id) {
                $.ajax({
                    url: base + '/api/promotions/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const promo = response.data;
                            $('#promotionId').val(promo.id);
                            $('#promotionName').val(promo.name);
                            if (promo.item_id) {
                                $('#targetType').val('item');
                                $('#promotionItem').val(promo.item_id);
                                $('#itemSelectDiv').removeClass('hidden');
                            } else if (promo.category_id) {
                                $('#targetType').val('category');
                                $('#promotionCategory').val(promo.category_id);
                                $('#categorySelectDiv').removeClass('hidden');
                            }
                            $('#discountType').val(promo.discount_type);
                            $('#discountValue').val(promo.discount_value);
                            $('#startDate').val(promo.start_date.substring(0, 10));
                            $('#endDate').val(promo.end_date.substring(0, 10));
                            $('#modalTitle').text('Edit Promotion');
                            $('#promotionModal').removeClass('hidden');
                        }
                    }
                });
            }
            
            function savePromotion() {
                // Validate required fields
                if (!$('#promotionName').val()) {
                    showMessage('Promotion name is required', 'error');
                    return;
                }
                
                if (!$('#startDate').val() || !$('#endDate').val()) {
                    showMessage('Start and end dates are required', 'error');
                    return;
                }
                
                const startDate = new Date($('#startDate').val());
                const endDate = new Date($('#endDate').val());
                
                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    showMessage('Invalid date format', 'error');
                    return;
                }
                
                if (startDate >= endDate) {
                    showMessage('End date must be after start date', 'error');
                    return;
                }
                
                // Get date values and append default times
                let startDateVal = $('#startDate').val();
                let endDateVal = $('#endDate').val();
                
                // Append default times for start and end dates
                if (startDateVal) {
                    startDateVal += ' 00:00:00';
                }
                if (endDateVal) {
                    endDateVal += ' 23:59:59';
                }
                
                const data = {
                    name: $('#promotionName').val(),
                    discount_type: $('#discountType').val(),
                    discount_value: $('#discountValue').val(),
                    start_date: startDateVal,
                    end_date: endDateVal
                };
                
                const targetType = $('#targetType').val();
                if (targetType === 'item') {
                    data.item_id = $('#promotionItem').val();
                } else if (targetType === 'category') {
                    data.category_id = $('#promotionCategory').val();
                }
                
                const id = $('#promotionId').val();
                const url = id ? (BASE_PATH || '') + '/api/promotions/' + id : (BASE_PATH || '') + '/api/promotions';
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
                            showMessage(response.message || 'Promotion saved successfully', 'success');
                            $('#promotionModal').addClass('hidden');
                            loadPromotions();
                        } else {
                            showMessage(response.message || 'Failed to save promotion', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function deletePromotion(id) {
                $.ajax({
                    url: base + '/api/promotions/' + id,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Promotion deleted successfully', 'success');
                            loadPromotions();
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
        });
    </script>
</body>
</html>

