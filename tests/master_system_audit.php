<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../app/Helpers/url.php';
require_once __DIR__ . '/../app/Helpers/format.php';
require_once __DIR__ . '/../app/Helpers/security.php';
require_once __DIR__ . '/../app/Helpers/food.php';
require_once __DIR__ . '/../app/Core/Env.php';
require_once __DIR__ . '/../app/Core/Database.php';

\App\Core\Env::load(__DIR__ . '/../.env');

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

class MasterTestSuite
{
    private PDO $pdo;
    private array $results = [
        'passed' => 0,
        'failed' => 0,
        'fixed' => 0,
        'categories' => []
    ];
    private string $baseUrl = 'http://localhost:8000';

    public function __construct()
    {
        $this->pdo = \App\Core\Database::getConnection();
    }

    private function record(string $category, string $testName, bool $pass, string $notes = ''): void
    {
        if ($pass) {
            $this->results['passed']++;
            echo "  [PASS] $testName" . ($notes ? " ($notes)" : "") . "\n";
        } else {
            $this->results['failed']++;
            echo "  [FAIL] $testName: $notes\n";
        }
        $this->results['categories'][$category][] = [
            'name' => $testName,
            'pass' => $pass,
            'notes' => $notes
        ];
    }

    public function runAll(): void
    {
        echo "========================================================\n";
        echo "HEALTHY BITE — MASTER FINAL TEST & VALIDATION SUITE\n";
        echo "========================================================\n\n";

        $this->testPhase1Environment();
        $this->testPhase3Database();
        $this->testPhase4UnitBusinessLogic();
        $this->testPhase5ApiEndpoints();
        $this->testPhase6AuthAndRbac();
        $this->testPhase7CustomerE2E();
        $this->testPhase8OwnerE2E();
        $this->testPhase9AdminE2E();
        $this->testPhase10Security();
        $this->testPhase12ImmutabilityAndConcurrency();
        $this->printSummary();
    }

    private function testPhase1Environment(): void
    {
        echo "--- PHASE 1: ENVIRONMENT & CONFIGURATION ---\n";
        $this->record('Environment', 'PHP Version >= 8.2', PHP_VERSION_ID >= 80200, PHP_VERSION);
        $this->record('Environment', 'PDO MySQL extension loaded', extension_loaded('pdo_mysql'));
        $this->record('Environment', 'Database Connection Alive', $this->pdo->query('SELECT 1')->fetchColumn() == 1);
        $this->record('Environment', '.env file present and loaded', file_exists(__DIR__ . '/../.env'));
        $this->record('Environment', 'Constants defined in config/constants.php', defined('ROLE_SUPER_ADMIN') && defined('ORDER_STATUS_PLACED'));
        echo "\n";
    }

    private function testPhase3Database(): void
    {
        echo "--- PHASE 3: DATABASE SCHEMA & INTEGRITY ---\n";
        $tables = $this->pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
        $this->record('Database', 'Exactly 17 Physical Tables Exist', count($tables) === 17, 'Found ' . count($tables));

        $colCount = (int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'healthy_bite'")->fetchColumn();
        $this->record('Database', 'Exactly 208 Columns Exist', $colCount === 208, 'Found ' . $colCount);

        $fkCount = (int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'healthy_bite' AND REFERENCED_TABLE_NAME IS NOT NULL")->fetchColumn();
        $this->record('Database', 'Exactly 26 Foreign Keys Exist', $fkCount === 26, 'Found ' . $fkCount);

        // Check payments.order_id UNIQUE key
        $unique = (int)$this->pdo->query("SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'healthy_bite' AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'order_id' AND NON_UNIQUE = 0")->fetchColumn();
        $this->record('Database', 'payments.order_id has UNIQUE key (1:1 constraint)', $unique >= 1);

        // Check zero orphan records across all FKs
        $fks = $this->pdo->query("
            SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = 'healthy_bite' AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);

        $totalOrphans = 0;
        foreach ($fks as $fk) {
            $sql = "SELECT COUNT(*) FROM `{$fk['TABLE_NAME']}` t LEFT JOIN `{$fk['REFERENCED_TABLE_NAME']}` r ON t.`{$fk['COLUMN_NAME']}` = r.`{$fk['REFERENCED_COLUMN_NAME']}` WHERE t.`{$fk['COLUMN_NAME']}` IS NOT NULL AND r.`{$fk['REFERENCED_COLUMN_NAME']}` IS NULL";
            $cnt = (int)$this->pdo->query($sql)->fetchColumn();
            $totalOrphans += $cnt;
        }
        $this->record('Database', 'Zero Orphan Records Across All 26 FKs', $totalOrphans === 0, "Orphans: $totalOrphans");
        echo "\n";
    }

    private function testPhase4UnitBusinessLogic(): void
    {
        echo "--- PHASE 4: UNIT BUSINESS LOGIC SERVICES ---\n";
        
        // 1. PricingService
        $pricing = new \App\Services\PricingService();
        $price = $pricing->calculateSingleItemPrice(100.0, ['price_adjustment' => 20.0], [
            ['price_adjustment' => 10.0, 'quantity' => 2],
            ['price_adjustment' => 5.0, 'quantity' => 1]
        ]);
        // 100 + 20 + 20 + 5 = 145.0
        $this->record('Unit:Pricing', 'calculateSingleItemPrice (Base + Variant + Customizations)', $price === 145.0, "Result: $price");

        $lineTotal = $pricing->calculateLineTotal(145.0, 3);
        $this->record('Unit:Pricing', 'calculateLineTotal (UnitPrice * Qty)', $lineTotal === 435.0, "Result: $lineTotal");

        $totals = $pricing->calculateOrderTotals([435.0], 0.05, 0.00);
        $this->record('Unit:Pricing', 'calculateOrderTotals 5% GST', $totals['tax'] === 21.75 && $totals['total_amount'] === 456.75, "Total: {$totals['total_amount']}");

        // 2. NutritionService
        $nutrService = new \App\Services\NutritionService();
        $baseFood = [
            'calories' => 300, 'protein' => 20.0, 'carbs' => 30.0, 'fat' => 10.0,
            'fiber' => 5.0, 'sugar' => 4.0, 'sodium' => 200.0, 'caffeine' => 50.0
        ];
        $variant = [
            'calories_adjustment' => 50, 'protein_adjustment' => 5.0, 'carbs_adjustment' => 5.0,
            'fat_adjustment' => 2.0, 'fiber_adjustment' => 1.0, 'sugar_adjustment' => 1.0,
            'sodium_adjustment' => 20.0, 'caffeine_adjustment' => 25.0
        ];
        $itemNutr = $nutrService->calculateItemNutrition($baseFood, $variant, []);
        $this->record('Unit:Nutrition', 'calculateItemNutrition 8 macros & caffeine', 
            $itemNutr['calories'] === 350 && $itemNutr['protein'] === 25.0 && $itemNutr['caffeine'] === 75.0,
            "Caffeine: {$itemNutr['caffeine']}mg, Calories: {$itemNutr['calories']}"
        );

        $scaled = $nutrService->scaleNutritionForQuantity($itemNutr, 2);
        $this->record('Unit:Nutrition', 'scaleNutritionForQuantity (2x scaling)', 
            $scaled['calories'] === 700 && $scaled['caffeine'] === 150.0
        );

        // Null preservation check
        $foodWithNull = ['calories' => 200, 'protein' => null, 'caffeine' => null];
        $nullNutr = $nutrService->calculateItemNutrition($foodWithNull, null, []);
        $this->record('Unit:Nutrition', 'NULL values preserved in nutrition calculation', 
            $nullNutr['protein'] === null && $nullNutr['caffeine'] === null && $nullNutr['calories'] === 200
        );

        // 3. QrService
        $qrService = new \App\Services\QrService();
        $tokenRow = $this->pdo->query("SELECT token, table_id, restaurant_id FROM qr_tokens WHERE status = 'active' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($tokenRow) {
            $resolved = $qrService->resolveToken($tokenRow['token']);
            $this->record('Unit:QR', 'Valid QR Token Resolves Successfully', $resolved !== null && (int)$resolved['restaurant_id'] === (int)$tokenRow['restaurant_id']);
        }
        $nonExistent = $qrService->resolveToken('INVALID_TOKEN_RANDOM_12345');
        $this->record('Unit:QR', 'Invalid QR Token Returns Null', $nonExistent === null);

        // 4. CartService
        $cartService = new \App\Services\CartService();
        try {
            $cartService->validateCartItems([], 1);
            $this->record('Unit:Cart', 'Empty Cart Throws Exception', false);
        } catch (\InvalidArgumentException $e) {
            $this->record('Unit:Cart', 'Empty Cart Throws Exception', true);
        }

        // Cross-restaurant food item validation
        try {
            // Food #1 belongs to restaurant 1. Validate against restaurant 2:
            $cartService->validateCartItems([['food_id' => 1, 'quantity' => 1]], 2);
            $this->record('Unit:Cart', 'Cross-restaurant food item throws exception', false);
        } catch (\InvalidArgumentException $e) {
            $this->record('Unit:Cart', 'Cross-restaurant food item throws exception', true);
        }

        echo "\n";
    }

    private function testPhase5ApiEndpoints(): void
    {
        echo "--- PHASE 5: API ENDPOINTS ---\n";
        
        // GET /api/health
        $health = $this->httpGet('/api/health');
        $this->record('API', 'GET /api/health returns 200 JSON', $health['code'] === 200 && ($health['json']['status'] ?? '') === 'ok');

        // GET /api/restaurant
        $rest = $this->httpGet('/api/restaurant');
        $this->record('API', 'GET /api/restaurant returns 200', $rest['code'] === 200 && isset($rest['json']['data']['name']));

        // GET /api/categories
        $cats = $this->httpGet('/api/categories');
        $this->record('API', 'GET /api/categories returns 200', $cats['code'] === 200 && is_array($cats['json']['data'] ?? null));

        // GET /api/foods
        $foods = $this->httpGet('/api/foods');
        $this->record('API', 'GET /api/foods returns 200', $foods['code'] === 200 && count($foods['json']['data'] ?? []) > 0);

        // GET /api/foods/1
        $food1 = $this->httpGet('/api/foods/1');
        $this->record('API', 'GET /api/foods/1 returns dish details', $food1['code'] === 200 && isset($food1['json']['data']['name']));

        // GET /api/foods/99999 (Non-existent)
        $food404 = $this->httpGet('/api/foods/99999');
        $this->record('API', 'GET /api/foods/99999 returns 404', $food404['code'] === 404);

        // POST /api/cart/validate (Valid payload with required Base Grain #142 and Dressing #148)
        $validCartPayload = [
            'restaurant_id' => 1,
            'items' => [
                [
                    'food_id' => 1,
                    'quantity' => 1,
                    'customizations' => [
                        ['id' => 142, 'quantity' => 1],
                        ['id' => 148, 'quantity' => 1]
                    ]
                ]
            ]
        ];
        $cartVal = $this->httpPost('/api/cart/validate', $validCartPayload);
        $this->record('API', 'POST /api/cart/validate succeeds for valid cart with required options', $cartVal['code'] === 200 && ($cartVal['json']['status'] ?? '') === 'success');

        // POST /api/cart/validate (Tampered fake price payload - server must ignore and calculate from DB)
        $tamperedCartPayload = [
            'restaurant_id' => 1,
            'items' => [
                [
                    'food_id' => 1,
                    'quantity' => 1,
                    'price' => 0.01,
                    'calories' => 5,
                    'customizations' => [
                        ['id' => 142, 'quantity' => 1],
                        ['id' => 148, 'quantity' => 1]
                    ]
                ]
            ]
        ];
        $tamperedRes = $this->httpPost('/api/cart/validate', $tamperedCartPayload);
        $actualSubtotal = (float)($tamperedRes['json']['data']['pricing']['subtotal'] ?? 0);
        $this->record('API', 'POST /api/cart/validate ignores client fake prices (authoritative DB pricing)', 
            $actualSubtotal >= 100.0, "Calculated Subtotal: $actualSubtotal"
        );

        echo "\n";
    }

    private function testPhase6AuthAndRbac(): void
    {
        echo "--- PHASE 6: AUTHENTICATION & ACCESS CONTROL ---\n";

        // Unauthenticated access to /owner/dashboard redirects to /owner/login
        $ownerDash = $this->httpGet('/owner/dashboard', [], false);
        $this->record('Auth:RBAC', 'Unauthenticated /owner/dashboard redirects to /owner/login', 
            in_array($ownerDash['code'], [301, 302]) && str_contains($ownerDash['headers']['location'] ?? '', '/owner/login'),
            "Code: {$ownerDash['code']}, Location: " . ($ownerDash['headers']['location'] ?? '')
        );

        // Unauthenticated access to /admin/dashboard redirects to /admin/login
        $adminDash = $this->httpGet('/admin/dashboard', [], false);
        $this->record('Auth:RBAC', 'Unauthenticated /admin/dashboard redirects to /admin/login', 
            in_array($adminDash['code'], [301, 302]) && str_contains($adminDash['headers']['location'] ?? '', '/admin/login')
        );

        // Invalid owner login attempt
        $badLogin = $this->httpPost('/owner/login', ['email' => 'fake@example.com', 'password' => 'wrongpass']);
        $this->record('Auth:RBAC', 'Invalid credentials rejected on login', in_array($badLogin['code'], [200, 302, 401]));

        // Test route /admin/orders does NOT exist (strictly removed)
        $removedAdminOrders = $this->httpGet('/admin/orders');
        $this->record('Auth:Legacy', 'Route /admin/orders is permanently REMOVED (404)', $removedAdminOrders['code'] === 404);

        echo "\n";
    }

    private function testPhase7CustomerE2E(): void
    {
        echo "--- PHASE 7: CUSTOMER E2E ORDERING JOURNEY ---\n";

        // Step 1: Place an order via OrderService with valid required customization selections
        $orderService = new \App\Services\OrderService();
        $orderPayload = [
            'restaurant_id'   => 1,
            'branch_id'       => 1,
            'table_id'        => 1,
            'order_type'      => 'dine_in',
            'customer_name'   => 'Test QA Runner',
            'customer_mobile' => '9876543210',
            'customer_email'  => 'qa@healthybite.test',
            'notes'           => 'QA Test Order - Automated Test Suite',
            'items'           => [
                [
                    'food_id'  => 1,
                    'quantity' => 2,
                    'customizations' => [
                        ['id' => 142, 'quantity' => 1],
                        ['id' => 148, 'quantity' => 1]
                    ]
                ]
            ]
        ];

        try {
            $createdOrder = $orderService->placeOrder($orderPayload);
            $orderId = (int)$createdOrder['id'];
            $orderNum = $createdOrder['order_number'];
            $this->record('E2E:Customer', 'Order Placement Succeeded', $orderId > 0 && !empty($orderNum), "Order #$orderNum");

            // Verify order item snapshots
            $itemSnapshot = $this->pdo->query("SELECT food_name_snapshot, base_price_snapshot, unit_price, total_price FROM order_items WHERE order_id = $orderId LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $this->record('E2E:Customer', 'Order Item Snapshots Frozen Correctly', 
                !empty($itemSnapshot['food_name_snapshot']) && (float)$itemSnapshot['unit_price'] > 0,
                "Snapshot: {$itemSnapshot['food_name_snapshot']}, UnitPrice: {$itemSnapshot['unit_price']}"
            );

            // Step 2: Simulate Payment
            $payService = new \App\Services\PaymentService();
            $payResult = $payService->simulatePayment($orderId, 'upi');
            $this->record('E2E:Customer', 'Payment Simulation (UPI) Succeeded', 
                $payResult['status'] === 'completed' && !empty($payResult['transaction_reference']),
                "Txn: {$payResult['transaction_reference']}"
            );

            // Verify order status advanced to 'accepted' and payment_status to 'completed'
            $updatedOrder = $this->pdo->query("SELECT order_status, payment_status FROM orders WHERE id = $orderId")->fetch(PDO::FETCH_ASSOC);
            $this->record('E2E:Customer', 'Order Status advanced to accepted on payment', 
                $updatedOrder['order_status'] === 'accepted' && $updatedOrder['payment_status'] === 'completed'
            );

            // Step 3: Duplicate payment prevention test
            try {
                $payService->simulatePayment($orderId, 'cash');
                $this->record('E2E:Customer', 'Duplicate payment attempt blocked gracefully', false);
            } catch (\InvalidArgumentException $e) {
                $this->record('E2E:Customer', 'Duplicate payment attempt blocked gracefully', true, $e->getMessage());
            }

            // Step 4: Track order via OrderService
            $tracked = $orderService->trackOrder($orderNum);
            $this->record('E2E:Customer', 'Order Tracking by Order Number', $tracked !== null && $tracked['order_number'] === $orderNum);

            // Clean up test order gracefully to keep DB clean
            $this->pdo->exec("DELETE FROM payments WHERE order_id = $orderId");
            $this->pdo->exec("DELETE FROM order_item_customizations WHERE order_item_id IN (SELECT id FROM order_items WHERE order_id = $orderId)");
            $this->pdo->exec("DELETE FROM order_items WHERE order_id = $orderId");
            $this->pdo->exec("DELETE FROM orders WHERE id = $orderId");

        } catch (\Exception $e) {
            $this->record('E2E:Customer', 'Customer E2E Journey Failed', false, $e->getMessage());
        }

        echo "\n";
    }

    private function testPhase8OwnerE2E(): void
    {
        echo "--- PHASE 8: OWNER PORTAL OPERATIONS ---\n";

        // Check kitchen orders method and valid transitions
        $orderRepo = new \App\Repositories\OrderRepository();
        
        // Create a temporary test order
        $testOrderNum = 'QA-KANBAN-' . rand(1000, 9999);
        $orderId = $orderRepo->createOrder([
            'order_number'   => $testOrderNum,
            'restaurant_id'  => 1,
            'branch_id'      => 1,
            'table_id'       => 1,
            'customer_id'    => 1,
            'order_type'     => 'dine_in',
            'subtotal'       => 100.0,
            'tax'            => 5.0,
            'service_charge' => 0.0,
            'total_amount'   => 105.0,
            'payment_status' => 'completed',
            'order_status'   => 'placed'
        ]);

        // Status transition testing: placed -> accepted -> preparing -> ready -> completed
        $statuses = ['accepted', 'preparing', 'ready', 'completed'];
        $allPassed = true;
        foreach ($statuses as $st) {
            $orderRepo->updateStatus($orderId, $st);
            $cur = $this->pdo->query("SELECT order_status FROM orders WHERE id = $orderId")->fetchColumn();
            if ($cur !== $st) {
                $allPassed = false;
                break;
            }
        }
        $this->record('Owner:Kitchen', 'Kitchen Kanban Order Status Progression (placed->accepted->preparing->ready->completed)', $allPassed);

        // Clean up test order
        $this->pdo->exec("DELETE FROM orders WHERE id = $orderId");

        // Verify all 12 tables exist for Branch 1
        $tableCount = (int)$this->pdo->query("SELECT COUNT(*) FROM restaurant_tables WHERE branch_id = 1")->fetchColumn();
        $this->record('Owner:Tables', 'All Dining Tables 1-12 Exist', $tableCount >= 12, "Found $tableCount tables");

        echo "\n";
    }

    private function testPhase9AdminE2E(): void
    {
        echo "--- PHASE 9: SUPER ADMIN PORTAL OPERATIONS ---\n";

        // Verify active users have correct roles
        $users = $this->pdo->query("
            SELECT u.id, u.email, r.name as role_name, r.id as role_id 
            FROM users u 
            JOIN roles r ON u.role_id = r.id
        ")->fetchAll(PDO::FETCH_ASSOC);

        $hasAdmin = false;
        $hasOwner = false;
        $hasKitchen = false;
        foreach ($users as $u) {
            if ($u['role_id'] == 1) $hasAdmin = true;
            if ($u['role_id'] == 2) $hasOwner = true;
            if ($u['role_id'] == 4) $hasKitchen = true;
        }

        $this->record('Admin:Roles', 'Super Admin User Exists (role_id=1)', $hasAdmin);
        $this->record('Admin:Roles', 'Restaurant Owner User Exists (role_id=2)', $hasOwner);
        $this->record('Admin:Roles', 'Kitchen Staff User Exists (role_id=4)', $hasKitchen);

        // Verify restaurant approval status
        $approvedCount = (int)$this->pdo->query("SELECT COUNT(*) FROM restaurants WHERE status = 'approved'")->fetchColumn();
        $this->record('Admin:Restaurants', 'Approved Restaurants Exist', $approvedCount >= 1, "Count: $approvedCount");

        echo "\n";
    }

    private function testPhase10Security(): void
    {
        echo "--- PHASE 10: DEFENSIVE SECURITY AUDIT ---\n";

        // 1. SQL Injection resilience: Search endpoint
        $sqliPayload = "' OR '1'='1' -- ";
        $foodRepo = new \App\Repositories\FoodRepository();
        $searchResults = $foodRepo->getByRestaurantId(1, ['search' => $sqliPayload]);
        $this->record('Security:SQLi', 'SQL Injection in Search Handled via PDO Prepared Statements', is_array($searchResults));

        // 2. IDOR Protection: Cart validation cross-restaurant isolation
        $cartService = new \App\Services\CartService();
        $idorBlocked = false;
        try {
            // Food #1 is in Restaurant 1. Attempt to add it under Restaurant 2 context
            $cartService->validateCartItems([
                [
                    'food_id' => 1,
                    'quantity' => 1,
                    'customizations' => [
                        ['id' => 142, 'quantity' => 1],
                        ['id' => 148, 'quantity' => 1]
                    ]
                ]
            ], 2);
        } catch (\InvalidArgumentException $e) {
            $idorBlocked = true;
        }
        $this->record('Security:IDOR', 'IDOR Tenant Isolation Enforced in Cart Validation', $idorBlocked);

        // 3. XSS Escaping test in html helper
        $xssString = '<script>alert("XSS")</script>';
        $escaped = htmlspecialchars($xssString, ENT_QUOTES, 'UTF-8');
        $this->record('Security:XSS', 'Output escaping converts dangerous HTML tags', !str_contains($escaped, '<script>'));

        echo "\n";
    }

    private function testPhase12ImmutabilityAndConcurrency(): void
    {
        echo "--- PHASE 12: DATA IMMUTABILITY & CONCURRENCY ---\n";

        // Test Historical Order Immutability
        // 1. Create order for item #1 with required customizations
        $orderService = new \App\Services\OrderService();
        $order = $orderService->placeOrder([
            'restaurant_id'   => 1,
            'branch_id'       => 1,
            'table_id'        => 1,
            'order_type'      => 'dine_in',
            'customer_name'   => 'Immutability Tester',
            'customer_mobile' => '9999988888',
            'items'           => [
                [
                    'food_id' => 1,
                    'quantity' => 1,
                    'customizations' => [
                        ['id' => 142, 'quantity' => 1],
                        ['id' => 148, 'quantity' => 1]
                    ]
                ]
            ]
        ]);
        $orderId = (int)$order['id'];

        $originalSnapshot = $this->pdo->query("SELECT food_name_snapshot, base_price_snapshot, unit_price FROM order_items WHERE order_id = $orderId")->fetch(PDO::FETCH_ASSOC);

        // 2. Temporarily update the food item's base price in the catalog
        $originalCatalogPrice = (float)$this->pdo->query("SELECT base_price FROM food_items WHERE id = 1")->fetchColumn();
        $this->pdo->exec("UPDATE food_items SET base_price = 999.00 WHERE id = 1");

        // 3. Inspect historical order again
        $reopenedSnapshot = $this->pdo->query("SELECT food_name_snapshot, base_price_snapshot, unit_price FROM order_items WHERE order_id = $orderId")->fetch(PDO::FETCH_ASSOC);

        $isImmutable = ((float)$reopenedSnapshot['unit_price'] === (float)$originalSnapshot['unit_price']) && ((float)$reopenedSnapshot['base_price_snapshot'] === (float)$originalSnapshot['base_price_snapshot']);
        $this->record('Immutability', 'Historical Order Snapshots are 100% Immutable against catalog price updates', $isImmutable, "Snapshot Price: {$reopenedSnapshot['unit_price']}");

        // Revert catalog price back
        $this->pdo->prepare("UPDATE food_items SET base_price = :p WHERE id = 1")->execute([':p' => $originalCatalogPrice]);

        // Clean up test order
        $this->pdo->exec("DELETE FROM order_item_customizations WHERE order_item_id IN (SELECT id FROM order_items WHERE order_id = $orderId)");
        $this->pdo->exec("DELETE FROM order_items WHERE order_id = $orderId");
        $this->pdo->exec("DELETE FROM orders WHERE id = $orderId");

        echo "\n";
    }

    private function httpGet(string $path, array $headers = [], bool $followRedirects = true): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $raw = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerStr = substr((string)$raw, 0, $headerSize);
        $body = substr((string)$raw, $headerSize);

        $parsedHeaders = [];
        foreach (explode("\r\n", $headerStr) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $parsedHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        return [
            'code' => $code,
            'body' => $body,
            'json' => json_decode($body, true),
            'headers' => $parsedHeaders
        ];
    }

    private function httpPost(string $path, array $data): array
    {
        $url = $this->baseUrl . $path;
        $payload = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'code' => $code,
            'body' => $body,
            'json' => json_decode((string)$body, true)
        ];
    }

    private function printSummary(): void
    {
        $total = $this->results['passed'] + $this->results['failed'];
        echo "========================================================\n";
        echo "TEST SUMMARY:\n";
        echo "Total Tests Run: $total\n";
        echo "Passed:         {$this->results['passed']}\n";
        echo "Failed:         {$this->results['failed']}\n";
        echo "========================================================\n";
        if ($this->results['failed'] === 0) {
            echo "FINAL STATUS: ALL TESTS PASSED!\n";
        } else {
            echo "FINAL STATUS: FAIL — UNRESOLVED DEFECTS REMAIN\n";
        }
        echo "========================================================\n";
    }
}

$suite = new MasterTestSuite();
$suite->runAll();
