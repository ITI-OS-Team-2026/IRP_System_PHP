<?php
/**
 * Smart Routing logic for the custom MVC
 * Automatically detects the base path for XAMPP or Linux/Homebrew
 */

// 1. Detect Request URI and Script Name
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /ITI/IRP_System_PHP/public/index.php

// 2. Calculate the Base Path (e.g., /ITI/IRP_System_PHP/public)
$basePath = str_replace('/index.php', '', $scriptName);
if ($basePath === '/') $basePath = '';

// 3. Extract the virtual path (remove base path from URI)
$path = $requestUri;
if (!empty($basePath) && strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
}

// Strip out query parameters
$path = parse_url($path, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// Define a constant for globally accessible Base URL in views
define('BASE_URL', $basePath);

// Simple Route mapping
switch ($path) {
    case '/':
        require __DIR__ . '/../app/Views/landing.php';
        break;
        
    case '/pending-approval':
        require __DIR__ . '/../app/Views/pending-approval.php';
        break;

    case '/dashboard':
        AuthMiddleware::requireLogin();
        $role = $_SESSION['user_role'] ?? '';
        if ($role === 'student') {
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        } elseif ($role === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
        require __DIR__ . '/../app/Views/dashboard.php';
        break;

    case '/student/dashboard':
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../app/Views/student/student_dashboard.php';
        break;

    case '/student/submissions':
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../app/Views/student/submissions.php';
        break;

    case '/student/submission/create':
        require __DIR__ . '/../app/Controllers/SubmissionController.php';
        (new SubmissionController())->create();
        break;

    case '/student/submission/store':
        require __DIR__ . '/../app/Controllers/SubmissionController.php';
        (new SubmissionController())->store();
        break;

    case '/student/settings':
        require __DIR__ . '/../app/Controllers/StudentSettingsController.php';
        (new StudentSettingsController())->show();
        break;

    case '/student/settings/update':
        require __DIR__ . '/../app/Controllers/StudentSettingsController.php';
        (new StudentSettingsController())->updateProfile();
        break;

    case '/student/settings/password':
        require __DIR__ . '/../app/Controllers/StudentSettingsController.php';
        (new StudentSettingsController())->updatePassword();
        break;

    case '/admin/dashboard':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Views/admin/admin_dashboard.php';
        break;

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
    case '/logout':
        require __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    default:
        // Dynamic routes (e.g. submissions/123)
        if (preg_match('#^/student/submissions/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('student');
            $_GET['id'] = (int) $matches[1];
            require __DIR__ . '/../app/Controllers/SubmissionController.php';
            (new SubmissionController())->show();
            break;
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found', 'path' => $path, 'base' => BASE_URL]);
        break;
}
