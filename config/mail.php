<?php

return [
    'app_name'         => env('APP_NAME', 'IRP System'),
    'host'             => env('MAIL_HOST', ''),
    'port'             => (int) env('MAIL_PORT', 587),
    'username'         => env('MAIL_USERNAME', ''),
    'password'         => env('MAIL_PASSWORD', ''),
    'encryption'       => strtolower((string) env('MAIL_ENCRYPTION', 'tls')),
    'from_address'     => env('MAIL_FROM_ADDRESS', ''),
    'from_name'        => env('MAIL_FROM_NAME', env('APP_NAME', 'IRP System')),
    'reply_to_address' => env('MAIL_REPLY_TO_ADDRESS', ''),
    'reply_to_name'    => env('MAIL_REPLY_TO_NAME', ''),
    'timeout'          => (int) env('MAIL_TIMEOUT', 20),
    'debug'            => (int) env('MAIL_DEBUG', 0),
];