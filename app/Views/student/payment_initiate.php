<?php
$pageTitle = 'الدفع - IRB Portal';
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
                    <h2 class="font-h1 text-2xl text-gray-900"><?= $paymentType === 'initial' ? 'دفع رسوم المراجعة الأولية' : 'دفع رسوم حجم العينة' ?></h2>
                </div>
            </header>

            <section class="px-4 md:px-8 py-6 flex justify-center">
                <div class="w-full max-w-xl">
                    <!-- Submission Info -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-h1 text-lg text-gray-900">بيانات البحث</h3>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">عنوان البحث</p>
                                    <p class="font-button text-gray-900"><?= htmlspecialchars($submission['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm mb-6">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-h1 text-lg text-gray-900">تفاصيل الرسوم</h3>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-600"><?= $paymentType === 'initial' ? 'رسوم المراجعة الأولية' : 'رسوم حجم العينة' ?></span>
                                <span class="text-2xl font-bold text-indigo-700">
                                    <?= htmlspecialchars(PaymentHelpers::formatAmount($paymentAmount), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="font-h1 text-lg text-gray-900">طريقة الدفع</h3>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/payment/store" class="p-5">
                            <input type="hidden" name="submission_id" value="<?= htmlspecialchars($submission['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="payment_type" value="<?= htmlspecialchars($paymentType, ENT_QUOTES, 'UTF-8') ?>">

                            <!-- Card Option -->
                            <div class="mb-3">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition" id="card-option-label">
                                    <input type="radio" name="payment_method" value="card" checked class="w-5 h-5" id="method-card" onchange="togglePhoneInput()">
                                    <div class="mr-4 flex-1">
                                        <p class="font-button text-gray-900">بطاقة ائتمان / خصم</p>
                                        <p class="text-sm text-gray-600 mt-1">ادفع باستخدام Visa أو Mastercard</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Wallet Option -->
                            <div class="mb-3">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition" id="wallet-option-label">
                                    <input type="radio" name="payment_method" value="wallet" class="w-5 h-5" id="method-wallet" onchange="togglePhoneInput()">
                                    <div class="mr-4 flex-1">
                                        <p class="font-button text-gray-900">محفظة الهاتف المحمول</p>
                                        <p class="text-sm text-gray-600 mt-1">ادفع عبر Vodafone Cash, Orange Cash, e& money, We Pay</p>
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
                                        walletLabel.style.borderColor = '#4338ca';
                                        cardLabel.style.borderColor = '';
                                    } else {
                                        phoneContainer.classList.add('hidden');
                                        phoneInput.removeAttribute('required');
                                        cardLabel.style.borderColor = '#4338ca';
                                        walletLabel.style.borderColor = '';
                                    }
                                }
                                // Initialize on load
                                togglePhoneInput();
                            </script>

                            <div class="flex gap-4">
                                <button type="submit" class="flex-1 bg-indigo-700 text-white px-6 py-3 rounded-lg font-button hover:bg-indigo-800 transition-all shadow-sm hover:shadow-lg">
                                    المتابعة للدفع
                                </button>
                                <a href="<?= BASE_URL ?>/student/submissions" class="flex-1 bg-gray-100 text-gray-900 px-6 py-3 rounded-lg font-button text-center hover:bg-gray-200">
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
