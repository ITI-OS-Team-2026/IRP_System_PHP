<?php
$pageTitle = 'IRB - نتيجة الدفع';
$sourceType = htmlspecialchars($_GET['source_data_type'] ?? '', ENT_QUOTES, 'UTF-8');
$sourceSubType = htmlspecialchars($_GET['source_data_sub_type'] ?? '', ENT_QUOTES, 'UTF-8');
$sourcePan = htmlspecialchars($_GET['source_data_pan'] ?? '', ENT_QUOTES, 'UTF-8');
$createdAt = htmlspecialchars($_GET['created_at'] ?? '', ENT_QUOTES, 'UTF-8');
$txnResponseCode = htmlspecialchars($_GET['txn_response_code'] ?? '', ENT_QUOTES, 'UTF-8');

$paymentMethodLabel = 'غير محدد';
if ($sourceType === 'card') {
    $paymentMethodLabel = 'بطاقة ' . ($sourceSubType ?: '');
} elseif ($sourceType === 'wallet' || strtoupper($sourceSubType) === 'WALLET') {
    $paymentMethodLabel = 'محفظة إلكترونية';
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
        <?php if ($success): ?>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-green-700 mb-1">تم الدفع بنجاح!</h1>
                <p class="text-slate-500 text-sm">تم تأكيد دفع الرسوم الأولية بنجاح</p>
            </div>

            <div class="border border-slate-200 rounded-lg divide-y divide-slate-100 mb-6">
                <?php if ($orderId > 0): ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">رقم الطلب</span>
                    <span class="font-semibold text-slate-800"><?= (int) $orderId ?></span>
                </div>
                <?php endif; ?>
                <?php if ($transactionId > 0): ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">رقم العملية</span>
                    <span class="font-semibold text-slate-800"><?= (int) $transactionId ?></span>
                </div>
                <?php endif; ?>
                <?php if ($amountCents > 0): ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">المبلغ</span>
                    <span class="font-bold text-green-700 text-lg"><?= number_format($amountCents / 100, 2) ?> <?= $currency ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">طريقة الدفع</span>
                    <span class="font-semibold text-slate-800"><?= $paymentMethodLabel ?></span>
                </div>
                <?php if ($sourcePan !== ''): ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">الرقم</span>
                    <span class="font-mono text-slate-800"><?= $sourcePan ?></span>
                </div>
                <?php endif; ?>
                <?php if ($createdAt !== ''): ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">تاريخ العملية</span>
                    <span class="text-slate-800"><?= $createdAt ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-slate-500 text-sm">الحالة</span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مكتملة</span>
                </div>
            </div>

            <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/student/submissions" class="block w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center font-semibold">
                العودة إلى طلباتي
            </a>

        <?php elseif ($pending): ?>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-yellow-700 mb-1">الدفع قيد المعالجة</h1>
                <p class="text-slate-500 text-sm">تم استلام طلب الدفع وهو قيد المعالجة. سيتم تحديث الحالة قريبا.</p>
            </div>
            <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/student/submissions" class="block w-full bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition text-center font-semibold">
                العودة إلى طلباتي
            </a>

        <?php else: ?>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-red-700 mb-1">فشل الدفع</h1>
                <p class="text-slate-500 text-sm">لم يتم إتمام عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم.</p>
            </div>

            <?php if ($txnResponseCode !== ''): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-4 text-sm text-red-700">
                رمز الاستجابة: <?= $txnResponseCode ?>
            </div>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/student/submissions" class="block w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition text-center font-semibold">
                العودة إلى طلباتي
            </a>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/student/dashboard" class="text-sm text-slate-500 hover:text-slate-700 underline">لوحة التحكم</a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/login" class="text-sm text-slate-500 hover:text-slate-700 underline">تسجيل الدخول</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
