<?php
$pagerPagination = $pagerPagination ?? [
    'currentPage' => 1,
    'lastPage' => 1,
    'from' => 0,
    'to' => 0,
    'hasPrevious' => false,
    'hasNext' => false,
    'previousPage' => 1,
    'nextPage' => 1,
];
$pagerTotal = $pagerTotal ?? 0;
$pagerBasePath = $pagerBasePath ?? '#';
$pagerQuery = $pagerQuery ?? [];
$pagerItemLabel = $pagerItemLabel ?? 'عنصر';

$buildPagerHref = static function ($page) use ($pagerBasePath, $pagerQuery) {
    $params = $pagerQuery;
    if ((int) $page > 1) {
        $params['page'] = (int) $page;
    } else {
        unset($params['page']);
    }

    $queryString = http_build_query($params);
    return $queryString !== '' ? ($pagerBasePath . '?' . $queryString) : $pagerBasePath;
};
?>
<div class="flex justify-between items-center mt-6 border-t border-charcoal pt-4">
    <span class="font-body-sm text-body-sm text-slate-gray">عرض <?= htmlspecialchars((string) $pagerPagination['from'], ENT_QUOTES, 'UTF-8') ?> إلى <?= htmlspecialchars((string) $pagerPagination['to'], ENT_QUOTES, 'UTF-8') ?> من أصل <?= htmlspecialchars((string) $pagerTotal, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($pagerItemLabel, ENT_QUOTES, 'UTF-8') ?></span>
    <div class="flex gap-2">
        <?php if ($pagerPagination['hasPrevious']): ?>
            <a href="<?= htmlspecialchars($buildPagerHref($pagerPagination['previousPage']), ENT_QUOTES, 'UTF-8') ?>" class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm">السابق</a>
        <?php else: ?>
            <span class="border border-charcoal bg-paper-white px-3 py-1 text-charcoal font-bold text-sm opacity-50 cursor-not-allowed">السابق</span>
        <?php endif; ?>

        <span class="border border-charcoal bg-royal-indigo text-on-primary px-3 py-1 font-bold text-sm">
            <?= htmlspecialchars((string) $pagerPagination['currentPage'], ENT_QUOTES, 'UTF-8') ?> / <?= htmlspecialchars((string) $pagerPagination['lastPage'], ENT_QUOTES, 'UTF-8') ?>
        </span>

        <?php if ($pagerPagination['hasNext']): ?>
            <a href="<?= htmlspecialchars($buildPagerHref($pagerPagination['nextPage']), ENT_QUOTES, 'UTF-8') ?>" class="border border-charcoal bg-paper-white px-3 py-1 hover:bg-surface-dim transition-colors text-charcoal font-bold text-sm">التالي</a>
        <?php else: ?>
            <span class="border border-charcoal bg-paper-white px-3 py-1 text-charcoal font-bold text-sm opacity-50 cursor-not-allowed">التالي</span>
        <?php endif; ?>
    </div>
</div>