<?php
/**
 * Paymob redirect entry point
 * Forwards to the secure controller route — all logic handled by PaymentController::paymobReturn()
 */

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
$query = !empty($_GET) ? '?' . http_build_query($_GET) : '';
header('Location: ' . $basePath . '/receipt' . $query);
exit;
