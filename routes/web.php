<?php
/**
 * Simple Routing logic for the custom MVC
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('/index.php', '', $scriptName);

// Clean up the path: remove base path and query strings
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
        require __DIR__ . '/../app/Views/auth/register.php';
        break;

    // API Routes
    case '/api/register':
        require __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->register();
        break;

    case '/api/login':
        require __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case '/api/logout':
        require __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    default:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found', 'path' => $path]);
        break;
}
