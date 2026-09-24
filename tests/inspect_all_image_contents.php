<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();
$stmt = $pdo->query("
    SELECT f.id, c.name as cat_name, f.name, f.image
    FROM food_items f
    LEFT JOIN categories c ON f.category_id = c.id
    ORDER BY f.id ASC
");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as $item) {
    $url = $item['image'];
    $photoId = '';
    if (preg_match('/photo-([0-9a-f-]+)/', $url, $m)) {
        $photoId = $m[1];
    }
    echo sprintf("[%02d] %-35s => Photo: %s\n", $item['id'], $item['name'], $photoId ?: $url);
}
