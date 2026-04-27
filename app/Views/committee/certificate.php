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
    <title>إصدار الشهادة - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
<div class="min-h-screen flex flex-col lg:flex-row">
    <aside class="w-full lg:w-72 bg-white border-b lg:border-b-0 lg:border-l border-gray-200 p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-700 text-white flex items-center justify-center rounded font-bold">IRB</div>
            <div>
                <div class="font-bold text-indigo-700">لجنة الاعتماد</div>
                <div class="text-xs text-gray-500">Committee Manager</div>
            </div>
        </div>
        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>/committee/certificates" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-700 text-white font-bold">
                <span class="material-symbols-outlined">workspace_premium</span>
                إدارة الشهادات
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="flex items-center gap-2 px-3 py-2 rounded text-gray-700 hover:bg-gray-100">
                <span class="material-symbols-outlined">fact_check</span>
                زمام الموافقات النهائية
            </a>
        </nav>
        <div class="mt-6 lg:mt-auto pt-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-5 lg:p-8 space-y-6">
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-indigo-700">الاعتماد النهائي وإصدار الشهادة</h1>
                <p class="text-gray-600 mt-1">الرقم التسلسلي: <?= htmlspecialchars($submission['serial_number'] ?: ('SUB-' . (int) $submission['id']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="inline-flex items-center gap-2 text-gray-700 hover:text-indigo-700">
                <span class="material-symbols-outlined">arrow_back</span>
                العودة إلى الطلبات
            </a>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">بيانات البحث</h2>
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold">العنوان:</span> <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">الباحث الرئيسي:</span> <?= htmlspecialchars($submission['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">اسم الطالب:</span> <?= htmlspecialchars($submission['student_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">القسم:</span> <?= htmlspecialchars($submission['department'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">التخصص:</span> <?= htmlspecialchars($submission['specialty'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">تاريخ المراجعة:</span> <?= htmlspecialchars($submission['reviewed_at'] ? date('Y/m/d', strtotime($submission['reviewed_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">ملاحظات المراجع</h2>
                <p class="text-sm leading-7 text-gray-700"><?= nl2br(htmlspecialchars($submission['feedback_notes'] ?: 'لا توجد ملاحظات إضافية.', ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </section>

        <section class="bg-white border-2 border-indigo-700 rounded-lg p-6">
            <?php if (!empty($submission['certificate_number'])): ?>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-green-700">تم إصدار الشهادة</h3>
                        <p class="text-gray-700 mt-1">رقم الشهادة: <span class="font-bold"><?= htmlspecialchars($submission['certificate_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
                        <p class="text-gray-700 mt-1">تاريخ الإصدار: <span class="font-bold"><?= htmlspecialchars($submission['issued_at'] ? date('Y/m/d', strtotime($submission['issued_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></span></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $submission['id'] ?>" target="_blank" class="inline-flex items-center gap-2 bg-indigo-700 text-white px-5 py-3 rounded hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                        <span class="material-symbols-outlined">download</span>
                        تحميل الشهادة
                    </a>
                </div>
            <?php else: ?>
                <h3 class="text-xl font-bold text-indigo-700 mb-2">إصدار الشهادة النهائية</h3>
                <p class="text-gray-700 mb-5">عند التأكيد سيتم إنشاء شهادة اعتماد نهائية لهذا البحث.</p>
                <form method="POST">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-700 text-white px-5 py-3 rounded hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                        <span class="material-symbols-outlined">verified</span>
                        إصدار الموافقة النهائية وإنشاء الشهادة
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
