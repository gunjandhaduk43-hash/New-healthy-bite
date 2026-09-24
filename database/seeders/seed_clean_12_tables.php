<?php
define('HB_ROOT', dirname(__DIR__, 2));
require_once HB_ROOT . '/app/Core/Env.php';
require_once HB_ROOT . '/app/Core/Database.php';
\App\Core\Env::load(HB_ROOT . '/.env');
$db = \App\Core\Database::getConnection();

echo "=== Ensuring Clean Table 1-12 and Tokens for Greenhouse Kitchen ===\n";

// Ensure tables 1 to 12 exist for restaurant 1, branch 1
for ($i = 1; $i <= 12; $i++) {
    $tableNum = "Table {$i}";
    $status = in_array($i, [1, 5, 9]) ? 'occupied' : 'available';

    // Find table
    $stmt = $db->prepare("SELECT id FROM restaurant_tables WHERE restaurant_id = 1 AND branch_id = 1 AND table_number = :num LIMIT 1");
    $stmt->execute([':num' => $tableNum]);
    $table = $stmt->fetch();

    if ($table) {
        $tableId = (int)$table['id'];
        $db->prepare("UPDATE restaurant_tables SET status = :status WHERE id = :id")->execute([':status' => $status, ':id' => $tableId]);
    } else {
        $insert = $db->prepare("INSERT INTO restaurant_tables (restaurant_id, branch_id, table_number, status) VALUES (1, 1, :num, :status)");
        $insert->execute([':num' => $tableNum, ':status' => $status]);
        $tableId = (int)$db->lastInsertId();
    }

    // Clean duplicate qr tokens for this table
    $db->prepare("DELETE FROM qr_tokens WHERE table_id = :tid")->execute([':tid' => $tableId]);

    // Insert clean active token
    $token = "hb_table_{$i}_qr_" . substr(md5("hb_tbl_{$i}_salt_2026"), 0, 8);
    $db->prepare("INSERT INTO qr_tokens (restaurant_id, branch_id, table_id, token, status) VALUES (1, 1, :tid, :token, 'active')")
       ->execute([':tid' => $tableId, ':token' => $token]);
}

echo "Cleaned and seeded 12 tables with unique QR tokens successfully.\n";
