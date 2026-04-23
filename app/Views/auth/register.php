<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إنشاء حساب جديد - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#1a146b",
                        "royal-indigo": "#312E81",
                        "paper-white": "#FDFDFC",
                        "charcoal": "#1A1C1E",
                        "secondary": "#5c5f61",
                        "on-primary": "#ffffff"
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
<body class="bg-paper-white text-on-surface min-h-screen flex font-body-lg">

<main class="flex-1 flex flex-col md:flex-row min-h-screen">
    <!-- Branding Side (Restored Pattern Style) -->
    <div class="hidden md:flex md:w-1/2 bg-royal-indigo text-on-primary flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <span class="material-symbols-outlined text-4xl">account_balance</span>
                <span class="font-bold text-2xl tracking-widest uppercase">IRB Portal</span>
            </div>
        </div>
        <div class="relative z-10 max-w-md">
            <blockquote class="text-3xl leading-tight mb-6 font-bold">
                "البحث العلمي هو نافذة الأمة نحو المستقبل، والأخلاقيات هي الإطار الذي يحمي هذا المستقبل."
            </blockquote>
            <p class="text-lg opacity-80">— الميثاق الأخلاقي للبحث العلمي</p>
        </div>
        <div class="relative z-10 text-sm opacity-60">
            © 2024 Institutional Review Board. All rights reserved.
        </div>
    </div>

    <!-- Form Side -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-paper-white relative">
        <div class="w-full max-w-lg">
            <div class="mb-10">
                <h1 class="text-4xl font-bold text-charcoal mb-2">إنشاء حساب جديد</h1>
                <p class="text-lg text-secondary">أدخل بياناتك الأساسية للبدء في استخدام المنصة.</p>
            </div>

            <form id="registerForm" class="space-y-6">
                <!-- Full Name -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-charcoal" for="fullName">الاسم بالكامل</label>
                    <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo transition-all rounded-none" id="fullName" name="full_name" placeholder="الاسم الرباعي" type="text" required/>
                </div>
                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-charcoal" for="email">البريد الإلكتروني</label>
                    <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo transition-all rounded-none" id="email" name="email" placeholder="user@university.edu.eg" type="email" required/>
                </div>
                <!-- Phone -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-charcoal" for="phone">رقم الهاتف</label>
                    <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo transition-all rounded-none" id="phone" name="phone_number" placeholder="+20 10X XXX XXXX" type="tel" required/>
                </div>
                <!-- Password -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-charcoal" for="password">كلمة المرور</label>
                    <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo transition-all rounded-none" id="password" name="password" placeholder="••••••••" type="password" required/>
                </div>

                <div class="pt-4 flex flex-col gap-4">
                    <button id="submitBtn" class="w-full bg-royal-indigo text-white py-4 px-6 hover:bg-primary transition-colors duration-200 rounded-none flex justify-center items-center gap-2 font-bold" type="submit">
                        تسجيل الحساب
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                    <div class="text-center mt-2">
                        <a class="text-royal-indigo hover:underline font-bold" href="/ITI/IRP_System_PHP/public/login">
                            لديك حساب بالفعل؟ تسجيل الدخول
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

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

    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        const formData = {
            full_name: document.getElementById('fullName').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone_number: document.getElementById('phone').value.trim(),
            password: document.getElementById('password').value,
            role: 'student'
        };

        // 1. Name Validation (Quadruple Name)
        const nameParts = formData.full_name.split(/\s+/);
        if (nameParts.length < 4) {
            showToast('الاسم يجب أن يكون رباعياً على الأقل', 'error');
            return;
        }

        // 2. Mobile Validation (Egyptian Format)
        const phoneRegex = /^01[0125][0-9]{8}$/;
        if (!phoneRegex.test(formData.phone_number)) {
            showToast('رقم الهاتف غير صحيح، يجب أن يكون رقم مصري مكون من 11 رقم', 'error');
            return;
        }

        // 3. Password Validation (8 chars, 1 Upper, 1 Number)
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!passwordRegex.test(formData.password)) {
            showToast('كلمة المرور ضعيفة: يجب أن تكون 8 أحرف، وتحتوي على حرف كبير (Capital) ورقم واحد على الأقل', 'error');
            return;
        }

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'جاري المعالجة...';

            const response = await fetch('/ITI/IRP_System_PHP/public/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
                showToast('تم إنشاء الحساب بنجاح! جاري تحويلك...');
                setTimeout(() => { window.location.href = '/ITI/IRP_System_PHP/public/login'; }, 1500);
            } else {
                showToast(result.error || 'فشل التسجيل', 'error');
            }
        } catch (error) {
            showToast('حدث خطأ في الاتصال بالخادم', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'تسجيل الحساب <span class="material-symbols-outlined text-lg">arrow_forward</span>';
        }
    });
</script>
</body>
</html>
