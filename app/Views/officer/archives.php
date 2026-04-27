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
                      "primary": "#4338ca",
                      "crimson": "#991B1B",
                      "slate-gray": "#4b5563",
                      "secondary": "#4b5563",
                      "paper-white": "#f9fafb",
                      "charcoal": "#111827",
                      "royal-indigo": "#4338ca",
                      "cool-slate": "#f3f4f6",
                      "surface-variant": "#e5e7eb",
                      "surface-container-low": "#f3f4f6",
                      "surface-container": "#e5e7eb"
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
<body class="bg-gray-50 text-gray-900 min-h-screen flex">

<!-- SideNavBar -->
<aside class="fixed inset-y-0 right-0 flex flex-col w-64 bg-gray-50 border-l border-gray-200 shadow-none z-20">
    <div class="px-6 py-6 border-b border-gray-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border border-gray-200 text-indigo-700 text-2xl">
             <span class="material-symbols-outlined">account_balance</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-indigo-900 leading-tight">مساحة عمل الإحصائي</h2>
            <p class="text-gray-500 text-xs mt-0.5">وحدة البحوث الإكلينيكية</p>
        </div>
    </div>
    <nav class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto">
        <a class="text-gray-700 flex items-center gap-3 px-4 py-3 mx-2 rounded-sm hover:bg-gray-200 transition-none" href="<?php echo BASE_URL; ?>/officer/sample-size/queue">
            <span class="material-symbols-outlined">calculate</span>
            الحسابات الحالية
        </a>
        <a aria-current="page" class="bg-indigo-700 text-white font-bold flex items-center gap-3 px-4 py-3 mx-2 rounded-sm" href="<?php echo BASE_URL; ?>/officer/sample-size/archives">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
            الأرشيف
        </a>
    </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 mr-64 flex flex-col min-h-screen">
    <header class="flex justify-between items-center px-8 h-16 w-full bg-white border-b border-gray-200 z-10 sticky top-0">
        <h1 class="text-xl font-black text-indigo-900 uppercase tracking-widest">أرشيف الإحصائي</h1>
        <div class="flex items-center gap-6 text-gray-500">
            <span class="material-symbols-outlined">notifications</span>
            <div class="w-8 h-8 rounded-full bg-gray-200 border border-gray-300 overflow-hidden flex items-center justify-center">
                <span class="material-symbols-outlined">person</span>
            </div>
        </div>
    </header>

    <div class="flex-1 p-edge-margin bg-gray-50 max-w-container-max mx-auto w-full">
        <div class="mb-section-stack text-right">
            <h2 class="font-h1 text-gray-900 mb-2 text-3xl">سجل الحسابات المكتملة</h2>
            <p class="text-gray-600">قائمة بالأبحاث التي تم تسجيل حجم عينتها بنجاح.</p>
        </div>

        <div class="w-full border-t border-b border-gray-200 bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-gray-600 font-bold">الرقم التسلسلي</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">عنوان البحث</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">حجم العينة</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">تاريخ المعالجة</th>
                        <th class="px-6 py-4 text-gray-600 font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="text-gray-900">
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-600">الأرشيف فارغ حالياً.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-5 font-numeral font-bold"><?= htmlspecialchars($sub['serial_number']) ?></td>
                            <td class="px-6 py-5 font-bold max-w-md truncate" title="<?= htmlspecialchars($sub['title']) ?>">
                                <?= htmlspecialchars($sub['title']) ?>
                                <div class="text-xs text-gray-600 font-normal"><?= htmlspecialchars($sub['student_name']) ?></div>
                            </td>
                            <td class="px-6 py-5 font-numeral font-bold text-indigo-700">
                                <?= (int)$sub['sample_size'] ?> مشارك
                            </td>
                            <td class="px-6 py-5 text-gray-600"><?= date('d أكتوبر Y', strtotime($sub['updated_at'])) ?></td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-green-800 border border-green-300 bg-green-50 uppercase tracking-wider">مكتمل</span>
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
