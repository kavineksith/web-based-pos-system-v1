<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {} }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php
    // Output base path for JavaScript
    $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
    echo "<script>const BASE_PATH = " . json_encode($basePath) . ";</script>\n";
    ?>
    <style>
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .bg-gray-100 { background-color: #111827 !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600 { color: #d1d5db !important; }
        .dark .border-gray-300 { border-color: #4b5563 !important; }
        .dark input { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen p-4 transition-colors duration-200">
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Lucky Book Shop</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">POS System Login</p>
        </div>
        
        <form id="loginForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Username</span>
                </label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Password</span>
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            
            <div id="errorMessage" class="hidden text-red-600 dark:text-red-400 text-sm"></div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span>Login</span>
            </button>
        </form>
        
        <!-- Theme toggle -->
        <div class="mt-4 text-center">
            <button id="themeToggleBtn" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm flex items-center justify-center space-x-2 mx-auto">
                <svg id="themeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <span>Toggle Theme</span>
            </button>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            // Initialize theme
            const savedTheme = localStorage.getItem('theme_mode') || 'light';
            applyTheme(savedTheme);
            
            function applyTheme(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                localStorage.setItem('theme_mode', theme);
            }
            
            $('#themeToggleBtn').on('click', function() {
                const currentTheme = localStorage.getItem('theme_mode') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                applyTheme(newTheme);
            });
            
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const username = $('#username').val();
                const password = $('#password').val();
                
                $.ajax({
                    url: (BASE_PATH || '') + '/api/auth/login',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        username: username,
                        password: password
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Store token in localStorage for AJAX calls
                            const token = response.data.token;
                            localStorage.setItem('auth_token', token);
                            
                            // Store user data including must_change_password flag
                            const userData = response.data.user;
                            userData.must_change_password = response.data.must_change_password;
                            localStorage.setItem('user', JSON.stringify(userData));

                            // Also store token in cookie so PHP middleware can read it for page loads
                            var path = (typeof BASE_PATH !== 'undefined' && BASE_PATH) ? BASE_PATH : '/';
                            document.cookie = 'auth_token=' + encodeURIComponent(token) + '; path=' + path;
                            
                            // Redirect to dashboard - the dashboard will check must_change_password and show modal
                            window.location.href = (BASE_PATH || '') + '/dashboard';
                        } else {
                            $('#errorMessage').text(response.message || 'Login failed').removeClass('hidden');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        $('#errorMessage').text(response.message || 'An error occurred').removeClass('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>

