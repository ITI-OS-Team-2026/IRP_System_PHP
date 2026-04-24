<?php

require __DIR__ . '/../Repositories/AdminRepository.php';

class AdminService {
    private $adminRepository;

    public function __construct() {
        $this->adminRepository = new AdminRepository();
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

    public function getUserActivationData() {
        $users = $this->adminRepository->getPendingStudentUsers(25);

        $pendingUsers = [];
        foreach ($users as $user) {
            $pendingUsers[] = [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'department' => $this->buildDepartmentLabel($user),
                'created_at' => $this->formatDate($user['created_at'] ?? null),
                'submission_count' => (int) ($user['submission_count'] ?? 0),
                'document_count' => (int) ($user['document_count'] ?? 0),
                'status_label' => $this->buildActivationStatusLabel($user),
                'status_class' => $this->buildActivationStatusClass($user),
                'can_activate' => true,
            ];
        }

        return [
            'pendingCount' => count($pendingUsers),
            'pendingUsers' => $pendingUsers,
        ];
    }

    public function activateUser($userId) {
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
    }

    public function getInitialPreviewQueueData() {
        $rows = $this->adminRepository->getInitialPreviewQueueSubmissions(25);

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
            'queueCount' => count($items),
            'queueItems' => $items,
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
}
