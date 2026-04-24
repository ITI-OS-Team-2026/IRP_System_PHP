<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <?php
    $pageTitle = 'نظام إدارة الموافقات البحثية - IRB Digital System';
    $includeTailwind = false;
    require __DIR__ . '/head.php';
    ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css">
</head>
<body>
    <div class="app-wrapper">
        <?= $content ?? '' ?>
    </div>
</body>
</html>
