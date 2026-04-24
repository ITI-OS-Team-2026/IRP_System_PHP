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

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6 space-y-6">
<?php if ($addStaffSuccessMessage): ?>
<div class="rounded-lg border border-forest bg-green-50 text-forest px-4 py-3 text-sm">
<?= htmlspecialchars($addStaffSuccessMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php if ($addStaffErrorMessage): ?>
<div class="rounded-lg border border-crimson bg-red-50 text-crimson px-4 py-3 text-sm">
<?= htmlspecialchars($addStaffErrorMessage, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="mb-4 pb-4 border-b-2 border-charcoal flex justify-between items-end gap-4">
<div>
<h2 class="font-display-lg text-display-lg text-charcoal mb-2">إضافة الموظفين</h2>
<p class="font-body-lg text-body-lg text-slate-gray">إنشاء حسابات الطاقم الإداري من داخل لوحة الإدارة مع اختيار الدور المناسب.</p>
</div>
<div class="text-right">
<span class="font-numeral text-numeral text-royal-indigo border border-royal-indigo px-3 py-1 bg-surface-bright">الوظائف المتاحة: <?= htmlspecialchars((string) count($staffRoleOptions), ENT_QUOTES, 'UTF-8') ?></span>
</div>
</div>

<div class="max-w-3xl mx-auto w-full bg-white border border-charcoal rounded-xl shadow-[0_2px_12px_rgba(15,23,42,0.05)] p-6 md:p-8">
<form method="post" action="/admin/add-staff/store" class="space-y-5">
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
<div class="space-y-2">
<label class="font-body-sm text-body-sm text-on-surface">الاسم الكامل</label>
<input name="full_name" value="<?= htmlspecialchars((string) ($addStaffOldInput['full_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-charcoal bg-paper-white px-4 py-3 outline-none focus:border-royal-indigo focus:border-2 transition-all" type="text" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-body-sm text-on-surface">البريد الإلكتروني</label>
<input name="email" value="<?= htmlspecialchars((string) ($addStaffOldInput['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-charcoal bg-paper-white px-4 py-3 outline-none focus:border-royal-indigo focus:border-2 transition-all" type="email" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-body-sm text-on-surface">رقم الهاتف</label>
<input name="phone_number" value="<?= htmlspecialchars((string) ($addStaffOldInput['phone_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-charcoal bg-paper-white px-4 py-3 outline-none focus:border-royal-indigo focus:border-2 transition-all" type="text" required />
</div>

<div class="space-y-2">
<label class="font-body-sm text-body-sm text-on-surface">الوظيفة</label>
<select name="role" class="w-full border border-charcoal bg-paper-white px-4 py-3 outline-none focus:border-royal-indigo focus:border-2 transition-all" required>
<option value="" disabled <?= empty($addStaffOldInput['role']) ? 'selected' : '' ?>>اختر الوظيفة...</option>
<?php foreach ($staffRoleOptions as $roleKey => $roleLabel): ?>
<option value="<?= htmlspecialchars($roleKey, ENT_QUOTES, 'UTF-8') ?>" <?= (($addStaffOldInput['role'] ?? '') === $roleKey) ? 'selected' : '' ?>><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="space-y-2">
<label class="font-body-sm text-body-sm text-on-surface">كلمة المرور</label>
<input name="password" class="w-full border border-charcoal bg-paper-white px-4 py-3 outline-none focus:border-royal-indigo focus:border-2 transition-all" type="password" minlength="8" required />
<p class="text-xs text-slate-gray">سيتم تفعيل الحساب مباشرة بعد إنشائه، ويمكن للموظف تسجيل الدخول فوراً.</p>
</div>

<div class="flex items-center justify-between gap-4 pt-2">
<p class="text-sm text-slate-gray">يتم تسجيل عملية إنشاء الموظف في سجل النظام تلقائياً.</p>
<button type="submit" class="bg-royal-indigo text-on-primary font-button text-button px-6 py-3 border border-royal-indigo hover:bg-primary transition-colors">حفظ الموظف</button>
</div>
</form>
</div>
</section>
</main>
</div>
</body>
