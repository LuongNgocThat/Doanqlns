<?php
/**
 * Base URL Helper for ngrok compatibility
 * Automatically detects the correct base URL for both localhost and ngrok
 */

// Detect base URL for ngrok compatibility
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

// Handle different environments
if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
    // Web request
    $host = $_SERVER['HTTP_HOST'];
    
    // Force HTTPS for ngrok domains
    if (strpos($host, 'ngrok') !== false || strpos($host, 'ngrok-free.app') !== false) {
        $protocol = 'https';
    }
} elseif (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) {
    // CLI with server name
    $host = $_SERVER['SERVER_NAME'];
} else {
    // Default fallback
    $host = 'localhost';
}

$base_url = $protocol . '://' . $host . '/doanqlns';

// Make it available globally
if (!defined('BASE_URL')) {
    define('BASE_URL', $base_url);
}
?>
