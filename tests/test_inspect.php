<?php
define('HB_ROOT', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $file = HB_ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require_once $file;
});
\App\Core\Env::load(HB_ROOT . '/.env');

$repo = new \App\Repositories\RestaurantRepository();
$res = $repo->getInspectionDetails(1);
echo "Inspection Details for Restaurant 1:\n";
echo "Name: " . $res['name'] . "\n";
echo "Branches: " . $res['branches_count'] . "\n";
echo "Tables: " . $res['table_stats']['total_tables'] . "\n";
echo "Menu Items: " . $res['menu_stats']['total_items'] . "\n";
echo "Categories: " . $res['menu_stats']['category_count'] . "\n";
echo "Sales: " . $res['sales_stats']['monthly_sales'] . "\n";
echo "Review Stats: " . $res['review_stats']['total_reviews'] . " reviews, avg: " . $res['review_stats']['avg_rating'] . "\n";
echo "SUCCESS!\n";
