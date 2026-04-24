<?php
$pageTitle = 'تعيين المراجعين - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'reviewer_assignment';
$adminPageHeading = 'تعيين المراجعين';
$adminPageSubtitle = 'إدارة توزيع الأبحاث على المراجعين المختصين';
?>

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<div class="flex flex-wrap justify-between items-end border-b border-charcoal pb-4 mb-2 gap-4">
<div>
<h1 class="font-h1 text-h1 text-charcoal mb-2">تعيين المراجعين</h1>
<p class="font-body-lg text-body-lg text-slate-gray">قائمة بالأبحاث المكتملة الدفع والجاهزة لتعيين المراجعين المختصين.</p>
</div>
<div class="flex gap-4">
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-2.5 text-outline">search</span>
<input class="pl-4 pr-10 py-2 border border-charcoal focus:border-royal-indigo focus:border-2 focus:ring-0 bg-paper-white outline-none font-body-sm text-body-sm w-64" placeholder="بحث برقم التسلسل..." type="text"/>
</div>
<button class="bg-paper-white text-charcoal border border-charcoal px-4 py-2 font-button text-button hover:bg-surface-dim transition-colors flex items-center gap-2">
<span class="material-symbols-outlined">filter_list</span>
تصفية
</button>
</div>
</div>

<div class="bg-white border border-charcoal rounded-xl shadow-[0_2px_12px_rgba(15,23,42,0.05)] overflow-hidden">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-cool-slate border-b border-charcoal font-body-sm text-body-sm text-charcoal font-bold">
<th class="py-4 px-6 text-right w-32">الرقم التسلسلي</th>
<th class="py-4 px-6 text-right">عنوان البحث</th>
<th class="py-4 px-6 text-center w-32">حالة الدفع</th>
<th class="py-4 px-6 text-right w-64">المراجع المعين</th>
<th class="py-4 px-6 text-center w-32">الإجراءات</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm">
<tr class="border-b border-charcoal hover:bg-surface-container-low transition-colors">
<td class="py-4 px-6 font-numeral text-numeral">IRB-2023-0142</td>
<td class="py-4 px-6 font-medium">دراسة استرجاعية حول مضاعفات الجراحة التنظيرية للقولون في المرضى كبار السن</td>
<td class="py-4 px-6 text-center"><span class="inline-block px-3 py-1 bg-forest text-on-primary font-bold text-xs">مدفوع</span></td>
<td class="py-4 px-6">
<div class="relative">
<select class="w-full appearance-none bg-paper-white border border-charcoal focus:border-royal-indigo focus:border-2 focus:ring-0 py-2 pl-8 pr-3 font-body-sm text-body-sm outline-none cursor-pointer">
<option disabled selected value="">اختر مراجعاً...</option>
<option value="1">د. أحمد عبد الله (جراحة عامة)</option>
<option value="2">د. سارة محمد (باطنية)</option>
<option value="3">د. خالد الرويلي (أخلاقيات طبية)</option>
</select>
<span class="material-symbols-outlined absolute left-2 top-2.5 text-outline pointer-events-none">expand_more</span>
</div>
</td>
<td class="py-4 px-6 text-center"><button class="bg-royal-indigo text-on-primary px-4 py-2 font-button text-button hover:bg-primary transition-colors opacity-50 cursor-not-allowed w-full" disabled>حفظ</button></td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface-container-low transition-colors">
<td class="py-4 px-6 font-numeral text-numeral">IRB-2023-0145</td>
<td class="py-4 px-6 font-medium">تأثير العلاج المعرفي السلوكي المدمج مع الواقع الافتراضي على اضطراب القلق الاجتماعي</td>
<td class="py-4 px-6 text-center"><span class="inline-block px-3 py-1 bg-forest text-on-primary font-bold text-xs">مدفوع</span></td>
<td class="py-4 px-6">
<div class="relative">
<select class="w-full appearance-none bg-paper-white border border-charcoal focus:border-royal-indigo focus:border-2 focus:ring-0 py-2 pl-8 pr-3 font-body-sm text-body-sm outline-none cursor-pointer">
<option value="1">د. ليلى الشمري (طب نفسي)</option>
<option value="2">د. أحمد عبد الله (جراحة عامة)</option>
<option value="3">د. خالد الرويلي (أخلاقيات طبية)</option>
</select>
<span class="material-symbols-outlined absolute left-2 top-2.5 text-outline pointer-events-none">expand_more</span>
</div>
</td>
<td class="py-4 px-6 text-center"><button class="bg-royal-indigo text-on-primary px-4 py-2 font-button text-button hover:bg-primary transition-colors w-full">تحديث</button></td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface-container-low transition-colors">
<td class="py-4 px-6 font-numeral text-numeral">IRB-2023-0150</td>
<td class="py-4 px-6 font-medium">تقييم فعالية عقار (س) في تقليل نوبات الصداع النصفي العنقودي: تجربة سريرية عشوائية مزدوجة التعمية</td>
<td class="py-4 px-6 text-center"><span class="inline-block px-3 py-1 bg-forest text-on-primary font-bold text-xs">مدفوع</span></td>
<td class="py-4 px-6">
<div class="relative">
<select class="w-full appearance-none bg-paper-white border border-charcoal focus:border-royal-indigo focus:border-2 focus:ring-0 py-2 pl-8 pr-3 font-body-sm text-body-sm outline-none cursor-pointer">
<option disabled selected value="">اختر مراجعاً...</option>
<option value="1">د. فهد الدوسري (طب أعصاب)</option>
<option value="2">د. سارة محمد (باطنية)</option>
<option value="3">د. صيدلي. عمر زيد (صيدلة إكلينيكية)</option>
</select>
<span class="material-symbols-outlined absolute left-2 top-2.5 text-outline pointer-events-none">expand_more</span>
</div>
</td>
<td class="py-4 px-6 text-center"><button class="bg-royal-indigo text-on-primary px-4 py-2 font-button text-button hover:bg-primary transition-colors opacity-50 cursor-not-allowed w-full" disabled>حفظ</button></td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface-container-low transition-colors">
<td class="py-4 px-6 font-numeral text-numeral">IRB-2023-0152</td>
<td class="py-4 px-6 font-medium">مدى انتشار مقاومة المضادات الحيوية في وحدات العناية المركزة لحديثي الولادة</td>
<td class="py-4 px-6 text-center"><span class="inline-block px-3 py-1 bg-forest text-on-primary font-bold text-xs">مدفوع</span></td>
<td class="py-4 px-6">
<div class="relative">
<select class="w-full appearance-none bg-paper-white border border-charcoal focus:border-royal-indigo focus:border-2 focus:ring-0 py-2 pl-8 pr-3 font-body-sm text-body-sm outline-none cursor-pointer">
<option value="4">د. هند القحطاني (أمراض معدية)</option>
<option value="1">د. فهد الدوسري (طب أعصاب)</option>
<option value="2">د. سارة محمد (باطنية)</option>
</select>
<span class="material-symbols-outlined absolute left-2 top-2.5 text-outline pointer-events-none">expand_more</span>
</div>
</td>
<td class="py-4 px-6 text-center"><button class="bg-royal-indigo text-on-primary px-4 py-2 font-button text-button hover:bg-primary transition-colors w-full">تحديث</button></td>
</tr>
</tbody>
</table>
</div>

<div class="flex justify-between items-center mt-6 border-t border-charcoal pt-4">
<span class="font-body-sm text-body-sm text-slate-gray">عرض 1 إلى 4 من 24 بحث</span>
<div class="flex gap-2">
<button class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>السابق</button>
<button class="border border-charcoal bg-royal-indigo text-on-primary px-3 py-1 font-bold text-sm">1</button>
<button class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm">2</button>
<button class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm">3</button>
<button class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm">التالي</button>
</div>
</div>
</section>
</main>
</div>
</body>
