<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تسجيل الدخول - IRB Institutional Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&amp;family=Tajawal:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
        
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#1a146b",
                        "royal-indigo": "#312E81",
                        "charcoal": "#1A1C1E",
                        "secondary": "#5c5f61",
                        "outline": "#777682",
                        "paper-white": "#FDFDFC",
                        "cool-slate": "#F8FAFC",
                        "surface": "#f9f9fc",
                        "primary-fixed-dim": "#c3c0ff"
                    },
                    "fontFamily": {
                        "display-lg": ["Amiri"],
                        "h1": ["Amiri"],
                        "body-lg": ["Tajawal"],
                        "numeral": ["Tajawal"],
                        "button": ["Tajawal"],
                        "body-sm": ["Tajawal"]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .toast {
            position: fixed; bottom: 2rem; right: 2rem;
            padding: 1rem 1.5rem; border-radius: 0.5rem;
            color: white; font-weight: 500; z-index: 50;
            display: flex; align-items: center; gap: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            opacity: 0; pointer-events: none;
            transform: translateY(2rem); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toast.show { opacity: 1; pointer-events: auto; transform: translateY(0); }
        .toast-success { background-color: #059669; }
        .toast-error { background-color: #dc2626; }
    </style>
</head>
<body class="bg-surface text-on-surface antialiased h-screen w-full overflow-hidden flex selection:bg-primary-fixed-dim selection:text-royal-indigo">

<!-- Right Panel: Form (RTL Start) -->
<main class="w-full lg:w-1/2 h-full bg-paper-white flex flex-col justify-center px-8 md:px-16 lg:px-24 xl:px-32 relative z-10">
    <!-- Header / Logo -->
    <header class="mb-12">
        <h1 class="font-h1 text-4xl text-charcoal mb-1">مجلس المراجعة المؤسسية</h1>
        <p class="font-body-sm text-sm text-outline uppercase tracking-widest">Institutional Review Board</p>
    </header>

    <!-- Form Container -->
    <div class="w-full max-w-md">
        <!-- Tabs -->
        <div class="flex w-full mb-8">
            <a href="<?php echo BASE_URL; ?>/login" class="flex-1 pb-3 text-center border-b-2 border-charcoal font-button text-button text-charcoal focus:outline-none">
                تسجيل الدخول
            </a>
            <a href="<?php echo BASE_URL; ?>/register" class="flex-1 pb-3 text-center border-b border-charcoal font-button text-button text-outline hover:text-charcoal focus:outline-none transition-colors">
                إنشاء حساب
            </a>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="flex flex-col gap-6">
            <!-- Email Input -->
            <div class="flex flex-col gap-2">
                <label class="font-body-sm text-sm text-charcoal font-bold" for="email">البريد الإلكتروني المؤسسي</label>
                <input autocomplete="email" class="w-full p-3 bg-cool-slate border border-charcoal rounded-none font-body-lg text-base text-charcoal focus:bg-paper-white focus:outline-none focus:border-royal-indigo focus:ring-1 focus:ring-royal-indigo transition-colors" id="email" name="email" placeholder="researcher@university.edu" required type="email"/>
            </div>

            <!-- Password Input -->
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-end">
                    <label class="font-body-sm text-sm text-charcoal font-bold" for="password">كلمة المرور</label>
                    <a class="font-body-sm text-sm text-outline hover:text-charcoal underline underline-offset-2 transition-colors" href="#">نسيت كلمة المرور؟</a>
                </div>
                <input autocomplete="current-password" class="w-full p-3 bg-cool-slate border border-charcoal rounded-none font-body-lg text-base text-charcoal focus:bg-paper-white focus:outline-none focus:border-royal-indigo focus:ring-1 focus:ring-royal-indigo transition-colors" id="password" name="password" placeholder="••••••••" required type="password"/>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="w-full mt-4 bg-royal-indigo text-paper-white py-4 font-button text-button rounded-none border border-royal-indigo hover:bg-primary transition-colors flex justify-center items-center gap-2 group">
                <span>متابعة</span>
                <span class="material-symbols-outlined text-paper-white transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
            </button>
        </form>

        <!-- Footer Links -->
        <div class="mt-12 flex flex-col gap-2 border-t border-charcoal pt-6">
            <p class="font-body-sm text-sm text-outline">
                بالدخول إلى هذا النظام، فإنك توافق على <a class="text-charcoal underline hover:text-royal-indigo" href="#">بروتوكول الخصوصية</a> و <a class="text-charcoal underline hover:text-royal-indigo" href="#">المعايير الأخلاقية</a> الخاصة بمجلس المراجعة المؤسسية.
            </p>
        </div>
    </div>
</main>

<!-- Left Panel: Quote/Branding -->
<aside class="hidden lg:flex w-1/2 h-full relative flex-col justify-between p-16 overflow-hidden bg-primary" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAuDqi_Ls0kDkZx8Bj2tZ0p_qJp8K13zi08ZVrjjXSozCbQCQZJN_AfZRP9pkEnihZFnLkUaBxduiJOup97VBq0cKVK6mI8kcnj9Aj_Oqg_WO42Kd3EJZzf9kgZQlgez5v2XgOWWnMcU3y2YUI1-tyJaTB-fc3thUkZ_Rpw0GNdKggpWcpsyOaQPOeiSfObhjgUWxY39nJG_D1efgBQwfMNzpR0uuRMdFoq7vFRdx5CHgGKEv_CZ-tRbDm3NVYQN8dRiHMDZGpj2ywJ'); background-size: cover; background-position: center;">
    <!-- Deep Indigo Overlay -->
    <div class="absolute inset-0 bg-primary/90 mix-blend-multiply z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-primary/60 z-0"></div>
    
    <!-- Content -->
    <div class="relative z-10 flex flex-col h-full justify-between" dir="rtl">
        <!-- Branding -->
        <div class="flex items-center gap-3 text-primary-fixed-dim">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
            <span class="font-body-lg text-lg uppercase tracking-widest font-bold">بوابة البحث العلمي</span>
        </div>
        
        <!-- Quote -->
        <blockquote class="max-w-2xl self-start text-right">
            <p class="font-display-lg text-4xl text-paper-white leading-relaxed mb-8 font-bold" style="line-height: 1.4;">
                "النزاهة الأكاديمية الصارمة هي أساس كل تقدم علمي موثوق."
            </p>
            <footer class="flex items-center gap-4 justify-start">
                <div class="h-[2px] w-16 bg-primary-fixed-dim"></div>
                <span class="font-body-lg text-lg text-primary-fixed-dim tracking-wide font-bold">المعيار الذهبي لأخلاقيات البحث</span>
            </footer>
        </blockquote>

        <!-- Bottom Note -->
        <div class="text-right">
            <p class="font-numeral text-sm text-primary-fixed-dim opacity-70">
                IRB.SYS.V2.0.4 // الأرشيف الآمن
            </p>
        </div>
    </div>
</aside>

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
        const submitBtn = document.getElementById('submitBtn');
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'جاري التحقق...';

            const response = await fetch(window.BASE_URL + '/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (response.ok) {
                showToast('تم تسجيل الدخول بنجاح! جاري تحويلك...');
                const target = result.redirect_to || (window.BASE_URL + '/dashboard');
                setTimeout(() => { window.location.href = target; }, 1500);
            } else {
                if (result.redirect_to) {
                    window.location.href = result.redirect_to;
                } else {
                    showToast(result.error || 'فشل تسجيل الدخول', 'error');
                }
            }
        } catch (error) {
            showToast('حدث خطأ في الاتصال بالخادم', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.querySelector('span').textContent = 'متابعة';
        }
    });
</script>
</body>
</html>