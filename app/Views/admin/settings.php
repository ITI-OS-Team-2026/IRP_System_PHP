<?php
$pageTitle = 'إعدادات الإدارة - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'settings';
$adminPageHeading = 'إعدادات الإدارة';
$adminPageSubtitle = 'إدارة إعدادات النظام والصلاحيات';
?>

<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6">
<div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] p-6 md:p-8">
<h3 class="font-h1 text-xl text-charcoal mb-3">صفحة الإعدادات</h3>
<p class="text-sm leading-7 text-slate-gray">
هذه الصفحة جاهزة للربط لاحقاً بإعدادات المستخدمين، الصلاحيات، وتفضيلات النظام.
</p>
</div>
</section>
</main>
</div>
</body>
