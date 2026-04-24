<?php
$activeAdminPage = $activeAdminPage ?? 'dashboard';
$adminSidebarItems = $adminSidebarItems ?? [
    ['key' => 'dashboard', 'label' => 'لوحة التحكم', 'icon' => 'dashboard', 'href' => '/admin/dashboard'],
    ['key' => 'user_activation', 'label' => 'تنشيط الحسابات', 'icon' => 'person_add', 'href' => '/admin/user-activation'],
    ['key' => 'initial_preview', 'label' => 'المراجعة المبدئية', 'icon' => 'fact_check', 'href' => '/admin/initial-preview-queue'],
    ['key' => 'reviewer_assignment', 'label' => 'تعيين المراجعين', 'icon' => 'group_add', 'href' => '/admin/reviewer-assignment'],
    ['key' => 'settings', 'label' => 'الإعدادات', 'icon' => 'settings', 'href' => '#'],
];
?>
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
        <?php foreach ($adminSidebarItems as $item): ?>
            <?php $isActive = $activeAdminPage === $item['key']; ?>
            <a
                href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-button transition-colors <?= $isActive ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-gray hover:bg-slate-100 hover:text-charcoal' ?>"
            >
                <span class="material-symbols-outlined text-[20px]"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
