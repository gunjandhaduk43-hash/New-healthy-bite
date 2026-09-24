<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

// Find test dishes
$stmt = $pdo->query("SELECT id, name FROM food_items WHERE name LIKE 'Test%' OR slug LIKE 'test%'");
$testItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($testItems) . " test dishes to delete:\n";
foreach ($testItems as $item) {
    echo " - ID {$item['id']}: {$item['name']}\n";
}

if (!empty($testItems)) {
    $ids = array_column($testItems, 'id');
    $inClause = implode(',', array_map('intval', $ids));
    
    // Check foreign keys
    $pdo->exec("DELETE FROM food_variants WHERE food_item_id IN ($inClause)");
    $pdo->exec("DELETE FROM food_customizations WHERE food_item_id IN ($inClause)");
    try { $pdo->exec("DELETE FROM order_item_customizations WHERE order_item_id IN (SELECT id FROM order_items WHERE food_item_id IN ($inClause))"); } catch (\Throwable $e) {}
    try { $pdo->exec("DELETE FROM order_items WHERE food_item_id IN ($inClause)"); } catch (\Throwable $e) {}
    try { $pdo->exec("DELETE FROM cart_items WHERE food_item_id IN ($inClause)"); } catch (\Throwable $e) {}
    
    $deleted = $pdo->exec("DELETE FROM food_items WHERE id IN ($inClause)");
    echo "Successfully deleted $deleted test dish(es)!\n";
} else {
    echo "No test dishes found.\n";
}
