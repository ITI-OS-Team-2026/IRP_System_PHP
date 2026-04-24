<?php
$adminPageHeading = $adminPageHeading ?? 'لوحة تحكم الإدارة';
$adminPageSubtitle = $adminPageSubtitle ?? 'نظرة عامة على النظام';
?>
<header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
    <div>
        <p class="text-sm text-slate-gray"><?= htmlspecialchars($adminPageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <h2 class="font-h1 text-2xl text-charcoal"><?= htmlspecialchars($adminPageHeading, ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto md:min-w-[420px]">
        <label class="flex items-center gap-2 w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2">
            <span class="material-symbols-outlined text-slate-gray text-[20px]">search</span>
            <input type="text" class="bg-transparent outline-none w-full text-sm" placeholder="بحث في النظام..." />
        </label>
        <button class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200" type="button">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
        </button>
        <button class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center border border-red-500" type="button">
            <span class="material-symbols-outlined text-[20px]">logout</span>
        </button>
    </div>
</header>
