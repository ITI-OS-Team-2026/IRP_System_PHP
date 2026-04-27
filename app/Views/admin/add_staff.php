<?php
$pageTitle = 'إضافة الموظفين - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'add_staff';
$adminPageHeading = 'إضافة الموظفين';
$adminPageSubtitle = 'تسجيل حسابات الطاقم الإداري والمراجعين من لوحة الإدارة';

$staffRoleOptions = $staffRoleOptions ?? [];
$addStaffSuccessMessage = $addStaffSuccessMessage ?? null;
$addStaffErrorMessage = $addStaffErrorMessage ?? null;
$addStaffOldInput = $addStaffOldInput ?? [];
?>

<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<?php if ($addStaffSuccessMessage): ?>
<div class="rounded-lg border border-green-600 bg-green-50 text-green-700 px-4 py-3 text-sm">
<?= htmlspecialchars($addStaffSuccessMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($addStaffErrorMessage): ?>
<div class="rounded-lg border border-red-600 bg-red-50 text-red-700 px-4 py-3 text-sm">
<?= htmlspecialchars($addStaffErrorMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="mb-4 pb-4 border-b border-gray-200 flex justify-between items-end gap-4" dir="rtl">
<div class="text-right">
<h2 class="font-h1 text-2xl text-gray-900 mb-2">إضافة الموظفين</h2>
<p class="text-gray-600">إنشاء حسابات الطاقم الإداري من داخل لوحة الإدارة مع اختيار الدور المناسب.</p>
</div>
<div>
<span class="font-numeral text-indigo-700 border border-indigo-200 px-3 py-1 bg-indigo-50 rounded-lg">الوظائف المتاحة: <?= htmlspecialchars((string) count($staffRoleOptions), ENT_QUOTES, 'UTF-8') ?></span>
</div>
</div>

<div class="max-w-3xl mx-auto w-full bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 md:p-8">
<form method="post" action="/admin/add-staff/store" class="space-y-5">
<input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<div class="space-y-2">
<label class="font-body-sm text-sm text-gray-700">الاسم الكامل</label>
<input name="full_name" value="<?= htmlspecialchars((string) ($addStaffOldInput['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="الاسم الكامل" class="w-full border border-gray-300 bg-gray-50 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" type="text" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-sm text-gray-700">البريد الإلكتروني</label>
<input name="email" value="<?= htmlspecialchars((string) ($addStaffOldInput['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="البريد الإلكتروني" class="w-full border border-gray-300 bg-gray-50 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" type="email" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-sm text-gray-700">رقم الهاتف</label>
<input name="phone_number" value="<?= htmlspecialchars((string) ($addStaffOldInput['phone_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="رقم الهاتف" class="w-full border border-gray-300 bg-gray-50 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" type="text" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-sm text-gray-700">الوظيفة</label>
<select name="role" class="w-full border border-gray-300 bg-gray-50 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" required>
<option value="" disabled <?= empty($addStaffOldInput['role']) ? 'selected' : '' ?>>اختر الوظيفة...</option>
<?php foreach ($staffRoleOptions as $roleKey => $roleLabel): ?>
<option value="<?= htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8') ?>" <?= (($addStaffOldInput['role'] ?? '') === $roleKey) ? 'selected' : '' ?>><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="space-y-2">
<label class="font-body-sm text-sm text-gray-700">كلمة المرور</label>
<input name="password" placeholder="كلمة المرور" class="w-full border border-gray-300 bg-gray-50 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg transition-all" type="password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="8 أحرف على الأقل مع حرف كبير وحرف صغير ورقم ورمز خاص" required />
<p class="text-xs text-gray-600"> يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل وحرف كبير وحرف صغير ورقم ورمز خاص.</p>
</div>

<div class="flex items-center justify-between gap-4 pt-2">
<p class="text-sm text-gray-600">يتم تسجيل عملية إنشاء الموظف في سجل النظام تلقائياً.</p>
<button type="submit" class="bg-indigo-700 text-white font-button px-6 py-3 border border-indigo-700 hover:bg-indigo-800 transition-all duration-300 rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-0.5">حفظ الموظف</button>
</div>
</form>
</div>
</section>
</main>
</div>
</body>
