<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

$stmt = $pdo->query("
    SELECT f.id, f.name, c.name as category_name, f.food_type, f.calories, f.protein, f.carbs, f.fat, f.sugar, f.image, f.description
    FROM food_items f
    JOIN categories c ON f.category_id = c.id
    ORDER BY f.id ASC
");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($foods as $f) {
    echo "ID: {$f['id']} | [{$f['category_name']}] {$f['name']} ({$f['food_type']})\n";
    echo "  Macros: Cal={$f['calories']}kcal, P={$f['protein']}g, C={$f['carbs']}g, F={$f['fat']}g, Sugar=" . ($f['sugar'] !== null ? $f['sugar'] . 'g' : 'NULL') . "\n";
    echo "  Image: {$f['image']}\n";
    echo "  Desc: {$f['description']}\n\n";
}
