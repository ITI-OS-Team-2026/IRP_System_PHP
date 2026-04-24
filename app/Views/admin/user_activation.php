<?php
$pageTitle = 'تنشيط الحسابات - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'user_activation';
$adminPageHeading = 'تنشيط الحسابات';
$adminPageSubtitle = 'مراجعة وتوثيق حسابات الباحثين الجدد';
?>

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<div class="mb-4 pb-4 border-b-2 border-charcoal flex justify-between items-end">
<div>
<h2 class="font-display-lg text-display-lg text-charcoal mb-2">طلبات التسجيل المعلقة</h2>
<p class="font-body-lg text-body-lg text-slate-gray">مراجعة وتوثيق هويات الباحثين الجدد للوصول إلى النظام.</p>
</div>
<div class="text-right">
<span class="font-numeral text-numeral text-royal-indigo border border-royal-indigo px-3 py-1 bg-surface-bright">العدد الإجمالي: 4</span>
</div>
</div>

<div class="w-full bg-white border border-charcoal rounded-xl shadow-[0_2px_12px_rgba(15,23,42,0.05)] overflow-hidden">
<table class="w-full text-right border-collapse">
<thead class="bg-cool-slate border-b border-charcoal">
<tr>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الاسم</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الرقم القومي</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الكلية/القسم</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">تاريخ التسجيل</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الحالة</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal w-40 text-center">الإجراءات</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm text-charcoal">
<tr class="border-b border-charcoal hover:bg-surface-bright transition-colors duration-150">
<td class="py-5 px-6 font-body-lg text-body-lg">د. طارق محمود السيد</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray tracking-wide">28509122104567</td>
<td class="py-5 px-6">كلية الطب / قسم الجراحة</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray">2023-10-24</td>
<td class="py-5 px-6"><span class="inline-block px-3 py-1 text-[11px] font-bold tracking-widest border border-charcoal text-charcoal bg-paper-white">قيد المراجعة</span></td>
<td class="py-5 px-6 text-center"><button class="bg-royal-indigo text-on-primary font-button text-button px-6 py-2 w-full hover:bg-primary transition-colors border border-royal-indigo shadow-none">تنشيط</button></td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface-bright transition-colors duration-150">
<td class="py-5 px-6 font-body-lg text-body-lg">أ.م.د. ليلى عبد الرحمن هاشم</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray tracking-wide">27903250102234</td>
<td class="py-5 px-6">كلية الصيدلة / الصيدلانيات</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray">2023-10-25</td>
<td class="py-5 px-6"><span class="inline-block px-3 py-1 text-[11px] font-bold tracking-widest border border-charcoal text-charcoal bg-paper-white">قيد المراجعة</span></td>
<td class="py-5 px-6 text-center"><button class="bg-royal-indigo text-on-primary font-button text-button px-6 py-2 w-full hover:bg-primary transition-colors border border-royal-indigo shadow-none">تنشيط</button></td>
</tr>
<tr class="border-b border-charcoal hover:bg-surface-bright transition-colors duration-150">
<td class="py-5 px-6 font-body-lg text-body-lg">أحمد يوسف منصور</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray tracking-wide">29811052107890</td>
<td class="py-5 px-6">كلية التمريض / تمريض البالغين</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray">2023-10-25</td>
<td class="py-5 px-6"><span class="inline-block px-3 py-1 text-[11px] font-bold tracking-widest border border-charcoal text-charcoal bg-paper-white">قيد المراجعة</span></td>
<td class="py-5 px-6 text-center"><button class="bg-royal-indigo text-on-primary font-button text-button px-6 py-2 w-full hover:bg-primary transition-colors border border-royal-indigo shadow-none">تنشيط</button></td>
</tr>
<tr class="hover:bg-surface-bright transition-colors duration-150">
<td class="py-5 px-6 font-body-lg text-body-lg">سارة كمال حسين</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray tracking-wide">30102140105678</td>
<td class="py-5 px-6">معهد الأورام / البيولوجيا الجزيئية</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray">2023-10-26</td>
<td class="py-5 px-6"><span class="inline-block px-3 py-1 text-[11px] font-bold tracking-widest border border-charcoal text-charcoal bg-paper-white">مستندات ناقصة</span></td>
<td class="py-5 px-6 text-center flex gap-2">
<button class="bg-royal-indigo text-on-primary font-button text-button px-6 py-2 flex-1 hover:bg-primary transition-colors border border-royal-indigo shadow-none opacity-50 cursor-not-allowed" disabled>تنشيط</button>
<button class="bg-paper-white text-charcoal font-button text-button px-3 py-2 border border-charcoal hover:bg-surface-bright transition-colors shadow-none" title="طلب استكمال بيانات">
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 0;">mark_email_unread</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>

<div class="mt-6 pt-4 border-t border-charcoal text-left">
<p class="font-body-sm text-body-sm text-slate-gray">
<span class="material-symbols-outlined align-middle text-[16px] ml-1" style="font-variation-settings: 'FILL' 0;">info</span>
تنشيط الحساب يمنح الباحث صلاحية تقديم بروتوكولات البحث إلى لجان الأخلاقيات المؤسسية.
</p>
</div>
</section>
</main>
</div>
</body>
