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
<body class="bg-gray-50 text-gray-900 min-h-screen flex">

<!-- SideNavBar -->
<aside class="fixed inset-y-0 right-0 flex flex-col w-64 bg-white border-l border-gray-200 shadow-sm z-20">
    <div class="px-6 py-6 border-b border-gray-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden text-indigo-700">
             <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-gray-900 leading-tight">مساحة عمل الإحصائي</h2>
            <p class="text-gray-600 text-xs mt-0.5">وحدة البحوث الإكلينيكية</p>
        </div>
    </div>
    <nav class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto">
        <a aria-current="page" class="bg-indigo-700 text-white font-bold flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition-all" href="<?php echo BASE_URL; ?>/officer/sample-size/queue">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">calculate</span>
            الحسابات الحالية
        </a>
        <a class="text-gray-700 flex items-center gap-3 px-4 py-3 mx-2 rounded-lg hover:bg-gray-100 transition-all" href="<?php echo BASE_URL; ?>/officer/sample-size/archives">
            <span class="material-symbols-outlined">inventory_2</span>
            الأرشيف
        </a>
    </nav>
    <div class="p-4 border-t border-gray-200">
        <a href="<?php echo BASE_URL; ?>/logout" class="text-red-600 flex items-center gap-3 px-4 py-3 hover:bg-red-50 rounded-lg transition-all">
            <span class="material-symbols-outlined">logout</span>
            تسجيل الخروج
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="flex-1 mr-64 flex flex-col min-h-screen">
    <!-- TopAppBar -->
    <header class="flex justify-between items-center px-8 h-16 w-full bg-white border-b border-gray-200 z-10 sticky top-0 shadow-sm">
        <h1 class="text-xl font-bold text-gray-900">مسؤول حجم العينة</h1>
        <div class="flex items-center gap-6 text-gray-600">
            <span class="material-symbols-outlined cursor-pointer hover:text-indigo-700 transition-colors">notifications</span>
            <span class="material-symbols-outlined cursor-pointer hover:text-indigo-700 transition-colors">help_outline</span>
            <div class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </header>

    <!-- Canvas Content -->
    <div class="flex-1 p-8 bg-gray-50 max-w-7xl mx-auto w-full">
        <div class="mb-8">
            <h2 class="font-h1 text-gray-900 mb-2 text-3xl">قائمة انتظار حساب حجم العينة</h2>
            <p class="text-gray-600">البحوث التي أكملت الدفع المبدئي وبانتظار الحساب الإحصائي.</p>
        </div>

        <?php if (isset($_SESSION['officer_success'])): ?>
            <div class="mb-6 p-4 border border-green-600 bg-green-50 text-green-700 rounded-lg font-bold flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <?= htmlspecialchars($_SESSION['officer_success']) ?>
                <?php unset($_SESSION['officer_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-gray-600 font-bold">الرقم التسلسلي</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">عنوان البحث</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">تاريخ الدفع</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">الحالة</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-900">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-600">لا توجد أبحاث في قائمة الانتظار حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-5 font-numeral font-bold text-indigo-700"><?= htmlspecialchars($sub['serial_number'] ?: '---') ?></td>
                            <td class="px-6 py-5 font-bold max-w-md truncate" title="<?= htmlspecialchars($sub['title']) ?>">
                                <?= htmlspecialchars($sub['title']) ?>
                                <div class="text-xs text-gray-600 font-normal"><?= htmlspecialchars($sub['principal_investigator']) ?></div>
                            </td>
                            <td class="px-6 py-5 text-gray-600"><?= date('d أكتوبر Y', strtotime($sub['created_at'])) ?></td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-indigo-700 border border-indigo-200 bg-indigo-50 rounded-lg">بانتظار الحساب</span>
                            </td>
                            <td class="px-6 py-5">
                                <a href="<?php echo BASE_URL; ?>/officer/sample-size/input/<?= $sub['id'] ?>"
                                   class="inline-flex items-center gap-2 bg-indigo-700 text-white px-4 py-2 hover:bg-indigo-800 transition-all font-bold text-sm rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5">
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

        <div class="mt-4 flex justify-between items-center text-gray-600 text-sm">
            <span>إجمالي الطلبات بانتظار الحساب: <span class="font-bold text-gray-900"><?= count($submissions) ?></span></span>
        </div>
    </div>
</main>
</body>
</html>
