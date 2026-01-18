<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Unauthorized | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad4" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Person with key -->
                <circle cx="400" cy="180" r="50" fill="#3b82f6"/>
                <ellipse cx="400" cy="280" rx="60" ry="90" fill="#3b82f6"/>
                
                <!-- Key -->
                <rect x="480" y="200" width="40" height="15" rx="5" fill="#fbbf24"/>
                <circle cx="500" y="207" r="8" fill="#fbbf24"/>
                <rect x="480" y="200" width="20" height="15" rx="5" fill="#fff"/>
                
                <!-- Lock -->
                <rect x="320" y="200" width="60" height="70" rx="5" fill="#6b7280"/>
                <rect x="330" y="210" width="40" height="40" rx="3" fill="#fff"/>
                <path d="M 350 200 Q 350 180 350 160 A 15 15 0 0 1 350 130" stroke="#6b7280" stroke-width="12" fill="none" stroke-linecap="round"/>
                
                <!-- Question mark above head -->
                <circle cx="400" cy="100" r="25" fill="#fbbf24" opacity="0.8"/>
                <text x="400" y="115" font-size="30" font-weight="bold" fill="#fff" text-anchor="middle">?</text>
                
                <!-- 401 Text -->
                <text x="400" y="420" font-size="120" font-weight="bold" fill="url(#grad4)" text-anchor="middle">401</text>
            </svg>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">401</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Unauthorized Access</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            You need to be authenticated to access this resource. 
            Please log in with valid credentials.
        </p>
        
        <div class="space-x-4">
            <?php
            // Keep unauthorized users inside the app base path
            $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
            $loginUrl = htmlspecialchars($basePath . '/login');
            $dashboardUrl = htmlspecialchars($basePath . '/dashboard');
            ?>
            <a href="<?php echo $loginUrl; ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Go to Login
            </a>
            <a href="<?php echo $dashboardUrl; ?>" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>

