<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <?php
    $pageTitle = 'إنشاء حساب جديد - IRB Portal';
    require __DIR__ . '/../layouts/head.php';
    ?>
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
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-charcoal mb-2">إنشاء حساب طالب</h1>
                <p class="text-secondary">يرجى إدخال كافة البيانات المطلوبة ورفع صور البطاقة.</p>
            </div>

            <form id="registerForm" class="space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-bold text-charcoal" for="fullName">الاسم بالكامل (رباعي)</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="fullName" name="full_name" placeholder="الاسم رباعياً كما في البطاقة" type="text" required/>
                    </div>
                    
                    <!-- Email -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="email">البريد الإلكتروني</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="email" name="email" placeholder="example@university.edu" type="email" required/>
                    </div>

                    <!-- Phone -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="phone">رقم الهاتف</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="phone" name="phone_number" placeholder="01XXXXXXXXX" type="tel" required/>
                    </div>

                    <!-- Department -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="department">القسم</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="department" name="department" placeholder="القسم الأكاديمي" type="text" required/>
                    </div>

                    <!-- Specialty -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal" for="specialty">التخصص</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="specialty" name="specialty" placeholder="التخصص الدقيق" type="text" required/>
                    </div>

                    <!-- ID Front -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal">صورة البطاقة (وجه)</label>
                        <input type="file" id="idFront" name="id_front" accept="image/*" class="w-full border border-dashed border-charcoal p-2 text-sm" required/>
                    </div>

                    <!-- ID Back -->
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-charcoal">صورة البطاقة (ظهر)</label>
                        <input type="file" id="idBack" name="id_back" accept="image/*" class="w-full border border-dashed border-charcoal p-2 text-sm" required/>
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-bold text-charcoal" for="password">كلمة المرور</label>
                        <input class="w-full bg-paper-white border border-charcoal p-3 focus:outline-none focus:ring-2 focus:ring-royal-indigo rounded-none" id="password" name="password" placeholder="••••••••" type="password" required/>
                        <p class="text-xs text-secondary mt-1">يجب أن تحتوي على 8 أحرف، حرف كبير ورقم واحد على الأقل.</p>
                    </div>
                </div>

                <div class="pt-4 flex flex-col gap-4">
                    <button id="submitBtn" class="w-full bg-royal-indigo text-white py-4 px-6 hover:bg-primary transition-colors font-bold rounded-none flex justify-center items-center gap-2 group" type="submit">
                        <span>تسجيل الحساب</span>
                        <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
                    </button>
                    <div class="text-center mt-2">
                        <a class="text-royal-indigo hover:underline font-bold" href="/login">
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
        const submitBtn = this.querySelector('#submitBtn');
        const formData = new FormData(this);
        formData.append('role', 'student');


        const nameParts = formData.get('full_name').trim().split(/\s+/);
        if (nameParts.length < 4) {
            showToast('الاسم يجب أن يكون رباعياً على الأقل', 'error');
            return;
        }


        const phoneRegex = /^01[0125][0-9]{8}$/;
        if (!phoneRegex.test(formData.get('phone_number'))) {
            showToast('رقم الهاتف غير صحيح، يجب أن يكون رقم مصري مكون من 11 رقم', 'error');
            return;
        }

        const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!passwordRegex.test(formData.get('password'))) {
            showToast('كلمة المرور ضعيفة: يجب أن تكون 8 أحرف، وتحتوي على حرف كبير ورقم واحد على الأقل', 'error');
            return;
        }

        const idFront = formData.get('id_front');
        const idBack = formData.get('id_back');

        if (!idFront.name || !idBack.name) {
            showToast('يرجى رفع صور البطاقة (وجه وظهرا)', 'error');
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        for (let file of [idFront, idBack]) {
            if (!allowedTypes.includes(file.type)) {
                showToast('يرجى رفع صور فقط (JPG أو PNG)', 'error');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                showToast('حجم الصورة كبير جداً (الأقصى 5 ميجابايت)', 'error');
                return;
            }
        }

        if (!formData.get('department') || !formData.get('specialty')) {
            showToast('يرجى ملء بيانات القسم والتخصص', 'error');
            return;
        }

        try {
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'جاري المعالجة...';

            const response = await fetch('/api/register', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                showToast('تم إنشاء الحساب بنجاح! جاري تحويلك...');
                setTimeout(() => { window.location.href = '/login'; }, 1500);
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
