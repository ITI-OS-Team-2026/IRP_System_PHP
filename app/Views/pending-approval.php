<?php
$pageTitle = 'حساب قيد المراجعة - IRB Portal';
require __DIR__ . '/layouts/head.php';
?>
<body class="min-h-screen bg-gray-50 text-gray-900 flex items-center justify-center px-6 py-12 font-body-lg">
    <main class="w-full max-w-2xl rounded-xl border border-gray-200 bg-white p-8 md:p-12 shadow-sm hover:shadow-lg transition-all duration-300 text-right rtl">
        <div class="space-y-6">
            <div class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-button text-indigo-700">
                Awaiting Approval
            </div>
            <h1 class="font-h1 text-gray-900 text-3xl md:text-4xl leading-tight">
                حسابك قيد مراجعة الإدارة
            </h1>
            <p class="text-gray-600 leading-8 text-base md:text-lg">
                تم إنشاء الحساب بنجاح، لكن لا يمكنك الوصول إلى النظام الآن حتى يقوم المسؤول بتفعيل حسابك.
                بمجرد الموافقة، يمكنك تسجيل الدخول واستخدام النظام بشكل طبيعي.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="<?php echo BASE_URL; ?>/login" class="inline-flex items-center justify-center rounded-lg bg-indigo-700 px-6 py-3 font-button text-white hover:bg-indigo-800 transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                    العودة لتسجيل الدخول
                </a>
                <a href="<?php echo BASE_URL; ?>/" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 font-button text-gray-700 hover:bg-gray-100 transition-all duration-300">
                    الصفحة الرئيسية
                </a>
            </div>
        </div>
    </main>
</body>