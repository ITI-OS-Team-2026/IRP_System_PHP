<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>سجل المراجع - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                  "royal-indigo": "#312E81",
                  "primary": "#4338ca",
                  "charcoal": "#111827",
                  "secondary": "#4b5563",
                  "paper-white": "#f9fafb",
                  "cool-slate": "#f3f4f6",
                  "surface-variant": "#e5e7eb",
                  "forest": "#166534",
                  "crimson": "#991B1B",
                  "amber": "#92400E"
              },
              fontFamily: {
                  "h1": ["Amiri"],
                  "body-lg": ["Tajawal"],
                  "numeral": ["Tajawal"],
                  "button": ["Tajawal"]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Tajawal', sans-serif; background-color: #f9fafb; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-900">
<header class="bg-white border-b border-gray-200 flex justify-between items-center w-full px-8 h-16 fixed top-0 z-50">
    <div class="text-xl font-black text-indigo-900">IRB Portal</div>
    <div class="flex items-center gap-4 text-gray-600">
        <div class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
            <span class="material-symbols-outlined">person</span>
        </div>
    </div>
</header>

<div class="flex flex-1 pt-16">
    <nav class="bg-gray-50 border-l border-gray-200 fixed inset-y-0 right-0 w-64 flex flex-col z-40 mt-16 pt-8 px-4">
        <div class="mb-8 px-4">
            <div class="w-12 h-12 bg-indigo-100 mb-4 flex items-center justify-center border border-gray-200">
                <span class="material-symbols-outlined text-indigo-700">domain</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900 leading-tight">مساحة المراجع</h2>
            <p class="text-xs text-gray-600 mt-1">مجلس المراجعة المؤسسية</p>
        </div>
        <ul class="flex flex-col gap-2">
            <li>
                <a class="text-gray-700 hover:bg-gray-200 flex items-center gap-3 px-4 py-3 border border-transparent transition-colors" href="<?php echo BASE_URL; ?>/reviewer/dashboard">
                    <span class="material-symbols-outlined">assignment</span>
                    <span>الأبحاث المعينة</span>
                </a>
            </li>
            <li>
                <a class="bg-indigo-700 text-white font-bold flex items-center gap-3 px-4 py-3 border border-indigo-700" href="<?php echo BASE_URL; ?>/reviewer/history">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">history</span>
                    <span>السجل</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto p-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="text-red-600 flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </nav>

    <main class="flex-1 mr-64 p-12 max-w-7xl mx-auto w-full">
        <header class="mb-10 border-b border-gray-200 pb-6">
            <h1 class="font-h1 text-4xl text-gray-900 mb-2">سجل المراجعات</h1>
            <p class="text-lg text-gray-600">كل القرارات التي قمت بتسجيلها على الأبحاث.</p>
        </header>

        <div class="bg-white border border-gray-200 overflow-hidden rounded-xl shadow-sm">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="py-4 px-6 font-bold border-l border-gray-200 text-gray-600">الرقم التسلسلي</th>
                        <th class="py-4 px-6 font-bold border-l border-gray-200 text-gray-600">عنوان البحث</th>
                        <th class="py-4 px-6 font-bold border-l border-gray-200 text-gray-600">القرار</th>
                        <th class="py-4 px-6 font-bold border-l border-gray-200 text-gray-600">تاريخ القرار</th>
                        <th class="py-4 px-6 font-bold text-gray-600">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-900">
                    <?php if (empty($reviewHistory)): ?>
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-600 font-bold">لا توجد مراجعات منتهية بعد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviewHistory as $item): ?>
                            <?php
                                $status = $item['review_status'];
                                if ($status === 'approved') {
                                    $statusLabel = 'موافقة';
                                    $statusClass = 'text-green-800 bg-green-50 border-green-200';
                                } elseif ($status === 'rejected') {
                                    $statusLabel = 'رفض';
                                    $statusClass = 'text-red-800 bg-red-50 border-red-200';
                                } else {
                                    $statusLabel = 'طلب تعديل';
                                    $statusClass = 'text-amber bg-amber-50 border-amber-200';
                                }
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="py-5 px-6 border-l border-gray-200 font-numeral font-bold text-indigo-700">
                                    <?= htmlspecialchars($item['serial_number'] ?: 'قيد المعالجة') ?>
                                </td>
                                <td class="py-5 px-6 border-l border-gray-200">
                                    <a class="font-bold text-indigo-700 hover:underline" href="<?php echo BASE_URL; ?>/reviewer/view/<?= (int) $item['submission_id'] ?>">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </td>
                                <td class="py-5 px-6 border-l border-gray-200">
                                    <span class="inline-flex px-3 py-1 rounded-full border text-sm font-bold <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="py-5 px-6 border-l border-gray-200 font-numeral">
                                    <?= $item['reviewed_at'] ? date('d/m/Y h:i A', strtotime($item['reviewed_at'])) : '-' ?>
                                </td>
                                <td class="py-5 px-6 text-sm text-gray-600">
                                    <?= htmlspecialchars($item['feedback_notes'] ?: 'لا توجد ملاحظات') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
