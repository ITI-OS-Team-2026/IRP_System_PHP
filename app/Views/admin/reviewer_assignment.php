<?php
$pageTitle = 'تعيين المراجعين - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'reviewer_assignment';
$adminPageHeading = 'تعيين المراجعين';
$adminPageSubtitle = 'إدارة توزيع الأبحاث على المراجعين المختصين';

$searchQuery = $searchQuery ?? '';
$reviewerAssignmentTotal = $reviewerAssignmentTotal ?? 0;
$reviewerAssignmentRows = $reviewerAssignmentRows ?? [];
$reviewerOptions = $reviewerOptions ?? [];
$reviewerAssignmentSuccess = $reviewerAssignmentSuccess ?? null;
$reviewerAssignmentError = $reviewerAssignmentError ?? null;
$reviewerAssignmentPagination = $reviewerAssignmentPagination ?? [
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

<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<?php if ($reviewerAssignmentSuccess): ?>
<div class="rounded-lg border border-green-600 bg-green-50 text-green-700 px-4 py-3 text-sm">
<?= htmlspecialchars($reviewerAssignmentSuccess, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($reviewerAssignmentError): ?>
<div class="rounded-lg border border-red-600 bg-red-50 text-red-700 px-4 py-3 text-sm">
<?= htmlspecialchars($reviewerAssignmentError, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="flex flex-wrap justify-between items-end border-b border-gray-200 pb-4 mb-2 gap-4" dir="rtl">
<div class="text-right">
<h1 class="font-h1 text-2xl text-gray-900 mb-2">تعيين المراجعين</h1>
<p class="text-gray-600">قائمة بالأبحاث المكتملة الدفع والجاهزة لتعيين المراجعين المختصين.</p>
</div>
<form method="get" action="/admin/reviewer-assignment" class="flex gap-4">
<div class="relative">
<span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400">search</span>
<input name="q" value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>" class="pl-4 pr-10 py-2 border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 bg-gray-50 outline-none font-body-sm w-64 rounded-lg transition-all" placeholder="بحث برقم التسلسل..." type="text"/>
</div>
<button class="bg-white text-gray-700 border border-gray-300 px-4 py-2 font-button hover:bg-gray-100 transition-all rounded-lg flex items-center gap-2" type="submit">
<span class="material-symbols-outlined">filter_list</span>
تصفية
</button>
</form>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-gray-100 border-b border-gray-200 font-body-sm text-gray-900 font-bold">
<th class="py-4 px-6 text-right w-32">الرقم التسلسلي</th>
<th class="py-4 px-6 text-right">عنوان البحث</th>
<th class="py-4 px-6 text-center w-32">حالة الدفع</th>
<th class="py-4 px-6 text-right min-w-[320px]">المراجع المعين</th>
<th class="py-4 px-6 text-center w-32">الإجراءات</th>
</tr>
</thead>
<tbody class="font-body-sm">
<?php foreach ($reviewerAssignmentRows as $row): ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
<td class="py-4 px-6 font-numeral"><?= htmlspecialchars($row['serial_number'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-4 px-6 font-medium"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-4 px-6 text-center"><span class="inline-block px-3 py-1 bg-green-600 text-white font-bold text-xs rounded-lg"><?= htmlspecialchars($row['payment_status_label'], ENT_QUOTES, 'UTF-8') ?></span></td>
<td class="py-4 px-6">
<form method="post" action="/admin/reviewer-assignment/save" class="flex items-center gap-3">
<input type="hidden" name="submission_id" value="<?= htmlspecialchars((string) $row['id'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="page" value="<?= htmlspecialchars((string) $reviewerAssignmentPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
<div class="relative flex-1 min-w-[240px]">
<select name="reviewer_id" class="w-full min-w-[240px] appearance-none bg-white border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 py-2 pl-8 pr-3 font-body-sm outline-none cursor-pointer rounded-lg transition-all" required>
<option value="" <?= $row['reviewer_id'] === null ? 'selected' : '' ?> disabled>اختر مراجعاً...</option>
<?php foreach ($reviewerOptions as $reviewer): ?>
<option value="<?= htmlspecialchars((string) $reviewer['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $row['reviewer_id'] === $reviewer['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reviewer['label'], ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
<span class="material-symbols-outlined absolute left-2 top-2.5 text-gray-400 pointer-events-none">expand_more</span>
</div>
<button class="bg-indigo-700 text-white px-4 py-2 font-button hover:bg-indigo-800 transition-all whitespace-nowrap rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5" type="submit">
<?= $row['reviewer_id'] === null ? 'حفظ' : 'تحديث' ?>
</button>
</form>
</td>
<td class="py-4 px-6 text-center text-xs text-gray-600">
<?= $row['reviewer_name'] !== '' ? htmlspecialchars($row['reviewer_name'], ENT_QUOTES, 'UTF-8') : 'غير معين' ?>
</td>
</tr>
<?php endforeach; ?>

<?php if (empty($reviewerAssignmentRows)): ?>
<tr>
<td class="py-8 px-6 text-center text-gray-600" colspan="5">لا توجد أبحاث جاهزة لتعيين المراجعين حالياً.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

<?php
$pagerPagination = $reviewerAssignmentPagination;
$pagerTotal = $reviewerAssignmentTotal;
$pagerBasePath = '/admin/reviewer-assignment';
$pagerQuery = $searchQuery !== '' ? ['q' => $searchQuery] : [];
$pagerItemLabel = 'بحث';
require __DIR__ . '/partials/pager.php';
?>
</section>
</main>
</div>
</body>
