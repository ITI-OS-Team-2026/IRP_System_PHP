<?php
/**
 * Paymob Transaction Processed Callback
 * Server-to-server POST from Paymob after transaction is processed
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
$logFile = __DIR__ . '/storage/logs/paymob_callback.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$rawInput = file_get_contents('php://input');
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'uri' => $_SERVER['REQUEST_URI'] ?? '',
    'get' => $_GET,
    'post' => $_POST,
    'raw_input' => $rawInput,
    'headers' => getallheaders(),
];
file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

// Parse callback data (Paymob sends POST with transaction data)
// If raw input is JSON, parse it; otherwise use $_POST
$callbackData = [];
if (!empty($rawInput)) {
    $jsonData = json_decode($rawInput, true);
    if (is_array($jsonData)) {
        $callbackData = $jsonData;
    }
}
if (empty($callbackData)) {
    $callbackData = $_POST;
}

// Paymob classic API callback fields
$orderId = (int) ($callbackData['order'] ?? $callbackData['order_id'] ?? 0);
$transactionId = (int) ($callbackData['id'] ?? $callbackData['transaction_id'] ?? 0);
$rawSuccess = (string) ($callbackData['success'] ?? 'false');
$txnResponseCode = (string) ($callbackData['txn_response_code'] ?? '');
$hmacReceived = $callbackData['hmac'] ?? '';

// Fix: use explicit string comparison — Paymob sends success="true" (string)
$success = (
    strtolower(trim($rawSuccess)) === 'true' &&
    trim($txnResponseCode) === '200'
);

// Debug logging
file_put_contents($logFile, "[Callback] raw_success={$rawSuccess}, txn_response_code={$txnResponseCode}, parsed_success=" . ($success ? '1' : '0') . ", order={$orderId}\n", FILE_APPEND | LOCK_EX);

// HMAC verification (optional but recommended)
$paymobConfig = require __DIR__ . '/config/paymob.php';
$hmacSecret = $paymobConfig['hmac_secret'] ?? '';
$verified = false;

if (!empty($hmacSecret) && !empty($hmacReceived)) {
    $hmacKeys = [
        'amount_cents', 'created_at', 'currency', 'delivery_fees_cents',
        'error_occured', 'has_delivery_service', 'id', 'is_cancellation',
        'is_refund', 'is_return', 'merchant_order_id', 'order_id',
        'owner', 'pending', 'source_data_pan', 'source_data_sub_type',
        'source_data_type', 'success',
    ];
    $concat = '';
    foreach ($hmacKeys as $k) {
        $concat .= $callbackData[$k] ?? '';
    }
    $calculated = hash_hmac('sha512', $concat, $hmacSecret);
    $verified = hash_equals($calculated, $hmacReceived);
}

// Update database
if ($orderId > 0) {
    try {
        $db = Database::getConnection();
        $repo = new PaymentRepository($db);

        $payment = $repo->getPaymentByPaymobOrderId($orderId);

        if ($payment) {
            if ($success) {
                $repo->markPaymentCompleted((int) $payment['id'], $transactionId);
                $newStatus = ($payment['payment_type'] === 'sample_size') ? 'fully_paid' : 'initial_paid';
                $repo->updateSubmissionStatus((int) $payment['submission_id'], $newStatus);
                file_put_contents($logFile, "[Callback] Payment {$payment['id']} marked COMPLETED\n", FILE_APPEND | LOCK_EX);
            } else {
                $failureReason = $callbackData['data_message'] ?? $callbackData['message'] ?? 'Transaction failed (callback)';
                $repo->markPaymentFailed((int) $payment['id'], $failureReason, $transactionId);
                file_put_contents($logFile, "[Callback] Payment {$payment['id']} marked FAILED: {$failureReason}\n", FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($logFile, "[Callback] No payment found for order_id={$orderId}\n", FILE_APPEND | LOCK_EX);
        }
    } catch (Exception $e) {
        file_put_contents($logFile, "[Callback] DB Error: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
    }
}

// Always return 200 OK so Paymob knows we received it
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';
