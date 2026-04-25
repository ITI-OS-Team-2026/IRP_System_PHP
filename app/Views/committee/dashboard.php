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
<body class="bg-slate-50 text-slate-900 min-h-screen">
<div class="min-h-screen flex flex-col lg:flex-row">
    <aside class="w-full lg:w-72 bg-white border-b lg:border-b-0 lg:border-l border-slate-200 p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-900 text-white flex items-center justify-center rounded font-bold">IRB</div>
            <div>
                <div class="font-bold text-indigo-900">لجنة الاعتماد</div>
                <div class="text-xs text-slate-500">Committee Manager</div>
            </div>
        </div>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>/committee/certificates" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-900 text-white font-bold">
                <span class="material-symbols-outlined">workspace_premium</span>
                إدارة الشهادات
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="flex items-center gap-2 px-3 py-2 rounded text-slate-700 hover:bg-slate-100">
                <span class="material-symbols-outlined">fact_check</span>
                زمام الموافقات النهائية
            </a>
        </nav>
        <div class="mt-6 lg:mt-auto pt-4 border-t border-slate-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-5 lg:p-8">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-indigo-900">إدارة الشهادات الصادرة</h1>
            <p class="text-slate-600 mt-1">قائمة الشهادات المعتمدة والصادرة للباحثين.</p>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="mb-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="mb-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="bg-white border border-slate-200 rounded-lg overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm">رقم الشهادة</th>
                        <th class="px-4 py-3 text-sm">الرقم التسلسلي</th>
                        <th class="px-4 py-3 text-sm">عنوان البحث</th>
                        <th class="px-4 py-3 text-sm">اسم الطالب</th>
                        <th class="px-4 py-3 text-sm">تاريخ الإصدار</th>
                        <th class="px-4 py-3 text-sm">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">لا توجد شهادات صادرة حاليًا.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $item): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-semibold"><?= htmlspecialchars($item['certificate_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['serial_number'] ?: 'لم يُحدد', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['issued_at'] ? date('Y/m/d', strtotime($item['issued_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a href="<?php echo BASE_URL; ?>/committee/certificate/<?= (int) $item['submission_id'] ?>" class="text-indigo-700 hover:underline">فتح</a>
                                        <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $item['submission_id'] ?>" target="_blank" class="text-slate-700 hover:underline">تحميل</a>
                                        <a href="<?php echo BASE_URL; ?>/committee/certificates/delete/<?= (int) $item['certificate_id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('هل أنت متأكد من حذف الشهادة؟');">حذف</a>
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
