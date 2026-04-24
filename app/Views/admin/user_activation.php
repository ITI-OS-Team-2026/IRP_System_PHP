<?php
$pageTitle = 'تنشيط الحسابات - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'user_activation';
$adminPageHeading = 'تنشيط الحسابات';
$adminPageSubtitle = 'مراجعة وتوثيق حسابات الباحثين الجدد';

$pendingCount = $pendingCount ?? 0;
$pendingUsers = $pendingUsers ?? [];
$userActivationPagination = $userActivationPagination ?? [
	'currentPage' => 1,
	'perPage' => 10,
	'lastPage' => 1,
	'from' => 0,
	'to' => 0,
	'hasPrevious' => false,
	'hasNext' => false,
	'previousPage' => 1,
	'nextPage' => 1,
];
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
<span class="font-numeral text-numeral text-royal-indigo border border-royal-indigo px-3 py-1 bg-surface-bright">العدد الإجمالي: <?= htmlspecialchars((string) $pendingCount, ENT_QUOTES, 'UTF-8') ?></span>
</div>
</div>

<div class="w-full bg-white border border-charcoal rounded-xl shadow-[0_2px_12px_rgba(15,23,42,0.05)] overflow-hidden">
<table class="w-full text-right border-collapse">
<thead class="bg-cool-slate border-b border-charcoal">
<tr>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الاسم</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الكلية/القسم</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">المستندات</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">تاريخ التسجيل</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal">الحالة</th>
<th class="py-4 px-6 font-button text-button text-charcoal border-b border-charcoal w-40 text-center">الإجراءات</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm text-charcoal">
<?php foreach ($pendingUsers as $user): ?>
<tr class="border-b border-charcoal hover:bg-surface-bright transition-colors duration-150">
<td class="py-5 px-6 font-body-lg text-body-lg"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6"><?= htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6">
<div class="text-sm text-charcoal"><?= htmlspecialchars((string) $user['document_count'], ENT_QUOTES, 'UTF-8') ?> مستندات</div>
<div class="text-xs text-slate-gray mt-1"><?= htmlspecialchars((string) $user['submission_count'], ENT_QUOTES, 'UTF-8') ?> تقديمات مرتبطة</div>
</td>
<td class="py-5 px-6 font-numeral text-numeral text-slate-gray"><?= htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6"><span class="inline-block px-3 py-1 text-[11px] font-bold tracking-widest border border-charcoal text-charcoal bg-paper-white"><?= htmlspecialchars($user['status_label'], ENT_QUOTES, 'UTF-8') ?></span></td>
<td class="py-5 px-6 text-center">
<form method="post" action="/admin/user-activation/activate">
<input type="hidden" name="user_id" value="<?= htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="page" value="<?= htmlspecialchars((string) $userActivationPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?>"/>
<button class="bg-royal-indigo text-on-primary font-button text-button px-6 py-2 w-full hover:bg-primary transition-colors border border-royal-indigo shadow-none" type="submit">تنشيط</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<div class="mt-6 pt-4 border-t border-charcoal text-left">
<?php
$pagerPagination = $userActivationPagination;
$pagerTotal = $pendingCount;
$pagerBasePath = '/admin/user-activation';
$pagerQuery = [];
$pagerItemLabel = 'حساب';
require __DIR__ . '/partials/pager.php';
?>

<p class="font-body-sm text-body-sm text-slate-gray">
<span class="material-symbols-outlined align-middle text-[16px] ml-1" style="font-variation-settings: 'FILL' 0;">info</span>
تنشيط الحساب يمنح الباحث صلاحية تقديم بروتوكولات البحث إلى لجان الأخلاقيات المؤسسية.
</p>
</div>
</section>
</main>
</div>
</body>
