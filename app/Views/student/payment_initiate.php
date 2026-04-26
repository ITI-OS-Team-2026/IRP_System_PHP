<?php
$pageTitle = 'الدفع - IRB Portal';
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
                    <h2 class="font-h1 text-2xl text-charcoal"><?= $paymentType === 'initial' ? 'دفع رسوم المراجعة الأولية' : 'دفع رسوم حجم العينة' ?></h2>
                </div>
            </header>

            <section class="px-4 md:px-8 py-6 flex justify-center">
                <div class="w-full max-w-xl">
                    <!-- Submission Info -->
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] mb-6">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h3 class="font-h1 text-lg text-charcoal">بيانات البحث</h3>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-slate-gray mb-1">عنوان البحث</p>
                                    <p class="font-button text-charcoal"><?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] mb-6">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h3 class="font-h1 text-lg text-charcoal">تفاصيل الرسوم</h3>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-slate-gray"><?= $paymentType === 'initial' ? 'رسوم المراجعة الأولية' : 'رسوم حجم العينة' ?></span>
                                <span class="text-2xl font-bold text-primary">
                                    <?= htmlspecialchars(PaymentHelpers::formatAmount($paymentAmount), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
                        <div class="px-5 py-4 border-b border-slate-200">
                            <h3 class="font-h1 text-lg text-charcoal">طريقة الدفع</h3>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/payment/store" class="p-5">
                            <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="payment_type" value="<?= htmlspecialchars($paymentType, ENT_QUOTES, 'UTF-8') ?>">

                            <!-- Card Option -->
                            <div class="mb-3">
                                <label class="flex items-center p-4 border-2 border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition" id="card-option-label">
                                    <input type="radio" name="payment_method" value="card" checked class="w-5 h-5" id="method-card" onchange="togglePhoneInput()">
                                    <div class="mr-4 flex-1">
                                        <p class="font-button text-charcoal">بطاقة ائتمان / خصم</p>
                                        <p class="text-sm text-slate-gray mt-1">ادفع باستخدام Visa أو Mastercard</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Wallet Option -->
                            <div class="mb-3">
                                <label class="flex items-center p-4 border-2 border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition" id="wallet-option-label">
                                    <input type="radio" name="payment_method" value="wallet" class="w-5 h-5" id="method-wallet" onchange="togglePhoneInput()">
                                    <div class="mr-4 flex-1">
                                        <p class="font-button text-charcoal">محفظة الهاتف المحمول</p>
                                        <p class="text-sm text-slate-gray mt-1">ادفع عبر Vodafone Cash, Orange Cash, e& money, We Pay</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Wallet Phone Input -->
                            <div id="wallet-phone-container" class="mb-4 hidden">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <label class="block text-sm font-medium text-yellow-800 mb-2">رقم الهاتف للمحفظة</label>
                                    <input type="tel" name="wallet_phone" id="wallet_phone" 
                                           placeholder="مثال: 01012345678"
                                           class="w-full px-3 py-2 border border-yellow-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                           pattern="[0-9]{11}" maxlength="11">
                                    <p class="text-xs text-yellow-600 mt-1">أدخل رقم الهاتف المسجل على محفظتك الإلكترونية (11 رقم)</p>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-green-800">✅ آمن تماماً: ستتم إعادة توجيهك إلى موقع Paymob الآمن</p>
                            </div>

                            <script>
                                function togglePhoneInput() {
                                    const walletRadio = document.getElementById('method-wallet');
                                    const phoneContainer = document.getElementById('wallet-phone-container');
                                    const cardLabel = document.getElementById('card-option-label');
                                    const walletLabel = document.getElementById('wallet-option-label');
                                    const phoneInput = document.getElementById('wallet_phone');

                                    if (walletRadio.checked) {
                                        phoneContainer.classList.remove('hidden');
                                        phoneInput.setAttribute('required', 'required');
                                        walletLabel.style.borderColor = '#3f4779';
                                        cardLabel.style.borderColor = '';
                                    } else {
                                        phoneContainer.classList.add('hidden');
                                        phoneInput.removeAttribute('required');
                                        cardLabel.style.borderColor = '#3f4779';
                                        walletLabel.style.borderColor = '';
                                    }
                                }
                                // Initialize on load
                                togglePhoneInput();
                            </script>

                            <div class="flex gap-4">
                                <button type="submit" class="flex-1 bg-primary text-on-primary px-6 py-3 rounded-lg font-button hover:bg-royal-indigo">
                                    المتابعة للدفع
                                </button>
                                <a href="<?= BASE_URL ?>/student/submissions" class="flex-1 bg-slate-100 text-charcoal px-6 py-3 rounded-lg font-button text-center hover:bg-slate-200">
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
