<?php
/**
 * Paymob Transaction Response Callback (Redirection)
 * Paymob redirects the user here after payment attempt
 */

// Bootstrap: load env and DB
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Repositories/PaymentRepository.php';

// Log everything
$logFile = __DIR__ . '/storage/logs/paymob_receipt.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'uri' => $_SERVER['REQUEST_URI'] ?? '',
    'get' => $_GET,
    'post' => $_POST,
    'headers' => getallheaders(),
];
file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

// Extract Paymob redirect parameters
$rawSuccess = (string) ($_GET['success'] ?? $_POST['success'] ?? 'false');
$rawPending = (string) ($_GET['pending'] ?? $_POST['pending'] ?? 'false');
$txnResponseCode = (string) ($_GET['txn_response_code'] ?? $_POST['txn_response_code'] ?? '');
$orderId = (int) ($_GET['order'] ?? $_GET['order_id'] ?? 0);
$transactionId = (int) ($_GET['id'] ?? $_GET['transaction_id'] ?? 0);
$amountCents = (int) ($_GET['amount_cents'] ?? 0);
$currency = $_GET['currency'] ?? 'EGP';

// Fix: use explicit string comparison — Paymob sends success="true" (string)
$success = (
    strtolower(trim($rawSuccess)) === 'true' &&
    trim($txnResponseCode) === '200'
);
$pending = strtolower(trim($rawPending)) === 'true';

// Debug logging
file_put_contents($logFile, "[Receipt] raw_success={$rawSuccess}, txn_response_code={$txnResponseCode}, parsed_success=" . ($success ? '1' : '0') . ", parsed_pending=" . ($pending ? '1' : '0') . ", order={$orderId}\n", FILE_APPEND | LOCK_EX);

// Try to find payment by order_id and update status as a fallback
if ($orderId > 0) {
    try {
        $db = Database::getConnection();
        $repo = new PaymentRepository($db);
        $payment = $repo->getPaymentByPaymobOrderId($orderId);
        var_dump($payment); exit;

        if ($payment) {
            $dbStatus = $payment['payment_status'] ?? 'unknown';

            // DB fallback: if already completed, force UI success
            if ($dbStatus === 'completed') {
                $success = true;
                $pending = false;
                file_put_contents($logFile, "[Receipt] Payment {$payment['id']} already COMPLETED in DB — forcing UI success\n", FILE_APPEND | LOCK_EX);
            } elseif ($success) {
                $repo->markPaymentCompleted((int) $payment['id'], $transactionId);
                $newStatus = ($payment['payment_type'] === 'sample_size') ? 'fully_paid' : 'initial_paid';
                $repo->updateSubmissionStatus((int) $payment['submission_id'], $newStatus);
                file_put_contents($logFile, "[Receipt] Payment {$payment['id']} marked COMPLETED from redirect\n", FILE_APPEND | LOCK_EX);
            } elseif (!$pending) {
                $repo->markPaymentFailed((int) $payment['id'], 'Payment failed on redirect', $transactionId);
                file_put_contents($logFile, "[Receipt] Payment {$payment['id']} marked FAILED from redirect\n", FILE_APPEND | LOCK_EX);
            }
        }
    } catch (Exception $e) {
        file_put_contents($logFile, "[Receipt] DB Error: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
    }
}

// Show simple receipt page
$baseUrl = getenv('BASE_URL') ?: '/';
$type = $payment['payment_type'] ?? null;
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRB - نتيجة الدفع</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <?php if ($success): ?>
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-green-700 mb-2">تم الدفع بنجاح!</h1>
            <p class="text-slate-600 mb-4">تم تأكيد دفع <?= $type === 'sample_size' ? 'رسوم حجم العينة' : 'رسوم التقديم' ?> بنجاح.</p>
            <div class="bg-slate-50 rounded-lg p-4 text-right mb-4 text-sm text-slate-700">
                <?php if (isset($payment) && isset($payment['id'])): ?>
                <p><strong>رقم الدفعة:</strong> #<?= (int) $payment['id'] ?></p>
                <?php endif; ?>
                <p><strong>نوع الدفع:</strong> <?= $type === 'sample_size' ? 'رسوم حجم العينة' : 'رسوم التقديم' ?></p>
                <p><strong>رقم الطلب:</strong> <?= htmlspecialchars((string) $orderId) ?></p>
                <p><strong>المبلغ:</strong> <?= number_format($amountCents / 100, 2) ?> <?= htmlspecialchars($currency) ?></p>
            </div>
            <a href="<?= htmlspecialchars($baseUrl) ?>/student/submissions" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                العودة إلى طلباتي
            </a>
        <?php elseif ($pending): ?>
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-yellow-700 mb-2">الدفع قيد المعالجة</h1>
            <p class="text-slate-600 mb-4">تم استلام طلب الدفع وهو قيد المعالجة. سيتم تحديث الحالة قريباً.</p>
            <a href="<?= htmlspecialchars($baseUrl) ?>/student/submissions" class="inline-block bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition">
                العودة إلى طلباتي
            </a>
        <?php else: ?>
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-red-700 mb-2">فشل الدفع</h1>
            <p class="text-slate-600 mb-4">لم يتم إتمام عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم.</p>
            <a href="<?= htmlspecialchars($baseUrl) ?>/student/submissions" class="inline-block bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                العودة إلى طلباتي
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
