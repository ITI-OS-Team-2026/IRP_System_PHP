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
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
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
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-900 text-white font-bold">
                <span class="material-symbols-outlined">fact_check</span>
                زمام الموافقات النهائية
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/certificates" class="flex items-center gap-2 px-3 py-2 rounded text-slate-700 hover:bg-slate-100">
                <span class="material-symbols-outlined">workspace_premium</span>
                إدارة الشهادات
            </a>
        </nav>
        <div class="mt-auto pt-4 border-t border-slate-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-indigo-900">إصدار الشهادة النهائية</h1>
                <p class="text-slate-600 mt-1">الرقم التسلسلي: <?= htmlspecialchars($submission['serial_number'] ?: ('SUB-' . (int) $submission['id']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/committee/approvals" class="inline-flex items-center gap-2 text-slate-700 hover:text-indigo-900">
                <span class="material-symbols-outlined">arrow_back</span>
                العودة
            </a>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">بيانات البحث</h2>
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold">العنوان:</span> <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">الباحث الرئيسي:</span> <?= htmlspecialchars($submission['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">اسم الطالب:</span> <?= htmlspecialchars($submission['student_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">القسم:</span> <?= htmlspecialchars($submission['department'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">التخصص:</span> <?= htmlspecialchars($submission['specialty'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">ملاحظات المراجع</h2>
                <p class="text-sm leading-7 text-slate-700"><?= nl2br(htmlspecialchars($submission['feedback_notes'] ?: 'لا توجد ملاحظات إضافية.', ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </section>

        <section class="bg-white border-2 border-indigo-900 rounded-lg p-6">
            <?php if (!empty($submission['certificate_number'])): ?>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-green-700">تم إصدار الشهادة</h3>
                        <p class="text-slate-700 mt-1">رقم الشهادة: <span class="font-bold"><?= htmlspecialchars($submission['certificate_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $submission['id'] ?>" target="_blank" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
                        <span class="material-symbols-outlined">download</span>
                        تحميل الشهادة
                    </a>
                </div>
            <?php else: ?>
                <h3 class="text-xl font-bold text-indigo-900 mb-2">اعتماد نهائي وإصدار شهادة</h3>
                <p class="text-slate-700 mb-5">بعد الإصدار ستنتقل هذه الشهادة إلى قسم إدارة الشهادات، ويمكن للطالب تنزيلها.</p>
                <form method="POST">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
                        <span class="material-symbols-outlined">verified</span>
                        إصدار الشهادة
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
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
    <title>إصدار الشهادة - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
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
            <a href="<?php echo BASE_URL; ?>/committee/dashboard" class="flex items-center gap-2 px-3 py-2 rounded text-slate-700 hover:bg-slate-100">
                <span class="material-symbols-outlined">fact_check</span>
                طلبات الاعتماد النهائي
            </a>
            <a href="<?php echo BASE_URL; ?>/committee/certificate/<?= (int) $submission['id'] ?>" class="flex items-center gap-2 px-3 py-2 rounded bg-indigo-900 text-white font-bold">
                <span class="material-symbols-outlined">workspace_premium</span>
                إصدار الشهادة
            </a>
        </nav>

        <div class="mt-auto pt-4 border-t border-slate-200">
            <a href="<?php echo BASE_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 rounded text-red-600 hover:bg-red-50">
                <span class="material-symbols-outlined">logout</span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-indigo-900">الاعتماد النهائي وإصدار الشهادة</h1>
                <p class="text-slate-600 mt-1">الرقم التسلسلي: <?= htmlspecialchars($submission['serial_number'] ?: ('SUB-' . (int) $submission['id']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/committee/dashboard" class="inline-flex items-center gap-2 text-slate-700 hover:text-indigo-900">
                <span class="material-symbols-outlined">arrow_back</span>
                العودة
            </a>
        </header>

        <?php if ($successMessage !== ''): ?>
            <div class="rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">بيانات البحث</h2>
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold">العنوان:</span> <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">الباحث الرئيسي:</span> <?= htmlspecialchars($submission['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">اسم الطالب:</span> <?= htmlspecialchars($submission['student_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">القسم:</span> <?= htmlspecialchars($submission['department'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">التخصص:</span> <?= htmlspecialchars($submission['specialty'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">ملاحظات المراجع</h2>
                <p class="text-sm leading-7 text-slate-700">
                    <?= nl2br(htmlspecialchars($submission['feedback_notes'] ?: 'لا توجد ملاحظات إضافية.', ENT_QUOTES, 'UTF-8')) ?>
                </p>
            </div>
        </section>

        <section class="bg-white border-2 border-indigo-900 rounded-lg p-6">
            <?php if (!empty($submission['certificate_number'])): ?>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-green-700">تم إصدار الشهادة</h3>
                        <p class="text-slate-700 mt-1">رقم الشهادة: <span class="font-bold"><?= htmlspecialchars($submission['certificate_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $submission['id'] ?>" target="_blank" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
                        <span class="material-symbols-outlined">download</span>
                        تحميل الشهادة
                    </a>
                </div>
            <?php else: ?>
                <h3 class="text-xl font-bold text-indigo-900 mb-2">إصدار الشهادة النهائية</h3>
                <p class="text-slate-700 mb-5">بعد الإصدار، سيظهر زر التحميل للطالب في لوحة التحكم.</p>
                <form method="POST">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
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
    <title>الاعتماد النهائي وإصدار الشهادة - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <main class="max-w-5xl mx-auto p-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-indigo-900">الاعتماد النهائي وإصدار الشهادة</h1>
                <p class="text-slate-600">المعرف: <?= htmlspecialchars($submission['serial_number'] ?: ('SUB-' . (int) $submission['id']), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/committee/dashboard" class="inline-flex items-center gap-2 text-slate-700 hover:text-indigo-900">
                <span class="material-symbols-outlined">arrow_back</span>
                العودة للقائمة
            </a>
        </div>

        <?php if ($successMessage !== ''): ?>
            <div class="rounded border border-green-300 bg-green-50 text-green-800 px-4 py-3"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <div class="rounded border border-red-300 bg-red-50 text-red-800 px-4 py-3"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">بيانات البحث</h2>
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold">العنوان:</span> <?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">الباحث الرئيسي:</span> <?= htmlspecialchars($submission['principal_investigator'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">اسم الطالب:</span> <?= htmlspecialchars($submission['student_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">القسم:</span> <?= htmlspecialchars($submission['department'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><span class="font-bold">التخصص:</span> <?= htmlspecialchars($submission['specialty'] ?: 'غير محدد', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-5">
                <h2 class="font-bold text-lg mb-4">نتيجة المراجعة</h2>
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold">قرار المراجع:</span> موافقة</p>
                    <p><span class="font-bold">تاريخ المراجعة:</span> <?= htmlspecialchars($submission['reviewed_at'] ? date('Y/m/d', strtotime($submission['reviewed_at'])) : '-', ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="leading-7"><span class="font-bold">ملاحظات المراجع:</span><br><?= nl2br(htmlspecialchars($submission['feedback_notes'] ?: 'لا توجد ملاحظات إضافية.', ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
            </div>
        </section>

        <section class="bg-white border-2 border-indigo-900 rounded-lg p-6">
            <?php if (!empty($submission['certificate_number'])): ?>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-green-700">تم إصدار الشهادة</h3>
                        <p class="text-slate-700 mt-1">رقم الشهادة: <span class="font-bold"><?= htmlspecialchars($submission['certificate_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/certificate/download/<?= (int) $submission['id'] ?>" target="_blank" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
                        <span class="material-symbols-outlined">download</span>
                        تحميل الشهادة (PDF)
                    </a>
                </div>
            <?php else: ?>
                <h3 class="text-xl font-bold text-indigo-900 mb-2">إصدار الشهادة النهائية</h3>
                <p class="text-slate-700 mb-5">سيتم إنشاء شهادة اعتماد للطالب ويمكنه تنزيلها لاحقًا من لوحة التحكم.</p>
                <form method="POST">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded hover:bg-indigo-800">
                        <span class="material-symbols-outlined">verified</span>
                        إصدار الموافقة النهائية وإنشاء الشهادة
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<!DOCTYPE html>

<html dir="rtl" lang="ar"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>الاعتماد النهائي وإصدار الشهادة (Approval &amp; Certificate View)</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-feature-settings: 'liga';
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
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
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "section-stack": "3rem",
                        "edge-margin": "2rem",
                        "gutter": "1.5rem",
                        "form-gap": "1.25rem",
                        "container-max": "1200px"
                    },
                    "fontFamily": {
                        "h1": ["Amiri"],
                        "body-lg": ["Tajawal"],
                        "button": ["Tajawal"],
                        "body-sm": ["Tajawal"],
                        "numeral": ["Tajawal"],
                        "display-md": ["Amiri"],
                        "display-lg": ["Amiri"]
                    },
                    "fontSize": {
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
<body class="bg-paper-white text-on-surface font-body-lg antialiased min-h-screen">
<!-- TopAppBar -->
<header class="fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-6 z-50 mr-64 bg-white dark:bg-slate-950 border-b-2 border-slate-900 dark:border-slate-100 font-['Tajawal'] text-right transition-all duration-200 flat no shadows">
<div class="flex items-center gap-4">
<h1 class="text-lg font-black text-indigo-900 dark:text-indigo-300">نظام إدارة لجنة المراجعة المؤسسية</h1>
</div>
<div class="flex items-center gap-6">
<div class="flex gap-4 text-indigo-900 dark:text-indigo-400">
<button class="hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-200">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
</button>
<button class="hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-200">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
</button>
</div>
<img alt="ملف المستخدم الأكاديمي" class="w-8 h-8 rounded-full border border-charcoal" data-alt="professional headshot of an academic researcher with a subtle neutral background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCa372Ep4gmVdkhp9t45noSEsP_yJgdKQTOXCgkyRefYpnPrnWBPqhmHTvigWFV5oqB2-vfsNWTIK9VQTVi80rsD2bmKJBFemI_ToPcaNn1OOY3lLt2ruP-Jz_-zxuY2gDi6U1XWRO78Z8z0BC8EfNPD-tBXdoqlfntegIaAiu-TZARWKdpgSIpgWBimJgG1WKyNk57W2SMAaiuGYyx5FLIhr7rdPkPlwhrclJUHj1vgv72HOux0nluRTr3xFdmzbAcpZY3cai4xjpS"/>
</div>
</header>
<!-- SideNavBar -->
<nav class="fixed right-0 top-0 h-full z-40 flex flex-col w-64 border-l border-charcoal-900/10 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 font-['Tajawal'] text-right font-medium text-indigo-900 dark:text-indigo-400 flat no shadows">
<div class="p-6 border-b border-charcoal-200 dark:border-slate-800">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-3xl" data-icon="account_balance">account_balance</span>
<div>
<h2 class="text-xl font-bold text-indigo-900 dark:text-indigo-400">لجنة المراجعة</h2>
<p class="text-sm opacity-80">إدارة البحوث الأكاديمية</p>
</div>
</div>
</div>
<div class="flex-1 overflow-y-auto py-4">
<ul class="space-y-1">
<li>
<a class="flex items-center gap-3 px-6 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-150 cursor-pointer active:opacity-80" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>لوحة القيادة العالمية</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-150 cursor-pointer active:opacity-80" href="#">
<span class="material-symbols-outlined" data-icon="fact_check">fact_check</span>
<span>زمام الموافقات النهائية</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-3 bg-indigo-900 text-white font-bold border-r-4 border-indigo-900 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors duration-150 cursor-pointer active:opacity-80" href="#">
<span class="material-symbols-outlined" data-icon="verified">verified</span>
<span>إدارة الشهادات</span>
</a>
</li>
</ul>
</div>
</nav>
<!-- Main Content Area -->
<main class="mr-64 pt-16 min-h-screen">
<div class="max-w-[container-max] mx-auto px-edge-margin py-section-stack">
<!-- Header Section -->
<div class="mb-gutter pb-4 border-b border-charcoal">
<div class="flex justify-between items-end">
<div>
<p class="font-body-sm text-slate-gray mb-2">معرف المراجعة: #IRB-2023-0892</p>
<h1 class="font-h1 text-h1 text-charcoal">الاعتماد النهائي وإصدار الشهادة</h1>
</div>
<div class="flex gap-4">
<span class="inline-flex items-center px-3 py-1 bg-cool-slate border border-charcoal text-charcoal font-button text-button rounded-none">
                            قيد المراجعة النهائية
                        </span>
</div>
</div>
</div>
<!-- Bento Grid Layout for Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-section-stack">
<!-- Research Details Card -->
<div class="md:col-span-2 bg-cool-slate border border-charcoal p-6 rounded-none">
<h2 class="font-body-lg text-body-lg font-bold border-b border-charcoal pb-3 mb-4">ملخص البحث الأكاديمي</h2>
<div class="space-y-4">
<div>
<p class="font-body-sm text-slate-gray">عنوان الدراسة</p>
<p class="font-body-lg text-charcoal">تأثير التدخلات السلوكية المعرفية على جودة النوم لدى مرضى السكري من النوع الثاني: دراسة معشاة ذات شواهد.</p>
</div>
<div class="grid grid-cols-2 gap-4">
<div>
<p class="font-body-sm text-slate-gray">الباحث الرئيسي</p>
<p class="font-body-lg text-charcoal">د. فاطمة الزهراء محمود</p>
</div>
<div>
<p class="font-body-sm text-slate-gray">القسم / الكلية</p>
<p class="font-body-lg text-charcoal">كلية الطب، قسم الأمراض الباطنة</p>
</div>
</div>
<div>
<p class="font-body-sm text-slate-gray">تاريخ تقديم الطلب</p>
<p class="font-numeral text-numeral text-charcoal">١٥ أكتوبر ٢٠٢٣</p>
</div>
</div>
</div>
<!-- Reviewer Verdict Card -->
<div class="md:col-span-1 bg-surface border border-charcoal p-6 rounded-none flex flex-col h-full">
<h2 class="font-body-lg text-body-lg font-bold border-b border-charcoal pb-3 mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-forest" data-icon="check_circle" data-weight="fill" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        حكم المراجع
                    </h2>
<div class="flex-grow">
<p class="font-body-sm text-charcoal leading-relaxed">
                            تمت مراجعة بروتوكول الدراسة بعناية. منهجية البحث سليمة، وتمت معالجة جميع المخاوف الأخلاقية المتعلقة بموافقة المرضى وسرية البيانات بشكل مناسب. نوصي بالموافقة النهائية دون تعديلات إضافية.
                        </p>
</div>
<div class="mt-4 pt-4 border-t border-charcoal">
<p class="font-body-sm text-slate-gray">المراجع المستقل</p>
<p class="font-body-lg text-charcoal font-bold">أ.د. يوسف العبدالله</p>
</div>
</div>
</div>
<!-- Action Area -->
<div class="bg-paper-white border-2 border-charcoal p-8 rounded-none flex flex-col items-center justify-center text-center">
<span class="material-symbols-outlined text-display-md text-charcoal mb-4" data-icon="policy">policy</span>
<h3 class="font-display-md text-display-md text-charcoal mb-2">إصدار قرار الاعتماد</h3>
<p class="font-body-lg text-slate-gray max-w-2xl mb-8">
                    بالنقر على الزر أدناه، فإنك بصفتك مدير اللجنة تصادق رسمياً على هذا البحث للاستمرار. سيتم إنشاء شهادة موافقة رقمية وإرسالها إلى الباحث الرئيسي.
                </p>
<button class="bg-royal-indigo hover:bg-primary text-white font-button text-button py-4 px-8 rounded-none flex items-center gap-3 transition-colors">
<span class="material-symbols-outlined" data-icon="verified" data-weight="fill" style="font-variation-settings: 'FILL' 1;">verified</span>
                    إصدار الموافقة النهائية وإنشاء الشهادة
                </button>
</div>
<!-- Success State Simulation (Commented out logically, showing design) -->
<div class="mt-section-stack border-2 border-forest bg-cool-slate p-8 rounded-none relative overflow-hidden">
<div class="absolute top-0 right-0 w-2 h-full bg-forest"></div>
<div class="flex items-start gap-6">
<span class="material-symbols-outlined text-display-lg text-forest mt-1" data-icon="task_alt" data-weight="fill" style="font-variation-settings: 'FILL' 1;">task_alt</span>
<div>
<h3 class="font-display-md text-display-md text-charcoal mb-2">تم الاعتماد بنجاح</h3>
<p class="font-body-lg text-slate-gray mb-6">
                            تم تسجيل الموافقة في السجل الرسمي للجنة. تم إنشاء شهادة الاعتماد (IRB-CERT-0892) وهي جاهزة للتحميل.
                        </p>
<div class="flex gap-4">
<button class="bg-paper-white border border-charcoal hover:bg-surface-variant text-charcoal font-button text-button py-3 px-6 rounded-none flex items-center gap-2 transition-colors">
<span class="material-symbols-outlined" data-icon="download">download</span>
                                تحميل الشهادة الرقمية (PDF)
                            </button>
<button class="bg-paper-white border border-charcoal hover:bg-surface-variant text-charcoal font-button text-button py-3 px-6 rounded-none flex items-center gap-2 transition-colors">
<span class="material-symbols-outlined" data-icon="mail">mail</span>
                                إرسال للباحث
                            </button>
</div>
</div>
</div>
</div>
</div>
</main>
</body></html>