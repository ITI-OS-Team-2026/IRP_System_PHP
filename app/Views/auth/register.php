<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إنشاء حساب جديد - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
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
    <!-- Branding Side -->
    <div class="hidden md:flex md:w-2/5 bg-royal-indigo text-on-primary flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <span class="material-symbols-outlined text-4xl">account_balance</span>
                <span class="font-bold text-2xl tracking-widest uppercase">IRB Portal</span>
            </div>
        </div>
        <div class="relative z-10">
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
    <div class="w-full md:w-3/5 flex items-center justify-center p-8 lg:p-12 bg-paper-white relative overflow-y-auto">
        <div class="w-full max-w-xl">
            <!-- Tabs -->
            <div class="flex w-full mb-8">
                <a href="<?php echo BASE_URL; ?>/login" class="flex-1 pb-3 text-center border-b border-outline text-outline hover:text-charcoal transition-colors">
                    تسجيل الدخول
                </a>
                <a href="<?php echo BASE_URL; ?>/register" class="flex-1 pb-3 text-center border-b-2 border-charcoal font-bold text-charcoal">
                    إنشاء حساب
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-charcoal mb-2">إنشاء حساب طالب</h1>
                <p class="text-secondary">يرجى إدخال كافة البيانات المطلوبة ورفع صور البطاقة.</p>
            </div>

            <form id="registerForm" class="space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-bold text-charcoal" for="fullName">الاسم بالكامل (رباعي)</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="fullName" name="full_name" placeholder="الاسم رباعياً كما في البطاقة" type="text" required/>
                    </div>
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="email">البريد الإلكتروني</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="email" name="email" placeholder="example@university.edu" type="email" required/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="phone">رقم الهاتف</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="phone" name="phone_number" placeholder="01XXXXXXXXX" type="tel" required/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="department">القسم</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="department" name="department" placeholder="القسم الأكاديمي" type="text" required/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="specialty">التخصص</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="specialty" name="specialty" placeholder="التخصص الدقيق" type="text" required/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal">صورة البطاقة (وجه)</label>
                        <input type="file" id="idFront" name="id_front" accept="image/*" class="w-full border border-dashed border-charcoal p-2 text-sm" required/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal">صورة البطاقة (ظهر)</label>
                        <input type="file" id="idBack" name="id_back" accept="image/*" class="w-full border border-dashed border-charcoal p-2 text-sm" required/>
                    </div>

                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-bold text-charcoal" for="password">كلمة المرور</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 rounded-none focus:ring-2 focus:ring-royal-indigo outline-none" id="password" name="password" placeholder="••••••••" type="password" required/>
                    </div>
                </div>

                <div class="pt-4">
                    <button id="submitBtn" class="w-full bg-royal-indigo text-white py-4 font-bold hover:bg-primary transition-colors flex justify-center items-center gap-2 group" type="submit">
                        <span>تسجيل الحساب</span>
                        <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

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
        const formData = new FormData(this);
        formData.append('role', 'student');

        // Validation logic
        const nameParts = formData.get('full_name').trim().split(/\s+/);
        if (nameParts.length < 4) {
            showToast('الاسم يجب أن يكون رباعياً على الأقل', 'error');
            return;
        }

        try {
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'جاري المعالجة...';

            const response = await fetch(window.BASE_URL + '/api/register', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                showToast('تم التسجيل بنجاح! جاري تحويلك...');
                setTimeout(() => { window.location.href = window.BASE_URL + '/login'; }, 1500);
            } else {
                showToast(result.error || 'فشل التسجيل', 'error');
            }
        } catch (error) {
            showToast('حدث خطأ في الاتصال بالخادم', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.querySelector('span').textContent = 'تسجيل الحساب';
        }
    });
</script>
</body>
</html>
