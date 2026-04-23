<?php
$pageTitle = 'حساب قيد المراجعة - IRB Portal';
require __DIR__ . '/layouts/head.php';
?>
<body class="min-h-screen bg-surface text-on-surface flex items-center justify-center px-6 py-12 font-body-lg">
    <main class="w-full max-w-2xl rounded-2xl border border-outline-variant bg-paper-white p-8 md:p-12 shadow-sm text-right rtl">
        <div class="space-y-6">
            <div class="inline-flex items-center rounded-full bg-surface-container-low px-4 py-2 text-sm font-button text-royal-indigo">
                Awaiting Approval
            </div>
            <h1 class="font-h1 text-charcoal text-3xl md:text-4xl leading-tight">
                حسابك قيد مراجعة الإدارة
            </h1>
            <p class="text-on-surface-variant leading-8 text-base md:text-lg">
                تم إنشاء الحساب بنجاح، لكن لا يمكنك الوصول إلى النظام الآن حتى يقوم المسؤول بتفعيل حسابك.
                بمجرد الموافقة، يمكنك تسجيل الدخول واستخدام النظام بشكل طبيعي.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="/login" class="inline-flex items-center justify-center rounded-full bg-royal-indigo px-6 py-3 font-button text-on-primary hover:bg-primary transition-colors">
                    العودة لتسجيل الدخول
                </a>
                <a href="/" class="inline-flex items-center justify-center rounded-full border border-charcoal px-6 py-3 font-button text-charcoal hover:bg-surface-container transition-colors">
                    الصفحة الرئيسية
                </a>
            </div>
        </div>
    </main>
</body>