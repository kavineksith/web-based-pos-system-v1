<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url('/js/theme.js'); ?>"></script>
    <script src="<?php echo base_url('/js/icons.js'); ?>"></script>
    <style>
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .bg-gray-50 { background-color: #374151 !important; }
        .dark .bg-gray-100 { background-color: #111827 !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500 { color: #d1d5db !important; }
        .dark .border-gray-300, .dark .border-gray-200 { border-color: #4b5563 !important; }
    </style>
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
    <?php
    // Base path support for subdirectory deployments (e.g. /pos-system)
    $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
    echo "<script>const BASE_PATH = " . json_encode($basePath) . ";</script>\n";
    function base_url($path = '') {
        global $basePath;
        return htmlspecialchars($basePath . $path);
    }
    ?>
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
            <button id="addUserBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add User
            </button>
        </div>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" id="searchInput" placeholder="Search users..." 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <select id="roleFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                        <option value="supervisor">Supervisor</option>
                    </select>
                </div>
                <div>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
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
        
        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Password Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Users will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Add User</h3>
                <form id="userForm" class="space-y-4">
                    <input type="hidden" id="userId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" id="username" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="firstName" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="lastName" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="role" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="cashier">Cashier</option>
                            <option value="supervisor">Supervisor</option>
                        </select>
                    </div>
                    <div id="editStatusDiv" class="hidden">
                        <label class="flex items-center">
                            <input type="checkbox" id="isActive" class="mr-2">
                            <span class="text-sm">Active</span>
                        </label>
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
                window.location.href = '/login';
                return;
            }
            
            if (user.role !== 'admin') {
                window.location.href = '/dashboard';
                return;
            }
            
            $('#userName').text(user.username || 'User');
            $('#userNameMobile').text(user.username || 'User');
            
            // Mobile menu toggle
            $('#mobileMenuBtn').on('click', function() {
                $('#mobileMenu').toggleClass('hidden');
            });
            
            // Theme toggle
            $('#themeToggleBtn, #themeToggleBtnMobile').on('click', function() {
                ThemeManager.toggle();
            });
            
            // Logout handlers
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
            
            loadUsers();
            
            $('#addUserBtn').on('click', function() {
                $('#modalTitle').text('Add User');
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#editStatusDiv').addClass('hidden');
                $('#userModal').removeClass('hidden');
            });
            
            $('#cancelBtn').on('click', function() {
                $('#userModal').addClass('hidden');
            });
            
            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                saveUser();
            });
            
            $('#applyFiltersBtn').on('click', function() {
                loadUsers();
            });
            
            $('#clearFiltersBtn').on('click', function() {
                $('#searchInput').val('');
                $('#roleFilter').val('');
                $('#statusFilter').val('');
                loadUsers();
            });
            
            function loadUsers() {
                const filters = {};
                if ($('#searchInput').val()) filters.search = $('#searchInput').val();
                if ($('#roleFilter').val()) filters.role = $('#roleFilter').val();
                if ($('#statusFilter').val() !== '') filters.is_active = $('#statusFilter').val();
                
                $.ajax({
                    url: base + '/api/users',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: filters,
                    success: function(response) {
                        if (response.success) {
                            renderUsers(response.data);
                        }
                    }
                });
            }
            
            function renderUsers(users) {
                const tbody = $('#usersTableBody');
                tbody.empty();
                
                if (users.length === 0) {
                    tbody.append('<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No users found</td></tr>');
                    return;
                }
                
                users.forEach(userItem => {
                    const roleClass = {
                        'admin': 'bg-purple-100 text-purple-800',
                        'supervisor': 'bg-blue-100 text-blue-800',
                        'cashier': 'bg-green-100 text-green-800'
                    }[userItem.role] || 'bg-gray-100 text-gray-800';
                    
                    const passwordStatus = userItem.must_change_password ? 
                        '<span class="text-red-600 text-xs">Must Change</span>' : 
                        '<span class="text-green-600 text-xs">OK</span>';
                    
                    const row = `
                        <tr>
                            <td class="px-4 py-3">${userItem.username}</td>
                            <td class="px-4 py-3">${userItem.first_name} ${userItem.last_name}</td>
                            <td class="px-4 py-3">${userItem.email}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded ${roleClass}">
                                    ${userItem.role.toUpperCase()}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded ${userItem.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${userItem.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="px-4 py-3">${passwordStatus}</td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button class="edit-user p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded" data-id="${userItem.id}" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="reset-password p-1.5 text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded" data-id="${userItem.id}" title="Reset Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>
                                    <button class="delete-user p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" data-id="${userItem.id}" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                $('.edit-user').on('click', function() {
                    const id = $(this).data('id');
                    editUser(id);
                });
                
                $('.reset-password').on('click', function() {
                    const id = $(this).data('id');
                    if (confirm('Reset password for this user? New password will be sent to their email.')) {
                        resetPassword(id);
                    }
                });
                
                $('.delete-user').on('click', function() {
                    const id = $(this).data('id');
                    if (confirm('Delete this user? This action cannot be undone.')) {
                        deleteUser(id);
                    }
                });
            }
            
            function editUser(id) {
                $.ajax({
                    url: base + '/api/users/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            const userItem = response.data;
                            $('#userId').val(userItem.id);
                            $('#username').val(userItem.username).prop('readonly', true);
                            $('#email').val(userItem.email);
                            $('#firstName').val(userItem.first_name);
                            $('#lastName').val(userItem.last_name);
                            $('#role').val(userItem.role);
                            $('#isActive').prop('checked', userItem.is_active);
                            $('#modalTitle').text('Edit User');
                            $('#editStatusDiv').removeClass('hidden');
                            $('#userModal').removeClass('hidden');
                        }
                    }
                });
            }
            
            function saveUser() {
                const id = $('#userId').val();
                const data = {
                    username: $('#username').val(),
                    email: $('#email').val(),
                    first_name: $('#firstName').val(),
                    last_name: $('#lastName').val(),
                    role: $('#role').val()
                };
                
                if (id) {
                    data.is_active = $('#isActive').is(':checked');
                }
                
                const url = id ? base + '/api/users/' + id : base + '/api/users';
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
                            let message = response.message || 'User saved successfully';
                            if (response.initial_password) {
                                message += ' Initial password: ' + response.initial_password;
                            }
                            showMessage(message, 'success');
                            $('#userModal').addClass('hidden');
                            loadUsers();
                        } else {
                            showMessage(response.message || 'Failed to save user', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        let errorMsg = response.message || 'An error occurred';
                        if (response.errors) {
                            errorMsg += ': ' + Object.values(response.errors).join(', ');
                        }
                        showMessage(errorMsg, 'error');
                    }
                });
            }
            
            function resetPassword(id) {
                $.ajax({
                    url: base + '/api/users/' + id + '/reset-password',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            let message = response.message || 'Password reset successfully';
                            if (response.new_password) {
                                message += ' New password: ' + response.new_password;
                            }
                            showMessage(message, 'success');
                            loadUsers();
                        } else {
                            showMessage(response.message || 'Failed to reset password', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred', 'error');
                    }
                });
            }
            
            function deleteUser(id) {
                $.ajax({
                    url: base + '/api/users/' + id,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('User deleted successfully', 'success');
                            loadUsers();
                        } else {
                            showMessage(response.message || 'Failed to delete user', 'error');
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
                $msg.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800');
                $msg.addClass(type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                $msg.text(message);
                setTimeout(() => $msg.addClass('hidden'), 8000);
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

