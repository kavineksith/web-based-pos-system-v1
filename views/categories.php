<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
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
                
                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button id="mobileMenuBtn" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Desktop nav -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
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
            
            <!-- Mobile menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="<?php echo base_url('/dashboard'); ?>" class="text-gray-700 dark:text-gray-300 py-2">Dashboard</a>
                    <div class="flex items-center justify-between py-2">
                        <span id="mobileUserName" class="text-gray-700 dark:text-gray-300"></span>
                    </div>
                    <button id="mobileThemeToggleBtn" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 py-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <span>Toggle Theme</span>
                    </button>
                    <button id="mobileLogoutBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Categories Management</h2>
            <button id="addCategoryBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center space-x-2 w-full sm:w-auto justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add Category</span>
            </button>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" id="searchInput" placeholder="Search categories..." 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                    <select id="sortFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="items_desc">Most Items</option>
                        <option value="items_asc">Least Items</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <div class="flex space-x-2 w-full">
                        <button id="applyFiltersBtn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span class="hidden sm:inline">Filter</span>
                        </button>
                        <button id="clearFiltersBtn" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="hidden sm:inline">Clear</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Categories Table - Desktop -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hidden md:block">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Items Count</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Categories will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Categories Cards - Mobile -->
        <div id="categoriesCards" class="md:hidden space-y-4">
            <!-- Mobile cards will be loaded here -->
        </div>
    </div>
    
    <!-- Add/Edit Category Modal -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 sm:top-20 mx-4 sm:mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="modalTitle">Add Category</h3>
                <form id="categoryForm" class="space-y-4">
                    <input type="hidden" id="categoryId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" id="categoryName" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="categoryDescription" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="categoryStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Save</span>
                        </button>
                        <button type="button" id="cancelBtn" class="flex-1 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Cancel</span>
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
            
            if (user.role !== 'admin') {
                window.location.href = base + '/dashboard';
                return;
            }
            
            $('#userName').text(user.username || 'User');
            $('#mobileUserName').text(user.username || 'User');
            
            let allCategories = [];
            let itemCounts = {};
            
            loadCategories();
            
            // Theme toggle
            $('#themeToggleBtn, #mobileThemeToggleBtn').on('click', function() {
                ThemeManager.toggle();
            });
            
            // Mobile menu toggle
            $('#mobileMenuBtn').on('click', function() {
                $('#mobileMenu').toggleClass('hidden');
            });
            
            $('#addCategoryBtn').on('click', function() {
                $('#modalTitle').text('Add Category');
                $('#categoryForm')[0].reset();
                $('#categoryId').val('');
                $('#categoryStatus').val('1');
                $('#categoryModal').removeClass('hidden');
            });
            
            $('#cancelBtn').on('click', function() {
                $('#categoryModal').addClass('hidden');
            });
            
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                saveCategory();
            });
            
            $('#applyFiltersBtn').on('click', function() {
                applyFilters();
            });
            
            $('#clearFiltersBtn').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#sortFilter').val('name_asc');
                applyFilters();
            });
            
            // Real-time search
            $('#searchInput').on('keyup', function() {
                applyFilters();
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
                            // Also load items to count per category
                            $.ajax({
                                url: base + '/api/items',
                                method: 'GET',
                                headers: {
                                    'Authorization': 'Bearer ' + token
                                },
                                success: function(itemsResponse) {
                                    itemCounts = {};
                                    if (itemsResponse.success) {
                                        itemsResponse.data.forEach(item => {
                                            itemCounts[item.category_id] = (itemCounts[item.category_id] || 0) + 1;
                                        });
                                    }
                                    allCategories = response.data;
                                    applyFilters();
                                },
                                error: function() {
                                    allCategories = response.data;
                                    applyFilters();
                                }
                            });
                        }
                    }
                });
            }
            
            function applyFilters() {
                let filtered = [...allCategories];
                
                // Search filter
                const search = $('#searchInput').val().toLowerCase();
                if (search) {
                    filtered = filtered.filter(cat => 
                        cat.name.toLowerCase().includes(search) || 
                        (cat.description && cat.description.toLowerCase().includes(search))
                    );
                }
                
                // Status filter
                const status = $('#statusFilter').val();
                if (status !== '') {
                    const isActive = status === '1';
                    filtered = filtered.filter(cat => cat.is_active === isActive);
                }
                
                // Sort
                const sort = $('#sortFilter').val();
                filtered.sort((a, b) => {
                    switch(sort) {
                        case 'name_asc':
                            return a.name.localeCompare(b.name);
                        case 'name_desc':
                            return b.name.localeCompare(a.name);
                        case 'items_desc':
                            return (itemCounts[b.id] || 0) - (itemCounts[a.id] || 0);
                        case 'items_asc':
                            return (itemCounts[a.id] || 0) - (itemCounts[b.id] || 0);
                        default:
                            return 0;
                    }
                });
                
                renderCategories(filtered, itemCounts);
            }
            
            function renderCategories(categories, itemCounts = {}) {
                const tbody = $('#categoriesTableBody');
                const cards = $('#categoriesCards');
                tbody.empty();
                cards.empty();
                
                if (categories.length === 0) {
                    tbody.append('<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No categories found</td></tr>');
                    cards.append('<div class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">No categories found</div>');
                    return;
                }
                
                categories.forEach(cat => {
                    const count = itemCounts[cat.id] || 0;
                    
                    // Desktop row
                    const row = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${cat.name}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">${cat.description || '-'}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">${count} items</span>
                            </td>
                            <td class="px-4 py-3">
                                <button class="toggle-status px-2 py-1 rounded ${cat.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}" 
                                        data-id="${cat.id}" data-active="${cat.is_active ? '1' : '0'}" title="Click to toggle">
                                    ${cat.is_active ? 'Active' : 'Inactive'}
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <button class="edit-category p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900 rounded-lg transition-colors" data-id="${cat.id}" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="delete-category p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition-colors ${count > 0 ? 'opacity-50 cursor-not-allowed' : ''}" 
                                            data-id="${cat.id}" ${count > 0 ? 'disabled title="Cannot delete: has items"' : 'title="Delete"'}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                    
                    // Mobile card
                    const card = `
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white text-lg">${cat.name}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">${cat.description || 'No description'}</p>
                                </div>
                                <button class="toggle-status px-2 py-1 rounded text-sm ${cat.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}" 
                                        data-id="${cat.id}" data-active="${cat.is_active ? '1' : '0'}">
                                    ${cat.is_active ? 'Active' : 'Inactive'}
                                </button>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-sm">${count} items</span>
                                <div class="flex items-center space-x-2">
                                    <button class="edit-category p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900 rounded-lg" data-id="${cat.id}" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button class="delete-category p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg ${count > 0 ? 'opacity-50 cursor-not-allowed' : ''}" 
                                            data-id="${cat.id}" ${count > 0 ? 'disabled' : ''} title="${count > 0 ? 'Cannot delete: has items' : 'Delete'}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    cards.append(card);
                });
                
                // Bind events
                $('.edit-category').on('click', function() {
                    const id = $(this).data('id');
                    editCategory(id);
                });
                
                $('.delete-category').on('click', function() {
                    if ($(this).prop('disabled')) return;
                    const id = $(this).data('id');
                    if (confirm('Delete this category?')) {
                        deleteCategory(id);
                    }
                });
                
                $('.toggle-status').on('click', function() {
                    const id = $(this).data('id');
                    const currentActive = $(this).data('active') === '1' || $(this).data('active') === 1;
                    toggleCategoryStatus(id, !currentActive);
                });
            }
            
            function editCategory(id) {
                $.ajax({
                    url: base + '/api/categories/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const cat = response.data;
                            $('#categoryId').val(cat.id);
                            $('#categoryName').val(cat.name);
                            $('#categoryDescription').val(cat.description || '');
                            $('#categoryStatus').val(cat.is_active ? '1' : '0');
                            $('#modalTitle').text('Edit Category');
                            $('#categoryModal').removeClass('hidden');
                        }
                    },
                    error: function() {
                        showMessage('Failed to load category', 'error');
                    }
                });
            }
            
            function saveCategory() {
                const data = {
                    name: $('#categoryName').val(),
                    description: $('#categoryDescription').val(),
                    is_active: $('#categoryStatus').val() === '1'
                };
                
                const id = $('#categoryId').val();
                const url = id ? base + '/api/categories/' + id : base + '/api/categories';
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
                            $('#categoryModal').addClass('hidden');
                            loadCategories();
                        } else {
                            showMessage(response.message || 'Failed to save category', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function toggleCategoryStatus(id, newStatus) {
                $.ajax({
                    url: base + '/api/categories/' + id,
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ is_active: newStatus }),
                    success: function(response) {
                        if (response.success) {
                            showMessage('Status updated', 'success');
                            loadCategories();
                        } else {
                            showMessage(response.message || 'Failed to update status', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function deleteCategory(id) {
                $.ajax({
                    url: base + '/api/categories/' + id,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Category deleted successfully', 'success');
                            loadCategories();
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
            
            // Logout function
            function performLogout() {
                $.ajax({
                    url: base + '/api/auth/logout',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (base || '/');
                        window.location.href = base + '/login';
                    },
                    error: function() {
                        localStorage.removeItem('auth_token');
                        localStorage.removeItem('user');
                        document.cookie = 'auth_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + (base || '/');
                        window.location.href = base + '/login';
                    }
                });
            }
            
            $('#logoutBtn, #mobileLogoutBtn').on('click', function() {
                if ($(this).hasClass('disabled')) return;
                $(this).addClass('disabled opacity-50 cursor-not-allowed');
                performLogout();
            });
        });
    </script>
</body>
</html>

