<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__));

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = HB_ROOT . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

\App\Core\Env::load(HB_ROOT . '/.env');

use App\Services\OrderService;
use App\Services\CartService;
use App\Core\Database;

$pdo = Database::getConnection();
$orderService = new OrderService();
$cartService = new CartService();

echo "=== STARTING END-TO-END ORDER SUGAR SNAPSHOT TEST ===\n\n";

// 1. Food item #13 (Paneer Steak with Quinoa & Greens)
$foodStmt = $pdo->prepare("SELECT * FROM food_items WHERE id = 13");
$foodStmt->execute();
$food = $foodStmt->fetch(PDO::FETCH_ASSOC);

echo "Base Food: {$food['name']} (ID: {$food['id']})\n";
echo "Base Sugar: {$food['sugar']} g\n";

// 2. Fetch Large Variant for food #13
$varStmt = $pdo->prepare("SELECT * FROM food_variants WHERE food_item_id = 13 AND sugar_adjustment > 0 LIMIT 1");
$varStmt->execute();
$selectedVariant = $varStmt->fetch(PDO::FETCH_ASSOC);

echo "Selected Variant: {$selectedVariant['name']} (ID: {$selectedVariant['id']})\n";
echo "Variant Sugar Adjustment: +{$selectedVariant['sugar_adjustment']} g\n";

// 3. Fetch customizations for food #13
$custStmt = $pdo->prepare("SELECT * FROM food_customizations WHERE food_item_id = 13 AND sugar_adjustment > 0");
$custStmt->execute();
$customizations = $custStmt->fetchAll(PDO::FETCH_ASSOC);

$selectedCustomizations = [];
$expectedCustomSugarSum = 0.0;

foreach ($customizations as $c) {
    echo "Customization Available: {$c['name']} (ID: {$c['id']}) -> Sugar Adj: +{$c['sugar_adjustment']} g\n";
    if (count($selectedCustomizations) < 2) {
        $selectedCustomizations[] = [
            'id' => (int)$c['id'],
            'quantity' => 1,
            'name' => $c['name'],
            'sugar_adj' => (float)$c['sugar_adjustment']
        ];
        $expectedCustomSugarSum += (float)$c['sugar_adjustment'];
    }
}

$expectedPerItemSugar = (float)$food['sugar'] + (float)$selectedVariant['sugar_adjustment'] + $expectedCustomSugarSum;
$quantity = 3;
$expectedTotalSugar = $expectedPerItemSugar * $quantity;

echo "\n--- EXPECTED NUTRITION CALCULATIONS ---\n";
echo "Per-item Sugar = {$food['sugar']} + {$selectedVariant['sugar_adjustment']} + {$expectedCustomSugarSum} = {$expectedPerItemSugar} g\n";
echo "Quantity = {$quantity}\n";
echo "Line Total Sugar = {$expectedPerItemSugar} * {$quantity} = {$expectedTotalSugar} g\n\n";

// 4. Test CartService validation & nutrition calculation
$cartPayload = [
    [
        'food_id' => 13,
        'variant_id' => (int)$selectedVariant['id'],
        'quantity' => $quantity,
        'customizations' => array_map(fn($c) => ['id' => $c['id'], 'quantity' => 1], $selectedCustomizations),
    ]
];

$validated = $cartService->validateCartItems($cartPayload, 1);
$itemNutr = $validated['items'][0]['nutrition'];
$scaledNutr = $validated['items'][0]['scaled_nutrition'];
$cartTotalNutr = $validated['total_nutrition'];

echo "--- CART SERVICE VALIDATION RESULT ---\n";
echo "Item Calculated Sugar: {$itemNutr['sugar']} g (Expected: {$expectedPerItemSugar} g)\n";
echo "Scaled Item Sugar: {$scaledNutr['sugar']} g (Expected: {$expectedTotalSugar} g)\n";
echo "Cart Total Sugar: {$cartTotalNutr['sugar']} g (Expected: {$expectedTotalSugar} g)\n";

if (abs((float)$itemNutr['sugar'] - (float)$expectedPerItemSugar) > 0.001) {
    throw new Exception("Cart item sugar mismatch!");
}
if (abs((float)$scaledNutr['sugar'] - (float)$expectedTotalSugar) > 0.001) {
    throw new Exception("Scaled item sugar mismatch!");
}
if (abs((float)$cartTotalNutr['sugar'] - (float)$expectedTotalSugar) > 0.001) {
    throw new Exception("Cart total sugar mismatch!");
}
echo "✓ CartService validation & calculations: 100% MATCH!\n\n";

// 5. Place Order and inspect frozen database snapshot
echo "--- PLACING ORDER AND CHECKING FROZEN SNAPSHOT ---\n";
$orderPayload = [
    'restaurant_id' => 1,
    'branch_id' => 1,
    'order_type' => 'dine_in',
    'table_id' => 1,
    'customer_name' => 'QA Sugar Tester',
    'customer_mobile' => '9876543210',
    'items' => $cartPayload,
    'notes' => 'Testing sugar snapshot persistence'
];

$order = $orderService->placeOrder($orderPayload);
echo "Order Placed Successfully! Order Number: {$order['order_number']} (ID: {$order['id']})\n";

// Check order_items table
$itemCheckStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemCheckStmt->execute([$order['id']]);
$savedItems = $itemCheckStmt->fetchAll(PDO::FETCH_ASSOC);

echo "Order Items Count: " . count($savedItems) . "\n";
foreach ($savedItems as $savedItem) {
    echo "  OrderItem ID: {$savedItem['id']} | Food: {$savedItem['food_name_snapshot']}\n";
    echo "  Sugar Snapshot in order_items: {$savedItem['sugar']} g\n";
    if (abs((float)$savedItem['sugar'] - (float)$expectedPerItemSugar) > 0.001) {
        throw new Exception("order_items.sugar snapshot mismatch!");
    }

    // Check order_item_customizations table
    $custCheckStmt = $pdo->prepare("SELECT * FROM order_item_customizations WHERE order_item_id = ?");
    $custCheckStmt->execute([$savedItem['id']]);
    $savedCusts = $custCheckStmt->fetchAll(PDO::FETCH_ASSOC);

    echo "  Saved Customizations Count: " . count($savedCusts) . "\n";
    foreach ($savedCusts as $sc) {
        echo "    - {$sc['customization_name_snapshot']} (Qty: {$sc['quantity']}): Sugar Adj = {$sc['sugar_adjustment']} g\n";
        if ($sc['sugar_adjustment'] === null) {
            throw new Exception("Customization sugar adjustment should not be null!");
        }
    }
}

echo "\n=== TESTING STRICT NULL SUGAR PRESERVATION ===\n";
// Check if there is an item with NULL sugar, or temporarily test one
$nullFoodStmt = $pdo->query("SELECT id, name, sugar FROM food_items WHERE sugar IS NULL LIMIT 1");
$nullFood = $nullFoodStmt->fetch(PDO::FETCH_ASSOC);

if (!$nullFood) {
    // Create a temporary unmeasured dish
    $pdo->exec("INSERT INTO food_items (restaurant_id, category_id, name, slug, description, base_price, sugar, is_available) 
                VALUES (1, 1, 'Unmeasured Chef Special', 'unmeasured-chef-special-" . time() . "', 'Test unmeasured nutrition', 250.00, NULL, 1)");
    $nullFoodId = (int)$pdo->lastInsertId();
    $nullFood = $pdo->query("SELECT id, name, sugar FROM food_items WHERE id = {$nullFoodId}")->fetch(PDO::FETCH_ASSOC);
    $createdTemp = true;
} else {
    $createdTemp = false;
}

echo "Testing Food with NULL Sugar: {$nullFood['name']} (ID: {$nullFood['id']})\n";
assert($nullFood['sugar'] === null, "Food sugar must be strictly NULL!");

$nullCartPayload = [
    [
        'food_id' => (int)$nullFood['id'],
        'quantity' => 2,
    ]
];

$nullValidated = $cartService->validateCartItems($nullCartPayload, 1);
$nullItemNutr = $nullValidated['items'][0]['nutrition'];
$nullScaledNutr = $nullValidated['items'][0]['scaled_nutrition'];
echo "Validated Nutrition Sugar: " . var_export($nullItemNutr['sugar'], true) . "\n";
echo "Scaled Nutrition Sugar: " . var_export($nullScaledNutr['sugar'], true) . "\n";

if ($nullItemNutr['sugar'] !== null) {
    throw new Exception("Expected nutrition sugar to be NULL, got: " . var_export($nullItemNutr['sugar'], true));
}
if ($nullScaledNutr['sugar'] !== null) {
    throw new Exception("Expected scaled nutrition sugar to be NULL, got: " . var_export($nullScaledNutr['sugar'], true));
}

// Place order with NULL sugar food
$nullOrderPayload = [
    'restaurant_id' => 1,
    'branch_id' => 1,
    'order_type' => 'dine_in',
    'table_id' => 1,
    'customer_name' => 'QA Null Sugar Tester',
    'customer_mobile' => '9876543210',
    'items' => $nullCartPayload,
];
$nullOrder = $orderService->placeOrder($nullOrderPayload);

$nullItemCheck = $pdo->prepare("SELECT id, food_name_snapshot, sugar FROM order_items WHERE order_id = ?");
$nullItemCheck->execute([$nullOrder['id']]);
$savedNullItem = $nullItemCheck->fetch(PDO::FETCH_ASSOC);

echo "Order Item Snapshot Sugar: " . var_export($savedNullItem['sugar'], true) . "\n";
if ($savedNullItem['sugar'] !== null) {
    throw new Exception("Expected order_items.sugar in MariaDB to be NULL, got: " . var_export($savedNullItem['sugar'], true));
}

echo "✓ Strict NULL Preservation Confirmed: NULL sugar is NEVER coerced to 0 in database or calculations!\n";

if (!empty($createdTemp)) {
    // Clean up temporary test item and its order
    $pdo->exec("DELETE FROM order_items WHERE order_id = {$nullOrder['id']}");
    $pdo->exec("DELETE FROM orders WHERE id = {$nullOrder['id']}");
    $pdo->exec("DELETE FROM food_items WHERE id = {$nullFood['id']}");
    echo "Cleaned up temporary test dish.\n";
}

echo "\n✓ 100% SUCCESS: All automated backend and snapshot tests PASSED!\n";

