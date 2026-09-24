<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

echo "=== 1. ALL FOOD VARIANTS ===\n";
$vars = $pdo->query("SELECT id, food_item_id, name, price_adjustment, calories_adjustment, protein_adjustment, carbs_adjustment, fat_adjustment, sugar_adjustment FROM food_variants ORDER BY food_item_id, id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vars as $v) {
    echo sprintf(
        "Var #%2d (Food #%2d) | Name: %-16s | Price: +₹%-6.2f | Cal: %4s | Sugar: %s\n",
        $v['id'],
        $v['food_item_id'],
        $v['name'],
        $v['price_adjustment'],
        $v['calories_adjustment'] ?? 'NULL',
        $v['sugar_adjustment'] !== null ? $v['sugar_adjustment'] . 'g' : 'NULL'
    );
}

echo "\n=== 2. CUSTOMIZATIONS NON-NULL SUGAR COUNT ===\n";
$totalCust = $pdo->query("SELECT COUNT(*) FROM food_customizations")->fetchColumn();
$nonNullSugar = $pdo->query("SELECT COUNT(*) FROM food_customizations WHERE sugar_adjustment IS NOT NULL")->fetchColumn();
echo "Total Customizations: $totalCust, with sugar_adjustment: $nonNullSugar\n\n";

echo "=== 3. ALL CUSTOMIZATIONS BY FOOD ===\n";
$custs = $pdo->query("SELECT c.id, c.food_item_id, f.name as food_name, c.group_name, c.name, c.price_adjustment, c.calories_adjustment, c.sugar_adjustment 
                      FROM food_customizations c 
                      JOIN food_items f ON f.id = c.food_item_id 
                      ORDER BY c.food_item_id, c.id")->fetchAll(PDO::FETCH_ASSOC);

foreach ($custs as $c) {
    echo sprintf(
        "Cust #%3d (Food #%2d %-20s) | [%-18s] %-32s | +₹%-6.2f | Cal: %4s | Sugar: %s\n",
        $c['id'],
        $c['food_item_id'],
        substr($c['food_name'], 0, 20),
        substr($c['group_name'], 0, 18),
        substr($c['name'], 0, 32),
        $c['price_adjustment'],
        $c['calories_adjustment'] ?? 'NULL',
        $c['sugar_adjustment'] !== null ? $c['sugar_adjustment'] . 'g' : 'NULL'
    );
}
