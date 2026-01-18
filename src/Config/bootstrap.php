<?php
/**
 * Bootstrap Configuration
 */

// Define base paths
define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/src');
define('CONFIG_PATH', APP_PATH . '/Config');
define('VIEWS_PATH', BASE_PATH . '/views');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('BACKUP_PATH', STORAGE_PATH . '/backups');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

// Get base URL path (for subdirectory support)
function getBasePath(): string
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = rtrim($scriptDir, '/\\');
    
    // If running from root or if the path is empty/just '/', return empty string
    if ($basePath === '' || $basePath === '/') {
        return '';
    }
    
    // Ensure it starts with / but doesn't end with /
    $basePath = '/' . ltrim($basePath, '/\\');
    $basePath = rtrim($basePath, '/\\');
    
    return $basePath;
}

// Define base URL path constant and full base URL
define('BASE_URL_PATH', getBasePath());

// Construct full base URL using server information
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = BASE_URL_PATH;

// Ensure basePath starts with / if not empty
if ($basePath !== '' && $basePath[0] !== '/') {
    $basePath = '/' . $basePath;
}

define('BASE_URL', $protocol . '://' . $host . $basePath);

// Create necessary directories
$directories = [
    STORAGE_PATH,
    BACKUP_PATH,
    UPLOAD_PATH,
    STORAGE_PATH . '/logs',
    STORAGE_PATH . '/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Timezone
date_default_timezone_set('Asia/Colombo');

