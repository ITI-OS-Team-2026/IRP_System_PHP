<?php
$pageTitle = 'أرشيف حسابات حجم العينة - مساحة عمل الإحصائي';
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
                      "secondary": "#5c5f61",
                      "paper-white": "#FDFDFC",
                      "charcoal": "#1A1C1E",
                      "royal-indigo": "#312E81",
                      "cool-slate": "#F8FAFC",
                      "surface-variant": "#e2e2e5",
                      "surface-container-low": "#f3f3f6"
              },
              "spacing": {
                      "section-stack": "3rem",
                      "container-max": "1200px",
                      "edge-margin": "2rem"
              },
              "fontFamily": {
                      "body-sm": ["Tajawal"],
                      "h1": ["Amiri"],
                      "numeral": ["Tajawal"]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Tajawal', sans-serif; }
        .font-h1 { font-family: 'Amiri', serif; font-size: 24px; font-weight: 700; }
    </style>
</head>
<body class="bg-paper-white text-charcoal min-h-screen flex">

<!-- SideNavBar -->
<aside class="fixed inset-y-0 right-0 flex flex-col w-64 bg-slate-50 border-l border-zinc-800 shadow-none z-20">
    <div class="px-6 py-6 border-b border-zinc-800 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center overflow-hidden border border-charcoal text-royal-indigo text-2xl">
             <span class="material-symbols-outlined">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-indigo-900 leading-tight">مساحة عمل الإحصائي</h2>
            <p class="text-zinc-500 text-xs mt-0.5">وحدة البحوث الإكلينيكية</p>
        </div>
    </div>
    <nav class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto">
        <a class="text-zinc-700 flex items-center gap-3 px-4 py-3 mx-2 rounded-sm hover:bg-zinc-200 transition-none" href="<?php echo BASE_URL; ?>/officer/sample-size/queue">
            <span class="material-symbols-outlined">calculate</span>
            الحسابات الحالية
        </a>
        <a aria-current="page" class="bg-indigo-900 text-white font-bold flex items-center gap-3 px-4 py-3 mx-2 rounded-sm" href="<?php echo BASE_URL; ?>/officer/sample-size/archives">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
            الأرشيف
        </a>
    </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 mr-64 flex flex-col min-h-screen">
    <header class="flex justify-between items-center px-8 h-16 w-full bg-white border-b-2 border-zinc-800 z-10 sticky top-0">
        <h1 class="text-xl font-black text-indigo-900 uppercase tracking-widest">أرشيف الإحصائي</h1>
        <div class="flex items-center gap-6 text-zinc-500">
            <span class="material-symbols-outlined">notifications</span>
            <div class="w-8 h-8 rounded-full bg-zinc-200 border border-charcoal overflow-hidden flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </header>

    <div class="flex-1 p-edge-margin bg-paper-white max-w-container-max mx-auto w-full">
        <div class="mb-section-stack text-right">
            <h2 class="font-h1 text-charcoal mb-2 text-3xl">سجل الحسابات المكتملة</h2>
            <p class="text-slate-gray">قائمة بالأبحاث التي تم تسجيل حجم عينتها بنجاح.</p>
        </div>

        <div class="w-full border-t border-b border-charcoal bg-paper-white">
            <table class="w-full text-right">
                <thead class="bg-cool-slate border-b border-charcoal">
                    <tr>
                        <th class="px-6 py-4 text-secondary font-bold">الرقم التسلسلي</th>
                        <th class="px-6 py-4 text-secondary font-bold">عنوان البحث</th>
                        <th class="px-6 py-4 text-secondary font-bold">حجم العينة</th>
                        <th class="px-6 py-4 text-secondary font-bold">تاريخ المعالجة</th>
                        <th class="px-6 py-4 text-secondary font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="text-charcoal">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-gray">الأرشيف فارغ حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                        <tr class="border-b border-surface-variant hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-5 font-numeral font-bold"><?= htmlspecialchars($sub['serial_number']) ?></td>
                            <td class="px-6 py-5 font-bold max-w-md truncate" title="<?= htmlspecialchars($sub['title']) ?>">
                                <?= htmlspecialchars($sub['title']) ?>
                                <div class="text-xs text-slate-gray font-normal"><?= htmlspecialchars($sub['student_name']) ?></div>
                            </td>
                            <td class="px-6 py-5 font-numeral font-bold text-royal-indigo">
                                <?= (int)$sub['sample_size'] ?> مشارك
                            </td>
                            <td class="px-6 py-5 text-secondary"><?= date('d أكتوبر Y', strtotime($sub['updated_at'])) ?></td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-forest border border-forest bg-paper-white uppercase tracking-wider">مكتمل</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
