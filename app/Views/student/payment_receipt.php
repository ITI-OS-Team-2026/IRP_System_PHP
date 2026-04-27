<?php
$pageTitle = 'إيصال الدفع - IRB Portal';

$paymentStatus = $payment['payment_status'] ?? 'pending';
$isSuccess = $paymentStatus === 'completed';
$isFailed = $paymentStatus === 'failed';
$isPending = $paymentStatus === 'pending';
$statusLabel = PaymentHelpers::getStatusLabel($paymentStatus);
$badgeClass = PaymentHelpers::getStatusBadgeClass($paymentStatus);
$paymentMethod = $payment['payment_method'] ?? 'wallet';
$failureReason = trim((string) ($payment['failure_reason'] ?? ''));
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
                <a href="<?= BASE_URL ?>/student/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>لوحة التحكم</span>
                </a>
                <a href="<?= BASE_URL ?>/student/submissions" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button bg-indigo-700 text-white shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">science</span>
                    <span>أبحاثي</span>
                </a>
                <a href="<?= BASE_URL ?>/student/settings" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="p-4 mt-auto border-t border-gray-200">
                <a href="<?= BASE_URL ?>/logout" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-red-600 hover:bg-red-50">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 shadow-sm">
                <div>
                    <p class="text-sm text-gray-600">الدفع</p>
                    <h2 class="font-h1 text-2xl text-gray-900">إيصال الدفع</h2>
                </div>
            </header>

            <section class="px-4 md:px-8 py-6 flex justify-center">
                <div class="w-full max-w-xl">
                    <?php if (!empty($_SESSION['submission_success'])): ?>
                        <div class="mb-4 rounded-lg bg-green-50 border border-green-300 text-green-800 px-5 py-3 flex items-center gap-3 text-sm font-button">
                            <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
                            <?= htmlspecialchars($_SESSION['submission_success'], ENT_QUOTES, 'UTF-8') ?>
                            <?php unset($_SESSION['submission_success']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['submission_error'])): ?>
                        <div class="mb-4 rounded-lg bg-red-50 border border-red-300 text-red-800 px-5 py-3 flex items-center gap-3 text-sm font-button">
                            <span class="material-symbols-outlined text-red-600" style="font-variation-settings:'FILL' 1;">error</span>
                            <?= htmlspecialchars($_SESSION['submission_error'], ENT_QUOTES, 'UTF-8') ?>
                            <?php unset($_SESSION['submission_error']); ?>
                        </div>
                    <?php endif; ?>
                    <!-- Status Card -->
                    <div class="rounded-xl border-2 bg-white shadow-sm mb-6" style="border-color: <?= $isSuccess ? '#10b981' : ($isPending ? '#f59e0b' : '#ef4444'); ?>">
                        <div class="p-8 text-center">
                            <?php if ($isSuccess): ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-green-100 text-green-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">check_circle</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-gray-900 mb-2">تم الدفع بنجاح!</h3>
                                <p class="text-gray-600">تم استلام دفعتك وسيتم معالجتها</p>
                            <?php elseif ($isPending): ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 text-amber-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">hourglass_top</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-gray-900 mb-2">الدفع قيد التحقق</h3>
                                <p class="text-gray-600">ننتظر تأكيد Paymob النهائي. يمكنك تحديث الصفحة بعد لحظات.</p>
                            <?php else: ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-red-100 text-red-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">cancel</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-gray-900 mb-2">فشل الدفع</h3>
                                <p class="text-gray-600">لم يتم استلام دفعتك بنجاح</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isFailed && $failureReason !== ''): ?>
                        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-5 py-4 mb-6">
                            <p class="font-button text-sm mb-1">سبب الفشل</p>
                            <p class="text-sm leading-7"><?= htmlspecialchars($failureReason, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Receipt Details -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-h1 text-lg text-gray-900">تفاصيل الإيصال</h3>
                        </div>
                        <div class="p-5">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">رقم الدفعة</span>
                                    <span class="font-button text-gray-900">#<?= htmlspecialchars($payment['id'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">نوع الدفع</span>
                                    <span class="font-button text-gray-900"><?= ($payment['payment_type'] ?? '') === 'sample_size' ? 'رسوم حجم العينة' : 'رسوم التقديم' ?></span>
                                </div>

                                <?php if (!empty($payment['serial_number'])): ?>
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">الرقم التسلسلي</span>
                                    <span class="font-button text-gray-900"><?= htmlspecialchars($payment['serial_number'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">الحالة</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-button <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">المبلغ المدفوع</span>
                                    <span class="font-button text-gray-900 text-lg text-indigo-700">
                                        <?= PaymentHelpers::formatAmount($payment['amount']) ?>
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">طريقة الدفع</span>
                                    <?php
                                        $methodLabels = ['card' => 'بطاقة بنكية', 'wallet' => 'محفظة إلكترونية'];
                                        $methodDisplay = $methodLabels[$paymentMethod] ?? 'بطاقة بنكية';
                                    ?>
                                    <span class="font-button text-gray-900"><?= htmlspecialchars($methodDisplay, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <?php if ($payment['paymob_transaction_id']): ?>
                                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                                    <span class="text-gray-600">معرّف العملية</span>
                                    <span class="font-button text-gray-900 text-sm"><?= htmlspecialchars($payment['paymob_transaction_id'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">تاريخ العملية</span>
                                    <span class="font-button text-gray-900"><?= htmlspecialchars(PaymentHelpers::formatDate($payment['transaction_date']), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Research Info -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-h1 text-lg text-gray-900">بيانات البحث</h3>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">عنوان البحث</p>
                                    <p class="font-button text-gray-900"><?= htmlspecialchars($payment['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">حالة البحث</p>
                                    <?php
                                    $statusMapLocal = [
                                        'submitted' => 'تم التقديم',
                                        'admin_reviewed' => 'تمت مراجعة الإدارة',
                                        'initial_paid' => 'تم سداد الرسوم الأولية',
                                        'sample_sized' => 'تم حساب حجم العينة',
                                        'fully_paid' => 'تم السداد بالكامل',
                                        'under_review' => 'قيد المراجعة',
                                        'revision_requested' => 'مطلوب تعديل',
                                        'approved' => 'تمت الموافقة',
                                        'rejected' => 'مرفوض',
                                    ];
                                    ?>
                                    <p class="font-button text-gray-900"><?= htmlspecialchars($statusMapLocal[$payment['status']] ?? $payment['status'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <?php if ($isSuccess): ?>
                            <a href="<?= BASE_URL ?>/receipt/download?payment_id=<?= (int) $payment['id'] ?>" class="w-full bg-emerald-600 text-white px-6 py-3 rounded-lg font-button text-center hover:bg-emerald-700 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">download</span>
                                تحميل الإيصال
                            </a>
                        <?php endif; ?>
                        <div class="flex gap-4">
                            <?php if ($isSuccess): ?>
                                <a href="<?= BASE_URL ?>/student/submissions" class="flex-1 bg-indigo-700 text-white px-6 py-3 rounded-lg font-button text-center hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                                    العودة إلى أبحاثي
                                </a>
                            <?php elseif ($isPending): ?>
                                <a href="<?= BASE_URL ?>/payment/receipt?payment_id=<?= (int) $payment['id'] ?>" class="flex-1 bg-amber-500 text-white px-6 py-3 rounded-lg font-button text-center hover:bg-amber-600">
                                    تحديث الحالة
                                </a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/payment/initiate?submission_id=<?= (int) $payment['submission_id'] ?>" class="flex-1 bg-indigo-700 text-white px-6 py-3 rounded-lg font-button text-center hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                                    محاولة الدفع مرة أخرى
                                </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/student/dashboard" class="flex-1 bg-gray-100 text-gray-900 px-6 py-3 rounded-lg font-button text-center hover:bg-gray-200">
                                لوحة التحكم
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
