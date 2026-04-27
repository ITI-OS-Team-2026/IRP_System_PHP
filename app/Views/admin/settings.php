<?php
$pageTitle = 'إعدادات الإدارة - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'settings';
$adminPageHeading = 'إعدادات الإدارة';
$adminPageSubtitle = 'إدارة إعدادات النظام والصلاحيات';
?>

<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
<div class="min-h-screen flex flex-col lg:flex-row-reverse">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<main class="flex-1">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<section class="px-4 md:px-8 py-6">
<div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300 p-6 md:p-8">
<h3 class="font-h1 text-xl text-gray-900 mb-3">صفحة الإعدادات</h3>
<p class="text-sm leading-7 text-gray-600">
هذه الصفحة جاهزة للربط لاحقاً بإعدادات المستخدمين، الصلاحيات، وتفضيلات النظام.
</p>
</div>
</section>
</main>
</div>
</body>
