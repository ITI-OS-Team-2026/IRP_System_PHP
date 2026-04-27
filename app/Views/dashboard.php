<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'لوحة التحكم - IRB Portal';
require __DIR__ . '/layouts/head.php';
?>
<body class="min-h-screen bg-gray-50 text-gray-900 flex items-center justify-center px-6 py-12 font-body-lg">
    <main class="w-full max-w-3xl rounded-xl border border-gray-200 bg-white p-8 md:p-12 shadow-sm hover:shadow-lg transition-all duration-300 text-right rtl">
        <div class="space-y-6">
            <div class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-button text-indigo-700">
                Dashboard
            </div>
            <h1 class="font-h1 text-gray-900 text-3xl md:text-4xl leading-tight">
                مرحباً <?= htmlspecialchars($currentUser['name'] ?? 'بك', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-gray-600 leading-8 text-base md:text-lg">
                حسابك مفعل الآن ويمكنك متابعة إجراءاتك من هنا.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="<?php echo BASE_URL; ?>/" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 font-button text-gray-700 hover:bg-gray-100 transition-all duration-300">
                    العودة للرئيسية
                </a>
                <a href="<?php echo BASE_URL; ?>/api/logout" class="inline-flex items-center justify-center rounded-lg bg-indigo-700 px-6 py-3 font-button text-white hover:bg-indigo-800 transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                    تسجيل الخروج
                </a>
            </div>
        </div>
    </main>
</body>