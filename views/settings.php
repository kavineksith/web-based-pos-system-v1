<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Lucky Book Shop POS</title>
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
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">System Settings</h2>
        
        <div id="message" class="hidden mb-4 p-4 rounded"></div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shop Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Shop Name</label>
                    <input type="text" id="shop_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Shop Address</label>
                    <input type="text" id="shop_address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Shop Phone</label>
                    <input type="text" id="shop_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Receipt Footer Message</label>
                    <textarea id="receipt_footer" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Thank you for shopping with us!"></textarea>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Inventory Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enable Stock Alerts</label>
                    <select id="stock_alerts_enabled" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Backup Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Auto Backup</label>
                    <select id="backup_auto_enabled" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Backup Retention (Days)</label>
                    <input type="number" id="backup_retention_days" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div class="mt-4">
                <button id="createBackupBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Create Manual Backup
                </button>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Soft Delete Settings</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Retention Period (Days)</label>
                <input type="number" id="soft_delete_retention_days" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enable Email</label>
                    <select id="email_enabled" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="1">Enabled</option>
                        <option value="0">Disabled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host</label>
                    <input type="text" id="email_host" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Port</label>
                    <input type="number" id="email_port" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Username</label>
                    <input type="text" id="email_username" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Password</label>
                    <input type="password" id="email_password" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Password Policy</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Expiry (Days)</label>
                <input type="number" id="password_expiry_days" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Database Import</h3>
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Import database from SQL file. File will be sanitized before import.</p>
                <input type="file" id="sqlImportFile" accept=".sql" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            <button id="importSqlBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Import SQL File
            </button>
        </div>
        
        <div class="mb-6">
            <button id="saveSettingsBtn" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">
                Save All Settings
            </button>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('auth_token');
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            const base = (typeof BASE_PATH !== 'undefined') ? BASE_PATH : '';
            
            if (!token || user.role !== 'admin') {
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
            
            // Load settings
            $.ajax({
                url: base + '/api/settings',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const settings = response.data;
                        console.log('Loaded settings:', settings); // Debug log
                        
                        // Shop Information
                        $('#shop_name').val(settings.shop_name || 'Lucky Book Shop');
                        $('#shop_address').val(settings.shop_address || '');
                        $('#shop_phone').val(settings.shop_phone || '');
                        $('#receipt_footer').val(settings.receipt_footer || 'Thank you for shopping with us!');
                        
                        // Inventory
                        $('#low_stock_threshold').val(settings.low_stock_threshold !== undefined ? settings.low_stock_threshold : 10);
                        $('#stock_alerts_enabled').val(settings.stock_alerts_enabled !== undefined ? (settings.stock_alerts_enabled ? '1' : '0') : '1');
                        
                        // Backup
                        $('#backup_auto_enabled').val(settings.backup_auto_enabled !== undefined ? (settings.backup_auto_enabled ? '1' : '0') : '1');
                        $('#backup_retention_days').val(settings.backup_retention_days !== undefined ? settings.backup_retention_days : 7);
                        $('#soft_delete_retention_days').val(settings.soft_delete_retention_days !== undefined ? settings.soft_delete_retention_days : 30);
                        
                        // Email
                        $('#email_enabled').val(settings.email_enabled !== undefined ? (settings.email_enabled ? '1' : '0') : '0');
                        $('#email_host').val(settings.email_host || '');
                        $('#email_port').val(settings.email_port !== undefined ? settings.email_port : 587);
                        $('#email_username').val(settings.email_username || '');
                        $('#email_password').val(settings.email_password || '');
                        
                        // Security
                        $('#password_expiry_days').val(settings.password_expiry_days !== undefined ? settings.password_expiry_days : 90);
                        
                        showMessage('Settings loaded successfully', 'success');
                    } else {
                        showMessage('Failed to load settings: Invalid response', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load settings:', xhr);
                    const response = xhr.responseJSON || {};
                    showMessage('Failed to load settings: ' + (response.message || 'Network error'), 'error');
                }
            });
            
            $('#saveSettingsBtn').on('click', function() {
                // Validate required fields
                if (!$('#shop_name').val().trim()) {
                    showMessage('Shop name is required', 'error');
                    return;
                }
                
                const settings = {
                    // Shop Information
                    shop_name: $('#shop_name').val(),
                    shop_address: $('#shop_address').val(),
                    shop_phone: $('#shop_phone').val(),
                    receipt_footer: $('#receipt_footer').val(),
                    // Inventory
                    low_stock_threshold: parseInt($('#low_stock_threshold').val()) || 10,
                    stock_alerts_enabled: $('#stock_alerts_enabled').val() === '1',
                    // Backup
                    backup_auto_enabled: $('#backup_auto_enabled').val() === '1',
                    backup_retention_days: parseInt($('#backup_retention_days').val()) || 7,
                    soft_delete_retention_days: parseInt($('#soft_delete_retention_days').val()) || 30,
                    // Email
                    email_enabled: $('#email_enabled').val() === '1',
                    email_host: $('#email_host').val(),
                    email_port: parseInt($('#email_port').val()) || 587,
                    email_username: $('#email_username').val(),
                    email_password: $('#email_password').val(), // Note: In a real app, this should be handled securely
                    // Security
                    password_expiry_days: parseInt($('#password_expiry_days').val()) || 90,
                };
                
                // Disable button to prevent multiple clicks
                $(this).prop('disabled', true).text('Saving...');
                
                $.ajax({
                    url: (BASE_PATH || '') + '/api/settings',
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(settings),
                    success: function(response) {
                        if (response.success) {
                            showMessage('Settings saved successfully', 'success');
                        } else {
                            showMessage(response.message || 'Failed to save settings', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        showMessage(response.message || 'An error occurred while saving settings', 'error');
                    },
                    complete: function() {
                        // Re-enable button
                        $('#saveSettingsBtn').prop('disabled', false).text('Save All Settings');
                    }
                });
            });
            
            $('#createBackupBtn').on('click', function() {
                $.ajax({
                    url: (BASE_PATH || '') + '/api/backup/create',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Backup created successfully: ' + response.data.filename, 'success');
                        } else {
                            showMessage('Failed to create backup', 'error');
                        }
                    },
                    error: function() {
                        showMessage('An error occurred', 'error');
                    }
                });
            });
            
            // SQL Import
            $('#importSqlBtn').on('click', function() {
                const fileInput = $('#sqlImportFile')[0];
                if (!fileInput.files.length) {
                    showMessage('Please select a SQL file', 'error');
                    return;
                }
                
                if (!confirm('WARNING: This will replace the current database. Are you sure?')) {
                    return;
                }
                
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                
                $.ajax({
                    url: (BASE_PATH || '') + '/api/backup/import',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showMessage('Database imported successfully', 'success');
                            $('#sqlImportFile').val('');
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
        });
    </script>
</body>
</html>

