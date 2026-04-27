<?php
$pageTitle = 'تنشيط الحسابات - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'user_activation';
$adminPageHeading = 'تنشيط الحسابات';
$adminPageSubtitle = 'مراجعة وتوثيق حسابات الباحثين الجدد';

$pendingCount = $pendingCount ?? 0;
$pendingUsers = $pendingUsers ?? [];
$activationSuccessMessage = $activationSuccessMessage ?? null;
$activationErrorMessage = $activationErrorMessage ?? null;
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

<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<?php if ($activationSuccessMessage): ?>
<div class="rounded-lg border border-green-600 bg-green-50 text-green-700 px-4 py-3 text-sm">
<?= htmlspecialchars($activationSuccessMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($activationErrorMessage): ?>
<div class="rounded-lg border border-red-600 bg-red-50 text-red-700 px-4 py-3 text-sm">
<?= htmlspecialchars($activationErrorMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="mb-4 pb-4 border-b border-gray-200 flex justify-between items-end" dir="rtl">
<div class="text-right">
<h2 class="font-h1 text-2xl text-gray-900 mb-2">طلبات التسجيل المعلقة</h2>
<p class="text-gray-600">مراجعة وتوثيق هويات الباحثين الجدد للوصول إلى النظام.</p>
</div>
<div>
<span class="font-numeral text-indigo-700 border border-indigo-200 px-3 py-1 bg-indigo-50 rounded-lg">العدد الإجمالي: <?= htmlspecialchars((string) $pendingCount, ENT_QUOTES, 'UTF-8') ?></span>
</div>
</div>

<div class="w-full bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
<table class="w-full text-right border-collapse">
<thead class="bg-gray-100 border-b border-gray-200">
<tr>
<th class="py-4 px-6 font-button text-gray-900 border-b border-gray-200">الاسم</th>
<th class="py-4 px-6 font-button text-gray-900 border-b border-gray-200">الكلية/القسم</th>
<th class="py-4 px-6 font-button text-gray-900 border-b border-gray-200">تاريخ التسجيل</th>
<th class="py-4 px-6 font-button text-gray-900 border-b border-gray-200 w-36 text-center">بطاقة</th>
<th class="py-4 px-6 font-button text-gray-900 border-b border-gray-200 w-40 text-center">الإجراءات</th>
</tr>
</thead>
<tbody class="font-body-sm text-gray-900">
<?php foreach ($pendingUsers as $user): ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
<td class="py-5 px-6 font-body-lg"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6"><?= htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6 font-numeral text-gray-600"><?= htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="py-5 px-6 text-center">
<button
	type="button"
	class="js-open-id-modal bg-white text-gray-900 font-button px-4 py-2 border border-gray-300 hover:bg-gray-100 transition-colors rounded-lg"
	data-student-name="<?= htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8') ?>"
	data-front-path="<?= htmlspecialchars((string) ($user['id_front_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
	data-back-path="<?= htmlspecialchars((string) ($user['id_back_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
>
عرض البطاقة
</button>
</td>
<td class="py-5 px-6 text-center align-middle">
<div class="flex flex-col gap-2 w-full">
<form method="post" action="/admin/user-activation/activate" class="w-full">
<input type="hidden" name="user_id" value="<?= htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="page" value="<?= htmlspecialchars((string) $userActivationPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
<button class="bg-indigo-700 text-white font-button px-4 py-2 w-full hover:bg-indigo-800 transition-colors border border-indigo-700 rounded-lg" type="submit">تنشيط</button>
</form>
<button type="button" class="js-open-refuse-modal bg-white text-red-700 font-button px-4 py-2 w-full hover:bg-red-50 transition-colors border border-red-200 rounded-lg text-center" data-user-id="<?= htmlspecialchars((string) $user['id'], ENT_QUOTES, 'UTF-8') ?>" data-student-name="<?= htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8') ?>">رفض</button>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<div id="idCardModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
<div class="absolute inset-0 bg-black/60"></div>
<div class="relative max-w-5xl mx-auto mt-8 md:mt-16 bg-white border border-gray-200 shadow-xl rounded-xl">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
<h3 id="idCardModalTitle" class="font-h1 text-xl text-gray-900">بطاقة الهوية</h3>
<button id="closeIdCardModal" type="button" class="text-gray-600 hover:text-indigo-700" aria-label="إغلاق">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
<div class="border border-gray-200 p-3 bg-gray-50 rounded-lg">
<p class="font-body-sm text-gray-900 mb-2">وجه البطاقة</p>
<img id="idCardFrontImage" src="" alt="وجه البطاقة" class="w-full h-[320px] object-contain bg-white border border-gray-200 rounded-lg" />
<p id="idCardFrontFallback" class="hidden text-sm text-gray-600 mt-2">لا توجد صورة لوجه البطاقة.</p>
</div>
<div class="border border-gray-200 p-3 bg-gray-50 rounded-lg">
<p class="font-body-sm text-gray-900 mb-2">ظهر البطاقة</p>
<img id="idCardBackImage" src="" alt="ظهر البطاقة" class="w-full h-[320px] object-contain bg-white border border-gray-200 rounded-lg" />
<p id="idCardBackFallback" class="hidden text-sm text-gray-600 mt-2">لا توجد صورة لظهر البطاقة.</p>
</div>
</div>
</div>
</div>

<div id="refuseModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
<div class="absolute inset-0 bg-black/60"></div>
<div class="relative max-w-lg mx-auto mt-16 bg-white border border-gray-200 shadow-xl rounded-xl">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
<h3 id="refuseModalTitle" class="font-h1 text-xl text-gray-900">رفض طلب تسجيل</h3>
<button id="closeRefuseModal" type="button" class="text-gray-600 hover:text-red-700" aria-label="إغلاق">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-5">
<form method="post" action="/admin/user-activation/refuse">
<input type="hidden" name="user_id" id="refuseUserId" value=""/>
<input type="hidden" name="page" value="<?= htmlspecialchars((string) $userActivationPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?>"/>
<input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
<div class="mb-4">
<label for="reason_for_refusal" class="block font-body-sm text-gray-900 mb-2">سبب الرفض (سيتم إرساله بالبريد الإلكتروني للباحث):</label>
<textarea id="reason_for_refusal" name="reason_for_refusal" rows="4" class="w-full border border-gray-300 p-3 font-body-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 rounded-lg" required placeholder="يرجى توضيح سبب رفض قبول طلب التسجيل، مثل: صورة الهوية غير واضحة..."></textarea>
</div>
<div class="flex justify-end gap-3 mt-6">
<button type="button" id="cancelRefuseModal" class="px-6 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 font-button rounded-lg transition-colors">إلغاء</button>
<button type="submit" class="px-6 py-2 border border-red-600 text-white bg-red-600 hover:bg-red-700 font-button rounded-lg transition-colors">تأكيد الرفض</button>
</div>
</form>
</div>
</div>
</div>

<div class="mt-6 pt-4 border-t border-gray-200 text-left">
<?php
$pagerPagination = $userActivationPagination;
$pagerTotal = $pendingCount;
$pagerBasePath = '/admin/user-activation';
$pagerQuery = [];
$pagerItemLabel = 'حساب';
require __DIR__ . '/partials/pager.php';
?>

<p class="font-body-sm text-gray-600">
<span class="material-symbols-outlined align-middle text-[16px] ml-1" style="font-variation-settings: 'FILL' 0;">info</span>
تنشيط الحساب يمنح الباحث صلاحية تقديم بروتوكولات البحث إلى لجان الأخلاقيات المؤسسية.
</p>
</div>
</section>
</main>
</div>
<script>
(() => {
	const modal = document.getElementById('idCardModal');
	const title = document.getElementById('idCardModalTitle');
	const closeButton = document.getElementById('closeIdCardModal');
	const frontImage = document.getElementById('idCardFrontImage');
	const backImage = document.getElementById('idCardBackImage');
	const frontFallback = document.getElementById('idCardFrontFallback');
	const backFallback = document.getElementById('idCardBackFallback');

	const hideModal = () => {
		modal.classList.add('hidden');
		modal.setAttribute('aria-hidden', 'true');
		title.textContent = 'بطاقة الهوية';
		frontImage.src = '';
		backImage.src = '';
	};

	const setImage = (imageElement, fallbackElement, pathValue) => {
		const path = String(pathValue || '').trim();
		if (path === '') {
			imageElement.classList.add('hidden');
			fallbackElement.classList.remove('hidden');
			return;
		}

		imageElement.classList.remove('hidden');
		fallbackElement.classList.add('hidden');
		imageElement.src = path;
	};

	document.querySelectorAll('.js-open-id-modal').forEach((button) => {
		button.addEventListener('click', () => {
			const studentName = button.dataset.studentName || 'الطالب';
			title.textContent = 'بطاقة الهوية - ' + studentName;

			setImage(frontImage, frontFallback, button.dataset.frontPath);
			setImage(backImage, backFallback, button.dataset.backPath);

			modal.classList.remove('hidden');
			modal.setAttribute('aria-hidden', 'false');
		});
	});

	closeButton.addEventListener('click', hideModal);
	modal.addEventListener('click', (event) => {
		if (event.target === modal || event.target === modal.firstElementChild) {
			hideModal();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
			hideModal();
		}
	});

	const refuseModal = document.getElementById('refuseModal');
	const refuseTitle = document.getElementById('refuseModalTitle');
	const closeRefuseButton = document.getElementById('closeRefuseModal');
	const cancelRefuseButton = document.getElementById('cancelRefuseModal');
	const refuseUserIdInput = document.getElementById('refuseUserId');
	const reasonInput = document.getElementById('reason_for_refusal');

	const hideRefuseModal = () => {
		refuseModal.classList.add('hidden');
		refuseModal.setAttribute('aria-hidden', 'true');
		reasonInput.value = '';
	};

	document.querySelectorAll('.js-open-refuse-modal').forEach((button) => {
		button.addEventListener('click', () => {
			const studentName = button.dataset.studentName || 'الطالب';
			refuseTitle.textContent = 'رفض طلب تسجيل - ' + studentName;
			refuseUserIdInput.value = button.dataset.userId;

			refuseModal.classList.remove('hidden');
			refuseModal.setAttribute('aria-hidden', 'false');
		});
	});

	closeRefuseButton.addEventListener('click', hideRefuseModal);
	cancelRefuseButton.addEventListener('click', hideRefuseModal);

	refuseModal.addEventListener('click', (event) => {
		if (event.target === refuseModal || event.target === refuseModal.firstElementChild) {
			hideRefuseModal();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && !refuseModal.classList.contains('hidden')) {
			hideRefuseModal();
		}
	});
})();
</script>
</body>
