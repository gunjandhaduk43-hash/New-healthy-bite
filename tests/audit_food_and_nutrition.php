<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

echo "=== 1. TABLES IN DATABASE ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

echo "\n=== 2. COLUMNS OF food_items ===\n";
$cols = $pdo->query("DESCRIBE food_items")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "{$c['Field']} - {$c['Type']} - Null: {$c['Null']} - Default: {$c['Default']}\n";
}

echo "\n=== 3. COLUMNS OF food_variants (IF EXISTS) ===\n";
if (in_array('food_variants', $tables, true)) {
    $cols = $pdo->query("DESCRIBE food_variants")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} - {$c['Type']} - Null: {$c['Null']} - Default: {$c['Default']}\n";
    }
} else {
    echo "food_variants table NOT found!\n";
}

echo "\n=== 4. COLUMNS OF food_customizations (IF EXISTS) ===\n";
if (in_array('food_customizations', $tables, true)) {
    $cols = $pdo->query("DESCRIBE food_customizations")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} - {$c['Type']} - Null: {$c['Null']} - Default: {$c['Default']}\n";
    }
} else {
    echo "food_customizations table NOT found!\n";
}

echo "\n=== 5. COLUMNS OF order_items / order_item_snapshots (IF EXISTS) ===\n";
foreach (['order_items', 'order_item_snapshots', 'order_item_customizations', 'order_customization_snapshots'] as $ot) {
    if (in_array($ot, $tables, true)) {
        echo "--- Columns for $ot ---\n";
        $cols = $pdo->query("DESCRIBE $ot")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            echo "{$c['Field']} - {$c['Type']} - Null: {$c['Null']} - Default: {$c['Default']}\n";
        }
    }
}

echo "\n=== 6. AUDIT OF ALL FOOD ITEMS (ID, Name, Image, Calories, Protein, Carbs, Fat) ===\n";
$foods = $pdo->query("SELECT id, restaurant_id, category_id, name, image, calories, protein, carbs, fat, base_price FROM food_items ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($foods as $f) {
    echo sprintf(
        "ID: %2d | Rest: %d | Cat: %d | Name: %-35s | Price: ₹%-6.2f | Cal: %s | Pro: %s | Carb: %s | Fat: %s | Image: %s\n",
        $f['id'],
        $f['restaurant_id'],
        $f['category_id'],
        $f['name'],
        $f['base_price'],
        $f['calories'] ?? 'NULL',
        $f['protein'] ?? 'NULL',
        $f['carbs'] ?? 'NULL',
        $f['fat'] ?? 'NULL',
        $f['image'] ?? 'NULL'
    );
}

echo "\n=== 7. AUDIT OF ALL FOOD VARIANTS ===\n";
if (in_array('food_variants', $tables, true)) {
    $variants = $pdo->query("SELECT * FROM food_variants")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($variants as $v) {
        print_r($v);
    }
}

echo "\n=== 8. AUDIT OF ALL FOOD CUSTOMIZATIONS ===\n";
if (in_array('food_customizations', $tables, true)) {
    $customizations = $pdo->query("SELECT * FROM food_customizations")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($customizations as $cu) {
        print_r($cu);
    }
}
