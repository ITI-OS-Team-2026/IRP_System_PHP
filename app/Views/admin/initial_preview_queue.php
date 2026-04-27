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
$initialPreviewPagination = $initialPreviewPagination ?? [
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
<?php if ($serialSuccessMessage): ?>
<div class="rounded-lg border border-green-600 bg-green-50 text-green-700 px-4 py-3 text-sm">
<?= htmlspecialchars($serialSuccessMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($serialErrorMessage): ?>
<div class="rounded-lg border border-red-600 bg-red-50 text-red-700 px-4 py-3 text-sm">
<?= htmlspecialchars($serialErrorMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="mb-6 border-b border-gray-200 pb-6 flex justify-between items-end" dir="rtl">
<div class="border border-indigo-200 px-4 py-2 bg-indigo-50 rounded-lg">
<span class="font-numeral text-gray-900 ml-2"><?= htmlspecialchars((string) $queueCount, ENT_QUOTES, 'UTF-8') ?></span>
<span class="text-gray-700">طلبات قيد الانتظار</span>
</div>
<div class="text-right">
<h1 class="font-h1 text-2xl text-gray-900 mb-2">قائمة المراجعة المبدئية</h1>
<p class="text-gray-600 text-right leading-relaxed">
يرجى مراجعة التقديمات الجديدة وتعيين رقم تسلسلي مؤسسي فريد لكل بحث لاعتماده في دورة المراجعة الرسمية.
</p>
</div>
</div>

<div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-gray-100 border-y border-gray-200">
<th class="py-4 px-4 font-body-sm text-gray-900 font-bold text-right w-1/4">اسم الباحث</th>
<th class="py-4 px-4 font-body-sm text-gray-900 font-bold text-right w-2/5">عنوان البحث</th>
<th class="py-4 px-4 font-body-sm text-gray-900 font-bold text-right w-1/6">تاريخ التقديم</th>
<th class="py-4 px-4 font-body-sm text-gray-900 font-bold text-right">تعيين رقم تسلسلي</th>
</tr>
</thead>
<tbody class="font-body-lg text-gray-900">
<?php foreach ($queueItems as $item): ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
<td class="py-6 px-4 align-top">
<div><?= htmlspecialchars($item['researcher_name'], ENT_QUOTES, 'UTF-8') ?></div>
<div class="font-body-sm text-gray-600 mt-1"><?= htmlspecialchars($item['researcher_department'], ENT_QUOTES, 'UTF-8') ?></div>
</td>
<td class="py-6 px-4 align-top"><div class="leading-relaxed"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></div></td>
<td class="py-6 px-4 align-top"><span class="font-numeral text-gray-600"><?= htmlspecialchars($item['created_at'], ENT_QUOTES, 'UTF-8') ?></span></td>
<td class="py-6 px-4 align-top">
<form method="post" action="/admin/initial-preview-queue/assign-serial" class="flex flex-col gap-2">
<input type="hidden" name="submission_id" value="<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="page" value="<?= htmlspecialchars((string) $initialPreviewPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
<label class="font-body-sm text-gray-700">أدخل الرقم التسلسلي (اختياري)</label>
<div class="flex items-center gap-3">
<input name="serial_number" class="flex-1 border border-gray-300 bg-gray-50 px-3 py-2 font-numeral text-gray-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" placeholder="IRB-<?= date('Y') ?>-0001" type="text"/>
<button class="bg-indigo-700 text-white font-button px-5 py-2 border border-indigo-700 hover:bg-indigo-800 transition-all rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5" type="submit">حفظ</button>
</div>
</form>
</td>
</tr>
<?php endforeach; ?>

<?php if (empty($queueItems)): ?>
<tr>
<td class="py-8 px-4 text-center text-gray-600" colspan="4">لا توجد طلبات جديدة في قائمة المراجعة المبدئية حالياً.</td>
</tr>
<?php endif; ?>
</tbody>
</table>

<div class="mt-6 px-4 pb-4">
<?php
$pagerPagination = $initialPreviewPagination;
$pagerTotal = $queueCount;
$pagerBasePath = '/admin/initial-preview-queue';
$pagerQuery = [];
$pagerItemLabel = 'تقديمات جديدة';
require __DIR__ . '/partials/pager.php';
?>
</div>
</div>
</section>
</main>
</div>
</body>
