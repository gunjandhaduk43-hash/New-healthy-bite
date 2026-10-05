<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../app/Core/Env.php';
require_once __DIR__ . '/../app/Core/Database.php';

\App\Core\Env::load(__DIR__ . '/../.env');
$pdo = \App\Core\Database::getConnection();

echo "=== PHASE 3: DATABASE INTEGRITY & ORPHAN AUDIT ===\n";

// 1. Check table count
$tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
echo "Total Tables: " . count($tables) . "\n";

// 2. Expected 17 tables
$expectedTables = [
    'admin', 'branches', 'categories', 'customers', 'food_customizations',
    'food_items', 'food_variants', 'order_item_customizations', 'order_items',
    'orders', 'payments', 'qr_tokens', 'restaurant_tables', 'restaurants',
    'reviews', 'roles', 'users'
];

$missingTables = array_diff($expectedTables, $tables);
$extraTables = array_diff($tables, $expectedTables);

if (!empty($missingTables)) {
    echo "ERROR: Missing tables: " . implode(', ', $missingTables) . "\n";
} else {
    echo "OK: All 17 expected physical tables exist.\n";
}

if (!empty($extraTables)) {
    echo "WARNING: Extra tables: " . implode(', ', $extraTables) . "\n";
}

// 3. Count total columns
$colStmt = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'healthy_bite'");
$totalCols = (int)$colStmt->fetchColumn();
echo "Total Columns: $totalCols (Expected 208)\n";

// 4. Check all 26 foreign keys
$fkStmt = $pdo->query("
    SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'healthy_bite' AND REFERENCED_TABLE_NAME IS NOT NULL
");
$fks = $fkStmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total Foreign Keys: " . count($fks) . " (Expected 26)\n";

// 5. Orphan record checks across all FKs
$orphanCount = 0;
foreach ($fks as $fk) {
    $table = $fk['TABLE_NAME'];
    $col = $fk['COLUMN_NAME'];
    $refTable = $fk['REFERENCED_TABLE_NAME'];
    $refCol = $fk['REFERENCED_COLUMN_NAME'];
    
    $checkSql = "
        SELECT COUNT(*) 
        FROM `$table` t
        LEFT JOIN `$refTable` r ON t.`$col` = r.`$refCol`
        WHERE t.`$col` IS NOT NULL AND r.`$refCol` IS NULL
    ";
    try {
        $orphans = (int)$pdo->query($checkSql)->fetchColumn();
        if ($orphans > 0) {
            echo "ORPHAN DETECTED: $table.$col has $orphans rows pointing to non-existent $refTable.$refCol!\n";
            $orphanCount++;
        }
    } catch (\Exception $e) {
        echo "Error checking orphans for $table.$col: " . $e->getMessage() . "\n";
    }
}

if ($orphanCount === 0) {
    echo "OK: Zero orphan records across all 26 foreign keys!\n";
}

// 6. Check unique constraints (e.g. payments.order_id)
$uniqueStmt = $pdo->query("
    SELECT TABLE_NAME, COLUMN_NAME, INDEX_NAME
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'healthy_bite' AND NON_UNIQUE = 0 AND INDEX_NAME != 'PRIMARY'
");
$uniques = $uniqueStmt->fetchAll(PDO::FETCH_ASSOC);
echo "Unique constraints found: " . count($uniques) . "\n";
$hasOrderPaymentUnique = false;
foreach ($uniques as $u) {
    if ($u['TABLE_NAME'] === 'payments' && $u['COLUMN_NAME'] === 'order_id') {
        $hasOrderPaymentUnique = true;
    }
}
if ($hasOrderPaymentUnique) {
    echo "OK: payments.order_id UNIQUE constraint verified (1:1 relationship strictly enforced).\n";
} else {
    echo "ERROR: payments.order_id UNIQUE constraint MISSING!\n";
}

// 7. Check snapshot consistency in order_items
$snapshotCheckSql = "
    SELECT COUNT(*) FROM order_items 
    WHERE food_name_snapshot IS NULL OR food_name_snapshot = ''
";
$emptySnapshots = (int)$pdo->query($snapshotCheckSql)->fetchColumn();
echo "order_items with empty food_name_snapshot: $emptySnapshots\n";

echo "=== DATABASE INTEGRITY AUDIT COMPLETE ===\n";
