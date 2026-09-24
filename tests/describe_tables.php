<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();

echo "=== TABLES IN DB ===\n";
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

echo "\n=== DESCRIBE restaurant_tables ===\n";
print_r($db->query("DESCRIBE restaurant_tables")->fetchAll(PDO::FETCH_ASSOC));

if (in_array('table_qr_codes', $tables)) {
    echo "\n=== DESCRIBE table_qr_codes ===\n";
    print_r($db->query("DESCRIBE table_qr_codes")->fetchAll(PDO::FETCH_ASSOC));
}
