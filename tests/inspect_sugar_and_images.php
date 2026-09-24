<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

echo "=== ALL FOOD VARIANTS ===\n";
$variants = $pdo->query("SELECT * FROM food_variants ORDER BY food_item_id, id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($variants as $v) {
    echo "ID: {$v['id']} | Food: {$v['food_item_id']} | Name: {$v['name']} | PriceAdj: {$v['price_adjustment']} | CalAdj: {$v['calories_adjustment']} | ProtAdj: {$v['protein_adjustment']} | CarbsAdj: {$v['carbs_adjustment']} | FatAdj: {$v['fat_adjustment']} | SugarAdj: {$v['sugar_adjustment']}\n";
}

echo "\n=== FIRST 20 CUSTOMIZATIONS ===\n";
$custs = $pdo->query("SELECT * FROM food_customizations ORDER BY food_item_id, id LIMIT 30")->fetchAll(PDO::FETCH_ASSOC);
foreach ($custs as $c) {
    echo "ID: {$c['id']} | Food: {$c['food_item_id']} | Group: {$c['group_name']} | Name: {$c['name']} | PriceAdj: {$c['price_adjustment']} | CalAdj: {$c['calories_adjustment']} | SugarAdj: {$c['sugar_adjustment']}\n";
}

echo "\n=== FOOD ITEMS 50-56 STATUS ===\n";
$testFoods = $pdo->query("SELECT id, restaurant_id, name, is_available, created_at FROM food_items WHERE id >= 50")->fetchAll(PDO::FETCH_ASSOC);
foreach ($testFoods as $tf) {
    echo "ID: {$tf['id']} | Name: {$tf['name']} | Avail: {$tf['is_available']} | Created: {$tf['created_at']}\n";
}
