<?php
require_once __DIR__ . '/../app/Core/Database.php';
$pdo = \App\Core\Database::getConnection();
$vars = $pdo->query("SELECT v.id, v.food_item_id, f.name as food_name, v.name as var_name, v.sugar_adjustment FROM food_variants v JOIN food_items f ON f.id = v.food_item_id WHERE v.sugar_adjustment IS NOT NULL LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vars as $v) {
    echo "Food #{$v['food_item_id']} ({$v['food_name']}) | Var #{$v['id']} ({$v['var_name']}) | Sugar: {$v['sugar_adjustment']}g\n";
}
