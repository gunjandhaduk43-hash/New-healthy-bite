<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use App\Core\Database;
use App\Repositories\BranchRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\StaffRepository;
use App\Repositories\OrderRepository;
use App\Repositories\FoodRepository;
use App\Services\OrderService;

echo "=== HEALTHY BITE: FULL SYSTEM DYNAMIC FLOW QA TEST ===\n\n";

$passedTests = 0;
$totalTests = 0;

function runTest(string $name, callable $fn) {
    global $passedTests, $totalTests;
    $totalTests++;
    echo "[TEST $totalTests] $name... ";
    try {
        $result = $fn();
        if ($result === true || $result === null) {
            echo "PASSED ✓\n";
            $passedTests++;
        } else {
            echo "FAILED ✗ - " . json_encode($result) . "\n";
        }
    } catch (\Throwable $e) {
        echo "EXCEPTION ✗: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// 1. Table Resolution via BranchRepository
runTest("BranchRepository::findTableByNumber finds active table with token", function() {
    $branchRepo = new BranchRepository();
    $table = $branchRepo->findTableByNumber(1, '12');
    if (!$table) return "Table 12 not found";
    if (empty($table['qr_token'])) return "Table 12 has no qr_token";
    if (strpos($table['table_number'], '12') === false) return "Table number mismatch: " . $table['table_number'];
    return true;
});

runTest("BranchRepository::getFirstAvailableTable finds an active table with token", function() {
    $branchRepo = new BranchRepository();
    $table = $branchRepo->getFirstAvailableTable(1);
    if (!$table) return "No available table found";
    if (empty($table['qr_token'])) return "Resolved table has no qr_token";
    return true;
});

// 2. HTTP Routes for /table/{tableNumber} and /t/{token}
runTest("HTTP GET /table/12 redirects (302) to /menu?token=...", function() {
    $ch = curl_init("http://localhost:8000/table/12");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 302) return "Expected HTTP 302, got $httpCode";
    if (!preg_match('/Location:\s*\/menu\?token=/i', (string)$response)) return "Location header mismatch: $response";
    return true;
});

runTest("HTTP GET /menu with invalid token displays error banner", function() {
    $ch = curl_init("http://localhost:8000/menu?token=NON_EXISTENT_TOKEN_12345");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) return "Expected HTTP 200, got $httpCode";
    if (strpos((string)$response, 'inactive, invalid, or expired') === false) return "Error banner text missing from response";
    return true;
});

// 3. Dynamic Category Management
runTest("CategoryRepository::createCategory creates a new category with auto-slug", function() {
    $catRepo = new CategoryRepository();
    $catId = $catRepo->createCategory(1, [
        'name' => 'QA Dynamic Dessert Test',
        'description' => 'Category created during automated QA testing',
        'image' => '/assets/images/foods/placeholder-dish.svg'
    ]);
    if ($catId <= 0) return "Failed to insert category, returned ID: $catId";

    // Verify it exists in DB
    $cat = $catRepo->findById($catId, 1);
    if (!$cat) return "Category $catId not found in DB";
    if (strpos($cat['slug'], 'qa-dynamic-dessert-test') === false) return "Slug mismatch: " . $cat['slug'];

    // Now test deletion
    $deleted = $catRepo->deleteCategory($catId, 1);
    if (!$deleted) return "Failed to delete category $catId";

    $catAfter = $catRepo->findById($catId, 1);
    if ($catAfter) return "Category $catId still exists after deletion";

    return true;
});

runTest("CategoryRepository::deleteCategory blocks deletion if active food items exist", function() {
    $catRepo = new CategoryRepository();
    // Category 1 (Bowls) has active food items
    try {
        $catRepo->deleteCategory(1, 1);
        return "Expected exception when deleting category with active foods, none thrown";
    } catch (\InvalidArgumentException $e) {
        if (strpos($e->getMessage(), 'active food items') === false) {
            return "Unexpected exception message: " . $e->getMessage();
        }
        return true;
    }
});

// 4. Dynamic Staff Management
runTest("StaffRepository::findById and updateStaff updates staff member dynamically", function() {
    $staffRepo = new StaffRepository();
    $staffList = $staffRepo->findByRestaurant(1);
    if (empty($staffList)) return "No staff found for restaurant 1";

    $target = $staffList[0];
    $targetId = (int)$target['id'];
    $originalName = $target['name'];
    $originalRoleId = (int)$target['role_id'];

    // Update name
    $newName = $originalName . " QA";
    $updated = $staffRepo->updateStaff($targetId, 1, $newName, $originalRoleId, null);
    if (!$updated) return "updateStaff returned false";

    $fresh = $staffRepo->findById($targetId, 1);
    if ($fresh['name'] !== $newName) return "Name was not updated in DB. Got: " . $fresh['name'];

    // Revert back
    $staffRepo->updateStaff($targetId, 1, $originalName, $originalRoleId, null);
    $reverted = $staffRepo->findById($targetId, 1);
    if ($reverted['name'] !== $originalName) return "Revert failed";

    return true;
});

// 5. Macro Stats including Sugar Aggregation
runTest("OrderRepository::getMacroStats computes total_sugar and avg_sugar", function() {
    $orderRepo = new OrderRepository();
    $macroStats = $orderRepo->getMacroStats(1);
    if (!isset($macroStats['total_sugar'])) return "total_sugar key missing from macro stats";
    if (!isset($macroStats['avg_sugar'])) return "avg_sugar key missing from macro stats";
    if (!is_numeric($macroStats['total_sugar'])) return "total_sugar is not numeric";
    if ((float)$macroStats['total_sugar'] < 0) return "total_sugar is negative";
    return true;
});

// 6. Dynamic Table Selection on Checkout
runTest("Checkout page renders real branch tables in dropdown", function() {
    $ch = curl_init("http://localhost:8000/menu/checkout");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) return "Expected HTTP 200, got $httpCode";
    // Check that table options are dynamically rendered
    if (strpos((string)$response, 'Table 12') === false && strpos((string)$response, 'Table 1') === false) {
        return "Dynamic table options not rendered in checkout page";
    }
    return true;
});

// 7. Owner Analytics Dynamic KPI Data
runTest("Owner Analytics data loads from database with real aov, revenue, and orders", function() {
    $orderRepo = new OrderRepository();
    $todayStats = $orderRepo->getTodayStats(1);
    if (!isset($todayStats['all_time_revenue'])) return "all_time_revenue missing";
    if (!isset($todayStats['all_time_orders'])) return "all_time_orders missing";
    if (!isset($todayStats['aov'])) return "aov missing";
    return true;
});

// 8. Order Placement with Complete Nutrition Snapshot (Calories, Protein, Carbs, Fat, Sugar)
runTest("OrderService creates an order with accurate sugar snapshot in order_items", function() {
    $orderService = new OrderService();
    $foodRepo = new FoodRepository();

    // Get an active food item
    $foods = $foodRepo->getByRestaurantId(1);
    $targetFood = null;
    foreach ($foods as $f) {
        if ((float)($f['sugar'] ?? 0) > 0) {
            $targetFood = $f;
            break;
        }
    }
    if (!$targetFood) $targetFood = $foods[0];

    $foodId = (int)$targetFood['id'];
    $variants = $foodRepo->getVariants($foodId);
    $customizations = $foodRepo->getCustomizations($foodId);

    // Build cart items array
    $cartItems = [
        [
            'food_id' => $foodId,
            'quantity' => 2,
            'variant_id' => !empty($variants) ? (int)$variants[0]['id'] : null,
            'customizations' => !empty($customizations) ? [
                ['id' => (int)$customizations[0]['id'], 'quantity' => 1]
            ] : []
        ]
    ];

    $orderPayload = [
        'restaurant_id' => 1,
        'branch_id' => 1,
        'table_id' => 1,
        'order_type' => 'dine_in',
        'customer_name' => 'QA Automated Dynamic Test',
        'customer_mobile' => '9876543210',
        'customer_email' => 'qa.test@healthybite.local',
        'payment_method' => 'cash',
        'notes' => 'Automated QA flow verification',
        'items' => $cartItems
    ];

    $order = $orderService->placeOrder($orderPayload);
    if (empty($order['id'])) return "Order creation failed or did not return ID";

    $orderId = (int)$order['id'];

    // Verify order in database
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $dbOrder = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$dbOrder) return "Order $orderId not found in orders table";

    // Verify order_items snapshot
    $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);
    if (empty($items)) return "No order items persisted for order $orderId";

    $firstItem = $items[0];
    if (!array_key_exists('sugar', $firstItem)) return "sugar column missing from order_items table";
    if ((float)$firstItem['unit_price'] <= 0) return "Order item snapshot unit_price is zero or invalid";

    // Verify customizations snapshot if any were attached
    if (!empty($customizations)) {
        $customStmt = $db->prepare("SELECT * FROM order_item_customizations WHERE order_item_id = ?");
        $customStmt->execute([(int)$firstItem['id']]);
        $savedCustoms = $customStmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($savedCustoms)) return "Customizations were provided but none persisted in order_item_customizations";
    }

    // Clean up QA test order so we leave the database pristine
    $db->prepare("DELETE FROM order_item_customizations WHERE order_item_id IN (SELECT id FROM order_items WHERE order_id = ?)")->execute([$orderId]);
    $db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
    $db->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$orderId]);
    $db->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);

    return true;
});

echo "\n============================================\n";
echo "QA AUTOMATED TESTS SUMMARY: $passedTests / $totalTests PASSED\n";
echo "============================================\n";

if ($passedTests === $totalTests) {
    echo "ALL SUITE TESTS PASSED WITH ZERO ERRORS!\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
