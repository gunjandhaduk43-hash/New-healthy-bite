# doc_data.py - Structured Data Dictionary, Test Cases & Code Snippets for Healthy Bite Documentation

TABLES_DATA_DICTIONARY = {
    "ADMIN": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Internal unique identifier for legacy admin record; auto-incremented."),
        ("name", "VARCHAR(120)", "None", "Full name of legacy system administrator."),
        ("created_at", "TIMESTAMP", "None", "Date and time when the legacy admin record was initialized."),
        ("updated_at", "TIMESTAMP", "None", "Timestamp of the last modification to the legacy admin record.")
    ],
    "ROLES": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; unique system authority role identifier."),
        ("name", "VARCHAR(50)", "None", "Human-readable role title (e.g. Super Admin, Restaurant Owner, Manager, Staff)."),
        ("slug", "VARCHAR(50)", "UK", "Unique URL/machine slug identifier (super_admin, restaurant_owner, manager, staff)."),
        ("description", "VARCHAR(255)", "None", "Functional description of security privileges and portal access rights."),
        ("created_at", "TIMESTAMP", "None", "Record creation timestamp (default CURRENT_TIMESTAMP)."),
        ("updated_at", "TIMESTAMP", "None", "Record last updated timestamp (default CURRENT_TIMESTAMP).")
    ],
    "RESTAURANTS": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; multi-tenant restaurant entity identifier."),
        ("owner_user_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing users(id); designates primary restaurant owner."),
        ("name", "VARCHAR(150)", "None", "Legal trade name of the dining establishment (e.g. Greenhouse Kitchen)."),
        ("slug", "VARCHAR(150)", "UK", "Unique URL-friendly slug used for tenant resolution and branding."),
        ("logo", "VARCHAR(255)", "None", "Relative path or URL to high-resolution brand logo image."),
        ("cover_image", "VARCHAR(255)", "None", "Path or URL to restaurant hero cover banner graphic."),
        ("description", "TEXT", "None", "Comprehensive brand narrative, culinary focus, and restaurant overview."),
        ("phone", "VARCHAR(30)", "None", "Primary customer service and inquiry contact telephone number."),
        ("email", "VARCHAR(191)", "None", "Official restaurant business contact email address."),
        ("address", "TEXT", "None", "Physical street address of restaurant headquarters or primary facility."),
        ("city", "VARCHAR(100)", "None", "Operating city where the restaurant facility is located."),
        ("state", "VARCHAR(100)", "None", "Operating state or administrative province."),
        ("status", "ENUM('pending','approved','suspended')", "None", "Platform tenancy status governed by Super Admin (default 'approved')."),
        ("created_at", "TIMESTAMP", "None", "Date and time when the restaurant was registered on the platform."),
        ("updated_at", "TIMESTAMP", "None", "Timestamp of the most recent profile modification.")
    ],
    "USERS": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; user account unique identifier."),
        ("role_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing roles(id); defines user authorization level."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); scopes user to tenant (NULL for Super Admin)."),
        ("name", "VARCHAR(120)", "None", "Full legal name of the user or staff employee."),
        ("email", "VARCHAR(191)", "UK", "Unique email address utilized for portal authentication."),
        ("password", "VARCHAR(255)", "None", "Secure one-way BCRYPT cryptographic password hash."),
        ("status", "ENUM('active','inactive','suspended')", "None", "Operational account status (default 'active')."),
        ("created_at", "TIMESTAMP", "None", "User registration timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Account profile modification timestamp.")
    ],
    "BRANCHES": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; physical dining branch outlet identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); parent restaurant entity."),
        ("name", "VARCHAR(150)", "None", "Physical branch title (e.g. Indiranagar Flagship, Koramangala Outlet)."),
        ("address", "TEXT", "None", "Physical street location of the specific branch outlet."),
        ("city", "VARCHAR(100)", "None", "Operating city of the branch."),
        ("state", "VARCHAR(100)", "None", "Operating state of the branch."),
        ("phone", "VARCHAR(30)", "None", "Direct telephone contact for branch operations and reservations."),
        ("status", "ENUM('active','inactive')", "None", "Operational state of the branch location (default 'active')."),
        ("created_at", "TIMESTAMP", "None", "Branch record creation timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Branch record modification timestamp.")
    ],
    "CATEGORIES": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; menu category identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); scopes category to restaurant."),
        ("name", "VARCHAR(100)", "None", "Category title (e.g. Protein Bowls, Artisan Wraps, Superfood Salads, Soups)."),
        ("slug", "VARCHAR(100)", "None", "URL slug used for client-side category filtering."),
        ("description", "TEXT", "None", "Detailed description of the food category classification."),
        ("image", "VARCHAR(255)", "None", "Category icon or representative banner image path."),
        ("sort_order", "INT(11)", "None", "Integer defining visual display order on customer menu (default 0)."),
        ("status", "ENUM('active','inactive')", "None", "Category display toggle state (default 'active')."),
        ("created_at", "TIMESTAMP", "None", "Category creation timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Category modification timestamp.")
    ],
    "FOOD_ITEMS": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; master food dish identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); owning restaurant tenant."),
        ("category_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing categories(id); menu classification."),
        ("name", "VARCHAR(150)", "None", "Culinary dish display title (e.g. Grilled Chicken & Quinoa Protein Bowl)."),
        ("slug", "VARCHAR(150)", "None", "URL-safe unique dish slug for routing and APIs."),
        ("description", "TEXT", "None", "Comprehensive sensory and culinary description of the food item."),
        ("image", "VARCHAR(255)", "None", "High-resolution Unsplash photo URL or local asset path."),
        ("ingredients", "TEXT", "None", "List of primary ingredients and culinary preparation components."),
        ("allergens", "VARCHAR(255)", "None", "Declared allergen warnings (e.g. Dairy, Tree Nuts, Gluten, Soy)."),
        ("food_type", "ENUM('vegetarian','non_vegetarian','vegan','jain','other')", "None", "Dietary lifestyle classification badge."),
        ("base_price", "DECIMAL(10,2)", "None", "Standard baseline menu price in Indian Rupees (₹)."),
        ("calories", "INT UNSIGNED", "None", "Total nutritional energy content measured in kilocalories (kcal)."),
        ("protein", "DECIMAL(6,2)", "None", "Total protein content measured strictly in grams (g)."),
        ("carbs", "DECIMAL(6,2)", "None", "Total carbohydrate content measured strictly in grams (g)."),
        ("fat", "DECIMAL(6,2)", "None", "Total dietary fat content measured strictly in grams (g)."),
        ("fiber", "DECIMAL(6,2)", "None", "Dietary fiber content measured strictly in grams (g)."),
        ("sugar", "DECIMAL(6,2)", "None", "Simple sugars content measured strictly in grams (g)."),
        ("sodium", "DECIMAL(7,2)", "None", "Sodium mineral content measured strictly in milligrams (mg)."),
        ("caffeine", "DECIMAL(6,2)", "None", "Caffeine stimulant content measured strictly in milligrams (mg)."),
        ("serving_size", "VARCHAR(100)", "None", "Standard portion weight or volume (e.g. 350g, 400ml)."),
        ("is_featured", "TINYINT(1)", "None", "Boolean flag highlighting dish on restaurant entrance showcase."),
        ("is_popular", "TINYINT(1)", "None", "Boolean flag designating popular best-seller badge."),
        ("is_available", "TINYINT(1)", "None", "Live kitchen availability toggle (1=Available, 0=Sold Out)."),
        ("created_at", "TIMESTAMP", "None", "Food dish registration timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Food dish modification timestamp.")
    ],
    "FOOD_VARIANTS": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; food portion variant identifier."),
        ("food_item_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing food_items(id) ON DELETE CASCADE."),
        ("name", "VARCHAR(100)", "None", "Portion title (e.g. Regular, Large Portion, Double Protein)."),
        ("description", "VARCHAR(255)", "None", "Explanation of portion size or preparation difference."),
        ("price_adjustment", "DECIMAL(10,2)", "None", "Price differential added to base price in Indian Rupees (₹)."),
        ("calories_adjustment", "INT(11)", "None", "Calorie differential applied to base calories (kcal)."),
        ("protein_adjustment", "DECIMAL(6,2)", "None", "Protein differential applied to base protein (g)."),
        ("carbs_adjustment", "DECIMAL(6,2)", "None", "Carbohydrate differential applied to base carbs (g)."),
        ("fat_adjustment", "DECIMAL(6,2)", "None", "Fat differential applied to base fat (g)."),
        ("fiber_adjustment", "DECIMAL(6,2)", "None", "Fiber differential applied to base fiber (g)."),
        ("sugar_adjustment", "DECIMAL(6,2)", "None", "Sugar differential applied to base sugar (g)."),
        ("sodium_adjustment", "DECIMAL(7,2)", "None", "Sodium differential applied to base sodium (mg)."),
        ("caffeine_adjustment", "DECIMAL(6,2)", "None", "Caffeine differential applied to base caffeine (mg)."),
        ("is_required", "TINYINT(1)", "None", "Mandatory selection flag (default 0)."),
        ("is_available", "TINYINT(1)", "None", "Active availability toggle for the portion option."),
        ("sort_order", "INT(11)", "None", "Display sort order within portion selection UI."),
        ("created_at", "TIMESTAMP", "None", "Variant record creation timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Variant record modification timestamp.")
    ],
    "FOOD_CUSTOMIZATION": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; food customization add-on identifier."),
        ("food_item_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing food_items(id) ON DELETE CASCADE."),
        ("group_name", "VARCHAR(100)", "None", "Option grouping header (e.g. Choose Base, Dressing, Extra Toppings)."),
        ("name", "VARCHAR(100)", "None", "Customization display title (e.g. Extra Hass Avocado, Greek Yogurt Dip)."),
        ("description", "VARCHAR(255)", "None", "Ingredient or preparation details of the add-on."),
        ("price_adjustment", "DECIMAL(10,2)", "None", "Unit cost added to item total in Indian Rupees (₹)."),
        ("calories_adjustment", "INT(11)", "None", "Caloric energy added per unit selection (kcal)."),
        ("protein_adjustment", "DECIMAL(6,2)", "None", "Protein added per unit selection in grams (g)."),
        ("carbs_adjustment", "DECIMAL(6,2)", "None", "Carbohydrates added per unit selection in grams (g)."),
        ("fat_adjustment", "DECIMAL(6,2)", "None", "Fats added per unit selection in grams (g)."),
        ("fiber_adjustment", "DECIMAL(6,2)", "None", "Fiber added per unit selection in grams (g)."),
        ("sugar_adjustment", "DECIMAL(6,2)", "None", "Sugar added per unit selection in grams (g)."),
        ("sodium_adjustment", "DECIMAL(7,2)", "None", "Sodium added per unit selection in milligrams (mg)."),
        ("caffeine_adjustment", "DECIMAL(6,2)", "None", "Caffeine added per unit selection in milligrams (mg)."),
        ("is_required", "TINYINT(1)", "None", "Enforces at least one option selection in this group."),
        ("min_quantity", "INT UNSIGNED", "None", "Minimum number of items customer must select from group (default 0)."),
        ("max_quantity", "INT UNSIGNED", "None", "Maximum number of items customer can select from group (default 1)."),
        ("is_available", "TINYINT(1)", "None", "Inventory availability toggle for the customization."),
        ("sort_order", "INT(11)", "None", "Sort order within the customization group."),
        ("created_at", "TIMESTAMP", "None", "Customization record creation timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Customization record modification timestamp.")
    ],
    "RESTAURANT_TABLE": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; physical dining table identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); parent restaurant entity."),
        ("branch_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing branches(id); physical branch location."),
        ("table_number", "VARCHAR(50)", "None", "Display label identifying dining table (e.g. Table 1, Table 12)."),
        ("status", "ENUM('available','occupied','cleaning','out_of_service')", "None", "Live occupancy state (default 'available')."),
        ("created_at", "TIMESTAMP", "None", "Table configuration timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Table occupancy state transition timestamp.")
    ],
    "QR_TOKEN": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; cryptographic QR token identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); bound restaurant entity."),
        ("branch_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing branches(id); bound branch location."),
        ("table_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurant_tables(id); bound dining table."),
        ("token", "VARCHAR(128)", "UK", "Cryptographically generated unique alphanumeric token string."),
        ("status", "ENUM('active','inactive','expired')", "None", "Token security validation state (default 'active')."),
        ("expires_at", "TIMESTAMP", "None", "Optional expiry timestamp for time-limited QR sessions."),
        ("created_at", "TIMESTAMP", "None", "Token generation and issuance timestamp.")
    ],
    "CUSTOMER": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; dining guest profile identifier."),
        ("name", "VARCHAR(120)", "None", "Full name of the dining customer captured during checkout."),
        ("mobile", "VARCHAR(30)", "None", "Customer contact mobile phone number."),
        ("email", "VARCHAR(191)", "None", "Customer contact email address for electronic receipts."),
        ("created_at", "TIMESTAMP", "None", "Customer profile creation timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Customer profile modification timestamp.")
    ],
    "ORDER": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; master dining order identifier."),
        ("order_number", "VARCHAR(64)", "UK", "Human-readable order tracking code (e.g. HB-1001, HB-18A3B4C-782)."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); target restaurant entity."),
        ("branch_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing branches(id); target dining outlet."),
        ("table_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurant_tables(id); NULL for Takeaway."),
        ("customer_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing customers(id); ordering guest diner."),
        ("order_type", "ENUM('dine_in','takeaway')", "None", "Dining service mode (default 'dine_in')."),
        ("subtotal", "DECIMAL(10,2)", "None", "Sum of all order items and customizations before taxation (₹)."),
        ("tax", "DECIMAL(10,2)", "None", "Calculated Goods & Services Tax (GST 5%) in Indian Rupees (₹)."),
        ("service_charge", "DECIMAL(10,2)", "None", "Optional restaurant hospitality service charge in Indian Rupees (₹)."),
        ("total_amount", "DECIMAL(10,2)", "None", "Final grand payable total amount in Indian Rupees (₹)."),
        ("payment_status", "ENUM('pending','completed','failed')", "None", "Settlement state of order financial transaction."),
        ("order_status", "ENUM('placed','accepted','preparing','ready','completed','cancelled')", "None", "Operational kitchen lifecycle status."),
        ("notes", "TEXT", "None", "Customer special food preparation or dietary instructions."),
        ("created_at", "TIMESTAMP", "None", "Order submission timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Order status transition timestamp.")
    ],
    "ORDER_ITEM": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; order line item identifier."),
        ("order_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing orders(id); master order parent."),
        ("food_item_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing food_items(id); source dish catalog item."),
        ("food_name_snapshot", "VARCHAR(150)", "None", "Frozen dish name at the precise moment of purchase."),
        ("base_price_snapshot", "DECIMAL(10,2)", "None", "Frozen baseline unit price at purchase time (₹)."),
        ("variant_name_snapshot", "VARCHAR(100)", "None", "Frozen portion variant title selected by customer."),
        ("variant_price_snapshot", "DECIMAL(10,2)", "None", "Frozen portion price adjustment at purchase time (₹)."),
        ("quantity", "INT UNSIGNED", "None", "Quantity of this specific configured dish ordered."),
        ("unit_price", "DECIMAL(10,2)", "None", "Calculated single-item price including variant & customizations (₹)."),
        ("total_price", "DECIMAL(10,2)", "None", "Line total = unit_price multiplied by quantity (₹)."),
        ("calories", "INT(11)", "None", "Frozen caloric energy snapshot per unit (kcal)."),
        ("protein", "DECIMAL(6,2)", "None", "Frozen protein snapshot per unit in grams (g)."),
        ("carbs", "DECIMAL(6,2)", "None", "Frozen carbohydrate snapshot per unit in grams (g)."),
        ("fat", "DECIMAL(6,2)", "None", "Frozen fat snapshot per unit in grams (g)."),
        ("fiber", "DECIMAL(6,2)", "None", "Frozen fiber snapshot per unit in grams (g)."),
        ("sugar", "DECIMAL(6,2)", "None", "Frozen sugar snapshot per unit in grams (g)."),
        ("sodium", "DECIMAL(7,2)", "None", "Frozen sodium snapshot per unit in milligrams (mg)."),
        ("caffeine", "DECIMAL(6,2)", "None", "Frozen caffeine snapshot per unit in milligrams (mg)."),
        ("created_at", "TIMESTAMP", "None", "Line item purchase timestamp.")
    ],
    "ORDER_ITEM_CUSTOMIZATION": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; order item customization snapshot identifier."),
        ("order_item_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing order_items(id) ON DELETE CASCADE."),
        ("customization_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing food_customizations(id)."),
        ("customization_name_snapshot", "VARCHAR(100)", "None", "Frozen name of selected add-on option."),
        ("quantity", "INT UNSIGNED", "None", "Quantity of this specific customization selected."),
        ("price_adjustment", "DECIMAL(10,2)", "None", "Frozen price delta applied per unit add-on (₹)."),
        ("calories_adjustment", "INT(11)", "None", "Frozen calorie adjustment per unit (kcal)."),
        ("protein_adjustment", "DECIMAL(6,2)", "None", "Frozen protein adjustment per unit in grams (g)."),
        ("carbs_adjustment", "DECIMAL(6,2)", "None", "Frozen carbohydrate adjustment per unit in grams (g)."),
        ("fat_adjustment", "DECIMAL(6,2)", "None", "Frozen fat adjustment per unit in grams (g)."),
        ("fiber_adjustment", "DECIMAL(6,2)", "None", "Frozen fiber adjustment per unit in grams (g)."),
        ("sugar_adjustment", "DECIMAL(6,2)", "None", "Frozen sugar adjustment per unit in grams (g)."),
        ("sodium_adjustment", "DECIMAL(7,2)", "None", "Frozen sodium adjustment per unit in milligrams (mg)."),
        ("caffeine_adjustment", "DECIMAL(6,2)", "None", "Frozen caffeine adjustment per unit in milligrams (mg)."),
        ("created_at", "TIMESTAMP", "None", "Snapshot creation timestamp.")
    ],
    "PAYMENT": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; financial payment settlement identifier."),
        ("order_id", "BIGINT(20) UNSIGNED", "UK, FK", "Foreign key referencing orders(id); strict 1-to-1 relationship."),
        ("payment_method", "ENUM('cash','upi','card')", "None", "Authorized settlement mechanism."),
        ("transaction_reference", "VARCHAR(128)", "None", "Unique transaction reference code (e.g. TXN-UPI-18A3B4C)."),
        ("amount", "DECIMAL(10,2)", "None", "Total settled financial amount in Indian Rupees (₹)."),
        ("status", "ENUM('pending','completed','failed')", "None", "Settlement transaction state (default 'pending')."),
        ("paid_at", "TIMESTAMP", "None", "Timestamp when payment was successfully verified and cleared."),
        ("created_at", "TIMESTAMP", "None", "Payment record creation timestamp.")
    ],
    "REVIEW": [
        ("id", "BIGINT(20) UNSIGNED", "PK", "Primary key; customer feedback review identifier."),
        ("restaurant_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing restaurants(id); target restaurant entity."),
        ("customer_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing customers(id); authoring guest customer."),
        ("order_id", "BIGINT(20) UNSIGNED", "FK", "Foreign key referencing orders(id); specific dining order reviewed."),
        ("rating", "TINYINT UNSIGNED", "None", "Numerical dining quality rating on 1 to 5 star scale."),
        ("comment", "TEXT", "None", "Qualitative feedback text submitted by the customer."),
        ("restaurant_reply", "TEXT", "None", "Official restaurant owner response reply published to customer."),
        ("replied_at", "TIMESTAMP", "None", "Timestamp when restaurant owner published the response reply."),
        ("status", "ENUM('pending','approved','hidden')", "None", "Public review moderation state (default 'pending')."),
        ("created_at", "TIMESTAMP", "None", "Review submission timestamp."),
        ("updated_at", "TIMESTAMP", "None", "Review or reply modification timestamp.")
    ]
}

# 18 Code Snippets Definitions
CODE_SECTIONS = [
    {
        "filename": "public/index.php",
        "module": "Architecture & Front Controller",
        "purpose": "Application entry point, PSR-4 fallback autoloader, helper loading, and lifecycle dispatch",
        "ref": "Lines 1–52",
        "snippet": """<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__));

// 1. PSR-4 Fallback Autoloader
spl_autoload_register(function (string $class) {
    $prefix = 'App\\\\';
    $baseDir = HB_ROOT . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

// 2. Load Helpers & Constants
require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

// 3. Load Environment & Dispatch
\\App\\Core\\Env::load(HB_ROOT . '/.env');
$app = new \\App\\Core\\App();
$router = $app->getRouter();
require_once HB_ROOT . '/routes/web.php';
require_once HB_ROOT . '/routes/api.php';
$app->run();""",
        "explanation": "Acts as the single gatekeeper for all HTTP requests. Automatically handles static file serving in CLI server mode, registers the custom PSR-4 autoloader, initializes environment variables, registers web and API routes, and triggers the request dispatcher.",
        "expected": "Every incoming browser or API request is intercepted, routed through security filters and middleware, and cleanly dispatched to the appropriate controller action."
    },
    {
        "filename": "app/Core/Router.php",
        "module": "Routing Engine",
        "purpose": "Dynamic regex pattern route matching with HTTP method enforcement and parameter extraction",
        "ref": "Lines 35–82",
        "snippet": """public function dispatch(Request $request): Response
{
    $method = $request->getMethod();
    $path = $request->getPath();

    foreach ($this->routes as $route) {
        if ($route['method'] !== $method) continue;
        
        $pattern = preg_replace('/\\{([a-zA-Z0-9_]+)\\}/', '(?P<$1>[^/]+)', $route['uri']);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $this->executeHandler($route['handler'], $params);
        }
    }
    return Response::html('<h1>404 Not Found</h1>', 404);
}""",
        "explanation": "Iterates through registered routes, converts dynamic URI segments (such as {orderNumber} or {id}) into named regular expression capture groups, extracts parameters, and invokes controller handlers.",
        "expected": "Accurately resolves dynamic URLs like /menu/confirmation/HB-1001 and passes extracted parameters directly to controller action arguments."
    },
    {
        "filename": "app/Core/Database.php",
        "module": "Data Abstraction Layer",
        "purpose": "Singleton PDO connection manager with secure configuration and prepared statement guarantees",
        "ref": "Lines 18–45",
        "snippet": """public static function getConnection(): PDO
{
    if (self::$instance === null) {
        $config = require HB_ROOT . '/config/database.php';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'], $config['port'], $config['database'], $config['charset']
        );
        self::$instance = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return self::$instance;
}""",
        "explanation": "Implements the Singleton design pattern to maintain a single reusable PDO connection to MySQL. Strictly disables emulated prepared statements to ensure all SQL queries are parsed and executed securely on the MySQL server.",
        "expected": "Protects against SQL injection vulnerabilities across all repository queries while minimizing database connection overhead."
    },
    {
        "filename": "app/Core/Auth.php",
        "module": "Security & Identity",
        "purpose": "Role verification, session authentication state, and credential encapsulation",
        "ref": "Lines 10–44",
        "snippet": """public static function check(): bool
{
    Session::start();
    return Session::has(self::USER_SESSION_KEY);
}

public static function user(): ?array
{
    Session::start();
    return Session::get(self::USER_SESSION_KEY);
}

public static function login(array $user): void
{
    Session::start();
    Session::regenerate();
    Session::set(self::USER_SESSION_KEY, [
        'id'            => $user['id'],
        'role_id'       => $user['role_id'],
        'restaurant_id' => $user['restaurant_id'] ?? null,
        'name'          => $user['name'],
        'email'         => $user['email'],
    ]);
}""",
        "explanation": "Provides a clean interface for session identity. On login, automatically regenerates the session ID to thwart session fixation attacks and encapsulates user ID, role ID, and restaurant scoping parameters.",
        "expected": "Enforces strict authorization boundaries, preventing unauthorized access across Super Admin, Restaurant Owner, Staff, and Guest portals."
    },
    {
        "filename": "app/Services/QrService.php",
        "module": "Contactless Dining",
        "purpose": "Cryptographic QR token resolution and multi-entity relational table context binding",
        "ref": "Lines 22–58",
        "snippet": """public function resolveToken(string $token): ?array
{
    $sql = "SELECT qr.id AS qr_token_id, qr.token, qr.status AS qr_status,
                   t.id AS table_id, t.table_number, t.status AS table_status,
                   b.id AS branch_id, b.name AS branch_name,
                   r.id AS restaurant_id, r.name AS restaurant_name
            FROM qr_tokens qr
            JOIN restaurant_tables t ON qr.table_id = t.id
            JOIN branches b ON t.branch_id = b.id
            JOIN restaurants r ON b.restaurant_id = r.id
            WHERE qr.token = :token AND qr.status = 'active'
              AND r.status = 'approved' AND b.status = 'active'
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':token' => $token]);
    return $stmt->fetch() ?: null;
}""",
        "explanation": "Executes an inner-join query across qr_tokens, restaurant_tables, branches, and restaurants. Validates that the token is active, the branch is operational, and the restaurant is approved by the Super Admin.",
        "expected": "Binds customer browsing sessions directly to the physical dining table and prevents spoofing of table numbers or restaurant identities."
    },
    {
        "filename": "app/Repositories/FoodRepository.php",
        "module": "Food Catalog Management",
        "purpose": "Restaurant-scoped food dish retrieval with full 8-macro nutritional metrics and diet tags",
        "ref": "Lines 28–64",
        "snippet": """public function getByRestaurantId(int $restaurantId, ?int $categoryId = null, ?string $search = null): array
{
    $sql = "SELECT f.*, c.name AS category_name
            FROM food_items f
            JOIN categories c ON f.category_id = c.id
            WHERE f.restaurant_id = :restaurant_id AND f.is_available = 1";
    $params = [':restaurant_id' => $restaurantId];

    if ($categoryId !== null) {
        $sql .= " AND f.category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }
    if ($search !== null && $search !== '') {
        $sql .= " AND (f.name LIKE :search OR f.description LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }
    $sql .= " ORDER BY c.sort_order ASC, f.id ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}""",
        "explanation": "Fetches dishes strictly belonging to the authorized restaurant. Enforces category filtering, search term matching, and active availability checks.",
        "expected": "Ensures complete multi-tenant catalog isolation so customers only view dishes belonging to the scanned restaurant."
    },
    {
        "filename": "app/Services/PricingService.php",
        "module": "Pricing Engine",
        "purpose": "Mathematical calculation of line item prices, variants, customization sums, and 5% GST",
        "ref": "Lines 18–55",
        "snippet": """public function calculateSingleItemPrice(float $basePrice, float $variantAdj, array $customizations): float
{
    $customTotal = 0.0;
    foreach ($customizations as $c) {
        $qty = max(1, (int)($c['quantity'] ?? 1));
        $customTotal += (float)$c['price_adjustment'] * $qty;
    }
    return round($basePrice + $variantAdj + $customTotal, 2);
}

public function calculateOrderTotals(array $lineTotals, float $taxRate = 0.05, float $serviceCharge = 0.0): array
{
    $subtotal = round(array_sum($lineTotals), 2);
    $tax = round($subtotal * $taxRate, 2);
    $totalAmount = round($subtotal + $tax + $serviceCharge, 2);
    return [
        'subtotal'       => $subtotal,
        'tax'            => $tax,
        'service_charge' => $serviceCharge,
        'total_amount'   => $totalAmount
    ];
}""",
        "explanation": "Calculates unit prices by summing base price, portion variant price adjustment, and all selected customization add-ons. Applies standard 5% GST tax and produces verifiable grand totals.",
        "expected": "Guarantees mathematical precision across cart drawer, checkout calculations, and database order snapshots."
    },
    {
        "filename": "app/Services/NutritionService.php",
        "module": "Nutrition Calculation Engine",
        "purpose": "Comprehensive arithmetic summation and quantity scaling across all 8 nutrient macros",
        "ref": "Lines 25–85",
        "snippet": """public function calculateItemNutrition(array $baseItem, ?array $variant, array $customizations): array
{
    $macroKeys = ['calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium', 'caffeine'];
    $result = [];

    foreach ($macroKeys as $m) {
        $base = $baseItem[$m] ?? null;
        if ($base === null) {
            $result[$m] = null;
            continue;
        }
        $val = (float)$base;
        if ($variant && isset($variant[$m . '_adjustment']) && $variant[$m . '_adjustment'] !== null) {
            $val += (float)$variant[$m . '_adjustment'];
        }
        foreach ($customizations as $c) {
            if (isset($c[$m . '_adjustment']) && $c[$m . '_adjustment'] !== null) {
                $qty = max(1, (int)($c['quantity'] ?? 1));
                $val += (float)$c[$m . '_adjustment'] * $qty;
            }
        }
        $result[$m] = ($m === 'calories') ? (int)round(max(0, $val)) : round(max(0.0, $val), 2);
    }
    return $result;
}""",
        "explanation": "Evaluates each of the 8 nutrient macros individually. Preserves NULL values if unconfigured, adds portion deltas and customization deltas, and scales integers for calories and decimals for macros.",
        "expected": "Displays scientifically accurate nutritional breakdowns for health-conscious diners and records immutable macro snapshots on purchase."
    },
    {
        "filename": "app/Services/CartService.php",
        "module": "Cart Verification",
        "purpose": "Server-side re-validation of client cart items against active MySQL records to prevent price tampering",
        "ref": "Lines 30–88",
        "snippet": """public function validateCartItems(array $rawCartItems, int $restaurantId): array
{
    $validatedItems = [];
    foreach ($rawCartItems as $raw) {
        $foodId = (int)($raw['food_id'] ?? 0);
        $food = $this->foodRepo->getByIdAndRestaurant($foodId, $restaurantId);
        if (!$food || !(bool)$food['is_available']) continue;

        $variant = null;
        if (!empty($raw['variant_id'])) {
            $variant = $this->foodRepo->getVariantById((int)$raw['variant_id'], $foodId);
        }

        $customizations = [];
        if (!empty($raw['customization_ids'])) {
            $customizations = $this->foodRepo->getCustomizationsByIds((array)$raw['customization_ids'], $foodId);
        }

        $qty = max(1, (int)($raw['quantity'] ?? 1));
        $unitPrice = $this->pricingService->calculateSingleItemPrice((float)$food['base_price'], (float)($variant['price_adjustment'] ?? 0), $customizations);
        $nutrition = $this->nutritionService->calculateItemNutrition($food, $variant, $customizations);

        $validatedItems[] = [
            'food'           => $food,
            'variant'        => $variant,
            'customizations' => $customizations,
            'quantity'       => $qty,
            'unit_price'     => $unitPrice,
            'line_total'     => round($unitPrice * $qty, 2),
            'nutrition'      => $nutrition
        ];
    }
    return $validatedItems;
}""",
        "explanation": "Intercepts client-submitted cart arrays during checkout. Re-fetches prices, variants, and customizations directly from MySQL, completely ignoring any client-modified price fields.",
        "expected": "Protects restaurant operators from client-side payload tampering and malicious price manipulations."
    },
    {
        "filename": "app/Services/OrderService.php",
        "module": "Order Fulfillment Pipeline",
        "purpose": "End-to-end transactional order creation, token locking, customer resolution, and snapshot persistence",
        "ref": "Lines 40–115",
        "snippet": """public function placeOrder(array $payload): array
{
    $db = Database::getConnection();
    $db->beginTransaction();
    try {
        // Enforce verified QR token context over client payload
        if (!empty($payload['qr_token'])) {
            $resolved = $this->qrService->resolveToken($payload['qr_token']);
            if ($resolved) {
                $payload['restaurant_id'] = $resolved['restaurant_id'];
                $payload['branch_id']     = $resolved['branch_id'];
                $payload['table_id']      = $resolved['table_id'];
            }
        }

        $validatedItems = $this->cartService->validateCartItems($payload['items'], (int)$payload['restaurant_id']);
        if (empty($validatedItems)) throw new \\Exception('Cart contains no valid items.');

        $customerId = $this->orderRepo->findOrCreateCustomer($payload['customer_name'], $payload['customer_mobile'], $payload['customer_email'] ?? null);
        $orderNumber = 'HB-' . strtoupper(dechex(time())) . '-' . rand(100, 999);
        
        $lineTotals = array_column($validatedItems, 'line_total');
        $totals = $this->pricingService->calculateOrderTotals($lineTotals);

        $orderId = $this->orderRepo->createOrder([
            'order_number'    => $orderNumber,
            'restaurant_id'   => $payload['restaurant_id'],
            'branch_id'       => $payload['branch_id'],
            'table_id'        => $payload['table_id'] ?? null,
            'customer_id'     => $customerId,
            'order_type'      => $payload['order_type'] ?? 'dine_in',
            'subtotal'        => $totals['subtotal'],
            'tax'             => $totals['tax'],
            'total_amount'    => $totals['total_amount'],
            'order_status'    => 'placed',
            'payment_status'  => 'pending'
        ]);

        foreach ($validatedItems as $item) {
            $orderItemId = $this->orderRepo->addOrderItem($orderId, $item);
            foreach ($item['customizations'] as $c) {
                $this->orderRepo->addOrderItemCustomization($orderItemId, $c);
            }
        }

        $db->commit();
        return ['success' => true, 'order_id' => $orderId, 'order_number' => $orderNumber];
    } catch (\\Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}""",
        "explanation": "Executes complete atomic transaction: locks table context from cryptographic token, validates cart items against database, creates customer profile, calculates grand totals, inserts master order record, and stores itemized snapshots.",
        "expected": "Guarantees database consistency through full ACID rollback protection if any component fails during order placement."
    },
    {
        "filename": "app/Repositories/OrderRepository.php",
        "module": "Order Persistence",
        "purpose": "Frozen snapshot insertion of food dishes and all 8 nutritional macros into order_items",
        "ref": "Lines 95–130",
        "snippet": """public function addOrderItem(int $orderId, array $item): int
{
    $sql = "INSERT INTO order_items (
                order_id, food_item_id, food_name_snapshot, base_price_snapshot,
                variant_name_snapshot, variant_price_snapshot, quantity, unit_price, total_price,
                calories, protein, carbs, fat, fiber, sugar, sodium, caffeine
            ) VALUES (
                :order_id, :food_id, :name_snap, :base_snap,
                :var_snap, :var_price, :qty, :unit_price, :total_price,
                :cal, :pro, :carb, :fat, :fib, :sug, :sod, :caff
            )";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':order_id'     => $orderId,
        ':food_id'      => $item['food']['id'],
        ':name_snap'    => $item['food']['name'],
        ':base_snap'    => $item['food']['base_price'],
        ':var_snap'     => $item['variant']['name'] ?? null,
        ':var_price'    => $item['variant']['price_adjustment'] ?? 0.00,
        ':qty'          => $item['quantity'],
        ':unit_price'   => $item['unit_price'],
        ':total_price'  => $item['line_total'],
        ':cal'          => $item['nutrition']['calories'] ?? null,
        ':pro'          => $item['nutrition']['protein'] ?? null,
        ':carb'         => $item['nutrition']['carbs'] ?? null,
        ':fat'          => $item['nutrition']['fat'] ?? null,
        ':fib'          => $item['nutrition']['fiber'] ?? null,
        ':sug'          => $item['nutrition']['sugar'] ?? null,
        ':sod'          => $item['nutrition']['sodium'] ?? null,
        ':caff'         => $item['nutrition']['caffeine'] ?? null,
    ]);
    return (int)$this->db->lastInsertId();
}""",
        "explanation": "Freezes all dish attributes, names, prices, and 8 nutrient values at the exact moment of order placement.",
        "expected": "Future changes to restaurant menu prices or recipes do not alter past historical financial records or customer order receipts."
    },
    {
        "filename": "app/Services/PaymentService.php",
        "module": "Financial Settlement",
        "purpose": "Realistic payment simulation across Cash, UPI, and Card advancing order lifecycle to accepted",
        "ref": "Lines 24–65",
        "snippet": """public function simulatePayment(int $orderId, string $method): array
{
    $allowed = ['cash', 'upi', 'card'];
    if (!in_array($method, $allowed, true)) throw new \\InvalidArgumentException('Invalid payment method.');

    $order = $this->orderRepo->findById($orderId);
    if (!$order) throw new \\Exception('Order not found.');

    $txRef = 'TXN-' . strtoupper($method) . '-' . strtoupper(substr(md5(uniqid()), 0, 10));
    
    $paymentId = $this->paymentRepo->createPayment([
        'order_id'              => $orderId,
        'payment_method'        => $method,
        'transaction_reference' => $txRef,
        'amount'                => $order['total_amount'],
        'status'                => 'completed',
        'paid_at'               => date('Y-m-d H:i:s')
    ]);

    $this->orderRepo->updatePaymentStatus($orderId, 'completed');
    $this->orderRepo->updateStatus($orderId, 'accepted');

    return ['success' => true, 'transaction_reference' => $txRef, 'status' => 'completed'];
}""",
        "explanation": "Simulates payment clearing, records a transaction in payments with unique reference, updates order payment status to completed, and advances kitchen lifecycle to accepted.",
        "expected": "Eliminates payment friction while maintaining strict referential integrity with a unique 1-to-1 foreign key linkage to orders."
    },
    {
        "filename": "app/Controllers/Owner/OrderController.php",
        "module": "Kitchen Operations",
        "purpose": "5-column operational Kanban board status transitions and order card advancement",
        "ref": "Lines 75–118",
        "snippet": """public function updateStatus(int $id): void
{
    $session = RestaurantMiddleware::handle();
    $restaurantId = $session['restaurant_id'];
    
    $nextStatus = trim((string)Request::post('status', ''));
    $validTransitions = [
        'placed'    => ['accepted', 'cancelled'],
        'accepted'  => ['preparing', 'cancelled'],
        'preparing' => ['ready'],
        'ready'     => ['completed'],
    ];

    $order = $this->orderRepo->findWithItemsById($id);
    if (!$order || (int)$order['restaurant_id'] !== $restaurantId) {
        Response::json(['success' => false, 'error' => 'Order not found'], 404);
        return;
    }

    $currentStatus = $order['order_status'];
    if (!isset($validTransitions[$currentStatus]) || !in_array($nextStatus, $validTransitions[$currentStatus], true)) {
        Response::json(['success' => false, 'error' => 'Invalid status transition'], 400);
        return;
    }

    $this->orderRepo->updateStatusForRestaurant($id, $restaurantId, $nextStatus);
    Response::json(['success' => true, 'current_status' => $nextStatus]);
}""",
        "explanation": "Validates state transitions against an enforced state machine matrix. Prevents out-of-order transitions and ensures kitchen staff cannot transition an order from 'placed' directly to 'completed'.",
        "expected": "Maintains sequential kitchen workflow and updates the live tracking timeline for the seated customer in real time."
    },
    {
        "filename": "app/Repositories/ReviewRepository.php",
        "module": "Customer Feedback",
        "purpose": "Customer review management and official restaurant owner response reply persistence",
        "ref": "Lines 42–72",
        "snippet": """public function addReply(int $reviewId, int $restaurantId, string $reply): bool
{
    $sql = "UPDATE reviews
            SET restaurant_reply = :reply,
                replied_at = NOW(),
                updated_at = NOW()
            WHERE id = :review_id AND restaurant_id = :restaurant_id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':reply'         => $reply,
        ':review_id'     => $reviewId,
        ':restaurant_id' => $restaurantId
    ]);
}""",
        "explanation": "Updates reviews record by storing the official restaurant owner reply text and stamping the replied_at timestamp, scoped strictly to the restaurant owner's tenant ID.",
        "expected": "Enables two-way customer communication, displaying verified owner response badges directly under customer feedback cards."
    },
    {
        "filename": "public/assets/js/live-kitchen.js",
        "module": "Real-time Tracking",
        "purpose": "Client-side polling engine updating customer 5-stage progress timeline without page reload",
        "ref": "Lines 15–68",
        "snippet": """async function pollOrderStatus() {
    try {
        const resp = await fetch(`/api/orders/table-latest?table_id=${tableId}&restaurant_id=${restaurantId}`);
        const data = await resp.json();
        if (!data || !data.order_status) return;

        const stages = ['placed', 'accepted', 'preparing', 'ready', 'completed'];
        const currentIdx = stages.indexOf(data.order_status);

        stages.forEach((stage, idx) => {
            const el = document.getElementById(`step-${stage}`);
            if (!el) return;
            el.classList.remove('active', 'completed', 'pending');
            if (idx < currentIdx) {
                el.classList.add('completed');
            } else if (idx === currentIdx) {
                el.classList.add('active');
            } else {
                el.classList.add('pending');
            }
        });
    } catch (e) {
        console.error('Tracking poll error:', e);
    }
}
setInterval(pollOrderStatus, 5000);""",
        "explanation": "Executes asynchronous HTTP polling every 5 seconds against the latest table order endpoint. Updates the CSS classes of 5 timeline DOM nodes to reflect real-time kitchen progress.",
        "expected": "Provides seated diners with immediate visual feedback as their dishes are accepted, prepared, and plated by the kitchen."
    },
    {
        "filename": "app/Controllers/Owner/TableController.php",
        "module": "Table & QR Infrastructure",
        "purpose": "Sequential table management (1–12), cryptographic QR generation, and real-time occupancy toggling",
        "ref": "Lines 35–80",
        "snippet": """public function updateStatus(int $id): void
{
    $session = RestaurantMiddleware::handle();
    $restaurantId = $session['restaurant_id'];
    
    $newStatus = trim((string)Request::post('status', 'available'));
    $allowed = ['available', 'occupied', 'cleaning', 'out_of_service'];
    
    if (!in_array($newStatus, $allowed, true)) {
        Response::json(['success' => false, 'error' => 'Invalid table status'], 400);
        return;
    }

    $updated = $this->branchRepo->updateTableStatus($id, $restaurantId, $newStatus);
    Response::json(['success' => $updated, 'new_status' => $newStatus]);
}""",
        "explanation": "Enables restaurant hosts to click occupancy status pills on Tables 1–12 to immediately toggle status between 'available' and 'occupied' via AJAX.",
        "expected": "Updates physical dining room occupancy status in real time without refreshing the management portal view."
    },
    {
        "filename": "app/Controllers/Admin/RestaurantController.php",
        "module": "Multi-Tenant Governance",
        "purpose": "Super Admin restaurant lifecycle governance (Approved, Pending, Suspended) and deep portal inspection",
        "ref": "Lines 45–90",
        "snippet": """public function updateStatus(int $id): void
{
    AdminMiddleware::handle();
    $status = trim((string)Request::post('status', ''));
    $allowed = ['pending', 'approved', 'suspended'];

    if (!in_array($status, $allowed, true)) {
        Session::setFlash('error', 'Invalid restaurant status specified.');
        Response::redirect('/admin/restaurants');
        return;
    }

    $this->restaurantRepo->updateStatus($id, $status);
    Session::setFlash('success', "Restaurant tenancy status updated to {$status}.");
    Response::redirect('/admin/restaurants');
}""",
        "explanation": "Allows the Platform Super Admin to inspect restaurant tenant details, audit active branches, review food menus, and approve or suspend tenant access.",
        "expected": "Approved restaurants immediately go live for customer QR ordering; suspended restaurants are immediately blocked from public access."
    },
    {
        "filename": "app/Core/Validator.php",
        "module": "Data Integrity",
        "purpose": "Multi-rule server-side input sanitization, type verification, and error aggregation",
        "ref": "Lines 18–60",
        "snippet": """public function validate(array $data, array $rules): bool
{
    $this->errors = [];
    foreach ($rules as $field => $fieldRules) {
        $val = $data[$field] ?? null;
        foreach ($fieldRules as $rule) {
            if ($rule === 'required' && ($val === null || trim((string)$val) === '')) {
                $this->errors[$field][] = "The {$field} field is required.";
            } elseif ($rule === 'email' && $val && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = "The {$field} must be a valid email address.";
            } elseif ($rule === 'numeric' && $val !== null && !is_numeric($val)) {
                $this->errors[$field][] = "The {$field} must be numeric.";
            }
        }
    }
    return empty($this->errors);
}""",
        "explanation": "Enforces strict validation rules on all server-side form submissions across owner registration, menu creation, customer checkout, and profile updates.",
        "expected": "Prevents malformed data from reaching the repository layer and provides clear error feedback to users."
    }
]

print("doc_data.py configured successfully with 17 Data Dictionary tables and 18 Code Sections.")
