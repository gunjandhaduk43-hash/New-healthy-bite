<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();

$repo = new \App\Repositories\BranchRepository();
$tables = $repo->getTablesWithQr(1, 1);
echo "Tables with QR count: " . count($tables) . "\n";
foreach ($tables as $t) {
    echo "ID: {$t['id']}, Num: {$t['table_number']}, Status: {$t['status']}, QR: {$t['qr_token']}\n";
}
