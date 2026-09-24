<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__));

require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Core/Env.php';
require_once HB_ROOT . '/app/Core/Database.php';

\App\Core\Env::load(HB_ROOT . '/.env');

$db = \App\Core\Database::getConnection();

echo "=== DATABASE EXTRACTION REPORT ===\n";

$tablesStmt = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
$tables = $tablesStmt->fetchAll(PDO::FETCH_NUM);

$dbName = 'healthy_bite';

$allData = [];

foreach ($tables as $tRow) {
    $tableName = $tRow[0];
    
    // Row count
    $cntStmt = $db->query("SELECT COUNT(*) FROM `{$tableName}`");
    $rowCount = (int)$cntStmt->fetchColumn();

    // Columns from information_schema
    $colStmt = $db->prepare("
        SELECT 
            COLUMN_NAME, ORDINAL_POSITION, COLUMN_DEFAULT, IS_NULLABLE, 
            DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE,
            COLUMN_TYPE, COLUMN_KEY, EXTRA, COLUMN_COMMENT
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table
        ORDER BY ORDINAL_POSITION
    ");
    $colStmt->execute([':schema' => $dbName, ':table' => $tableName]);
    $columns = $colStmt->fetchAll(PDO::FETCH_ASSOC);

    // Foreign keys
    $fkStmt = $db->prepare("
        SELECT 
            k.CONSTRAINT_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME,
            r.UPDATE_RULE, r.DELETE_RULE
        FROM information_schema.KEY_COLUMN_USAGE k
        JOIN information_schema.REFERENTIAL_CONSTRAINTS r 
            ON k.CONSTRAINT_NAME = r.CONSTRAINT_NAME AND k.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA
        WHERE k.TABLE_SCHEMA = :schema AND k.TABLE_NAME = :table
          AND k.REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY k.ORDINAL_POSITION
    ");
    $fkStmt->execute([':schema' => $dbName, ':table' => $tableName]);
    $foreignKeys = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

    // Referencing tables (tables that point to this table)
    $referencingStmt = $db->prepare("
        SELECT 
            k.TABLE_NAME, k.COLUMN_NAME, k.CONSTRAINT_NAME,
            r.UPDATE_RULE, r.DELETE_RULE
        FROM information_schema.KEY_COLUMN_USAGE k
        JOIN information_schema.REFERENTIAL_CONSTRAINTS r 
            ON k.CONSTRAINT_NAME = r.CONSTRAINT_NAME AND k.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA
        WHERE k.TABLE_SCHEMA = :schema AND k.REFERENCED_TABLE_NAME = :table
    ");
    $referencingStmt->execute([':schema' => $dbName, ':table' => $tableName]);
    $referencingTables = $referencingStmt->fetchAll(PDO::FETCH_ASSOC);

    // Indexes
    $idxStmt = $db->prepare("
        SHOW INDEXES FROM `{$tableName}`
    ");
    $idxStmt->execute();
    $indexes = $idxStmt->fetchAll(PDO::FETCH_ASSOC);

    // Show create table
    $createStmt = $db->query("SHOW CREATE TABLE `{$tableName}`");
    $createRow = $createStmt->fetch(PDO::FETCH_NUM);
    $createSql = $createRow[1] ?? '';

    $allData[$tableName] = [
        'row_count' => $rowCount,
        'columns' => $columns,
        'foreign_keys' => $foreignKeys,
        'referencing_tables' => $referencingTables,
        'indexes' => $indexes,
        'create_sql' => $createSql
    ];
}

file_put_contents(HB_ROOT . '/storage/db_extraction.json', json_encode($allData, JSON_PRETTY_PRINT));
echo "Extracted " . count($allData) . " tables to storage/db_extraction.json successfully.\n";
foreach ($allData as $name => $d) {
    echo "- Table: {$name} ({$d['row_count']} rows, " . count($d['columns']) . " cols, " . count($d['foreign_keys']) . " FKs)\n";
}
