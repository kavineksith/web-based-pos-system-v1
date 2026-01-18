<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad3" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#ef4444;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Lock -->
                <rect x="350" y="200" width="100" height="120" rx="10" fill="#f59e0b" opacity="0.8"/>
                <rect x="360" y="210" width="80" height="60" rx="5" fill="#fff"/>
                <path d="M 400 200 Q 400 180 400 160 A 20 20 0 0 1 400 120" stroke="#f59e0b" stroke-width="15" fill="none" stroke-linecap="round"/>
                
                <!-- Shield -->
                <path d="M 400 180 L 420 200 L 400 240 L 380 200 Z" fill="#ef4444" opacity="0.6"/>
                
                <!-- Stop sign -->
                <polygon points="400,280 450,300 400,320 350,300" fill="#ef4444" opacity="0.8"/>
                <text x="400" y="305" font-size="40" font-weight="bold" fill="#fff" text-anchor="middle">STOP</text>
                
                <!-- 403 Text -->
                <text x="400" y="420" font-size="120" font-weight="bold" fill="url(#grad3)" text-anchor="middle">403</text>
            </svg>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">403</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Access Forbidden</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            You don't have permission to access this resource. 
            This area is restricted to authorized users only.
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

