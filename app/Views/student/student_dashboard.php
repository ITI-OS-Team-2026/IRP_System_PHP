<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'لوحة تحكم الباحث - IRB Portal';

require_once __DIR__ . '/../../../config/database.php';
$db = Database::getConnection();

$studentId = (int) $_SESSION['user_id'];
$hasCertificatesTableResult = $db->query("SHOW TABLES LIKE 'certificates'");
$hasCertificatesTable = $hasCertificatesTableResult instanceof mysqli_result && $hasCertificatesTableResult->num_rows > 0;

// Summary counts
$totalResult = $db->query("SELECT COUNT(*) as cnt FROM research_submissions WHERE student_id = $studentId");
$totalSubmissions = $totalResult->fetch_assoc()['cnt'];

$pendingResult = $db->query("SELECT COUNT(*) as cnt FROM research_submissions WHERE student_id = $studentId AND status IN ('submitted', 'admin_reviewed', 'initial_paid', 'sample_sized', 'fully_paid', 'under_review')");
$pendingCount = $pendingResult->fetch_assoc()['cnt'];

$approvedResult = $db->query("SELECT COUNT(*) as cnt FROM research_submissions WHERE student_id = $studentId AND status = 'approved'");
$approvedCount = $approvedResult->fetch_assoc()['cnt'];

$rejectedResult = $db->query("SELECT COUNT(*) as cnt FROM research_submissions WHERE student_id = $studentId AND status = 'rejected'");
$rejectedCount = $rejectedResult->fetch_assoc()['cnt'];

// Submissions list
if ($hasCertificatesTable) {
    $submissionsResult = $db->query(
        "SELECT rs.id, rs.title, rs.status, rs.serial_number, rs.created_at, rs.updated_at,
                c.certificate_number
         FROM research_submissions rs
         LEFT JOIN certificates c ON c.submission_id = rs.id
         WHERE rs.student_id = $studentId
         ORDER BY rs.created_at DESC"
    );
} else {
    $submissionsResult = $db->query(
        "SELECT rs.id, rs.title, rs.status, rs.serial_number, rs.created_at, rs.updated_at,
                NULL AS certificate_number
         FROM research_submissions rs
         WHERE rs.student_id = $studentId
         ORDER BY rs.created_at DESC"
    );
}
$submissions = [];
while ($row = $submissionsResult->fetch_assoc()) {
    $submissions[] = $row;
}

// Notifications from system_logs (latest 5 for this student)
$logsResult = $db->query("SELECT sl.action, sl.details, sl.created_at, rs.title as submission_title
    FROM system_logs sl
    LEFT JOIN research_submissions rs ON sl.submission_id = rs.id
    WHERE sl.user_id = $studentId
    ORDER BY sl.created_at DESC LIMIT 5");
$notifications = [];
while ($row = $logsResult->fetch_assoc()) {
    $notifications[] = $row;
}

$statusMap = [
    'submitted'          => ['label' => 'تم التقديم',             'color' => 'bg-blue-100 text-blue-800'],
    'admin_reviewed'     => ['label' => 'تمت مراجعة الإدارة',     'color' => 'bg-indigo-100 text-indigo-800'],
    'initial_paid'       => ['label' => 'تم سداد الرسوم الأولية', 'color' => 'bg-cyan-100 text-cyan-800'],
    'sample_sized'       => ['label' => 'تم حساب حجم العينة',     'color' => 'bg-purple-100 text-purple-800'],
    'fully_paid'         => ['label' => 'تم السداد بالكامل',      'color' => 'bg-teal-100 text-teal-800'],
    'under_review'       => ['label' => 'قيد المراجعة',           'color' => 'bg-yellow-100 text-yellow-800'],
    'revision_requested' => ['label' => 'مطلوب تعديل',            'color' => 'bg-orange-100 text-orange-800'],
    'approved'           => ['label' => 'تمت الموافقة',           'color' => 'bg-green-100 text-green-800'],
    'rejected'           => ['label' => 'مرفوض',                  'color' => 'bg-red-100 text-red-800'],
];

$actionLabels = [
    'research_submitted'   => 'تم تقديم البحث',
    'status_updated'       => 'تم تحديث الحالة',
    'admin_reviewed'       => 'تمت مراجعة البحث من الإدارة',
    'serial_number_issued' => 'تم إصدار الرقم التسلسلي',
    'payment_confirmed'    => 'تم تأكيد الدفع',
    'payment_success'      => 'تم الدفع بنجاح',
    'sample_size_recorded' => 'تم تسجيل حجم العينة',
    'reviewer_assigned'    => 'تم تعيين مراجع',
    'revision_requested'   => 'مطلوب تعديل على البحث',
    'research_approved'    => 'تمت الموافقة على البحث',
    'research_rejected'    => 'تم رفض البحث',
    'certificate_issued'   => 'تم إصدار الشهادة',
];

// Unread count: notifications created after the last time the bell was dismissed.
// Stored in session to survive page refreshes; JS updates it on bell click.
$notifLastSeen = $_SESSION['notif_last_seen'] ?? null;
$unreadCount = 0;
if (!empty($notifications)) {
    foreach ($notifications as $n) {
        if ($notifLastSeen === null || strtotime($n['created_at']) > strtotime($notifLastSeen)) {
            $unreadCount++;
        }
    }
}

$summaryCards = [
    [
        'count' => $totalSubmissions,
        'title' => 'إجمالي الأبحاث',
        'description' => 'عدد جميع الأبحاث المقدمة في النظام.',
        'icon' => 'description',
        'accent' => 'bg-primary',
    ],
    [
        'count' => $pendingCount,
        'title' => 'قيد المعالجة',
        'description' => 'أبحاث لا تزال في مراحل المراجعة أو الدفع.',
        'icon' => 'pending_actions',
        'accent' => 'bg-yellow-500',
    ],
    [
        'count' => $approvedCount,
        'title' => 'تمت الموافقة',
        'description' => 'أبحاث حصلت على الموافقة النهائية من اللجنة.',
        'icon' => 'check_circle',
        'accent' => 'bg-green-600',
    ],
    [
        'count' => $rejectedCount,
        'title' => 'مرفوضة',
        'description' => 'أبحاث تم رفضها من قبل اللجنة.',
        'icon' => 'cancel',
        'accent' => 'bg-red-600',
    ],
];

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => BASE_URL . '/student/dashboard', 'active' => true],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => BASE_URL . '/student/submissions'],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => BASE_URL . '/student/submission/create'],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => BASE_URL . '/student/settings'],
];

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d >= 1) return 'قبل ' . $diff->d . ' يوم';
    if ($diff->h >= 1) return 'قبل ' . $diff->h . ' ساعة';
    if ($diff->i >= 1) return 'قبل ' . $diff->i . ' دقيقة';
    return 'الآن';
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Top Header -->
            <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-sm text-slate-gray">لوحة تحكم الباحث</p>
                    <h2 class="font-h1 text-2xl text-charcoal">مرحباً، <?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo BASE_URL; ?>/student/submission/create"
                       class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">note_add</span>
                        تقديم بحث جديد
                    </a>

                    <!-- ═══ Notification Bell ═══ -->
                    <div class="relative" id="notif-wrapper">
                        <button id="notif-bell-btn"
                                type="button"
                                aria-label="الإشعارات"
                                aria-expanded="false"
                                aria-controls="notif-dropdown"
                                class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200 relative hover:bg-slate-200 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            <?php if ($unreadCount > 0): ?>
                            <span id="notif-badge"
                                  class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center pointer-events-none">
                                <?= $unreadCount ?>
                            </span>
                            <?php else: ?>
                            <span id="notif-badge"
                                  class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full items-center justify-center pointer-events-none hidden">
                                0
                            </span>
                            <?php endif; ?>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notif-dropdown"
                             role="dialog"
                             aria-label="الإشعارات"
                             class="hidden absolute left-0 top-full mt-2 w-80 bg-white rounded-xl shadow-2xl border border-slate-200 z-50 overflow-hidden origin-top-right"
                             style="animation: notifFadeIn 0.15s ease;">

                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                                <span class="font-bold text-charcoal text-sm">الإشعارات</span>
                                <button id="notif-mark-read-btn"
                                        type="button"
                                        class="text-xs text-primary hover:underline transition-colors">
                                    تحديد الكل كمقروء
                                </button>
                            </div>

                            <!-- List -->
                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto" id="notif-list">
                                <?php if (empty($notifications)): ?>
                                    <div class="p-6 text-center">
                                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">notifications_off</span>
                                        <p class="text-sm text-slate-gray">لا توجد إشعارات حالياً</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notif): ?>
                                        <?php
                                            $nAction      = $notif['action'] ?? '';
                                            $nLabel       = $actionLabels[$nAction] ?? $nAction;
                                            $isPaymentN   = str_starts_with($nAction, 'payment');
                                            $dotClass     = $isPaymentN ? 'bg-emerald-500' : 'bg-primary';
                                            $iconBg       = $isPaymentN ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-primary';
                                            $nIcon        = $isPaymentN ? 'payments' : 'notifications';
                                        ?>
                                        <div class="notif-item px-4 py-3 hover:bg-slate-50 transition-colors cursor-default"
                                             data-time="<?= htmlspecialchars($notif['created_at'], ENT_QUOTES, 'UTF-8') ?>">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-full <?= $iconBg ?> flex items-center justify-center shrink-0 mt-0.5">
                                                    <span class="material-symbols-outlined text-[15px]"><?= $nIcon ?></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-charcoal leading-5"><?= htmlspecialchars($nLabel, ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-xs text-slate-gray mt-0.5 leading-5"><?= htmlspecialchars($notif['details'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                                    <?php if (!empty($notif['submission_title'])): ?>
                                                        <p class="text-[10px] text-slate-400 mt-0.5 truncate"><?= htmlspecialchars($notif['submission_title'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <?php endif; ?>
                                                    <p class="text-[10px] text-slate-400 mt-1"><?= timeAgo($notif['created_at']) ?></p>
                                                </div>
                                                <span class="notif-unread-dot w-2 h-2 rounded-full <?= $dotClass ?> shrink-0 mt-2 transition-opacity"></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Footer -->
                            <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 text-center">
                                <span class="text-xs text-slate-400">آخر <?= count($notifications) ?> إشعارات</span>
                            </div>
                        </div><!-- /notif-dropdown -->
                    </div><!-- /notif-wrapper -->
                </div>
            </header>

            <section class="px-4 md:px-8 py-6 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <?php foreach ($summaryCards as $card): ?>
                        <article class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] p-5 flex flex-col gap-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-3xl font-bold text-charcoal"><?= (int)$card['count'] ?></div>
                                <div class="w-10 h-10 rounded-lg <?= htmlspecialchars($card['accent'], ENT_QUOTES, 'UTF-8') ?> text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-h1 text-lg text-charcoal"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="text-sm leading-7 text-slate-gray"><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Submissions Table -->
                    <div class="lg:col-span-2 rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="font-h1 text-lg text-charcoal">أبحاثي</h3>
                            <span class="text-sm text-slate-gray"><?= (int)$totalSubmissions ?> بحث</span>
                        </div>

                        <?php if (empty($submissions)): ?>
                            <div class="p-8 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-300 mb-4 block">science</span>
                                <p class="text-slate-gray text-lg mb-2">لا توجد أبحاث حتى الآن</p>
                                <p class="text-sm text-slate-400 mb-6">ابدأ بتقديم أول بحث لك عبر الزر</p>
                                <a href="<?php echo BASE_URL; ?>/student/submission/create"
                                   class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">note_add</span>
                                    تقديم بحث جديد
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-right">
                                    <thead class="bg-slate-50 border-b border-slate-200">
                                        <tr>
                                            <th class="px-5 py-3 text-sm font-button text-slate-gray">عنوان البحث</th>
                                            <th class="px-5 py-3 text-sm font-button text-slate-gray">الحالة</th>
                                            <th class="px-5 py-3 text-sm font-button text-slate-gray">تاريخ التقديم</th>
                                            <th class="px-5 py-3 text-sm font-button text-slate-gray">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($submissions as $sub): ?>
                                            <?php
                                                $status = $statusMap[$sub['status']] ?? ['label' => $sub['status'], 'color' => 'bg-slate-100 text-slate-800'];
                                                $date = date('Y/m/d', strtotime($sub['created_at']));
                                            ?>
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-5 py-4">
                                                    <div class="font-bold text-charcoal text-sm"><?= htmlspecialchars($sub['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php if ($sub['serial_number']): ?>
                                                        <div class="text-xs text-slate-gray mt-1"><?= htmlspecialchars($sub['serial_number'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-button <?= htmlspecialchars($status['color'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($status['label'], ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-sm text-slate-gray"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="px-5 py-4">
                                                    <div class="flex flex-col gap-2">
                                                        <a href="<?php echo BASE_URL; ?>/student/submissions/<?= (int) $sub['id'] ?>" class="inline-flex items-center gap-1 text-sm font-button text-primary hover:underline">
                                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                            عرض التفاصيل
                                                        </a>
                                                        
                                                        <?php if (in_array($sub['status'], ['admin_reviewed', 'sample_sized'])): ?>
                                                            <?php $paymentType = $sub['status'] === 'admin_reviewed' ? 'initial' : 'sample_size'; ?>
                                                            <a href="<?php echo BASE_URL; ?>/student/payment/<?= (int) $sub['id'] ?>?type=<?= $paymentType ?>" 
                                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-500 text-white rounded text-xs font-button hover:bg-orange-600 transition-colors w-fit">
                                                                <span class="material-symbols-outlined text-[14px]">payments</span>
                                                                <?= $sub['status'] === 'admin_reviewed' ? 'سداد الرسوم الأولية' : 'سداد رسوم العينة' ?>
                                                            </a>
                                                        <?php endif; ?>

                                                    <?php if ($sub['status'] === 'approved' && !empty($sub['certificate_number'])): ?>
                                                        <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $sub['id'] ?>"
                                                           target="_blank"
                                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white rounded text-xs font-button hover:bg-green-700 transition-colors w-fit">
                                                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                                                            تحميل الشهادة
                                                        </a>
                                                    <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Notifications Panel -->
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="font-h1 text-lg text-charcoal">آخر الإشعارات</h3>
                            <span class="material-symbols-outlined text-slate-gray text-[20px]">notifications</span>
                        </div>

                        <?php if (empty($notifications)): ?>
                            <div class="p-6 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">notifications_off</span>
                                <p class="text-sm text-slate-gray">لا توجد إشعارات حالياً</p>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-slate-200">
                                <?php foreach ($notifications as $notif): ?>
                                    <?php
                                        $actionLabel = $actionLabels[$notif['action']] ?? $notif['action'];
                                    ?>
                                    <div class="px-5 py-4 flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center rounded-md bg-primary px-2 py-1.5 text-xs font-button text-on-primary shrink-0">
                                            <?= htmlspecialchars(mb_substr($actionLabel, 0, 20, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <div class="flex-1 text-right">
                                            <p class="text-sm leading-7 text-charcoal"><?= htmlspecialchars($notif['details'] ?? $notif['action'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <?php if ($notif['submission_title']): ?>
                                                <p class="text-xs text-slate-gray mt-0.5"><?= htmlspecialchars($notif['submission_title'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <?php endif; ?>
                                            <p class="text-xs text-slate-400 mt-1"><?= timeAgo($notif['created_at']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

<style>
@keyframes notifFadeIn {
    from { opacity: 0; transform: translateY(-6px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)   scale(1);    }
}
#notif-dropdown:not(.hidden) {
    animation: notifFadeIn 0.15s ease forwards;
}
</style>

<script>
(function () {
    'use strict';

    const bellBtn     = document.getElementById('notif-bell-btn');
    const dropdown    = document.getElementById('notif-dropdown');
    const badge       = document.getElementById('notif-badge');
    const markReadBtn = document.getElementById('notif-mark-read-btn');
    const BASE_URL    = '<?= rtrim(BASE_URL, '/') ?>';

    if (!bellBtn || !dropdown) return;

    /* ── Toggle dropdown ────────────────────────────────────── */
    bellBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = !dropdown.classList.contains('hidden');

        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    function openDropdown() {
        dropdown.classList.remove('hidden');
        bellBtn.setAttribute('aria-expanded', 'true');
        // Persist last-seen on server so badge resets on next page load
        fetch(BASE_URL + '/api/notifications/mark-read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function () { /* non-critical */ });
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        bellBtn.setAttribute('aria-expanded', 'false');
    }

    /* ── Close on outside click ─────────────────────────────── */
    document.addEventListener('click', function (e) {
        if (!dropdown.classList.contains('hidden') &&
            !dropdown.contains(e.target) &&
            !bellBtn.contains(e.target)) {
            closeDropdown();
        }
    });

    /* ── Close on Escape ────────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });

    /* ── Mark all as read (client-side dots + badge) ────────── */
    if (markReadBtn) {
        markReadBtn.addEventListener('click', function () {
            // Hide all unread indicator dots
            document.querySelectorAll('.notif-unread-dot').forEach(function (dot) {
                dot.classList.add('opacity-0');
            });
            // Hide the badge
            if (badge) {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
            // Persist on server
            fetch(BASE_URL + '/api/notifications/mark-read', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(function () { /* non-critical */ });
        });
    }
})();
</script>
</body>
</html>

