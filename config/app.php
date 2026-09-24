<?php
declare(strict_types=1);

return [
    'name'     => $_ENV['APP_NAME'] ?? 'Healthy Bite',
    'env'      => $_ENV['APP_ENV'] ?? 'development',
    'url'      => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'debug'    => ($_ENV['APP_ENV'] ?? 'development') === 'development',
    'timezone' => 'Asia/Kolkata',
    'key'      => $_ENV['APP_KEY'] ?? 'hb_default_insecure_key',
    'tax_rate' => (float)($_ENV['TAX_RATE'] ?? 0.05),
    'service_charge' => (float)($_ENV['SERVICE_CHARGE'] ?? 0.00),
    'default_restaurant_id' => (int)($_ENV['DEFAULT_RESTAURANT_ID'] ?? 1),
];
