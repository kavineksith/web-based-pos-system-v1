<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Bad Request | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad5" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#ef4444;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Document with X -->
                <rect x="300" y="150" width="200" height="250" rx="5" fill="#fff" stroke="#f59e0b" stroke-width="3"/>
                <line x1="320" y1="180" x2="480" y2="180" stroke="#e5e7eb" stroke-width="2"/>
                <line x1="320" y1="210" x2="480" y2="210" stroke="#e5e7eb" stroke-width="2"/>
                <line x1="320" y1="240" x2="480" y2="240" stroke="#e5e7eb" stroke-width="2"/>
                
                <!-- X mark -->
                <line x1="350" y1="280" x2="450" y2="360" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
                <line x1="450" y1="280" x2="350" y2="360" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
                
                <!-- Warning icon -->
                <circle cx="400" cy="100" r="30" fill="#fbbf24"/>
                <text x="400" y="115" font-size="35" font-weight="bold" fill="#fff" text-anchor="middle">!</text>
                
                <!-- 400 Text -->
                <text x="400" y="480" font-size="120" font-weight="bold" fill="url(#grad5)" text-anchor="middle">400</text>
            </svg>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">400</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Bad Request</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            The request you sent was invalid or malformed. 
            Please check your input and try again.
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
            <a href="javascript:(function(){
                var base = window.location.origin + '<?php echo $basePath; ?>';
                if (document.referrer && document.referrer.indexOf(base) === 0) {
                    history.back();
                } else {
                    window.location.href = '<?php echo $dashboardUrl; ?>';
                }
            }())" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Go Back
            </a>
        </div>
    </div>
</body>
</html>

