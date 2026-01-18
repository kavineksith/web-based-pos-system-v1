<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#ef4444;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#f97316;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Server/Box -->
                <rect x="300" y="200" width="200" height="150" rx="10" fill="#ef4444" opacity="0.8"/>
                <rect x="320" y="220" width="160" height="20" rx="5" fill="#fff"/>
                <rect x="320" y="250" width="160" height="20" rx="5" fill="#fff" opacity="0.7"/>
                <rect x="320" y="280" width="160" height="20" rx="5" fill="#fff" opacity="0.5"/>
                
                <!-- Warning symbol -->
                <path d="M 400 120 L 430 180 L 370 180 Z" fill="#fbbf24"/>
                <text x="400" y="165" font-size="30" font-weight="bold" fill="#fff" text-anchor="middle">!</text>
                
                <!-- Error sparks -->
                <circle cx="280" cy="220" r="8" fill="#fbbf24" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0.2;0.8" dur="1s" repeatCount="indefinite"/>
                </circle>
                <circle cx="520" cy="250" r="6" fill="#fbbf24" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0.2;0.8" dur="1.5s" repeatCount="indefinite"/>
                </circle>
                <circle cx="290" cy="300" r="7" fill="#fbbf24" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0.2;0.8" dur="0.8s" repeatCount="indefinite"/>
                </circle>
                
                <!-- 500 Text -->
                <text x="400" y="420" font-size="120" font-weight="bold" fill="url(#grad2)" text-anchor="middle">500</text>
            </svg>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Internal Server Error</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Something went wrong on our end. We're working to fix the issue. 
            Please try again in a few moments.
        </p>
        
        <div class="space-x-4">
            <?php
            // Keep navigation inside the app base path
            $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
            $dashboardUrl = htmlspecialchars($basePath . '/dashboard');
            ?>
            <a href="<?php echo $dashboardUrl; ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Go to Dashboard
            </a>
            <button onclick="location.reload()" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Reload Page
            </button>
        </div>
    </div>
</body>
</html>

