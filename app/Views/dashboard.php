<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'لوحة التحكم - IRB Portal';
require __DIR__ . '/layouts/head.php';
?>
<body class="min-h-screen bg-surface text-on-surface flex items-center justify-center px-6 py-12 font-body-lg">
    <main class="w-full max-w-3xl rounded-2xl border border-outline-variant bg-paper-white p-8 md:p-12 shadow-sm text-right rtl">
        <div class="space-y-6">
            <div class="inline-flex items-center rounded-full bg-surface-container-low px-4 py-2 text-sm font-button text-royal-indigo">
                Dashboard
            </div>
            <h1 class="font-h1 text-charcoal text-3xl md:text-4xl leading-tight">
                مرحباً <?= htmlspecialchars($currentUser['name'] ?? 'بك', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-on-surface-variant leading-8 text-base md:text-lg">
                حسابك مفعل الآن ويمكنك متابعة إجراءاتك من هنا.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="/" class="inline-flex items-center justify-center rounded-full border border-charcoal px-6 py-3 font-button text-charcoal hover:bg-surface-container transition-colors">
                    العودة للرئيسية
                </a>
                <a href="/api/logout" class="inline-flex items-center justify-center rounded-full bg-royal-indigo px-6 py-3 font-button text-on-primary hover:bg-primary transition-colors">
                    تسجيل الخروج
                </a>
            </div>
        </div>
    </main>
</body>