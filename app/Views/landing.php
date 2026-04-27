<!DOCTYPE html>

<html dir="rtl" lang="ar"><head>
<?php
$pageTitle = 'نظام إدارة أخلاقيات البحث العلمي | Scholarly Slate IRB';
require __DIR__ . '/layouts/head.php';
$currentUser = AuthMiddleware::user();

$dashboardByRole = [
    'admin' => [
        'href' => BASE_URL . '/admin/dashboard',
        'label' => 'لوحة الإدارة',
    ],
    'student' => [
        'href' => BASE_URL . '/student/dashboard',
        'label' => 'لوحة الباحث',
    ],
    'sample_size_officer' => [
        'href' => BASE_URL . '/officer/sample-size/queue',
        'label' => 'لوحة مسؤول حجم العينة',
    ],
    'reviewer' => [
        'href' => BASE_URL . '/reviewer/dashboard',
        'label' => 'لوحة المراجع',
    ],
    'manager' => [
        'href' => BASE_URL . '/committee/dashboard',
        'label' => 'لوحة اللجنة',
    ],
];

$currentRole = (string) ($currentUser['role'] ?? '');
$dashboardTarget = $dashboardByRole[$currentRole] ?? [
    'href' => BASE_URL . '/dashboard',
    'label' => 'لوحة التحكم',
];

$heroPrimaryCta = [
    'href' => BASE_URL . '/login',
    'label' => 'بدء تقديم طلب',
];

if ($currentUser) {
    $heroPrimaryCta = [
        'href' => $dashboardTarget['href'],
        'label' => 'الانتقال إلى ' . $dashboardTarget['label'],
    ];
}
?>
</head>
<body class="bg-gray-50 text-on-background min-h-screen flex flex-col font-body-lg">
<!-- TopNavBar -->
<header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-200 shadow-sm flex justify-between items-center w-full px-6 md:px-10 h-16 rtl">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-indigo-700 text-2xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
<a class="text-lg font-bold text-indigo-900 font-h1" href="#">نظام أخلاقيات البحث العلمي</a>
</div>
<div class="flex items-center gap-3">
<?php if ($currentUser): ?>
<a class="inline-flex items-center justify-center bg-indigo-700 text-white hover:bg-indigo-800 px-5 py-2.5 rounded-lg font-button text-sm shadow-sm hover:shadow transition-all" href="<?php echo htmlspecialchars($dashboardTarget['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($dashboardTarget['label'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
<?php else: ?>
<a class="hidden md:inline-flex items-center justify-center border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 px-5 py-2.5 rounded-lg font-button text-sm transition-all" href="<?php echo BASE_URL; ?>/register">
                إنشاء حساب
            </a>
<a class="inline-flex items-center justify-center bg-indigo-700 text-white hover:bg-indigo-800 px-5 py-2.5 rounded-lg font-button text-sm shadow-sm hover:shadow transition-all" href="<?php echo BASE_URL; ?>/login">
                تسجيل الدخول
            </a>
<?php endif; ?>
</div>
</header>
<!-- Main Canvas -->
<main class="flex-grow w-full flex flex-col">
<!-- Hero Section -->
<section class="bg-white py-16 md:py-24">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
<div class="flex flex-col gap-6">
<span class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 font-button text-sm px-4 py-1.5 rounded-full w-fit">
<span class="material-symbols-outlined text-base">verified</span>
                    منصة مراجعة مؤسسية معتمدة
                </span>
<h1 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight font-h1">
                    نظام إدارة أخلاقيات<br/>البحث العلمي
                </h1>
<p class="text-lg text-gray-600 leading-relaxed max-w-xl font-body-lg">
                    منصة رقمية موحدة لتقديم، مراجعة، وإدارة مقترحات الأبحاث العلمية وفقاً لأعلى المعايير الأخلاقية والضوابط الأكاديمية العالمية.
                </p>
<div class="flex flex-wrap gap-4 mt-2">
<a href="<?php echo htmlspecialchars($heroPrimaryCta['href'], ENT_QUOTES, 'UTF-8'); ?>" class="bg-indigo-700 text-white px-8 py-3.5 rounded-lg font-button shadow-md hover:bg-indigo-800 hover:shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2 inline-flex">
<span><?php echo htmlspecialchars($heroPrimaryCta['label'], ENT_QUOTES, 'UTF-8'); ?></span>
<span class="material-symbols-outlined text-sm">arrow_back</span>
</a>
<a href="#workflow" class="border border-gray-300 bg-white text-gray-700 px-8 py-3.5 rounded-lg font-button hover:bg-gray-100 transition-all inline-flex items-center gap-2">
<span class="material-symbols-outlined text-sm">menu_book</span>
                        استعراض الدليل
                    </a>
</div>
</div>
<div class="relative h-[420px] rounded-2xl overflow-hidden shadow-2xl">
<img alt="باحث في مختبر طبي" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&q=80"/>
<div class="absolute inset-0 bg-gradient-to-l from-indigo-900/60 via-indigo-900/20 to-transparent"></div>
<div class="absolute bottom-6 right-6 bg-white/90 backdrop-blur-sm rounded-xl p-4 shadow-lg max-w-[220px]">
<div class="flex items-center gap-2 mb-1">
<span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
<span class="font-button text-sm text-gray-800">معتمد أخلاقياً</span>
</div>
<p class="text-xs text-gray-500 font-body-sm">مراجعة مؤسسية معتمدة لضمان النزاهة</p>
</div>
</div>
</div>
</section>
<!-- Stats Bar -->
<section class="bg-indigo-700 py-10">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
<div>
<p class="text-3xl font-bold text-white font-numeral">+500</p>
<p class="text-indigo-200 mt-1 font-body-sm text-sm">بحث معتمد</p>
</div>
<div>
<p class="text-3xl font-bold text-white font-numeral">14</p>
<p class="text-indigo-200 mt-1 font-body-sm text-sm">يوم متوسط المراجعة</p>
</div>
<div>
<p class="text-3xl font-bold text-white font-numeral">50+</p>
<p class="text-indigo-200 mt-1 font-body-sm text-sm">مراجع معتمد</p>
</div>
<div>
<p class="text-3xl font-bold text-white font-numeral">98%</p>
<p class="text-indigo-200 mt-1 font-body-sm text-sm">نسبة رضا الباحثين</p>
</div>
</div>
</section>
<!-- How It Works -->
<section class="py-20 bg-white">
<div class="max-w-7xl mx-auto px-6">
<h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12 font-h1">كيف تسير العملية؟</h2>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
<div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-6 text-center border border-gray-100">
<div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4"><span class="text-indigo-700 font-bold font-numeral">01</span></div>
<h3 class="font-h1 text-gray-900 mb-2">إنشاء الحساب</h3>
<p class="font-body-sm text-gray-600">تسجيل الباحث وتفعيل الحساب.</p>
</div>
<div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-6 text-center border border-gray-100">
<div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4"><span class="text-indigo-700 font-bold font-numeral">02</span></div>
<h3 class="font-h1 text-gray-900 mb-2">رفع المستندات</h3>
<p class="font-body-sm text-gray-600">إرفاق النماذج المطلوبة والتأكد من اكتمالها.</p>
</div>
<div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-6 text-center border border-gray-100">
<div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4"><span class="text-indigo-700 font-bold font-numeral">03</span></div>
<h3 class="font-h1 text-gray-900 mb-2">المراجعة الأخلاقية</h3>
<p class="font-body-sm text-gray-600">تقييم البحث من المراجعين واللجنة المختصة.</p>
</div>
<div class="bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all p-6 text-center border border-gray-100">
<div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4"><span class="text-indigo-700 font-bold font-numeral">04</span></div>
<h3 class="font-h1 text-gray-900 mb-2">القرار النهائي</h3>
<p class="font-body-sm text-gray-600">اعتماد، طلب تعديل، أو إصدار الشهادة الرقمية.</p>
</div>
</div>
</div>
</section>

<section class="py-20 bg-gray-50">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
<h2 class="text-xl font-bold text-gray-900 mb-6 font-h1">قائمة المستندات الأساسية</h2>
<ul class="space-y-4 font-body-sm text-gray-700">
<li class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-600 text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span> بروتوكول البحث (Protocol)</li>
<li class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-600 text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span> نموذج طلب المراجعة</li>
<li class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-600 text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span> تعارض المصالح</li>
<li class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-600 text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span> موافقة الباحث الرئيسي</li>
<li class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-600 text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span> نماذج موافقة المشاركين (حسب نوع البحث)</li>
</ul>
<a href="<?php echo BASE_URL; ?>/register" class="inline-flex mt-6 bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-800 shadow-sm hover:shadow transition-all">ابدأ التجهيز الآن</a>
</div>
<div class="bg-indigo-50 rounded-xl shadow-sm p-8 border border-indigo-100">
<h2 class="text-xl font-bold text-gray-900 mb-4 font-h1">الامتثال والمعايير</h2>
<p class="font-body-sm text-gray-600 mb-6">جميع إجراءات المنصة مبنية على مبادئ النزاهة البحثية وحماية المشاركين والشفافية في التقييم.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 font-body-sm">
<div class="bg-white rounded-lg px-4 py-3 shadow-sm flex items-center gap-2"><span class="material-symbols-outlined text-indigo-600 text-sm">shield</span> الموافقة المستنيرة</div>
<div class="bg-white rounded-lg px-4 py-3 shadow-sm flex items-center gap-2"><span class="material-symbols-outlined text-indigo-600 text-sm">lock</span> سرية البيانات</div>
<div class="bg-white rounded-lg px-4 py-3 shadow-sm flex items-center gap-2"><span class="material-symbols-outlined text-indigo-600 text-sm">warning</span> تقييم المخاطر</div>
<div class="bg-white rounded-lg px-4 py-3 shadow-sm flex items-center gap-2"><span class="material-symbols-outlined text-indigo-600 text-sm">gavel</span> المساءلة المؤسسية</div>
</div>
</div>
</div>
</section>

<section class="py-20 bg-white">
<div class="max-w-7xl mx-auto px-6">
<h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12 font-h1">آراء وتجارب الباحثين</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-gray-100">
<div class="flex items-center gap-1 mb-4 text-amber-400"><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span></div>
<p class="font-body-sm text-gray-600 leading-relaxed mb-6">"خطوات واضحة وسريعة، وتمكنت من تتبع حالة الطلب بسهولة حتى الاعتماد النهائي."</p>
<div class="flex items-center gap-3 pt-4 border-t border-gray-100">
<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">س.م</div>
<div><p class="font-button text-gray-900 text-sm">د. سارة محمود</p><p class="text-xs text-gray-500">كلية الطب - قسم الجراحة</p></div>
</div>
</article>
<article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-gray-100">
<div class="flex items-center gap-1 mb-4 text-amber-400"><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span></div>
<p class="font-body-sm text-gray-600 leading-relaxed mb-6">"إدارة المراجعات والتعديلات أصبحت أكثر تنظيمًا، خصوصًا مع السجل الكامل لكل نسخة."</p>
<div class="flex items-center gap-3 pt-4 border-t border-gray-100">
<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">أ.ي</div>
<div><p class="font-button text-gray-900 text-sm">د. أحمد ياسر</p><p class="text-xs text-gray-500">كلية الطب - قسم الباطنة</p></div>
</div>
</article>
<article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-6 border border-gray-100">
<div class="flex items-center gap-1 mb-4 text-amber-400"><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span><span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">star</span></div>
<p class="font-body-sm text-gray-600 leading-relaxed mb-6">"التواصل مع اللجنة وتحديثات الحالة الفورية خففت وقت الانتظار بشكل كبير."</p>
<div class="flex items-center gap-3 pt-4 border-t border-gray-100">
<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">م.ف</div>
<div><p class="font-button text-gray-900 text-sm">د. منى فوزي</p><p class="text-xs text-gray-500">كلية الطب - قسم العيون</p></div>
</div>
</article>
</div>
</div>
</section>

<!-- FAQ & Support -->
<section class="py-20 bg-gray-50" id="workflow">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
<h2 class="text-xl font-bold text-gray-900 mb-6 font-h1">الأسئلة الشائعة</h2>
<div class="space-y-3">
<details class="group bg-gray-50 rounded-lg p-4 hover:bg-indigo-50 transition-colors">
<summary class="font-button text-gray-900 cursor-pointer flex justify-between items-center"><span>كم تستغرق المراجعة عادة؟</span><span class="material-symbols-outlined text-gray-400 group-open:rotate-180 transition-transform text-sm">expand_more</span></summary>
<p class="font-body-sm text-gray-600 mt-3 pr-2">تختلف المدة حسب نوع البحث واكتمال المستندات، وغالبًا تبدأ المراجعة فور استيفاء الرسوم والمتطلبات.</p>
</details>
<details class="group bg-gray-50 rounded-lg p-4 hover:bg-indigo-50 transition-colors">
<summary class="font-button text-gray-900 cursor-pointer flex justify-between items-center"><span>هل يمكن تعديل الملفات بعد طلب التعديل؟</span><span class="material-symbols-outlined text-gray-400 group-open:rotate-180 transition-transform text-sm">expand_more</span></summary>
<p class="font-body-sm text-gray-600 mt-3 pr-2">نعم، يمكنك رفع نسخة تعديل جديدة وسيتم تحويل الطلب تلقائيًا إلى دورة مراجعة جديدة.</p>
</details>
<details class="group bg-gray-50 rounded-lg p-4 hover:bg-indigo-50 transition-colors">
<summary class="font-button text-gray-900 cursor-pointer flex justify-between items-center"><span>ما هي المستندات الإلزامية؟</span><span class="material-symbols-outlined text-gray-400 group-open:rotate-180 transition-transform text-sm">expand_more</span></summary>
<p class="font-body-sm text-gray-600 mt-3 pr-2">يعتمد ذلك على نوع الدراسة، لكن القائمة الأساسية موضحة في قسم المستندات أعلى الصفحة.</p>
</details>
</div>
</div>
<div class="bg-indigo-700 rounded-xl shadow-lg p-8 text-white">
<h2 class="text-xl font-bold mb-4 font-h1">الدعم والتواصل</h2>
<p class="font-body-sm text-indigo-200 mb-6">فريق الدعم متاح لمساعدتك في التسجيل، رفع الملفات، ومتابعة حالة الطلب.</p>
<div class="space-y-4 font-body-sm">
<p class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-200">mail</span> irb-support@university.edu</p>
<p class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-200">call</span> +20 100 000 0000</p>
<p class="flex items-center gap-3"><span class="material-symbols-outlined text-indigo-200">schedule</span> الأحد - الخميس | 9:00 ص - 3:00 م</p>
</div>
<div class="mt-6 flex flex-wrap gap-3">
<a href="<?php echo BASE_URL; ?>/login" class="bg-white text-indigo-700 px-6 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-50 shadow-sm transition-all inline-flex">تسجيل الدخول</a>
<a href="<?php echo BASE_URL; ?>/register" class="border border-indigo-400 text-white px-6 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-600 transition-all inline-flex">إنشاء حساب</a>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-gray-900 border-t border-gray-800 mt-auto">
<div class="max-w-7xl mx-auto px-6 py-12">
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
<div>
<div class="flex items-center gap-2 mb-4">
<span class="material-symbols-outlined text-indigo-400 text-2xl" style="font-variation-settings:'FILL' 1;">account_balance</span>
<span class="text-lg font-bold text-white font-h1">نظام أخلاقيات البحث</span>
</div>
<p class="font-body-sm text-gray-400 text-sm leading-relaxed">منصة مؤسسية معتمدة لإدارة ومراجعة مقترحات الأبحاث وفق أعلى المعايير الأخلاقية.</p>
</div>
<div>
<h3 class="font-button text-white text-sm mb-4">روابط سريعة</h3>
<nav class="flex flex-col gap-2">
<a class="text-gray-400 hover:text-indigo-300 transition-colors font-body-sm text-sm" href="#">سياسة الخصوصية</a>
<a class="text-gray-400 hover:text-indigo-300 transition-colors font-body-sm text-sm" href="#">شروط الاستخدام</a>
<a class="text-gray-400 hover:text-indigo-300 transition-colors font-body-sm text-sm" href="#">دليل الباحث</a>
</nav>
</div>
<div>
<h3 class="font-button text-white text-sm mb-4">تواصل معنا</h3>
<div class="flex flex-col gap-2 font-body-sm text-gray-400 text-sm">
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-indigo-400">mail</span> irb-support@university.edu</p>
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-indigo-400">call</span> +20 100 000 0000</p>
</div>
</div>
</div>
<div class="border-t border-gray-800 pt-6 text-center">
<p class="text-gray-500 font-body-sm text-xs">© 2024 نظام إدارة أخلاقيات البحث العلمي. جميع الحقوق محفوظة للمؤسسة الأكاديمية.</p>
</div>
</div>
</footer>
</body></html>