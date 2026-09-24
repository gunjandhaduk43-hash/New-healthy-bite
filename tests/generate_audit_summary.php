<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT f.id, f.restaurant_id, c.name as category, f.name, f.image, f.calories, f.protein, f.carbs, f.fat, f.sugar, f.fiber 
                     FROM food_items f 
                     LEFT JOIN categories c ON c.id = f.category_id 
                     ORDER BY f.id ASC");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$duplicates = [];
foreach ($foods as $f) {
    $img = $f['image'] ?? 'MISSING';
    $duplicates[$img][] = [
        'id' => $f['id'],
        'name' => $f['name'],
        'category' => $f['category'],
        'sugar' => $f['sugar']
    ];
}

$output = "=== TOTAL FOOD ITEMS: " . count($foods) . " ===\n\n";

$output .= "=== DUPLICATE IMAGES DETECTED ===\n";
foreach ($duplicates as $img => $items) {
    if (count($items) > 1) {
        $output .= "Image URL: $img\n";
        $output .= "Shared by (" . count($items) . " items):\n";
        foreach ($items as $it) {
            $output .= "  - ID {$it['id']}: {$it['name']} ({$it['category']})\n";
        }
        $output .= "\n";
    }
}

$output .= "=== ALL FOOD ITEMS WITH NUTRITION & CURRENT IMAGES ===\n";
foreach ($foods as $f) {
    $sugarStr = $f['sugar'] !== null ? $f['sugar'] . 'g' : 'NULL';
    $output .= sprintf("#%02d | %-32s | %-12s | Sug: %-5s | %s\n", $f['id'], substr($f['name'], 0, 32), substr($f['category'] ?? '', 0, 12), $sugarStr, $f['image']);
}

file_put_contents(__DIR__ . '/audit_summary.txt', $output);
echo "Wrote audit_summary.txt successfully (" . strlen($output) . " bytes)\n";
