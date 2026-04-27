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
    <title>زمام الموافقات النهائية - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
<div class="min-h-screen flex">
    <aside class="w-72 bg-white border-l border-gray-200 p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-700 text-white flex items-center justify-center rounded font-bold">IRB</div>
            <div>
                <div class="font-bold text-indigo-700">لجنة الاعتماد</div>
                <div class="text-xs text-gray-500">Committee Manager</div>
            </div>
        </div>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>/committee/certificates" class="flex items-center gap-2 px-3 py-2 rounded text-gray-700 hover:bg-gray-100">
                <span class="material-symbols-outlined">workspace_premium</span>
                إدارة الشهادات
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-700 text-white font-bold">
                <span class="material-symbols-outlined">fact_check</span>
                زمام الموافقات النهائية
            </a>
        </nav>
        <div class="mt-auto pt-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-indigo-700">زمام الموافقات النهائية</h1>
            <p class="text-gray-600 mt-1">الأبحاث الموافق عليها من المراجع ولم تُصدر لها شهادة بعد.</p>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="mb-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="mb-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <table class="w-full text-right">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-sm text-gray-600">الرقم التسلسلي</th>
                        <th class="px-4 py-3 text-sm text-gray-600">عنوان البحث</th>
                        <th class="px-4 py-3 text-sm text-gray-600">اسم الطالب</th>
                        <th class="px-4 py-3 text-sm text-gray-600">تاريخ المراجعة</th>
                        <th class="px-4 py-3 text-sm text-gray-600">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات بانتظار القرار النهائي.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['serial_number'] ?: 'لم يُحدد', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm font-semibold"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['reviewed_at'] ? date('Y/m/d', strtotime($item['reviewed_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="<?php echo BASE_URL; ?>/committee/certificate/<?= (int) $item['id'] ?>" class="inline-flex items-center gap-2 rounded-md bg-indigo-700 px-4 py-2 text-white font-bold hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                        تقييم وإصدار الشهادة
                                    </a>
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
