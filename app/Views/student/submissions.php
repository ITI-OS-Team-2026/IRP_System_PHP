<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'أبحاثي - IRB Portal';

require_once __DIR__ . '/../../../config/database.php';
$db = Database::getConnection();

$studentId = (int) $_SESSION['user_id'];
$errorMessage = '';
$hasCertificatesTableResult = $db->query("SHOW TABLES LIKE 'certificates'");
$hasCertificatesTable = $hasCertificatesTableResult instanceof mysqli_result && $hasCertificatesTableResult->num_rows > 0;

if (isset($_SESSION['submission_error'])) {
    $errorMessage = $_SESSION['submission_error'];
    unset($_SESSION['submission_error']);
}

// Fetch all submissions for this student
if ($hasCertificatesTable) {
    $submissionsResult = $db->query(
        "SELECT rs.id, rs.title, rs.principal_investigator, rs.serial_number, rs.status, rs.created_at,
                c.certificate_number
         FROM research_submissions rs
         LEFT JOIN certificates c ON c.submission_id = rs.id
         WHERE rs.student_id = $studentId
         ORDER BY rs.created_at DESC"
    );
} else {
    $submissionsResult = $db->query(
        "SELECT rs.id, rs.title, rs.principal_investigator, rs.serial_number, rs.status, rs.created_at,
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

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => BASE_URL . '/student/dashboard', 'active' => false],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => BASE_URL . '/student/submissions', 'active' => true],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => BASE_URL . '/student/submission/create'],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => BASE_URL . '/student/settings'],
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
<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <aside class="w-full lg:w-[260px] bg-white border-r border-gray-200 shadow-sm lg:shadow-none">
            <div class="p-5 border-b border-gray-200 flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-100 overflow-hidden flex items-center justify-center text-indigo-700">
                    <span class="material-symbols-outlined text-3xl">account_balance</span>
                </div>
                <div>
                    <h1 class="font-h1 text-lg text-gray-900">IRB</h1>
                    <p class="text-sm text-gray-600">بوابة الباحث</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <?php foreach ($sidebarItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-all <?= !empty($item['active']) ? 'bg-indigo-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-4 mt-auto border-t border-gray-200">
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
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-sm text-gray-600">أبحاثي</p>
                    <h2 class="font-h1 text-2xl text-gray-900">أبحاثي</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/student/submission/create"
                   class="inline-flex items-center gap-2 bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg hover:-translate-y-0.5">
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
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-h1 text-lg text-gray-900">قائمة أبحاثي</h3>
                        <span class="text-sm text-gray-600"><?= (int)count($submissions) ?> بحث</span>
                    </div>

                    <?php if (empty($submissions)): ?>
                        <!-- Empty State -->
                        <div class="p-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4 block">science</span>
                            <p class="text-gray-600 text-lg mb-2">لا توجد أبحاث حتى الآن</p>
                            <p class="text-sm text-gray-500 mb-6">ابدأ بتقديم أول بحث لك</p>
                            <a href="<?php echo BASE_URL; ?>/student/submission/create"
                               class="inline-flex items-center gap-2 bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-[18px]">note_add</span>
                                تقديم بحث جديد
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Submissions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-right">
                                <thead class="bg-gray-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">عنوان البحث</th>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">الباحث الرئيسي</th>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">الرقم التسلسلي</th>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">الحالة</th>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">تاريخ التقديم</th>
                                        <th class="px-5 py-3 text-sm font-button text-gray-600">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($submissions as $sub): ?>
                                        <?php
                                            $status = $statusMap[$sub['status']] ?? ['label' => $sub['status'], 'color' => 'bg-gray-100 text-gray-800'];
                                            $date = formatDate($sub['created_at']);
                                            $serialNumber = $sub['serial_number'] ?? 'لم يُحدد بعد';
                                        ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($sub['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-gray-600">
                                                <?= htmlspecialchars($sub['principal_investigator'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-gray-600">
                                                <?= htmlspecialchars($serialNumber, ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-button <?= htmlspecialchars($status['color'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars($status['label'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-gray-600"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col gap-2">
                                                    <a href="<?php echo BASE_URL; ?>/student/submissions/<?= (int) $sub['id'] ?>" class="inline-flex items-center gap-1 text-sm font-button text-indigo-700 hover:underline">
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
            </section>
        </main>
    </div>
</body>
</html>
