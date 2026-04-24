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
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $activationData = $this->adminService->getUserActivationData($page, 10);

        $pendingCount = $activationData['pendingCount'];
        $pendingUsers = $activationData['pendingUsers'];
        $userActivationPagination = $activationData['pagination'];

        $activationSuccessMessage = $_SESSION['admin_user_activation_success'] ?? null;
        $activationErrorMessage = $_SESSION['admin_user_activation_error'] ?? null;
        unset($_SESSION['admin_user_activation_success'], $_SESSION['admin_user_activation_error']);

        require __DIR__ . '/../Views/admin/user_activation.php';
    }

    public function activateUser() {
        AuthMiddleware::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        $page = max(1, (int) ($_POST['page'] ?? 1));
        if ($userId <= 0) {
            $_SESSION['admin_user_activation_error'] = 'معرف المستخدم غير صالح';
            $redirect = '/admin/user-activation';
            if ($page > 1) {
                $redirect .= '?page=' . $page;
            }
            header('Location: ' . $redirect);
            exit;
        }

        try {
            $this->adminService->activateUser($userId);
            $_SESSION['admin_user_activation_success'] = 'تم تنشيط الحساب بنجاح';
        } catch (Throwable $e) {
            $_SESSION['admin_user_activation_error'] = $e->getMessage();
        }

        $redirect = '/admin/user-activation';
        if ($page > 1) {
            $redirect .= '?page=' . $page;
        }

        header('Location: ' . $redirect);
        exit;
    }

    public function initialPreviewQueue() {
        AuthMiddleware::requireRole('admin');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $queueData = $this->adminService->getInitialPreviewQueueData($page, 10);

        $queueCount = $queueData['queueCount'];
        $queueItems = $queueData['queueItems'];
        $initialPreviewPagination = $queueData['pagination'];

        $serialSuccessMessage = $_SESSION['admin_serial_success'] ?? null;
        $serialErrorMessage = $_SESSION['admin_serial_error'] ?? null;
        unset($_SESSION['admin_serial_success'], $_SESSION['admin_serial_error']);

        require __DIR__ . '/../Views/admin/initial_preview_queue.php';
    }

    public function assignInitialPreviewSerial() {
        AuthMiddleware::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $serialInput = $_POST['serial_number'] ?? '';
        $page = max(1, (int) ($_POST['page'] ?? 1));

        try {
            $serialNumber = $this->adminService->assignInitialPreviewSerialNumber(
                $submissionId,
                $serialInput,
                isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null
            );
            $_SESSION['admin_serial_success'] = 'تم حفظ الرقم التسلسلي بنجاح: ' . $serialNumber;
        } catch (Throwable $e) {
            $_SESSION['admin_serial_error'] = $e->getMessage();
        }

        $redirect = '/admin/initial-preview-queue';
        if ($page > 1) {
            $redirect .= '?page=' . $page;
        }

        header('Location: ' . $redirect);
        exit;
    }

    public function reviewerAssignment() {
        AuthMiddleware::requireRole('admin');

        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $data = $this->adminService->getReviewerAssignmentData($search, $page, 10);

        $searchQuery = $data['search'];
        $reviewerAssignmentTotal = $data['totalCount'];
        $reviewerAssignmentRows = $data['submissions'];
        $reviewerOptions = $data['reviewers'];
        $reviewerAssignmentPagination = $data['pagination'];

        $reviewerAssignmentSuccess = $_SESSION['admin_reviewer_assignment_success'] ?? null;
        $reviewerAssignmentError = $_SESSION['admin_reviewer_assignment_error'] ?? null;
        unset($_SESSION['admin_reviewer_assignment_success'], $_SESSION['admin_reviewer_assignment_error']);

        require __DIR__ . '/../Views/admin/reviewer_assignment.php';
    }

    public function saveReviewerAssignment() {
        AuthMiddleware::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $reviewerId = (int) ($_POST['reviewer_id'] ?? 0);

        try {
            $mode = $this->adminService->assignReviewerToSubmission(
                $submissionId,
                $reviewerId,
                isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null
            );

            $_SESSION['admin_reviewer_assignment_success'] = $mode === 'created'
                ? 'تم حفظ تعيين المراجع بنجاح.'
                : 'تم تحديث تعيين المراجع بنجاح.';
        } catch (Throwable $e) {
            $_SESSION['admin_reviewer_assignment_error'] = $e->getMessage();
        }

        $redirect = '/admin/reviewer-assignment';
        $q = trim((string) ($_POST['q'] ?? ''));
        $page = max(1, (int) ($_POST['page'] ?? 1));

        $queryParams = [];
        if ($q !== '') {
            $queryParams['q'] = $q;
        }
        if ($page > 1) {
            $queryParams['page'] = $page;
        }

        if (!empty($queryParams)) {
            $redirect .= '?' . http_build_query($queryParams);
        }

        header('Location: ' . $redirect);
        exit;
    }
}
