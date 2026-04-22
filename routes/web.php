<?php
/**
 * Simple Routing logic for the custom MVC
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
// Strip out query parameters and base path
$basePath = '/ITI/IRP_System_PHP/public';
$path = str_replace($basePath, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// Simple Route mapping
switch ($path) {
    case '/':
    case '/login':
        require __DIR__ . '/../app/Views/auth/login.php';
        break;
        
    case '/register':
        // Placeholder for student registration
        echo "<h1>Student Registration Page (Coming Soon)</h1>";
        break;

    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; padding: 50px;'>404 - الصفحة غير موجودة</h1>";
        break;
}
