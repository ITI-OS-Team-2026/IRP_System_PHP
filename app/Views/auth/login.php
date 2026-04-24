<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تسجيل الدخول - IRB Institutional Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&amp;family=Tajawal:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script>
        // Store the base URL from PHP to JS
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
                        "surface": "#f9f9fc"
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
<body class="bg-surface text-on-surface antialiased h-screen w-full overflow-hidden flex">

<main class="w-full lg:w-1/2 h-full bg-paper-white flex flex-col justify-center px-8 md:px-16 lg:px-24 xl:px-32 relative z-10">
    <header class="mb-12">
        <h1 class="text-3xl font-bold text-charcoal mb-1">مجلس المراجعة المؤسسية</h1>
        <p class="text-sm text-outline uppercase tracking-widest">Institutional Review Board</p>
    </header>

    <div class="w-full max-w-md">
        <!-- Tabs -->
        <div class="flex w-full mb-8">
            <a href="<?php echo BASE_URL; ?>/login" class="flex-1 pb-3 text-center border-b-2 border-charcoal font-bold text-charcoal">
                تسجيل الدخول
            </a>
            <a href="<?php echo BASE_URL; ?>/register" class="flex-1 pb-3 text-center border-b border-outline text-outline hover:text-charcoal transition-colors">
                إنشاء حساب
            </a>
        </div>

        <form id="loginForm" class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-charcoal" for="email">البريد الإلكتروني المؤسسي</label>
                <input class="w-full p-3 bg-cool-slate border border-charcoal rounded-none focus:outline-none focus:ring-2 focus:ring-royal-indigo" id="email" name="email" placeholder="researcher@university.edu" required type="email"/>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-charcoal" for="password">كلمة المرور</label>
                <input class="w-full p-3 bg-cool-slate border border-charcoal rounded-none focus:outline-none focus:ring-2 focus:ring-royal-indigo" id="password" name="password" placeholder="••••••••" required type="password"/>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-royal-indigo text-white py-4 font-bold hover:bg-primary transition-colors flex justify-center items-center gap-2 group">
                <span>دخول</span>
                <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
            </button>
        </form>
    </div>
</main>

<!-- Branding Side Panel (Pattern) -->
<aside class="hidden lg:flex lg:w-1/2 bg-royal-indigo text-white flex-col justify-between p-16 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="relative z-10">
        <div class="flex items-center gap-3 mb-12">
            <span class="material-symbols-outlined text-4xl">account_balance</span>
            <span class="text-2xl font-bold tracking-widest uppercase">IRB Portal</span>
        </div>
    </div>
    <div class="relative z-10 max-w-md">
        <blockquote class="text-4xl font-bold leading-tight mb-6">
            "البحث العلمي هو نافذة الأمة نحو المستقبل، والأخلاقيات هي الإطار الذي يحمي هذا المستقبل."
        </blockquote>
        <p class="text-lg opacity-80">— الميثاق الأخلاقي للبحث العلمي</p>
    </div>
    <div class="relative z-10 text-sm opacity-60">
        © 2024 Institutional Review Board. All rights reserved.
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
                setTimeout(() => { window.location.href = window.BASE_URL + '/dashboard'; }, 1500);
            } else {
                showToast(result.error || 'فشل تسجيل الدخول', 'error');
            }
        } catch (error) {
            showToast('حدث خطأ في الاتصال بالخادم', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.querySelector('span').textContent = 'دخول';
        }
    });
</script>
</body>
</html>