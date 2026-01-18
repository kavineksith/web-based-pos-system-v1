<?php
/**
 * View Helper Functions
 */

namespace App\Helpers;

class ViewHelper
{
    /**
     * Get base URL path for subdirectory support
     */
    public static function getBasePath(): string
    {
        return defined('BASE_URL_PATH') ? BASE_URL_PATH : '';
    }
    
    /**
     * Output JavaScript variable for base path
     */
    public static function outputBasePathScript(): void
    {
        $basePath = self::getBasePath();
        echo "<script>const BASE_PATH = " . json_encode($basePath) . ";</script>\n";
    }
    
    /**
     * Get full URL path with base path
     */
    public static function url(string $path): string
    {
        $basePath = self::getBasePath();
        return $basePath . $path;
    }
}

