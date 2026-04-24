<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'أبحاثي - IRB Portal';

require_once __DIR__ . '/../../../config/database.php';
$db = Database::getConnection();

$studentId = (int) $_SESSION['user_id'];
$errorMessage = '';

if (isset($_SESSION['submission_error'])) {
    $errorMessage = $_SESSION['submission_error'];
    unset($_SESSION['submission_error']);
}

// Fetch all submissions for this student
$submissionsResult = $db->query(
    "SELECT id, title, principal_investigator, serial_number, status, created_at
     FROM research_submissions
     WHERE student_id = $studentId
     ORDER BY created_at DESC"
);

$submissions = [];
while ($row = $submissionsResult->fetch_assoc()) {
    $submissions[] = $row;
}

$statusMap = [
    'submitted'          => ['label' => 'تم التقديم',        'color' => 'bg-blue-100 text-blue-800'],
    'admin_reviewed'     => ['label' => 'تمت المراجعة',      'color' => 'bg-yellow-100 text-yellow-800'],
    'initial_paid'       => ['label' => 'تم الدفع الأولي',   'color' => 'bg-yellow-100 text-yellow-800'],
    'sample_sized'       => ['label' => 'تم حساب العينة',    'color' => 'bg-yellow-100 text-yellow-800'],
    'fully_paid'         => ['label' => 'تم الدفع الكامل',   'color' => 'bg-yellow-100 text-yellow-800'],
    'under_review'       => ['label' => 'قيد المراجعة',      'color' => 'bg-orange-100 text-orange-800'],
    'revision_requested' => ['label' => 'مطلوب تعديل',       'color' => 'bg-red-100 text-red-800'],
    'approved'           => ['label' => 'تمت الموافقة',      'color' => 'bg-green-100 text-green-800'],
    'rejected'           => ['label' => 'مرفوض',             'color' => 'bg-red-100 text-red-800'],
];

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => '/student/dashboard', 'active' => false],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => '/student/submissions', 'active' => true],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => '/student/submission/create'],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => '#'],
];

function formatDate($datetime) {
    return date('d/m/Y', strtotime($datetime));
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
                <a href="/logout"
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
                    <p class="text-sm text-slate-gray">أبحاثي</p>
                    <h2 class="font-h1 text-2xl text-charcoal">أبحاثي</h2>
                </div>
                <a href="/student/submission/create"
                   class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">note_add</span>
                    تقديم بحث جديد
                </a>
            </header>

            <section class="px-4 md:px-8 py-6">
                <?php if (!empty($errorMessage)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600 text-2xl shrink-0">error</span>
                        <div>
                            <h3 class="font-button text-sm text-red-800">خطأ في الوصول</h3>
                            <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Submissions Table -->
                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="font-h1 text-lg text-charcoal">قائمة أبحاثي</h3>
                        <span class="text-sm text-slate-gray"><?= (int)count($submissions) ?> بحث</span>
                    </div>

                    <?php if (empty($submissions)): ?>
                        <!-- Empty State -->
                        <div class="p-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">science</span>
                            <p class="text-slate-gray text-lg mb-2">لا توجد أبحاث حتى الآن</p>
                            <p class="text-sm text-slate-400 mb-6">ابدأ بتقديم أول بحث لك</p>
                            <a href="/student/submission/create"
                               class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors">
                                <span class="material-symbols-outlined text-[18px]">note_add</span>
                                تقديم بحث جديد
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Submissions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">عنوان البحث</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">الباحث الرئيسي</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">الرقم التسلسلي</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">الحالة</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">تاريخ التقديم</th>
                                        <th class="px-5 py-3 text-sm font-button text-slate-gray">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($submissions as $sub): ?>
                                        <?php
                                            $status = $statusMap[$sub['status']] ?? ['label' => $sub['status'], 'color' => 'bg-slate-100 text-slate-800'];
                                            $date = formatDate($sub['created_at']);
                                            $serialNumber = $sub['serial_number'] ?? 'لم يُحدد بعد';
                                        ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="font-bold text-charcoal text-sm"><?= htmlspecialchars($sub['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-gray">
                                                <?= htmlspecialchars($sub['principal_investigator'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-gray">
                                                <?= htmlspecialchars($serialNumber, ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-button <?= htmlspecialchars($status['color'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars($status['label'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-gray"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="px-5 py-4">
                                                <a href="/student/submissions/<?= (int) $sub['id'] ?>" class="inline-flex items-center gap-1 text-sm font-button text-primary hover:underline">
                                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                    عرض التفاصيل
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
