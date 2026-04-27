<?php
$currentUser = AuthMiddleware::user();
$pageTitle = 'تقديم بحث جديد - IRB Portal';

$sidebarItems = [
    ['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => BASE_URL . '/student/dashboard', 'active' => false],
    ['label' => 'أبحاثي', 'icon' => 'science', 'href' => BASE_URL . '/student/submissions'],
    ['label' => 'تقديم بحث جديد', 'icon' => 'note_add', 'href' => BASE_URL . '/student/submission/create', 'active' => true],
    ['label' => 'الإعدادات', 'icon' => 'settings', 'href' => BASE_URL . '/student/settings'],
];

$errorMessage = '';
if (isset($_SESSION['submission_error'])) {
    $errorMessage = $_SESSION['submission_error'];
    unset($_SESSION['submission_error']);
}

$successMessage = '';
if (isset($_SESSION['submission_success'])) {
    $successMessage = $_SESSION['submission_success'];
    unset($_SESSION['submission_success']);
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <aside class="w-full lg:w-[260px] bg-white border-r border-gray-200 shadow-sm lg:shadow-none">
            <div class="p-5 border-b border-gray-200 flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-100 overflow-hidden flex items-center justify-center text-indigo-700">
                    <span class="material-symbols-outlined text-3xl">account_balance</span>
                </div>
                <div>
                    <h1 class="font-h1 text-lg text-gray-900">IRB</h1>
                    <p class="text-sm text-gray-600">بوابة الباحث</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <?php foreach ($sidebarItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-all <?= !empty($item['active']) ? 'bg-indigo-700 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' ?>">
                        <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="p-4 mt-auto border-t border-gray-200">
                <a href="<?php echo BASE_URL; ?>/logout"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-red-600 hover:bg-red-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                <div>
                    <p class="text-sm text-gray-600">تقديم بحث جديد</p>
                    <h2 class="font-h1 text-2xl text-gray-900">قدم بحثك</h2>
                </div>
            </header>

            <section class="px-4 md:px-8 py-6">
                <!-- Alert Messages -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600 text-2xl shrink-0">error</span>
                        <div>
                            <h3 class="font-button text-sm text-red-800">خطأ في التقديم</h3>
                            <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($successMessage)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
                        <span class="material-symbols-outlined text-green-600 text-2xl shrink-0">check_circle</span>
                        <div>
                            <h3 class="font-button text-sm text-green-800">تم التقديم بنجاح</h3>
                            <p class="text-sm text-green-700 mt-1"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Container -->
                <div class="mx-auto max-w-3xl rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
                    <form id="submissionForm" method="POST" action="<?php echo BASE_URL; ?>/student/submission/store" enctype="multipart/form-data" class="space-y-6">
                        <!-- Section 1: Research Information -->
                        <div class="px-5 md:px-8 py-6 border-b border-gray-200">
                            <h3 class="font-display-lg text-2xl font-bold text-gray-900 mb-5">معلومات البحث</h3>

                            <div class="space-y-4">
                                <!-- Research Title -->
                                <div>
                                    <label for="title" class="block text-sm font-button text-gray-900 mb-2">
                                        عنوان البحث <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                           placeholder="أدخل عنوان بحثك">
                                </div>

                                <!-- Principal Investigator -->
                                <div>
                                    <label for="principal_investigator" class="block text-sm font-button text-gray-900 mb-2">
                                        اسم الباحث الرئيسي <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" id="principal_investigator" name="principal_investigator" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                           placeholder="أدخل اسم الباحث الرئيسي">
                                </div>

                                <!-- Co-Investigators -->
                                <div>
                                    <label for="co_investigators" class="block text-sm font-button text-gray-900 mb-2">
                                        المشاركون في البحث <span class="text-gray-400">(اختياري)</span>
                                    </label>
                                    <textarea id="co_investigators" name="co_investigators" rows="3"
                                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                              placeholder="أدخل أسماء المشاركين في البحث مفصولة بفواصل"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Required Documents -->
                        <div class="px-5 md:px-8 py-6">
                            <h3 class="font-display-lg text-2xl font-bold text-gray-900 mb-5">المستندات المطلوبة</h3>

                            <div class="space-y-5">
                                <!-- Protocol -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        البروتوكول - وصف البحث الكامل <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_protocol" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- Protocol Review Application -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        استمارة طلب المراجعة <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_review_application" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- Conflict of Interest -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        إقرار تضارب المصالح <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_conflict_of_interest" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- IRB Review Checklist -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        قائمة المراجعة <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_irb_checklist" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- Principal Investigator Consent -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        إقرار الموافقة من الباحث الرئيسي <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_pi_consent" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- Patient Consent -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        إقرار موافقة المريض <span class="text-red-600">*</span>
                                    </label>
                                    <input type="file" name="document_patient_consent" accept=".pdf,.doc,.docx" required
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>

                                <!-- Photos & Biopsies Consent (Optional) -->
                                <div class="p-4 rounded-lg border-2 border-gray-300 bg-indigo-50">
                                    <label class="block text-sm font-button font-bold text-gray-900 mb-3">
                                        إقرار الموافقة على الصور والخزعات <span class="text-gray-400">(اختياري)</span>
                                    </label>
                                    <input type="file" name="document_photos_biopsies_consent" accept=".pdf,.doc,.docx"
                                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-button file:bg-indigo-700 file:text-white hover:file:bg-indigo-800">
                                    <p class="text-xs text-gray-600 mt-2">الملفات المقبولة: PDF, DOC, DOCX (الحد الأقصى: 10 ميجابايت)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-5 md:px-8 py-6 border-t border-gray-200 flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-button text-sm hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-[18px]">check</span>
                                تقديم البحث
                            </button>
                            <a href="<?php echo BASE_URL; ?>/student/dashboard"
                               class="inline-flex items-center gap-2 bg-gray-100 text-gray-900 px-6 py-2.5 rounded-lg font-button text-sm hover:bg-gray-200 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script>
        const form = document.getElementById('submissionForm');
        const requiredFields = {
            title: 'عنوان البحث',
            principal_investigator: 'اسم الباحث الرئيسي'
        };

        const requiredDocuments = [
            'document_protocol',
            'document_review_application',
            'document_conflict_of_interest',
            'document_irb_checklist',
            'document_pi_consent',
            'document_patient_consent'
        ];

        // Add file upload change listeners for border color indicator
        const allFileInputs = document.querySelectorAll('input[type="file"]');
        allFileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const container = this.closest('.bg-indigo-50');

                if (this.files && this.files.length > 0) {
                    container.classList.replace('border-gray-300', 'border-green-500');
                } else {
                    container.classList.replace('border-green-500', 'border-gray-300');
                }
            });
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            let errors = [];

            // Check text fields
            for (const [fieldName, fieldLabel] of Object.entries(requiredFields)) {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    errors.push(`${fieldLabel} مطلوب`);
                }
            }

            // Check required documents
            for (const docName of requiredDocuments) {
                const fileInput = document.querySelector(`input[name="${docName}"]`);
                if (!fileInput.files || fileInput.files.length === 0) {
                    errors.push(`يجب رفع ${fileInput.previousElementSibling.textContent.trim()}`);
                }
            }

            // Validate file sizes and types
            const allFileInputs = document.querySelectorAll('input[type="file"]');
            const maxSize = 10 * 1024 * 1024; // 10 MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            for (const input of allFileInputs) {
                if (input.files && input.files.length > 0) {
                    const file = input.files[0];
                    if (file.size > maxSize) {
                        errors.push(`ملف ${file.name} يتجاوز الحد الأقصى المسموح (10 ميجابايت)`);
                    }
                    if (!allowedTypes.includes(file.type)) {
                        errors.push(`نوع الملف ${file.name} غير مدعوم. يرجى استخدام PDF أو DOC أو DOCX`);
                    }
                }
            }

            if (errors.length > 0) {
                alert('حدثت أخطاء في النموذج:\n\n' + errors.join('\n'));
                return;
            }

            // Submit form if validation passes
            this.submit();
        });
    </script>
</body>
</html>
