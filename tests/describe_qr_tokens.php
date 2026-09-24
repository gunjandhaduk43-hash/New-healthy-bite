<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();

echo "=== DESCRIBE qr_tokens ===\n";
print_r($db->query("DESCRIBE qr_tokens")->fetchAll(PDO::FETCH_ASSOC));
