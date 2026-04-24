<?php
$pageTitle = 'قائمة المراجعة المبدئية - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'initial_preview';
$adminPageHeading = 'قائمة المراجعة المبدئية';
$adminPageSubtitle = 'متابعة الطلبات الجديدة قبل الإحالة الرسمية';

$queueCount = $queueCount ?? 0;
$queueItems = $queueItems ?? [];
$serialSuccessMessage = $serialSuccessMessage ?? null;
$serialErrorMessage = $serialErrorMessage ?? null;
?>

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<?php if ($serialSuccessMessage): ?>
<div class="rounded-lg border border-forest bg-green-50 text-forest px-4 py-3 text-sm">
<?= htmlspecialchars($serialSuccessMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($serialErrorMessage): ?>
<div class="rounded-lg border border-crimson bg-red-50 text-crimson px-4 py-3 text-sm">
<?= htmlspecialchars($serialErrorMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="mb-section-stack border-b border-charcoal pb-6 flex justify-between items-end">
<div>
<h1 class="font-h1 text-h1 text-on-surface mb-2">قائمة المراجعة المبدئية</h1>
<p class="font-body-sm text-body-sm text-slate-gray">
يرجى مراجعة التقديمات الجديدة وتعيين رقم تسلسلي مؤسسي فريد لكل بحث لاعتماده في دورة المراجعة الرسمية.
</p>
</div>
<div class="border border-charcoal px-4 py-2 bg-cool-slate">
<span class="font-numeral text-numeral text-on-surface mr-2"><?= htmlspecialchars((string) $queueCount, ENT_QUOTES, 'UTF-8') ?></span>
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
<?php foreach ($queueItems as $item): ?>
<tr class="border-b border-charcoal hover:bg-surface transition-colors">
<td class="py-6 px-4 align-top">
<div><?= htmlspecialchars($item['researcher_name'], ENT_QUOTES, 'UTF-8') ?></div>
<div class="font-body-sm text-body-sm text-slate-gray mt-1"><?= htmlspecialchars($item['researcher_department'], ENT_QUOTES, 'UTF-8') ?></div>
</td>
<td class="py-6 px-4 align-top"><div class="leading-relaxed"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></div></td>
<td class="py-6 px-4 align-top"><span class="font-numeral text-numeral text-slate-gray"><?= htmlspecialchars($item['created_at'], ENT_QUOTES, 'UTF-8') ?></span></td>
<td class="py-6 px-4 align-top">
<form method="post" action="/admin/initial-preview-queue/assign-serial" class="flex flex-col gap-2">
<input type="hidden" name="submission_id" value="<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"/>
<label class="font-body-sm text-body-sm text-on-surface">أدخل الرقم التسلسلي (اختياري)</label>
<div class="flex items-center gap-3">
<input name="serial_number" class="flex-1 border border-charcoal bg-paper-white px-3 py-2 font-numeral text-numeral text-on-surface focus:outline-none focus:border-royal-indigo focus:border-2 transition-all" placeholder="IRB-<?= date('Y') ?>-0001" type="text"/>
<button class="bg-royal-indigo text-on-primary font-button text-button px-5 py-2 border border-royal-indigo hover:bg-primary-container transition-colors" type="submit">حفظ</button>
</div>
</form>
</td>
</tr>
<?php endforeach; ?>

<?php if (empty($queueItems)): ?>
<tr>
<td class="py-8 px-4 text-center text-slate-gray" colspan="4">لا توجد طلبات جديدة في قائمة المراجعة المبدئية حالياً.</td>
</tr>
<?php endif; ?>
</tbody>
</table>

<div class="mt-6 flex justify-between items-center border-t border-charcoal pt-4 px-4 pb-4">
<span class="font-body-sm text-body-sm text-slate-gray">عرض 1-<?= htmlspecialchars((string) $queueCount, ENT_QUOTES, 'UTF-8') ?> من أصل <?= htmlspecialchars((string) $queueCount, ENT_QUOTES, 'UTF-8') ?> تقديمات جديدة</span>
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
