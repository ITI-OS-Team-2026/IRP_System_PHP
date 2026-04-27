<?php
$pageTitle = 'لوحة تحكم الإدارة - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$activeAdminPage = 'dashboard';
$adminPageHeading = 'لوحة تحكم الإدارة';
$adminPageSubtitle = 'نظرة عامة على النظام';

$summaryCards = $summaryCards ?? [];
$activityItems = $activityItems ?? [];
$quickMetrics = $quickMetrics ?? [];

?>
<body class="min-h-screen bg-gray-50 text-gray-900 rtl font-body-lg">
    <div class="min-h-screen flex flex-col lg:flex-row-reverse">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="flex-1">
            <?php require __DIR__ . '/partials/navbar.php'; ?>

            <section class="px-4 md:px-8 py-6 space-y-6">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <?php foreach ($summaryCards as $card): ?>
                        <article class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col gap-4 min-h-[220px]">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($card['count'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="w-10 h-10 rounded-lg bg-indigo-700 text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                            <div class="space-y-2 flex-1">
                                <h3 class="font-h1 text-lg text-gray-900"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="text-sm leading-7 text-gray-600"><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <a href="<?= htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-lg px-4 py-3 font-button text-sm transition-all duration-300 bg-indigo-700 text-white hover:bg-indigo-800 shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                <?= htmlspecialchars($card['action'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-h1 text-lg text-gray-900">أحدث النشاطات</h3>
                            <a href="#" class="text-sm font-button text-indigo-700 hover:underline">عرض الكل</a>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($activityItems as $activity): ?>
                                <div class="px-5 py-4 flex items-start gap-4 hover:bg-gray-50 transition-colors">
                                    <span class="inline-flex items-center justify-center rounded-md bg-indigo-700 px-3 py-2 text-xs font-button text-white shrink-0"><?= htmlspecialchars($activity['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <div class="flex-1 text-right">
                                        <p class="text-sm leading-7 text-gray-900"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300 p-6">
                        <h3 class="font-h1 text-lg text-gray-900 mb-4">مؤشرات سريعة</h3>
                        <div class="space-y-4 text-sm">
                            <?php foreach ($quickMetrics as $metric): ?>
                                <div class="flex items-center justify-between"><span class="text-gray-600"><?= htmlspecialchars($metric['label'], ENT_QUOTES, 'UTF-8') ?></span><span class="font-button text-gray-900"><?= htmlspecialchars($metric['value'], ENT_QUOTES, 'UTF-8') ?></span></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 rounded-lg bg-gray-50 border border-gray-200 p-4">
                            <p class="text-sm leading-7 text-gray-600">
                                هذه المساحة مخصصة لاحقاً لرسوم بيانية أو تنبيهات حية حسب بيانات النظام الفعلية.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
