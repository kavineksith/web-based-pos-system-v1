<?php
/**
 * Rate Limiting Configuration
 */

namespace App\Config;

class RateLimiter
{
    private const MAX_REQUESTS = 100;
    private const TIME_WINDOW = 60; // seconds
    
    public static function checkLimit(string $identifier): bool
    {
        $cacheFile = STORAGE_PATH . '/cache/rate_limit_' . md5($identifier) . '.json';
        
        $data = [];
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true) ?? [];
        }
        
        $currentTime = time();
        $windowStart = $currentTime - self::TIME_WINDOW;
        
        // Remove old requests
        $data['requests'] = array_filter($data['requests'] ?? [], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        // Check limit
        if (count($data['requests']) >= self::MAX_REQUESTS) {
            return false;
        }
        
        // Add current request
        $data['requests'][] = $currentTime;
        $data['last_request'] = $currentTime;
        
        file_put_contents($cacheFile, json_encode($data));
        
        return true;
    }
    
    public static function getRemainingRequests(string $identifier): int
    {
        $cacheFile = STORAGE_PATH . '/cache/rate_limit_' . md5($identifier) . '.json';
        
        if (!file_exists($cacheFile)) {
            return self::MAX_REQUESTS;
        }
        
        $data = json_decode(file_get_contents($cacheFile), true) ?? [];
        $currentTime = time();
        $windowStart = $currentTime - self::TIME_WINDOW;
        
        $requests = array_filter($data['requests'] ?? [], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        return max(0, self::MAX_REQUESTS - count($requests));
    }
}

