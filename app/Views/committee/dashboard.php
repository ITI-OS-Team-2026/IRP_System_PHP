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
    <title>لجنة الاعتماد النهائي - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
<div class="min-h-screen flex">
    <aside class="w-72 bg-white border-l border-slate-200 p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-900 text-white flex items-center justify-center rounded font-bold">IRB</div>
            <div>
                <div class="font-bold text-indigo-900">لجنة الاعتماد</div>
                <div class="text-xs text-slate-500">Committee Manager</div>
            </div>
        </div>

        <nav class="space-y-2">
            <a href="<?php echo BASE_URL; ?>/committee/dashboard" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-900 text-white font-bold">
                <span class="material-symbols-outlined">fact_check</span>
                طلبات الاعتماد النهائي
            </a>
        </nav>

        <div class="mt-auto pt-4 border-t border-slate-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

  
</div>
</body>
</html>
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
    <title>لوحة لجنة الاعتماد النهائي - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <div class="min-h-screen flex">
        <aside class="w-72 bg-white border-l border-slate-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded bg-indigo-900 text-white flex items-center justify-center font-bold">IRB</div>
                <div>
                    <p class="font-bold text-indigo-900">لجنة الاعتماد</p>
                    <p class="text-xs text-slate-500">Committee Manager</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a href="<?php echo BASE_URL; ?>/committee/dashboard" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-900 text-white font-bold">
                    <span class="material-symbols-outlined">fact_check</span>
                    طلبات الاعتماد النهائي
                </a>
            </nav>
            <div class="mt-auto pt-4 border-t border-slate-200">
                <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                    <span class="material-symbols-outlined">logout</span>
                    تسجيل الخروج
                </a>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-indigo-900">طلبات الاعتماد النهائي</h1>
                <p class="text-slate-600 mt-1">يتم عرض الأبحاث التي حصلت على موافقة المراجع فقط.</p>
            </div>

            <?php if ($successMessage !== ''): ?>
                <div class="mb-4 rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($errorMessage !== ''): ?>
                <div class="mb-4 rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                <table class="w-full text-right">
                    <thead class="bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-sm">الرقم التسلسلي</th>
                            <th class="px-4 py-3 text-sm">عنوان البحث</th>
                            <th class="px-4 py-3 text-sm">الباحث</th>
                            <th class="px-4 py-3 text-sm">قرار المراجع</th>
                            <th class="px-4 py-3 text-sm">الشهادة</th>
                            <th class="px-4 py-3 text-sm">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد طلبات اعتماد نهائي حالياً.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $item): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['serial_number'] ?: 'لم يُحدد', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-4 py-3 text-sm font-semibold"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['student_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-4 py-3 text-sm text-green-700">موافق عليه</td>
                                    <td class="px-4 py-3 text-sm">
                                        <?php if (!empty($item['certificate_number'])): ?>
                                            <span class="inline-flex px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-bold">
                                                <?= htmlspecialchars($item['certificate_number'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex px-2 py-1 rounded bg-amber-100 text-amber-800 text-xs font-bold">لم تصدر بعد</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="<?php echo BASE_URL; ?>/committee/certificate/<?= (int) $item['id'] ?>" class="inline-flex items-center gap-1 text-indigo-700 hover:underline">
                                            <span class="material-symbols-outlined text-[18px]">verified</span>
                                            فتح الاعتماد والشهادة
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
<!DOCTYPE html>

<html dir="rtl" lang="ar"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>قائمة الاعتماد النهائي - نظام إدارة لجنة المراجعة المؤسسية</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "paper-white": "#FDFDFC",
                        "on-tertiary-fixed": "#311300",
                        "surface-container": "#eeeef0",
                        "on-error-container": "#93000a",
                        "tertiary": "#3e1a00",
                        "on-secondary": "#ffffff",
                        "outline-variant": "#c8c5d3",
                        "surface-variant": "#e2e2e5",
                        "on-background": "#1a1c1e",
                        "slate-gray": "#64748B",
                        "on-surface-variant": "#474651",
                        "secondary": "#5c5f61",
                        "on-secondary-fixed": "#191c1e",
                        "forest": "#166534",
                        "on-primary-fixed": "#100563",
                        "on-tertiary-fixed-variant": "#70380b",
                        "crimson": "#991B1B",
                        "on-tertiary-container": "#de915e",
                        "primary-fixed-dim": "#c3c0ff",
                        "tertiary-fixed": "#ffdbc7",
                        "surface-container-high": "#e8e8ea",
                        "secondary-fixed-dim": "#c4c7c9",
                        "primary-fixed": "#e2dfff",
                        "primary": "#1a146b",
                        "primary-container": "#312e81",
                        "surface": "#f9f9fc",
                        "royal-indigo": "#312E81",
                        "on-primary-fixed-variant": "#3e3c8f",
                        "on-secondary-container": "#626567",
                        "inverse-surface": "#2f3133",
                        "on-primary-container": "#9c9af4",
                        "surface-container-low": "#f3f3f6",
                        "cool-slate": "#F8FAFC",
                        "tertiary-fixed-dim": "#ffb688",
                        "surface-dim": "#dadadc",
                        "error-container": "#ffdad6",
                        "secondary-container": "#e0e3e5",
                        "on-secondary-fixed-variant": "#444749",
                        "secondary-fixed": "#e0e3e5",
                        "surface-container-highest": "#e2e2e5",
                        "background": "#f9f9fc",
                        "surface-bright": "#f9f9fc",
                        "outline": "#777682",
                        "inverse-primary": "#c3c0ff",
                        "tertiary-container": "#5f2b00",
                        "inverse-on-surface": "#f0f0f3",
                        "on-error": "#ffffff",
                        "on-primary": "#ffffff",
                        "on-surface": "#1a1c1e",
                        "charcoal": "#1A1C1E",
                        "surface-tint": "#5654a8",
                        "error": "#ba1a1a"
                    },
                    borderRadius: {
                        "DEFAULT": "0px",
                        "lg": "0px",
                        "xl": "0px",
                        "full": "0px"
                    },
                    spacing: {
                        "section-stack": "3rem",
                        "edge-margin": "2rem",
                        "gutter": "1.5rem",
                        "form-gap": "1.25rem",
                        "container-max": "1200px"
                    },
                    fontFamily: {
                        "h1": ["Amiri"],
                        "body-lg": ["Tajawal"],
                        "button": ["Tajawal"],
                        "body-sm": ["Tajawal"],
                        "numeral": ["Tajawal"],
                        "display-md": ["Amiri"],
                        "display-lg": ["Amiri"]
                    },
                    fontSize: {
                        "h1": ["24px", { "lineHeight": "1.4", "fontWeight": "700" }],
                        "body-lg": ["16px", { "lineHeight": "1.6", "fontWeight": "500" }],
                        "button": ["15px", { "lineHeight": "1", "fontWeight": "700" }],
                        "body-sm": ["13px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "numeral": ["14px", { "lineHeight": "1", "letterSpacing": "0.02em", "fontWeight": "500" }],
                        "display-md": ["32px", { "lineHeight": "1.2", "fontWeight": "700" }],
                        "display-lg": ["40px", { "lineHeight": "1.2", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-paper-white text-on-surface min-h-screen">
<!-- TopAppBar -->
<header class="fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-6 z-50 mr-64 bg-white dark:bg-slate-950 border-b border-charcoal font-['Tajawal'] text-right transition-all duration-200">
<div class="flex items-center gap-4 w-full justify-between">
<h1 class="text-lg font-black text-indigo-900 dark:text-indigo-300">نظام إدارة لجنة المراجعة المؤسسية</h1>
<div class="flex items-center gap-4">
<div class="relative hidden md:flex items-center">
<input class="h-9 px-4 pr-10 border border-charcoal bg-paper-white text-on-surface focus:outline-none focus:border-royal-indigo focus:border-2 font-body-sm text-body-sm w-64" placeholder="بحث..." type="text"/>
<span class="material-symbols-outlined absolute right-3 text-charcoal">search</span>
</div>
<button class="w-9 h-9 flex items-center justify-center text-charcoal hover:bg-surface-variant transition-colors">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">notifications</span>
</button>
<button class="w-9 h-9 flex items-center justify-center text-charcoal hover:bg-surface-variant transition-colors">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">settings</span>
</button>
<div class="w-9 h-9 bg-surface-variant overflow-hidden border border-charcoal">
<img alt="ملف المستخدم الأكاديمي" class="w-full h-full object-cover" data-alt="professional headshot of an academic researcher with a neutral background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGopj8uBk_txLapRpREDLmlmCJFCOkI0PcIbtV1jUkYej9WLSg7AlYH7M9Zi-iXUa1vqZoUn8Kxu7-61JasUHK7ipSC6bxyVlaRd_335AOJplRndVFMC5EabKDssZuCoVN5UObDWO_APh8-r0CNO4ZQDXURMIET6S1be0Vhkx0aQLL9OUxGXt_5o3dapdrM-c2ybGfe9MSEmIIJc_bS7FREDWNE1cIyENrW87v19UPmR1LkPLycuIDtnh8cC4-7suird0wk4CFnPxl"/>
</div>
</div>
</div>
</header>
<!-- SideNavBar -->
<nav class="fixed right-0 top-0 h-full z-40 flex flex-col bg-slate-100 dark:bg-slate-900 w-64 border-l border-charcoal-200 dark:border-slate-800 font-['Tajawal'] text-right font-medium">
<div class="h-16 flex items-center px-6 border-b border-charcoal-200 dark:border-slate-800">
<div class="flex items-center gap-3">
<div class="w-8 h-8 bg-royal-indigo flex items-center justify-center">
<span class="text-white font-bold font-display-md text-display-md">IRB</span>
</div>
<div>
<div class="font-bold text-indigo-900 dark:text-indigo-400">لجنة المراجعة</div>
<div class="text-xs text-slate-600 dark:text-slate-400">إدارة البحوث الأكاديمية</div>
</div>
</div>
</div>
<div class="flex-1 overflow-y-auto py-6">
<ul class="flex flex-col gap-1">
<li>
<a class="flex items-center gap-3 px-6 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-150 cursor-pointer active:opacity-80" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">dashboard</span>
<span>لوحة القيادة العالمية</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-3 bg-indigo-900 text-white font-bold border-r-4 border-indigo-900 cursor-pointer active:opacity-80 transition-colors duration-150" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">fact_check</span>
<span>زمام الموافقات النهائية</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-150 cursor-pointer active:opacity-80" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">verified</span>
<span>إدارة الشهادات</span>
</a>
</li>
</ul>
</div>
</nav>
<!-- Main Content -->
<main class="mr-64 pt-16 min-h-screen">
<div class="max-w-[1200px] mx-auto p-edge-margin">
<div class="mb-section-stack">
<h2 class="font-h1 text-h1 text-charcoal mb-2">قائمة الاعتماد النهائي</h2>
<p class="font-body-lg text-body-lg text-secondary">المقترحات البحثية التي اجتازت مرحلة المراجعة العلمية وتنتظر الاعتماد النهائي.</p>
</div>
<!-- Action Bar -->
<div class="flex justify-between items-center mb-6 bg-surface p-4 border border-charcoal">
<div class="flex items-center gap-4">
<button class="flex items-center gap-2 font-button text-button bg-paper-white border border-charcoal text-charcoal px-4 py-2 hover:bg-surface-variant transition-colors">
<span class="material-symbols-outlined text-[18px]">filter_list</span>
<span>تصفية</span>
</button>
<div class="font-body-sm text-body-sm text-secondary">
                        يعرض 5 من أصل 12 طلب
                    </div>
</div>
<div>
<button class="flex items-center gap-2 font-button text-button bg-paper-white border border-charcoal text-charcoal px-4 py-2 hover:bg-surface-variant transition-colors">
<span class="material-symbols-outlined text-[18px]">download</span>
<span>تصدير السجل</span>
</button>
</div>
</div>
<!-- Data Table -->
<div class="border border-charcoal bg-paper-white">
<table class="w-full text-right">
<thead class="bg-cool-slate border-b border-charcoal">
<tr>
<th class="py-4 px-6 font-body-lg text-body-lg text-charcoal font-bold w-24">المعرف</th>
<th class="py-4 px-6 font-body-lg text-body-lg text-charcoal font-bold">عنوان البحث</th>
<th class="py-4 px-6 font-body-lg text-body-lg text-charcoal font-bold w-48">قرار المراجع</th>
<th class="py-4 px-6 font-body-lg text-body-lg text-charcoal font-bold w-32">تاريخ المراجعة</th>
<th class="py-4 px-6 font-body-lg text-body-lg text-charcoal font-bold w-40 text-center">الإجراء</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm text-on-surface">
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-4 px-6 font-numeral text-numeral text-secondary">#IRB-2023-041</td>
<td class="py-4 px-6 font-bold text-charcoal">تأثير التعلم النشط على التحصيل الدراسي في مساقات العلوم الحيوية</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 bg-forest text-white px-2 py-1 font-button text-[12px]">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    موصى بالاعتماد
                                </span>
</td>
<td class="py-4 px-6 font-numeral text-numeral text-secondary">15 أكتوبر 2023</td>
<td class="py-4 px-6 text-center">
<button class="font-button text-button bg-royal-indigo text-white px-4 py-2 hover:bg-primary transition-colors w-full">
                                    اعتماد نهائي
                                </button>
</td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-4 px-6 font-numeral text-numeral text-secondary">#IRB-2023-045</td>
<td class="py-4 px-6 font-bold text-charcoal">تحليل أنماط التفاعل الاجتماعي في منصات التعليم الافتراضي</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 bg-forest text-white px-2 py-1 font-button text-[12px]">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    موصى بالاعتماد
                                </span>
</td>
<td class="py-4 px-6 font-numeral text-numeral text-secondary">18 أكتوبر 2023</td>
<td class="py-4 px-6 text-center">
<button class="font-button text-button bg-royal-indigo text-white px-4 py-2 hover:bg-primary transition-colors w-full">
                                    اعتماد نهائي
                                </button>
</td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-4 px-6 font-numeral text-numeral text-secondary">#IRB-2023-052</td>
<td class="py-4 px-6 font-bold text-charcoal">استراتيجيات التكيف النفسي لدى طلبة السنة الأولى في الكليات الصحية</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 border border-charcoal text-charcoal bg-paper-white px-2 py-1 font-button text-[12px]">
<span class="material-symbols-outlined text-[14px]">warning</span>
                                    موافقة مشروطة
                                </span>
</td>
<td class="py-4 px-6 font-numeral text-numeral text-secondary">20 أكتوبر 2023</td>
<td class="py-4 px-6 text-center">
<button class="font-button text-button bg-paper-white border border-charcoal text-charcoal px-4 py-2 hover:bg-surface-variant transition-colors w-full">
                                    مراجعة الشروط
                                </button>
</td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-4 px-6 font-numeral text-numeral text-secondary">#IRB-2023-058</td>
<td class="py-4 px-6 font-bold text-charcoal">تقييم فعالية برامج الدعم الأكاديمي للطلبة المتعثرين</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 bg-forest text-white px-2 py-1 font-button text-[12px]">
<span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    موصى بالاعتماد
                                </span>
</td>
<td class="py-4 px-6 font-numeral text-numeral text-secondary">22 أكتوبر 2023</td>
<td class="py-4 px-6 text-center">
<button class="font-button text-button bg-royal-indigo text-white px-4 py-2 hover:bg-primary transition-colors w-full">
                                    اعتماد نهائي
                                </button>
</td>
</tr>
<tr class="hover:bg-surface transition-colors">
<td class="py-4 px-6 font-numeral text-numeral text-secondary">#IRB-2023-061</td>
<td class="py-4 px-6 font-bold text-charcoal">العوامل المؤثرة على جودة النوم وعلاقتها بالأداء المعرفي</td>
<td class="py-4 px-6">
<span class="inline-flex items-center gap-1.5 bg-crimson text-white px-2 py-1 font-button text-[12px]">
<span class="material-symbols-outlined text-[14px]">error</span>
                                    يحتاج توضيح
                                </span>
</td>
<td class="py-4 px-6 font-numeral text-numeral text-secondary">24 أكتوبر 2023</td>
<td class="py-4 px-6 text-center">
<button class="font-button text-button bg-paper-white border border-charcoal text-charcoal px-4 py-2 hover:bg-surface-variant transition-colors w-full">
                                    طلب توضيح
                                </button>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="mt-6 flex justify-center items-center gap-2">
<button class="w-8 h-8 flex items-center justify-center border border-charcoal bg-paper-white text-secondary hover:bg-surface-variant transition-colors" disabled="">
<span class="material-symbols-outlined text-[18px]">chevron_right</span>
</button>
<button class="w-8 h-8 flex items-center justify-center border-2 border-royal-indigo bg-paper-white text-royal-indigo font-numeral text-numeral font-bold">1</button>
<button class="w-8 h-8 flex items-center justify-center border border-charcoal bg-paper-white text-charcoal font-numeral text-numeral hover:bg-surface-variant transition-colors">2</button>
<button class="w-8 h-8 flex items-center justify-center border border-charcoal bg-paper-white text-charcoal font-numeral text-numeral hover:bg-surface-variant transition-colors">3</button>
<button class="w-8 h-8 flex items-center justify-center border border-charcoal bg-paper-white text-charcoal hover:bg-surface-variant transition-colors">
<span class="material-symbols-outlined text-[18px]">chevron_left</span>
</button>
</div>
</div>
</main>
</body></html>