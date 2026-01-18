<?php
/**
 * POS System Entry Point
 * Lucky Book Shop
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Config/bootstrap.php';

use App\Config\Router;
use App\Config\CorsHandler;
use App\Config\ErrorHandler;

// Initialize CORS
CorsHandler::handle();

// Initialize Error Handler
ErrorHandler::register();

// Start Session
session_start();

// Route Request
$router = new Router();
$router->dispatch();

