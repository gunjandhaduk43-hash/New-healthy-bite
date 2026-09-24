<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

echo "=== 1. CHECK IMAGE UNIQUENESS ACROSS OFFICIAL FOOD ITEMS (1-49) ===\n";
$stmt = $pdo->query("SELECT id, name, image FROM food_items WHERE id <= 49 ORDER BY id");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

$images = [];
$dups = [];
foreach ($foods as $f) {
    if (isset($images[$f['image']])) {
        $dups[] = "Image shared by #{$f['id']} ({$f['name']}) and #{$images[$f['image']]}";
    }
    $images[$f['image']] = $f['id'];
}

echo "Total official foods: " . count($foods) . "\n";
echo "Unique images: " . count($images) . "\n";
if (empty($dups)) {
    echo "✓ 100% UNIQUE IMAGES: ZERO DUPLICATES!\n\n";
} else {
    echo "DUPLICATES: " . implode("\n", $dups) . "\n\n";
}

echo "=== 2. CHECK VARIANT SUGAR ADJUSTMENTS ===\n";
$varSummary = $pdo->query("
    SELECT 
        COUNT(*) as total,
        COUNT(sugar_adjustment) as non_null,
        SUM(CASE WHEN sugar_adjustment = 0 THEN 1 ELSE 0 END) as zero_adj,
        SUM(CASE WHEN sugar_adjustment > 0 THEN 1 ELSE 0 END) as pos_adj
    FROM food_variants
")->fetch(PDO::FETCH_ASSOC);
echo "Total Variants: {$varSummary['total']} | Non-null Sugar Adj: {$varSummary['non_null']} | Zero: {$varSummary['zero_adj']} | Positive: {$varSummary['pos_adj']}\n\n";

echo "=== 3. CHECK CUSTOMIZATION SUGAR ADJUSTMENTS ===\n";
$custSummary = $pdo->query("
    SELECT 
        COUNT(*) as total,
        COUNT(sugar_adjustment) as non_null,
        SUM(CASE WHEN sugar_adjustment = 0 THEN 1 ELSE 0 END) as zero_adj,
        SUM(CASE WHEN sugar_adjustment > 0 THEN 1 ELSE 0 END) as pos_adj,
        SUM(CASE WHEN sugar_adjustment < 0 THEN 1 ELSE 0 END) as neg_adj
    FROM food_customizations
")->fetch(PDO::FETCH_ASSOC);
echo "Total Customizations: {$custSummary['total']} | Non-null Sugar Adj: {$custSummary['non_null']} | Zero: {$custSummary['zero_adj']} | Positive: {$custSummary['pos_adj']} | Negative: {$custSummary['neg_adj']}\n";
