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
<body class="min-h-screen flex flex-col text-charcoal">

<header class="bg-white border-b border-charcoal flex justify-between items-center w-full px-8 h-16 fixed top-0 z-50">
    <div class="text-xl font-black text-indigo-900">IRB Portal</div>
    <div class="flex items-center gap-4 text-slate-600">
        <span class="material-symbols-outlined cursor-pointer hover:text-royal-indigo transition-colors">notifications</span>
        <div class="w-8 h-8 rounded-full bg-slate-200 border border-charcoal flex items-center justify-center">
            <span class="material-symbols-outlined">person</span>
        </div>
    </div>
</header>

<div class="flex flex-1 pt-16">
    <nav class="bg-slate-50 border-l border-charcoal fixed inset-y-0 right-0 w-64 flex flex-col z-40 mt-16 pt-8 px-4">
        <div class="mb-8 px-4">
            <div class="w-12 h-12 bg-surface-variant mb-4 flex items-center justify-center border border-charcoal">
                <span class="material-symbols-outlined text-secondary">domain</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 leading-tight">مساحة المراجع</h2>
            <p class="text-xs text-secondary mt-1">مجلس المراجعة المؤسسية</p>
        </div>
        <ul class="flex flex-col gap-2">
            <li>
                <a class="bg-indigo-900 text-white font-bold flex items-center gap-3 px-4 py-3 border border-charcoal" href="<?php echo BASE_URL; ?>/reviewer/dashboard">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
                    <span>الأبحاث المعينة</span>
                </a>
            </li>
            <li>
                <a class="text-slate-700 hover:bg-slate-200 flex items-center gap-3 px-4 py-3 border border-transparent transition-colors" href="<?php echo BASE_URL; ?>/reviewer/history">
                    <span class="material-symbols-outlined">history</span>
                    <span>السجل</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto p-4 border-t border-charcoal">
            <a href="<?php echo BASE_URL; ?>/logout" class="text-red-600 flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </nav>

    <main class="flex-1 mr-64 p-12 max-w-7xl mx-auto w-full">
        <header class="mb-12 border-b border-charcoal pb-6">
            <h1 class="font-h1 text-4xl text-charcoal mb-2">لوحة تحكم المراجع</h1>
            <p class="text-lg text-secondary">الأبحاث المعينة للمراجعة والتقييم ضمن الإطار الزمني المحدد.</p>
        </header>

        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="mb-8 p-4 bg-green-50 border border-green-600 text-green-700 flex items-center gap-3 font-bold">
                <span class="material-symbols-outlined">check_circle</span>
                <?= htmlspecialchars($_SESSION['review_success']) ?>
                <?php unset($_SESSION['review_success']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-charcoal overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead class="bg-cool-slate border-b border-charcoal">
                    <tr>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">الرقم التسلسلي</th>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">عنوان البحث</th>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">تاريخ التقديم</th>
                        <th class="py-4 px-6 font-bold">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="text-charcoal">
                    <?php if (empty($assignedReviews)): ?>
                        <tr>
                            <td colspan="4" class="py-12 px-6 text-center text-secondary font-bold">لا توجد أبحاث معينة للمراجعة حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignedReviews as $review): ?>
                        <tr class="border-b border-surface-variant hover:bg-slate-50 transition-colors">
                            <td class="py-5 px-6 border-l border-surface-variant font-numeral font-bold text-royal-indigo">
                                <?= htmlspecialchars($review['serial_number'] ?: 'قيد المعالجة') ?>
                            </td>
                            <td class="py-5 px-6 border-l border-surface-variant">
                                <span class="block font-bold mb-1 line-clamp-2"><?= htmlspecialchars($review['title']) ?></span>
                            </td>
                            <td class="py-5 px-6 border-l border-surface-variant font-numeral">
                                <?= date('d/m/Y', strtotime($review['created_at'])) ?>
                            </td>
                            <td class="py-5 px-6">
                                <a href="<?php echo BASE_URL; ?>/reviewer/view/<?= $review['submission_id'] ?>" 
                                   class="block bg-royal-indigo text-white px-5 py-2.5 hover:bg-primary transition-colors text-center font-bold text-sm">
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