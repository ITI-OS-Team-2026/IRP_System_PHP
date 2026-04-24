<?php

require __DIR__ . '/../Services/AdminService.php';

class AdminController {
    private $adminService;

    public function __construct() {
        $this->adminService = new AdminService();
    }

    public function dashboard() {
        AuthMiddleware::requireRole('admin');
        $dashboardData = $this->adminService->getDashboardData();

        $summaryCards = $dashboardData['summaryCards'];
        $activityItems = $dashboardData['activityItems'];
        $quickMetrics = $dashboardData['quickMetrics'];

        require __DIR__ . '/../Views/admin/admin_dashboard.php';
    }

    public function userActivation() {
        AuthMiddleware::requireRole('admin');
        $activationData = $this->adminService->getUserActivationData();

        $pendingCount = $activationData['pendingCount'];
        $pendingUsers = $activationData['pendingUsers'];

        require __DIR__ . '/../Views/admin/user_activation.php';
    }

    public function activateUser() {
        AuthMiddleware::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $_SESSION['admin_user_activation_error'] = 'معرف المستخدم غير صالح';
            header('Location: /admin/user-activation');
            exit;
        }

        try {
            $this->adminService->activateUser($userId);
            $_SESSION['admin_user_activation_success'] = 'تم تنشيط الحساب بنجاح';
        } catch (Throwable $e) {
            $_SESSION['admin_user_activation_error'] = $e->getMessage();
        }

        header('Location: /admin/user-activation');
        exit;
    }
}
