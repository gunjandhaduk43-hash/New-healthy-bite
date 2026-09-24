<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

echo "========================================================================================\n";
echo "1. FOOD ITEMS AUDIT (NAME, IMAGE, NUTRITION INCLUDING SUGAR)\n";
echo "========================================================================================\n";
$stmt = $pdo->query("SELECT f.id, f.restaurant_id, c.name as category, f.name, f.image, f.calories, f.protein, f.carbs, f.fat, f.sugar, f.fiber 
                     FROM food_items f 
                     LEFT JOIN categories c ON c.id = f.category_id 
                     ORDER BY f.id ASC");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$imageCounts = [];
foreach ($foods as $f) {
    $img = $f['image'] ?? 'MISSING';
    if (!isset($imageCounts[$img])) {
        $imageCounts[$img] = [];
    }
    $imageCounts[$img][] = "#{$f['id']} {$f['name']}";
    
    echo sprintf(
        "ID: %2d | Cat: %-15s | Name: %-38s | Cal: %4s | Pro: %5s | Carb: %5s | Fat: %5s | Sugar: %5s | Img: %s\n",
        $f['id'],
        $f['category'] ?? 'None',
        $f['name'],
        $f['calories'] ?? 'NULL',
        $f['protein'] ?? 'NULL',
        $f['carbs'] ?? 'NULL',
        $f['fat'] ?? 'NULL',
        $f['sugar'] !== null ? $f['sugar'] . 'g' : 'NULL',
        $f['image'] ?? 'NULL'
    );
}

echo "\n========================================================================================\n";
echo "2. DUPLICATE IMAGES DETECTED IN FOOD CATALOG\n";
echo "========================================================================================\n";
foreach ($imageCounts as $img => $items) {
    if (count($items) > 1) {
        echo "Image: $img\n";
        echo "  Used by " . count($items) . " items:\n";
        foreach ($items as $item) {
            echo "    - $item\n";
        }
        echo "\n";
    }
}

echo "========================================================================================\n";
echo "3. FOOD VARIANTS AUDIT\n";
echo "========================================================================================\n";
$variants = $pdo->query("SELECT v.*, f.name as food_name FROM food_variants v JOIN food_items f ON f.id = v.food_item_id ORDER BY v.food_item_id, v.id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($variants as $v) {
    echo sprintf(
        "Var ID: %2d | Food: #%2d %-25s | Name: %-15s | +₹%-6.2f | +Cal: %4s | +Pro: %5s | +Carb: %5s | +Fat: %5s | +Sugar: %5s\n",
        $v['id'],
        $v['food_item_id'],
        $v['food_name'],
        $v['name'],
        $v['price_adjustment'],
        $v['calories_adjustment'] ?? 'NULL',
        $v['protein_adjustment'] ?? 'NULL',
        $v['carbs_adjustment'] ?? 'NULL',
        $v['fat_adjustment'] ?? 'NULL',
        $v['sugar_adjustment'] !== null ? $v['sugar_adjustment'] . 'g' : 'NULL'
    );
}

echo "\n========================================================================================\n";
echo "4. FOOD CUSTOMIZATIONS AUDIT\n";
echo "========================================================================================\n";
$customs = $pdo->query("SELECT c.*, f.name as food_name FROM food_customizations c JOIN food_items f ON f.id = c.food_item_id ORDER BY c.food_item_id, c.group_name, c.id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($customs as $c) {
    echo sprintf(
        "Cust ID: %3d | Food: #%2d %-25s | Group: %-20s | Name: %-30s | +₹%-6.2f | +Cal: %4s | +Sugar: %5s\n",
        $c['id'],
        $c['food_item_id'],
        $c['food_name'],
        $c['group_name'],
        $c['name'],
        $c['price_adjustment'],
        $c['calories_adjustment'] ?? 'NULL',
        $c['sugar_adjustment'] !== null ? $c['sugar_adjustment'] . 'g' : 'NULL'
    );
}
