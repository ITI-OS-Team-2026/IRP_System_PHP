<?php

require __DIR__ . '/../Repositories/AdminRepository.php';
require __DIR__ . '/MailService.php';

class AdminService {
    private $adminRepository;
    private $mailService;

    private $staffRoleOptions = [
        'reviewer' => 'مراجع',
        'sample_size_officer' => 'مسؤول حجم العينة',
        'manager' => 'مدير',
    ];

    public function __construct() {
        $this->adminRepository = new AdminRepository();
        $this->mailService = new MailService();
    }

    public function getDashboardData() {
        $summary = $this->adminRepository->getDashboardSummary();

        $summaryCards = [
            [
                'count' => (string) $summary['pendingActivations'],
                'title' => 'حسابات قيد التنشيط',
                'description' => 'باحثون جدد ينتظرون التحقق من وثائقهم الأكاديمية والموافقة على حساباتهم.',
                'action' => 'مراجعة الحسابات',
                'icon' => 'person_add',
                'accent' => 'bg-primary',
                'buttonStyle' => 'bg-primary text-on-primary hover:bg-primary-container',
                'href' => '/admin/user-activation',
            ],
            [
                'count' => (string) $summary['serialQueue'],
                'title' => 'طلبات بانتظار الرقم التسلسلي',
                'description' => 'دراسات استوفت الشروط الأولية وتنتظر إصدار رقم تسلسلي ورمز اعتماد.',
                'action' => 'إصدار الأرقام',
                'icon' => '123',
                'accent' => 'bg-surface-container-low',
                'buttonStyle' => 'border border-charcoal text-charcoal hover:bg-surface-container',
                'href' => '/admin/initial-preview-queue',
            ],
            [
                'count' => (string) $summary['readyForReviewerAssignment'],
                'title' => 'طلبات جاهزة للمراجعين',
                'description' => 'مقترحات مكتملة من الناحية الإجرائية ويمكن توزيعها على المراجعين المختصين.',
                'action' => 'تعيين المراجعين',
                'icon' => 'badge',
                'accent' => 'bg-surface-container-low',
                'buttonStyle' => 'border border-charcoal text-charcoal hover:bg-surface-container',
                'href' => '/admin/reviewer-assignment',
            ],
        ];

        $activityItems = [];
        foreach ($summary['recentActivity'] as $activity) {
            $activityItems[] = [
                'label' => $this->mapActivityLabel($activity['action'] ?? ''),
                'title' => $this->buildActivityTitle($activity),
                'time' => $this->formatRelativeTime($activity['created_at'] ?? null),
            ];
        }

        $quickMetrics = [
            [
                'label' => 'الحسابات المفعلة',
                'value' => $this->toPercent($summary['activeUsers'], $summary['activeUsers'] + $summary['pendingActivations']),
            ],
            [
                'label' => 'الطلبات المكتملة',
                'value' => $this->toPercent($summary['completedSubmissions'], max($summary['totalSubmissions'], 1)),
            ],
            [
                'label' => 'متوسط زمن المراجعة',
                'value' => $summary['averageReviewTimeDays'] !== null ? number_format((float) $summary['averageReviewTimeDays'], 0) . ' يوم' : 'لا توجد بيانات',
            ],
        ];

        return [
            'summaryCards' => $summaryCards,
            'activityItems' => $activityItems,
            'quickMetrics' => $quickMetrics,
        ];
    }

    public function getUserActivationData($page = 1, $perPage = 10) {
        $perPage = max(1, min(50, (int) $perPage));
        $requestedPage = max(1, (int) $page);

        $total = $this->adminRepository->countPendingStudentUsers();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($requestedPage, $lastPage);
        $offset = ($currentPage - 1) * $perPage;

        $users = $this->adminRepository->getPendingStudentUsers($perPage, $offset);

        $pendingUsers = [];
        foreach ($users as $user) {
            $pendingUsers[] = [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'department' => $this->buildDepartmentLabel($user),
                'created_at' => $this->formatDate($user['created_at'] ?? null),
                'id_front_path' => (string) ($user['id_front_path'] ?? ''),
                'id_back_path' => (string) ($user['id_back_path'] ?? ''),
                'submission_count' => (int) ($user['submission_count'] ?? 0),
                'document_count' => (int) ($user['document_count'] ?? 0),
                'status_label' => $this->buildActivationStatusLabel($user),
                'status_class' => $this->buildActivationStatusClass($user),
                'can_activate' => true,
            ];
        }

        return [
            'pendingCount' => $total,
            'pendingUsers' => $pendingUsers,
            'pagination' => [
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'lastPage' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + count($pendingUsers), $total),
                'hasPrevious' => $currentPage > 1,
                'hasNext' => $currentPage < $lastPage,
                'previousPage' => max(1, $currentPage - 1),
                'nextPage' => min($lastPage, $currentPage + 1),
            ],
        ];
    }

    public function activateUser($userId) {
        $user = $this->adminRepository->findUserById($userId);
        if (!$user || ($user['role'] ?? '') !== 'student') {
            throw new Exception('لم يتم العثور على الحساب المطلوب تنشيطه.');
        }

        $activated = $this->adminRepository->activateStudentUser($userId);

        if (!$activated) {
            throw new Exception('لم يتم العثور على الحساب المطلوب تنشيطه.');
        }

        $this->adminRepository->logAction(
            'user_activated',
            'تم تنشيط حساب الباحث رقم ' . (int) $userId,
            (int) $userId,
            null
        );

        try {
            $this->mailService->sendTemplate('user_activated', [
                'email' => (string) ($user['email'] ?? ''),
                'name' => (string) ($user['full_name'] ?? ''),
            ], [
                'login_url' => $this->buildLoginUrl(),
            ]);
        } catch (Throwable $mailException) {
            error_log('Activation email failed for user ' . (int) $userId . ': ' . $mailException->getMessage());
        }
    }

    public function refuseUser($userId, $reason) {
        $user = $this->adminRepository->findUserById($userId);
        if (!$user || ($user['role'] ?? '') !== 'student') {
            throw new Exception('لم يتم العثور على الحساب المطلوب رفضه.');
        }

        $deleted = $this->adminRepository->deleteStudentUser($userId);

        if (!$deleted) {
            throw new Exception('لم يتم العثور على الحساب المطلوب رفضه أو تم حذفه مسبقاً.');
        }

        $this->adminRepository->logAction(
            'user_refused',
            'تم رفض وحذف حساب الباحث رقم ' . (int) $userId,
            isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
            null
        );

        try {
            $this->mailService->sendTemplate('user_refused', [
                'email' => (string) ($user['email'] ?? ''),
                'name' => (string) ($user['full_name'] ?? ''),
            ], [
                'login_url' => $this->buildLoginUrl() !== '/login' ? str_replace('/login', '/register', $this->buildLoginUrl()) : '/register',
                'reason' => $reason
            ]);
        } catch (Throwable $mailException) {
            error_log('Refusal email failed for user ' . (int) $userId . ': ' . $mailException->getMessage());
        }
    }

    public function getInitialPreviewQueueData($page = 1, $perPage = 10) {
        $perPage = max(1, min(50, (int) $perPage));
        $requestedPage = max(1, (int) $page);

        $total = $this->adminRepository->countInitialPreviewQueueSubmissions();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($requestedPage, $lastPage);
        $offset = ($currentPage - 1) * $perPage;

        $rows = $this->adminRepository->getInitialPreviewQueueSubmissions($perPage, $offset);

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => (int) $row['id'],
                'researcher_name' => (string) $row['full_name'],
                'researcher_department' => $this->buildDepartmentLabel($row),
                'title' => (string) $row['title'],
                'created_at' => $this->formatDate($row['created_at'] ?? null),
            ];
        }

        return [
            'queueCount' => $total,
            'queueItems' => $items,
            'pagination' => [
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'lastPage' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + count($items), $total),
                'hasPrevious' => $currentPage > 1,
                'hasNext' => $currentPage < $lastPage,
                'previousPage' => max(1, $currentPage - 1),
                'nextPage' => min($lastPage, $currentPage + 1),
            ],
        ];
    }

    public function assignInitialPreviewSerialNumber($submissionId, $serialInput, $adminUserId = null) {
        $submissionId = (int) $submissionId;
        if ($submissionId <= 0) {
            throw new Exception('معرف الطلب غير صالح.');
        }

        $serialNumber = $this->normalizeSerialNumber($serialInput);
        if ($serialNumber === '') {
            $serialNumber = $this->generateUniqueSerialNumber();
        }

        if ($this->adminRepository->serialNumberExists($serialNumber)) {
            throw new Exception('الرقم التسلسلي مستخدم بالفعل. يرجى إدخال رقم مختلف.');
        }

        $updated = $this->adminRepository->assignSerialNumberToSubmission($submissionId, $serialNumber);
        if (!$updated) {
            throw new Exception('تعذر تحديث الطلب. ربما تم تحديثه مسبقاً بواسطة مسؤول آخر.');
        }

        $this->adminRepository->logAction(
            'serial_number_assigned',
            'تم إصدار رقم تسلسلي للطلب: ' . $serialNumber,
            $adminUserId !== null ? (int) $adminUserId : null,
            $submissionId
        );

        return $serialNumber;
    }

    public function getReviewerAssignmentData($search = '', $page = 1, $perPage = 10) {
        $search = trim((string) $search);

        $perPage = max(1, min(50, (int) $perPage));
        $requestedPage = max(1, (int) $page);
        $total = $this->adminRepository->countReviewerAssignmentSubmissions($search);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($requestedPage, $lastPage);
        $offset = ($currentPage - 1) * $perPage;

        $submissions = $this->adminRepository->getReviewerAssignmentSubmissions($search, $perPage, $offset);
        $reviewers = $this->adminRepository->getReviewers();

        $normalizedSubmissions = [];
        foreach ($submissions as $row) {
            $normalizedSubmissions[] = [
                'id' => (int) $row['id'],
                'serial_number' => (string) $row['serial_number'],
                'title' => (string) $row['title'],
                'payment_status_label' => 'مدفوع',
                'reviewer_id' => $row['reviewer_id'] !== null ? (int) $row['reviewer_id'] : null,
                'reviewer_name' => (string) ($row['reviewer_name'] ?? ''),
                'reviewer_specialty' => (string) ($row['reviewer_specialty'] ?? ''),
            ];
        }

        $normalizedReviewers = [];
        foreach ($reviewers as $reviewer) {
            $normalizedReviewers[] = [
                'id' => (int) $reviewer['id'],
                'label' => $this->buildReviewerLabel($reviewer),
            ];
        }

        return [
            'search' => $search,
            'totalCount' => $total,
            'submissions' => $normalizedSubmissions,
            'reviewers' => $normalizedReviewers,
            'pagination' => [
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'lastPage' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + count($normalizedSubmissions), $total),
                'hasPrevious' => $currentPage > 1,
                'hasNext' => $currentPage < $lastPage,
                'previousPage' => max(1, $currentPage - 1),
                'nextPage' => min($lastPage, $currentPage + 1),
            ],
        ];
    }

    public function assignReviewerToSubmission($submissionId, $reviewerId, $adminUserId = null) {
        $submissionId = (int) $submissionId;
        $reviewerId = (int) $reviewerId;

        if ($submissionId <= 0 || $reviewerId <= 0) {
            throw new Exception('بيانات التعيين غير صالحة.');
        }

        if (!$this->adminRepository->submissionExistsForReviewerAssignment($submissionId)) {
            throw new Exception('هذا البحث غير متاح حالياً للتعيين.');
        }

        if (!$this->adminRepository->reviewerExists($reviewerId)) {
            throw new Exception('المراجع المختار غير متاح.');
        }

        $assignedReviewerId = $this->adminRepository->getAssignedReviewerIdForSubmission($submissionId);
        if ($assignedReviewerId !== null && $assignedReviewerId === $reviewerId) {
            throw new Exception('هذا المراجع معين بالفعل لهذا البحث.');
        }

        $mode = $this->adminRepository->upsertReviewerAssignment($submissionId, $reviewerId);
        $action = $mode === 'created' ? 'reviewer_assigned' : 'reviewer_reassigned';

        $this->adminRepository->logAction(
            $action,
            'تم تعيين المراجع رقم ' . $reviewerId . ' للطلب رقم ' . $submissionId,
            $adminUserId !== null ? (int) $adminUserId : null,
            $submissionId
        );

        return $mode;
    }

    public function getAddStaffData() {
        return [
            'staffRoleOptions' => $this->staffRoleOptions,
        ];
    }

    public function createStaffAccount(array $data, $adminUserId = null) {
        $fullName = preg_replace('/\s+/u', ' ', trim((string) ($data['full_name'] ?? '')));
        $emailRaw = trim((string) ($data['email'] ?? ''));
        $email = function_exists('mb_strtolower') ? mb_strtolower($emailRaw, 'UTF-8') : strtolower($emailRaw);
        $phoneNumber = trim((string) ($data['phone_number'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $role = trim((string) ($data['role'] ?? ''));

        if ($fullName === '') {
            throw new Exception('اسم الموظف مطلوب.');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('البريد الإلكتروني غير صالح.');
        }

        if ($phoneNumber === '') {
            throw new Exception('رقم الهاتف مطلوب.');
        }

        if (!$this->isStrongPassword($password)) {
            throw new Exception('كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل وحرف كبير وحرف صغير ورقم ورمز خاص.');
        }

        if (!array_key_exists($role, $this->staffRoleOptions)) {
            throw new Exception('الوظيفة المختارة غير صالحة.');
        }

        if ($this->adminRepository->findUserByFullName($fullName)) {
            throw new Exception('الاسم الكامل مستخدم بالفعل.');
        }

        if ($this->adminRepository->findUserByEmail($email)) {
            throw new Exception('هذا البريد الإلكتروني مستخدم بالفعل.');
        }

        $userId = $this->adminRepository->createStaffUser($fullName, $email, $phoneNumber, $password, $role);

        $this->adminRepository->logAction(
            'staff_registered',
            'تم تسجيل موظف جديد: ' . $fullName . ' (' . $role . ')',
            $adminUserId !== null ? (int) $adminUserId : null,
            null
        );

        try {
            $this->mailService->sendTemplate('staff_account_created', [
                'email' => $email,
                'name' => $fullName,
            ], [
                'login_url' => $this->buildLoginUrl(),
                'role_label' => $this->staffRoleOptions[$role] ?? $role,
            ]);
        } catch (Throwable $mailException) {
            error_log('Staff account email failed for ' . $email . ': ' . $mailException->getMessage());
        }

        return $userId;
    }

    private function buildDepartmentLabel(array $user) {
        $department = trim((string) ($user['department'] ?? ''));
        $specialty = trim((string) ($user['specialty'] ?? ''));

        if ($department !== '' && $specialty !== '') {
            return $department . ' / ' . $specialty;
        }

        if ($department !== '') {
            return $department;
        }

        if ($specialty !== '') {
            return $specialty;
        }

        return 'غير محدد';
    }

    private function buildActivationStatusLabel(array $user) {
        $submissionCount = (int) ($user['submission_count'] ?? 0);
        $documentCount = (int) ($user['document_count'] ?? 0);

        if ($submissionCount === 0) {
            return 'قيد المراجعة';
        }

        if ($documentCount === 0) {
            return 'مستندات ناقصة';
        }

        return 'قيد المراجعة';
    }

    private function buildActivationStatusClass(array $user) {
        $submissionCount = (int) ($user['submission_count'] ?? 0);
        $documentCount = (int) ($user['document_count'] ?? 0);

        if ($submissionCount > 0 && $documentCount === 0) {
            return 'border-charcoal text-charcoal bg-paper-white';
        }

        return 'border-charcoal text-charcoal bg-paper-white';
    }

    private function formatDate($timestamp) {
        if (!$timestamp) {
            return 'غير محدد';
        }

        return date('Y-m-d', strtotime($timestamp));
    }

    private function normalizeSerialNumber($serialInput) {
        $serial = strtoupper(trim((string) $serialInput));
        if ($serial === '') {
            return '';
        }

        if (!preg_match('/^IRB-\d{4}-\d{4}$/', $serial)) {
            throw new Exception('صيغة الرقم التسلسلي غير صحيحة. استخدم الصيغة: IRB-YYYY-0001');
        }

        return $serial;
    }

    private function generateUniqueSerialNumber() {
        $year = date('Y');
        $sequence = $this->adminRepository->getMaxSerialSequenceByYear($year) + 1;

        while ($sequence < 999999) {
            $candidate = 'IRB-' . $year . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            if (!$this->adminRepository->serialNumberExists($candidate)) {
                return $candidate;
            }
            $sequence++;
        }

        throw new Exception('تعذر إنشاء رقم تسلسلي فريد. يرجى المحاولة لاحقاً.');
    }

    private function buildReviewerLabel(array $reviewer) {
        $name = trim((string) ($reviewer['full_name'] ?? ''));
        $specialty = trim((string) ($reviewer['specialty'] ?? ''));

        if ($specialty !== '') {
            return $name . ' (' . $specialty . ')';
        }

        return $name;
    }

    private function mapActivityLabel($action) {
        $labels = [
            'research_submitted' => 'تقديم جديد',
            'user_activated' => 'حساب مفعل',
            'serial_number_assigned' => 'إصدار رقم',
            'reviewer_assigned' => 'تعيين مراجع',
        ];

        return $labels[$action] ?? 'تحديث';
    }

    private function buildActivityTitle(array $activity) {
        $details = trim((string) ($activity['details'] ?? ''));
        if ($details !== '') {
            return $details;
        }

        $actor = trim((string) ($activity['full_name'] ?? ''));
        $serialNumber = trim((string) ($activity['serial_number'] ?? ''));

        if ($actor !== '' && $serialNumber !== '') {
            return $actor . ' على البحث ' . $serialNumber;
        }

        if ($actor !== '') {
            return $actor;
        }

        return 'نشاط إداري حديث';
    }

    private function formatRelativeTime($timestamp) {
        if (!$timestamp) {
            return 'قبل قليل';
        }

        $time = strtotime($timestamp);
        if ($time === false) {
            return 'قبل قليل';
        }

        $diff = time() - $time;
        if ($diff < 60) {
            return 'قبل لحظات';
        }
        if ($diff < 3600) {
            return 'قبل ' . max(1, (int) floor($diff / 60)) . ' دقيقة';
        }
        if ($diff < 86400) {
            return 'قبل ' . max(1, (int) floor($diff / 3600)) . ' ساعة';
        }

        return date('d/m', $time);
    }

    private function toPercent($part, $whole) {
        $whole = max((int) $whole, 1);
        $part = (int) $part;
        return (int) round(($part / $whole) * 100) . '%';
    }

    private function buildLoginUrl() {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $baseUrl = rtrim($baseUrl, '/');

        return $baseUrl === '' ? '/login' : $baseUrl . '/login';
    }

    private function isStrongPassword($password) {
        if (strlen($password) < 8) {
            return false;
        }

        return preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }
}
