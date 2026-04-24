<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'تفاصيل البحث - IRB Portal';

require_once __DIR__ . '/../../../config/database.php';
$db = Database::getConnection();

$studentId = (int) $_SESSION['user_id'];
$submissionId = (int) ($_GET['id'] ?? 0);

if ($submissionId <= 0) {
    $_SESSION['submission_error'] = 'المعرف غير صالح.';
    header('Location: ' . BASE_URL . '/student/submissions');
    exit;
}

$stmt = $db->prepare(
    "SELECT id, student_id, title, principal_investigator, co_investigators, serial_number, status, created_at, updated_at
     FROM research_submissions
     WHERE id = ?
     LIMIT 1"
);
$stmt->bind_param('i', $submissionId);
$stmt->execute();
$submissionResult = $stmt->get_result();
$submission = $submissionResult->fetch_assoc();

if (!$submission) {
    $_SESSION['submission_error'] = 'لم يتم العثور على البحث المطلوب.';
    header('Location: ' . BASE_URL . '/student/submissions');
    exit;
}

if ((int) $submission['student_id'] != (int) $studentId) {
    $_SESSION['submission_error'] = 'لا يمكنك الوصول إلى تفاصيل هذا البحث.';
    header('Location: ' . BASE_URL . '/student/submissions');
    exit;
}

$documentsStmt = $db->prepare(
    "SELECT document_type, file_path, uploaded_at
     FROM research_documents
     WHERE submission_id = ?
     ORDER BY uploaded_at DESC"
);
$documentsStmt->bind_param('i', $submissionId);
$documentsStmt->execute();
$documentsResult = $documentsStmt->get_result();

$documents = [];
while ($row = $documentsResult->fetch_assoc()) {
    $documents[] = $row;
}

$reviewsStmt = $db->prepare(
    "SELECT review_status, feedback_notes, assigned_at, reviewed_at
     FROM reviews
     WHERE submission_id = ?
     ORDER BY assigned_at DESC"
);
$reviewsStmt->bind_param('i', $submissionId);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();

$reviews = [];
while ($row = $reviewsResult->fetch_assoc()) {
    $reviews[] = $row;
}

$statusMap = [
    'submitted' => ['label' => 'تم التقديم', 'color' => 'bg-blue-100 text-blue-800'],
    'admin_reviewed' => ['label' => 'تمت المراجعة', 'color' => 'bg-yellow-100 text-yellow-800'],
    'initial_paid' => ['label' => 'تم الدفع الأولي', 'color' => 'bg-yellow-100 text-yellow-800'],
    'sample_sized' => ['label' => 'تم حساب العينة', 'color' => 'bg-yellow-100 text-yellow-800'],
    'fully_paid' => ['label' => 'تم الدفع الكامل', 'color' => 'bg-yellow-100 text-yellow-800'],
    'under_review' => ['label' => 'قيد المراجعة', 'color' => 'bg-orange-100 text-orange-800'],
    'revision_requested' => ['label' => 'مطلوب تعديل', 'color' => 'bg-red-100 text-red-800'],
    'approved' => ['label' => 'تمت الموافقة', 'color' => 'bg-green-100 text-green-800'],
    'rejected' => ['label' => 'مرفوض', 'color' => 'bg-red-100 text-red-800'],
];

$documentTypeMap = [
    'protocol' => 'البروتوكول',
    'review_application' => 'استمارة طلب المراجعة',
    'conflict_of_interest' => 'إقرار تضارب المصالح',
    'irb_checklist' => 'قائمة المراجعة',
    'pi_consent' => 'إقرار الموافقة من الباحث الرئيسي',
    'patient_consent' => 'إقرار موافقة المريض',
    'photos_biopsies_consent' => 'إقرار الموافقة على الصور والخزعات',
    'research_file' => 'ملف البحث',
];

$reviewStatusMap = [
    'pending' => ['label' => 'قيد المراجعة', 'color' => 'bg-slate-100 text-slate-700'],
    'approved' => ['label' => 'تمت الموافقة', 'color' => 'bg-green-100 text-green-800'],
    'revision_requested' => ['label' => 'مطلوب تعديل', 'color' => 'bg-red-100 text-red-800'],
    'rejected' => ['label' => 'مرفوض', 'color' => 'bg-red-100 text-red-800'],
];

$statusInfo = $statusMap[$submission['status']] ?? ['label' => $submission['status'], 'color' => 'bg-slate-100 text-slate-700'];
$coInvestigators = trim((string) ($submission['co_investigators'] ?? ''));
$serialNumber = $submission['serial_number'] ?: 'لم يُحدد بعد';

$timelineStages = [
    1 => 'التسجيل وتقديم البحث',
    2 => 'مراجعة الإدارة والتفعيل',
    3 => 'الدفع الأول',
    4 => 'حساب حجم العينة',
    5 => 'الدفع الثاني',
    6 => 'مراجعة المراجعين',
    7 => 'الإشعارات',
    8 => 'المراجعة النهائية',
    9 => 'إصدار الشهادة',
];

$timelineStatusMap = [
    'submitted' => 1,
    'admin_reviewed' => 2,
    'initial_paid' => 3,
    'sample_sized' => 4,
    'fully_paid' => 5,
    'under_review' => 6,
    'revision_requested' => 6,
    'approved' => 9,
    'rejected' => 6,
];

$currentTimelineStage = $timelineStatusMap[$submission['status']] ?? 1;

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => BASE_URL . '/student/dashboard', 'active' => false],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => BASE_URL . '/student/submissions', 'active' => true],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => BASE_URL . '/student/submission/create'],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => BASE_URL . '/student/settings'],
];

function formatDateTime($datetime) {
    return date('Y/m/d - h:i A', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <aside class="w-full lg:w-[260px] bg-white border-r border-slate-200 shadow-sm lg:shadow-none">
            <div class="p-5 border-b border-slate-200 flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500">
                    <span class="material-symbols-outlined text-3xl">account_balance</span>
                </div>
                <div>
                    <h1 class="font-h1 text-lg text-charcoal">IRB</h1>
                    <p class="text-sm text-slate-gray">بوابة الباحث</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <?php foreach ($sidebarItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-colors <?= !empty($item['active']) ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-gray hover:bg-slate-100 hover:text-charcoal' ?>">
                        <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-4 mt-auto border-t border-slate-200">
                <a href="<?php echo BASE_URL; ?>/logout"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-red-600 hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-sm text-slate-gray">أبحاثي</p>
                    <p class="text-xs text-slate-gray mb-1">تفاصيل البحث</p>
                    <h2 class="font-h1 text-2xl text-charcoal"><?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/student/submissions"
                   class="inline-flex items-center gap-2 bg-slate-100 text-charcoal px-4 py-2.5 rounded-lg font-button text-sm hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    العودة إلى أبحاثي
                </a>
            </header>

            <section class="px-4 md:px-8 py-6 space-y-6">
                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">معلومات البحث</h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">عنوان البحث</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">اسم الباحث الرئيسي</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars($submission['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">المشاركون في البحث</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars($coInvestigators !== '' ? $coInvestigators : 'لا يوجد', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">الرقم التسلسلي</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars($serialNumber, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">تاريخ التقديم</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars(formatDateTime($submission['created_at']), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-slate-gray mb-1">آخر تحديث</p>
                            <p class="font-button text-charcoal"><?= htmlspecialchars(formatDateTime($submission['updated_at']), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">الحالة الحالية</h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="inline-flex items-center px-5 py-2.5 rounded-full text-sm font-button <?= htmlspecialchars($statusInfo['color'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <?php if (in_array($submission['status'], ['admin_reviewed', 'sample_sized'])): ?>
                                <a href="<?php echo BASE_URL; ?>/student/payment/<?= (int) $submission['id'] ?>" 
                                   class="inline-flex items-center gap-2 bg-orange-500 text-white px-6 py-2.5 rounded-full font-button text-sm hover:bg-orange-600 transition-colors shadow-md">
                                    <span class="material-symbols-outlined">payments</span>
                                    <?= $submission['status'] === 'admin_reviewed' ? 'سداد الرسوم الأولية' : 'سداد رسوم العينة' ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-center">
                            <div class="relative max-w-[500px]">
                                <?php foreach ($timelineStages as $stageNumber => $stageLabel): ?>
                                    <?php
                                        $isCompleted = $stageNumber < $currentTimelineStage;
                                        $isCurrent = $stageNumber == $currentTimelineStage;
                                        $isUpcoming = $stageNumber > $currentTimelineStage;
                                        $isLastStage = $stageNumber == count($timelineStages);

                                        if ($isCompleted) {
                                            $circleClass = 'bg-green-100 text-green-700 border border-green-300';
                                            $labelClass = 'text-charcoal';
                                        } elseif ($isCurrent) {
                                            $circleClass = 'bg-blue-500 text-white';
                                            $labelClass = 'text-charcoal font-bold';
                                        } else {
                                            $circleClass = 'bg-slate-300 text-slate-600';
                                            $labelClass = 'text-slate-600';
                                        }
                                    ?>
                                    <div class="relative flex items-start gap-4 <?= !$isLastStage ? 'pb-12' : '' ?>">
                                        <?php if (!$isLastStage): ?>
                                            <div class="absolute right-4 top-8 bottom-0 w-px <?= $isCompleted ? 'bg-green-500' : 'bg-slate-300' ?>"></div>
                                        <?php endif; ?>
                                        <div class="relative z-10">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center <?= htmlspecialchars($circleClass, ENT_QUOTES, 'UTF-8') ?>">
                                                <span class="text-sm font-bold"><?= (int) $stageNumber ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 pt-1 text-right">
                                            <div class="text-sm font-button <?= htmlspecialchars($labelClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($stageLabel, ENT_QUOTES, 'UTF-8') ?></div>
                                            <?php if ($isCurrent): ?>
                                                <div class="text-xs text-blue-600 mt-1">المرحلة الحالية</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">المستندات المرفوعة</h3>
                    </div>
                    <?php if (empty($documents)): ?>
                        <div class="p-6 text-sm text-slate-gray">لا توجد مستندات مرفوعة لهذا البحث.</div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">نوع المستند</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">تاريخ الرفع</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">تحميل</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($documents as $document): ?>
                                        <?php
                                            $documentType = $documentTypeMap[$document['document_type']] ?? $document['document_type'];
                                            $downloadPath = BASE_URL . '/storage/' . ltrim($document['file_path'], '/');
                                        ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-4 text-sm text-charcoal"><?= htmlspecialchars($documentType, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="px-5 py-4 text-sm text-slate-gray"><?= htmlspecialchars(formatDateTime($document['uploaded_at']), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="px-5 py-4">
                                                <a href="<?= htmlspecialchars($downloadPath, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="inline-flex items-center gap-1 text-sm font-button text-primary hover:underline">
                                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                                    تنزيل
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">ملاحظات المراجعة</h3>
                    </div>
                    <?php if (empty($reviews)): ?>
                        <div class="p-6 text-sm text-slate-gray">لم تتم المراجعة بعد</div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($reviews as $review): ?>
                                <?php
                                    $reviewStatus = $review['review_status'];
                                    if ($reviewStatus === 'modification_requested') {
                                        $reviewStatus = 'revision_requested';
                                    }
                                    $reviewInfo = $reviewStatusMap[$reviewStatus] ?? ['label' => $reviewStatus, 'color' => 'bg-slate-100 text-slate-700'];
                                    $showFeedback = in_array($reviewStatus, ['revision_requested', 'rejected']);
                                ?>
                                <div class="p-5 space-y-3">
                                    <div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-button <?= htmlspecialchars($reviewInfo['color'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($reviewInfo['label'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                    <?php if ($showFeedback): ?>
                                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-charcoal leading-7">
                                            <?= nl2br(htmlspecialchars($review['feedback_notes'] ?: 'لا توجد ملاحظات.', ENT_QUOTES, 'UTF-8')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
