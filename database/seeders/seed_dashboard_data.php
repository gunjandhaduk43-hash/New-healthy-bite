<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/Core/Database.php';

use App\Core\Database;

$db = Database::getConnection();

// 1. Roles
$db->exec("
    INSERT IGNORE INTO roles (id, name, slug, description) VALUES
    (1, 'Super Admin', 'super_admin', 'Platform administrator'),
    (2, 'Restaurant Owner', 'restaurant_owner', 'Owner of a restaurant entity'),
    (3, 'Manager', 'manager', 'Branch manager'),
    (4, 'Kitchen Staff', 'kitchen_staff', 'Kitchen staff handling orders');
");

// 2. Additional Restaurants matching Figma
$db->exec("
    INSERT INTO restaurants (id, name, slug, phone, email, address, city, state, status) VALUES
    (1, 'Greenhouse Kitchen', 'greenhouse-kitchen', '+91 98765 43210', 'hello@greenhousekitchen.com', '100 Feet Road, Indiranagar', 'Bengaluru', 'Karnataka', 'approved'),
    (2, 'Green Earth Bistro', 'green-earth-bistro', '+91 98200 44551', 'contact@greenearthbistro.in', 'Koregaon Park', 'Pune', 'Maharashtra', 'approved'),
    (3, 'Pure Green Kitchen', 'pure-green-kitchen', '+91 99876 22110', 'info@puregreenkitchen.in', 'Bandra West', 'Mumbai', 'Maharashtra', 'pending')
    ON DUPLICATE KEY UPDATE name = VALUES(name), city = VALUES(city), status = VALUES(status);
");

// 3. Branches
$db->exec("
    INSERT INTO branches (id, restaurant_id, name, city, state, status) VALUES
    (1, 1, 'Indiranagar Branch', 'Bengaluru', 'Karnataka', 'active'),
    (2, 1, 'Koramangala Branch', 'Bengaluru', 'Karnataka', 'active'),
    (3, 1, 'Whitefield Branch', 'Bengaluru', 'Karnataka', 'active'),
    (4, 2, 'Koregaon Park Branch', 'Pune', 'Maharashtra', 'active'),
    (5, 2, 'FC Road Branch', 'Pune', 'Maharashtra', 'active'),
    (6, 3, 'Bandra Main Branch', 'Mumbai', 'Maharashtra', 'active')
    ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status);
");

// 4. Users matching Figma
$passwordHash = password_hash('Secret@123', PASSWORD_BCRYPT);
$db->exec("
    INSERT INTO users (id, role_id, restaurant_id, name, email, password, status, created_at) VALUES
    (1, 1, NULL, 'Mira Shah', 'mira@healthybite.in', '{$passwordHash}', 'active', '2025-01-04 10:00:00'),
    (2, 2, 1, 'Aarav Sharma', 'aarav@greenhouse.in', '{$passwordHash}', 'active', '2025-01-12 10:00:00'),
    (3, 3, 1, 'Meera Nair', 'meera@greenhouse.in', '{$passwordHash}', 'active', '2025-03-05 10:00:00'),
    (4, 4, 1, 'Rohan Patel', 'rohan@greenhouse.in', '{$passwordHash}', 'inactive', '2025-06-21 10:00:00'),
    (5, 2, 2, 'Neha Kapoor', 'neha@greenearth.in', '{$passwordHash}', 'active', '2025-09-14 10:00:00'),
    (6, 2, 3, 'Vikram Iyer', 'vikram@puregreen.in', '{$passwordHash}', 'active', '2025-09-13 10:00:00')
    ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email), role_id = VALUES(role_id), restaurant_id = VALUES(restaurant_id);
");

$db->exec("UPDATE restaurants SET owner_user_id = 2 WHERE id = 1;");
$db->exec("UPDATE restaurants SET owner_user_id = 5 WHERE id = 2;");
$db->exec("UPDATE restaurants SET owner_user_id = 6 WHERE id = 3;");

// 5. Additional Tables for Greenhouse Kitchen (12 tables)
for ($i = 1; $i <= 12; $i++) {
    $tableNum = "Table {$i}";
    $status = in_array($i, [1, 5, 9]) ? 'occupied' : 'available';
    $stmt = $db->prepare("
        INSERT INTO restaurant_tables (restaurant_id, branch_id, table_number, status)
        VALUES (1, 1, :tbl, :st)
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    $stmt->execute([':tbl' => $tableNum, ':st' => $status]);
    $tblId = $db->lastInsertId() ?: $i;

    // QR token
    $token = "hb_qr_token_indiranagar_tbl{$i}_" . substr(md5("tbl{$i}"), 0, 6);
    $stmtQr = $db->prepare("
        INSERT INTO qr_tokens (restaurant_id, branch_id, table_id, token, status)
        VALUES (1, 1, :tbl_id, :token, 'active')
        ON DUPLICATE KEY UPDATE status = 'active'
    ");
    $stmtQr->execute([':tbl_id' => $tblId, ':token' => $token]);
}

// 6. Customers matching Figma
$db->exec("
    INSERT INTO customers (id, name, mobile, email) VALUES
    (1, 'Gunjan Dhaduk', '+91 98765 11001', 'gunjan@example.com'),
    (2, 'Riya Mehta', '+91 98765 11002', 'riya@example.com'),
    (3, 'Kabir Rao', '+91 98765 11003', 'kabir@example.com'),
    (4, 'Ananya Sen', '+91 98765 11004', 'ananya@example.com')
    ON DUPLICATE KEY UPDATE name = VALUES(name);
");

// 7. Reviews
$db->exec("
    INSERT INTO reviews (id, restaurant_id, customer_id, rating, comment, status, created_at) VALUES
    (1, 1, 1, 5, 'The food was fresh and delicious.', 'approved', '2026-09-15 11:20:00'),
    (2, 1, 2, 5, 'Healthy, filling and delivered right on time.', 'approved', '2026-09-15 11:00:00'),
    (3, 1, 3, 5, 'Healthy, filling and delivered right on time.', 'approved', '2026-09-15 10:45:00'),
    (4, 1, 4, 4, 'Great macros, perfectly balanced bowl.', 'approved', '2026-09-14 16:30:00'),
    (5, 1, 1, 5, 'Amazing avocado and grilled protein bowl!', 'approved', '2026-09-14 12:15:00')
    ON DUPLICATE KEY UPDATE comment = VALUES(comment), rating = VALUES(rating);
");

// 8. Orders for Greenhouse Kitchen representing each status in Kanban
$ordersData = [
    [
        'order_number' => 'HB-1001',
        'customer_id'  => 1,
        'table_id'     => 4, // Table 12
        'order_type'   => 'dine_in',
        'subtotal'     => 502.86,
        'tax'          => 25.14,
        'total_amount' => 528.00,
        'payment_status' => 'completed',
        'order_status'   => 'placed',
        'notes'          => 'Special instruction: Less spicy',
        'time'           => date('Y-m-d 10:28:00')
    ],
    [
        'order_number' => 'HB-1002',
        'customer_id'  => 2,
        'table_id'     => null,
        'order_type'   => 'takeaway',
        'subtotal'     => 366.67,
        'tax'          => 18.33,
        'total_amount' => 385.00,
        'payment_status' => 'completed',
        'order_status'   => 'accepted',
        'notes'          => 'Special instruction: Less spicy',
        'time'           => date('Y-m-d 10:24:00')
    ],
    [
        'order_number' => 'HB-1003',
        'customer_id'  => 3,
        'table_id'     => 11, // Table 4
        'order_type'   => 'dine_in',
        'subtotal'     => 706.67,
        'tax'          => 35.33,
        'total_amount' => 742.00,
        'payment_status' => 'completed',
        'order_status'   => 'preparing',
        'notes'          => 'Special instruction: Less spicy',
        'time'           => date('Y-m-d 10:04:00')
    ],
    [
        'order_number' => 'HB-1004',
        'customer_id'  => 4,
        'table_id'     => 15, // Table 8
        'order_type'   => 'dine_in',
        'subtotal'     => 281.90,
        'tax'          => 14.10,
        'total_amount' => 296.00,
        'payment_status' => 'completed',
        'order_status'   => 'ready',
        'notes'          => 'Special instruction: Extra dressing on side',
        'time'           => date('Y-m-d 09:52:00')
    ],
    [
        'order_number' => 'HB-1005',
        'customer_id'  => 1,
        'table_id'     => 1, // Table 1
        'order_type'   => 'dine_in',
        'subtotal'     => 502.86,
        'tax'          => 25.14,
        'total_amount' => 528.00,
        'payment_status' => 'completed',
        'order_status'   => 'completed',
        'notes'          => 'Special instruction: Less spicy',
        'time'           => date('Y-m-d 09:30:00')
    ]
];

foreach ($ordersData as $od) {
    $stmt = $db->prepare("
        INSERT INTO orders (
            order_number, restaurant_id, branch_id, table_id, customer_id,
            order_type, subtotal, tax, service_charge, total_amount,
            payment_status, order_status, notes, created_at
        ) VALUES (
            :num, 1, 1, :tbl, :cust, :otype, :sub, :tax, 0.00, :tot, :pstat, :ostat, :notes, :created
        )
        ON DUPLICATE KEY UPDATE
            table_id = VALUES(table_id),
            order_status = VALUES(order_status),
            total_amount = VALUES(total_amount),
            notes = VALUES(notes);
    ");
    $stmt->execute([
        ':num'    => $od['order_number'],
        ':tbl'    => $od['table_id'],
        ':cust'   => $od['customer_id'],
        ':otype'  => $od['order_type'],
        ':sub'    => $od['subtotal'],
        ':tax'    => $od['tax'],
        ':tot'    => $od['total_amount'],
        ':pstat'  => $od['payment_status'],
        ':ostat'  => $od['order_status'],
        ':notes'  => $od['notes'],
        ':created'=> $od['time']
    ]);

    $orderId = $db->lastInsertId();
    if (!$orderId) {
        $q = $db->prepare("SELECT id FROM orders WHERE order_number = ?");
        $q->execute([$od['order_number']]);
        $orderId = $q->fetchColumn();
    }

    if ($orderId) {
        // Ensure items exist
        $itemCount = $db->query("SELECT COUNT(*) FROM order_items WHERE order_id = {$orderId}")->fetchColumn();
        if ($itemCount == 0) {
            $db->exec("
                INSERT INTO order_items (order_id, food_item_id, food_name_snapshot, base_price_snapshot, quantity, unit_price, total_price, calories, protein, carbs, fat) VALUES
                ({$orderId}, 2, 'Chicken Rice Bowl', 279.00, 1, 279.00, 279.00, 560, 44.00, 50.00, 14.00),
                ({$orderId}, 1, 'Paneer Protein Bowl', 249.00, 1, 249.00, 249.00, 520, 38.00, 48.00, 18.00);
            ");
            $firstItemId = $db->lastInsertId();
            if ($firstItemId) {
                $custId = $db->query("SELECT id FROM food_customizations LIMIT 1")->fetchColumn() ?: 11;
                $db->exec("
                    INSERT INTO order_item_customizations (order_item_id, customization_id, customization_name_snapshot, quantity, price_adjustment) VALUES
                    ({$firstItemId}, {$custId}, 'Regular Chicken · Brown Rice · Extra Vegetables', 1, 20.00);
                ");
            }
        }

        // Ensure payments exist
        $db->exec("
            INSERT IGNORE INTO payments (order_id, payment_method, amount, status, paid_at)
            VALUES ({$orderId}, 'upi', {$od['total_amount']}, 'completed', '{$od['time']}');
        ");
    }
}

echo "Dashboard seeding completed successfully!\n";
