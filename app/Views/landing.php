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
<body class="bg-paper-white text-on-background min-h-screen flex flex-col font-body-lg">
<!-- TopNavBar -->
<header class="bg-slate-50 dark:bg-slate-950 docked full-width top-0 border-b-2 border-slate-900 dark:border-slate-100 flex justify-between items-center w-full px-8 h-20 rtl">
<div class="flex items-center gap-8">
<a class="text-xl font-black text-[#312E81] dark:text-indigo-300 uppercase font-h1" href="#">نظام أخلاقيات البحث العلمي</a>
</div>
<div class="flex items-center gap-4">
<?php if ($currentUser): ?>
<a class="inline-flex items-center justify-center bg-royal-indigo text-on-primary hover:bg-primary-container px-5 py-2 font-button transition-colors" href="<?php echo htmlspecialchars($dashboardTarget['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($dashboardTarget['label'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
<?php else: ?>
<a class="hidden md:inline-flex items-center justify-center border border-charcoal bg-paper-white text-on-surface hover:bg-surface-container px-4 py-2 font-button transition-colors" href="<?php echo BASE_URL; ?>/register">
                إنشاء حساب
            </a>
<a class="inline-flex items-center justify-center bg-royal-indigo text-on-primary hover:bg-primary-container px-5 py-2 font-button transition-colors" href="<?php echo BASE_URL; ?>/login">
                تسجيل الدخول
            </a>
<?php endif; ?>
</div>
</header>
<!-- Main Canvas -->
<main class="flex-grow max-w-container-max mx-auto w-full px-edge-margin pt-section-stack pb-section-stack flex flex-col gap-section-stack">
<!-- Hero Section -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-gutter items-center border-b border-outline-variant pb-section-stack">
<div class="flex flex-col gap-6">
<span class="text-royal-indigo font-button tracking-wider uppercase inline-flex items-center gap-2">
<span class="w-8 h-[1px] bg-royal-indigo inline-block"></span>
                    منصة مراجعة مؤسسية
                </span>
<h1 class="font-display-lg text-charcoal leading-tight">
                    نظام إدارة أخلاقيات البحث العلمي
                </h1>
<p class="font-body-lg text-on-surface-variant max-w-xl">
                    منصة رقمية موحدة لتقديم، مراجعة، وإدارة مقترحات الأبحاث العلمية وفقاً لأعلى المعايير الأخلاقية والضوابط الأكاديمية العالمية.
                </p>
<div class="flex flex-wrap gap-4 mt-4">
<a href="<?php echo htmlspecialchars($heroPrimaryCta['href'], ENT_QUOTES, 'UTF-8'); ?>" class="bg-royal-indigo text-on-primary px-8 py-3 font-button hover:bg-primary transition-colors flex items-center gap-2 inline-flex">
<span><?php echo htmlspecialchars($heroPrimaryCta['label'], ENT_QUOTES, 'UTF-8'); ?></span>
<span class="material-symbols-outlined text-sm">arrow_back</span>
</a>
<button class="border border-charcoal bg-paper-white text-charcoal px-8 py-3 font-button hover:bg-surface-container-low transition-colors">
                        استعراض الدليل
                    </button>
</div>
</div>
<div class="relative h-[400px] border border-charcoal overflow-hidden p-2 bg-cool-slate">
<img alt="وثائق أكاديمية مرتبة على مكتبة خشبية، إضاءة درامية جانبية" class="w-full h-full object-cover filter grayscale opacity-90 mix-blend-multiply" data-alt="neatly stacked academic journals and research papers on a dark wooden desk with dramatic side lighting in a quiet library" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-0fOgjFMUrqQ8KuByCsY4DK8L99z2t16-ACumtSKyDZ01uXbS_urDYDVvebo92O3rf1zwH0LJXY9V2qEW1gvpF8zhtyNWch3k-Bwk-klK3eMxZhJgvcoFSIt8TJu4zl9OK5mfHTLGI_8M2ixpW32LtncSXeWj06_vuMl4oIC3IviPsBgIRGoSpfnzCLWprQhXKcT2VjFOjnchmzx_6nSAq6nriAW9JqUW9I0aXvrYRWylTMWBsk3fyVlDJJDAoxjAZglUlGzYIyKl"/>
<!-- Abstract geometric overlay -->
<div class="absolute inset-0 border-[0.5px] border-charcoal opacity-20 m-6 pointer-events-none"></div>
<div class="absolute inset-0 border-[0.5px] border-charcoal opacity-20 m-12 pointer-events-none"></div>
</div>
</section>
<!-- Added Sections -->
<section class="flex flex-col gap-8 border-b border-outline-variant pb-section-stack">
<div class="flex justify-between items-end border-b border-charcoal pb-4">
<h2 class="font-h1 text-charcoal">كيف تسير العملية؟</h2>
<a class="font-button text-royal-indigo hover:underline" href="#">عرض المسار الكامل</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
<div class="border border-charcoal bg-paper-white p-5">
<p class="font-numeral text-2xl text-royal-indigo">01</p>
<h3 class="font-h1 text-charcoal mt-2 mb-1">إنشاء الحساب</h3>
<p class="font-body-sm text-on-surface-variant">تسجيل الباحث وتفعيل الحساب.</p>
</div>
<div class="border border-charcoal bg-paper-white p-5">
<p class="font-numeral text-2xl text-royal-indigo">02</p>
<h3 class="font-h1 text-charcoal mt-2 mb-1">رفع المستندات</h3>
<p class="font-body-sm text-on-surface-variant">إرفاق النماذج المطلوبة والتأكد من اكتمالها.</p>
</div>
<div class="border border-charcoal bg-paper-white p-5">
<p class="font-numeral text-2xl text-royal-indigo">03</p>
<h3 class="font-h1 text-charcoal mt-2 mb-1">المراجعة الأخلاقية</h3>
<p class="font-body-sm text-on-surface-variant">تقييم البحث من المراجعين واللجنة المختصة.</p>
</div>
<div class="border border-charcoal bg-paper-white p-5">
<p class="font-numeral text-2xl text-royal-indigo">04</p>
<h3 class="font-h1 text-charcoal mt-2 mb-1">القرار النهائي</h3>
<p class="font-body-sm text-on-surface-variant">اعتماد، طلب تعديل، أو إصدار الشهادة الرقمية.</p>
</div>
</div>
</section>

<section class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-outline-variant pb-section-stack">
<div class="border border-charcoal bg-paper-white p-6">
<h2 class="font-h1 text-charcoal mb-4">قائمة المستندات الأساسية</h2>
<ul class="space-y-3 font-body-sm text-on-surface-variant">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-royal-indigo text-sm">check_circle</span> بروتوكول البحث (Protocol)</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-royal-indigo text-sm">check_circle</span> نموذج طلب المراجعة</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-royal-indigo text-sm">check_circle</span> تعارض المصالح</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-royal-indigo text-sm">check_circle</span> موافقة الباحث الرئيسي</li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-royal-indigo text-sm">check_circle</span> نماذج موافقة المشاركين (حسب نوع البحث)</li>
</ul>
<a href="<?php echo BASE_URL; ?>/register" class="inline-flex mt-5 border border-charcoal bg-cool-slate text-charcoal px-5 py-2 font-button hover:bg-surface-container-low transition-colors">ابدأ التجهيز الآن</a>
</div>
<div class="border border-charcoal bg-cool-slate p-6">
<h2 class="font-h1 text-charcoal mb-4">الامتثال والمعايير</h2>
<p class="font-body-sm text-on-surface-variant mb-4">جميع إجراءات المنصة مبنية على مبادئ النزاهة البحثية وحماية المشاركين والشفافية في التقييم.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 font-body-sm">
<div class="border border-charcoal bg-paper-white px-4 py-3">الموافقة المستنيرة</div>
<div class="border border-charcoal bg-paper-white px-4 py-3">سرية البيانات</div>
<div class="border border-charcoal bg-paper-white px-4 py-3">تقييم المخاطر</div>
<div class="border border-charcoal bg-paper-white px-4 py-3">المساءلة المؤسسية</div>
</div>
</div>
</section>

<section class="border-b border-outline-variant pb-section-stack">
<div class="flex justify-between items-end border-b border-charcoal pb-4 mb-6">
<h2 class="font-h1 text-charcoal">آراء وتجارب الباحثين</h2>
<a class="font-button text-royal-indigo hover:underline" href="#">عرض المزيد</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<article class="border border-charcoal bg-paper-white p-5">
<p class="font-body-sm text-on-surface-variant">"خطوات واضحة وسريعة، وتمكنت من تتبع حالة الطلب بسهولة حتى الاعتماد النهائي."</p>
<p class="font-button text-charcoal mt-4">د. سارة محمود</p>
<p class="text-xs text-slate-gray">كلية الطب</p>
</article>
<article class="border border-charcoal bg-paper-white p-5">
<p class="font-body-sm text-on-surface-variant">"إدارة المراجعات والتعديلات أصبحت أكثر تنظيمًا، خصوصًا مع السجل الكامل لكل نسخة."</p>
<p class="font-button text-charcoal mt-4">د. أحمد ياسر</p>
<p class="text-xs text-slate-gray">كلية الصيدلة</p>
</article>
<article class="border border-charcoal bg-paper-white p-5">
<p class="font-body-sm text-on-surface-variant">"التواصل مع اللجنة وتحديثات الحالة الفورية خففت وقت الانتظار بشكل كبير."</p>
<p class="font-button text-charcoal mt-4">د. منى فوزي</p>
<p class="text-xs text-slate-gray">كلية التمريض</p>
</article>
</div>
</section>

<section class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b border-outline-variant pb-section-stack">
<div class="border border-charcoal bg-paper-white p-6">
<h2 class="font-h1 text-charcoal mb-4">الأسئلة الشائعة</h2>
<div class="space-y-3">
<details class="border border-outline-variant p-4">
<summary class="font-button text-charcoal cursor-pointer">كم تستغرق المراجعة عادة؟</summary>
<p class="font-body-sm text-on-surface-variant mt-2">تختلف المدة حسب نوع البحث واكتمال المستندات، وغالبًا تبدأ المراجعة فور استيفاء الرسوم والمتطلبات.</p>
</details>
<details class="border border-outline-variant p-4">
<summary class="font-button text-charcoal cursor-pointer">هل يمكن تعديل الملفات بعد طلب التعديل؟</summary>
<p class="font-body-sm text-on-surface-variant mt-2">نعم، يمكنك رفع نسخة تعديل جديدة وسيتم تحويل الطلب تلقائيًا إلى دورة مراجعة جديدة.</p>
</details>
<details class="border border-outline-variant p-4">
<summary class="font-button text-charcoal cursor-pointer">ما هي المستندات الإلزامية؟</summary>
<p class="font-body-sm text-on-surface-variant mt-2">يعتمد ذلك على نوع الدراسة، لكن القائمة الأساسية موضحة في قسم المستندات أعلى الصفحة.</p>
</details>
</div>
</div>
<div class="border border-charcoal bg-cool-slate p-6">
<h2 class="font-h1 text-charcoal mb-4">الدعم والتواصل</h2>
<p class="font-body-sm text-on-surface-variant mb-5">فريق الدعم متاح لمساعدتك في التسجيل، رفع الملفات، ومتابعة حالة الطلب.</p>
<div class="space-y-3 font-body-sm text-charcoal">
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">mail</span> irb-support@university.edu</p>
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">call</span> +20 100 000 0000</p>
<p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">schedule</span> الأحد - الخميس | 9:00 ص - 3:00 م</p>
</div>
<div class="mt-5 flex flex-wrap gap-3">
<a href="<?php echo BASE_URL; ?>/login" class="bg-royal-indigo text-on-primary px-5 py-2 font-button hover:bg-primary transition-colors inline-flex">تسجيل الدخول</a>
<a href="<?php echo BASE_URL; ?>/register" class="border border-charcoal bg-paper-white text-charcoal px-5 py-2 font-button hover:bg-surface-container-low transition-colors inline-flex">إنشاء حساب</a>
</div>
</div>
</section>
<!-- Workflow Section (Bento Grid Style) -->
<section class="flex flex-col gap-8">
<div class="flex justify-between items-end border-b border-charcoal pb-4">
<h2 class="font-h1 text-charcoal">دورة حياة المراجعة الأخلاقية</h2>
<a class="font-button text-royal-indigo hover:underline flex items-center gap-1" href="#">
                    عرض المخطط التفصيلي <span class="material-symbols-outlined text-sm">open_in_new</span>
</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<!-- Step 1 & 2 -->
<div class="md:col-span-1 flex flex-col gap-6">
<div class="border border-charcoal p-6 bg-paper-white relative group hover:bg-cool-slate transition-colors h-full flex flex-col">
<div class="text-royal-indigo font-numeral text-4xl font-bold opacity-20 absolute top-4 left-4">01</div>
<span class="material-symbols-outlined text-royal-indigo text-3xl mb-4">app_registration</span>
<h3 class="font-h1 text-charcoal mb-2">التسجيل والتقديم</h3>
<p class="font-body-sm text-on-surface-variant flex-grow">إنشاء حساب باحث وتقديم مقترح البحث مع كافة الوثائق الداعمة ونماذج الموافقة المستنيرة عبر بوابة آمنة.</p>
<div class="mt-4 pt-4 border-t border-outline-variant flex items-center text-xs font-button text-slate-gray">
<span class="material-symbols-outlined text-sm mr-1">schedule</span>
                            وقت التقديم المقدر: ١٥ دقيقة
                        </div>
</div>
</div>
<!-- Step 3 & 4 Main Flow -->
<div class="md:col-span-2 border border-charcoal p-0 bg-cool-slate flex flex-col md:flex-row relative overflow-hidden">
<div class="flex-1 p-8 border-b md:border-b-0 md:border-l border-charcoal relative z-10">
<div class="text-royal-indigo font-numeral text-4xl font-bold opacity-20 absolute top-4 left-4">02</div>
<span class="material-symbols-outlined text-charcoal text-3xl mb-4">payments</span>
<h3 class="font-h1 text-charcoal mb-2">معالجة الرسوم</h3>
<p class="font-body-sm text-on-surface-variant mb-6">سداد رسوم المراجعة الأخلاقية إلكترونياً أو إرفاق إثبات الإعفاء للباحثين التابعين للمؤسسة.</p>
<div class="inline-flex items-center px-2 py-1 border border-charcoal bg-paper-white text-xs font-button text-charcoal">
                            بوابة دفع معتمدة
                        </div>
</div>
<div class="flex-1 p-8 bg-royal-indigo text-paper-white relative z-10">
<div class="text-primary-fixed-dim font-numeral text-4xl font-bold opacity-20 absolute top-4 left-4">03</div>
<span class="material-symbols-outlined text-primary-fixed text-3xl mb-4">gavel</span>
<h3 class="font-h1 text-paper-white mb-2">المراجعة والتقييم</h3>
<p class="font-body-sm text-inverse-primary">مراجعة معمقة مزدوجة التعمية من قبل أعضاء اللجنة لضمان سلامة المشاركين والالتزام بالضوابط الأخلاقية.</p>
<ul class="mt-4 space-y-2 font-body-sm text-primary-fixed-dim border-t border-primary-container pt-4">
<li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-paper-white rounded-none"></div> مراجعة أولية</li>
<li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-paper-white rounded-none"></div> مراجعة اللجان المتخصصة</li>
</ul>
</div>
</div>
<!-- Final Steps -->
<div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-charcoal pt-6">
<div class="flex items-start gap-4">
<div class="w-12 h-12 flex-shrink-0 border border-charcoal flex items-center justify-center bg-paper-white">
<span class="material-symbols-outlined text-charcoal">verified_user</span>
</div>
<div>
<h4 class="font-h1 text-charcoal text-lg mb-1">الاعتماد النهائي</h4>
<p class="font-body-sm text-on-surface-variant">إصدار القرار النهائي (موافقة، تعديلات مطلوبة، أو رفض) مع ملاحظات تفصيلية من اللجنة المراجعة.</p>
</div>
</div>
<div class="flex items-start gap-4">
<div class="w-12 h-12 flex-shrink-0 border border-charcoal flex items-center justify-center bg-cool-slate">
<span class="material-symbols-outlined text-charcoal">workspace_premium</span>
</div>
<div>
<h4 class="font-h1 text-charcoal text-lg mb-1">الشهادة الرقمية</h4>
<p class="font-body-sm text-on-surface-variant">إصدار شهادة الموافقة الأخلاقية الرقمية مع رمز استجابة سريعة (QR Code) للتحقق من المصداقية.</p>
</div>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-slate-900 dark:bg-black docked full-width bottom-0 border-t border-slate-700 flex flex-col md:flex-row justify-between items-center max-w-[1200px] mx-auto px-8 py-10 rtl gap-6 mt-auto">
<div class="text-lg font-bold text-white font-h1">
            نظام إدارة أخلاقيات البحث العلمي
        </div>
<nav class="flex flex-wrap justify-center gap-6">
<a class="text-slate-400 hover:text-white transition-colors hover:text-indigo-300 underline decoration-1 font-body-sm" href="#">سياسة الخصوصية</a>
<a class="text-slate-400 hover:text-white transition-colors hover:text-indigo-300 underline decoration-1 font-body-sm" href="#">شروط الاستخدام</a>
<a class="text-slate-400 hover:text-white transition-colors hover:text-indigo-300 underline decoration-1 font-body-sm" href="#">دليل الباحث</a>
<a class="text-slate-400 hover:text-white transition-colors hover:text-indigo-300 underline decoration-1 font-body-sm" href="#">المكتبة الرقمية</a>
</nav>
<div class="text-indigo-400 font-tajawal text-sm tracking-wide">
            © 2024 نظام إدارة أخلاقيات البحث العلمي. جميع الحقوق محفوظة للمؤسسة الأكاديمية.
        </div>
<!-- 1px horizontal rule logic (visual only for separation as requested by JSON, applied to full width if needed, but container is handled) -->
<div class="w-full h-px bg-slate-700 absolute top-0 left-0 hidden"></div>
</footer>
</body></html>