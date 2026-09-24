<?php
$data = json_decode(file_get_contents(__DIR__ . '/../storage/db_extraction.json'), true);
foreach ($data as $table => $info) {
    echo "=== TABLE: {$table} ===\n";
    echo "Rows: {$info['row_count']}\n";
    echo "Columns:\n";
    foreach ($info['columns'] as $c) {
        $def = $c['COLUMN_DEFAULT'] === null ? 'NULL' : $c['COLUMN_DEFAULT'];
        echo "  - {$c['COLUMN_NAME']} | {$c['COLUMN_TYPE']} | Null:{$c['IS_NULLABLE']} | Def:{$def} | Key:{$c['COLUMN_KEY']} | Extra:{$c['EXTRA']}\n";
    }
    if (!empty($info['foreign_keys'])) {
        echo "Foreign Keys:\n";
        foreach ($info['foreign_keys'] as $fk) {
            echo "  - {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']}) [{$fk['CONSTRAINT_NAME']}] ON UPDATE {$fk['UPDATE_RULE']} ON DELETE {$fk['DELETE_RULE']}\n";
        }
    } else {
        echo "Foreign Keys: NONE\n";
    }
    if (!empty($info['referencing_tables'])) {
        echo "Referenced By:\n";
        foreach ($info['referencing_tables'] as $rf) {
            echo "  - {$rf['TABLE_NAME']}({$rf['COLUMN_NAME']}) [{$rf['CONSTRAINT_NAME']}]\n";
        }
    } else {
        echo "Referenced By: NONE\n";
    }
    echo "\n";
}
