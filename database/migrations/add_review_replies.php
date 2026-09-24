<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__, 2));
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) require_once $file;
});
require_once HB_ROOT . '/config/constants.php';
\App\Core\Env::load(HB_ROOT . '/.env');

$db = \App\Core\Database::getConnection();

try {
    $cols = $db->query("SHOW COLUMNS FROM reviews")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('restaurant_reply', $cols, true)) {
        $db->exec("ALTER TABLE reviews ADD COLUMN restaurant_reply TEXT NULL AFTER comment");
        echo "Added restaurant_reply column.\n";
    } else {
        echo "restaurant_reply column already exists.\n";
    }

    if (!in_array('replied_at', $cols, true)) {
        $db->exec("ALTER TABLE reviews ADD COLUMN replied_at TIMESTAMP NULL AFTER restaurant_reply");
        echo "Added replied_at column.\n";
    } else {
        echo "replied_at column already exists.\n";
    }

    // Seed one sample reply so the user can see existing replies as well
    $db->exec("UPDATE reviews SET restaurant_reply = 'Thank you Gunjan! We are thrilled you enjoyed the protein bowl.', replied_at = NOW() WHERE id = 1 AND restaurant_reply IS NULL");

    echo "Migration completed successfully.\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
