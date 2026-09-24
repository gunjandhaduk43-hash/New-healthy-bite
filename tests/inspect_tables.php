<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();
$stmt = $db->query("
    SELECT t.id, t.restaurant_id, t.branch_id, t.table_number, t.status, q.token AS qr_token 
    FROM restaurant_tables t
    LEFT JOIN qr_tokens q ON q.table_id = t.id AND q.status = 'active'
    WHERE t.restaurant_id = 1 
    ORDER BY t.id ASC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID: {$r['id']}, Number: {$r['table_number']}, Status: {$r['status']}, Token: {$r['qr_token']}\n";
}
