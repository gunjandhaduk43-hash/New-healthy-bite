<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$jsonFile = __DIR__ . '/../storage/basil_valley_menu_import.json';
if (!file_exists($jsonFile)) {
    die("Error: JSON file not found at {$jsonFile}\n");
}

$data = json_decode(file_get_contents($jsonFile), true);
if (!$data || !isset($data['restaurant'])) {
    die("Error: Invalid JSON format.\n");
}

$restaurantData = $data['restaurant'];
$db = Database::getConnection();

try {
    $db->beginTransaction();

    // 1. Ensure Restaurant Exists
    $stmt = $db->prepare("SELECT id FROM restaurants WHERE slug = ? OR id = 4");
    $stmt->execute([$restaurantData['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $restaurantId = (int)$existing['id'];
        $updateRest = $db->prepare("
            UPDATE restaurants 
            SET name = ?, description = ?, city = ?, state = ?, status = 'approved'
            WHERE id = ?
        ");
        $updateRest->execute([
            $restaurantData['name'],
            $restaurantData['tagline'] ?? 'The Ultimate Café Place!',
            $restaurantData['city'] ?? 'Rajkot',
            $restaurantData['state'] ?? 'Gujarat',
            $restaurantId
        ]);
        echo "Updated existing restaurant ID: {$restaurantId} ({$restaurantData['name']})\n";
    } else {
        $insertRest = $db->prepare("
            INSERT INTO restaurants (id, name, slug, logo, cover_image, description, phone, email, address, city, state, status)
            VALUES (4, ?, ?, 'basil_valley_logo.png', 'basil_valley_cover.jpg', ?, '+919876543210', 'contact@basilvalley.com', 'Kalawad Road / University Road', 'Rajkot', 'Gujarat', 'approved')
        ");
        $insertRest->execute([
            $restaurantData['name'],
            $restaurantData['id'],
            $restaurantData['tagline'] ?? 'The Ultimate Café Place!'
        ]);
        $restaurantId = 4;
        echo "Inserted new restaurant ID: 4 ({$restaurantData['name']})\n";
    }

    // 1b. Ensure Branch exists
    $stmtBranch = $db->prepare("SELECT id FROM branches WHERE restaurant_id = ?");
    $stmtBranch->execute([$restaurantId]);
    $branch = $stmtBranch->fetch(PDO::FETCH_ASSOC);
    if (!$branch) {
        $insertBranch = $db->prepare("
            INSERT INTO branches (restaurant_id, name, address, city, state, phone, status)
            VALUES (?, 'Basil Valley — Kalawad Road', 'Kalawad Road, Near Kotecha Chowk', 'Rajkot', 'Gujarat', '+919876543210', 'active')
        ");
        $insertBranch->execute([$restaurantId]);
        $branchId = (int)$db->lastInsertId();
        echo "Created branch ID {$branchId} for Basil Valley\n";
    } else {
        $branchId = (int)$branch['id'];
    }

    // 1c. Ensure Tables 1-10 exist for Basil Valley
    for ($t = 1; $t <= 10; $t++) {
        $tNum = "T-" . str_pad((string)$t, 2, '0', STR_PAD_LEFT);
        $checkT = $db->prepare("SELECT id FROM restaurant_tables WHERE restaurant_id = ? AND table_number = ?");
        $checkT->execute([$restaurantId, $tNum]);
        if (!$checkT->fetch()) {
            $insertT = $db->prepare("INSERT INTO restaurant_tables (restaurant_id, branch_id, table_number, status) VALUES (?, ?, ?, 'available')");
            $insertT->execute([$restaurantId, $branchId, $tNum]);
        }
    }

    // 2. Insert or update Categories and Items
    $catOrder = 1;
    $totalCategories = 0;
    $totalItems = 0;

    foreach ($restaurantData['categories'] as $cat) {
        // Find or create category
        $stmtCat = $db->prepare("SELECT id FROM categories WHERE restaurant_id = ? AND slug = ?");
        $stmtCat->execute([$restaurantId, $cat['slug']]);
        $catRow = $stmtCat->fetch(PDO::FETCH_ASSOC);

        if ($catRow) {
            $catId = (int)$catRow['id'];
            $updateCat = $db->prepare("UPDATE categories SET name = ?, description = ?, sort_order = ?, status = 'active' WHERE id = ?");
            $updateCat->execute([$cat['name'], $cat['description'] ?? null, $catOrder, $catId]);
        } else {
            $insertCat = $db->prepare("
                INSERT INTO categories (restaurant_id, name, slug, description, image, sort_order, status)
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");
            $insertCat->execute([
                $restaurantId,
                $cat['name'],
                $cat['slug'],
                $cat['description'] ?? null,
                "cat_{$cat['slug']}.jpg",
                $catOrder
            ]);
            $catId = (int)$db->lastInsertId();
        }
        $catOrder++;
        $totalCategories++;

        // Process items for this category
        foreach ($cat['items'] as $item) {
            $foodType = 'vegetarian';
            if (isset($item['dietary']) && in_array('vegan', $item['dietary'])) {
                $foodType = 'vegan';
            } elseif (isset($item['dietary']) && in_array('jain_available', $item['dietary'])) {
                $foodType = 'jain';
            }

            $stmtItem = $db->prepare("SELECT id FROM food_items WHERE restaurant_id = ? AND slug = ?");
            $stmtItem->execute([$restaurantId, $item['id']]);
            $itemRow = $stmtItem->fetch(PDO::FETCH_ASSOC);

            if ($itemRow) {
                $updateItem = $db->prepare("
                    UPDATE food_items 
                    SET category_id = ?, name = ?, description = ?, base_price = ?, food_type = ?, is_available = 1
                    WHERE id = ?
                ");
                $updateItem->execute([
                    $catId,
                    $item['name'],
                    $item['description'],
                    $item['price'],
                    $foodType,
                    $itemRow['id']
                ]);
            } else {
                $insertItem = $db->prepare("
                    INSERT INTO food_items (restaurant_id, category_id, name, slug, description, image, food_type, base_price, is_available)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                ");
                $insertItem->execute([
                    $restaurantId,
                    $catId,
                    $item['name'],
                    $item['id'],
                    $item['description'],
                    $item['image'],
                    $foodType,
                    $item['price']
                ]);
            }
            $totalItems++;
        }
    }

    $db->commit();
    echo "SUCCESS: Imported {$totalCategories} categories and {$totalItems} menu items for {$restaurantData['name']} (ID: {$restaurantId})!\n";
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR during import: " . $e->getMessage() . "\n";
    exit(1);
}
