<!DOCTYPE html>

<html dir="rtl" lang="ar"><head>
<?php
$pageTitle = 'Scholarly Slate IRB - Auth';
require __DIR__ . '/../layouts/head.php';
?>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            padding: 1rem 1.5rem; border-radius: 0.5rem;
            color: white; font-weight: 500; z-index: 50;
            display: flex; align-items: center; gap: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(150%); transition: transform 0.3s ease-out;
        }
        .toast.show { transform: translateY(0); }
        .toast-success { background-color: #059669; }
        .toast-error { background-color: #dc2626; }
    </style>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed-dim": "#c3c0ff",
                        "forest": "#166534",
                        "secondary-fixed": "#e0e3e5",
                        "on-background": "#1a1c1e",
                        "surface": "#f9f9fc",
                        "secondary-container": "#e0e3e5",
                        "on-primary-fixed-variant": "#3e3c8f",
                        "surface-variant": "#e2e2e5",
                        "inverse-on-surface": "#f0f0f3",
                        "inverse-surface": "#2f3133",
                        "on-primary": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "surface-dim": "#dadadc",
                        "surface-container": "#eeeef0",
                        "surface-container-lowest": "#ffffff",
                        "primary-container": "#312e81",
                        "primary-fixed": "#e2dfff",
                        "charcoal": "#1A1C1E",
                        "cool-slate": "#F8FAFC",
                        "outline": "#777682",
                        "tertiary-fixed": "#ffdbc7",
                        "on-surface": "#1a1c1e",
                        "primary": "#1a146b",
                        "slate-gray": "#64748B",
                        "surface-bright": "#f9f9fc",
                        "on-error": "#ffffff",
                        "royal-indigo": "#312E81",
                        "on-secondary": "#ffffff",
                        "paper-white": "#FDFDFC",
                        "on-secondary-container": "#626567",
                        "on-secondary-fixed-variant": "#444749",
                        "on-secondary-fixed": "#191c1e",
                        "on-tertiary-fixed": "#311300",
                        "surface-tint": "#5654a8",
                        "inverse-primary": "#c3c0ff",
                        "tertiary-fixed-dim": "#ffb688",
                        "secondary": "#5c5f61",
                        "background": "#f9f9fc",
                        "error-container": "#ffdad6",
                        "on-tertiary-fixed-variant": "#70380b",
                        "secondary-fixed-dim": "#c4c7c9",
                        "outline-variant": "#c8c5d3",
                        "tertiary-container": "#5f2b00",
                        "surface-container-highest": "#e2e2e5",
                        "on-primary-container": "#9c9af4",
                        "on-surface-variant": "#474651",
                        "on-primary-fixed": "#100563",
                        "on-error-container": "#93000a",
                        "surface-container-low": "#f3f3f6",
                        "surface-container-high": "#e8e8ea",
                        "crimson": "#991B1B",
                        "on-tertiary-container": "#de915e",
                        "error": "#ba1a1a",
                        "tertiary": "#3e1a00"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "edge-margin": "2rem",
                        "section-stack": "3rem",
                        "gutter": "1.5rem",
                        "form-gap": "1.25rem",
                        "container-max": "1200px"
                    },
                    "fontFamily": {
                        "display-md": ["Amiri"],
                        "body-sm": ["Tajawal"],
                        "display-lg": ["Amiri"],
                        "h1": ["Amiri"],
                        "body-lg": ["Tajawal"],
                        "numeral": ["Tajawal"],
                        "button": ["Tajawal"]
                    },
                    "fontSize": {
                        "display-md": ["32px", { "lineHeight": "1.2", "fontWeight": "700" }],
                        "body-sm": ["13px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "display-lg": ["40px", { "lineHeight": "1.2", "fontWeight": "700" }],
                        "h1": ["24px", { "lineHeight": "1.4", "fontWeight": "700" }],
                        "body-lg": ["16px", { "lineHeight": "1.6", "fontWeight": "500" }],
                        "numeral": ["14px", { "lineHeight": "1", "letterSpacing": "0.02em", "fontWeight": "500" }],
                        "button": ["15px", { "lineHeight": "1", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-on-surface antialiased h-screen w-full overflow-hidden flex selection:bg-primary-fixed-dim selection:text-royal-indigo">
<!-- Right Panel: Form (RTL Start) -->
<main class="w-full lg:w-1/2 h-full bg-paper-white flex flex-col justify-center px-8 md:px-16 lg:px-24 xl:px-32 relative z-10">
<!-- Header / Logo -->
<header class="mb-12">
<h1 class="font-h1 text-h1 text-charcoal mb-1">مجلس المراجعة المؤسسية</h1>
<p class="font-body-sm text-body-sm text-outline uppercase tracking-widest">Institutional Review Board</p>
</header>
<!-- Form Container -->
<div class="w-full max-w-md">
<!-- Tabs -->
            <div class="flex w-full mb-8">
                <button class="flex-1 pb-3 text-center border-b-2 border-charcoal font-button text-button text-charcoal focus:outline-none">
                    تسجيل الدخول
                </button>
                <a href="/register" class="flex-1 pb-3 text-center border-b border-charcoal font-button text-button text-outline hover:text-charcoal focus:outline-none transition-colors">
                    إنشاء حساب
                </a>
            </div>
<!-- Login Form -->
<form id="loginForm" action="#" class="flex flex-col gap-form-gap" method="POST">
<!-- Email Input -->
<div class="flex flex-col gap-2">
<label class="font-body-sm text-body-sm text-charcoal font-bold" for="email">البريد الإلكتروني المؤسسي</label>
<input autocomplete="email" class="w-full p-3 bg-cool-slate border border-charcoal rounded-none font-body-lg text-body-lg text-charcoal focus:bg-paper-white focus:outline-none focus:border-royal-indigo focus:ring-1 focus:ring-royal-indigo transition-colors" id="email" name="email" placeholder="researcher@university.edu" required="" type="email"/>
</div>
<!-- Password Input -->
<div class="flex flex-col gap-2">
<div class="flex justify-between items-end">
<label class="font-body-sm text-body-sm text-charcoal font-bold" for="password">كلمة المرور</label>
<a class="font-body-sm text-body-sm text-outline hover:text-charcoal underline underline-offset-2 transition-colors" href="#">نسيت كلمة المرور؟</a>
</div>
<input autocomplete="current-password" class="w-full p-3 bg-cool-slate border border-charcoal rounded-none font-body-lg text-body-lg text-charcoal focus:bg-paper-white focus:outline-none focus:border-royal-indigo focus:ring-1 focus:ring-royal-indigo transition-colors" id="password" name="password" placeholder="••••••••" required="" type="password"/>
</div>
<!-- Submit Button -->
<button class="w-full mt-4 bg-royal-indigo text-paper-white py-4 font-button text-button rounded-none border border-royal-indigo hover:bg-primary transition-colors flex justify-center items-center gap-2 group" type="submit">
<span>متابعة</span>
<span class="material-symbols-outlined text-paper-white transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
</button>
</form>
<!-- Footer Links -->
<div class="mt-12 flex flex-col gap-2 border-t border-charcoal pt-6">
<p class="font-body-sm text-body-sm text-outline">
                    بالدخول إلى هذا النظام، فإنك توافق على <a class="text-charcoal underline hover:text-royal-indigo" href="#">بروتوكول الخصوصية</a> و <a class="text-charcoal underline hover:text-royal-indigo" href="#">المعايير الأخلاقية</a> الخاصة بمجلس المراجعة المؤسسية.
                </p>
</div>
</div>
</main>
<!-- Left Panel: Quote/Branding (RTL End) -->
<aside class="hidden lg:flex w-1/2 h-full relative flex-col justify-between p-16 overflow-hidden bg-primary" data-alt="Intricate geometric Arabic patterns creating a sophisticated academic atmosphere, tinted in deep indigo" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAuDqi_Ls0kDkZx8Bj2tZ0p_qJp8K13zi08ZVrjjXSozCbQCQZJN_AfZRP9pkEnihZFnLkUaBxduiJOup97VBq0cKVK6mI8kcnj9Aj_Oqg_WO42Kd3EJZzf9kgZQlgez5v2XgOWWnMcU3y2YUI1-tyJaTB-fc3thUkZ_Rpw0GNdKggpWcpsyOaQPOeiSfObhjgUWxY39nJG_D1efgBQwfMNzpR0uuRMdFoq7vFRdx5CHgGKEv_CZ-tRbDm3NVYQN8dRiHMDZGpj2ywJ'); background-size: cover; background-position: center;">
<!-- Deep Indigo Overlay -->
<div class="absolute inset-0 bg-primary/90 mix-blend-multiply z-0"></div>
<div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-primary/60 z-0"></div>
<!-- Content -->
<div class="relative z-10 flex flex-col h-full justify-between" dir="rtl">
<!-- Branding -->
<div class="flex items-center gap-3 text-primary-fixed">
<span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
<span class="font-body-lg text-body-lg uppercase tracking-widest font-bold">بوابة البحث العلمي</span>
</div>
<!-- Quote -->
<blockquote class="max-w-2xl self-start text-right">
<p class="font-display-lg text-display-lg text-paper-white leading-relaxed mb-8 font-bold" style="line-height: 1.4;">
                    "النزاهة الأكاديمية الصارمة هي أساس كل تقدم علمي موثوق."
                </p>
<footer class="flex items-center gap-4 justify-start">
<div class="h-[2px] w-16 bg-primary-fixed-dim"></div>
<span class="font-body-lg text-body-lg text-primary-fixed-dim tracking-wide font-bold">المعيار الذهبي لأخلاقيات البحث</span>
</footer>
</blockquote>
<!-- Bottom Note -->
<div class="text-right">
<p class="font-numeral text-numeral text-primary-fixed-dim opacity-70">
                    IRB.SYS.V2.0.4 // الأرشيف الآمن
                </p>
</div>
</div>
</aside>
<!-- Toast Container -->
<div id="toast" class="toast">
    <span id="toastIcon" class="material-symbols-outlined"></span>
    <span id="toastMessage"></span>
</div>

<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const msg = document.getElementById('toastMessage');

    toast.className = `toast show toast-${type}`;
    icon.textContent = type === 'success' ? 'check_circle' : 'error';
    msg.textContent = message;

    setTimeout(() => { toast.classList.remove('show'); }, 4000);
}

document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const submitBtn = this.querySelector('button[type="submit"]');

    if (!email || !password) {
        showToast('يرجى ملء جميع الحقول', 'error');
        return;
    }

    try {
        submitBtn.disabled = true;
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.innerHTML = 'جاري التحقق...';

        const response = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok) {
            showToast('تم تسجيل الدخول بنجاح! جاري التحويل...');
            setTimeout(() => { window.location.href = '/'; }, 1500);
        } else {
            showToast(result.error || 'فشل تسجيل الدخول', 'error');
        }
    } catch (error) {
        showToast('حدث خطأ في الاتصال بالخادم', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>متابعة</span><span class="material-symbols-outlined text-paper-white transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>';
    }
});
</script>
</body></html>