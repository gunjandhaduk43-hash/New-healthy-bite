# HEALTHY BITE — PROJECT DOCUMENTATION SOURCE OF TRUTH
**Digital Restaurant Menu and Food Ordering System**  
**Project Location:** `D:\NEW healthy bite`  
**Extraction Date & Verification Time:** September 17, 2026  
**Document Status:** Fact-Checked Source of Truth (Analysis & Verification Only — No Code/Data Modifications)

---

## Classification Standard
Every finding, specification, and architectural element in this document is strictly categorized into one of seven formal verification statuses:
- **[VERIFIED IMPLEMENTED]**: Actively present in source code, wired in routing/controllers, verified in database, and functional.
- **[PARTIALLY IMPLEMENTED]**: Core structure or endpoint exists, but secondary flows, full validation, or UI hooks are incomplete.
- **[PRESENT IN DATABASE BUT NOT IMPLEMENTED]**: Schema table, column, or constraint exists in MySQL, but application code does not read or write it.
- **[PRESENT IN CODE BUT NOT CURRENTLY USED]**: PHP class, method, or JS function exists in codebase but is not invoked by any active route or UI workflow.
- **[LEGACY / DUPLICATE]**: Relic from earlier iterations (e.g., prototype folder, standalone HTML prototypes, unused tables).
- **[UNKNOWN — REQUIRES VERIFICATION]**: Information ambiguous, missing schema backing, or requiring runtime stakeholder input.
- **[CONFLICT / INCONSISTENCY]**: Discrepancy between documentation, schema files, database state, or frontend/backend behavior.

---

# 1. Project Overview

- **Project Name:** Healthy Bite [VERIFIED IMPLEMENTED]
- **Sub-Title / Type:** Digital Restaurant Menu and Food Ordering System [VERIFIED IMPLEMENTED]
- **Operational Paradigm:** Multi-tenant Restaurant Management and Customer QR-Code Dining Platform [VERIFIED IMPLEMENTED]
- **Primary Core Capabilities:**
  1. **Customer Digital QR Menu & Ordering:** Instant table-side mobile menu ordering via scannable QR tokens without requiring native app installation. Real-time food customization, live nutrient macro calculation (calories, protein, carbs, fat), and automated price calculation.
  2. **Kitchen Live Orders Management:** Operational 5-column Kanban board (`New Orders` [placed], `Accepted`, `Preparing`, `Ready`, `Completed`) enabling kitchen staff to transition orders with live card updates and timing snapshots.
  3. **Customer Live Order Tracking:** Real-time visual progress tracker showing 5 stages (`Order Placed`, `Order Accepted`, `Preparing in Kitchen`, `Ready for Service`, `Completed`) updated via table order polling.
  4. **Restaurant Owner Portal:** Operational control suite covering Overview monitoring, Live Orders tracking, Kitchen Kanban, Menu & Food dish management with full macro/price updates, Tables & QR code generation (Tables 1–12), Customer Reviews management with reply persistence, Staff access management, and Sales & Macro Analytics.
  5. **Platform Super Admin Portal:** High-level tenant oversight covering Platform Overview KPIs, Registered Restaurant management (Approved, Pending, Suspended lifecycle), User Access control, and deep Restaurant Portal Inspection.
- **Evidence:** `app/Controllers/*`, `routes/web.php`, `routes/api.php`, `database/schema/schema.sql`, `tests/verify_interactive_features.php`.

---

# 2. Technology Stack

- **Backend Runtime:** PHP 8.5.10 (64-bit CLI / Built-in Web Server) [VERIFIED IMPLEMENTED]
  - Type strictness: `declare(strict_types=1);` declared at the top of every class and script file.
- **Architecture:** Lightweight Custom Object-Oriented PHP MVC (Front Controller, Routing Engine, Base Controller, Service Layer, Repository Pattern, PDO Database Abstraction, View Engine with Modular Layouts) [VERIFIED IMPLEMENTED]
- **Database Management System:** MySQL 10.4.32-MariaDB / MySQL 8.x [VERIFIED IMPLEMENTED]
  - Database Name: `healthy_bite`
  - Engine: `InnoDB` (ACID compliant, foreign key support)
  - Character Set: `utf8mb4`
  - Collation: `utf8mb4_unicode_ci`
  - Connection: Native PHP Data Objects (`PDO`) with prepared statements and `PDO::ATTR_EMULATE_PREPARES => false`.
- **Frontend Architecture:**
  - Structure: HTML5 Semantic Markup [VERIFIED IMPLEMENTED]
  - Styling: Vanilla CSS3 utilizing CSS Custom Properties (`public/assets/css/dashboard.css`, `public/assets/css/menu.css`) matching Healthy Bite Figma design system. No external CSS framework dependencies (e.g. TailwindCSS or Bootstrap CSS framework are NOT loaded; only Bootstrap Icons webfont is used). [VERIFIED IMPLEMENTED]
  - Iconography: Bootstrap Icons v1.11.3 (`cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css`) [VERIFIED IMPLEMENTED]
  - Typography: Google Fonts `Inter` font family (weights 400, 500, 600, 700, 800) [VERIFIED IMPLEMENTED]
  - Client-Side Logic: Vanilla JavaScript ES6+ (Modular State pattern, reactive event listeners, asynchronous Fetch API) [VERIFIED IMPLEMENTED]
  - QR Code Engine: Standalone `qrcode.min.js` (Canvas-based vector/raster generator embedded locally at `public/assets/js/qrcode.min.js`) [VERIFIED IMPLEMENTED]
- **Dependency Management:** Composer (`composer.json`) with custom PSR-4 fallback autoloader in `public/index.php` [VERIFIED IMPLEMENTED]
- **Hosting / Local Server:** PHP CLI Server (`php -S 127.0.0.1:8000 -t public`) [VERIFIED IMPLEMENTED]

---

# 3. Current Architecture

### 3.1 Architecture Summary
Healthy Bite operates as a strictly separated **3-Tier Web Application** built upon custom PHP MVC abstractions:
1. **Presentation Tier:**
   - Customer Web App: Responsive mobile-first digital ordering web application (`/menu`, `/menu/checkout`, `/menu/confirmation/{orderNumber}`, `/menu/tracking/{orderNumber}`).
   - Restaurant Owner Portal: Desktop/tablet optimized management portal (`/owner/*`).
   - Platform Super Admin Portal: Governance dashboard (`/admin/*`).
2. **Application / Business Logic Tier:**
   - Front Controller: `public/index.php`.
   - Core Services: Business logic encapsulated in `app/Services/` (Cart, Pricing, Nutrition, Order, Payment, QR, Menu).
   - Middleware: Context resolution and gatekeeping in `app/Middleware/` (`AdminMiddleware`, `RestaurantMiddleware`, `AuthMiddleware`).
   - Repositories: Data retrieval, aggregation, and persistence in `app/Repositories/`.
3. **Data Tier:**
   - Relational MySQL database `healthy_bite` containing 16 active relational tables and 1 legacy table.

### 3.2 Request Lifecycle Flow
```
User / Browser / QR Code Scan
          ↓
[ public/index.php ] (Front Controller)
          ↓
[ config/constants.php, config/app.php, config/database.php ]
[ app/Helpers/* ] (url.php, format.php, security.php, food.php)
[ App\Core\Env::load() ]
          ↓
[ App\Core\App::run() ]
          ↓
[ App\Core\Router::dispatch() ]
          ↓
[ App\Middleware\* ] (AdminMiddleware / RestaurantMiddleware / AuthMiddleware)
          ↓
[ App\Controllers\* ]
          ↓
[ App\Services\* ] (PricingService, NutritionService, CartService, OrderService, etc.)
          ↓
[ App\Repositories\* ] (FoodRepository, OrderRepository, RestaurantRepository, etc.)
          ↓
[ App\Core\Database::getConnection() ] → [ PDO MySQL healthy_bite ]
          ↓
[ App\Core\Controller::render() ] OR [ App\Core\Response::json() ]
          ↓
HTTP 200 / 201 Response (HTML View or JSON Payload)
```

---

# 4. Directory / Module Structure

### 4.1 Root Directory Map (`d:\NEW healthy bite`)
```
d:\NEW healthy bite
├── .env                                  # Active environment credentials & configuration
├── .env.example                          # Environment template
├── .gitignore                            # Git exclusion rules
├── composer.json                         # Project package descriptor (PSR-4 App\ namespace)
├── composer.lock                         # Composer lock metadata
├── README.md                             # Project overview documentation
├── Healthy_Bite_Database_All_Tables.pdf   # Reference documentation PDF
├── app/                                  # Core Application Codebase
│   ├── Controllers/                      # Request Handlers & View Controllers
│   │   ├── Admin/                        # Platform Super Admin Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── RestaurantController.php
│   │   │   └── UserController.php
│   │   ├── Owner/                        # Restaurant Owner Portal Controllers
│   │   │   ├── AnalyticsController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── MenuController.php
│   │   │   ├── OrderController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ReviewController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── StaffController.php
│   │   │   └── TableController.php
│   │   ├── CartController.php            # Customer Cart API Controller
│   │   ├── CheckoutController.php        # Customer Checkout Page Controller
│   │   ├── FoodController.php            # Customer Food Details & Options API
│   │   ├── HomeController.php            # Welcome / Landing Controller
│   │   ├── MenuController.php            # Customer Digital Menu Controller
│   │   ├── OrderController.php           # Customer Order Placement & Tracking
│   │   ├── PaymentController.php         # Customer Simulated Payment API
│   │   └── RestaurantController.php      # Customer Restaurant Data API
│   ├── Core/                             # Architectural Framework Abstractions
│   │   ├── App.php                       # Application lifecycle coordinator
│   │   ├── Auth.php                      # Session authentication helpers
│   │   ├── Controller.php                # Base Controller with render() & json()
│   │   ├── Csrf.php                      # CSRF token generator & validator
│   │   ├── Database.php                  # PDO database singleton connection manager
│   │   ├── Env.php                       # .env parser and environment loader
│   │   ├── Request.php                   # HTTP Request abstraction (GET, POST, JSON)
│   │   ├── Response.php                  # HTTP Response abstraction (JSON, Redirect)
│   │   ├── Router.php                    # URI routing engine with dynamic {param} regex
│   │   ├── Session.php                   # Session lifecycle & flash message manager
│   │   └── Validator.php                 # Form and payload validator
│   ├── Helpers/                          # Global Procedural Helper Functions
│   │   ├── food.php                      # Diet badges & food image resolver
│   │   ├── format.php                    # Currency, date, and macro formatting
│   │   ├── security.php                  # HTML escaping e() & CSRF helpers
│   │   └── url.php                       # url(), asset(), and route builders
│   ├── Middleware/                       # Route Guards & Context Resolvers
│   │   ├── AdminMiddleware.php           # Guards /admin/* (requires role_id == 1)
│   │   ├── AuthMiddleware.php            # Generic logged-in session guard
│   │   └── RestaurantMiddleware.php      # Guards /owner/* (resolves restaurant_id)
│   ├── Models/                           # [EMPTY / UNUSED] No Active Eloquent/ORM Models
│   ├── Repositories/                     # SQL Data Layer via PDO
│   │   ├── BranchRepository.php          # Branches, Tables, and QR tokens
│   │   ├── CategoryRepository.php        # Menu categories
│   │   ├── FoodRepository.php            # Dishes, variants, customizations
│   │   ├── OrderRepository.php           # Orders, items, items customization, stats
│   │   ├── PaymentRepository.php         # Payment records
│   │   ├── RestaurantRepository.php      # Restaurant directory, inspection stats
│   │   ├── ReviewRepository.php          # Customer reviews, replies, rating metrics
│   │   ├── StaffRepository.php           # Restaurant staff users
│   │   └── UserRepository.php            # User authentication & role lookups
│   └── Services/                         # Business Domain Logic
│       ├── CartService.php               # Server-side cart recalculation & verification
│       ├── MenuService.php               # Catalog aggregation & food details
│       ├── NutritionService.php          # 8-macro nutrient calculations & quantity scaling
│       ├── OrderService.php              # Full order placement pipeline & token locking
│       ├── PaymentService.php            # Simulated payment execution & status advancing
│       ├── PricingService.php            # Variant + Customization price sum & taxes
│       └── QrService.php                 # Token verification & table context binding
├── config/                               # Static Application Configuration
│   ├── app.php                           # App metadata, tax rate, default restaurant
│   ├── constants.php                     # Role IDs, order statuses, table statuses
│   └── database.php                      # Database credentials & PDO options
├── database/                             # Database Assets & Migrations
│   ├── migrations/
│   │   └── add_review_replies.php        # Migration script adding review replies
│   ├── schema/
│   │   └── schema.sql                    # Definitive MySQL DDL schema (16 tables)
│   ├── seeders/
│   │   ├── sample_data.sql               # Seed SQL data
│   │   ├── seed_clean_12_tables.php      # Clean table seeder for Tables 1–12
│   │   └── seed_dashboard_data.php       # Operational demo data seeder
│   └── run_upgrade_migrations.php        # Standalone migration executor
├── public/                               # Web Server Document Root
│   ├── assets/
│   │   ├── css/                          # CSS Style Sheets
│   │   │   ├── dashboard.css             # Main unified design system
│   │   │   └── menu.css                  # Customer digital menu styles
│   │   ├── images/                       # Static graphics, food banners, logos
│   │   │   └── foods/                    # Dish image assets (Unsplash photos & icons)
│   │   └── js/                           # Client JavaScript Modules
│   │       ├── api.js                    # Fetch client for /api/* endpoints
│   │       ├── app.js                    # Global app initialization
│   │       ├── cart.js                   # Cart drawer & items calculation
│   │       ├── checkout.js               # Checkout form & payment simulation
│   │       ├── dashboard.js              # Table filters, modal bindings, charts
│   │       ├── food-details.js           # Modal customization & live macro updater
│   │       ├── live-kitchen.js           # 5-step kitchen polling progress tracker
│   │       ├── menu.js                   # Search, filter pills, dish card click
│   │       ├── nutrition.js              # Client-side nutrient scaling
│   │       ├── order.js                  # Order creation client helpers
│   │       ├── pricing.js                # Client-side price summing
│   │       ├── qrcode.min.js             # Canvas QR code generator engine
│   │       ├── state.js                  # Centralized client state store
│   │       └── utils.js                  # Toast notifications & formatting
│   ├── index.php                         # Application Entrypoint (Front Controller)
│   └── uploads/                          # Uploaded image directory
├── resources/
│   └── views/                            # PHP Template Views
│       ├── admin/                        # Super Admin Views
│       │   ├── dashboard.php             # Platform Overview
│       │   ├── login.php                 # Admin Sign In
│       │   ├── portal_inspect.php        # Restaurant Portal Inspection
│       │   ├── restaurants.php           # Registered Restaurants directory
│       │   └── users.php                 # Platform Users & Access
│       ├── customer/                     # Customer Digital Menu Views
│       │   ├── checkout.php              # Order review & table confirmation
│       │   ├── confirmation.php          # Post-order success screen
│       │   ├── menu.php                  # Interactive digital menu
│       │   ├── tracking.php              # Live kitchen tracking screen
│       │   ├── welcome.php               # Landing welcome page
│       │   └── errors/                   # 404 & restaurant unavailable views
│       ├── layouts/                      # Layout Shells
│       │   ├── admin.php                 # Admin Portal sidebar + topbar layout
│       │   ├── customer.php              # Customer responsive layout
│       │   ├── minimal.php               # Standalone minimal layout
│       │   └── owner.php                 # Restaurant Owner sidebar + topbar layout
│       ├── owner/                        # Restaurant Owner Views
│       │   ├── analytics.php             # Sales & Macro Analytics
│       │   ├── dashboard.php             # Overview (Monitoring only)
│       │   ├── kitchen_orders.php        # Kitchen Live Orders Kanban Board
│       │   ├── live_menu.php             # Live Menu Preview with real photos
│       │   ├── login.php                 # Restaurant Owner Sign In
│       │   ├── menu.php                  # Menu & Foods CRUD Management
│       │   ├── orders.php                # Live Orders (Monitoring with details modal)
│       │   ├── profile.php               # Restaurant Profile settings
│       │   ├── reviews.php               # Customer Reviews with reply workflow
│       │   ├── settings.php              # System Settings
│       │   ├── staff.php                 # Staff Management
│       │   └── tables.php                # Tables & QR Codes (1–12 real QR generator)
│       └── partials/                     # Reusable Component Views
│           ├── food-detail.php           # Modal customization popup
│           └── restaurant-header.php     # Brand hero banner
├── routes/
│   ├── api.php                           # RESTful JSON API Routes
│   └── web.php                           # Browser HTML Routes
├── storage/                              # Application Storage & Runtime Data
│   ├── cache/                            # View / query cache directory
│   ├── logs/                             # System logs (`app.log`)
│   └── db_extraction.json                # Complete verified schema metadata extraction
├── tests/                                # Verification Test Suites
│   ├── check_users.php                   # Authentication alias verification
│   ├── verify_endpoints.php              # Route HTTP verification
│   └── verify_interactive_features.php   # Comprehensive automated test suite
├── prototype/                            # [LEGACY / PROTOTYPE]
│   └── customer-ui/                      # Earlier static HTML customer prototypes
└── vendor/                               # Composer autoload artifacts
```

---

# 5. Authentication and Roles

### 5.1 Role Hierarchy & Storage
Roles are stored in the relational table `roles` and referenced by `users.role_id` [VERIFIED IMPLEMENTED]:

| Role ID | Name | Slug | Stored In | Application Scope | Current Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | Super Admin | `super_admin` | `roles.id = 1` | Global Platform Administration | **VERIFIED IMPLEMENTED** |
| **2** | Restaurant Owner | `restaurant_owner` | `roles.id = 2` | Single Restaurant Entity Administration | **VERIFIED IMPLEMENTED** |
| **3** | Manager | `manager` | `roles.id = 3` | Restaurant Branch Supervision | **VERIFIED IMPLEMENTED** |
| **4** | Staff | `staff` | `roles.id = 4` | Floor Service & Kitchen Operations | **VERIFIED IMPLEMENTED** |

- **Evidence:** `config/constants.php` lines 4–7, `database/schema/schema.sql` lines 36–43, database records extracted from `roles`.

### 5.2 User Authentication Details
User authentication is managed via `App\Repositories\UserRepository::findByEmail()` and verified using `password_verify($password, $user['password'])` with PHP native BCRYPT hashing [VERIFIED IMPLEMENTED].

- **Universal Master/Demo Password:** `Secret@123` [VERIFIED IMPLEMENTED]
- **Email Alias Resolution:** `UserRepository::findByEmail()` normalizes input and supports both production emails and development aliases:
  - **Super Admin Primary:** `mira@healthybite.in` (User ID 1)
  - **Super Admin Aliases:** `admin@healthybite.com`, `admin@healthybite.in`, `admin`, `mira@healthybite.com`
  - **Restaurant Owner Primary:** `aarav@greenhouse.in` (User ID 2, Restaurant ID 1: Greenhouse Kitchen)
  - **Restaurant Owner Aliases:** `owner@greenhousekitchen.com`, `owner@greenhouse.in`, `owner`, `aarav@greenhousekitchen.com`

### 5.3 Authorization & Scoping Matrix

| Role | Login Route | Logout Route | Guard Middleware | Accessible Portals / Routes | Scoping Boundary |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | `POST /admin/login` | `GET /admin/logout` | `AdminMiddleware` | `/admin/dashboard`<br>`/admin/restaurants`<br>`/admin/portal-inspect`<br>`/admin/users` | Global platform-wide (Cross-restaurant access) |
| **Restaurant Owner** | `POST /owner/login` | `GET /owner/logout` | `RestaurantMiddleware` | `/owner/dashboard`<br>`/owner/orders`<br>`/owner/kitchen-orders`<br>`/owner/menu`<br>`/owner/tables`<br>`/owner/reviews`<br>`/owner/staff`<br>`/owner/analytics`<br>`/owner/live-menu`<br>`/owner/profile`<br>`/owner/settings` | Strictly scoped to `users.restaurant_id` (e.g. Restaurant #1) |
| **Manager / Staff** | `POST /owner/login` | `GET /owner/logout` | `RestaurantMiddleware` | `/owner/orders`<br>`/owner/kitchen-orders`<br>`/owner/tables` | Strictly scoped to assigned `users.restaurant_id` |
| **Customer** | None (Guest session) | None | None | `/menu`<br>`/menu/checkout`<br>`/menu/confirmation/*`<br>`/menu/tracking/*` | Scoped via table QR token or active table query parameter |

---

# 6. Database Overview

- **Database Engine:** MySQL / MariaDB (InnoDB) [VERIFIED IMPLEMENTED]
- **Schema Source File:** `database/schema/schema.sql` (19,319 bytes) [VERIFIED IMPLEMENTED]
- **Actual Live Tables in MySQL:** Exactly **17 Tables** [VERIFIED IMPLEMENTED]
  - 16 Core Active Tables (Defined in `schema.sql` and backed by MVC Repositories).
  - 1 Legacy Table (`admin` — present in MySQL with 4 rows, but omitted from `schema.sql` and unused by backend code).
- **Referential Integrity:** 24 Active Foreign Key constraints enforced across the schema with `ON UPDATE CASCADE` and contextual delete actions (`RESTRICT`, `CASCADE`, or `SET NULL`).

---

# 7. Complete Table Inventory

| # | Exact Table Name | Live Row Count | Column Count | Foreign Keys | Primary Key | Purpose / Description | Status Classification |
| :---: | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| **1** | `admin` | 4 | 4 | 0 | `id` | Legacy table containing administrator names and timestamps. Not referenced by any code. | **LEGACY / DUPLICATE** |
| **2** | `roles` | 4 | 6 | 0 | `id` | System user authorization roles (`super_admin`, `restaurant_owner`, `manager`, `staff`). | **VERIFIED IMPLEMENTED** |
| **3** | `restaurants` | 3 | 15 | 1 | `id` | Multi-tenant restaurant entity directory with branding, contact, and approval status. | **VERIFIED IMPLEMENTED** |
| **4** | `users` | 8 | 9 | 2 | `id` | Authenticated accounts across all portals with role and restaurant scoping. | **VERIFIED IMPLEMENTED** |
| **5** | `branches` | 6 | 10 | 1 | `id` | Physical dining branches/locations associated with a restaurant tenant. | **VERIFIED IMPLEMENTED** |
| **6** | `categories` | 16 | 10 | 1 | `id` | Menu food categories across tenants (8 for Greenhouse Kitchen + 8 tenant categories). | **VERIFIED IMPLEMENTED** |
| **7** | `food_items` | 47 | 25 | 2 | `id` | Core food catalog dishes with base price, 8-macro nutrition, caffeine, and diet type. | **VERIFIED IMPLEMENTED** |
| **8** | `food_variants` | 94 | 18 | 1 | `id` | Dish portion/preparation variations (e.g. Standard, Large) with price/macro/caffeine adjustments. | **VERIFIED IMPLEMENTED** |
| **9** | `food_customizations` | 543 | 21 | 1 | `id` | Item-specific add-ons, bases, dressings, and toppings with min/max quantity limits. | **VERIFIED IMPLEMENTED** |
| **10** | `restaurant_tables` | 12 | 7 | 2 | `id` | Dining tables belonging to branches with occupancy status (`available`, `occupied`, etc.). | **VERIFIED IMPLEMENTED** |
| **11** | `qr_tokens` | 12 | 8 | 3 | `id` | Cryptographic alphanumeric tokens linking physical QR scans to specific tables. | **VERIFIED IMPLEMENTED** |
| **12** | `customers` | 37 | 6 | 0 | `id` | Dining guest customer records created upon order placement (name, mobile, email). | **VERIFIED IMPLEMENTED** |
| **13** | `orders` | 34 | 16 | 4 | `id` | Master orders table tracking order numbers, dining type, totals, and lifecycle statuses. | **VERIFIED IMPLEMENTED** |
| **14** | `order_items` | 43 | 19 | 2 | `id` | Itemized order dishes with frozen price and nutrition snapshots at purchase time. | **VERIFIED IMPLEMENTED** |
| **15** | `order_item_customizations` | 66 | 15 | 2 | `id` | Frozen customization choices and macro/price/caffeine adjustments per order item. | **VERIFIED IMPLEMENTED** |
| **16** | `payments` | 21 | 8 | 1 | `id` | Payment settlement records linked 1-to-1 with orders (`cash`, `upi`, `card`). | **VERIFIED IMPLEMENTED** |
| **17** | `reviews` | 7 | 11 | 3 | `id` | Customer dining feedback, star ratings, and restaurant owner response replies. | **VERIFIED IMPLEMENTED** |

> **Critical Data Dictionary Architecture Advisory & Unit Reference:**  
> - **Calories:** Kilocalories (`kcal`), stored as `int(10) unsigned` / `int(11)`.
> - **Macronutrients (Protein, Carbs, Fat, Fiber, Sugar):** Strictly measured in **grams (`g`)**, stored as `decimal(6,2)`.
> - **Sodium:** Strictly measured in **milligrams (`mg`)**, stored as `decimal(7,2)`.
> - **Caffeine:** Strictly measured in **milligrams (`mg`)**, stored as `decimal(6,2)` (`food_items.caffeine`, `food_variants.caffeine_adjustment`, `food_customizations.caffeine_adjustment`, `order_items.caffeine`, `order_item_customizations.caffeine_adjustment`).  
> *(Note: Any specification or external sheet stating caffeine is measured in grams `g` is incorrect; caffeine is strictly tracked in milligrams `mg`).*
> - **Table Number Indexing:** `restaurant_tables.table_number` is indexed via composite unique constraint `uq_branch_table (branch_id, table_number)` to ensure uniqueness per physical restaurant branch.

---

# 8. Complete Data Dictionary Extraction

### Table 1: `admin` [LEGACY / DUPLICATE]
- **Purpose:** Legacy admin user records from early prototype.
- **Primary Key:** `id`
- **Foreign Keys:** None
- **Verified From:** `information_schema.COLUMNS`, live MySQL database table `healthy_bite.admin`.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Internal auto-increment record ID | live DB |
| `name` | varchar | 120 | NO | None | | None | Administrator name | live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Timestamp of creation | live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Timestamp of last update | live DB |

---

### Table 2: `roles` [VERIFIED IMPLEMENTED]
- **Purpose:** System-wide user access and authority roles.
- **Primary Key:** `id`
- **Foreign Keys:** None
- **Verified From:** `database/schema/schema.sql` lines 36–43, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Role primary identifier | schema.sql, live DB |
| `name` | varchar | 50 | NO | None | | None | Human readable role title (e.g. Super Admin) | schema.sql, live DB |
| `slug` | varchar | 50 | NO | None | UNI | None | Unique machine identifier (`super_admin`, etc.) | schema.sql, live DB |
| `description` | varchar | 255 | YES | NULL | | None | Explanation of role permissions | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Record creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Record last updated timestamp | schema.sql, live DB |

---

### Table 3: `restaurants` [VERIFIED IMPLEMENTED]
- **Purpose:** Multi-tenant restaurant entity profile, contact details, and platform approval status.
- **Primary Key:** `id`
- **Foreign Keys:** `owner_user_id` → `users(id)`
- **Verified From:** `database/schema/schema.sql` lines 46–64, 85–87, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Restaurant tenant primary ID | schema.sql, live DB |
| `owner_user_id` | bigint | 20 (unsigned) | YES | NULL | MUL | `users(id)` | User ID of restaurant primary owner | schema.sql, live DB |
| `name` | varchar | 150 | NO | None | | None | Legal / trade name of restaurant | schema.sql, live DB |
| `slug` | varchar | 150 | NO | None | UNI | None | Unique URL-safe restaurant identifier | schema.sql, live DB |
| `logo` | varchar | 255 | YES | NULL | | None | URL/path to restaurant logo graphic | schema.sql, live DB |
| `cover_image` | varchar | 255 | YES | NULL | | None | URL/path to restaurant hero cover banner | schema.sql, live DB |
| `description` | text | | YES | NULL | | None | Brand narrative and bio | schema.sql, live DB |
| `phone` | varchar | 30 | YES | NULL | | None | Primary business telephone | schema.sql, live DB |
| `email` | varchar | 191 | YES | NULL | | None | Business contact email | schema.sql, live DB |
| `address` | text | | YES | NULL | | None | Registered physical street address | schema.sql, live DB |
| `city` | varchar | 100 | YES | NULL | | None | Operating city | schema.sql, live DB |
| `state` | varchar | 100 | YES | NULL | | None | Operating state / province | schema.sql, live DB |
| `status` | enum | 'pending','approved','suspended' | NO | 'approved' | MUL | None | Platform tenancy approval status | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Registration timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Modification timestamp | schema.sql, live DB |

---

### Table 4: `users` [VERIFIED IMPLEMENTED]
- **Purpose:** Platform and restaurant user accounts with authentication credentials and role assignments.
- **Primary Key:** `id`
- **Foreign Keys:** `role_id` → `roles(id)`, `restaurant_id` → `restaurants(id)`
- **Verified From:** `database/schema/schema.sql` lines 67–82, `UserRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | User account primary identifier | schema.sql, live DB |
| `role_id` | bigint | 20 (unsigned) | NO | None | MUL | `roles(id)` | Assigned authority role foreign key | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | YES | NULL | MUL | `restaurants(id)` | Scoped restaurant entity (NULL for Super Admin) | schema.sql, live DB |
| `name` | varchar | 120 | NO | None | | None | User full name | schema.sql, live DB |
| `email` | varchar | 191 | NO | None | UNI | None | Unique login email address | schema.sql, live DB |
| `password` | varchar | 255 | NO | None | | None | BCRYPT password hash | schema.sql, live DB |
| `status` | enum | 'active','inactive','suspended' | NO | 'active' | MUL | None | User account operational state | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Account creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Account modification timestamp | schema.sql, live DB |

---

### Table 5: `branches` [VERIFIED IMPLEMENTED]
- **Purpose:** Physical restaurant dining branches or outlets.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`
- **Verified From:** `database/schema/schema.sql` lines 90–100, `BranchRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Branch primary identifier | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Parent restaurant entity | schema.sql, live DB |
| `name` | varchar | 150 | NO | None | | None | Branch title (e.g. Indiranagar Branch) | schema.sql, live DB |
| `address` | text | | YES | NULL | | None | Physical location address | schema.sql, live DB |
| `city` | varchar | 100 | YES | NULL | | None | Branch city | schema.sql, live DB |
| `state` | varchar | 100 | YES | NULL | | None | Branch state | schema.sql, live DB |
| `phone` | varchar | 30 | YES | NULL | | None | Branch contact telephone | schema.sql, live DB |
| `status` | enum | 'active','inactive' | NO | 'active' | MUL | None | Branch active status | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Last update timestamp | schema.sql, live DB |

---

### Table 6: `categories` [VERIFIED IMPLEMENTED]
- **Purpose:** Menu grouping divisions (Main Meals, Bowls, Wraps, Salads, Soups, etc.).
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`
- **Verified From:** `database/schema/schema.sql`, `CategoryRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Category primary identifier | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Scoped restaurant entity | schema.sql, live DB |
| `name` | varchar | 100 | NO | None | | None | Category title | schema.sql, live DB |
| `slug` | varchar | 100 | NO | None | MUL | None | URL slug identifier | schema.sql, live DB |
| `description` | text | | YES | NULL | | None | Category description | schema.sql, live DB |
| `image` | varchar | 255 | YES | NULL | | None | Category banner image URL | schema.sql, live DB |
| `sort_order` | int | 11 | NO | 0 | | None | Display sort ordering | schema.sql, live DB |
| `status` | enum | 'active','inactive' | NO | 'active' | | None | Category active status | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Update timestamp | schema.sql, live DB |

---

### Table 7: `food_items` [VERIFIED IMPLEMENTED]
- **Purpose:** Master food dishes with base pricing, full 8-macro nutritional metrics, dietary classifications, allergens, and availability.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`, `category_id` → `categories(id)`
- **Verified From:** `database/schema/schema.sql`, `FoodRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Food dish primary identifier | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Owning restaurant tenant | schema.sql, live DB |
| `category_id` | bigint | 20 (unsigned) | NO | None | MUL | `categories(id)` | Menu category foreign key | schema.sql, live DB |
| `name` | varchar | 150 | NO | None | | None | Dish display name | schema.sql, live DB |
| `slug` | varchar | 150 | NO | None | MUL | None | URL-safe slug | schema.sql, live DB |
| `description` | text | | YES | NULL | | None | Detailed dish culinary description | schema.sql, live DB |
| `image` | varchar | 255 | YES | NULL | | None | Dish photo URL (Unsplash or local) | schema.sql, live DB |
| `ingredients` | text | | YES | NULL | | None | Core ingredient list | schema.sql, live DB |
| `allergens` | varchar | 255 | YES | NULL | | None | Allergen warning text (e.g. Dairy, Nuts) | schema.sql, live DB |
| `food_type` | enum | 'vegetarian','non_vegetarian','vegan','jain','other' | NO | 'vegetarian' | | None | Dietary lifestyle categorization | schema.sql, live DB |
| `base_price` | decimal | 10,2 | NO | None | | None | Standard dish base price (₹) | schema.sql, live DB |
| `calories` | int | 10 (unsigned) | YES | NULL | | None | Total energy in kcal | schema.sql, live DB |
| `protein` | decimal | 6,2 | YES | NULL | | None | Protein content in grams (g) | schema.sql, live DB |
| `carbs` | decimal | 6,2 | YES | NULL | | None | Total carbohydrates in grams (g) | schema.sql, live DB |
| `fat` | decimal | 6,2 | YES | NULL | | None | Total fats in grams (g) | schema.sql, live DB |
| `fiber` | decimal | 6,2 | YES | NULL | | None | Dietary fiber in grams (g) | schema.sql, live DB |
| `sugar` | decimal | 6,2 | YES | NULL | | None | Simple sugars in grams (g) | schema.sql, live DB |
| `sodium` | decimal | 7,2 | YES | NULL | | None | Sodium content in milligrams (mg) | schema.sql, live DB |
| `caffeine` | decimal | 6,2 | YES | NULL | | None | Caffeine in milligrams (mg) | schema.sql, live DB |
| `serving_size` | varchar | 100 | YES | NULL | | None | Serving portion weight / units | schema.sql, live DB |
| `is_featured` | tinyint | 1 | NO | 0 | MUL | None | Featured dish flag (1/0) | schema.sql, live DB |
| `is_popular` | tinyint | 1 | NO | 0 | MUL | None | Popular best-seller badge flag (1/0) | schema.sql, live DB |
| `is_available` | tinyint | 1 | NO | 1 | MUL | None | Live ordering availability toggle (1/0) | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Last update timestamp | schema.sql, live DB |

---

### Table 8: `food_variants` [VERIFIED IMPLEMENTED]
- **Purpose:** Preparation or portion size options (e.g. Regular, Large, Double Protein) modifying base price and nutrients.
- **Primary Key:** `id`
- **Foreign Keys:** `food_item_id` → `food_items(id)` (ON DELETE CASCADE)
- **Verified From:** `database/schema/schema.sql`, `FoodRepository.php`, `PricingService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Variant primary identifier | schema.sql, live DB |
| `food_item_id` | bigint | 20 (unsigned) | NO | None | MUL | `food_items(id)` | Parent food dish | schema.sql, live DB |
| `name` | varchar | 100 | NO | None | | None | Variant title (e.g. Large / Regular) | schema.sql, live DB |
| `description` | varchar | 255 | YES | NULL | | None | Variant description | schema.sql, live DB |
| `price_adjustment` | decimal | 10,2 | NO | 0.00 | | None | Price change relative to base price (₹) | schema.sql, live DB |
| `calories_adjustment`| int | 11 | YES | NULL | | None | Calorie modification (kcal) | schema.sql, live DB |
| `protein_adjustment` | decimal | 6,2 | YES | NULL | | None | Protein modification (g) | schema.sql, live DB |
| `carbs_adjustment`   | decimal | 6,2 | YES | NULL | | None | Carbs modification (g) | schema.sql, live DB |
| `fat_adjustment`     | decimal | 6,2 | YES | NULL | | None | Fat modification (g) | schema.sql, live DB |
| `fiber_adjustment`   | decimal | 6,2 | YES | NULL | | None | Fiber modification (g) | schema.sql, live DB |
| `sugar_adjustment`   | decimal | 6,2 | YES | NULL | | None | Sugar modification (g) | schema.sql, live DB |
| `sodium_adjustment`  | decimal | 7,2 | YES | NULL | | None | Sodium modification (mg) | schema.sql, live DB |
| `caffeine_adjustment`| decimal | 6,2 | YES | NULL | | None | Caffeine modification (mg) | schema.sql, live DB |
| `is_required` | tinyint | 1 | NO | 0 | | None | Mandatory variant choice flag (1/0) | schema.sql, live DB |
| `is_available` | tinyint | 1 | NO | 1 | | None | Active availability flag (1/0) | schema.sql, live DB |
| `sort_order` | int | 11 | NO | 0 | | None | Display sort position | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Update timestamp | schema.sql, live DB |

---

### Table 9: `food_customizations` [VERIFIED IMPLEMENTED]
- **Purpose:** Add-ons, bases, toppings, and dressing choices with quantity rules and nutrition/pricing adjustments.
- **Primary Key:** `id`
- **Foreign Keys:** `food_item_id` → `food_items(id)` (ON DELETE CASCADE)
- **Verified From:** `database/schema/schema.sql`, `FoodRepository.php`, `PricingService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Customization primary identifier | schema.sql, live DB |
| `food_item_id` | bigint | 20 (unsigned) | NO | None | MUL | `food_items(id)` | Parent food dish | schema.sql, live DB |
| `group_name` | varchar | 100 | NO | 'Add-ons' | | None | Option grouping (e.g. Choose Base, Dressing) | schema.sql, live DB |
| `name` | varchar | 100 | NO | None | | None | Customization title (e.g. Extra Avocado) | schema.sql, live DB |
| `description` | varchar | 255 | YES | NULL | | None | Customization description | schema.sql, live DB |
| `price_adjustment` | decimal | 10,2 | NO | 0.00 | | None | Cost per unit (₹) | schema.sql, live DB |
| `calories_adjustment`| int | 11 | YES | NULL | | None | Calorie delta per unit (kcal) | schema.sql, live DB |
| `protein_adjustment` | decimal | 6,2 | YES | NULL | | None | Protein delta per unit (g) | schema.sql, live DB |
| `carbs_adjustment`   | decimal | 6,2 | YES | NULL | | None | Carbs delta per unit (g) | schema.sql, live DB |
| `fat_adjustment`     | decimal | 6,2 | YES | NULL | | None | Fat delta per unit (g) | schema.sql, live DB |
| `fiber_adjustment`   | decimal | 6,2 | YES | NULL | | None | Fiber delta per unit (g) | schema.sql, live DB |
| `sugar_adjustment`   | decimal | 6,2 | YES | NULL | | None | Sugar delta per unit (g) | schema.sql, live DB |
| `sodium_adjustment`  | decimal | 7,2 | YES | NULL | | None | Sodium delta per unit (mg) | schema.sql, live DB |
| `caffeine_adjustment`| decimal | 6,2 | YES | NULL | | None | Caffeine delta per unit (mg) | schema.sql, live DB |
| `is_required` | tinyint | 1 | NO | 0 | | None | Mandatory group flag | schema.sql, live DB |
| `min_quantity` | int | 10 (unsigned) | NO | 0 | | None | Minimum required selections | schema.sql, live DB |
| `max_quantity` | int | 10 (unsigned) | NO | 1 | | None | Maximum allowed selections | schema.sql, live DB |
| `is_available` | tinyint | 1 | NO | 1 | MUL | None | Live availability status | schema.sql, live DB |
| `sort_order` | int | 11 | NO | 0 | | None | Sort order within group | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Update timestamp | schema.sql, live DB |

---

### Table 10: `restaurant_tables` [VERIFIED IMPLEMENTED]
- **Purpose:** Physical dining tables configured within branches with live occupancy tracking.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`, `branch_id` → `branches(id)`
- **Verified From:** `database/schema/schema.sql`, `BranchRepository.php`, `TableController.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Table primary identifier | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Parent restaurant tenant | schema.sql, live DB |
| `branch_id` | bigint | 20 (unsigned) | NO | None | MUL | `branches(id)` | Physical branch outlet | schema.sql, live DB |
| `table_number` | varchar | 50 | NO | None | | None | Display label (e.g. Table 1, Table 12) | schema.sql, live DB |
| `status` | enum | 'available','occupied','cleaning','out_of_service' | NO | 'available' | MUL | None | Operational occupancy state | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Modification timestamp | schema.sql, live DB |

---

### Table 11: `qr_tokens` [VERIFIED IMPLEMENTED]
- **Purpose:** Cryptographically generated tokens uniquely associated with dining tables for contactless menu resolution.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`, `branch_id` → `branches(id)`, `table_id` → `restaurant_tables(id)`
- **Verified From:** `database/schema/schema.sql`, `BranchRepository.php`, `QrService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | QR token record ID | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Scoped restaurant | schema.sql, live DB |
| `branch_id` | bigint | 20 (unsigned) | NO | None | MUL | `branches(id)` | Scoped branch | schema.sql, live DB |
| `table_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurant_tables(id)` | Associated dining table | schema.sql, live DB |
| `token` | varchar | 128 | NO | None | UNI | None | Alphanumeric secret token | schema.sql, live DB |
| `status` | enum | 'active','inactive','expired' | NO | 'active' | MUL | None | Token validity status | schema.sql, live DB |
| `expires_at` | timestamp | | YES | NULL | | None | Optional expiration timestamp | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Token generation timestamp | schema.sql, live DB |

---

### Table 12: `customers` [VERIFIED IMPLEMENTED]
- **Purpose:** Guest customer records captured during digital checkout.
- **Primary Key:** `id`
- **Foreign Keys:** None
- **Verified From:** `database/schema/schema.sql`, `OrderRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Customer primary identifier | schema.sql, live DB |
| `name` | varchar | 120 | NO | None | | None | Customer full name | schema.sql, live DB |
| `mobile` | varchar | 30 | YES | NULL | MUL | None | Customer mobile phone number | schema.sql, live DB |
| `email` | varchar | 191 | YES | NULL | MUL | None | Customer email address | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Record creation timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Record update timestamp | schema.sql, live DB |

---

### Table 13: `orders` [VERIFIED IMPLEMENTED]
- **Purpose:** Master order records maintaining dining type, table linkage, subtotal, tax, service charge, grand total, payment status, and kitchen operational status.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`, `branch_id` → `branches(id)`, `table_id` → `restaurant_tables(id)`, `customer_id` → `customers(id)`
- **Verified From:** `database/schema/schema.sql`, `OrderRepository.php`, `OrderService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Order primary identifier | schema.sql, live DB |
| `order_number` | varchar | 64 | NO | None | UNI | None | Unique human-readable code (e.g. `HB-1001`) | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Tenant restaurant | schema.sql, live DB |
| `branch_id` | bigint | 20 (unsigned) | NO | None | MUL | `branches(id)` | Dining branch location | schema.sql, live DB |
| `table_id` | bigint | 20 (unsigned) | YES | NULL | MUL | `restaurant_tables(id)` | Dining table (NULL for takeaway) | schema.sql, live DB |
| `customer_id` | bigint | 20 (unsigned) | NO | None | MUL | `customers(id)` | Ordering customer record | schema.sql, live DB |
| `order_type` | enum | 'dine_in','takeaway' | NO | 'dine_in' | MUL | None | Dining mode | schema.sql, live DB |
| `subtotal` | decimal | 10,2 | NO | None | | None | Order items subtotal before taxes (₹) | schema.sql, live DB |
| `tax` | decimal | 10,2 | NO | 0.00 | | None | Calculated GST tax amount (₹) | schema.sql, live DB |
| `service_charge`| decimal | 10,2 | NO | 0.00 | | None | Optional restaurant service charge (₹) | schema.sql, live DB |
| `total_amount` | decimal | 10,2 | NO | None | | None | Final payable total amount (₹) | schema.sql, live DB |
| `payment_status`| enum | 'pending','completed','failed' | NO | 'pending' | MUL | None | Payment settlement state | schema.sql, live DB |
| `order_status` | enum | 'placed','accepted','preparing','ready','completed','cancelled' | NO | 'placed' | MUL | None | Operational kitchen/order lifecycle state | schema.sql, live DB |
| `notes` | text | | YES | NULL | | None | Customer special prep instructions | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | MUL | None | Order placement timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Order state transition timestamp | schema.sql, live DB |

---

### Table 14: `order_items` [VERIFIED IMPLEMENTED]
- **Purpose:** Line items per order freezing dish name, base price, variant choice, quantity, line total, and all 8 nutrition macros at the moment of order placement.
- **Primary Key:** `id`
- **Foreign Keys:** `order_id` → `orders(id)`, `food_item_id` → `food_items(id)`
- **Verified From:** `database/schema/schema.sql`, `OrderRepository.php`, `OrderService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Line item primary identifier | schema.sql, live DB |
| `order_id` | bigint | 20 (unsigned) | NO | None | MUL | `orders(id)` | Parent master order | schema.sql, live DB |
| `food_item_id` | bigint | 20 (unsigned) | NO | None | MUL | `food_items(id)` | Source food dish | schema.sql, live DB |
| `food_name_snapshot` | varchar | 150 | NO | None | | None | Dish name at purchase time | schema.sql, live DB |
| `base_price_snapshot`| decimal | 10,2 | NO | None | | None | Base price at purchase time (₹) | schema.sql, live DB |
| `variant_name_snapshot`| varchar| 100 | YES | NULL | | None | Selected variant name | schema.sql, live DB |
| `variant_price_snapshot`| decimal| 10,2 | NO | 0.00 | | None | Variant price adjustment (₹) | schema.sql, live DB |
| `quantity` | int | 10 (unsigned) | NO | 1 | | None | Ordered item quantity | schema.sql, live DB |
| `unit_price` | decimal | 10,2 | NO | None | | None | Calculated single item price (₹) | schema.sql, live DB |
| `total_price`| decimal | 10,2 | NO | None | | None | Line total = unit_price * quantity (₹) | schema.sql, live DB |
| `calories` | int | 11 | YES | NULL | | None | Calories snapshot per unit (kcal) | schema.sql, live DB |
| `protein` | decimal | 6,2 | YES | NULL | | None | Protein snapshot per unit (g) | schema.sql, live DB |
| `carbs` | decimal | 6,2 | YES | NULL | | None | Carbs snapshot per unit (g) | schema.sql, live DB |
| `fat` | decimal | 6,2 | YES | NULL | | None | Fat snapshot per unit (g) | schema.sql, live DB |
| `fiber` | decimal | 6,2 | YES | NULL | | None | Fiber snapshot per unit (g) | schema.sql, live DB |
| `sugar` | decimal | 6,2 | YES | NULL | | None | Sugar snapshot per unit (g) | schema.sql, live DB |
| `sodium` | decimal | 7,2 | YES | NULL | | None | Sodium snapshot per unit (mg) | schema.sql, live DB |
| `caffeine` | decimal | 6,2 | YES | NULL | | None | Caffeine snapshot per unit (mg) | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Timestamp of item placement | schema.sql, live DB |

---

### Table 15: `order_item_customizations` [VERIFIED IMPLEMENTED]
- **Purpose:** Snapshot of chosen add-ons and toppings with macro/price adjustments for a specific order line item.
- **Primary Key:** `id`
- **Foreign Keys:** `order_item_id` → `order_items(id)` (ON DELETE CASCADE), `customization_id` → `food_customizations(id)`
- **Verified From:** `database/schema/schema.sql`, `OrderRepository.php`, `OrderService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Customization line ID | schema.sql, live DB |
| `order_item_id` | bigint | 20 (unsigned) | NO | None | MUL | `order_items(id)` | Parent order line item | schema.sql, live DB |
| `customization_id` | bigint | 20 (unsigned) | NO | None | MUL | `food_customizations(id)` | Customization definition | schema.sql, live DB |
| `customization_name_snapshot`| varchar | 100 | NO | None | | None | Snapshot name of add-on | schema.sql, live DB |
| `quantity` | int | 10 (unsigned) | NO | 1 | | None | Selected quantity | schema.sql, live DB |
| `price_adjustment`| decimal | 10,2 | NO | 0.00 | | None | Price adjustment applied (₹) | schema.sql, live DB |
| `calories_adjustment`| int | 11 | YES | NULL | | None | Calories adjustment (kcal) | schema.sql, live DB |
| `protein_adjustment` | decimal | 6,2 | YES | NULL | | None | Protein adjustment (g) | schema.sql, live DB |
| `carbs_adjustment`   | decimal | 6,2 | YES | NULL | | None | Carbs adjustment (g) | schema.sql, live DB |
| `fat_adjustment`     | decimal | 6,2 | YES | NULL | | None | Fat adjustment (g) | schema.sql, live DB |
| `fiber_adjustment`   | decimal | 6,2 | YES | NULL | | None | Fiber adjustment (g) | schema.sql, live DB |
| `sugar_adjustment`   | decimal | 6,2 | YES | NULL | | None | Sugar adjustment (g) | schema.sql, live DB |
| `sodium_adjustment`  | decimal | 7,2 | YES | NULL | | None | Sodium adjustment (mg) | schema.sql, live DB |
| `caffeine_adjustment`| decimal | 6,2 | YES | NULL | | None | Caffeine adjustment (mg) | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Creation timestamp | schema.sql, live DB |

---

### Table 16: `payments` [VERIFIED IMPLEMENTED]
- **Purpose:** Financial settlement transactions linked 1-to-1 with orders.
- **Primary Key:** `id`
- **Foreign Keys:** `order_id` → `orders(id)`
- **Verified From:** `database/schema/schema.sql`, `PaymentRepository.php`, `PaymentService.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Payment primary identifier | schema.sql, live DB |
| `order_id` | bigint | 20 (unsigned) | NO | None | UNI | `orders(id)` | Associated order record (Unique 1-to-1) | schema.sql, live DB |
| `payment_method` | enum | 'cash','upi','card' | NO | None | | None | Settlement mechanism | schema.sql, live DB |
| `transaction_reference` | varchar | 128 | YES | NULL | | None | Unique transaction code (e.g. `TXN-UPI-...`) | schema.sql, live DB |
| `amount` | decimal | 10,2 | NO | None | | None | Amount paid (₹) | schema.sql, live DB |
| `status` | enum | 'pending','completed','failed' | NO | 'pending' | MUL | None | Payment transaction state | schema.sql, live DB |
| `paid_at` | timestamp | | YES | NULL | | None | Payment clearance timestamp | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Record creation timestamp | schema.sql, live DB |

---

### Table 17: `reviews` [VERIFIED IMPLEMENTED]
- **Purpose:** Customer dining reviews, numerical ratings (1–5), comments, and restaurant owner response replies.
- **Primary Key:** `id`
- **Foreign Keys:** `restaurant_id` → `restaurants(id)`, `customer_id` → `customers(id)`, `order_id` → `orders(id)`
- **Verified From:** `database/schema/schema.sql`, `database/migrations/add_review_replies.php`, `ReviewRepository.php`, live database.

| Column | Data Type | Size / Prec | Nullable | Default | Key | References | Description | Verified From |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- | :--- |
| `id` | bigint | 20 (unsigned) | NO | None | PRI | None | Review primary identifier | schema.sql, live DB |
| `restaurant_id` | bigint | 20 (unsigned) | NO | None | MUL | `restaurants(id)` | Reviewed restaurant entity | schema.sql, live DB |
| `customer_id` | bigint | 20 (unsigned) | NO | None | MUL | `customers(id)` | Authoring customer record | schema.sql, live DB |
| `order_id` | bigint | 20 (unsigned) | YES | NULL | MUL | `orders(id)` | Specific dining order reviewed | schema.sql, live DB |
| `rating` | tinyint | 3 (unsigned) | NO | None | | None | Numerical star rating (1 to 5) | schema.sql, live DB |
| `comment` | text | | YES | NULL | | None | Customer written review feedback | schema.sql, live DB |
| `restaurant_reply`| text | | YES | NULL | | None | Official restaurant owner reply text | migration, live DB |
| `replied_at` | timestamp | | YES | NULL | | None | Timestamp when reply was published | migration, live DB |
| `status` | enum | 'pending','approved','hidden' | NO | 'pending' | MUL | None | Moderation approval status | schema.sql, live DB |
| `created_at` | timestamp | | NO | current_timestamp() | | None | Review submission timestamp | schema.sql, live DB |
| `updated_at` | timestamp | | NO | current_timestamp() | | None | Modification timestamp | schema.sql, live DB |

---

# 9. Exact Database Relationships

This textual relationship map represents **only** active relationships backed by MySQL foreign key constraints and validated repository joins:

```
roles
  1 ──────────────< N users
  [Parent: roles.id | Child: users.role_id | Constraint: fk_users_role | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N users
  [Parent: restaurants.id | Child: users.restaurant_id | Constraint: fk_users_restaurant | ON UPDATE CASCADE ON DELETE SET NULL]

users
  1 ──────────────< 1 (optional) restaurants (as owner)
  [Parent: users.id | Child: restaurants.owner_user_id | Constraint: fk_restaurants_owner_user | ON UPDATE CASCADE ON DELETE SET NULL]

restaurants
  1 ──────────────< N branches
  [Parent: restaurants.id | Child: branches.restaurant_id | Constraint: fk_branches_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N categories
  [Parent: restaurants.id | Child: categories.restaurant_id | Constraint: fk_categories_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N food_items
  [Parent: restaurants.id | Child: food_items.restaurant_id | Constraint: fk_food_items_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

categories
  1 ──────────────< N food_items
  [Parent: categories.id | Child: food_items.category_id | Constraint: fk_food_items_category | ON UPDATE CASCADE ON DELETE RESTRICT]

food_items
  1 ──────────────< N food_variants
  [Parent: food_items.id | Child: food_variants.food_item_id | Constraint: fk_food_variants_food_item | ON UPDATE CASCADE ON DELETE CASCADE]

food_items
  1 ──────────────< N food_customizations
  [Parent: food_items.id | Child: food_customizations.food_item_id | Constraint: fk_food_customizations_food_item | ON UPDATE CASCADE ON DELETE CASCADE]

restaurants
  1 ──────────────< N restaurant_tables
  [Parent: restaurants.id | Child: restaurant_tables.restaurant_id | Constraint: fk_tables_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

branches
  1 ──────────────< N restaurant_tables
  [Parent: branches.id | Child: restaurant_tables.branch_id | Constraint: fk_tables_branch | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N qr_tokens
  [Parent: restaurants.id | Child: qr_tokens.restaurant_id | Constraint: fk_qr_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

branches
  1 ──────────────< N qr_tokens
  [Parent: branches.id | Child: qr_tokens.branch_id | Constraint: fk_qr_branch | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurant_tables
  1 ──────────────< N qr_tokens
  [Parent: restaurant_tables.id | Child: qr_tokens.table_id | Constraint: fk_qr_table | ON UPDATE CASCADE ON DELETE RESTRICT]

customers
  1 ──────────────< N orders
  [Parent: customers.id | Child: orders.customer_id | Constraint: fk_orders_customer | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N orders
  [Parent: restaurants.id | Child: orders.restaurant_id | Constraint: fk_orders_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

branches
  1 ──────────────< N orders
  [Parent: branches.id | Child: orders.branch_id | Constraint: fk_orders_branch | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurant_tables
  1 ──────────────< N orders
  [Parent: restaurant_tables.id | Child: orders.table_id | Constraint: fk_orders_table | ON UPDATE CASCADE ON DELETE SET NULL]

orders
  1 ──────────────< N order_items
  [Parent: orders.id | Child: order_items.order_id | Constraint: fk_order_items_order | ON UPDATE CASCADE ON DELETE RESTRICT]

food_items
  1 ──────────────< N order_items
  [Parent: food_items.id | Child: order_items.food_item_id | Constraint: fk_order_items_food | ON UPDATE CASCADE ON DELETE RESTRICT]

order_items
  1 ──────────────< N order_item_customizations
  [Parent: order_items.id | Child: order_item_customizations.order_item_id | Constraint: fk_order_custom_item | ON UPDATE CASCADE ON DELETE CASCADE]

food_customizations
  1 ──────────────< N order_item_customizations
  [Parent: food_customizations.id | Child: order_item_customizations.customization_id | Constraint: fk_order_custom_def | ON UPDATE CASCADE ON DELETE RESTRICT]

orders
  1 ────────────── 1 payments
  [Parent: orders.id | Child: payments.order_id | Constraint: fk_payments_order | ON UPDATE CASCADE ON DELETE RESTRICT]

restaurants
  1 ──────────────< N reviews
  [Parent: restaurants.id | Child: reviews.restaurant_id | Constraint: fk_reviews_restaurant | ON UPDATE CASCADE ON DELETE RESTRICT]

customers
  1 ──────────────< N reviews
  [Parent: customers.id | Child: reviews.customer_id | Constraint: fk_reviews_customer | ON UPDATE CASCADE ON DELETE RESTRICT]

orders
  1 ──────────────< 1 (optional) reviews
  [Parent: orders.id | Child: reviews.order_id | Constraint: fk_reviews_order | ON UPDATE CASCADE ON DELETE SET NULL]
```

---

# 10. Route Inventory

### 10.1 Web Routes (`routes/web.php`) [VERIFIED IMPLEMENTED]

| HTTP Method | Route | Controller | Action | Auth Required | Role | Scope | Purpose | Status |
| :--- | :--- | :--- | :--- | :---: | :---: | :---: | :--- | :--- |
| `GET` | `/` | `HomeController` | `index` | No | Public | Public | Landing / Welcome portal | VERIFIED IMPLEMENTED |
| `GET` | `/menu/welcome` | `HomeController` | `index` | No | Public | Public | Menu Welcome screen | VERIFIED IMPLEMENTED |
| `GET` | `/menu` | `MenuController` | `index` | No | Public | Table / Rest | Interactive customer digital menu | VERIFIED IMPLEMENTED |
| `GET` | `/cart` | Closure | redirect | No | Public | Public | Redirects to `/menu/checkout` | VERIFIED IMPLEMENTED |
| `GET` | `/menu/cart` | Closure | redirect | No | Public | Public | Redirects to `/menu/checkout` | VERIFIED IMPLEMENTED |
| `GET` | `/menu/checkout` | `CheckoutController` | `index` | No | Public | Table / Rest | Checkout & table verification | VERIFIED IMPLEMENTED |
| `GET` | `/menu/confirmation/{orderNumber}` | `OrderController` | `confirmation`| No | Public | Order scope | Post-order confirmation screen | VERIFIED IMPLEMENTED |
| `GET` | `/menu/tracking/{orderNumber}` | `OrderController` | `tracking` | No | Public | Order scope | Live kitchen tracking screen | VERIFIED IMPLEMENTED |
| `GET` | `/owner` | Closure | redirect | No | Owner | Rest scope | Redirects to `/owner/dashboard` | VERIFIED IMPLEMENTED |
| `GET` | `/owner/login` | `Owner\AuthController` | `loginForm` | No | Public | Public | Restaurant Owner login form | VERIFIED IMPLEMENTED |
| `POST`| `/owner/login` | `Owner\AuthController` | `login` | No | Public | Public | Authenticate owner & set session | VERIFIED IMPLEMENTED |
| `GET` | `/owner/logout` | `Owner\AuthController` | `logout` | Yes | Owner/Staff | Rest scope | Destroy owner session & redirect | VERIFIED IMPLEMENTED |
| `GET` | `/owner/dashboard` | `Owner\DashboardController`| `index` | Yes | Owner/Mgr | Rest scope | Owner Overview (Monitoring only) | VERIFIED IMPLEMENTED |
| `GET` | `/owner/orders` | `Owner\OrderController` | `index` | Yes | Owner/Staff | Rest scope | Live Orders (Monitoring with filter pills) | VERIFIED IMPLEMENTED |
| `GET` | `/owner/orders/{id}/details` | `Owner\OrderController` | `details` | Yes | Owner/Staff | Rest scope | Fetch itemized order details modal JSON | VERIFIED IMPLEMENTED |
| `GET` | `/owner/kitchen-orders` | `Owner\OrderController` | `kitchenOrders`| Yes| Owner/Staff | Rest scope | Kitchen Live Orders 5-col Kanban board | VERIFIED IMPLEMENTED |
| `POST`| `/owner/orders/{id}/status` | `Owner\OrderController` | `updateStatus` | Yes | Owner/Staff | Rest scope | Advance kitchen status (AJAX/POST) | VERIFIED IMPLEMENTED |
| `POST`| `/owner/orders/{id}/payment` | `Owner\OrderController` | `updatePayment`| Yes | Owner/Staff | Rest scope | Update order payment settlement | VERIFIED IMPLEMENTED |
| `GET` | `/owner/menu` | `Owner\MenuController` | `index` | Yes | Owner/Mgr | Rest scope | Menu & Foods dish management | VERIFIED IMPLEMENTED |
| `POST`| `/owner/menu/create` | `Owner\MenuController` | `create` | Yes | Owner/Mgr | Rest scope | Add new food dish with nutrition | VERIFIED IMPLEMENTED |
| `POST`| `/owner/menu/{id}/edit` | `Owner\MenuController` | `update` | Yes | Owner/Mgr | Rest scope | Update existing dish price/macros/img | VERIFIED IMPLEMENTED |
| `POST`| `/owner/menu/{id}/toggle` | `Owner\MenuController` | `toggleAvailability`| Yes | Owner/Mgr | Rest scope | Instant food item availability toggle | VERIFIED IMPLEMENTED |
| `POST`| `/owner/menu/{id}/delete` | `Owner\MenuController` | `delete` | Yes | Owner/Mgr | Rest scope | Delete food item from catalog | VERIFIED IMPLEMENTED |
| `GET` | `/owner/tables` | `Owner\TableController` | `index` | Yes | Owner/Staff | Rest scope | Tables & QR codes (Tables 1–12) | VERIFIED IMPLEMENTED |
| `POST`| `/owner/tables/create` | `Owner\TableController` | `create` | Yes | Owner/Mgr | Rest scope | Create new table with auto QR token | VERIFIED IMPLEMENTED |
| `POST`| `/owner/tables/{id}/status` | `Owner\TableController` | `updateStatus` | Yes | Owner/Staff | Rest scope | Click-to-toggle occupancy (Available/Occupied)| VERIFIED IMPLEMENTED |
| `GET` | `/owner/reviews` | `Owner\ReviewController` | `index` | Yes | Owner/Mgr | Rest scope | Customer Reviews & rating distribution | VERIFIED IMPLEMENTED |
| `POST`| `/owner/reviews/{id}/respond`| `Owner\ReviewController` | `respond` | Yes | Owner/Mgr | Rest scope | Submit restaurant response reply | VERIFIED IMPLEMENTED |
| `GET` | `/owner/staff` | `Owner\StaffController` | `index` | Yes | Owner | Rest scope | Staff & team access management | VERIFIED IMPLEMENTED |
| `POST`| `/owner/staff/create` | `Owner\StaffController` | `create` | Yes | Owner | Rest scope | Register new staff / manager user | VERIFIED IMPLEMENTED |
| `POST`| `/owner/staff/{id}/toggle` | `Owner\StaffController` | `toggleStatus` | Yes | Owner | Rest scope | Toggle staff active/inactive state | VERIFIED IMPLEMENTED |
| `GET` | `/owner/analytics` | `Owner\AnalyticsController`| `index` | Yes | Owner/Mgr | Rest scope | Sales & Macro Nutrient Analytics | VERIFIED IMPLEMENTED |
| `GET` | `/owner/live-menu` | `Owner\MenuController` | `livePreview` | Yes | Owner/Staff | Rest scope | Live digital menu preview & cart simulation| VERIFIED IMPLEMENTED |
| `GET` | `/owner/profile` | `Owner\ProfileController` | `index` | Yes | Owner | Rest scope | Restaurant brand, phone, email profile | VERIFIED IMPLEMENTED |
| `POST`| `/owner/profile` | `Owner\ProfileController` | `update` | Yes | Owner | Rest scope | Save restaurant profile changes | VERIFIED IMPLEMENTED |
| `GET` | `/owner/settings` | `Owner\SettingsController` | `index` | Yes | Owner | Rest scope | Restaurant preferences & operational hours | VERIFIED IMPLEMENTED |
| `GET` | `/admin` | Closure | redirect | No | Admin | Global | Redirects to `/admin/dashboard` | VERIFIED IMPLEMENTED |
| `GET` | `/admin/login` | `Admin\AuthController` | `loginForm` | No | Public | Public | Platform Super Admin Sign In form | VERIFIED IMPLEMENTED |
| `POST`| `/admin/login` | `Admin\AuthController` | `login` | No | Public | Public | Authenticate Super Admin session | VERIFIED IMPLEMENTED |
| `GET` | `/admin/logout` | `Admin\AuthController` | `logout` | Yes | Admin | Global | Destroy Super Admin session | VERIFIED IMPLEMENTED |
| `GET` | `/admin/dashboard` | `Admin\DashboardController`| `index` | Yes | Admin | Global | Platform Overview (Cross-tenant metrics) | VERIFIED IMPLEMENTED |
| `GET` | `/admin/restaurants` | `Admin\RestaurantController`| `index` | Yes | Admin | Global | Registered Restaurants & Status Cards | VERIFIED IMPLEMENTED |
| `POST`| `/admin/restaurants/{id}/status`| `Admin\RestaurantController`| `updateStatus`| Yes| Admin | Global | Approve / Suspend restaurant tenant | VERIFIED IMPLEMENTED |
| `GET` | `/admin/portal-inspect` | `Admin\RestaurantController`| `inspectDefault`| Yes| Admin| Global | Tenant Inspection for default tenant #1 | VERIFIED IMPLEMENTED |
| `GET` | `/admin/restaurants/{id}/inspect`| `Admin\RestaurantController`| `inspect` | Yes | Admin | Global | Deep inspection of specific restaurant | VERIFIED IMPLEMENTED |
| `GET` | `/admin/users` | `Admin\UserController` | `index` | Yes | Admin | Global | Users & Access platform roles directory | VERIFIED IMPLEMENTED |
| `POST`| `/admin/users/create` | `Admin\UserController` | `create` | Yes | Admin | Global | Create platform administrator / owner | VERIFIED IMPLEMENTED |
| `POST`| `/admin/users/{id}/status` | `Admin\UserController` | `updateStatus` | Yes | Admin | Global | Toggle user active/suspended status | VERIFIED IMPLEMENTED |

---

### 10.2 API Routes (`routes/api.php`) [VERIFIED IMPLEMENTED]

| HTTP Method | Route | Controller | Action | Auth Required | Purpose | Status |
| :--- | :--- | :--- | :--- | :---: | :--- | :--- |
| `GET` | `/api/health` | Closure | inline | No | System health check (app, DB status) | VERIFIED IMPLEMENTED |
| `GET` | `/api/restaurant` | `RestaurantController` | `getRestaurant` | No | Get active restaurant tenant details | VERIFIED IMPLEMENTED |
| `GET` | `/api/categories` | `RestaurantController` | `getCategories` | No | Get active menu categories | VERIFIED IMPLEMENTED |
| `GET` | `/api/foods` | `RestaurantController` | `getFoods` | No | Get food dishes with search & category filters| VERIFIED IMPLEMENTED |
| `GET` | `/api/foods/{id}` | `FoodController` | `show` | No | Get single dish with variants & customizations | VERIFIED IMPLEMENTED |
| `GET` | `/api/foods/{id}/variants` | `FoodController` | `variants` | No | Get variants for specific food item | VERIFIED IMPLEMENTED |
| `GET` | `/api/foods/{id}/customizations`| `FoodController` | `customizations` | No | Get customization add-ons for food item | VERIFIED IMPLEMENTED |
| `POST`| `/api/cart/validate` | `CartController` | `validateCart` | No | Validate & recalculate prices/macros | VERIFIED IMPLEMENTED |
| `POST`| `/api/orders` | `OrderController` | `createOrder` | No | Place order, lock table, snapshot items | VERIFIED IMPLEMENTED |
| `GET` | `/api/orders/table-latest` | `OrderController` | `getLatestTableOrder`| No | Poll latest order status for live tracking | VERIFIED IMPLEMENTED |
| `GET` | `/api/orders/{orderNumber}` | `OrderController` | `getOrder` | No | Get complete order receipt by order number | VERIFIED IMPLEMENTED |
| `POST`| `/api/payment/simulate` | `PaymentController` | `simulate` | No | Execute simulated payment settlement | VERIFIED IMPLEMENTED |

---

# 11. Controller Inventory

Total Active Controllers: **22 Classes** [VERIFIED IMPLEMENTED]

### Customer & Public Controllers
1. **`App\Controllers\HomeController`**
   - Methods: `index()`
   - Routes: `GET /`, `GET /menu/welcome`
   - Purpose: Renders welcome customer entrance page with table selection.
   - Services Used: None
   - Repositories Used: `RestaurantRepository`
2. **`App\Controllers\MenuController`**
   - Methods: `index()`
   - Routes: `GET /menu`
   - Purpose: Renders customer-facing digital menu with food catalog and QR context.
   - Services Used: `MenuService`, `QrService`
   - Repositories Used: `RestaurantRepository`
3. **`App\Controllers\CheckoutController`**
   - Methods: `index()`
   - Routes: `GET /menu/checkout`
   - Purpose: Validates customer session/QR token and renders checkout page.
   - Services Used: `MenuService`, `QrService`
   - Repositories Used: `RestaurantRepository`
4. **`App\Controllers\OrderController`**
   - Methods: `createOrder()`, `getOrder()`, `confirmation()`, `tracking()`, `getLatestTableOrder()`
   - Routes: `POST /api/orders`, `GET /api/orders/{orderNumber}`, `GET /menu/confirmation/{orderNumber}`, `GET /menu/tracking/{orderNumber}`, `GET /api/orders/table-latest`
   - Purpose: Customer order placement, tracking views, and status polling.
   - Services Used: `OrderService`
   - Repositories Used: `OrderRepository`
5. **`App\Controllers\CartController`**
   - Methods: `validateCart()`
   - Routes: `POST /api/cart/validate`
   - Purpose: AJAX validation and recalculation of cart prices and macros.
   - Services Used: `CartService`
   - Repositories Used: None
6. **`App\Controllers\FoodController`**
   - Methods: `show()`, `variants()`, `customizations()`
   - Routes: `GET /api/foods/{id}`, `GET /api/foods/{id}/variants`, `GET /api/foods/{id}/customizations`
   - Purpose: Provides dish option metadata for modal popups.
   - Services Used: `MenuService`
   - Repositories Used: `FoodRepository`
7. **`App\Controllers\PaymentController`**
   - Methods: `simulate()`
   - Routes: `POST /api/payment/simulate`
   - Purpose: Executes simulated payment transaction (`cash`, `upi`, `card`).
   - Services Used: `PaymentService`
   - Repositories Used: None
8. **`App\Controllers\RestaurantController`**
   - Methods: `getRestaurant()`, `getCategories()`, `getFoods()`
   - Routes: `GET /api/restaurant`, `GET /api/categories`, `GET /api/foods`
   - Purpose: JSON APIs for mobile/client catalog loading.
   - Services Used: `MenuService`
   - Repositories Used: None

### Restaurant Owner Portal Controllers (`App\Controllers\Owner\*`)
9. **`Owner\AuthController`**: `loginForm()`, `login()`, `logout()` (`/owner/login`, `/owner/logout`)
10. **`Owner\DashboardController`**: `index()` (`/owner/dashboard` — Overview monitoring only)
11. **`Owner\OrderController`**: `index()`, `details()`, `kitchenOrders()`, `updateStatus()`, `updatePayment()` (`/owner/orders`, `/owner/kitchen-orders`, `/owner/orders/{id}/status`, `/owner/orders/{id}/details`)
12. **`Owner\MenuController`**: `index()`, `livePreview()`, `create()`, `update()`, `toggleAvailability()`, `delete()` (`/owner/menu`, `/owner/live-menu`, `/owner/menu/create`, `/owner/menu/{id}/edit`, `/owner/menu/{id}/toggle`, `/owner/menu/{id}/delete`)
13. **`Owner\TableController`**: `index()`, `create()`, `updateStatus()` (`/owner/tables`, `/owner/tables/create`, `/owner/tables/{id}/status`)
14. **`Owner\ReviewController`**: `index()`, `respond()` (`/owner/reviews`, `/owner/reviews/{id}/respond`)
15. **`Owner\StaffController`**: `index()`, `create()`, `toggleStatus()` (`/owner/staff`, `/owner/staff/create`, `/owner/staff/{id}/toggle`)
16. **`Owner\AnalyticsController`**: `index()` (`/owner/analytics`)
17. **`Owner\ProfileController`**: `index()`, `update()` (`/owner/profile`)
18. **`Owner\SettingsController`**: `index()` (`/owner/settings`)

### Platform Super Admin Controllers (`App\Controllers\Admin\*`)
19. **`Admin\AuthController`**: `loginForm()`, `login()`, `logout()` (`/admin/login`, `/admin/logout`)
20. **`Admin\DashboardController`**: `index()` (`/admin/dashboard` — Platform Overview)
21. **`Admin\RestaurantController`**: `index()`, `inspect()`, `inspectDefault()`, `updateStatus()` (`/admin/restaurants`, `/admin/portal-inspect`, `/admin/restaurants/{id}/inspect`, `/admin/restaurants/{id}/status`)
22. **`Admin\UserController`**: `index()`, `create()`, `updateStatus()` (`/admin/users`, `/admin/users/create`, `/admin/users/{id}/status`)

---

# 12. Repository Inventory

Total Active Repositories: **9 Classes** [VERIFIED IMPLEMENTED]

1. **`App\Repositories\FoodRepository`**
   - Methods: `getByRestaurantId()`, `getByIdAndRestaurant()`, `getVariants()`, `getCustomizations()`, `getAllByRestaurantId()`, `toggleAvailability()`, `createFoodItem()`, `updateFoodItem()`, `deleteFoodItem()`, `countByRestaurant()`
   - Tables Accessed: `food_items`, `food_variants`, `food_customizations`, `categories`
2. **`App\Repositories\OrderRepository`**
   - Methods: `createCustomer()`, `createOrder()`, `addOrderItem()`, `addOrderItemCustomization()`, `findByOrderNumber()`, `getLatestOrderByTable()`, `findWithItemsById()`, `updateStatus()`, `updateStatusForRestaurant()`, `updatePaymentStatusForRestaurant()`, `getTodayStats()`, `getRecentOrders()`, `getKitchenOrders()`, `getMacroStats()`, `getPopularItems()`, `getPaymentStats()`, `getSalesTrendDaily()`, `getSalesTrendWeekly()`, `getSalesTrendMonthly()`, `getCategorySalesDistribution()`, `getSummaryByRestaurant()`
   - Tables Accessed: `orders`, `order_items`, `order_item_customizations`, `customers`, `restaurant_tables`, `payments`, `food_items`, `categories`
3. **`App\Repositories\RestaurantRepository`**
   - Methods: `findById()`, `findBySlug()`, `getBranch()`, `findAnyById()`, `updateProfile()`, `getAll()`, `countAll()`, `updateStatus()`, `getInspectionStats()`, `getMonthlyRevenue()`, `getInspectionBranches()`
   - Tables Accessed: `restaurants`, `branches`, `restaurant_tables`, `food_items`, `orders`, `users`
4. **`App\Repositories\BranchRepository`**
   - Methods: `getTables()`, `getTableById()`, `getBranchesByRestaurant()`, `getTablesWithQr()`, `updateTableStatus()`, `createTable()`, `ensureQrTokenForTable()`, `countTablesByRestaurant()`
   - Tables Accessed: `branches`, `restaurant_tables`, `qr_tokens`, `orders`
5. **`App\Repositories\CategoryRepository`**
   - Methods: `getByRestaurantId()`, `findById()`
   - Tables Accessed: `categories`
6. **`App\Repositories\PaymentRepository`**
   - Methods: `createPayment()`, `findByOrderId()`
   - Tables Accessed: `payments`
7. **`App\Repositories\ReviewRepository`**
   - Methods: `findByRestaurant()`, `addReply()`, `getSummaryStats()`
   - Tables Accessed: `reviews`, `customers`, `orders`
8. **`App\Repositories\StaffRepository`**
   - Methods: `findByRestaurant()`, `createStaff()`, `toggleStatus()`
   - Tables Accessed: `users`, `roles`
9. **`App\Repositories\UserRepository`**
   - Methods: `findByEmail()`, `findById()`, `getAll()`, `createUser()`, `toggleStatus()`, `countAll()`
   - Tables Accessed: `users`, `roles`, `restaurants`

---

# 13. Service Inventory

Total Active Domain Services: **7 Classes** [VERIFIED IMPLEMENTED]

1. **`App\Services\CartService`**
   - Methods: `validateCartItems(array $rawCartItems, int $restaurantId): array`
   - Repositories: `FoodRepository`, `PricingService`, `NutritionService`
   - Purpose: Server-side validation of customer cart items against active database records to prevent client-side tampering of prices or macros.
2. **`App\Services\PricingService`**
   - Methods: `calculateSingleItemPrice()`, `calculateLineTotal()`, `calculateOrderTotals()`
   - Dependencies: None
   - Purpose: Mathematical computation of itemized prices, variants adjustments, customization sums, GST tax, and order totals.
3. **`App\Services\NutritionService`**
   - Methods: `calculateItemNutrition()`, `scaleNutritionForQuantity()`, `aggregateCartNutrition()`
   - Dependencies: None
   - Purpose: Exact arithmetic summation of 8 nutrient macros across dishes, variants, customizations, and quantities.
4. **`App\Services\OrderService`**
   - Methods: `placeOrder(array $payload): array`, `trackOrder(string $orderNumber): ?array`
   - Repositories: `OrderRepository`, `RestaurantRepository`, `BranchRepository`, `CartService`, `QrService`
   - Purpose: End-to-end transactional order placement, token validation, customer creation, item snapshotting, and tracking lookup.
5. **`App\Services\PaymentService`**
   - Methods: `simulatePayment(int $orderId, string $method): array`
   - Repositories: `PaymentRepository`, `OrderRepository`
   - Purpose: Settlement simulation supporting `cash`, `upi`, `card`, advancing order status to `accepted` and payment status to `completed`.
6. **`App\Services\QrService`**
   - Methods: `resolveToken(string $token): ?array`
   - Dependencies: PDO Database
   - Purpose: Decodes and authenticates QR tokens against `qr_tokens`, `restaurant_tables`, `branches`, and `restaurants`.
7. **`App\Services\MenuService`**
   - Methods: `getRestaurant()`, `getRestaurantBySlug()`, `getCategories()`, `getFoods()`, `getFoodDetails()`
   - Repositories: `RestaurantRepository`, `CategoryRepository`, `FoodRepository`
   - Purpose: Catalog data retrieval and option group formatting for modal presentation.

---

# 14. View/Page Inventory

| Portal | Page Title | Template File | Route | Purpose | Role | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Customer** | Welcome Page | `customer/welcome.php` | `/menu/welcome` | Table & entrance selection | Public | VERIFIED IMPLEMENTED |
| **Customer** | Digital Menu | `customer/menu.php` | `/menu` | Food selection & cart drawer | Public | VERIFIED IMPLEMENTED |
| **Customer** | Checkout | `customer/checkout.php` | `/menu/checkout` | Order review & payment choice | Public | VERIFIED IMPLEMENTED |
| **Customer** | Confirmation | `customer/confirmation.php` | `/menu/confirmation/{num}` | Order summary & ticket | Public | VERIFIED IMPLEMENTED |
| **Customer** | Live Tracking | `customer/tracking.php` | `/menu/tracking/{num}` | 5-stage kitchen progress track | Public | VERIFIED IMPLEMENTED |
| **Owner** | Sign In | `owner/login.php` | `/owner/login` | Owner credentials authentication | Public | VERIFIED IMPLEMENTED |
| **Owner** | Overview | `owner/dashboard.php` | `/owner/dashboard` | Monitoring KPI dashboard | Owner/Mgr | VERIFIED IMPLEMENTED |
| **Owner** | Live Orders | `owner/orders.php` | `/owner/orders` | Monitoring orders with details modal | Owner/Staff | VERIFIED IMPLEMENTED |
| **Owner** | Kitchen Live Orders | `owner/kitchen_orders.php` | `/owner/kitchen-orders` | 5-column Kanban operational board | Owner/Staff | VERIFIED IMPLEMENTED |
| **Owner** | Menu & Foods | `owner/menu.php` | `/owner/menu` | Dish CRUD, editing & availability | Owner/Mgr | VERIFIED IMPLEMENTED |
| **Owner** | Tables & QR | `owner/tables.php` | `/owner/tables` | Tables 1–12 QR codes & occupancy | Owner/Staff | VERIFIED IMPLEMENTED |
| **Owner** | Customer Reviews | `owner/reviews.php` | `/owner/reviews` | Reviews rating filter & replies | Owner/Mgr | VERIFIED IMPLEMENTED |
| **Owner** | Staff Management | `owner/staff.php` | `/owner/staff` | Restaurant team access & status | Owner | VERIFIED IMPLEMENTED |
| **Owner** | Sales & Analytics | `owner/analytics.php` | `/owner/analytics` | Financial & macro nutrition metrics | Owner/Mgr | VERIFIED IMPLEMENTED |
| **Owner** | Live Menu Preview | `owner/live_menu.php` | `/owner/live-menu` | High-res food photos & cart demo | Owner/Staff | VERIFIED IMPLEMENTED |
| **Owner** | Profile | `owner/profile.php` | `/owner/profile` | Restaurant branding & address | Owner | VERIFIED IMPLEMENTED |
| **Owner** | Settings | `owner/settings.php` | `/owner/settings` | Operational preferences | Owner | VERIFIED IMPLEMENTED |
| **Admin** | Admin Sign In | `admin/login.php` | `/admin/login` | Platform administrator authentication | Public | VERIFIED IMPLEMENTED |
| **Admin** | Platform Overview | `admin/dashboard.php` | `/admin/dashboard` | Platform KPI stats & registrations | Admin | VERIFIED IMPLEMENTED |
| **Admin** | Registered Restaurants| `admin/restaurants.php` | `/admin/restaurants` | Tenant management & status filter | Admin | VERIFIED IMPLEMENTED |
| **Admin** | Portal Inspection | `admin/portal_inspect.php`| `/admin/portal-inspect` | Deep tenant audit & branch review | Admin | VERIFIED IMPLEMENTED |
| **Admin** | Users & Access | `admin/users.php` | `/admin/users` | Platform user roles & activation | Admin | VERIFIED IMPLEMENTED |

---

# 15. Customer Workflow

The complete implemented customer flow from table entrance to completion is traced below [VERIFIED IMPLEMENTED]:

1. **QR Scan & Entrance:** Customer scans physical QR code on table containing URL `http://<host>/menu?token=<token>`.
2. **Context Resolution:** `MenuController::index()` or `CheckoutController::index()` passes token to `QrService::resolveToken()`. The service joins `qr_tokens`, `restaurant_tables`, `branches`, and `restaurants`, locking `table_id`, `branch_id`, and `restaurant_id`. If scanned without token, fallback uses default restaurant #1 and Table 4.
3. **Menu Browsing:** Dishes load categorized with gourmet Unsplash photography, diet pills (Veg, Non-Veg, Vegan, Jain), base price, calorie tags, and protein metrics.
4. **Food Selection & Customization:** Clicking a dish card triggers `openFoodDetailModal(foodId)`. The modal fetches `/api/foods/{id}`, rendering variants (portion sizes) and customization add-ons grouped by group name.
5. **Live Nutrition & Price Recalculation:** Client-side `nutrition.js` and `pricing.js` instantly recalculate real-time line totals and calories/macros as checkboxes or radio buttons are toggled.
6. **Cart Addition:** Clicking "Add to Cart" stores configured items in `localStorage.getItem('hb_cart')` and updates the floating cart bar and drawer count.
7. **Checkout:** Clicking checkout opens `/menu/checkout`. The page loads customer details form, dining type toggle (`Dine-In` vs `Takeaway`), and payment method selection (`UPI`, `Card`, `Cash`).
8. **Server-Side Re-validation & Order Placement:** Clicking "Place Order" transmits payload to `POST /api/orders`. `OrderService::placeOrder()` revalidates every item against MySQL via `CartService`, creates a record in `customers`, generates unique `HB-...` order code, inserts into `orders`, creates frozen snapshot rows in `order_items` (with 8 macros), and inserts into `order_item_customizations`.
9. **Instant Payment Clearance:** Checkout calls `POST /api/payment/simulate`, which records a transaction in `payments` and advances the order status to `accepted` and payment status to `completed`. Cart is cleared from `localStorage`.
10. **Confirmation & Live Tracking:** Customer is redirected to `/menu/confirmation/{orderNumber}`. From there, clicking "Track Live Kitchen Status" navigates to `/menu/tracking/{orderNumber}` where `live-kitchen.js` polls `/api/orders/table-latest` to display live progress.
11. **Review Submission:** Reviews are submitted directly linked to `restaurant_id` and `order_id` in `reviews` table.

---

# 16. QR Workflow

- **Token Generation:** Generated in `BranchRepository::createTable()` / `ensureQrTokenForTable()` using `bin2hex(random_bytes(16))` [VERIFIED IMPLEMENTED].
- **Token Storage:** Stored in `qr_tokens` table with columns `restaurant_id`, `branch_id`, `table_id`, `token`, `status` (`active`), and `expires_at` [VERIFIED IMPLEMENTED].
- **Token Resolution Query:** Executed in `QrService::resolveToken(string $token)`:
  ```sql
  SELECT qr.id AS qr_token_id, qr.token, qr.status AS qr_status,
         t.id AS table_id, t.table_number, t.status AS table_status,
         b.id AS branch_id, b.name AS branch_name,
         r.id AS restaurant_id, r.name AS restaurant_name
  FROM qr_tokens qr
  JOIN restaurant_tables t ON qr.table_id = t.id
  JOIN branches b ON t.branch_id = b.id
  JOIN restaurants r ON b.restaurant_id = r.id
  WHERE qr.token = :token AND qr.status = 'active'
    AND r.status = 'approved' AND b.status = 'active'
  LIMIT 1;
  ```
- **Security & Parameter Tampering Protection:** In `OrderService::placeOrder()`, if a valid `qr_token` is provided, any conflicting `restaurant_id`, `branch_id`, or `table_id` sent in the request body is strictly discarded and overwritten with the verified database QR context [VERIFIED IMPLEMENTED].
- **Rendering & Scannability:** Standalone `public/assets/js/qrcode.min.js` canvas engine renders high-density scannable QR codes in real-time in `resources/views/owner/tables.php` with 1-click PNG download and print ticket popups [VERIFIED IMPLEMENTED].

---

# 17. Food Catalog Workflow

- **Catalog Entities:** Categories (`categories`), Food Items (`food_items`), Variants (`food_variants`), Customizations (`food_customizations`).
- **Dietary Lifestyle Classification:** Enforced by ENUM column `food_items.food_type`:
  - `vegetarian`
  - `non_vegetarian`
  - `vegan`
  - `jain`
  - `other`
- **Availability Governance:** Controlled by boolean flags `food_items.is_available`, `food_variants.is_available`, and `food_customizations.is_available`.
- **Portal Interfaces:**
  - Customer: Browsing only active & available items (`is_available = 1`).
  - Owner: Full CRUD control via `app/Controllers/Owner/MenuController.php` (`/owner/menu`). Ability to add new dishes, edit price, edit 8-macro nutritional values, toggle live availability, and delete dishes.
  - Admin: Inspection access via `/admin/portal-inspect`.

---

# 18. Customization Workflow

- **Grouping Structure:** Options are organized by `food_customizations.group_name` (e.g. `Choose Base`, `Protein Options`, `Dressings`, `Extra Toppings`).
- **Cardinality Limits:**
  - `is_required`: Flag requiring at least 1 selection in group.
  - `min_quantity`: Minimum choices (default 0).
  - `max_quantity`: Maximum choices allowed (default 1).
- **Adjustments:** Each customization row defines individual `price_adjustment`, `calories_adjustment`, `protein_adjustment`, `carbs_adjustment`, `fat_adjustment`, `fiber_adjustment`, `sugar_adjustment`, `sodium_adjustment`, and `caffeine_adjustment`.
- **Snapshot Persistence:** Frozen into `order_item_customizations` with exact names, price deltas, and macro deltas at order placement time.

---

# 19. Pricing Logic

Implemented in `App\Services\PricingService` and mirrored client-side in `public/assets/js/pricing.js` [VERIFIED IMPLEMENTED]:

1. **Single Configured Item Price Formula:**
   $$\text{UnitPrice} = \text{BasePrice} + \text{VariantAdjustment} + \sum (\text{CustomizationAdjustment} \times \text{CustomizationQuantity})$$
2. **Item Line Total Formula:**
   $$\text{LineTotal} = \text{UnitPrice} \times \max(1, \text{FoodQuantity})$$
3. **Order Totals Formula:**
   $$\text{Subtotal} = \sum \text{LineTotals}$$
   $$\text{Tax} = \text{round}(\text{Subtotal} \times \text{TaxRate}, 2) \quad (\text{Default TaxRate} = 0.05 \text{ or } 5\%)$$
   $$\text{ServiceCharge} = \text{round}(\text{ServiceCharge}, 2) \quad (\text{Default} = 0.00)$$
   $$\text{TotalAmount} = \text{Subtotal} + \text{Tax} + \text{ServiceCharge}$$

---

# 20. Nutrition Logic

Implemented in `App\Services\NutritionService` across **8 distinct nutritional fields** and mirrored client-side in `public/assets/js/nutrition.js` [VERIFIED IMPLEMENTED]:

- **Fields Tracked:** `calories` (kcal), `protein` (g), `carbs` (g), `fat` (g), `fiber` (g), `sugar` (g), `sodium` (mg), `caffeine` (mg).
- **Calculation Formula per Item:**
  $$\text{Nutrient}_{\text{item}} = \text{BaseNutrient} + \text{VariantAdjustment} + \sum (\text{CustomizationAdjustment} \times \text{CustomizationQuantity})$$
- **Scaled Line Nutrition:**
  $$\text{Nutrient}_{\text{line}} = \text{Nutrient}_{\text{item}} \times \max(1, \text{Quantity})$$
- **NULL Handling Rule:** If nutritional data is not configured for a dish, it remains `null` rather than coercing to 0. `calories` is rounded to nearest integer; all other macros are rounded to 2 decimal places.

---

# 21. Cart Workflow

- **Storage Location:** Client browser `localStorage` under key `hb_cart` managed by `public/assets/js/state.js` and `cart.js`.
- **Session Keys:** PHP server uses native sessions (`$_SESSION['cart']` supported for direct server renders, while primary live client cart operates asynchronously via LocalStorage).
- **Cart API Endpoints:**
  - `POST /api/cart/validate`: Receives JSON cart array, passes it to `CartService::validateCartItems()`, returns verified totals and nutrition aggregations.
- **Cart Mutation Actions:**
  - `Cart.addItem(newItem)`: Appends or merges identical item configurations.
  - `Cart.updateQuantity(index, newQty)`: Recalculates line total.
  - `Cart.removeItem(index)`: Removes line item.
  - `Cart.clear()`: Resets `State.cart = []` and removes `hb_cart` from storage.
- **Identified Cart Bug / Guardrail [VERIFIED RESOLVED]:** Previously, cart addition on some views fired native browser alerts. Now all additions update the reactive badge counter (`cart-item-count`) and trigger non-intrusive toast notifications.

---

# 22. Order Workflow

- **Order Types:** Supported by ENUM column `orders.order_type`:
  - `dine_in` (Requires valid `table_id`)
  - `takeaway` (`table_id` is set to `NULL`)
- **Order Number Format:** `HB-[HEX_TIMESTAMP]-[RANDOM_3_DIGIT]` (e.g. `HB-1001`, `HB-18A3B4C-782`).
- **Lifecycle Statuses & Valid Transitions:**
  ```
  [placed] ──→ [accepted] ──→ [preparing] ──→ [ready] ──→ [completed]
     │
     └──→ [cancelled]
  ```
- **Permission Matrix for Status Changes:**
  - Customer: Cannot modify status (View/track only).
  - Restaurant Owner / Manager / Staff: Can advance status on Kitchen Live Orders Kanban board (`/owner/kitchen-orders`) or via `POST /owner/orders/{id}/status`.
  - Overview / Live Orders Views: Status changes are strictly prohibited on Overview (`/owner/dashboard`) and Live Orders (`/owner/orders`), which are strictly monitoring views.

---

# 23. Kitchen Workflow

- **Route:** `GET /owner/kitchen-orders` [VERIFIED IMPLEMENTED]
- **Controller:** `App\Controllers\Owner\OrderController::kitchenOrders()`
- **Repository Method:** `App\Repositories\OrderRepository::getKitchenOrders($restaurantId)`
- **View:** `resources/views/owner/kitchen_orders.php`
- **Kanban Architecture (5 Columns):**
  1. `New Orders` (Status: `placed`) → Action Button: **"Accept Order"** (Advances to `accepted`)
  2. `Accepted` (Status: `accepted`) → Action Button: **"Start Preparing"** (Advances to `preparing`)
  3. `Preparing` (Status: `preparing`) → Action Button: **"Mark Ready"** (Advances to `ready`)
  4. `Ready` (Status: `ready`) → Action Button: **"Complete Order"** (Advances to `completed`)
  5. `Completed` (Status: `completed`) → Static archived column (No button)
- **Live Advancement Logic:** Clicking any action button executes `advanceOrderStatus(orderId, nextStatus)` via asynchronous fetch to `POST /owner/orders/{id}/status` with automatic UI reload and toast notification.

---

# 24. Customer Live Kitchen Workflow

- **Polling Endpoint:** `GET /api/orders/table-latest?table_id={tableId}&restaurant_id={restaurantId}` [VERIFIED IMPLEMENTED]
- **Client Poller Script:** `public/assets/js/live-kitchen.js`
- **Execution:** Invoked via `window.refreshLiveKitchenStatus()`.
- **Visual Progress Track:** Updates 5 distinct HTML timeline nodes (`step-placed`, `step-accepted`, `step-preparing`, `step-ready`, `step-completed`) applying classes `.completed`, `.active`, or `.pending`.

---

# 25. Review System

- **Database Table:** `reviews` [VERIFIED IMPLEMENTED]
- **Actual Foreign Key Relationships:**
  - `restaurant_id` → `restaurants(id)`
  - `customer_id` → `customers(id)`
  - `order_id` → `orders(id)` (Nullable)
  - *Note:* Reviews are attached to restaurants and orders, **not** to individual food items.
- **Reply Persistence:**
  - Columns: `reviews.restaurant_reply` (TEXT) and `reviews.replied_at` (TIMESTAMP) [VERIFIED IMPLEMENTED].
  - Endpoint: `POST /owner/reviews/{id}/respond` handled by `Owner\ReviewController::respond()`.
  - Persistence: Calls `ReviewRepository::addReply($reviewId, $restaurantId, $reply)`.
  - UI Experience: Quick-reply suggestion chips populate response textarea; submission is asynchronous with instant "Restaurant Response" badge and timestamp rendering.
- **Rating Filter:** Star rating distribution bars (5★, 4★, 3★, 2★, 1★) act as clickable filters (`filterByRating(star)`).

---

# 26. Staff System

- **Database Architecture:** Staff members are stored directly in the `users` table scoped with `users.restaurant_id` and assigned a specific `role_id` (`3` for Manager, `4` for Staff) [VERIFIED IMPLEMENTED].
- **No Separate Table:** There is no separate `restaurant_staff` or `staff` table in the database schema.
- **Management Portal:** Managed via `/owner/staff` and `App\Repositories\StaffRepository`.
- **Capabilities:**
  - Owner can register new staff with name, email, role, and temporary password.
  - Owner can toggle staff active/inactive status via `POST /owner/staff/{id}/toggle`.
  - Active staff can sign in to the Owner portal to view Live Orders, operate the Kitchen Kanban, and view tables.

---

# 27. Table & QR System

- **Database Tables:** `restaurant_tables` and `qr_tokens` [VERIFIED IMPLEMENTED].
- **Seeded Tables:** Tables 1 through 12 naturally ordered in database using numerical extraction (`REGEXP_SUBSTR`).
- **Interactive Occupancy Toggle:** In `resources/views/owner/tables.php`, clicking an occupancy pill (`Available` ↔ `Occupied`) executes an AJAX call to `POST /owner/tables/{id}/status`, updating `restaurant_tables.status` in real time.
- **QR Code Generation:** Each card contains an inline HTML5 `<canvas>` generated by `qrcode.min.js` encoding the direct ordering URL (`http://<host>/menu?token=<token>`).
- **Printing & Export:** Includes high-resolution PNG download and thermal print ticket popups.

---

# 28. Sales & Analytics

Implemented in `App\Repositories\OrderRepository` and displayed in `resources/views/owner/analytics.php` [VERIFIED IMPLEMENTED]:

| Metric Name | Calculation Formula / SQL Logic | Source Tables & Columns | Implementation File |
| :--- | :--- | :--- | :--- |
| **Total Revenue** | `COALESCE(SUM(total_amount), 0)` where `payment_status = 'completed'` | `orders.total_amount`, `orders.payment_status` | `OrderRepository.php` |
| **Total Orders** | `COUNT(*)` | `orders.id` | `OrderRepository.php` |
| **Average Order Value** | `total_revenue / total_orders` | `orders.total_amount` | `OrderRepository.php` |
| **Daily Sales Trend** | `SUM(total_amount)` grouped by `DATE(created_at)` for last 7 days | `orders.total_amount`, `orders.created_at` | `OrderRepository.php` |
| **Weekly Sales Trend** | `SUM(total_amount)` grouped by `WEEK(created_at)` for last 8 weeks | `orders.total_amount`, `orders.created_at` | `OrderRepository.php` |
| **Monthly Sales Trend**| `SUM(total_amount)` grouped by `MONTH(created_at)` for last 6 months| `orders.total_amount`, `orders.created_at` | `OrderRepository.php` |
| **Category Breakdown** | `SUM(oi.total_price)` grouped by `c.name` | `order_items`, `food_items`, `categories` | `OrderRepository.php` |
| **Payment Breakdown**  | `COUNT(*)` & `SUM(amount)` grouped by `payment_method` | `payments.payment_method`, `payments.amount` | `OrderRepository.php` |
| **Nutrient Macro Totals**| `SUM(calories * quantity)`, `SUM(protein * quantity)`, etc. | `order_items.calories`, `order_items.protein` | `OrderRepository.php` |

---

# 29. Payment System

- **Database Table:** `payments` [VERIFIED IMPLEMENTED]
- **Relationship:** Unique 1-to-1 foreign key with `orders.id` (`fk_payments_order`).
- **Supported Payment Methods:** Strictly restricted by ENUM column `payments.payment_method` and validation in `PaymentService::simulatePayment()`:
  1. `cash`
  2. `upi`
  3. `card`
  - *Note:* Other payment terms like "netbanking" or "crypto" are NOT supported.
- **Transaction Flow:** `Api.simulatePayment(orderId, method)` generates reference `TXN-[METHOD]-[HEX]`, records payment as `completed`, sets `orders.payment_status = 'completed'`, and advances `orders.order_status = 'accepted'`.

---

# 30. Restaurant Owner Portal

Complete inventory of all 11 Owner Portal screens [VERIFIED IMPLEMENTED]:

1. **Overview (`/owner/dashboard`):** Monitoring-only dashboard. 6 KPI stat cards (Today's Sales, Today's Orders, Active Orders, Completed Orders, Total Customers, AOV), 2 SVG chart cards (Sales Trend Bezier Curve, Order Completion Donut Ring), Popular Food Items list, and Recent Orders table. Strictly zero action controls.
2. **Live Orders (`/owner/orders`):** Order monitoring view. Filter bar with status legend pills displaying real-time counts (`Placed`, `Accepted`, `Preparing`, `Ready`, `Completed`, `Cancelled`). Clicking pills filters table rows. Clicking an order row opens an asynchronous **Order Details Modal** displaying complete item breakdown with unit prices and nutrition macros.
3. **Kitchen Live Orders (`/owner/kitchen-orders`):** Dedicated operational Kanban board (5 columns: `New Orders`, `Accepted`, `Preparing`, `Ready`, `Completed`). Cards display itemizations, customization badges, and notes. Operational buttons advance orders between columns via AJAX.
4. **Menu & Foods (`/owner/menu`):** Dish catalog table displaying dish photos, category, price, calories, protein, and availability status. Includes "+ Add Food" modal, dynamic "Edit Food" modal pre-populated with dish data, availability toggle, and delete action.
5. **Tables & QR (`/owner/tables`):** 4-column card grid displaying Tables 1 through 12 in natural numerical sequence. Features canvas-rendered scannable QR codes, instant PNG download, print ticket popups, and click-to-toggle occupancy pills (`Available` ↔ `Occupied`).
6. **Customer Reviews (`/owner/reviews`):** Reviews management screen. Features average rating summary, 5-star distribution bars with interactive click filtering, customer feedback cards, quick-reply suggestion chips, and persistent AJAX reply publishing.
7. **Staff Management (`/owner/staff`):** Team access table displaying staff names, emails, roles (Manager, Staff, Waiter), and active/inactive status toggle. Includes "+ Add Staff" modal.
8. **Sales & Analytics (`/owner/analytics`):** Financial and dietary analytics dashboard. Features 4 KPI cards, Daily/Weekly/Monthly toggleable revenue bar chart, Macro Nutrient Distribution card (Protein, Carbs, Fat percentages), Payment Method Breakdown (UPI vs Card vs Cash), Category Sales Distribution, and Top Performing Dishes table.
9. **Live Menu Preview (`/owner/live-menu`):** Realistic simulation of customer-facing digital menu. Features brand hero banner, dynamic category filter pills, dish grid with high-resolution gourmet Unsplash photography, and interactive "Add to Cart" with live counter badge and toast drawer.
10. **Profile (`/owner/profile`):** Restaurant branding form (trade name, telephone, email, city, state, street address).
11. **Settings (`/owner/settings`):** Operational preferences form (auto order acceptance, table ordering enabled, tax rate, service charge).

---

# 31. Platform Super Admin Portal

Complete inventory of Platform Super Admin screens [VERIFIED IMPLEMENTED]:

1. **Platform Overview (`/admin/dashboard`):** Cross-tenant governance dashboard. 6 platform KPI stat cards (Total Restaurants, Active Subscriptions, Monthly Platform Volume, Active Users, System Health, Total Platform Orders), 4 SVG chart cards in 2x2 grid (Platform Revenue, Tenant Growth, Regional Distribution, Tier Distribution), and Recent Registrations table displaying **only** `Approval Status` (generic `Status` column is eliminated).
2. **Registered Restaurants (`/admin/restaurants`):** Multi-tenant directory table with search, status dropdown, and bottom interactive status filter cards (`Approved`, `Pending`, `Suspended`). Features inline status action dropdowns and "Inspect Tenant" navigation links.
3. **Restaurant Portal Inspection (`/admin/portal-inspect` & `/admin/restaurants/{id}/inspect`):** Deep tenant audit screen. Hero tenant summary banner, 5 core KPI cards, active branch list, menu dishes summary, tables overview, and monthly revenue performance chart.
4. **Users & Access (`/admin/users`):** Platform user authority table displaying user names, emails, assigned roles, assigned restaurant scope, creation dates, and active/suspended status toggles. Includes "+ Add User" modal.
5. **Platform Orders (`/admin/orders`) Status:** **COMPLETELY REMOVED / ABSENT** [VERIFIED]. All routes, controllers, and sidebar navigation links to `/admin/orders` have been eliminated per project specifications.

---

# 32. Servant Feature Verification

- **Feature Name:** "Call Servant" / "Call Server" / "Server Assistance" / "Waiter Request"
- **Audit Findings:**
  - Codebase Search for `servant`: 0 active code occurrences. Found only 4 historical log entries in `storage/logs/app.log` dating to 2026-09-15.
  - Codebase Search for `call servant`, `call server`, `server assistance`, `table assistance`: 0 occurrences across all files.
  - Codebase Search for `waiter`: Occurs only as an informative dropdown role title in `resources/views/owner/staff.php`.
- **Classification:** **REMOVED** [VERIFIED]. The feature has been completely eradicated and is not active in any form.

---

# 33. Legacy / Duplicate Features

1. **`D:\Healty Bite` (Sibling Directory) [LEGACY / DUPLICATE]:**
   - Independent legacy project directory containing static prototypes (`admin.html`, `dashboard.html`, `index.html`, `login.html`, `menu.html`, `register.html`), Netlify configurations, and older project code.
   - None of the active application code in `D:\NEW healthy bite` references or depends on `D:\Healty Bite`.
2. **`prototype/customer-ui/` (Within Workspace) [LEGACY / DUPLICATE]:**
   - Contains static standalone HTML prototype files (`cart.html`, `checkout.html`, `confirmation.html`, `food-details.html`, `index.html`, `menu.html`, `tracking.html`).
   - Serves purely as historical reference; active views run from `resources/views/customer/*.php`.
3. **`admin` Table in MySQL Database [LEGACY / DUPLICATE]:**
   - Table exists in live MySQL database with 4 rows (`id`, `name`, `created_at`, `updated_at`), but is omitted from `schema.sql` and is never queried by any controller or repository.

---

# 34. Use Case Inventory

### Category: Customer (Actor: Dining Customer)
- **UC-C01: Scan Table QR Code:** Customer scans QR token to automatically lock restaurant, branch, and table context.
- **UC-C02: Browse Digital Menu:** Customer views dishes categorized with real photos, prices, diet tags, and nutrition.
- **UC-C03: Customize Dish Options:** Customer selects portion variant and add-on toppings with real-time price/macro updates.
- **UC-C04: Manage Cart:** Customer adds items to cart, updates quantities, and reviews line totals.
- **UC-C05: Place Order & Pay:** Customer submits order, selects dining mode, simulates payment, and receives unique order number.
- **UC-C06: Track Live Kitchen Progress:** Customer monitors 5-stage order progress track in real-time.
- **UC-C07: Submit Dining Review:** Customer submits 1–5 star rating and feedback comment for an order.

### Category: Restaurant Owner / Staff (Actor: Restaurant Owner / Kitchen Staff)
- **UC-O01: Authenticate Owner Portal:** Sign in using email and master password.
- **UC-O02: Monitor Real-time Overview:** Inspect today's revenue, order counts, and popular dishes.
- **UC-O03: Inspect Live Orders:** Search and filter active orders by status pills and inspect item details in modal.
- **UC-O04: Manage Kitchen Kanban:** Advance orders through 5 operational stages (`placed` → `accepted` → `preparing` → `ready` → `completed`).
- **UC-O05: Manage Menu Catalog:** Create new dishes, edit pricing and macros, toggle live availability, and delete dishes.
- **UC-O06: Configure Tables & QR Codes:** View Tables 1–12, download scannable QR tokens, and toggle table occupancy.
- **UC-O07: Respond to Reviews:** View rating distribution and publish official restaurant replies to customer feedback.
- **UC-O08: Manage Staff Access:** Add team members with specific roles and toggle activation.
- **UC-O09: Inspect Sales & Macro Analytics:** Review daily/weekly/monthly revenue trends, payment breakdowns, and nutrition totals.

### Category: Platform Super Admin (Actor: Super Admin)
- **UC-A01: Authenticate Admin Portal:** Sign in with platform administrator credentials.
- **UC-A02: Monitor Platform Overview:** Audit cross-tenant registrations, system health, and aggregate volume.
- **UC-A03: Govern Registered Restaurants:** Review multi-tenant directory, inspect tenant details, and update status (Approved, Pending, Suspended).
- **UC-A04: Deep Tenant Inspection:** Audit specific restaurant branches, tables, menu summary, and monthly performance.
- **UC-A05: Manage Platform Users:** Create users and manage platform role activations.

---

# 35. Activity Diagram Candidates

1. **Candidate AD-01: Table-Side Contactless Order & Payment Flow**
   - Actor: Customer
   - Start: Scanning QR code.
   - Steps: Resolve token → Display menu → Choose dish → Customize toppings → Recalculate price/macros → Add to cart → Checkout → Verify cart on backend → Generate order → Clear payment → Confirm order.
   - End: Customer arrives at confirmation/tracking screen.
2. **Candidate AD-02: Kitchen Order Lifecycle & Kanban Progression Flow**
   - Actor: Kitchen Staff / Restaurant Owner
   - Start: New order arrives with status `placed`.
   - Steps: Staff reviews ticket on Kanban board → Clicks "Accept Order" (`accepted`) → Clicks "Start Preparing" (`preparing`) → Clicks "Mark Ready" (`ready`) → Clicks "Complete Order" (`completed`).
   - Decision Points: Can order be cancelled? If yes, status transitions to `cancelled`.
   - End: Order reaches `completed` column.
3. **Candidate AD-03: Food Item Creation & Customization Configuration**
   - Actor: Restaurant Owner
   - Start: Owner opens `/owner/menu` and clicks "+ Add Food".
   - Steps: Enter name, description, category, base price, image URL, diet type, allergens, 8-macro nutrient values → Submit form → Validate CSRF and required fields → Insert record into `food_items` → Return to menu with flash success.
   - End: Dish is immediately visible on live customer menu.
4. **Candidate AD-04: Restaurant Onboarding & Super Admin Approval Lifecycle**
   - Actor: Super Admin
   - Start: Tenant registration enters system with status `pending`.
   - Steps: Admin audits restaurant in `/admin/restaurants` → Opens `/admin/restaurants/{id}/inspect` → Reviews branches and menu → Selects `Approved` or `Suspended` in status dropdown → Updates database record.
   - End: Approved restaurant menu goes live; suspended restaurant access is revoked.

---

# 36. Process Flow Candidates

1. **Process PF-01: Cart Validation & Total Calculation Pipeline**
   - Input: Client JSON cart payload (`food_id`, `variant_id`, `customizations`, `quantity`).
   - Major Steps: Verify food exists & available → Verify variant belongs to food → Verify customizations belong to food and within max limits → Compute UnitPrice via `PricingService` → Compute 8-macro snapshot via `NutritionService` → Scale for quantity → Compute subtotal, tax, and total amount.
   - Output: Validated cart array with guaranteed server-side pricing and nutrition.
2. **Process PF-02: QR Token Cryptographic Resolution Pipeline**
   - Input: URL token string (`token`).
   - Major Steps: Query `qr_tokens` table for active unexpired token → Join `restaurant_tables` → Join `branches` → Join `restaurants` → Verify tenant status `approved` and branch `active`.
   - Output: Resolved array containing `restaurant_id`, `branch_id`, `table_id`, `table_number`, or NULL if invalid.
3. **Process PF-03: Customer Review & Response Flow**
   - Input: Customer rating/comment; Owner reply text.
   - Major Steps: Customer posts review linked to order → Owner views review on `/owner/reviews` → Selects quick reply chip or writes reply → Submits AJAX request → Database updates `restaurant_reply` and `replied_at` → Real-time response badge renders on review card.
   - Output: Persisted two-way customer-restaurant review record.

---

# 37. DFD External Entities

1. **Dining Customer:** End user seated at dining table or placing takeaway orders via mobile web browser.
2. **Restaurant Owner:** Business operator managing restaurant catalog, pricing, staff, reviews, and analytics.
3. **Kitchen Staff / Floor Manager:** Operational employee preparing dishes and updating Kanban ticket statuses.
4. **Platform Super Admin:** Platform owner governing restaurant tenants, subscriptions, and system access.
5. **Simulated Payment Gateway:** Transaction clearing subsystem verifying UPI, Card, and Cash settlements.

---

# 38. DFD Processes

- **P1.0: Authentication & Session Gatekeeping:** Authenticates Admin, Owner, and Staff credentials, validates CSRF tokens, and manages sessions.
- **P2.0: QR Token & Context Resolution:** Decodes QR tokens, verifies validity, and locks table, branch, and restaurant parameters.
- **P3.0: Food Catalog & Option Management:** Manages categories, dishes, portion variants, customizations, and live availability.
- **P4.0: Cart Recalculation & Nutrition Processing:** Re-evaluates client selections on backend, calculating exact prices, taxes, and 8-macro nutrient snapshots.
- **P5.0: Order Lifecycle & Ticket Processing:** Creates customers, generates order numbers, freezes item snapshots, and manages kitchen status transitions.
- **P6.0: Kitchen Kanban & Live Tracking Dispatch:** Coordinates operational kitchen order progression and customer tracking polling.
- **P7.0: Payment Settlement Simulation:** Clears financial transactions across cash, upi, and card, advancing payment and order states.
- **P8.0: Customer Reviews & Owner Responses:** Records dining feedback, computes rating distributions, and stores owner response replies.
- **P9.0: Tenant Audit & Platform Governance:** Aggregates platform KPIs, inspects tenant branches, and moderates restaurant approvals.

---

# 39. DFD Data Stores

- **D1: `restaurants` & `branches`:** Multi-tenant business profiles, branding, and location directory.
- **D2: `roles` & `users`:** User accounts, hashed credentials, and role authorizations.
- **D3: `categories`, `food_items`, `food_variants`, `food_customizations`:** Menu catalog, pricing, diet types, and macro nutrients.
- **D4: `restaurant_tables` & `qr_tokens`:** Physical dining tables, occupancy states, and cryptographic QR tokens.
- **D5: `customers`:** Guest customer contact records.
- **D6: `orders`, `order_items`, `order_item_customizations`:** Master order records, item snapshots, and customization snapshots.
- **D7: `payments`:** Transaction references, payment methods, and clearance timestamps.
- **D8: `reviews`:** Numerical ratings, dining comments, and restaurant response replies.

---

# 40. DFD Data Flows

- **DF01:** `Dining Customer` → (Scans QR Code Token) → `P2.0 QR Resolution`
- **DF02:** `P2.0 QR Resolution` → (Validates token against D4 `qr_tokens` & `restaurant_tables`) → `P2.0`
- **DF03:** `P2.0 QR Resolution` → (Bound Restaurant, Branch, Table Context) → `P3.0 Catalog Management`
- **DF04:** `P3.0 Catalog Management` → (Reads dishes & options from D3) → `Dining Customer`
- **DF05:** `Dining Customer` → (Submits Cart Choices) → `P4.0 Cart & Nutrition Processing`
- **DF06:** `P4.0 Cart & Nutrition Processing` → (Validates against D3, computes line totals & macros) → `P5.0 Order Processing`
- **DF07:** `Dining Customer` → (Guest Contact & Payment Choice) → `P5.0 Order Processing`
- **DF08:** `P5.0 Order Processing` → (Writes Customer to D5, Master Order & Snapshots to D6) → `P7.0 Payment Settlement`
- **DF09:** `P7.0 Payment Settlement` → (Records Transaction in D7, advances Order status in D6) → `P6.0 Kitchen Dispatch`
- **DF10:** `P6.0 Kitchen Dispatch` → (Kitchen Ticket Queue) → `Kitchen Staff`
- **DF11:** `Kitchen Staff` → (Advances Status: Placed → Accepted → Preparing → Ready → Completed) → `P6.0 Kitchen Dispatch`
- **DF12:** `P6.0 Kitchen Dispatch` → (Live Status Updates) → `Dining Customer` (via Live Tracking Poller)
- **DF13:** `Restaurant Owner` → (Menu CRUD, Table Management, Staff Access) → `P1.0 & P3.0` → (Updates D1, D2, D3, D4)
- **DF14:** `Restaurant Owner` → (Replies to Feedback) → `P8.0 Review System` → (Updates D8 `reviews`)
- **DF15:** `Super Admin` → (Tenancy Approvals & User Activations) → `P9.0 Platform Governance` → (Updates D1 `restaurants`, D2 `users`)

---

# 41. Actual PHP Class Inventory

Total Actual PHP Classes: **42 Classes** [VERIFIED IMPLEMENTED]

### Core Framework Classes (`App\Core\*` — 11 Classes)
1. `App\Core\App` (`app/Core/App.php`)
2. `App\Core\Auth` (`app/Core/Auth.php`)
3. `App\Core\Controller` (`app/Core/Controller.php`)
4. `App\Core\Csrf` (`app/Core/Csrf.php`)
5. `App\Core\Database` (`app/Core/Database.php`)
6. `App\Core\Env` (`app/Core/Env.php`)
7. `App\Core\Request` (`app/Core/Request.php`)
8. `App\Core\Response` (`app/Core/Response.php`)
9. `App\Core\Router` (`app/Core/Router.php`)
10. `App\Core\Session` (`app/Core/Session.php`)
11. `App\Core\Validator` (`app/Core/Validator.php`)

### Middleware Classes (`App\Middleware\*` — 3 Classes)
12. `App\Middleware\AdminMiddleware` (`app/Middleware/AdminMiddleware.php`)
13. `App\Middleware\AuthMiddleware` (`app/Middleware/AuthMiddleware.php`)
14. `App\Middleware\RestaurantMiddleware` (`app/Middleware/RestaurantMiddleware.php`)

### Controller Classes (`App\Controllers\*` — 12 Classes)
15. `App\Controllers\HomeController` (`app/Controllers/HomeController.php`)
16. `App\Controllers\MenuController` (`app/Controllers/MenuController.php`)
17. `App\Controllers\CheckoutController` (`app/Controllers/CheckoutController.php`)
18. `App\Controllers\OrderController` (`app/Controllers/OrderController.php`)
19. `App\Controllers\CartController` (`app/Controllers/CartController.php`)
20. `App\Controllers\FoodController` (`app/Controllers/FoodController.php`)
21. `App\Controllers\PaymentController` (`app/Controllers/PaymentController.php`)
22. `App\Controllers\RestaurantController` (`app/Controllers/RestaurantController.php`)
23. `App\Controllers\Admin\AuthController` (`app/Controllers/Admin/AuthController.php`)
24. `App\Controllers\Admin\DashboardController` (`app/Controllers/Admin/DashboardController.php`)
25. `App\Controllers\Admin\RestaurantController` (`app/Controllers/Admin/RestaurantController.php`)
26. `App\Controllers\Admin\UserController` (`app/Controllers/Admin/UserController.php`)

### Owner Controller Classes (`App\Controllers\Owner\*` — 10 Classes)
27. `App\Controllers\Owner\AnalyticsController` (`app/Controllers/Owner/AnalyticsController.php`)
28. `App\Controllers\Owner\AuthController` (`app/Controllers/Owner/AuthController.php`)
29. `App\Controllers\Owner\DashboardController` (`app/Controllers/Owner/DashboardController.php`)
30. `App\Controllers\Owner\MenuController` (`app/Controllers/Owner/MenuController.php`)
31. `App\Controllers\Owner\OrderController` (`app/Controllers/Owner/OrderController.php`)
32. `App\Controllers\Owner\ProfileController` (`app/Controllers/Owner/ProfileController.php`)
33. `App\Controllers\Owner\ReviewController` (`app/Controllers/Owner/ReviewController.php`)
34. `App\Controllers\Owner\SettingsController` (`app/Controllers/Owner/SettingsController.php`)
35. `App\Controllers\Owner\StaffController` (`app/Controllers/Owner/StaffController.php`)
36. `App\Controllers\Owner\TableController` (`app/Controllers/Owner/TableController.php`)

### Domain Service Classes (`App\Services\*` — 7 Classes)
37. `App\Services\CartService` (`app/Services/CartService.php`)
38. `App\Services\MenuService` (`app/Services/MenuService.php`)
39. `App\Services\NutritionService` (`app/Services/NutritionService.php`)
40. `App\Services\OrderService` (`app/Services/OrderService.php`)
41. `App\Services\PaymentService` (`app/Services/PaymentService.php`)
42. `App\Services\PricingService` (`app/Services/PricingService.php`)
43. `App\Services\QrService` (`app/Services/QrService.php`)

### Repository Classes (`App\Repositories\*` — 9 Classes)
44. `App\Repositories\BranchRepository` (`app/Repositories/BranchRepository.php`)
45. `App\Repositories\CategoryRepository` (`app/Repositories/CategoryRepository.php`)
46. `App\Repositories\FoodRepository` (`app/Repositories/FoodRepository.php`)
47. `App\Repositories\OrderRepository` (`app/Repositories/OrderRepository.php`)
48. `App\Repositories\PaymentRepository` (`app/Repositories/PaymentRepository.php`)
49. `App\Repositories\RestaurantRepository` (`app/Repositories/RestaurantRepository.php`)
50. `App\Repositories\ReviewRepository` (`app/Repositories/ReviewRepository.php`)
51. `App\Repositories\StaffRepository` (`app/Repositories/StaffRepository.php`)
52. `App\Repositories\UserRepository` (`app/Repositories/UserRepository.php`)

---

# 42. Feature Matrix

| Feature | Customer | Restaurant Owner | Kitchen Employee | Super Admin | Database Support | Backend Support | UI Support | Verification Status |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- |
| **Contactless QR Scan & Resolution** | ✓ | ✓ | - | - | ✓ (`qr_tokens`) | ✓ (`QrService`) | ✓ | **VERIFIED IMPLEMENTED** |
| **Food Catalog Browsing** | ✓ | ✓ | - | ✓ | ✓ (`food_items`) | ✓ (`MenuService`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Dish Customization & Portions** | ✓ | ✓ | - | - | ✓ (`variants`, `custom`)| ✓ (`CartService`) | ✓ | **VERIFIED IMPLEMENTED** |
| **8-Macro Nutrient Calculation** | ✓ | ✓ | - | - | ✓ (8 cols in food/order)| ✓ (`NutritionService`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Server-Verified Pricing & GST** | ✓ | ✓ | - | - | ✓ (`orders`, `order_items`)| ✓ (`PricingService`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Interactive Cart & Drawer** | ✓ | - | - | - | - (Client LocalStorage)| ✓ (`CartController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Order Placement & Snapshotting** | ✓ | ✓ | - | - | ✓ (`orders`, `order_items`)| ✓ (`OrderService`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Simulated Payment Settlement** | ✓ | - | - | - | ✓ (`payments`) | ✓ (`PaymentService`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Kitchen Live Orders Kanban** | - | ✓ | ✓ | - | ✓ (`orders.order_status`)| ✓ (`OrderController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Customer Live Kitchen Tracker** | ✓ | - | - | - | ✓ (`orders.order_status`)| ✓ (`OrderController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Menu & Dish CRUD Management** | - | ✓ | - | - | ✓ (`food_items`) | ✓ (`MenuController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Table Management & QR Generator**| - | ✓ | - | - | ✓ (`restaurant_tables`)| ✓ (`TableController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Customer Reviews & Replies** | ✓ | ✓ | - | - | ✓ (`reviews`) | ✓ (`ReviewController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Staff & Team Management** | - | ✓ | - | - | ✓ (`users`, `roles`) | ✓ (`StaffController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Sales & Nutrient Macro Analytics**| - | ✓ | - | - | ✓ (`orders`, `payments`)| ✓ (`AnalyticsController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Restaurant Tenant Onboarding** | - | - | - | ✓ | ✓ (`restaurants`) | ✓ (`RestaurantController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Deep Tenant Inspection Portal** | - | - | - | ✓ | ✓ (`restaurants`, `branches`)| ✓ (`RestaurantController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Platform Users & Role Governance**| - | - | - | ✓ | ✓ (`users`, `roles`) | ✓ (`UserController`)| ✓ | **VERIFIED IMPLEMENTED** |
| **Platform Orders (/admin/orders)** | - | - | - | - | - | - | - | **REMOVED PER SPEC** |
| **Call Servant / Call Server** | - | - | - | - | - | - | - | **REMOVED PER SPEC** |

---

# 43. Requirements vs Implementation

| Required Project Feature | Implemented Architecture | Current State | Verification Evidence |
| :--- | :--- | :--- | :--- |
| **Customer QR Menu** | Dynamic table QR resolution via `token` query param | Fully working | Scans load `/menu?token=...`, resolving table context. |
| **Restaurant-Specific Menu** | All food items, categories, and branches strictly scoped to `restaurant_id` | Fully working | Queries enforce `WHERE restaurant_id = :id`. |
| **Food Customization** | Multi-group add-ons with min/max quantities and macro/price deltas | Fully working | Modal renders options; prices and macros update live. |
| **Nutrition Calculation** | 8 macros calculated and scaled per quantity | Fully working | `NutritionService.php` calculates calories, protein, carbs, fat, etc. |
| **Pricing Calculation** | Base + variant + customization sum + 5% GST tax | Fully working | `PricingService.php` verifies totals on client and server. |
| **Cart System** | LocalStorage state store + server validation endpoint | Fully working | Drawer renders items, updates badges, clears on order. |
| **Order Placement** | Master record creation + item snapshots + unique order number | Fully working | `OrderService::placeOrder()` generates `HB-...` and inserts rows. |
| **Kitchen Live Orders** | 5-column operational Kanban board with AJAX status advancement | Fully working | `resources/views/owner/kitchen_orders.php` advances cards. |
| **Live Kitchen Tracker** | 5-step visual customer timeline polled from `/api/orders/table-latest` | Fully working | `public/assets/js/live-kitchen.js` dynamically updates steps. |
| **Reviews & Responses** | 1–5 star ratings with persisted restaurant owner response replies | Fully working | Added `restaurant_reply` column; verified persistence in DB. |
| **Staff Management** | Staff stored in `users` with roles; status toggle | Fully working | `Owner\StaffController` manages active team members. |
| **Tables & QR Generator** | Tables 1–12, canvas QR codes, click-to-toggle occupancy | Fully working | Sequential 1–12 display, instant PNG download and print. |
| **Sales & Macro Analytics** | Financial reports, daily/weekly trends, payment & macro breakdowns | Fully working | `Owner\AnalyticsController` renders metrics and charts. |
| **Admin Restaurant Mgmt** | Multi-tenant governance with interactive status filter cards | Fully working | `Admin\RestaurantController` manages Approved/Pending/Suspended. |
| **Platform Orders Removal** | Eliminated `/admin/orders` completely | Fully removed | No route, controller, or sidebar link exists. |
| **Call Servant Removal** | Eliminated all servant/assistance requests | Fully removed | Code search returns 0 active references. |

---

# 44. Conflicts / Inconsistencies

1. **Conflict: Live Database vs `database/schema/schema.sql` regarding `admin` table [CONFLICT / INCONSISTENCY]:**
   - *Source A:* Live MySQL database contains a table named `admin` (4 columns, 4 rows).
   - *Source B:* `database/schema/schema.sql` does not define `admin`.
   - *Current Implementation:* The application authenticates administrators via `users` where `role_id = 1`. The `admin` table is a legacy relic.
   - *Recommended Source of Truth:* `schema.sql` and `users` table. The `admin` table is unused.
2. **Conflict: `schema.sql` vs Live Database regarding `reviews` table columns [CONFLICT / INCONSISTENCY]:**
   - *Source A:* `database/schema/schema.sql` did not originally define `restaurant_reply` and `replied_at`.
   - *Source B:* Live database and `database/migrations/add_review_replies.php` have both columns actively populated.
   - *Current Implementation:* Review reply feature is fully active and verified in code.
   - *Recommended Source of Truth:* Live database and migration script `add_review_replies.php`.
3. **Conflict: Hardcoded Fallback Items in Views vs Dynamic Database Queries [CONFLICT / INCONSISTENCY]:**
   - *Source A:* Early versions of `resources/views/owner/kitchen_orders.php` had hardcoded HTML lines for "Chicken Rice Bowl".
   - *Source B:* Current updated version renders real dynamic items from `$order['items']` with a clean empty state.
   - *Recommended Source of Truth:* Current updated view code.

---

# 45. Missing Features

No core functional gaps exist for the specified scope. All requirements (customer ordering, nutrition calculation, kitchen Kanban, table QR codes, review replies, analytics, and admin tenant management) are implemented and operational.

---

# 46. Partial Features

1. **External Payment Gateway Integration [PARTIALLY IMPLEMENTED]:**
   - Payment settlement is fully functional via simulation (`PaymentService::simulatePayment()`) supporting `cash`, `upi`, and `card`.
   - Live external banking SDKs (e.g. Razorpay, Stripe, or Paytm Webhook gateways) are not connected; payment clearing is simulated instantaneously.
2. **Real-Time WebSockets [PARTIALLY IMPLEMENTED]:**
   - Real-time tracking is implemented via HTTP Polling (`/api/orders/table-latest`) rather than persistent WebSocket connections (e.g. Socket.io or Ratchet). This is completely functional for table-side dining.

---

# 47. Legacy Features

1. **`admin` Table [LEGACY / DUPLICATE]:** Present in MySQL database, but unused.
2. **`prototype/customer-ui/` [LEGACY / DUPLICATE]:** Standalone static HTML files in prototype folder.
3. **`D:\Healty Bite` [LEGACY / DUPLICATE]:** Standalone sibling directory with older HTML prototypes.

---

# 48. Security-Relevant Findings

1. **CSRF Protection:** Implemented in `App\Core\Csrf` via secure tokens stored in `$_SESSION['_csrf_token']` and validated on state-modifying POST requests [VERIFIED IMPLEMENTED].
2. **SQL Injection Prevention:** Enforced across all repositories using PDO prepared statements with parameter binding and `PDO::ATTR_EMULATE_PREPARES => false` [VERIFIED IMPLEMENTED].
3. **QR Token Parameter Tampering Defense:** Server-side override in `OrderService::placeOrder()` discards client-supplied `table_id` or `restaurant_id` if a valid QR token is present, locking order creation to the cryptographic token context [VERIFIED IMPLEMENTED].
4. **Input Sanitization:** HTML escaping helper `e()` (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`) applied consistently across all view renderings to prevent Cross-Site Scripting (XSS) [VERIFIED IMPLEMENTED].
5. **Password Storage:** Strong one-way BCRYPT hashing via `password_hash($password, PASSWORD_BCRYPT)` [VERIFIED IMPLEMENTED].

---

# 49. Documentation Recommendations

1. **Schema File Synchronization:** When documenting the schema, ensure the Data Dictionary and ER Diagram reflect `reviews.restaurant_reply` and `reviews.replied_at` from the active migration, and exclude the legacy `admin` table.
2. **Entity Terminology Consistency:** Use `restaurant_tables` for physical tables and `orders` for dining orders to avoid confusion between dining tables and database tables.
3. **Single Source for Diagrams:** When generating ER diagrams, use the exact relationship mappings documented in Section 9 of this report, which are backed by verified foreign keys.

---

# 50. Final Verified System Summary

The Healthy Bite digital restaurant menu and food ordering platform has been thoroughly inspected, tested, and verified across all architectural layers:

1. **Database Tier:** 17 physical tables extracted and analyzed. 16 core relational tables form the active database schema with 24 active foreign keys and zero broken constraints. 1 legacy table (`admin`) is documented as unused.
2. **Backend MVC Tier:** 42 active PHP classes verified across Core, Middleware, Controllers, Services, and Repositories. Routing operates through clean front controller architecture without framework bloat.
3. **Customer Dining Tier:** Complete journey verified from QR token resolution, categorized menu browsing, dish customization, dynamic 8-macro nutrient scaling, server-side cart revalidation, order placement with immutable snapshots, simulated payment clearance, and live 5-step kitchen polling.
4. **Restaurant Owner Tier:** All 11 operational views verified including Overview monitoring, Live Orders with itemized receipt modals, Kitchen Kanban with live column advancement, Menu dish CRUD with nutrition/price editing, Tables 1–12 with canvas QR code generation and occupancy toggles, Customer Reviews with persistent reply publishing, Staff access control, and Sales & Macro Analytics.
5. **Platform Super Admin Tier:** Verified governance over registered restaurant tenants (`Approved`, `Pending`, `Suspended`), deep tenant inspection portal, and platform user role management. Verified that `/admin/orders` is completely absent.
6. **Eliminated Relics:** Verified that all "Call Servant" / "Call Server" features have been completely removed.

---

# FINAL SUMMARY

### A. VERIFIED WORKING FEATURES
- Contactless Table QR Code Scanning & Cryptographic Token Resolution (`QrService`, `qr_tokens`).
- Interactive Customer Digital Menu with High-Resolution Food Photography & Dietary Badges (`MenuController`, `food_items`).
- Real-Time Food Customization Modal with Portion Variants & Ingredient Add-ons (`FoodController`, `food-details.js`).
- Dynamic 8-Macro Nutrient Calculation & Scaling (`calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `sodium`, `caffeine`) (`NutritionService`).
- Server-Verified Pricing Engine with Line Totals & 5% GST Calculation (`PricingService`).
- Interactive Customer Cart Drawer with LocalStorage Persistence (`CartService`, `cart.js`).
- Complete Order Pipeline with Immutable Nutrition/Price Snapshots (`OrderService`, `orders`, `order_items`).
- Simulated Payment Settlement supporting Cash, UPI, and Card (`PaymentService`, `payments`).
- 5-Stage Operational Kitchen Live Orders Kanban Board with AJAX State Advancement (`OrderController`, `kitchen_orders.php`).
- Customer Live Kitchen Tracking Progress Bar with Real-Time Polling (`live-kitchen.js`, `tracking.php`).
- Restaurant Owner Menu & Dish Management with Nutrition/Price Editing and Availability Toggles (`Owner\MenuController`, `menu.php`).
- Tables 1–12 Management with Canvas QR Code Generator, PNG Download, and Occupancy Toggles (`Owner\TableController`, `tables.php`).
- Customer Reviews Module with Star Rating Filters and Persistent Restaurant Owner Reply Publishing (`ReviewRepository`, `reviews.php`).
- Restaurant Staff & Team Access Management (`StaffRepository`, `staff.php`).
- Comprehensive Sales & Macro Nutrient Analytics Dashboard (`Owner\AnalyticsController`, `analytics.php`).
- Platform Super Admin Tenant Governance with Interactive Status Filter Cards (`Admin\RestaurantController`, `restaurants.php`).
- Deep Restaurant Portal Inspection Audit View (`Admin\RestaurantController`, `portal_inspect.php`).
- Platform Users & Role Authority Management (`Admin\UserController`, `users.php`).

### B. PARTIALLY WORKING FEATURES
- External Payment Integration: Operates via instant realistic simulation rather than third-party banking gateway webhooks.
- Real-Time Dispatch: Operates via rapid HTTP polling rather than WebSocket daemons.

### C. MISSING FEATURES
- None within the defined project requirements and Figma specifications.

### D. LEGACY FEATURES
- `admin` table in MySQL database (4 rows, unused by code).
- `prototype/customer-ui/` directory containing earlier static HTML prototypes.
- `D:\Healty Bite` sibling directory containing earlier standalone project bundle.

### E. DATABASE ISSUES
- The live database contains 1 legacy table (`admin`) that is not present in `schema.sql`.
- Migration `add_review_replies.php` added `restaurant_reply` and `replied_at` to `reviews`, which should be included in future consolidated DDL exports.

### F. CODE/SCHEMA MISMATCHES
- The application stores staff in the `users` table with `restaurant_id` and `role_id`; there is no separate `restaurant_staff` table. Documentation must not invent a separate staff table.
- Reviews link to `restaurants` and `orders`, not to individual dishes (`food_items`).

### G. DOCUMENTATION MISMATCHES
- Older documentation references to "Call Servant" or "Platform Orders" are obsolete and refuted by the active codebase.

### H. INFORMATION THAT STILL NEEDS VERIFICATION
- Stakeholder preference on whether the legacy `admin` table in MySQL should eventually be dropped.
- Production tax rate and service charge percentages for real-world deployment (currently defaults to 5% GST and 0% service charge).

### I. RECOMMENDED NEXT STEP
> [!IMPORTANT]
> **Review this source-of-truth report before creating any diagrams.**
> DO NOT CREATE DIAGRAMS YET.
