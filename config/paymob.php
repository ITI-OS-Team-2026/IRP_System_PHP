<?php

$env = static function ($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
};

return [
    // Paymob Intention API (v2 Unified Checkout)
    'secret_key' => $env('PAYMOB_SECRET_KEY', ''),
    'public_key' => $env('PAYMOB_PUBLIC_KEY', ''),
    'api_key' => $env('PAYMOB_API_KEY', ''),
    'merchant_id' => (int) $env('PAYMOB_MERCHANT_ID', 0),
    'wallet_integration_id' => (int) $env('PAYMOB_WALLET_INTEGRATION_ID', 0),
    'card_integration_id' => (int) $env('PAYMOB_CARD_INTEGRATION_ID', 0),
    'integration_id' => (int) $env('PAYMOB_WALLET_INTEGRATION_ID', $env('PAYMOB_INTEGRATION_ID', 0)),
    'hmac_secret' => $env('PAYMOB_HMAC_SECRET', ''),

    'api' => [
        'intention_url' => 'https://accept.paymobsolutions.com/v1/intention/',
        'unified_checkout' => 'https://accept.paymob.com/unifiedcheckout/',
    ],

    'payment' => [
        'amount' => (float) $env('INITIAL_PAYMENT_AMOUNT', 500),
        'sample_size_rate' => (float) $env('SAMPLE_SIZE_RATE', 10),
        'currency' => $env('CURRENCY', 'EGP'),
    ],

    'callbacks' => [
        'base_url' => rtrim($env('BASE_URL') ?: $env('WEBHOOK_BASE_URL') ?: $env('APP_BASE_URL', ''), '/'),
    ],
];
