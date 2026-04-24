<?php
$pageTitle = 'قائمة المراجعة المبدئية - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'initial_preview';
$adminPageHeading = 'قائمة المراجعة المبدئية';
$adminPageSubtitle = 'متابعة الطلبات الجديدة قبل الإحالة الرسمية';
?>

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<div class="mb-section-stack border-b border-charcoal pb-6 flex justify-between items-end">
<div>
<h1 class="font-h1 text-h1 text-on-surface mb-2">قائمة المراجعة المبدئية</h1>
<p class="font-body-sm text-body-sm text-slate-gray">
يرجى مراجعة التقديمات الجديدة وتعيين رقم تسلسلي مؤسسي فريد لكل بحث لاعتماده في دورة المراجعة الرسمية.
</p>
</div>
<div class="border border-charcoal px-4 py-2 bg-cool-slate">
<span class="font-numeral text-numeral text-on-surface mr-2">٣</span>
<span class="font-body-sm text-body-sm text-on-surface">طلبات قيد الانتظار</span>
</div>
</div>

<div class="w-full bg-white border border-charcoal rounded-xl shadow-[0_2px_12px_rgba(15,23,42,0.05)] overflow-hidden">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-cool-slate border-y border-charcoal">
<th class="py-4 px-4 font-body-sm text-body-sm text-on-surface font-bold text-right w-1/4">اسم الباحث</th>
<th class="py-4 px-4 font-body-sm text-body-sm text-on-surface font-bold text-right w-2/5">عنوان البحث</th>
<th class="py-4 px-4 font-body-sm text-body-sm text-on-surface font-bold text-right w-1/6">تاريخ التقديم</th>
<th class="py-4 px-4 font-body-sm text-body-sm text-on-surface font-bold text-right">تعيين رقم تسلسلي</th>
</tr>
</thead>
<tbody class="font-body-lg text-body-lg text-on-surface">
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-6 px-4 align-top">
<div>د. طارق محمود الشناوي</div>
<div class="font-body-sm text-body-sm text-slate-gray mt-1">قسم الأورام السريرية</div>
</td>
<td class="py-6 px-4 align-top"><div class="leading-relaxed">دراسة طولية لتأثيرات العلاج المناعي الموجه على معدلات البقاء في مرضى سرطان الرئة ذو الخلايا غير الصغيرة</div></td>
<td class="py-6 px-4 align-top"><span class="font-numeral text-numeral text-slate-gray">٢٠٢٣/١٠/١٥</span></td>
<td class="py-6 px-4 align-top">
<div class="flex flex-col gap-2">
<label class="font-body-sm text-body-sm text-on-surface">أدخل الرقم التسلسلي</label>
<div class="flex items-center gap-3">
<input class="flex-1 border border-charcoal bg-paper-white px-3 py-2 font-numeral text-numeral text-on-surface focus:outline-none focus:border-royal-indigo focus:border-2 transition-all" placeholder="IRB-2023-..." type="text"/>
<button class="bg-royal-indigo text-on-primary font-button text-button px-5 py-2 border border-royal-indigo hover:bg-primary-container transition-colors">حفظ</button>
</div>
</div>
</td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-6 px-4 align-top">
<div>أ.د. فاطمة الزهراء علي</div>
<div class="font-body-sm text-body-sm text-slate-gray mt-1">كلية طب الأسنان</div>
</td>
<td class="py-6 px-4 align-top"><div class="leading-relaxed">تقييم فعالية المواد الحيوية النانوية في تجديد أنسجة اللب: تجربة سريرية معشاة</div></td>
<td class="py-6 px-4 align-top"><span class="font-numeral text-numeral text-slate-gray">٢٠٢٣/١٠/١٤</span></td>
<td class="py-6 px-4 align-top">
<div class="flex flex-col gap-2">
<label class="font-body-sm text-body-sm text-on-surface">أدخل الرقم التسلسلي</label>
<div class="flex items-center gap-3">
<input class="flex-1 border border-charcoal bg-paper-white px-3 py-2 font-numeral text-numeral text-on-surface focus:outline-none focus:border-royal-indigo focus:border-2 transition-all" placeholder="IRB-2023-..." type="text"/>
<button class="bg-royal-indigo text-on-primary font-button text-button px-5 py-2 border border-royal-indigo hover:bg-primary-container transition-colors">حفظ</button>
</div>
</div>
</td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface transition-colors bg-surface-container-low">
<td class="py-6 px-4 align-top">
<div>د. يوسف عبد المجيد</div>
<div class="font-body-sm text-body-sm text-slate-gray mt-1">الصحة العامة</div>
</td>
<td class="py-6 px-4 align-top"><div class="leading-relaxed">التحليل الوبائي لانتشار العدوى المكتسبة في المستشفيات في وحدات العناية المركزة</div></td>
<td class="py-6 px-4 align-top"><span class="font-numeral text-numeral text-slate-gray">٢٠٢٣/١٠/١٢</span></td>
<td class="py-6 px-4 align-top">
<div class="flex flex-col gap-2">
<label class="font-body-sm text-body-sm text-on-surface">أدخل الرقم التسلسلي</label>
<div class="flex items-center gap-3">
<input class="flex-1 border border-charcoal bg-paper-white px-3 py-2 font-numeral text-numeral text-on-surface focus:outline-none focus:border-royal-indigo focus:border-2 transition-all" placeholder="IRB-2023-..." type="text"/>
<button class="bg-royal-indigo text-on-primary font-button text-button px-5 py-2 border border-royal-indigo hover:bg-primary-container transition-colors">حفظ</button>
</div>
</div>
</td>
</tr>
</tbody>
</table>

<div class="mt-6 flex justify-between items-center border-t border-charcoal pt-4 px-4 pb-4">
<span class="font-body-sm text-body-sm text-slate-gray">عرض ١-٣ من أصل ٣ تقديمات جديدة</span>
<div class="flex gap-2">
<button class="border border-charcoal px-3 py-1 text-slate-gray bg-surface-dim cursor-not-allowed font-body-sm text-body-sm" disabled>السابق</button>
<button class="border border-charcoal px-3 py-1 text-slate-gray bg-surface-dim cursor-not-allowed font-body-sm text-body-sm" disabled>التالي</button>
</div>
</div>
</div>
</section>
</main>
</div>
</body>
