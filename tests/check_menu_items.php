<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) require_once $file;
});
require_once HB_ROOT . '/config/constants.php';
\App\Core\Env::load(HB_ROOT . '/.env');

$db = \App\Core\Database::getConnection();
$stmt = $db->query('SELECT id, name, category_id, image, base_price, is_available FROM food_items LIMIT 25');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("ID: %-3d | Name: %-30s | Cat: %s | Img: %-22s | ₹%s | Avail: %s\n", $row['id'], $row['name'], $row['category_id'], $row['image'] ?? 'NULL', $row['base_price'], $row['is_available']);
}
