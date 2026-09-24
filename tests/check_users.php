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

// Ensure both alias emails exist as active records in DB as well
$passHash = password_hash('Secret@123', PASSWORD_BCRYPT);
$db->exec("
    INSERT INTO users (role_id, restaurant_id, name, email, password, status, created_at)
    VALUES (1, NULL, 'System Admin', 'admin@healthybite.com', '{$passHash}', 'active', NOW())
    ON DUPLICATE KEY UPDATE password = '{$passHash}', status = 'active';
");
$db->exec("
    INSERT INTO users (role_id, restaurant_id, name, email, password, status, created_at)
    VALUES (2, 1, 'Greenhouse Owner', 'owner@greenhousekitchen.com', '{$passHash}', 'active', NOW())
    ON DUPLICATE KEY UPDATE password = '{$passHash}', status = 'active', restaurant_id = 1;
");

$repo = new \App\Repositories\UserRepository();
$testEmails = [
    'admin@healthybite.com',
    'mira@healthybite.in',
    'admin@healthybite.in',
    'owner@greenhousekitchen.com',
    'aarav@greenhouse.in',
    'owner@greenhouse.in'
];

echo "Testing user lookup via UserRepository:\n";
foreach ($testEmails as $email) {
    $u = $repo->findByEmail($email);
    if ($u) {
        $verify = password_verify('Secret@123', $u['password']) ? 'OK' : 'FAIL';
        echo sprintf("✓ %-30s => Found: %-20s (Role: %d, Rest: %s, Pass: %s)\n", $email, $u['name'], $u['role_id'], $u['restaurant_id'] ?? 'null', $verify);
    } else {
        echo sprintf("✗ %-30s => NOT FOUND\n", $email);
    }
}
