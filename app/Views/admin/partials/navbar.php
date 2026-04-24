<?php
$adminPageHeading = $adminPageHeading ?? 'لوحة تحكم الإدارة';
$adminPageSubtitle = $adminPageSubtitle ?? 'نظرة عامة على النظام';
?>
<header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
    <div>
        <p class="text-sm text-slate-gray"><?= htmlspecialchars($adminPageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <h2 class="font-h1 text-2xl text-charcoal"><?= htmlspecialchars($adminPageHeading, ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <a href="/logout" class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center border border-red-500" aria-label="تسجيل الخروج" title="تسجيل الخروج">
        <span class="material-symbols-outlined text-[20px]">logout</span>
    </a>
</header>
