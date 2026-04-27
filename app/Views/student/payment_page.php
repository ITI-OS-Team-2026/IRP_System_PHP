<?php
/**
 * @deprecated This view is no longer used by any route.
 * Payment flow is handled by payment_initiate.php via /payment/initiate.
 * Kept for reference only.
 */
$pageTitle = 'بوابة الدفع الإلكتروني - IRB Portal';
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 mb-4">
                <span class="material-symbols-outlined text-4xl">payments</span>
            </div>
            <h1 class="font-h1 text-3xl text-gray-900 mb-2">بوابة الدفع الإلكتروني الموحدة</h1>
            <p class="text-gray-600">يرجى مراجعة تفاصيل الفاتورة وإكمال عملية السداد</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Payment Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-button text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-700">credit_card</span>
                            اختر وسيلة الدفع
                        </h3>
                    </div>
                    
                    <div class="p-8">
                        <form action="<?php echo BASE_URL; ?>/student/payment/process" method="POST" id="paymentForm">
                            <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
                            <input type="hidden" name="payment_type" value="<?= $paymentType ?>">
                            <input type="hidden" name="amount" value="<?= $amount ?>">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                <!-- Card Option -->
                                <label class="relative flex items-center p-4 border-2 border-indigo-700 rounded-xl cursor-pointer bg-indigo-50">
                                    <input type="radio" name="method" value="card" checked class="hidden">
                                    <span class="material-symbols-outlined text-indigo-700 text-3xl ml-3">contactless</span>
                                    <div class="flex-1">
                                        <span class="block font-bold text-gray-900">بطاقة بنكية</span>
                                        <span class="text-xs text-gray-600">Visa / MasterCard / Meeza</span>
                                    </div>
                                    <span class="material-symbols-outlined text-indigo-700">check_circle</span>
                                </label>

                                <!-- Wallet Option -->
                                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-700 transition-all" id="walletLabel">
                                    <input type="radio" name="method" value="wallet" class="hidden" id="walletRadio">
                                    <span class="material-symbols-outlined text-emerald-500 text-3xl ml-3">smartphone</span>
                                    <div class="flex-1">
                                        <span class="block font-bold text-gray-900">محفظة إلكترونية</span>
                                        <span class="text-xs text-gray-600">فودافون كاش / اتصالات كاش</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Card Details (Placeholder) -->
                            <div class="space-y-4" id="cardDetails">
                                <div>
                                    <label class="block text-sm font-button mb-1 text-gray-600">رقم البطاقة</label>
                                    <input type="text" placeholder="**** **** **** ****" class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-button mb-1 text-gray-600">تاريخ الانتهاء</label>
                                        <input type="text" placeholder="MM/YY" class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-button mb-1 text-gray-600">CVV</label>
                                        <input type="text" placeholder="***" class="w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="payBtn"
                                class="w-full mt-8 bg-indigo-700 text-white font-button py-4 rounded-xl hover:bg-indigo-800 transition-all shadow-lg flex items-center justify-center gap-2 text-lg disabled:opacity-60 disabled:cursor-not-allowed"
                                onclick="this.disabled=true; this.innerHTML='<span class=\'material-symbols-outlined animate-spin\'>progress_activity</span> جارٍ التحويل إلى بوابة الدفع...'; this.form.submit();">
                                <span class="material-symbols-outlined">shield_with_heart</span>
                                تأكيد دفع <?= number_format($amount, 2) ?> ج.م
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-center text-sm text-gray-600 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-green-500 text-sm">lock</span>
                    اتصال آمن ومُشفر 256-بت لضمان حماية بياناتك
                </p>
            </div>

            <!-- Right: Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-8">
                    <h3 class="font-h1 text-lg text-gray-900 mb-4 border-b pb-4">ملخص الطلب</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">نوع الطلب:</span>
                            <span class="font-bold text-gray-900"><?= $paymentType === 'initial' ? 'تقديم أولي' : 'رسوم العينة' ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">الرقم التسلسلي:</span>
                            <span class="font-bold text-indigo-700"><?= htmlspecialchars($submission['serial_number']) ?></span>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">الوصف:</p>
                            <p class="text-sm text-gray-600 italic leading-relaxed">
                                <?= htmlspecialchars($description) ?>
                            </p>
                        </div>
                        <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-lg font-h1 text-gray-900">الإجمالي</span>
                            <span class="text-2xl font-numeral font-bold text-indigo-700"><?= number_format($amount, 2) ?> <small class="text-xs">ج.م</small></span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-orange-50 rounded-lg border border-orange-100">
                        <div class="flex gap-2">
                            <span class="material-symbols-outlined text-orange-500">info</span>
                            <p class="text-xs text-orange-800 leading-relaxed">
                                بمجرد تأكيد الدفع، سيتم تحديث حالة بحثك تلقائياً وإصدار إيصال إلكتروني متاح للتحميل.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
