# HEALTHY BITE — HOW THE WHOLE PROJECT WORKS
### A Beginner-Friendly Technical Guide from Zero to Complete System

---

## 1. PROJECT IDENTITY & TECHNOLOGY AUDIT

Healthy Bite is a full-stack, web-based digital restaurant menu, QR ordering, and nutritional management platform designed for restaurants, cafes, and healthy food outlets.

### Real Project Technology Stack (Audited from Source Code)

* **Backend Language:** PHP (Version `>= 8.0`, with strict typing `declare(strict_types=1);` enabled on all core files).
* **Database Management System:** MySQL / MariaDB (using InnoDB storage engine, `utf8mb4` charset, and foreign key relational constraints).
* **Database Abstraction Layer:** PHP Data Objects (PDO) with persistent prepared statements and strict error mode (`PDO::ERRMODE_EXCEPTION`).
* **Software Architecture:** Custom Lightweight MVC (Model-View-Controller) utilizing the **Repository Pattern** and dedicated **Domain Services**.
* **Autoloading:** Custom PSR-4 Fallback Autoloader implemented in [public/index.php](file:///d:/NEW%20healthy%20bite/public/index.php) (Zero external runtime dependencies; does not require Composer `vendor/` to run).
* **HTTP Routing:** Front Controller routing engine with dynamic regular expression URL parameter matching (`App\Core\Router`).
* **Frontend Presentation:** Semantic HTML5, Modular CSS3 (custom CSS design system with CSS custom properties/variables), and Vanilla JavaScript (ES6+ with `fetch` API, no external heavyweight JS frameworks).
* **QR Token Engine:** Server-side cryptographic token validation paired with client-side SVG/Canvas rendering via [qrcode.min.js](file:///d:/NEW%20healthy%20bite/public/assets/js/qrcode.min.js).
* **Authentication & Session:** Native PHP Session Management (`App\Core\Session`) with password hashing via `password_hash()` / `password_verify()` using `PASSWORD_BCRYPT` (cost factor 12) and automatic session fixation regeneration.

> [!NOTE]
> **Component Audit Note:**
> * Traditional Active Record Models (where every database table is mapped to an ORM class) are **not present in the current implementation**. Instead, Healthy Bite uses clean **Repository classes** (`app/Repositories/`) and **Domain Service classes** (`app/Services/`), which separates SQL querying from business logic.
> * Third-party PHP frameworks (Laravel, Symfony, CodeIgniter) are **not used**. The framework is 100% custom-built, lightweight, transparent, and ideal for learning how web applications actually work from scratch.

---

## 2. EXPLAINING HEALTHY BITE LIKE I AM A 100% BEGINNER

Before looking at any code, let us understand what every technical term means using simple real-world analogies:

### Website
* **Simple Meaning:** A collection of digital pages connected together that you can view on your screen.
* **Why Healthy Bite needs it:** Customers need a place to view the food menu, choose dishes, and place orders without waiting for a waiter.
* **Healthy Bite Example:** When you open `http://localhost:8000/menu`, you see dishes like "Quinoa Power Bowl" and "Keto Avocado Salad".

### Server
* **Simple Meaning:** A powerful computer program that stays on 24/7, listens for visitors, and sends back the requested web pages.
* **Why Healthy Bite needs it:** The restaurant menu and orders must be processed, stored, and sent to whoever asks for them.
* **Healthy Bite Example:** Apache (inside XAMPP) or the PHP built-in development server running on port 8000.

### Browser
* **Simple Meaning:** The app on your laptop or phone (like Google Chrome, Safari, or Microsoft Edge) that downloads code from the server and draws it nicely on your screen.
* **Why Healthy Bite needs it:** The customer uses their phone browser after scanning a QR code to view the menu.

### PHP (Hypertext Preprocessor)
* **Simple Meaning:** The programming language that runs on the server. It decides what HTML to build and talks directly to the database.
* **Why Healthy Bite needs it:** The browser cannot talk directly to MySQL for security reasons. PHP acts as the brain in the middle.
* **Healthy Bite Example:** When an order is placed, [app/Controllers/OrderController.php](file:///d:/NEW%20healthy%20bite/app/Controllers/OrderController.php) validates the price and saves it into the database.

### MySQL / Database
* **Simple Meaning:** A digital filing cabinet with organized folders (tables) where all information is stored permanently.
* **Why Healthy Bite needs it:** If the server is turned off and on again, menu items, prices, user accounts, and past orders must never be lost.
* **Healthy Bite Example:** The `healthy_bite` database stored inside MariaDB.

### Table, Record & Query
* **Table:** A spreadsheet inside the database (e.g., `food_items`).
* **Record:** A single row in that spreadsheet (e.g., Row 1: "Green Protein Smoothie, ₹180, 240 Calories").
* **Query:** A command written in SQL asking the database to find, save, or change information (e.g., `SELECT * FROM food_items WHERE is_available = 1`).

### URL & Route
* **URL (Uniform Resource Locator):** The web address you type in (e.g., `http://localhost:8000/owner/orders`).
* **Route:** A rule inside the PHP code that says: *"When someone visits this specific URL, run this specific function."*
* **Healthy Bite Example:** In [routes/web.php](file:///d:/NEW%20healthy%20bite/routes/web.php), `/owner/orders` is routed to `App\Controllers\Owner\OrderController::index()`.

### Controller, Model/Repository & View (MVC)
* **Controller:** The receptionist or manager. It receives the visitor's request, asks the kitchen (Repository) for data, and hands it to the decorator (View).
* **Repository (Model Layer):** The pantry or warehouse manager. It knows how to write SQL queries to pull data from or insert data into MySQL.
* **View:** The painter or decorator. It takes raw numbers and text from the Controller and generates the final HTML page that looks beautiful.

### Session & Authentication vs. Authorization
* **Session:** A temporary memory card kept on the server for each visitor (e.g., keeping the restaurant owner logged in as they click between pages).
* **Authentication:** Checking *"Who are you?"* (e.g., verifying email `owner@greenhousekitchen.com` and password).
* **Authorization:** Checking *"What are you allowed to do?"* (e.g., a waiter cannot delete the restaurant; a customer cannot see another customer's payment history).

### API & JSON
* **API (Application Programming Interface):** A window where programs exchange raw data instead of full web pages.
* **JSON (JavaScript Object Notation):** A standard text format for packaging data (like `{ "name": "Brown Rice Bowl", "price": 180 }`).
* **Healthy Bite Example:** When a customer clicks "Add to Cart", the browser sends a quick background JSON request to `/api/cart/validate` to confirm the price.

### PDO (PHP Data Objects)
* **Simple Meaning:** A secure, standard translator inside PHP that communicates with database systems like MySQL.
* **Why Healthy Bite needs it:** It prevents hackers from injecting dangerous SQL commands (SQL Injection) by using **Prepared Statements**.

---

## 3. WHAT PROBLEM DOES HEALTHY BITE SOLVE?

1. **Nutritional Blindness in Traditional Dining:** Traditional paper menus show food names and prices, but conceal calories, carbohydrates, fats, proteins, sodium, and allergens. Health-conscious diners, diabetics, and fitness enthusiasts cannot make informed choices.
2. **Order Errors & Wait Times:** Waiting for waitstaff to bring menus, take orders manually, and write them on paper slips causes human error and dining delays.
3. **Menu Inflexibility:** Updating paper menus when an ingredient is out of stock is expensive and slow. Healthy Bite allows restaurant owners to toggle dish availability instantly with one click.
4. **Kitchen Bottlenecks:** Healthy Bite includes a real-time **Kitchen Live Orders (KDS Kanban)** view where chefs see orders appear instantly as `placed`, advance them to `preparing`, and mark them `ready` for service.

---

## 4. COMPLETE SYSTEM OVERVIEW & ARCHITECTURE

Healthy Bite follows a modern web architecture where all incoming web traffic is funneled through a single front controller:

```text
[Customer / Staff / Owner / Admin]
                │
                ▼ (Scans QR or opens URL in Browser)
         [Web Browser]
                │
                ▼ (HTTP GET / POST Request)
        [Apache / Web Server]
                │  (Rewrites request via .htaccess)
                ▼
      [public/index.php] <── Entry Point & PSR-4 Autoloader
                │
                ▼
       [App\Core\Router] <── Matches URL against web.php / api.php
                │
       ┌────────┴──────────────────────────┐
       ▼                                   ▼
 [Web Controller]                    [API Controller]
       │                                   │
       ▼                                   ▼
[Domain Services] ── (PricingService, NutritionService, QrService)
       │
       ▼
 [Repositories]   ── (FoodRepository, OrderRepository, etc.)
       │
       ▼
  [App\Core\Database] (Singleton PDO Instance)
       │
       ▼
[MySQL Database] (17 Relational Tables: orders, food_items, etc.)
       │
       ▼ (Returns Structured Data)
 [Repositories]
       │
       ▼
  [Controller]
       │
       ├─────────────────────────────────┐
       ▼                                 ▼
 (Web: HTML View + Layout)       (API: JSON Response)
       │                                 │
       └────────────────┬────────────────┘
                        ▼
                 [Web Browser]
```

### Complete Request/Response Cycle

```text
User Action (Click/Scan)
       │
       ▼
Browser sends HTTP Request: GET /menu?token=hb_table_1_tok
       │
       ▼
Apache receives request -> uses .htaccess -> executes public/index.php
       │
       ▼
index.php initializes App\Core\App -> starts Session -> registers Autoloader
       │
       ▼
App\Core\Router matches URL against routes/web.php -> identifies MenuController::index()
       │
       ▼
MenuController calls QrService::resolveToken('hb_table_1_tok')
       │
       ▼
QrService queries qr_tokens, restaurant_tables, branches, restaurants via PDO
       │
       ▼
Database returns verified restaurant_id = 1, branch_id = 1, table_id = 1
       │
       ▼
MenuController fetches categories and food items via FoodRepository
       │
       ▼
Controller renders 'resources/views/customer/menu.php' wrapped in 'layouts/customer.php'
       │
       ▼
Server sends 200 OK with complete HTML/CSS/JS payload to Browser
       │
       ▼
Browser parses HTML, applies styles, runs menu.js and cart.js for user interaction
```

---

## 5. COMPLETE FOLDER STRUCTURE

Healthy Bite is organized cleanly into standard production directories:

```text
d:\NEW healthy bite\
│
├── app/                      <-- All Backend Application Code
│   ├── Controllers/          <-- Request handlers (Customer, Owner, Admin)
│   │   ├── Admin/            <-- Super Admin portal controllers
│   │   ├── Owner/            <-- Restaurant owner & kitchen controllers
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── FoodController.php
│   │   ├── HomeController.php
│   │   ├── MenuController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   └── RestaurantController.php
│   ├── Core/                 <-- Framework foundations (Router, Database, Session, etc.)
│   ├── Helpers/              <-- Global utility functions (url, format, security, food)
│   ├── Middleware/           <-- Route access guards (Auth, Admin, Restaurant)
│   ├── Repositories/         <-- Database queries (PDO Repository Pattern)
│   └── Services/             <-- Business logic (Pricing, Nutrition, QR, Orders)
│
├── config/                   <-- System configuration files (app, database, constants)
│   ├── app.php
│   ├── constants.php
│   └── database.php
│
├── database/                 <-- Schemas, migrations, seeders, and SQL dumps
│   ├── healthy_bite_infinityfree_dump.sql  <-- Clean production SQL dump
│   ├── schema/
│   ├── seeders/
│   └── migrations/
│
├── public/                   <-- Web root directory (The ONLY folder exposed to visitors)
│   ├── .htaccess             <-- URL rewrite rules for public folder
│   ├── index.php             <-- THE PRIMARY APPLICATION ENTRY POINT
│   └── assets/               <-- Static assets served to the browser
│       ├── css/              <-- Stylesheets
│       ├── js/               <-- Frontend JavaScript logic
│       └── images/           <-- Logos, food imagery, icons
│
├── resources/                <-- Views and presentation templates
│   └── views/
│       ├── admin/            <-- Super Admin dashboard views
│       ├── customer/         <-- Customer-facing menu, cart, tracking views
│       ├── layouts/          <-- Master HTML layout shells (customer, owner, admin)
│       ├── owner/            <-- Restaurant owner & live kitchen views
│       └── partials/         <-- Reusable UI snippets (navbars, modals, headers)
│
├── routes/                   <-- URL Route Definitions
│   ├── api.php               <-- JSON API endpoints (/api/foods, /api/orders, etc.)
│   └── web.php               <-- Browser page routes (/menu, /owner/dashboard, etc.)
│
├── storage/                  <-- Application storage (logs, temporary sessions)
│   └── logs/                 <-- app.log error and exception records
│
├── .env                      <-- Local environment configuration (credentials)
├── .env.example              <-- Template for creating .env
└── .htaccess                 <-- Root rewrite rule (routes requests into /public)
```

### Folder Breakdown Table

| Directory | Purpose | Important Files | Used By | Beginner Explanation |
| :--- | :--- | :--- | :--- | :--- |
| **`app/Core/`** | The engine of the framework | `App.php`, `Router.php`, `Database.php`, `Request.php`, `Response.php` | The entire project | The "chassis and engine" of the car that powers everything. |
| **`app/Controllers/`** | Handles user requests | `MenuController.php`, `OrderController.php`, `Owner/OrderController.php` | The Router | The "waiters" that take requests from customers and tell the kitchen what to do. |
| **`app/Repositories/`**| Talks directly to MySQL | `FoodRepository.php`, `OrderRepository.php`, `RestaurantRepository.php` | Controllers & Services | The "storekeepers" who know exactly how to fetch or store data in the database. |
| **`app/Services/`** | Pure calculation & rules | `NutritionService.php`, `PricingService.php`, `QrService.php` | Controllers | The "calculators" that do math on calories, prices, taxes, and QR codes. |
| **`app/Middleware/`** | Security guards | `AuthMiddleware.php`, `RestaurantMiddleware.php` | Controllers | The "security guards at the door" checking if you have a valid ID badge. |
| **`config/`** | Global settings | `constants.php`, `database.php`, `app.php` | Core & Repositories | The "rulebook" defining database settings, tax rates, and role IDs. |
| **`public/`** | The public website door | `index.php`, `assets/js/cart.js`, `assets/css/` | Web Browser | The only room guests are allowed into. All other folders are locked behind the front desk. |
| **`resources/views/`** | HTML templates | `customer/menu.php`, `owner/kitchen_orders.php`, `layouts/` | Controllers | The "visual stage" where data is rendered into clean HTML for people to read. |
| **`routes/`** | Address book of URLs | `web.php`, `api.php` | Router | The signpost map that tells PHP which controller to run when a URL is requested. |

---

## 6. COMPLETE FILE-BY-FILE GUIDE (THE CORE APPLICATION)

### 1. [public/index.php](file:///d:/NEW%20healthy%20bite/public/index.php)
* **Exact Path:** `public/index.php`
* **Purpose:** The single entry point for 100% of all incoming requests to Healthy Bite.
* **What happens inside it:**
  1. Detects PHP CLI built-in web server static files.
  2. Defines the global application root constant `define('HB_ROOT', dirname(__DIR__));`.
  3. Registers the **PSR-4 fallback autoloader** to automatically load classes from the `app/` folder without manual `require` statements.
  4. Loads helper functions ([app/Helpers/url.php](file:///d:/NEW%20healthy%20bite/app/Helpers/url.php), [security.php](file:///d:/NEW%20healthy%20bite/app/Helpers/security.php), etc.) and constants ([config/constants.php](file:///d:/NEW%20healthy%20bite/config/constants.php)).
  5. Reads environment variables from `.env` via `App\Core\Env::load()`.
  6. Initializes `new App\Core\App()`.
  7. Loads routes from [routes/web.php](file:///d:/NEW%20healthy%20bite/routes/web.php) and [routes/api.php](file:///d:/NEW%20healthy%20bite/routes/api.php).
  8. Calls `$app->run()`, which dispatches the route.
* **What would break if changed incorrectly:** The entire website will stop working completely; any syntax error here produces a blank screen or a 500 error.

### 2. [app/Core/App.php](file:///d:/NEW%20healthy%20bite/app/Core/App.php)
* **Exact Path:** `app/Core/App.php`
* **Purpose:** Initializes application state, starts the session, sets up custom error/exception handlers, and kicks off routing.
* **What happens inside it:** Registers a global error handler that catches PHP errors and logs them to `storage/logs/app.log`. If an error occurs, it cleanly shows a friendly 500 page to customers or a JSON error to API callers, rather than exposing raw server errors to users.

### 3. [app/Core/Router.php](file:///d:/NEW%20healthy%20bite/app/Core/Router.php)
* **Exact Path:** `app/Core/Router.php`
* **Purpose:** Matches the incoming HTTP request method (`GET`, `POST`) and URL path against registered routes.
* **What happens inside it:** Converts dynamic route patterns like `/qr/{token}` or `/owner/orders/{id}/details` into regular expressions: `#^/qr/(?P<token>[^/]+)$#`. When matched, it extracts the parameters and calls the target controller method with them.

### 4. [app/Core/Database.php](file:///d:/NEW%20healthy%20bite/app/Core/Database.php)
* **Exact Path:** `app/Core/Database.php`
* **Purpose:** Manages the single shared PDO connection to MySQL using the **Singleton Pattern**.
* **What happens inside it:** Checks if a connection `$instance` already exists. If not, reads [config/database.php](file:///d:/NEW%20healthy%20bite/config/database.php), builds the DSN (`mysql:host=...;dbname=...;charset=utf8mb4`), and creates a PDO object with strict exceptions enabled.
* **Why it matters:** Ensures the server creates only **one** database connection per request instead of opening dozens of connections, which keeps the application fast and prevents server memory overload.

### 5. [app/Services/NutritionService.php](file:///d:/NEW%20healthy%20bite/app/Services/NutritionService.php)
* **Exact Path:** `app/Services/NutritionService.php`
* **Purpose:** The central mathematical engine for all calorie, macronutrient, and micronutrient calculations.
* **What happens inside it:** Calculates the nutritional profile of an item:
  $$\text{Final Nutrient} = \text{Base Nutrient} + \text{Variant Adjustment} + \sum (\text{Customization Adjustment} \times \text{Quantity})$$
  Supports: `calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `sodium`, and `caffeine`. Preserves `null` if data was never provided to avoid misleading customers with fake "0" values.

### 6. [app/Services/PricingService.php](file:///d:/NEW%20healthy%20bite/app/Services/PricingService.php)
* **Exact Path:** `app/Services/PricingService.php`
* **Purpose:** The authoritative financial engine for calculating food item prices and order bill totals.
* **What happens inside it:**
  * Calculates Single Item Price: $\text{Base Price} + \text{Variant Price Adjustment} + \sum (\text{Customization Price} \times \text{Qty})$
  * Calculates Line Total: $\text{Single Item Price} \times \text{Food Quantity}$
  * Calculates Order Total: $\text{Subtotal} + \text{Tax (5\%)} + \text{Service Charge (0\%)}$

### 7. [app/Services/QrService.php](file:///d:/NEW%20healthy%20bite/app/Services/QrService.php)
* **Exact Path:** `app/Services/QrService.php`
* **Purpose:** Resolves encrypted/random table QR tokens into verified database records.
* **What happens inside it:** Executes a multi-table `INNER JOIN` query across `qr_tokens`, `restaurant_tables`, `branches`, and `restaurants`. Validates that the token exists, is marked `'active'`, has not expired, and belongs to an approved restaurant and active branch.

### 8. [app/Services/OrderService.php](file:///d:/NEW%20healthy%20bite/app/Services/OrderService.php)
* **Exact Path:** `app/Services/OrderService.php`
* **Purpose:** Orchestrates complete order placement, cart re-validation, customer assignment, unique order number generation, and snapshot persistence.

---

## 7. PSR-4 AUTOLOADING EXPLAINED

In traditional beginner PHP projects, every file starts with a long, messy list of manual imports:
```php
// The old, fragile way (NOT used in Healthy Bite):
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Repositories/FoodRepository.php';
require_once __DIR__ . '/../Services/NutritionService.php';
```
If you move a file or forget an include, PHP crashes with `Fatal error: Class not found`.

Healthy Bite solves this using a **PSR-4 Compliant Autoloader** in [public/index.php](file:///d:/NEW%20healthy%20bite/public/index.php#L15-L30):

```php
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = HB_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
```

### How It Works:
1. When PHP encounters a class name like `App\Repositories\FoodRepository`, PHP pauses and hands the name to this function.
2. The function strips off `App\` and replaces the remaining backslashes `\` with folder slashes `/`.
3. It looks for the file: `d:\NEW healthy bite/app/Repositories/FoodRepository.php`.
4. If found, it loads it automatically. You never have to write a manual `require_once` for any class!

---

## 8. ROUTING SYSTEM & COMPLETE ROUTE TABLE

Routing separates what the URL looks like in the browser from where the PHP code physically lives on the hard drive. All routes are registered in [routes/web.php](file:///d:/NEW%20healthy%20bite/routes/web.php) and [routes/api.php](file:///d:/NEW%20healthy%20bite/routes/api.php).

### Complete Route Master Directory

| HTTP Method | Route URL Pattern | Controller & Method | Purpose / User Journey |
| :--- | :--- | :--- | :--- |
| **GET** | `/` or `/menu/welcome` | `HomeController::index` | Landing & table QR welcome page |
| **GET** | `/menu` | `MenuController::index` | Full interactive customer menu |
| **GET** | `/table/{tableNumber}` | `MenuController::resolveTableNumber` | Direct table number shortcut |
| **GET** | `/qr/{token}` or `/t/{token}` | `MenuController::resolveQr` | QR scan entry point (validates token & loads menu) |
| **GET** | `/menu/checkout` | `CheckoutController::index` | Customer cart review & order checkout page |
| **GET** | `/menu/confirmation/{orderNumber}` | `OrderController::confirmation` | Post-order success & receipt page |
| **GET** | `/menu/tracking/{orderNumber}` | `OrderController::tracking` | Real-time live status tracking for customer |
| **GET** | `/owner/login` | `Owner\AuthController::loginForm` | Restaurant owner portal login page |
| **POST**| `/owner/login` | `Owner\AuthController::login` | Authenticate owner credentials |
| **GET** | `/owner/logout` | `Owner\AuthController::logout` | Terminate session & redirect to login |
| **GET** | `/owner/dashboard` | `Owner\DashboardController::index`| Overview metrics (Revenue, Active Orders, Table Status) |
| **GET** | `/owner/orders` | `Owner\OrderController::index` | Monitoring table of all restaurant orders |
| **GET** | `/owner/kitchen-orders` | `Owner\OrderController::kitchenOrders` | **Kitchen KDS Kanban Board** (Placed -> Preparing -> Ready) |
| **POST**| `/owner/orders/{id}/status` | `Owner\OrderController::updateStatus` | Advance order status (Ajax or Form) |
| **POST**| `/owner/orders/{id}/payment`| `Owner\OrderController::updatePayment`| Mark payment as completed or failed |
| **GET** | `/owner/menu` | `Owner\MenuController::index` | Menu management (categories, food items, prices) |
| **POST**| `/owner/menu/{id}/toggle` | `Owner\MenuController::toggleAvailability` | 1-click In-Stock / Out-of-Stock toggle |
| **GET** | `/owner/tables` | `Owner\TableController::index` | Dining table management & QR code generator |
| **GET** | `/owner/analytics` | `Owner\AnalyticsController::index` | Macro nutrition & sales charts |
| **GET** | `/admin/login` | `Admin\AuthController::loginForm` | Super Admin platform login |
| **GET** | `/admin/dashboard` | `Admin\DashboardController::index`| Platform-wide overview (total restaurants, tenants) |
| **GET** | `/admin/restaurants` | `Admin\RestaurantController::index`| Super admin restaurant approvals/status |
| **GET** | `/api/health` | Closure in `api.php` | System health check (database ping) |
| **GET** | `/api/foods` | `RestaurantController::getFoods` | JSON endpoint for foods with nutrition |
| **GET** | `/api/foods/{id}/variants` | `FoodController::variants` | JSON endpoint for food size/portion options |
| **GET** | `/api/foods/{id}/customizations` | `FoodController::customizations` | JSON endpoint for add-ons (extra protein, dressing) |
| **POST**| `/api/cart/validate` | `CartController::validateCart` | Backend cart verification before checkout |
| **POST**| `/api/orders` | `OrderController::createOrder` | Submit final order payload & generate order number |
| **GET** | `/api/orders/{orderNumber}` | `OrderController::getOrder` | Polling endpoint for live order status |
| **POST**| `/api/payment/simulate` | `PaymentController::simulate` | Simulated payment gateway (success/failure) |

---

## 9. COMPLETE DATABASE SCHEMA (ALL 17 TABLES)

The database schema is strictly normalized and enforced with foreign keys. The actual table names from the database dump are:

```text
[restaurants] ──< [branches] ──< [restaurant_tables] ──< [qr_tokens]
      │               │
      │               └──< [orders] ──< [order_items] ──< [order_item_customizations]
      │                       │              │
      │                       ├── [customers]│
      │                       └── [payments] └── [food_items]
      │                                                │
      ├──< [categories] ───────────────────────────────┤
      └──< [users] >── [roles]                         ├──< [food_variants]
                                                       └──< [food_customizations]
```

### Table Specifications

#### 1. `restaurants`
* **Purpose:** Stores the top-level restaurant enterprise identity (e.g., "The Greenhouse Kitchen").
* **Key Columns:** `id` (PK), `name`, `slug`, `email`, `phone`, `logo_url`, `status` (`'pending'`, `'approved'`, `'rejected'`), `created_at`.
* **Who accesses it:** Admin approves/edits; Owner views profile; Customers read name/branding.

#### 2. `branches`
* **Purpose:** Supports multi-location restaurant chains.
* **Key Columns:** `id` (PK), `restaurant_id` (FK), `name` (e.g., "Downtown Branch"), `address`, `phone`, `status` (`'active'`, `'inactive'`).

#### 3. `restaurant_tables`
* **Purpose:** Physical dining tables inside a branch.
* **Key Columns:** `id` (PK), `branch_id` (FK), `table_number` (e.g., "T-01"), `capacity` (e.g., 4), `status` (`'available'`, `'occupied'`, `'cleaning'`, `'out_of_service'`).

#### 4. `qr_tokens`
* **Purpose:** Stores the unique cryptographic access tokens linked to physical dining tables.
* **Key Columns:** `id` (PK), `table_id` (FK), `token` (e.g., `hb_greenhouse_downtown_t1_sec`), `status` (`'active'`, `'inactive'`), `expires_at`.

#### 5. `categories`
* **Purpose:** Food grouping for navigation (e.g., "Salad Bowls", "Smoothies", "Warm Protein Plates").
* **Key Columns:** `id` (PK), `restaurant_id` (FK), `name`, `description`, `display_order`, `is_active`.

#### 6. `food_items`
* **Purpose:** The core meal catalog containing base nutritional specifications and pricing.
* **Key Columns:** `id` (PK), `restaurant_id` (FK), `category_id` (FK), `name`, `description`, `base_price`, `calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `sodium`, `caffeine`, `dietary_flags` (JSON or tags: vegan, keto, gluten-free), `is_available`, `image_url`.

#### 7. `food_variants`
* **Purpose:** Portion or size choices for a food item (e.g., "Regular Bowl" vs. "Large Athlete Portion").
* **Key Columns:** `id` (PK), `food_item_id` (FK), `name`, `price_adjustment` (e.g., `+40.00`), `calories_adjustment` (e.g., `+120`), `protein_adjustment` (e.g., `+14.0`).

#### 8. `food_customizations`
* **Purpose:** Optional add-ons or ingredient modifications (e.g., "Extra Grilled Tofu", "Olive Oil Dressing").
* **Key Columns:** `id` (PK), `food_item_id` (FK), `name`, `price_adjustment`, `calories_adjustment`, `protein_adjustment`, `is_available`.

#### 9. `customers`
* **Purpose:** Records diner contact information for order communication and receipts.
* **Key Columns:** `id` (PK), `name`, `mobile`, `email`, `created_at`.

#### 10. `orders`
* **Purpose:** The master record of a dining order.
* **Key Columns:** `id` (PK), `order_number` (Unique, e.g., `HB-184E9-421`), `restaurant_id` (FK), `branch_id` (FK), `table_id` (FK, nullable for takeaway), `customer_id` (FK), `order_type` (`'dine_in'`, `'takeaway'`), `subtotal`, `tax`, `service_charge`, `total_amount`, `order_status` (`'placed'`, `'accepted'`, `'preparing'`, `'ready'`, `'completed'`, `'cancelled'`), `payment_status` (`'pending'`, `'completed'`, `'failed'`), `notes`, `created_at`.

#### 11. `order_items`
* **Purpose:** Individual dishes within an order, stored with **historical snapshots**.
* **Key Columns:** `id` (PK), `order_id` (FK), `food_item_id` (FK), `food_name_snapshot`, `base_price_snapshot`, `variant_name_snapshot`, `quantity`, `unit_price`, `total_price`, `calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `sodium`, `caffeine`.

#### 12. `order_item_customizations`
* **Purpose:** Snapshot of exact add-ons selected for a specific order item.
* **Key Columns:** `id` (PK), `order_item_id` (FK), `customization_id` (FK), `customization_name_snapshot`, `quantity`, `price_adjustment`, `calories_adjustment`, `protein_adjustment`, `carbs_adjustment`, `fat_adjustment`.

#### 13. `payments`
* **Purpose:** Transaction ledger recording payment method and gateway references.
* **Key Columns:** `id` (PK), `order_id` (FK), `amount`, `payment_method` (`'card'`, `'upi'`, `'cash'`, `'net_banking'`), `transaction_reference`, `status` (`'pending'`, `'completed'`, `'failed'`), `paid_at`.

#### 14. `reviews`
* **Purpose:** Customer feedback and star ratings on dining experience.
* **Key Columns:** `id` (PK), `restaurant_id` (FK), `order_id` (FK), `rating` (1 to 5), `comment`, `response_comment`, `created_at`.

#### 15. `roles`
* **Purpose:** Role-based access definitions.
* **Records:** `1 = Platform Super Admin`, `2 = Restaurant Owner`, `3 = Restaurant Manager`, `4 = Kitchen/Service Staff`.

#### 16. `users`
* **Purpose:** Staff, Owner, and Administrator login credentials.
* **Key Columns:** `id` (PK), `role_id` (FK), `restaurant_id` (FK, nullable for Super Admin), `name`, `email`, `password` (bcrypt hash), `status` (`'active'`, `'inactive'`).

#### 17. `admin`
* **Purpose:** System-level configuration and platform audit entity.

---

## 10. CUSTOMER FLOW: FROM QR SCAN TO COMPLETED ORDER (STEP-BY-STEP)

```text
Step 1: Scan Table QR
Customer points smartphone camera at QR code on Table 3.
URL encoded in QR: https://yourdomain.com/qr/hb_greenhouse_downtown_t3_sec

Step 2: Server Token Resolution
The request hits public/index.php -> MenuController::resolveQr().
QrService executes SQL query on `qr_tokens` table.
Verifies token is 'active' and not expired.

Step 3: Session & Context Locking
The server identifies:
Restaurant ID = 1 ("The Greenhouse Kitchen")
Branch ID     = 1 ("Downtown Branch")
Table ID      = 3 ("Table 03")
Stores this verified context in session: $_SESSION['qr_context'].

Step 4: Menu Render
MenuController::index() fetches active categories and dishes.
Renders resources/views/customer/menu.php.
The customer sees categories ("Bowls", "Smoothies", "Salads").

Step 5: Food Detail Modal
Customer clicks "Avocado Quinoa Bowl".
Frontend JavaScript (food-details.js) opens a modal.
Fetches /api/foods/2/variants and /api/foods/2/customizations.

Step 6: Real-Time Nutrition & Price Calculation
Customer selects:
- Variant: "Large Athlete Size" (+₹40, +120 kcal, +14g Protein)
- Customization: "Extra Boiled Egg" (+₹25, +75 kcal, +6g Protein)
JavaScript dynamically recalculates badges on screen:
Price: ₹220 + ₹40 + ₹25 = ₹285
Calories: 450 + 120 + 75 = 645 kcal
Protein: 18g + 14g + 6g = 38g

Step 7: Add to Cart
Customer clicks "Add to Cart".
cart.js stores item in browser localStorage and updates the floating cart counter.

Step 8: Cart Validation
Customer clicks "Proceed to Checkout" (/menu/checkout).
Frontend sends cart payload to POST /api/cart/validate.
CartService re-verifies prices and availability in MySQL to ensure
no customer manipulated prices using browser developer tools.

Step 9: Order Submission
Customer enters Name ("Aarav") and Mobile, selects "Dine In", and clicks "Place Order".
POST /api/orders receives payload.
OrderService generates unique order number: "HB-192B-847".

Step 10: Database Transaction & Snapshot Creation
1. Inserts into `customers` -> creates customer_id.
2. Inserts into `orders` -> creates order record (status = 'placed').
3. Inserts into `order_items` -> freezes dish name, price, and macro calories snapshot.
4. Inserts into `order_item_customizations` -> freezes add-ons snapshot.

Step 11: Kitchen Notification & Live Tracking
Customer is redirected to /menu/tracking/HB-192B-847.
order.js polls /api/orders/HB-192B-847 every 5 seconds.
Simultaneously, the order appears live on the Owner's Kitchen Kanban Board (/owner/kitchen-orders)!
```

---

## 11. RESTAURANT OWNER & KITCHEN STAFF PORTAL

The owner management interface is protected by [app/Middleware/RestaurantMiddleware.php](file:///d:/NEW%20healthy%20bite/app/Middleware/RestaurantMiddleware.php), which verifies that:
1. The user is logged in (`Auth::check()`).
2. The user has role `ROLE_RESTAURANT_OWNER`, `ROLE_MANAGER`, or `ROLE_SUPER_ADMIN`.
3. The user is scoped strictly to their own restaurant ID (`$user['restaurant_id']`). A restaurant owner can never view or modify data belonging to another restaurant.

### Kitchen Live Orders (KDS Kanban Board)
Kitchen staff use the dedicated kitchen dashboard at `/owner/kitchen-orders` ([resources/views/owner/kitchen_orders.php](file:///d:/NEW%20healthy%20bite/resources/views/owner/kitchen_orders.php)):

| Kanban Column | Order Status | Meaning | Action Taken |
| :--- | :--- | :--- | :--- |
| **Column 1: New Orders** | `placed` | Customer just submitted order from table | Chef clicks **"Accept Order"** |
| **Column 2: In Preparation**| `preparing` | Kitchen is cooking the meal | Chef clicks **"Mark as Ready"** |
| **Column 3: Ready to Serve**| `ready` | Dish is plated; ready for waiter pickup | Waiter serves food to table, clicks **"Complete"** |
| **Column 4: Completed** | `completed` | Meal served and bill settled | Archived into daily revenue reports |

When the chef clicks a button, a background `fetch()` request is sent to `POST /owner/orders/{id}/status`. The database updates instantly, and the customer's phone screen tracking the order moves from "Chef is preparing your meal" to "Order is Ready!" without anyone refreshing a page.

---

## 12. LOCALHOST EXECUTION GUIDE: ZERO TO RUNNING

### Required Software
1. **XAMPP** (Provides Apache web server, PHP 8.1+, and MariaDB/MySQL server).
2. **Web Browser** (Chrome, Edge, Firefox).
3. **Code Editor** (Visual Studio Code recommended).

### Why Do We Run Locally First?
* **Safety:** If a bug causes an error, it only happens on your laptop, not in front of real customers.
* **Speed:** Changes saved in VS Code are immediately visible on your browser upon refresh (zero file upload wait time).
* **Cost:** You can develop, test, and polish the complete platform for 0 dollars without needing domain names or paid hosting.
* **Database Inspection:** You have complete access to phpMyAdmin to inspect rows, debug foreign keys, and test queries.

---

### Step-by-Step Installation & Run Checklist

#### Step 1: Open XAMPP Control Panel
* Start **Apache** (Wait until PID and Port `80, 443` turn green).
* Start **MySQL** (Wait until PID and Port `3306` turn green).

#### Step 2: Create & Import the Database
1. Open your browser and navigate to: `http://localhost/phpmyadmin/`
2. Click **New** in the left sidebar.
3. Enter Database name: `healthy_bite` and choose Collation: `utf8mb4_unicode_ci`. Click **Create**.
4. With `healthy_bite` selected in the sidebar, click the **Import** tab at the top.
5. Click **Choose File** and select:
   `d:\NEW healthy bite\database\healthy_bite_infinityfree_dump.sql`
6. Click **Import** at the bottom.
7. Verify all 17 tables appear in the left sidebar.

#### Step 3: Configure `.env`
Ensure your local `d:\NEW healthy bite\.env` file matches your local XAMPP setup:
```dotenv
APP_NAME="Healthy Bite"
APP_ENV=development
APP_URL=http://localhost:8000
APP_KEY=hb_secret_key_change_in_production

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthy_bite
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4

DEFAULT_RESTAURANT_ID=1
TAX_RATE=0.05
SERVICE_CHARGE=0.00
```

#### Step 4: Run the Application (Two Methods)

**Method A: Using PHP Built-In Web Server (Fastest & Recommended)**
1. Open a terminal (PowerShell or Command Prompt) in your project directory:
   ```powershell
   cd "d:\NEW healthy bite"
   php -S localhost:8000 -t public
   ```
2. Open your browser and visit: `http://localhost:8000`

**Method B: Using XAMPP Apache `htdocs`**
1. Copy or symlink your project into `C:\xampp\htdocs\healthybite`.
2. Visit: `http://localhost/healthybite/` (Our root [`.htaccess`](file:///d:/NEW%20healthy%20bite/.htaccess) automatically routes traffic into `public/`).

---

## 13. DEMO LOGIN CREDENTIALS & TEST SCENARIOS

All demo passwords in the seed database are: `Password@123`

| Role | Login URL | Email | Password |
| :--- | :--- | :--- | :--- |
| **Restaurant Owner** | `http://localhost:8000/owner/login` | `owner@greenhousekitchen.com` | `Password@123` |
| **Platform Super Admin** | `http://localhost:8000/admin/login` | `admin@healthybite.com` | `Password@123` |
| **Restaurant Staff / Chef** | `http://localhost:8000/owner/login` | `aarav@greenhouse.in` | `Password@123` |

### 3 Quick Manual Test Scenarios

#### Test 1: Customer Table QR Ordering
1. Open URL: `http://localhost:8000/qr/hb_greenhouse_downtown_t1_sec`
2. Notice the top banner shows: *"Table 01 — Downtown Branch"*.
3. Click on **"Quinoa Power Bowl"** -> Choose "Extra Avocado" -> Click "Add to Cart".
4. Click Cart Icon -> Click "Proceed to Checkout" -> Fill customer name -> Click "Place Order".
5. Observe the order confirmation number and live status timeline.

#### Test 2: Kitchen Live Orders (KDS Kanban)
1. In another browser tab, log in as Owner (`owner@greenhousekitchen.com` / `Password@123`).
2. Click **"Kitchen Live Orders"** in the sidebar (`/owner/kitchen-orders`).
3. You will see the order placed in Test 1 under the **"New Orders"** column!
4. Click **"Accept"** -> the card moves to **"Preparing"**.
5. Switch back to the customer tab: the status timeline instantly updates!

#### Test 3: Instant Menu Out-of-Stock Toggle
1. In the Owner Portal, click **"Menu & Foods"** (`/owner/menu`).
2. Find any dish and click the toggle switch to mark it **Unavailable**.
3. Open the customer menu: that dish now shows a grayed-out badge reading *"Sold Out"* and cannot be added to cart.

---

## 14. BEGINNER DEBUGGING & TROUBLESHOOTING GUIDE

| Problem / Error | Root Cause | Exact Solution |
| :--- | :--- | :--- |
| **Apache won't start in XAMPP** (Port 80 conflict) | Another app (Skype, VMware, or IIS) is using Port 80. | In XAMPP, click Apache `Config` -> `httpd.conf` -> Change `Listen 80` to `Listen 8080`, then access via `http://localhost:8080`. Or use `php -S localhost:8000 -t public`. |
| **MySQL won't start in XAMPP** | Corrupted log files or port 3306 blocked. | In XAMPP, click MySQL `Logs` -> check error. Never delete `ibdata1`. If a previous MySQL crashed, kill `mysqld.exe` in Windows Task Manager. |
| **"Could not connect to database"** | Incorrect password, wrong port, or MySQL server is turned off. | Open XAMPP, ensure MySQL is green. Check [`.env`](file:///d:/NEW%20healthy%20bite/.env): ensure `DB_HOST=127.0.0.1`, `DB_PORT=3306`, and `DB_PASSWORD=` is empty for default XAMPP. |
| **404 Not Found on Subpages** (e.g., `/menu`) | Apache `mod_rewrite` is disabled, or `.htaccess` is missing. | Ensure both [`.htaccess`](file:///d:/NEW%20healthy%20bite/.htaccess) in root and [`public/.htaccess`](file:///d:/NEW%20healthy%20bite/public/.htaccess) exist. If running PHP built-in server, make sure you ran with `-t public`. |
| **Blank White Screen** | Fatal PHP syntax error while `display_errors` is off. | Open [storage/logs/app.log](file:///d:/NEW%20healthy%20bite/storage/logs/app.log) to read the exact PHP exception and line number. |
| **QR Code says "Invalid or Expired"** | Token was typed incorrectly or database is missing seed rows. | Verify `qr_tokens` table in phpMyAdmin has rows where `status = 'active'`. Test with token `hb_greenhouse_downtown_t1_sec`. |

---

## 15. SAFE FILE MODIFICATION GUIDE

| Safety Level | Folder / Files | What It Contains | Rules For Beginners |
| :--- | :--- | :--- | :--- |
| 🟢 **SAFE TO EDIT** | `resources/views/`, `public/assets/css/`, `public/assets/images/` | HTML layouts, button colors, CSS styling, text labels | **Feel free to experiment.** You can change colors, typography, titles, and layout without breaking database operations. |
| 🟡 **EDIT CAREFULLY** | `app/Controllers/`, `app/Services/`, `routes/web.php`, `public/assets/js/` | Page navigation, formula math, cart operations | Keep backups before editing. Test thoroughly in the browser after changing routes or calculations. |
| 🔴 **DO NOT TOUCH CASUALLY** | `public/index.php`, `app/Core/`, `config/`, `.htaccess`, `.env` | Autoloader, routing engine, database connection, security guards | **Do not modify unless you understand the architecture completely.** A single broken semicolon here can take down the entire system. |

---

## 16. TOP 20 FILES I MUST UNDERSTAND

1. **[`public/index.php`](file:///d:/NEW%20healthy%20bite/public/index.php)** — The front door. Teaches you PSR-4 autoloading and request bootstrapping.
2. **[`app/Core/Router.php`](file:///d:/NEW%20healthy%20bite/app/Core/Router.php)** — Teaches you how URLs map to controller actions via regex.
3. **[`app/Core/Database.php`](file:///d:/NEW%20healthy%20bite/app/Core/Database.php)** — Teaches you the Singleton Pattern and secure PDO connection handling.
4. **[`routes/web.php`](file:///d:/NEW%20healthy%20bite/routes/web.php)** — The central roadmap of every browser page in the system.
5. **[`routes/api.php`](file:///d:/NEW%20healthy%20bite/routes/api.php)** — The roadmap of JSON endpoints used by JavaScript for dynamic updates.
6. **[`app/Services/NutritionService.php`](file:///d:/NEW%20healthy%20bite/app/Services/NutritionService.php)** — Teaches you the mathematical logic of nutritional breakdown and NULL handling.
7. **[`app/Services/PricingService.php`](file:///d:/NEW%20healthy%20bite/app/Services/PricingService.php)** — Teaches you financial order totals, taxes, and add-on price summation.
8. **[`app/Services/QrService.php`](file:///d:/NEW%20healthy%20bite/app/Services/QrService.php)** — Teaches you how QR tokens prevent table tampering via multi-table SQL joins.
9. **[`app/Services/OrderService.php`](file:///d:/NEW%20healthy%20bite/app/Services/OrderService.php)** — Teaches you the complete transaction pipeline from cart to frozen snapshots.
10. **[`app/Repositories/FoodRepository.php`](file:///d:/NEW%20healthy%20bite/app/Repositories/FoodRepository.php)** — Teaches you how SQL queries fetch categories, foods, and variants.
11. **[`app/Repositories/OrderRepository.php`](file:///d:/NEW%20healthy%20bite/app/Repositories/OrderRepository.php)** — Teaches you complex order insertion, customer creation, and status updates.
12. **[`app/Controllers/MenuController.php`](file:///d:/NEW%20healthy%20bite/app/Controllers/MenuController.php)** — Teaches you how customer menus are loaded and rendered.
13. **[`app/Controllers/OrderController.php`](file:///d:/NEW%20healthy%20bite/app/Controllers/OrderController.php)** — Teaches you how order submission and live tracking work.
14. **[`app/Controllers/Owner/OrderController.php`](file:///d:/NEW%20healthy%20bite/app/Controllers/Owner/OrderController.php)** — Teaches you the Kitchen Live Orders Kanban board logic.
15. **[`app/Middleware/RestaurantMiddleware.php`](file:///d:/NEW%20healthy%20bite/app/Middleware/RestaurantMiddleware.php)** — Teaches you role-based access control and multi-tenant restaurant scoping.
16. **[`config/constants.php`](file:///d:/NEW%20healthy%20bite/config/constants.php)** — Teaches you the master statuses for roles, tables, orders, and payments.
17. **[`public/assets/js/cart.js`](file:///d:/NEW%20healthy%20bite/public/assets/js/cart.js)** — Teaches you client-side state management using browser `localStorage`.
18. **[`public/assets/js/food-details.js`](file:///d:/NEW%20healthy%20bite/public/assets/js/food-details.js)** — Teaches you dynamic DOM manipulation for real-time calorie and price recalculation.
19. **[`resources/views/customer/menu.php`](file:///d:/NEW%20healthy%20bite/resources/views/customer/menu.php)** — Teaches you how PHP loops through categories and food cards.
20. **[`resources/views/owner/kitchen_orders.php`](file:///d:/NEW%20healthy%20bite/resources/views/owner/kitchen_orders.php)** — Teaches you the operational UI of the restaurant kitchen display system.

---

## 17. 10-DAY BEGINNER LEARNING ROADMAP

* **Day 1: Project Tour & Local Setup**
  * Start Apache and MySQL in XAMPP. Import `healthy_bite_infinityfree_dump.sql`.
  * Open `http://localhost:8000` and browse the menu.
* **Day 2: The Database**
  * Open phpMyAdmin. Browse `restaurants`, `categories`, `food_items`, and `orders`.
  * Understand Primary Keys (`id`) and Foreign Keys (`restaurant_id`).
* **Day 3: The Front Door & Routing**
  * Open [public/index.php](file:///d:/NEW%20healthy%20bite/public/index.php) and read the autoloader.
  * Open [routes/web.php](file:///d:/NEW%20healthy%20bite/routes/web.php) and trace how `/menu` points to `MenuController`.
* **Day 4: Controllers & Views**
  * Open [app/Controllers/MenuController.php](file:///d:/NEW%20healthy%20bite/app/Controllers/MenuController.php) and [resources/views/customer/menu.php](file:///d:/NEW%20healthy%20bite/resources/views/customer/menu.php).
  * See how `$categories` and `$foods` are passed from PHP to HTML.
* **Day 5: Repositories & SQL**
  * Open [app/Repositories/FoodRepository.php](file:///d:/NEW%20healthy%20bite/app/Repositories/FoodRepository.php).
  * Study how PDO prepared statements (`$stmt->prepare()`, `$stmt->execute()`) prevent SQL injection.
* **Day 6: The Frontend Magic**
  * Open [public/assets/js/food-details.js](file:///d:/NEW%20healthy%20bite/public/assets/js/food-details.js).
  * Inspect how clicking an add-on recalculates total calories live on the screen.
* **Day 7: The Cart & Order Pipeline**
  * Trace [public/assets/js/cart.js](file:///d:/NEW%20healthy%20bite/public/assets/js/cart.js) into [app/Services/OrderService.php](file:///d:/NEW%20healthy%20bite/app/Services/OrderService.php).
  * Understand why frozen snapshots protect historical order accuracy.
* **Day 8: Restaurant Owner Portal & Kitchen Kanban**
  * Log in as Owner (`owner@greenhousekitchen.com`).
  * Test moving an order across the Kitchen Live Orders Kanban board.
* **Day 9: Debugging & Experiments**
  * Open [storage/logs/app.log](file:///d:/NEW%20healthy%20bite/storage/logs/app.log).
  * Change a CSS button color in [public/assets/css/](file:///d:/NEW%20healthy%20bite/public/assets/css/) and observe the instant UI change.
* **Day 10: Viva Preparation**
  * Practice explaining the architecture, QR security, and nutrition formulas out loud using the Viva Section below.

---

## 18. ONE-PAGE CHEAT SHEET: HOW HEALTHY BITE WORKS IN 5 MINUTES

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                       HEALTHY BITE — ARCHITECTURE AT A GLANCE               │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. ENTRY POINT      : public/index.php receives every web request.          │
│ 2. AUTOLOADER       : Custom PSR-4 autoloader translates App\Class into     │
│                       app/Class.php automatically (no Composer needed).     │
│ 3. ROUTER           : App\Core\Router matches URL with regex in web.php     │
│                       and api.php, extracting parameters like {token}.      │
│ 4. CONTROLLER       : Receives request, validates input, calls Services.    │
│ 5. DOMAIN SERVICES  : NutritionService (macro math), PricingService (bill   │
│                       math), QrService (token verification).                │
│ 6. REPOSITORIES     : FoodRepository, OrderRepository execute safe PDO      │
│                       prepared SQL queries against MySQL.                   │
│ 7. SNAPSHOT PATTERN : Orders freeze food names, prices, and calories at     │
│                       the moment of checkout so future menu edits never     │
│                       corrupt past financial or accounting records.         │
│ 8. KITCHEN KANBAN   : Live multi-stage pipeline: Placed -> Preparing ->     │
│                       Ready -> Completed for seamless kitchen operations.   │
│ 9. SECURITY         : Bcrypt password hashing, session fixation protection, │
│                       PDO parameterized queries, role middleware isolation. │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 19. VIVA VOCE PREPARATION GUIDE (QUESTIONS & CONFIDENT ANSWERS)

### Q1: What is Healthy Bite and what makes it different from standard food ordering apps?
> **Answer:** Healthy Bite is a digital restaurant menu and ordering system specifically engineered for nutritional transparency. Unlike conventional platforms that only display names and prices, Healthy Bite dynamically calculates exact calories, macronutrients (proteins, carbs, fats), and allergens based on customer customizations (such as extra protein or alternative dressings) in real time before checkout.

### Q2: Why did you choose a custom MVC architecture instead of a framework like Laravel?
> **Answer:** Building a custom MVC architecture demonstrates a thorough understanding of core computer science principles—specifically the Front Controller pattern, PSR-4 autoloading, HTTP routing, the Repository pattern, and PDO abstraction—without the overhead and hidden complexity of large third-party dependencies.

### Q3: How do you prevent SQL Injection in Healthy Bite?
> **Answer:** We use PHP Data Objects (PDO) with strict parameterized prepared statements across all Repository classes (`app/Repositories/`). User inputs are never directly concatenated into SQL strings; instead, placeholders (`:id`, `:token`, `:status`) are bound at execution time, ensuring MySQL treats all input strictly as data, never as executable SQL code.

### Q4: How does the QR code ordering flow prevent URL tampering?
> **Answer:** The QR code does not embed easily guessable URL parameters like `?restaurant_id=1&table=5`. Instead, it embeds a unique, high-entropy cryptographic token (`/qr/{token}`). When scanned, `QrService` resolves the token against the `qr_tokens` database table through strict inner joins with `restaurant_tables`, `branches`, and `restaurants`. The server strictly overrides any client-supplied parameters with the verified database context.

### Q5: What is the "Snapshot Pattern" in your database design, and why is it necessary?
> **Answer:** In our `order_items` and `order_item_customizations` tables, we store snapshot columns such as `food_name_snapshot`, `base_price_snapshot`, and nutrient snapshots. If a restaurant owner later changes a salad price from ₹150 to ₹180 or alters an ingredient next month, past historical orders and tax receipts must remain unaltered for accounting, tax auditing, and legal accuracy.

### Q6: How does the Kitchen Live Orders system communicate status updates?
> **Answer:** When kitchen staff click "Accept" or "Mark Ready" on the Kitchen Kanban board (`/owner/kitchen-orders`), a background asynchronous `fetch()` request is sent to `POST /owner/orders/{id}/status`. The backend updates the `orders` record in MySQL. The customer's tracking screen (`/menu/tracking/{orderNumber}`) polls the server and immediately reflects the new status without requiring a manual page refresh.
