<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../app/Core/Env.php';
require_once __DIR__ . '/../../app/Core/Database.php';

\App\Core\Env::load(__DIR__ . '/../../.env');
$pdo = \App\Core\Database::getConnection();

echo "=== RESTORING REFERENTIAL INTEGRITY FOR ORDER_ITEM_CUSTOMIZATIONS ===\n";

$orphans = $pdo->query("
    SELECT DISTINCT oic.customization_id, oi.food_item_id, oic.customization_name_snapshot, oic.price_adjustment,
           oic.calories_adjustment, oic.protein_adjustment, oic.carbs_adjustment, oic.fat_adjustment,
           oic.fiber_adjustment, oic.sugar_adjustment, oic.sodium_adjustment, oic.caffeine_adjustment
    FROM order_item_customizations oic
    JOIN order_items oi ON oic.order_item_id = oi.id
    WHERE oic.customization_id NOT IN (SELECT id FROM food_customizations)
    ORDER BY oic.customization_id
")->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($orphans) . " missing customization references.\n";

$insertStmt = $pdo->prepare("
    INSERT INTO food_customizations (
        id, food_item_id, name, description, group_name, price_adjustment,
        calories_adjustment, protein_adjustment, carbs_adjustment, fat_adjustment,
        fiber_adjustment, sugar_adjustment, sodium_adjustment, caffeine_adjustment,
        is_required, min_quantity, max_quantity, is_available, sort_order
    ) VALUES (
        :id, :food_item_id, :name, :description, :group_name, :price_adjustment,
        :calories_adjustment, :protein_adjustment, :carbs_adjustment, :fat_adjustment,
        :fiber_adjustment, :sugar_adjustment, :sodium_adjustment, :caffeine_adjustment,
        0, 0, 1, 0, 99
    )
    ON DUPLICATE KEY UPDATE name = VALUES(name)
");

$inserted = 0;
foreach ($orphans as $o) {
    try {
        $insertStmt->execute([
            ':id' => $o['customization_id'],
            ':food_item_id' => $o['food_item_id'],
            ':name' => $o['customization_name_snapshot'],
            ':description' => 'Historical order option preserved for referential integrity',
            ':group_name' => 'Legacy Options',
            ':price_adjustment' => $o['price_adjustment'] ?? 0.00,
            ':calories_adjustment' => $o['calories_adjustment'],
            ':protein_adjustment' => $o['protein_adjustment'],
            ':carbs_adjustment' => $o['carbs_adjustment'],
            ':fat_adjustment' => $o['fat_adjustment'],
            ':fiber_adjustment' => $o['fiber_adjustment'],
            ':sugar_adjustment' => $o['sugar_adjustment'],
            ':sodium_adjustment' => $o['sodium_adjustment'],
            ':caffeine_adjustment' => $o['caffeine_adjustment'],
        ]);
        $inserted++;
    } catch (\Exception $e) {
        echo "Error inserting id {$o['customization_id']}: " . $e->getMessage() . "\n";
    }
}

echo "Successfully inserted $inserted legacy customization records (all marked is_available = 0).\n";
