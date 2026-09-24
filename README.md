# Healthy Bite — Digital Restaurant Menu and Food Ordering System

Healthy Bite is a modular, responsive restaurant food ordering platform built from scratch with modern PHP (MVC), MySQL, and vanilla JavaScript.

---

## Current Status: Customer Website & QR Ordering (Complete)

The **Customer Website and QR Ordering System** is fully implemented and tested with the complete underlying MVC backend, database schema, seeder, repositories, domain services, and UI flows.

---

## Key Features Implemented

1. **Digital Menu & QR Code Ordering:**
   - Access menu via standard URL or physical table QR code (`/menu?token=hb_qr_token_indiranagar_tbl12_7f8a9b`).
   - Server-side QR token verification resolves verified restaurant, branch, and table without trusting client-side overrides.
2. **Dynamic Live Search & Category Filtering:**
   - Real-time client search across food names, descriptions, and ingredients with clean empty state handling.
   - Horizontally scrollable category chips scoped strictly to the restaurant (*Greenhouse Kitchen*).
3. **Interactive Food Customization Modal:**
   - Portions & Variants (e.g. Regular, Large +₹50).
   - Customization groups (e.g. Extra Paneer, Hass Avocado, Pumpkin Seeds) with min/max quantities.
   - Real-time price and nutrition recalculation (Calories, Protein, Carbs, Fat) preserving strict `NULL` representation (never converting unknown nutrition to 0).
4. **Slide-out Cart Drawer:**
   - Centralized reactive state in vanilla JavaScript synchronized with `localStorage`.
   - Automatic line merging if food, portion, and customizations match; independent lines otherwise.
   - Subtotal, GST (5%), and total calculation with quantity steppers.
5. **Checkout & Dining Options:**
   - Dine-In vs. Takeaway selector.
   - Dining table locking for QR diners.
   - Customer details input and cooking notes.
   - Payment method selection (Instant UPI, Card/POS, Counter Cash).
6. **Order Placement & Immutability:**
   - Backend re-calculates and validates all items, availability, portion prices, and customization adjustments before storing.
   - Freezes immutable price and nutrition snapshots in `order_items` and `order_item_customizations`.
7. **Order Confirmation & Live Tracking:**
   - Order confirmation with summary receipt.
   - Real-time order progress stepper (*Placed* $\rightarrow$ *Accepted* $\rightarrow$ *Preparing* $\rightarrow$ *Ready* $\rightarrow$ *Completed*) with live backend polling.

---

## Technology Stack

- **Backend:** PHP 8.0+ (Custom MVC, PSR-4 Autoloading, PDO Prepared Statements)
- **Database:** MySQL 8.x (`healthy_bite`, `InnoDB`, `utf8mb4_unicode_ci`)
- **Frontend:** Vanilla JavaScript (ES6+), CSS3 Custom Properties Design System, Bootstrap Icons
- **Security:** PDO parameterized queries, CSRF tokens, strict server-side price recalculation, output escaping via `e()` (`htmlspecialchars`).

---

## Directory Layout

```
healthy-bite/
├── app/
│   ├── Controllers/
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── FoodController.php
│   │   ├── HomeController.php
│   │   ├── MenuController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   └── RestaurantController.php
│   ├── Core/
│   │   ├── App.php
│   │   ├── Auth.php
│   │   ├── Controller.php
│   │   ├── Csrf.php
│   │   ├── Database.php
│   │   ├── Env.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── Validator.php
│   ├── Helpers/
│   │   ├── food.php
│   │   ├── format.php
│   │   ├── security.php
│   │   └── url.php
│   ├── Repositories/
│   │   ├── BranchRepository.php
│   │   ├── CategoryRepository.php
│   │   ├── FoodRepository.php
│   │   ├── OrderRepository.php
│   │   ├── PaymentRepository.php
│   │   └── RestaurantRepository.php
│   └── Services/
│       ├── CartService.php
│       ├── MenuService.php
│       ├── NutritionService.php
│       ├── OrderService.php
│       ├── PaymentService.php
│       ├── PricingService.php
│       └── QrService.php
├── config/
│   ├── app.php
│   ├── constants.php
│   └── database.php
├── database/
│   ├── schema/
│   │   └── schema.sql
│   └── seeders/
│       └── sample_data.sql
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── cart.css
│   │   │   ├── checkout.css
│   │   │   ├── components.css
│   │   │   ├── customer.css
│   │   │   ├── food-details.css
│   │   │   ├── layout.css
│   │   │   ├── reset.css
│   │   │   ├── responsive.css
│   │   │   └── variables.css
│   │   └── js/
│   │       ├── api.js
│   │       ├── app.js
│   │       ├── cart.js
│   │       ├── checkout.js
│   │       ├── food-details.js
│   │       ├── menu.js
│   │       ├── nutrition.js
│   │       ├── order.js
│   │       ├── pricing.js
│   │       ├── state.js
│   │       └── utils.js
│   ├── .htaccess
│   └── index.php
├── resources/
│   └── views/
│       ├── customer/
│       │   ├── errors/
│       │   │   ├── 404.php
│       │   │   ├── 500.php
│       │   │   └── restaurant-unavailable.php
│       │   ├── checkout.php
│       │   ├── confirmation.php
│       │   ├── menu.php
│       │   ├── tracking.php
│       │   └── welcome.php
│       ├── layouts/
│       │   ├── customer.php
│       │   └── minimal.php
│       └── partials/
│           ├── cart-drawer.php
│           ├── food-detail.php
│           └── restaurant-header.php
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   ├── cache/
│   └── logs/
├── .env
├── .env.example
├── .gitignore
└── composer.json
```

---

## How to Run & Test

1. **Start the PHP Development Server:**
   ```powershell
   cd "d:\NEW healthy bite"
   php -S localhost:8000 -t public
   ```

2. **Customer Experience URLs:**
   - **Welcome / Landing:** [http://localhost:8000/](http://localhost:8000/)
   - **Digital Menu (General):** [http://localhost:8000/menu](http://localhost:8000/menu)
   - **Digital Menu (Table 12 QR Code):** [http://localhost:8000/menu?token=hb_qr_token_indiranagar_tbl12_7f8a9b](http://localhost:8000/menu?token=hb_qr_token_indiranagar_tbl12_7f8a9b)
   - **Checkout:** [http://localhost:8000/menu/checkout](http://localhost:8000/menu/checkout)

3. **REST API Endpoints:**
   - `GET /api/restaurant` — Restaurant & branch profile
   - `GET /api/categories` — Categories
   - `GET /api/foods` — Filterable food items
   - `GET /api/foods/{id}` — Single item with variants and customization groups
   - `POST /api/cart/validate` — Server-side cart validation
   - `POST /api/orders` — Order placement
   - `GET /api/orders/{orderNumber}` — Order tracking details
   - `POST /api/payment/simulate` — Payment simulation
