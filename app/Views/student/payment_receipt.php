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
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Sidebar -->
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
                <a href="<?= BASE_URL ?>/student/dashboard" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-slate-gray hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>لوحة التحكم</span>
                </a>
                <a href="<?= BASE_URL ?>/student/submissions" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button bg-primary text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">science</span>
                    <span>أبحاثي</span>
                </a>
                <a href="<?= BASE_URL ?>/student/settings" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-slate-gray hover:bg-slate-100">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="p-4 mt-auto border-t border-slate-200">
                <a href="<?= BASE_URL ?>/logout" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button text-red-600 hover:bg-red-50">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </aside>

        <main class="flex-1">
            <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 shadow-sm">
                <div>
                    <p class="text-sm text-slate-gray">الدفع</p>
                    <h2 class="font-h1 text-2xl text-charcoal">إيصال الدفع</h2>
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
                    <div class="rounded-xl border-2 bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] mb-6" style="border-color: <?= $isSuccess ? '#10b981' : ($isPending ? '#f59e0b' : '#ef4444'); ?>">
                        <div class="p-8 text-center">
                            <?php if ($isSuccess): ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-green-100 text-green-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">check_circle</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-charcoal mb-2">تم الدفع بنجاح!</h3>
                                <p class="text-slate-gray">تم استلام دفعتك وسيتم معالجتها</p>
                            <?php elseif ($isPending): ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 text-amber-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">hourglass_top</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-charcoal mb-2">الدفع قيد التحقق</h3>
                                <p class="text-slate-gray">ننتظر تأكيد Paymob النهائي. يمكنك تحديث الصفحة بعد لحظات.</p>
                            <?php else: ?>
                                <div class="mb-4">
                                    <span class="inline-flex items-center justify-center w-16 h-16 bg-red-100 text-red-600 rounded-full">
                                        <span class="material-symbols-outlined text-4xl">cancel</span>
                                    </span>
                                </div>
                                <h3 class="font-h1 text-2xl text-charcoal mb-2">فشل الدفع</h3>
                                <p class="text-slate-gray">لم يتم استلام دفعتك بنجاح</p>
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
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] mb-6">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h3 class="font-h1 text-lg text-charcoal">تفاصيل الإيصال</h3>
                        </div>
                        <div class="p-5">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-gray">رقم الدفعة</span>
                                    <span class="font-button text-charcoal">#<?= htmlspecialchars($payment['id'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-gray">الحالة</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-button <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-gray">المبلغ المدفوع</span>
                                    <span class="font-button text-charcoal text-lg text-primary">
                                        <?= PaymentHelpers::formatAmount($payment['amount']) ?>
                                    </span>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-gray">طريقة الدفع</span>
                                    <?php
                                        $methodLabels = ['card' => 'بطاقة بنكية', 'wallet' => 'محفظة إلكترونية'];
                                        $methodDisplay = $methodLabels[$paymentMethod] ?? 'بطاقة بنكية';
                                    ?>
                                    <span class="font-button text-charcoal"><?= htmlspecialchars($methodDisplay, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <?php if ($payment['paymob_transaction_id']): ?>
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-slate-gray">معرّف العملية</span>
                                    <span class="font-button text-charcoal text-sm"><?= htmlspecialchars($payment['paymob_transaction_id'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="flex justify-between items-center">
                                    <span class="text-slate-gray">تاريخ العملية</span>
                                    <span class="font-button text-charcoal"><?= htmlspecialchars(PaymentHelpers::formatDate($payment['transaction_date']), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Research Info -->
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] mb-6">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h3 class="font-h1 text-lg text-charcoal">بيانات البحث</h3>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-slate-gray mb-1">عنوان البحث</p>
                                    <p class="font-button text-charcoal"><?= htmlspecialchars($payment['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-gray mb-1">حالة البحث</p>
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
                                    <p class="font-button text-charcoal"><?= htmlspecialchars($statusMapLocal[$payment['status']] ?? $payment['status'], ENT_QUOTES, 'UTF-8') ?></p>
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
                                <a href="<?= BASE_URL ?>/student/submissions" class="flex-1 bg-primary text-on-primary px-6 py-3 rounded-lg font-button text-center hover:bg-royal-indigo">
                                    العودة إلى أبحاثي
                                </a>
                            <?php elseif ($isPending): ?>
                                <a href="<?= BASE_URL ?>/payment/receipt?payment_id=<?= (int) $payment['id'] ?>" class="flex-1 bg-amber-500 text-white px-6 py-3 rounded-lg font-button text-center hover:bg-amber-600">
                                    تحديث الحالة
                                </a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/payment/initiate?submission_id=<?= (int) $payment['submission_id'] ?>" class="flex-1 bg-primary text-on-primary px-6 py-3 rounded-lg font-button text-center hover:bg-royal-indigo">
                                    محاولة الدفع مرة أخرى
                                </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/student/dashboard" class="flex-1 bg-slate-100 text-charcoal px-6 py-3 rounded-lg font-button text-center hover:bg-slate-200">
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
