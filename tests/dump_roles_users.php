<?php
define('HB_ROOT', dirname(__DIR__));
require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Core/Env.php';
require_once HB_ROOT . '/app/Core/Database.php';
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();

echo "=== ROLES IN DATABASE ===\n";
$roles = $db->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) {
    echo "Role ID {$r['id']}: {$r['name']} ({$r['slug']}) - {$r['description']}\n";
}

echo "\n=== USERS IN DATABASE ===\n";
$users = $db->query("
    SELECT u.id, u.role_id, r.name as role_name, r.slug as role_slug,
           u.restaurant_id, rest.name as restaurant_name,
           u.name, u.email, u.status, u.created_at
    FROM users u
    JOIN roles r ON u.role_id = r.id
    LEFT JOIN restaurants rest ON u.restaurant_id = rest.id
    ORDER BY u.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $u) {
    echo "User #{$u['id']}: {$u['name']} <{$u['email']}> | Role: {$u['role_name']} (ID: {$u['role_id']}) | Rest: " . ($u['restaurant_name'] ?? 'None (Platform)') . " | Status: {$u['status']}\n";
}
