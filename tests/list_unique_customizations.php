<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::getConnection();

$stmt = $pdo->query("
    SELECT DISTINCT c.group_name, c.name, c.price_adjustment, c.calories_adjustment
    FROM food_customizations c
    ORDER BY c.group_name, c.name
");
$custs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "TOTAL UNIQUE CUSTOMIZATION OPTIONS: " . count($custs) . "\n\n";
foreach ($custs as $c) {
    echo sprintf("[%-24s] %-35s | Price: +₹%-5.2f | Cal: %s\n", $c['group_name'], $c['name'], $c['price_adjustment'], $c['calories_adjustment'] ?? 'NULL');
}
