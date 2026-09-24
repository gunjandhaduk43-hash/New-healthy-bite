<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();
$stmt = $pdo->query("
    SELECT f.id, c.name as cat_name, f.name, f.image, f.description
    FROM food_items f
    LEFT JOIN categories c ON f.category_id = c.id
    WHERE f.id <= 20
    ORDER BY f.id ASC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    echo "ID: {$item['id']} | [{$item['cat_name']}] {$item['name']}\n";
    echo "  Desc: {$item['description']}\n";
    echo "  Img:  {$item['image']}\n\n";
}
