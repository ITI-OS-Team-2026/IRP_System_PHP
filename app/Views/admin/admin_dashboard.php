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
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
	<div class="min-h-screen flex flex-col lg:flex-row-reverse">
		<?php require __DIR__ . '/partials/sidebar.php'; ?>

		<main class="flex-1">
			<?php require __DIR__ . '/partials/navbar.php'; ?>

			<section class="px-4 md:px-8 py-6 space-y-6">
				<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
					<?php foreach ($summaryCards as $card): ?>
						<article class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] p-5 flex flex-col gap-4 min-h-[220px]">
							<div class="flex items-start justify-between gap-4">
								<div class="text-3xl font-bold text-charcoal"><?= htmlspecialchars($card['count'], ENT_QUOTES, 'UTF-8') ?></div>
								<div class="w-10 h-10 rounded-lg <?= htmlspecialchars($card['accent'], ENT_QUOTES, 'UTF-8') ?> text-white flex items-center justify-center">
									<span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
								</div>
							</div>
							<div class="space-y-2 flex-1">
								<h3 class="font-h1 text-lg text-charcoal"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
								<p class="text-sm leading-7 text-slate-gray"><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
							</div>
							<a href="<?= htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-lg px-4 py-3 font-button text-sm transition-colors <?= htmlspecialchars($card['buttonStyle'], ENT_QUOTES, 'UTF-8') ?>">
								<?= htmlspecialchars($card['action'], ENT_QUOTES, 'UTF-8') ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
					<div class="lg:col-span-2 rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)]">
						<div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
							<h3 class="font-h1 text-lg text-charcoal">أحدث النشاطات</h3>
							<a href="#" class="text-sm font-button text-primary hover:underline">عرض الكل</a>
						</div>
						<div class="divide-y divide-slate-200">
							<?php foreach ($activityItems as $activity): ?>
								<div class="px-5 py-4 flex items-start gap-4">
									<span class="inline-flex items-center justify-center rounded-md bg-primary px-3 py-2 text-xs font-button text-on-primary shrink-0"><?= htmlspecialchars($activity['label'], ENT_QUOTES, 'UTF-8') ?></span>
									<div class="flex-1 text-right">
										<p class="text-sm leading-7 text-charcoal"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></p>
										<p class="text-xs text-slate-gray mt-1"><?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?></p>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="rounded-xl border border-[#3f4779] bg-white shadow-[0_2px_12px_rgba(15,23,42,0.05)] p-5">
						<h3 class="font-h1 text-lg text-charcoal mb-4">مؤشرات سريعة</h3>
						<div class="space-y-4 text-sm">
							<?php foreach ($quickMetrics as $metric): ?>
								<div class="flex items-center justify-between"><span class="text-slate-gray"><?= htmlspecialchars($metric['label'], ENT_QUOTES, 'UTF-8') ?></span><span class="font-button text-charcoal"><?= htmlspecialchars($metric['value'], ENT_QUOTES, 'UTF-8') ?></span></div>
							<?php endforeach; ?>
						</div>
						<div class="mt-6 rounded-lg bg-slate-50 border border-slate-200 p-4">
							<p class="text-sm leading-7 text-slate-gray">
								هذه المساحة مخصصة لاحقاً لرسوم بيانية أو تنبيهات حية حسب بيانات النظام الفعلية.
							</p>
						</div>
					</div>
				</div>
			</section>
		</main>
	</div>
</body>
