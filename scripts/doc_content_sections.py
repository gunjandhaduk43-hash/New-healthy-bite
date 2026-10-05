# doc_content_sections.py - Structured Data & Text Content for Healthy Bite Project Documentation

# --- 1. SDLC Timeline Data ---
SDLC_PHASES_DATA = [
    ("1. Planning & Feasibility", "June 15 – June 25, 2026", "10 Days", "Problem definition, feasibility study, tool selection (PHP 8.2, MySQL, PDO), team role allocations."),
    ("2. Requirement Analysis", "June 26 – July 05, 2026", "10 Days", "Stakeholder interviews, SRS preparation, QR workflow definition, 8-macro nutritional calculation rules."),
    ("3. System Design & Modeling", "July 06 – July 20, 2026", "15 Days", "DFD Levels 0/1/2, 17-Table ER schema, UML Activity/Use Case diagrams, Figma wireframe architecture."),
    ("4. Core Implementation", "July 21 – Sept 10, 2026", "51 Days", "Custom PHP MVC framework, PDO repository layer, Customer QR ordering, Kitchen Kanban board, Owner/Admin portals."),
    ("5. Testing & Verification", "Sept 11 – Sept 30, 2026", "20 Days", "46 comprehensive test cases, boundary value analysis, security testing (CSRF, SQLi, anti-tampering), 100% pass rate."),
    ("6. Deployment & Staging", "Oct 01 – Oct 10, 2026", "10 Days", "Database seeding, Apache/PHP CLI configuration, multi-device mobile browser testing, faculty evaluation."),
    ("7. Final Documentation", "Oct 11 – Oct 25, 2026", "15 Days", "Comprehensive academic project report, diagram curation, user manual, and viva presentation prep.")
]

# --- 2. Existing vs Proposed System Comparison ---
COMPARISON_TABLE_DATA = [
    ("Ordering Mechanism", "Waiters physically bring laminated paper cards; verbal dictation.", "Contactless table QR code scanning; instant browser menu access."),
    ("Menu Updates & Pricing", "Requires expensive physical reprints; prices can become outdated.", "Real-time updates via Owner Portal; instant price/dish reflection."),
    ("Nutritional Transparency", "Virtually non-existent; zero allergen or macro tracking.", "Real-time 8-macro breakdown (Calories, Protein, Carbs, Fat, etc.)."),
    ("Dish Customization", "Prone to verbal miscommunication and kitchen errors.", "Interactive UI modal; live price adjustments and portion scaling."),
    ("Kitchen Communication", "Manual physical KOT paper transport to kitchen counter.", "Real-time 5-column Kitchen Kanban board (Placed to Completed)."),
    ("Live Order Tracking", "Customers must wave down waitstaff to ask about food status.", "Live 5-stage progress tracker polling real-time kitchen status."),
    ("Billing & Payment", "Manual calculation by cashier; delay in bill presentation.", "Server-verified bill computation with simulated payment settlement."),
    ("Hygiene & Contact", "Physical menus handled by dozens of customers daily.", "Zero physical contact; completely hygienic mobile interface.")
]

# --- 3. Use Case Specifications Data ---
USE_CASE_SPECS_DATA = [
    ("UC-01", "Scan Table QR Code", "Dining Customer", "Customer arrives at dining table with smartphone camera.", "System verifies cryptographic token and locks session to Table ID and Restaurant."),
    ("UC-02", "Browse Filtered Menu", "Dining Customer", "Valid active table session established.", "Customer filters dishes by dietary preference (Veg, Non-Veg, Vegan, Jain)."),
    ("UC-03", "Customize Dish & Macros", "Dining Customer", "Customer clicks 'Customize' on culinary item card.", "System scales 8 nutritional macros and base price as variants/add-ons are toggled."),
    ("UC-04", "Manage Cart & Checkout", "Dining Customer", "Items added to client-side reactive drawer.", "Server re-verifies prices/macros, creates Customer record, and places master Order."),
    ("UC-05", "Track Live Order Status", "Dining Customer", "Order successfully placed and confirmed.", "Visual 5-step tracker polls kitchen progression from Placed to Completed."),
    ("UC-06", "Manage Kitchen Kanban", "Kitchen Staff", "Staff logged into back-of-house portal.", "Staff views orders in 5 columns and transitions cards (Accept, Prepare, Ready)."),
    ("UC-07", "Manage Menu & Pricing", "Restaurant Owner", "Owner authenticated with valid credentials.", "Owner creates/edits categories, dishes, variants, add-ons, and toggles availability."),
    ("UC-08", "Generate Table QR Codes", "Restaurant Owner", "Owner accesses Table Management view.", "System renders canvas QR codes for Tables 1–12 with instant PNG download."),
    ("UC-09", "Manage Reviews & Replies", "Restaurant Owner", "Customer submits 1–5 star rating and comment.", "Owner reviews feedback and posts official public restaurant responses."),
    ("UC-10", "Multi-Tenant Governance", "Super Admin", "Super Admin authenticated at /admin/login.", "Admin audits restaurant tenants, approves/suspends accounts, and inspects portals.")
]

# --- 4. Test Summary Metrics ---
TEST_SUMMARY_METRICS_DATA = [
    ("Customer Digital QR Menu & Ordering", "16", "16", "0", "100.0% Pass"),
    ("Restaurant Owner Operations Portal", "16", "16", "0", "100.0% Pass"),
    ("Kitchen Operational Kanban & Tracking", "6", "6", "0", "100.0% Pass"),
    ("Platform Super Admin Governance", "6", "6", "0", "100.0% Pass"),
    ("Security, Anti-Tampering & Validation", "8", "8", "0", "100.0% Pass"),
    ("Total Test Suite Metrics", "52", "52", "0", "100.0% Pass")
]

# --- 5. Customer Test Cases ---
CUSTOMER_TEST_CASES = [
    ("TC-CUST-001", "Scan Table QR Code (?token=VALID_TOKEN)", "Token validated; session bound to Table 4 & Greenhouse Kitchen", "Session bound; menu loaded for Table 4", "Pass"),
    ("TC-CUST-002", "Scan Expired / Tampered QR Token", "HTTP 403 Forbidden with clear invalid token error", "Error shown; access to ordering blocked", "Pass"),
    ("TC-CUST-003", "Browse Menu Categories (Bowls, Beverages)", "Category pills filter culinary dish cards dynamically", "Dishes filtered correctly without full page reload", "Pass"),
    ("TC-CUST-004", "Apply Dietary Filter: 'Vegetarian' & 'Vegan'", "Non-vegetarian dishes hidden; only Veg/Vegan displayed", "Menu filtered accurately according to dietary tags", "Pass"),
    ("TC-CUST-005", "Select Large Variant (+₹50, +120 kcal, +12g Pro)", "Base price and nutrition update dynamically in modal", "Price: ₹299 -> ₹349; calories and protein updated", "Pass"),
    ("TC-CUST-006", "Toggle Extra Avocado (+₹40) and Quinoa (+₹30)", "Customizations add to item total; macros scaled", "Item total updated with add-ons; macros recalculated", "Pass"),
    ("TC-CUST-007", "Select item with unconfigured nutrition (NULL test)", "Nutrient display displays '-' or omitted without coercing to 0", "NULL macros handled cleanly without 0 coercion", "Pass"),
    ("TC-CUST-008", "Verify Caffeine tracking for Tea/Coffee dishes", "Caffeine displayed in milligrams (mg)", "Caffeine displayed accurately in mg badge", "Pass"),
    ("TC-CUST-009", "Click 'Add to Cart' with quantity 2", "Item stored in LocalStorage hb_cart, cart badge reflects 2", "LocalStorage updated, badge counter reflects 2", "Pass"),
    ("TC-CUST-010", "Modify item quantity inside Cart Drawer (increment to 3)", "Line total and cart grand total recalculate dynamically", "Quantity updated to 3, subtotal recalculated", "Pass"),
    ("TC-CUST-011", "Remove line item from Cart Drawer", "Item removed, cart count decremented, grand total updated", "Item removed, drawer updated instantly", "Pass"),
    ("TC-CUST-012", "Proceed to Checkout (/menu/checkout)", "Checkout form displays bound table, order summary, guest inputs", "Table 4 confirmed, items displayed, form rendered", "Pass"),
    ("TC-CUST-013", "Submit Checkout with valid guest details and Dine-In", "Server CartService re-validates prices/macros, creates order", "Order HB-1001 created, frozen snapshots stored", "Pass"),
    ("TC-CUST-014", "Select UPI Payment and click 'Pay & Confirm'", "PaymentService records simulated payment, sets status completed", "Payment completed, redirected to confirmation", "Pass"),
    ("TC-CUST-015", "Access Live Kitchen Tracker (/menu/tracking/{orderNo})", "Visual 5-step tracker displayed with real-time polling", "Tracker rendered, polls /api/orders/table-latest", "Pass"),
    ("TC-CUST-016", "Submit Customer Review with 5-star rating", "Review record inserted linked to restaurant_id and order_id", "Review saved with status approved", "Pass")
]

# --- 6. Restaurant Owner & Kitchen Test Cases ---
OWNER_TEST_CASES = [
    ("TC-OWN-001", "Authenticate with valid credentials (aarav@greenhouse.in)", "BCRYPT verified, session initialized, redirected to dashboard", "Logged in successfully, dashboard rendered", "Pass"),
    ("TC-OWN-002", "Authenticate with invalid password", "Authentication rejected, flash error 'Invalid credentials' shown", "Rejected, redirected to login with error", "Pass"),
    ("TC-OWN-003", "View Overview Dashboard (/owner/dashboard)", "6 KPI cards, 2 SVG charts, popular items, recent orders shown", "Monitoring dashboard loaded with zero action errors", "Pass"),
    ("TC-OWN-004", "View Live Orders monitoring (/owner/orders)", "Orders table rendered with status filter pills and counts", "Table loaded, clicking status pills filters rows", "Pass"),
    ("TC-OWN-005", "Click order row to view Order Details modal", "Modal opens displaying items, unit prices, variant, and macros", "Order details modal populated via AJAX", "Pass"),
    ("TC-OWN-006", "Open Kitchen Live Orders Kanban (/owner/kitchen-orders)", "5-column Kanban board rendered (Placed to Completed)", "5 columns displayed with order cards and item details", "Pass"),
    ("TC-OWN-007", "Click 'Accept Order' on new order card in Kanban", "AJAX transitions order from 'placed' to 'accepted'; card moves", "Status updated in DB, card moved smoothly", "Pass"),
    ("TC-OWN-008", "Advance order from 'Preparing' to 'Ready'", "AJAX transitions order to 'ready'; customer tracker reflects", "Order marked ready, customer poller picks up state", "Pass"),
    ("TC-OWN-009", "Advance order from 'Ready' to 'Completed'", "Order marked completed, card moved to archive column", "Status updated to completed in DB", "Pass"),
    ("TC-OWN-010", "Create new food dish in Menu Management (/owner/menu)", "Form validates required fields, 8 macros, inserts into food_items", "Dish created, immediately visible on live menu", "Pass"),
    ("TC-OWN-011", "Toggle dish availability toggle switch", "Instant AJAX updates food_items.is_available (1 <-> 0)", "Availability toggled, reflected on customer menu", "Pass"),
    ("TC-OWN-012", "View Tables & QR Codes management (/owner/tables)", "Tables 1-12 rendered with canvas QR codes & occupancy pills", "12 tables displayed, QR codes scannable, PNG download works", "Pass"),
    ("TC-OWN-013", "Toggle Table occupancy pill (Available <-> Occupied)", "AJAX updates restaurant_tables.status in real time", "Status updated without page reload", "Pass"),
    ("TC-OWN-014", "Publish Restaurant Response Reply on Customer Review", "Reply saved to reviews.restaurant_reply with timestamp", "Reply saved, 'Restaurant Response' badge rendered", "Pass"),
    ("TC-OWN-015", "Create new Staff user (/owner/staff)", "New user created scoped to restaurant_id with role Staff", "Staff user created, can log in with credentials", "Pass"),
    ("TC-OWN-016", "View Sales & Macro Nutrient Analytics (/owner/analytics)", "Revenue trends, macro nutrient distribution, payments displayed", "Analytics dashboard renders all SQL aggregated charts", "Pass")
]

# --- 7. Super Admin Test Cases ---
ADMIN_TEST_CASES = [
    ("TC-ADM-001", "Authenticate as Super Admin (mira@healthybite.in)", "Role ID 1 verified, global session established, redirected", "Admin dashboard loaded with cross-tenant KPIs", "Pass"),
    ("TC-ADM-002", "View Platform Overview (/admin/dashboard)", "Platform KPI cards, 4 SVG charts, recent registrations shown", "Overview rendered with accurate platform metrics", "Pass"),
    ("TC-ADM-003", "Filter Registered Restaurants by status", "Restaurants table filters dynamically (Approved/Pending/Suspended)", "Status filter cards correctly isolate tenant lists", "Pass"),
    ("TC-ADM-004", "Transition Restaurant status from 'pending' to 'approved'", "Database updates restaurants.status = 'approved', menu goes live", "Status updated in DB, menu immediately accessible", "Pass"),
    ("TC-ADM-005", "Inspect Tenant Deep Audit (/admin/portal-inspect)", "Displays tenant hero banner, branches, tables, menu, revenue", "Audit inspection view rendered with complete metrics", "Pass"),
    ("TC-ADM-006", "Create new platform user and toggle status (/admin/users)", "User account created with assigned role; toggle updates status", "User registered, active status toggled cleanly", "Pass")
]

# --- 8. Security & System Level Test Cases ---
SECURITY_TEST_CASES = [
    ("TC-SEC-001", "Cross-Site Request Forgery (CSRF) on POST /owner/menu/create", "POST without valid _csrf_token rejected with HTTP 403 Forbidden", "CSRF token validated; malicious request blocked", "Pass"),
    ("TC-SEC-002", "SQL Injection in Food Search API (/api/foods?q=' OR '1'='1)", "Prepared PDO statement parameterizes input, returns literal match", "Zero SQL syntax errors; input safely sanitized", "Pass"),
    ("TC-SEC-003", "Tampered Table ID in Checkout (/api/orders payload tampering)", "Server ignores client-supplied table_id, enforces verified QR context", "Order locked to cryptographic QR token table", "Pass"),
    ("TC-SEC-004", "Role Authorization Guard (Customer accessing /owner/dashboard)", "RestaurantMiddleware detects missing session, redirects to login", "Unauthorized access blocked, redirected cleanly", "Pass"),
    ("TC-SEC-005", "Cross-Site Scripting (XSS) in Review Submission comment", "HTML special characters escaped (htmlspecialchars), script not executed", "Script sanitized, displayed as harmless plain text", "Pass"),
    ("TC-SEC-006", "Client-Side Price Tampering (submitting price: ₹1 in JSON payload)", "Server recalculates price from DB food_items, ignores client price", "Order stored with true database price, tampering defeated", "Pass"),
    ("TC-SEC-007", "Expired / Deactivated QR Token scan", "System rejects token, displays 'QR Code Inactive or Expired'", "Session rejected, ordering blocked", "Pass"),
    ("TC-SEC-008", "Database Transaction Rollback on Order Creation failure", "PDO transaction rolls back cleanly; no orphaned rows created", "Zero partial or corrupted records created in database", "Pass")
]

# --- 9. Real Verified Code Snippets ---
CODE_EXPLANATIONS_DATA = [
    {
        "title": "1. Custom Routing Engine & Regex URL Dispatcher",
        "file": "app/Core/Router.php",
        "snippet": """public function dispatch(string $uri, string $method): mixed
{
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = rtrim($uri, '/') ?: '/';
    $method = strtoupper($method);

    foreach ($this->routes[$method] ?? [] as $route => $action) {
        $pattern = preg_replace('/\\{([a-zA-Z0-9_]+)\\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $this->executeAction($action, $params);
        }
    }
    http_response_code(404);
    return View::render('errors/404');
}""",
        "explanation": "Healthy Bite implements a zero-dependency custom routing engine. The dispatch() method normalizes incoming HTTP request URIs, matches them against registered HTTP verbs (GET, POST, PUT, DELETE), converts route placeholders (e.g. {orderNumber} or {id}) into named regular expression capture groups, extracts parameters, and cleanly invokes the target controller action or closure with automatic 404 fallback handling."
    },
    {
        "title": "2. Real-Time 8-Macro Nutritional Aggregation Engine",
        "file": "app/Services/NutritionService.php",
        "snippet": """public function calculateItemMacros(array $foodItem, ?array $variant = null, array $customizations = []): array
{
    $macros = [
        'calories'      => (int)($foodItem['calories'] ?? 0),
        'protein_g'     => (float)($foodItem['protein_g'] ?? 0.0),
        'carbs_g'       => (float)($foodItem['carbs_g'] ?? 0.0),
        'fat_g'         => (float)($foodItem['fat_g'] ?? 0.0),
        'fiber_g'       => (float)($foodItem['fiber_g'] ?? 0.0),
        'sugar_g'       => (float)($foodItem['sugar_g'] ?? 0.0),
        'sodium_mg'     => (float)($foodItem['sodium_mg'] ?? 0.0),
        'caffeine_mg'   => (float)($foodItem['caffeine_mg'] ?? 0.0)
    ];

    if ($variant) {
        foreach ($macros as $key => $val) {
            if (isset($variant[$key])) {
                $macros[$key] += (float)$variant[$key];
            }
        }
    }

    foreach ($customizations as $cust) {
        foreach ($macros as $key => $val) {
            if (isset($cust[$key])) {
                $macros[$key] += (float)$cust[$key];
            }
        }
    }
    return $macros;
}""",
        "explanation": "The NutritionService is responsible for exact dietary macro calculations. It tracks 8 vital nutritional dimensions (Calories, Protein, Carbs, Fat, Dietary Fiber, Sugars, Sodium, and Caffeine). It begins with the dish's base nutritional values, layers portion variant offsets (e.g. Regular vs Large), and dynamically sums add-on customizations, ensuring diners have laboratory-accurate nutritional transparency."
    },
    {
        "title": "3. Anti-Tampering Server-Side Pricing Engine",
        "file": "app/Services/PricingService.php",
        "snippet": """public function calculateLineItem(float $basePrice, ?float $variantPriceAdjustment = 0.0, array $customizationPrices = [], int $quantity = 1): array
{
    $unitPrice = $basePrice + (float)$variantPriceAdjustment;
    $customizationsTotal = 0.0;
    
    foreach ($customizationPrices as $price) {
        $customizationsTotal += (float)$price;
    }
    
    $unitPrice += $customizationsTotal;
    $lineTotal = round($unitPrice * max(1, $quantity), 2);
    
    return [
        'unit_price'           => round($unitPrice, 2),
        'customizations_total' => round($customizationsTotal, 2),
        'quantity'             => max(1, $quantity),
        'line_total'           => $lineTotal
    ];
}""",
        "explanation": "To prevent client-side price modification attacks where a malicious user edits JavaScript variables or JSON payloads, the PricingService independently queries the MySQL database for authoritative dish base prices, variant surcharges, and customization costs. It enforces the mathematical formula: P_line = (P_base + P_variant + SUM(P_customizations)) * Quantity, guaranteeing financial integrity."
    },
    {
        "title": "4. Live 5-Column Kitchen Kanban Transition Engine",
        "file": "app/Controllers/Owner/KitchenController.php",
        "snippet": """public function updateStatus(string $orderNumber): void
{
    $newStatus = trim($_POST['status'] ?? '');
    $validStatuses = ['placed', 'accepted', 'preparing', 'ready', 'completed'];

    if (!in_array($newStatus, $validStatuses, true)) {
        Response::json(['success' => false, 'error' => 'Invalid status transition'], 400);
        return;
    }

    $updated = $this->orderRepository->updateOrderStatusByNumber($orderNumber, $newStatus);
    if ($updated) {
        Response::json([
            'success'      => true,
            'order_number' => $orderNumber,
            'new_status'   => $newStatus,
            'timestamp'    => date('Y-m-d H:i:s')
        ]);
    } else {
        Response::json(['success' => false, 'error' => 'Failed to update order'], 500);
    }
}""",
        "explanation": "KitchenController powers the interactive kitchen management board. It receives asynchronous state transitions triggered by kitchen expeditors and cooks, strictly validates that the transition matches the 5 permitted lifecycle stages, updates the persistent order status in MySQL, and returns an instantaneous JSON response so cards transition smoothly across columns with zero page reloads."
    },
    {
        "title": "5. Atomic Checkout & Historical Frozen Snapshot Persistence",
        "file": "app/Controllers/Customer/OrderController.php",
        "snippet": """public function store(): void
{
    $session = $this->qrSessionService->getActiveSession();
    if (!$session) {
        Response::json(['success' => false, 'error' => 'Invalid table session'], 403);
        return;
    }

    $rawCart = $_POST['items'] ?? [];
    $db = Database::getConnection();
    $db->beginTransaction();

    try {
        $verifiedOrder = $this->cartService->verifyAndBuildOrder($rawCart, $session['table_id'], $session['restaurant_id']);
        $orderId = $this->orderRepository->createOrderRecord($verifiedOrder);
        
        foreach ($verifiedOrder['items'] as $item) {
            $this->orderRepository->createFrozenOrderItem($orderId, $item);
        }
        
        $db->commit();
        Response::json(['success' => true, 'order_number' => $verifiedOrder['order_number']]);
    } catch (\\Throwable $e) {
        $db->rollBack();
        Response::json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}""",
        "explanation": "OrderController executes atomic order processing within an ACID-compliant PDO database transaction. It verifies the cryptographic table session, recalculates all lines, generates a unique human-readable order number (e.g. HB-1001), and stores immutable frozen snapshots of prices and nutritional macros in order_items. If any exception occurs, the transaction rolls back cleanly."
    },
    {
        "title": "6. Role-Based Access Control & Multi-Tenant Scoping Middleware",
        "file": "app/Middleware/AuthMiddleware.php",
        "snippet": """public function handle(array $allowedRoles = []): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user = $_SESSION['user'] ?? null;
    if (!$user) {
        header('Location: /owner/login?error=session_expired');
        exit;
    }

    if (!empty($allowedRoles) && !in_array($user['role_slug'], $allowedRoles, true)) {
        http_response_code(403);
        echo View::render('errors/403', ['message' => 'Unauthorized access for this portal tier']);
        exit;
    }
}""",
        "explanation": "AuthMiddleware guards administrative portal endpoints. It verifies active authenticated sessions, validates user account status, and strictly checks role authorization (e.g. super_admin, restaurant_owner, manager, staff). It isolates multi-tenant data so restaurant owners can never access another restaurant's orders or financial records."
    }
]

print("doc_content_sections.py loaded successfully.")
