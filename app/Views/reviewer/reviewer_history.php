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
                  "primary": "#1a146b",
                  "charcoal": "#1A1C1E",
                  "secondary": "#5c5f61",
                  "paper-white": "#FDFDFC",
                  "cool-slate": "#F8FAFC",
                  "surface-variant": "#e2e2e5",
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
        body { font-family: 'Tajawal', sans-serif; background-color: #FDFDFC; }
    </style>
</head>
<body class="min-h-screen flex flex-col text-charcoal">
<header class="bg-white border-b border-charcoal flex justify-between items-center w-full px-8 h-16 fixed top-0 z-50">
    <div class="text-xl font-black text-indigo-900">IRB Portal</div>
    <div class="flex items-center gap-4 text-slate-600">
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
                <a class="text-slate-700 hover:bg-slate-200 flex items-center gap-3 px-4 py-3 border border-transparent transition-colors" href="<?php echo BASE_URL; ?>/reviewer/dashboard">
                    <span class="material-symbols-outlined">assignment</span>
                    <span>الأبحاث المعينة</span>
                </a>
            </li>
            <li>
                <a class="bg-indigo-900 text-white font-bold flex items-center gap-3 px-4 py-3 border border-charcoal" href="<?php echo BASE_URL; ?>/reviewer/history">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">history</span>
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
        <header class="mb-10 border-b border-charcoal pb-6">
            <h1 class="font-h1 text-4xl text-charcoal mb-2">سجل المراجعات</h1>
            <p class="text-lg text-secondary">كل القرارات التي قمت بتسجيلها على الأبحاث.</p>
        </header>

        <div class="bg-white border border-charcoal overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead class="bg-cool-slate border-b border-charcoal">
                    <tr>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">الرقم التسلسلي</th>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">عنوان البحث</th>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">القرار</th>
                        <th class="py-4 px-6 font-bold border-l border-charcoal">تاريخ القرار</th>
                        <th class="py-4 px-6 font-bold">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="text-charcoal">
                    <?php if (empty($reviewHistory)): ?>
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-secondary font-bold">لا توجد مراجعات منتهية بعد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviewHistory as $item): ?>
                            <?php
                                $status = $item['review_status'];
                                if ($status === 'approved') {
                                    $statusLabel = 'موافقة';
                                    $statusClass = 'text-forest bg-green-50 border-green-200';
                                } elseif ($status === 'rejected') {
                                    $statusLabel = 'رفض';
                                    $statusClass = 'text-crimson bg-red-50 border-red-200';
                                } else {
                                    $statusLabel = 'طلب تعديل';
                                    $statusClass = 'text-amber bg-amber-50 border-amber-200';
                                }
                            ?>
                            <tr class="border-b border-surface-variant hover:bg-slate-50 transition-colors">
                                <td class="py-5 px-6 border-l border-surface-variant font-numeral font-bold text-royal-indigo">
                                    <?= htmlspecialchars($item['serial_number'] ?: 'قيد المعالجة') ?>
                                </td>
                                <td class="py-5 px-6 border-l border-surface-variant">
                                    <a class="font-bold text-royal-indigo hover:underline" href="<?php echo BASE_URL; ?>/reviewer/view/<?= (int) $item['submission_id'] ?>">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </td>
                                <td class="py-5 px-6 border-l border-surface-variant">
                                    <span class="inline-flex px-3 py-1 rounded-full border text-sm font-bold <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="py-5 px-6 border-l border-surface-variant font-numeral">
                                    <?= $item['reviewed_at'] ? date('d/m/Y h:i A', strtotime($item['reviewed_at'])) : '-' ?>
                                </td>
                                <td class="py-5 px-6 text-sm text-secondary">
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
