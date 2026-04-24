<?php
$pageTitle = 'إدخال حجم العينة - مساحة عمل الإحصائي';
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
          theme: {
            extend: {
              "colors": {
                      "primary": "#1a146b",
                      "crimson": "#991B1B",
                      "slate-gray": "#64748B",
                      "charcoal": "#1A1C1E",
                      "royal-indigo": "#312E81",
                      "paper-white": "#FDFDFC",
                      "cool-slate": "#F8FAFC"
              },
              "spacing": {
                      "section-stack": "3rem",
                      "edge-margin": "2rem",
                      "form-gap": "1.25rem"
              },
              "fontFamily": {
                      "body-sm": ["Tajawal"],
                      "h1": ["Amiri"],
                      "display-md": ["Amiri"],
                      "numeral": ["Tajawal"]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-paper-white text-charcoal min-h-screen pr-64">

<!-- SideNavBar -->
<aside class="fixed inset-y-0 right-0 flex flex-col w-64 bg-slate-50 border-l border-zinc-800 z-50">
    <div class="px-6 py-8 border-b border-zinc-800 flex flex-col items-start gap-4">
        <div class="w-12 h-12 bg-cool-slate border border-charcoal flex items-center justify-center">
            <span class="material-symbols-outlined text-royal-indigo" style="font-variation-settings: 'FILL' 1;">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-indigo-900 tracking-tight">مساحة عمل الإحصائي</h2>
            <p class="text-sm tracking-tight text-zinc-500">وحدة البحوث الإكلينيكية</p>
        </div>
    </div>
    <nav class="flex-1 py-6 flex flex-col gap-1">
        <a class="bg-indigo-900 text-white font-bold flex items-center gap-3 px-4 py-3" href="<?php echo BASE_URL; ?>/officer/sample-size/queue">
            <span class="material-symbols-outlined">calculate</span>
            <span class="text-sm">الحسابات الحالية</span>
        </a>
        <a class="text-zinc-700 flex items-center gap-3 px-4 py-3 hover:bg-zinc-200 transition-none" href="<?php echo BASE_URL; ?>/officer/sample-size/archives">
            <span class="material-symbols-outlined">inventory_2</span>
            <span class="text-sm">الأرشيف</span>
        </a>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<div class="flex flex-col min-h-screen">
    <!-- TopAppBar -->
    <header class="flex justify-between items-center px-8 h-16 w-full border-b-2 border-zinc-800 bg-white z-40 relative">
        <h1 class="text-xl font-black text-indigo-900 uppercase tracking-widest">مسؤول حجم العينة</h1>
        <div class="flex items-center gap-6 text-zinc-500">
            <span class="material-symbols-outlined">notifications</span>
            <span class="material-symbols-outlined">help_outline</span>
            <div class="w-8 h-8 bg-zinc-200 border border-zinc-300 rounded-full overflow-hidden flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </header>

    <!-- Canvas -->
    <main class="flex-1 p-edge-margin">
        <div class="max-w-[800px] mx-auto w-full flex flex-col gap-section-stack">
            <!-- Page Header -->
            <div class="border-b border-charcoal pb-4 text-right">
                <h2 class="font-h1 text-4xl text-charcoal">إدخال حجم العينة</h2>
                <p class="text-slate-gray mt-2 text-lg">يرجى مراجعة تفاصيل البحث وإدخال حجم العينة المعتمد للاستمرار.</p>
            </div>

            <!-- Focused Form Card -->
            <article class="bg-cool-slate border border-charcoal p-8">
                <!-- Research Meta Data -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-charcoal text-right">
                    <div>
                        <span class="block text-xs text-slate-gray uppercase tracking-wider mb-1">الرقم التسلسلي للبحث</span>
                        <span class="font-numeral font-bold text-charcoal"><?= htmlspecialchars($submission['serial_number'] ?: '---') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-gray uppercase tracking-wider mb-1">اسم الباحث</span>
                        <span class="font-bold text-charcoal"><?= htmlspecialchars($submission['student_name']) ?></span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs text-slate-gray uppercase tracking-wider mb-1">عنوان البحث المعتمد</span>
                        <h3 class="font-display-md text-3xl text-charcoal leading-tight mt-1"><?= htmlspecialchars($submission['title']) ?></h3>
                    </div>
                </div>

                <!-- Input Form -->
                <form action="<?php echo BASE_URL; ?>/officer/sample-size/store" method="POST" class="flex flex-col gap-form-gap text-right">
                    <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-lg text-charcoal flex items-center gap-2" for="sample_size">
                            حجم العينة المطلوب
                            <span aria-hidden="true" class="text-crimson text-sm">*</span>
                        </label>
                        <p class="text-sm text-slate-gray mb-2">أدخل العدد النهائي المعتمد بعد إجراء الحسابات الإحصائية اللازمة لضمان قوة الدراسة.</p>
                        
                        <?php if (isset($_SESSION['officer_error'])): ?>
                            <div class="p-4 border border-crimson bg-red-50 text-crimson text-sm mb-4">
                                <?= htmlspecialchars($_SESSION['officer_error']) ?>
                                <?php unset($_SESSION['officer_error']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="relative w-full max-w-xs">
                            <input class="w-full border border-charcoal bg-paper-white px-4 py-3 font-numeral text-xl text-charcoal focus:border-2 focus:border-royal-indigo focus:outline-none transition-none text-center" 
                                   id="sample_size" name="sample_size" placeholder="مثال: 350" required type="number" min="1"/>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-charcoal flex items-center justify-end gap-4">
                        <a href="<?php echo BASE_URL; ?>/officer/sample-size/queue" 
                           class="border border-charcoal bg-paper-white text-charcoal px-6 py-3 font-bold hover:bg-surface-container-high transition-colors">
                            إلغاء
                        </a>
                        <button class="bg-royal-indigo text-white px-8 py-3 font-bold hover:bg-indigo-900 transition-colors flex items-center gap-2" type="submit">
                            <span>حفظ وتفعيل الدفع الثاني</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </article>

            <!-- Contextual Audit Trail -->
            <div class="pr-4 border-l-2 border-charcoal py-2 mt-4 mr-auto w-3/4 opacity-70 text-right">
                <p class="text-sm text-slate-gray flex items-center gap-2 justify-end">
                    تاريخ التقديم: <?= date('d أكتوبر Y', strtotime($submission['created_at'])) ?>. البحث بانتظار تحديد حجم العينة.
                    <span class="material-symbols-outlined text-sm">history</span>
                </p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
