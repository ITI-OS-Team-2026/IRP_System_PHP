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
        } elseif ($role === 'sample_size_officer') {
            header('Location: ' . BASE_URL . '/officer/sample-size/queue');
            exit;
        } elseif ($role === 'reviewer') {
            header('Location: ' . BASE_URL . '/reviewer/dashboard');
            exit;
        } elseif ($role === 'manager') {
            header('Location: ' . BASE_URL . '/committee/certificates');
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

    case '/student/payment/process':
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../app/Controllers/PaymentController.php';
        (new PaymentController())->process();
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

    case '/officer/sample-size/queue':
        AuthMiddleware::requireRole('sample_size_officer');
        require __DIR__ . '/../app/Controllers/SampleSizeController.php';
        (new SampleSizeController())->queue();
        break;

    case '/officer/sample-size/archives':
        AuthMiddleware::requireRole('sample_size_officer');
        require __DIR__ . '/../app/Controllers/SampleSizeController.php';
        (new SampleSizeController())->archives();
        break;

    case '/officer/sample-size/store':
        AuthMiddleware::requireRole('sample_size_officer');
        require __DIR__ . '/../app/Controllers/SampleSizeController.php';
        (new SampleSizeController())->store();
        break;

    // Reviewer Routes
    case '/reviewer/dashboard':
        AuthMiddleware::requireRole('reviewer');
        require __DIR__ . '/../app/Controllers/ReviewerController.php';
        (new ReviewerController())->dashboard();
        break;

    case (preg_match('/^\/reviewer\/view\/(\d+)$/', $path, $matches) ? true : false):
        AuthMiddleware::requireRole('reviewer');
        require __DIR__ . '/../app/Controllers/ReviewerController.php';
        (new ReviewerController())->viewSubmission($matches[1]);
        break;

    case '/reviewer/submit-evaluation':
        AuthMiddleware::requireRole('reviewer');
        require __DIR__ . '/../app/Controllers/ReviewerController.php';
        (new ReviewerController())->submitEvaluation();
        break;

    case '/reviewer/history':
        AuthMiddleware::requireRole('reviewer');
        require __DIR__ . '/../app/Controllers/ReviewerController.php';
        (new ReviewerController())->history();
        break;

    // Committee Manager Routes
    case '/committee/dashboard':
        AuthMiddleware::requireRole('manager');
        require __DIR__ . '/../app/Controllers/CommitteeController.php';
        (new CommitteeController())->dashboard();
        break;

    case '/committee/approvals':
        AuthMiddleware::requireRole('manager');
        require __DIR__ . '/../app/Controllers/CommitteeController.php';
        (new CommitteeController())->approvals();
        break;

    case '/committee/certificates':
        AuthMiddleware::requireRole('manager');
        require __DIR__ . '/../app/Controllers/CommitteeController.php';
        (new CommitteeController())->certificates();
        break;

    case '/officer/sample-size/input':
        AuthMiddleware::requireRole('officer');
        require __DIR__ . '/../app/Controllers/SampleSizeController.php';
        (new SampleSizeController())->input();
        break;

    case '/admin/dashboard':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->dashboard();
        break;

    case '/admin/user-activation':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->userActivation();
        break;

    case '/admin/user-activation/activate':
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->activateUser();
        break;

    case '/admin/initial-preview-queue':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->initialPreviewQueue();
        break;

    case '/admin/initial-preview-queue/assign-serial':
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->assignInitialPreviewSerial();
        break;

    case '/admin/reviewer-assignment':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->reviewerAssignment();
        break;

    case '/admin/reviewer-assignment/save':
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->saveReviewerAssignment();
        break;

    case '/admin/add-staff':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->addStaff();
        break;

    case '/admin/add-staff/store':
        require __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->storeStaff();
        break;

    case '/admin/settings':
        AuthMiddleware::requireRole('admin');
        require __DIR__ . '/../app/Views/admin/settings.php';
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

        if (preg_match('#^/officer/sample-size/input/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('sample_size_officer');
            $_GET['id'] = (int) $matches[1];
            require __DIR__ . '/../app/Controllers/SampleSizeController.php';
            (new SampleSizeController())->inputForm();
            break;
        }

        if (preg_match('#^/student/payment/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('student');
            $_GET['id'] = (int) $matches[1];
            require __DIR__ . '/../app/Controllers/PaymentController.php';
            (new PaymentController())->showPayment();
            break;
        }

        if (preg_match('#^/student/submission/revision/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('student');
            require __DIR__ . '/../app/Controllers/SubmissionController.php';
            (new SubmissionController())->submitRevision((int) $matches[1]);
            break;
        }

        if (preg_match('#^/committee/certificate/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('manager');
            require __DIR__ . '/../app/Controllers/CommitteeController.php';
            (new CommitteeController())->certificate((int) $matches[1]);
            break;
        }

        if (preg_match('#^/committee/certificates/delete/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole('manager');
            require __DIR__ . '/../app/Controllers/CommitteeController.php';
            (new CommitteeController())->deleteCertificate((int) $matches[1]);
            break;
        }

        if (preg_match('#^/certificate/download/(\d+)$#', $path, $matches)) {
            AuthMiddleware::requireRole(['manager', 'student']);
            require __DIR__ . '/../app/Controllers/CommitteeController.php';
            (new CommitteeController())->downloadCertificate((int) $matches[1]);
            break;
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found', 'path' => $path, 'base' => BASE_URL]);
        break;
}
