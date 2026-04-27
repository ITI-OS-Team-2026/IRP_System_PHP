<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>لوحة تحكم المراجع - IRB Portal</title>
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
                  "primary": "#1a146b",
                  "charcoal": "#1A1C1E",
                  "secondary": "#5c5f61",
                  "paper-white": "#FDFDFC",
                  "cool-slate": "#F8FAFC",
                  "surface-variant": "#e2e2e5"
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
        body { font-family: 'Tajawal', sans-serif; background-color: #FDFDFC; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-900 bg-gray-50">

<header class="bg-white border-b border-gray-200 flex justify-between items-center w-full px-8 h-16 fixed top-0 z-50 shadow-sm">
    <div class="text-xl font-bold text-indigo-700">IRB Portal</div>
    <div class="flex items-center gap-4 text-gray-600">
        <span class="material-symbols-outlined cursor-pointer hover:text-indigo-700 transition-colors">notifications</span>
        <div class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center">
            <span class="material-symbols-outlined">person</span>
        </div>
    </div>
</header>

<div class="flex flex-1 pt-16">
    <nav class="bg-white border-l border-gray-200 fixed inset-y-0 right-0 w-64 flex flex-col z-40 mt-16 pt-8 px-4 shadow-sm">
        <div class="mb-8 px-4">
            <div class="w-12 h-12 bg-indigo-100 mb-4 flex items-center justify-center rounded-xl">
                <span class="material-symbols-outlined text-indigo-700">domain</span>
            </div>
            <h2 class="text-lg font-bold text-gray-900 leading-tight">مساحة المراجع</h2>
            <p class="text-xs text-gray-600 mt-1">مجلس المراجعة المؤسسية</p>
        </div>
        <ul class="flex flex-col gap-2">
            <li>
                <a class="bg-indigo-700 text-white font-bold flex items-center gap-3 px-4 py-3 rounded-lg transition-all" href="<?php echo BASE_URL; ?>/reviewer/dashboard">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
                    <span>الأبحاث المعينة</span>
                </a>
            </li>
            <li>
                <a class="text-gray-700 hover:bg-gray-100 flex items-center gap-3 px-4 py-3 rounded-lg transition-all" href="<?php echo BASE_URL; ?>/reviewer/history">
                    <span class="material-symbols-outlined">history</span>
                    <span>السجل</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto p-4 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="text-red-600 flex items-center gap-3 px-4 py-3 hover:bg-red-50 rounded-lg transition-all">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </nav>

    <main class="flex-1 mr-64 p-8 max-w-7xl mx-auto w-full">
        <header class="mb-8 border-b border-gray-200 pb-6">
            <h1 class="font-h1 text-3xl text-gray-900 mb-2">لوحة تحكم المراجع</h1>
            <p class="text-lg text-gray-600">الأبحاث المعينة للمراجعة والتقييم ضمن الإطار الزمني المحدد.</p>
        </header>

        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-600 text-green-700 rounded-lg flex items-center gap-3 font-bold">
                <span class="material-symbols-outlined">check_circle</span>
                <?= htmlspecialchars($_SESSION['review_success']) ?>
                <?php unset($_SESSION['review_success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="py-4 px-6 font-bold text-gray-900 border-l border-gray-200">الرقم التسلسلي</th>
                        <th class="py-4 px-6 font-bold text-gray-900 border-l border-gray-200">عنوان البحث</th>
                        <th class="py-4 px-6 font-bold text-gray-900 border-l border-gray-200">تاريخ التقديم</th>
                        <th class="py-4 px-6 font-bold text-gray-900">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="text-gray-900">
                    <?php if (empty($assignedReviews)): ?>
                        <tr>
                            <td colspan="4" class="py-12 px-6 text-center text-gray-600 font-bold">لا توجد أبحاث معينة للمراجعة حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignedReviews as $review): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-5 px-6 border-l border-gray-200 font-numeral font-bold text-indigo-700">
                                <?= htmlspecialchars($review['serial_number'] ?: 'قيد المعالجة') ?>
                            </td>
                            <td class="py-5 px-6 border-l border-gray-200">
                                <span class="block font-bold mb-1 line-clamp-2"><?= htmlspecialchars($review['title']) ?></span>
                            </td>
                            <td class="py-5 px-6 border-l border-gray-200 font-numeral text-gray-600">
                                <?= date('d/m/Y', strtotime($review['created_at'])) ?>
                            </td>
                            <td class="py-5 px-6">
                                <a href="<?php echo BASE_URL; ?>/reviewer/view/<?= $review['submission_id'] ?>"
                                   class="block bg-indigo-700 text-white px-5 py-2.5 hover:bg-indigo-800 transition-all text-center font-bold text-sm rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                    ابدأ المراجعة
                                </a>
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