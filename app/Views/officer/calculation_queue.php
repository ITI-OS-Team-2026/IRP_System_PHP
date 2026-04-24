<?php
$pageTitle = 'لوحة تحكم الإحصائي - قائمة الانتظار';
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
                      "surface-container-low": "#f3f3f6",
                      "surface-container": "#eeeef0"
              },
              "spacing": {
                      "section-stack": "3rem",
                      "container-max": "1200px",
                      "edge-margin": "2rem"
              },
              "fontFamily": {
                      "body-sm": ["Tajawal"],
                      "numeral": ["Tajawal"],
                      "h1": ["Amiri"],
                      "body-lg": ["Tajawal"],
                      "button": ["Tajawal"],
                      "display-md": ["Amiri"]
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
        <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center overflow-hidden border border-charcoal text-royal-indigo">
             <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-indigo-900 leading-tight">مساحة عمل الإحصائي</h2>
            <p class="text-zinc-500 text-xs mt-0.5">وحدة البحوث الإكلينيكية</p>
        </div>
    </div>
    <nav class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto">
        <a aria-current="page" class="bg-indigo-900 text-white font-bold flex items-center gap-3 px-4 py-3 mx-2 rounded-sm" href="<?php echo BASE_URL; ?>/officer/sample-size/queue">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">calculate</span>
            الحسابات الحالية
        </a>
        <a class="text-zinc-700 flex items-center gap-3 px-4 py-3 mx-2 rounded-sm hover:bg-zinc-200 transition-none" href="<?php echo BASE_URL; ?>/officer/sample-size/archives">
            <span class="material-symbols-outlined">inventory_2</span>
            الأرشيف
        </a>
    </nav>
    <div class="p-4 border-t border-zinc-200">
        <a href="<?php echo BASE_URL; ?>/logout" class="text-crimson flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-none">
            <span class="material-symbols-outlined">logout</span>
            تسجيل الخروج
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="flex-1 mr-64 flex flex-col min-h-screen">
    <!-- TopAppBar -->
    <header class="flex justify-between items-center px-8 h-16 w-full bg-white border-b-2 border-zinc-800 z-10 sticky top-0">
        <h1 class="text-xl font-black text-indigo-900 uppercase tracking-widest">مسؤول حجم العينة</h1>
        <div class="flex items-center gap-6 text-zinc-500">
            <span class="material-symbols-outlined">notifications</span>
            <span class="material-symbols-outlined">help_outline</span>
            <div class="w-8 h-8 rounded-full bg-zinc-200 border border-charcoal overflow-hidden flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </header>

    <!-- Canvas Content -->
    <div class="flex-1 p-edge-margin bg-paper-white max-w-container-max mx-auto w-full">
        <div class="mb-section-stack">
            <h2 class="font-h1 text-charcoal mb-2 text-3xl">قائمة انتظار حساب حجم العينة</h2>
            <p class="text-slate-gray">البحوث التي أكملت الدفع المبدئي وبانتظار الحساب الإحصائي.</p>
        </div>

        <?php if (isset($_SESSION['officer_success'])): ?>
            <div class="mb-6 p-4 border-2 border-green-600 bg-green-50 text-green-700 font-bold flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <?= htmlspecialchars($_SESSION['officer_success']) ?>
                <?php unset($_SESSION['officer_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="w-full border-t border-b border-charcoal bg-paper-white">
            <table class="w-full text-right">
                <thead class="bg-cool-slate border-b border-charcoal">
                    <tr>
                        <th class="px-6 py-4 text-secondary font-bold">الرقم التسلسلي</th>
                        <th class="px-6 py-4 text-secondary font-bold">عنوان البحث</th>
                        <th class="px-6 py-4 text-secondary font-bold">تاريخ الدفع</th>
                        <th class="px-6 py-4 text-secondary font-bold">الحالة</th>
                        <th class="px-6 py-4 text-secondary font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="text-charcoal">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-gray">لا توجد أبحاث في قائمة الانتظار حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                        <tr class="border-b border-surface-variant hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-5 font-numeral font-bold"><?= htmlspecialchars($sub['serial_number'] ?: '---') ?></td>
                            <td class="px-6 py-5 font-bold max-w-md truncate" title="<?= htmlspecialchars($sub['title']) ?>">
                                <?= htmlspecialchars($sub['title']) ?>
                                <div class="text-xs text-slate-gray font-normal"><?= htmlspecialchars($sub['principal_investigator']) ?></div>
                            </td>
                            <td class="px-6 py-5 text-secondary"><?= date('d أكتوبر Y', strtotime($sub['created_at'])) ?></td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-royal-indigo border border-royal-indigo bg-paper-white uppercase tracking-wider">بانتظار الحساب</span>
                            </td>
                            <td class="px-6 py-5">
                                <a href="<?php echo BASE_URL; ?>/officer/sample-size/input/<?= $sub['id'] ?>" 
                                   class="inline-flex items-center gap-2 bg-royal-indigo text-white px-4 py-2 hover:bg-indigo-900 transition-colors font-bold text-sm">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">functions</span>
                                    حساب الآن
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center text-secondary text-sm">
            <span>إجمالي الطلبات بانتظار الحساب: <span class="font-bold text-charcoal"><?= count($submissions) ?></span></span>
        </div>
    </div>
</main>
</body>
</html>
