<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__));

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = HB_ROOT . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

\App\Core\Env::load(HB_ROOT . '/.env');

echo "=== Verifying Database Connection ===\n";
try {
    $db = \App\Core\Database::getConnection();
    echo "✓ Database connected successfully.\n";
} catch (\Throwable $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Verifying Key Repositories ===\n";
try {
    $restRepo = new \App\Repositories\RestaurantRepository();
    $restaurant = $restRepo->findAnyById(1);
    echo "✓ Restaurant loaded: " . ($restaurant['name'] ?? 'Unknown') . "\n";

    $orderRepo = new \App\Repositories\OrderRepository();
    $kitchenOrders = $orderRepo->getKitchenOrders(1);
    echo "✓ Kitchen orders count: " . count($kitchenOrders) . "\n";

    $revRepo = new \App\Repositories\ReviewRepository();
    $summary = $revRepo->getSummaryStats(1);
    echo "✓ Review stats: " . ($summary['total_reviews'] ?? 0) . " reviews, avg rating: " . ($summary['avg_rating'] ?? 0) . "\n";

    $staffRepo = new \App\Repositories\StaffRepository();
    $staff = $staffRepo->findByRestaurant(1);
    echo "✓ Staff count: " . count($staff) . "\n";

    $platformStats = $restRepo->getPlatformOverviewStats();
    echo "✓ Platform stats: " . ($platformStats['total_restaurants'] ?? 0) . " restaurants, " . ($platformStats['total_orders'] ?? 0) . " orders\n";

} catch (\Throwable $e) {
    echo "✗ Repository verification failed: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== ALL REPOSITORY VERIFICATIONS PASSED ===\n";
