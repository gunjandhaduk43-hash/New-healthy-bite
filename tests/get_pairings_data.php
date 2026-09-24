<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();
$stmt = $pdo->query('SELECT id, name, base_price, image, calories, protein, carbs, fat, sugar FROM food_items WHERE id IN (4, 47, 42, 39)');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
