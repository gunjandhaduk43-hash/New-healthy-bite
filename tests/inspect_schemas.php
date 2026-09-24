<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

function showCols($pdo, $table) {
    echo "=== COLUMNS OF $table ===\n";
    try {
        $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            echo sprintf("%-25s | %-15s | Null: %-3s | Default: %s\n", $c['Field'], $c['Type'], $c['Null'], $c['Default'] ?? 'NULL');
        }
    } catch (\Exception $e) {
        echo "Error or table does not exist: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

showCols($pdo, 'food_items');
showCols($pdo, 'food_variants');
showCols($pdo, 'food_customizations');
showCols($pdo, 'order_items');
showCols($pdo, 'order_item_snapshots');
showCols($pdo, 'order_item_customizations');
showCols($pdo, 'order_customization_snapshots');
