<?php

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'IRP System',
    'host' => $_ENV['MAIL_HOST'] ?? '',
    'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
    'username' => $_ENV['MAIL_USERNAME'] ?? '',
    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'encryption' => strtolower((string) ($_ENV['MAIL_ENCRYPTION'] ?? 'tls')),
    'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? '',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? ($_ENV['APP_NAME'] ?? 'IRP System'),
    'reply_to_address' => $_ENV['MAIL_REPLY_TO_ADDRESS'] ?? '',
    'reply_to_name' => $_ENV['MAIL_REPLY_TO_NAME'] ?? '',
    'timeout' => (int) ($_ENV['MAIL_TIMEOUT'] ?? 20),
    'debug' => (int) ($_ENV['MAIL_DEBUG'] ?? 0),
];