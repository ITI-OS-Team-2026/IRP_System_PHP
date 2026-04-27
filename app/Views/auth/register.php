<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إنشاء حساب جديد - IRB Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&amp;family=Tajawal:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
<body class="bg-gray-50 text-gray-900 antialiased h-screen w-full overflow-hidden flex selection:bg-indigo-100 selection:text-indigo-700">

<!-- Right Panel: Form (RTL Start) -->
<main class="w-full lg:w-1/2 h-full bg-white flex flex-col justify-center px-8 md:px-16 lg:px-24 xl:px-32 relative z-10 overflow-y-auto">
    <!-- Header / Logo -->
    <header class="mb-12">
        <h1 class="font-h1 text-4xl text-gray-900 mb-1">مجلس المراجعة المؤسسية</h1>
        <p class="font-body-sm text-sm text-gray-600 uppercase tracking-widest">Institutional Review Board</p>
    </header>

    <!-- Form Container -->
    <div class="w-full max-w-md">
            <!-- Tabs -->
            <div class="flex w-full mb-8">
                <a href="<?php echo BASE_URL; ?>/login" class="flex-1 pb-3 text-center border-b border-gray-300 font-button text-button text-gray-600 hover:text-gray-900 focus:outline-none transition-colors">
                    تسجيل الدخول
                </a>
                <a href="<?php echo BASE_URL; ?>/register" class="flex-1 pb-3 text-center border-b-2 border-indigo-700 font-button text-button text-gray-900 focus:outline-none">
                    إنشاء حساب
                </a>
            </div>

            <div class="mb-6">
                <h2 class="font-h1 text-3xl text-gray-900 mb-2">إنشاء حساب طالب</h2>
                <p class="font-body-sm text-gray-600">يرجى إدخال كافة البيانات المطلوبة ورفع صور البطاقة.</p>
            </div>

            <form id="registerForm" class="space-y-4" enctype="multipart/form-data">
                <!-- Full Name -->
                <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold" for="fullName">الاسم بالكامل (رباعي)</label>
                        <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="fullName" name="full_name" placeholder="الاسم رباعياً كما في البطاقة" type="text" required/>
                    </div>

                <!-- Two Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold" for="email">البريد الإلكتروني</label>
                        <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="email" name="email" placeholder="example@university.edu" type="email" required/>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold" for="phone">رقم الهاتف</label>
                        <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="phone" name="phone_number" placeholder="01XXXXXXXXX" type="tel" required/>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold" for="department">القسم</label>
                        <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="department" name="department" placeholder="القسم الأكاديمي" type="text" required/>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold" for="specialty">التخصص</label>
                        <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="specialty" name="specialty" placeholder="التخصص الدقيق" type="text" required/>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold">صورة البطاقة (وجه)</label>
                        <input type="file" id="idFront" name="id_front" accept="image/*" class="w-full border border-dashed border-gray-300 px-3 py-2.5 text-sm rounded-lg bg-gray-50" required/>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-body-sm text-sm text-gray-900 font-bold">صورة البطاقة (ظهر)</label>
                        <input type="file" id="idBack" name="id_back" accept="image/*" class="w-full border border-dashed border-gray-300 px-3 py-2.5 text-sm rounded-lg bg-gray-50" required/>
                    </div>
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-2">
                    <label class="font-body-sm text-sm text-gray-900 font-bold" for="password">كلمة المرور</label>
                    <input class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg font-body-lg text-base text-gray-900 focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all" id="password" name="password" placeholder="••••••••" type="password" minlength="8" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}" title="8 أحرف على الأقل مع حرف كبير وحرف صغير ورقم ورمز خاص" required/>
                    <p class="font-body-sm text-xs text-gray-600">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل وحرف كبير وحرف صغير ورقم ورمز خاص.</p>
                </div>

                <div class="pt-2">
                    <button id="submitBtn" type="submit" class="w-full mt-4 bg-indigo-700 text-white py-4 font-button text-button rounded-lg border border-indigo-700 hover:bg-indigo-800 transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-0.5 flex justify-center items-center gap-2 group">
                        <span>تسجيل الحساب</span>
                        <span class="material-symbols-outlined text-white transform group-hover:-translate-x-1 transition-transform rtl:-scale-x-100">arrow_forward</span>
                    </button>
                </div>
            </form>

            <!-- Footer Links -->
            <div class="mt-12 flex flex-col gap-2 border-t border-gray-200 pt-6">
                <p class="font-body-sm text-sm text-gray-600">
                    بالتسجيل في هذا النظام، فإنك توافق على <a class="text-indigo-700 underline hover:text-indigo-800" href="#">بروتوكول الخصوصية</a> و <a class="text-indigo-700 underline hover:text-indigo-800" href="#">المعايير الأخلاقية</a> الخاصة بمجلس المراجعة المؤسسية.
                </p>
            </div>
        </div>
    </div>
</main>

<!-- Left Panel: Quote/Branding -->
<aside class="hidden lg:flex w-1/2 h-full relative flex-col justify-between p-16 overflow-hidden bg-indigo-900" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAuDqi_Ls0kDkZx8Bj2tZ0p_qJp8K13zi08ZVrjjXSozCbQCQZJN_AfZRP9pkEnihZFnLkUaBxduiJOup97VBq0cKVK6mI8kcnj9Aj_Oqg_WO42Kd3EJZzf9kgZQlgez5v2XgOWWnMcU3y2YUI1-tyJaTB-fc3thUkZ_Rpw0GNdKggpWcpsyOaQPOeiSfObhjgUWxY39nJG_D1efgBQwfMNzpR0uuRMdFoq7vFRdx5CHgGKEv_CZ-tRbDm3NVYQN8dRiHMDZGpj2ywJ'); background-size: cover; background-position: center;">
    <!-- Deep Indigo Overlay -->
    <div class="absolute inset-0 bg-indigo-900/90 mix-blend-multiply z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-indigo-900 via-transparent to-indigo-900/60 z-0"></div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col h-full justify-between" dir="rtl">
        <!-- Branding -->
        <div class="flex items-center gap-3 text-indigo-200">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">account_balance</span>
            <span class="font-body-lg text-lg uppercase tracking-widest font-bold">بوابة البحث العلمي</span>
        </div>

        <!-- Quote -->
        <blockquote class="max-w-2xl self-start text-right">
            <p class="font-display-lg text-4xl text-white leading-relaxed mb-8 font-bold" style="line-height: 1.4;">
                "النزاهة الأكاديمية الصارمة هي أساس كل تقدم علمي موثوق."
            </p>
            <footer class="flex items-center gap-4 justify-start">
                <div class="h-[2px] w-16 bg-indigo-200"></div>
                <span class="font-body-lg text-lg text-indigo-200 tracking-wide font-bold">المعيار الذهبي لأخلاقيات البحث</span>
            </footer>
        </blockquote>

        <!-- Bottom Note -->
        <div class="text-right">
            <p class="font-numeral text-sm text-indigo-200 opacity-70">
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

        const password = String(formData.get('password') || '');
        const strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        if (!strongPasswordPattern.test(password)) {
            showToast('كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل وحرف كبير وحرف صغير ورقم ورمز خاص', 'error');
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
