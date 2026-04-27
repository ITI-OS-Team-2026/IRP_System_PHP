<?php
$successMessage = $_SESSION['committee_success'] ?? '';
$errorMessage = $_SESSION['committee_error'] ?? '';
unset($_SESSION['committee_success'], $_SESSION['committee_error']);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إدارة الشهادات - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
<div class="min-h-screen flex flex-col lg:flex-row">
    <aside class="w-full lg:w-72 bg-white border-b lg:border-b-0 lg:border-l border-gray-200 p-5 flex flex-col shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-700 text-white flex items-center justify-center rounded-xl font-bold">IRB</div>
            <div>
                <div class="font-bold text-gray-900">لجنة الاعتماد</div>
                <div class="text-xs text-gray-600">Committee Manager</div>
            </div>
        </div>
        <nav class="space-y-1">
            <a href="<?php echo BASE_URL; ?>/committee/certificates" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-indigo-700 text-white font-bold transition-all">
                <span class="material-symbols-outlined">workspace_premium</span>
                إدارة الشهادات
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <span class="material-symbols-outlined">fact_check</span>
                زمام الموافقات النهائية
            </a>
        </nav>
        <div class="mt-6 lg:mt-auto pt-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-5 lg:p-8">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">إدارة الشهادات الصادرة</h1>
            <p class="text-gray-600 mt-1">قائمة الشهادات المعتمدة والصادرة للباحثين.</p>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="mb-4 rounded-lg border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">رقم الشهادة</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">الرقم التسلسلي</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">عنوان البحث</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">اسم الطالب</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">تاريخ الإصدار</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-600">لا توجد شهادات صادرة حاليًا.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900"><?= htmlspecialchars($item['certificate_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($item['serial_number'] ?: 'لم يُحدد', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($item['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($item['issued_at'] ? date('Y/m/d', strtotime($item['issued_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a href="<?php echo BASE_URL; ?>/committee/certificate/<?= (int) $item['submission_id'] ?>" class="text-indigo-700 hover:text-indigo-800 font-bold">فتح</a>
                                        <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $item['submission_id'] ?>" target="_blank" class="text-gray-600 hover:text-gray-900">تحميل</a>
                                        <a href="<?php echo BASE_URL; ?>/committee/certificates/delete/<?= (int) $item['certificate_id'] ?>" class="text-red-600 hover:text-red-700" onclick="return confirm('هل أنت متأكد من حذف الشهادة؟');">حذف</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
