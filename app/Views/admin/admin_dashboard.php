<?php
$pageTitle = 'لوحة تحكم الإدارة - IRB Portal';
require __DIR__ . '/../layouts/head.php';

$summaryCards = [
	[
		'count' => '12',
		'title' => 'حسابات قيد التنشيط',
		'description' => 'باحثون جدد ينتظرون التحقق من وثائقهم الأكاديمية والموافقة على حساباتهم.',
		'action' => 'مراجعة الحسابات',
		'icon' => 'person_add',
		'accent' => 'bg-primary',
		'buttonStyle' => 'bg-primary text-on-primary hover:bg-primary-container',
	],
	[
		'count' => '5',
		'title' => 'طلبات بانتظار الرقم التسلسلي',
		'description' => 'دراسات استوفت الشروط الأولية وتنتظر إصدار رقم تسلسلي ورمز اعتماد.',
		'action' => 'إصدار الأرقام',
		'icon' => '123',
		'accent' => 'bg-surface-container-low',
		'buttonStyle' => 'border border-charcoal text-charcoal hover:bg-surface-container',
	],
	[
		'count' => '8',
		'title' => 'طلبات جاهزة للمراجعين',
		'description' => 'مقترحات مكتملة من الناحية الإجرائية ويمكن توزيعها على المراجعين المختصين.',
		'action' => 'تعيين المراجعين',
		'icon' => 'badge',
		'accent' => 'bg-surface-container-low',
		'buttonStyle' => 'border border-charcoal text-charcoal hover:bg-surface-container',
	],
];

$activityItems = [
	['label' => 'تحديث مرفقات', 'title' => 'أ. أحمد محمود قام بتحديث مستندات الدراسة IRB-2023-045', 'time' => 'قبل 15 دقيقة'],
	['label' => 'حساب جديد', 'title' => 'طلب إنشاء حساب جديد من سارة عبد الرحمن (كلية الطب)', 'time' => 'قبل ساعتين'],
	['label' => 'مراجعة مكتملة', 'title' => 'تم اعتماد المراجعة المبدئية للدراسة IRB-2023-042 بواسطة المسؤول الإداري', 'time' => 'أمس 14:30'],
];

$sidebarItems = [
	['label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => BASE_URL . '/admin/dashboard', 'active' => true],
	['label' => 'تنشيط الحسابات', 'icon' => 'person_add', 'href' => '#'],
	['label' => 'المراجعة المبدئية', 'icon' => 'fact_check', 'href' => '#'],
	['label' => 'تعيين المراجعين', 'icon' => 'group_add', 'href' => '#'],
	['label' => 'الإعدادات', 'icon' => 'settings', 'href' => '#'],
];
?>
<body class="min-h-screen bg-[#f6f7fb] text-charcoal rtl font-body-lg">
	<div class="min-h-screen flex flex-col lg:flex-row-reverse">
		<aside class="w-full lg:w-[260px] bg-white border-l border-slate-200 shadow-sm lg:shadow-none">
			<div class="p-5 border-b border-slate-200 flex items-center gap-4">
				<div class="w-14 h-14 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500">
					<span class="material-symbols-outlined text-3xl">account_balance</span>
				</div>
				<div>
					<h1 class="font-h1 text-lg text-charcoal">IRB</h1>
					<p class="text-sm text-slate-gray">بوابة التدقيق والبحث</p>
				</div>
			</div>

			<nav class="p-4 space-y-1">
				<?php foreach ($sidebarItems as $item): ?>
					<a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-colors <?= !empty($item['active']) ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-gray hover:bg-slate-100 hover:text-charcoal' ?>">
						<span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
						<span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</aside>

		<main class="flex-1">
			<header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
				<div>
					<p class="text-sm text-slate-gray">نظرة عامة على النظام</p>
					<h2 class="font-h1 text-2xl text-charcoal">لوحة تحكم الإدارة</h2>
				</div>

				<div class="flex items-center gap-3 w-full md:w-auto md:min-w-[420px]">
					<label class="flex items-center gap-2 w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2">
						<span class="material-symbols-outlined text-slate-gray text-[20px]">search</span>
						<input type="text" class="bg-transparent outline-none w-full text-sm" placeholder="بحث في النظام..." />
					</label>
					<button class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200" type="button">
						<span class="material-symbols-outlined text-[20px]">notifications</span>
					</button>
					<a href="<?php echo BASE_URL; ?>/logout" class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center border border-red-500" title="تسجيل الخروج">
						<span class="material-symbols-outlined text-[20px]">logout</span>
					</a>
				</div>
			</header>

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
							<a href="#" class="inline-flex items-center justify-center rounded-lg px-4 py-3 font-button text-sm transition-colors <?= htmlspecialchars($card['buttonStyle'], ENT_QUOTES, 'UTF-8') ?>">
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
							<div class="flex items-center justify-between"><span class="text-slate-gray">الحسابات المفعلة</span><span class="font-button text-charcoal">84%</span></div>
							<div class="flex items-center justify-between"><span class="text-slate-gray">الطلبات المكتملة</span><span class="font-button text-charcoal">67%</span></div>
							<div class="flex items-center justify-between"><span class="text-slate-gray">متوسط زمن المراجعة</span><span class="font-button text-charcoal">14 يوم</span></div>
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
