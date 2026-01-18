<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | Lucky Book Shop POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-4">
        <!-- SVG Illustration -->
        <div class="max-w-md mx-auto mb-8">
            <svg viewBox="0 0 800 600" class="w-full h-auto">
                <defs>
                    <linearGradient id="grad6" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#6b7280;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#374151;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Background -->
                <rect width="800" height="600" fill="#f3f4f6"/>
                
                <!-- Alert triangle -->
                <path d="M 400 150 L 500 350 L 300 350 Z" fill="#fbbf24" opacity="0.8"/>
                <text x="400" y="280" font-size="60" font-weight="bold" fill="#fff" text-anchor="middle">!</text>
                
                <!-- Floating elements -->
                <circle cx="200" cy="200" r="30" fill="#60a5fa" opacity="0.2" class="animate-pulse"/>
                <circle cx="600" cy="250" r="25" fill="#a78bfa" opacity="0.2" class="animate-pulse"/>
                <circle cx="150" cy="400" r="35" fill="#34d399" opacity="0.15" class="animate-pulse"/>
                <circle cx="650" cy="450" r="28" fill="#f472b6" opacity="0.2" class="animate-pulse"/>
                
                <!-- Error text -->
                <text x="400" y="450" font-size="80" font-weight="bold" fill="url(#grad6)" text-anchor="middle">ERROR</text>
            </svg>
        </div>
        
        <h1 class="text-5xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($errorCode ?? 'Error'); ?></h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4"><?php echo htmlspecialchars($errorTitle ?? 'An Error Occurred'); ?></h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            <?php echo htmlspecialchars($errorMessage ?? 'Something went wrong. Please try again later.'); ?>
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

