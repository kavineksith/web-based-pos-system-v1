<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Person looking confused -->
                <circle cx="400" cy="200" r="60" fill="#fbbf24" opacity="0.2"/>
                <circle cx="400" cy="180" r="40" fill="#fbbf24"/>
                <ellipse cx="400" cy="250" rx="50" ry="70" fill="#fbbf24"/>
                
                <!-- Question mark -->
                <text x="400" y="200" font-size="80" font-weight="bold" fill="#3b82f6" text-anchor="middle">?</text>
                
                <!-- 404 Text -->
                <text x="400" y="350" font-size="120" font-weight="bold" fill="url(#grad1)" text-anchor="middle">404</text>
                
                <!-- Floating elements -->
                <circle cx="150" cy="150" r="20" fill="#60a5fa" opacity="0.3" class="animate-pulse"/>
                <circle cx="650" cy="200" r="15" fill="#a78bfa" opacity="0.3" class="animate-pulse"/>
                <circle cx="200" cy="400" r="25" fill="#34d399" opacity="0.2" class="animate-pulse"/>
                <circle cx="600" cy="450" r="18" fill="#f472b6" opacity="0.3" class="animate-pulse"/>
            </svg>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Oops! The page you're looking for seems to have wandered off. 
            It might have been moved, deleted, or never existed.
        </p>
        
        <div class="space-x-4">
            <?php
            // Ensure links stay inside the application base path (e.g. /pos-system)
            $basePath = defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
            $dashboardUrl = htmlspecialchars($basePath . '/dashboard');
            ?>
            <a href="<?php echo $dashboardUrl; ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Go to Dashboard
            </a>
            <a href="javascript:(function(){var base=window.location.origin + '<?php echo $basePath; ?>';if(document.referrer && document.referrer.indexOf(base)===0){history.back();}else{window.location.href='<?php echo $dashboardUrl; ?>';}}())" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                Go Back
            </a>
        </div>
    </div>
</body>
</html>

