<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'الإعدادات - IRB Portal';

require_once __DIR__ . '/../../../config/database.php';
$db = Database::getConnection();

$studentId = (int) $_SESSION['user_id'];

$stmt = $db->prepare(
    "SELECT full_name, email, phone_number, department, specialty
     FROM users
     WHERE id = ? AND role = 'student'
     LIMIT 1"
);
$stmt->bind_param('i', $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header('Location: /login');
    exit;
}

$profileSuccessMessage = $_SESSION['settings_profile_success'] ?? '';
$profileErrorMessage = $_SESSION['settings_profile_error'] ?? '';
$passwordSuccessMessage = $_SESSION['settings_password_success'] ?? '';
$passwordErrorMessage = $_SESSION['settings_password_error'] ?? '';

unset($_SESSION['settings_profile_success'], $_SESSION['settings_profile_error'], $_SESSION['settings_password_success'], $_SESSION['settings_password_error']);

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => '/student/dashboard', 'active' => false],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => '/student/submissions'],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => '/student/submission/create'],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => '/student/settings', 'active' => true],
];
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <aside class="w-full lg:w-[260px] bg-white border-r border-slate-200 shadow-sm lg:shadow-none">
            <div class="p-5 border-b border-slate-200 flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500">
                    <span class="material-symbols-outlined text-3xl">account_balance</span>
                </div>
                <div>
                    <h1 class="font-h1 text-lg text-charcoal">IRB</h1>
                    <p class="text-sm text-slate-gray">بوابة الباحث</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <?php foreach ($sidebarItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-colors <?= !empty($item['active']) ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-gray hover:bg-slate-100 hover:text-charcoal' ?>">
                        <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-4 mt-auto border-t border-slate-200">
                <a href="/logout"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-red-600 hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-sm text-slate-gray">الإعدادات</p>
                    <h2 class="font-h1 text-2xl text-charcoal">إعدادات الحساب</h2>
                </div>
            </header>

            <section class="px-4 md:px-8 py-6 space-y-6">
                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">البيانات الشخصية</h3>
                    </div>

                    <?php if ($profileErrorMessage !== ''): ?>
                        <div class="mx-5 mt-5 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-600 text-2xl shrink-0">error</span>
                            <div>
                                <h3 class="font-button text-sm text-red-800">خطأ في تحديث البيانات</h3>
                                <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($profileErrorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profileSuccessMessage !== ''): ?>
                        <div class="mx-5 mt-5 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
                            <span class="material-symbols-outlined text-green-600 text-2xl shrink-0">check_circle</span>
                            <div>
                                <h3 class="font-button text-sm text-green-800">تم التحديث بنجاح</h3>
                                <p class="text-sm text-green-700 mt-1"><?= htmlspecialchars($profileSuccessMessage, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/student/settings/update" class="p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="block text-sm font-button text-charcoal mb-2">الاسم الكامل <span class="text-red-600">*</span></label>
                                <input type="text" id="full_name" name="full_name" required
                                       value="<?= htmlspecialchars($student['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-button text-charcoal mb-2">البريد الإلكتروني</label>
                                <input type="email" id="email" readonly
                                       value="<?= htmlspecialchars($student['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-gray focus:outline-none">
                            </div>

                            <div>
                                <label for="phone_number" class="block text-sm font-button text-charcoal mb-2">رقم الهاتف <span class="text-red-600">*</span></label>
                                <input type="text" id="phone_number" name="phone_number" required
                                       value="<?= htmlspecialchars($student['phone_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="department" class="block text-sm font-button text-charcoal mb-2">القسم <span class="text-red-600">*</span></label>
                                <input type="text" id="department" name="department" required
                                       value="<?= htmlspecialchars($student['department'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label for="specialty" class="block text-sm font-button text-charcoal mb-2">التخصص <span class="text-red-600">*</span></label>
                                <input type="text" id="specialty" name="specialty" required
                                       value="<?= htmlspecialchars($student['specialty'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>

                <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-h1 text-lg text-charcoal">تغيير كلمة المرور</h3>
                    </div>

                    <?php if ($passwordErrorMessage !== ''): ?>
                        <div class="mx-5 mt-5 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-600 text-2xl shrink-0">error</span>
                            <div>
                                <h3 class="font-button text-sm text-red-800">خطأ في تغيير كلمة المرور</h3>
                                <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($passwordErrorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($passwordSuccessMessage !== ''): ?>
                        <div class="mx-5 mt-5 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
                            <span class="material-symbols-outlined text-green-600 text-2xl shrink-0">check_circle</span>
                            <div>
                                <h3 class="font-button text-sm text-green-800">تم التغيير بنجاح</h3>
                                <p class="text-sm text-green-700 mt-1"><?= htmlspecialchars($passwordSuccessMessage, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/student/settings/password" class="p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="current_password" class="block text-sm font-button text-charcoal mb-2">كلمة المرور الحالية <span class="text-red-600">*</span></label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-button text-charcoal mb-2">كلمة المرور الجديدة <span class="text-red-600">*</span></label>
                                <input type="password" id="new_password" name="new_password" minlength="8" required
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-button text-charcoal mb-2">تأكيد كلمة المرور الجديدة <span class="text-red-600">*</span></label>
                                <input type="password" id="confirm_password" name="confirm_password" minlength="8" required
                                       class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-button text-sm hover:bg-royal-indigo transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                تغيير كلمة المرور
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
