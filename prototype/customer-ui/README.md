# Healthy Bite — Customer Frontend UI Prototype
## Figma-Accurate Implementation (Greenhouse Kitchen)

This folder contains the **standalone visual and interactive customer frontend prototype** for the **Healthy Bite** digital restaurant menu and food ordering platform. It is built strictly according to the Figma-generated design references in `Layout/`.

---

## 1. Prototype Purpose
The customer frontend allows guests dining at a restaurant (or ordering online) to:
- Browse a restaurant-specific digital menu with accurate category filters.
- Search food by name, ingredients, dietary tags, or category with real-time feedback.
- Filter by dynamic dietary attributes (`High Protein`, `Vegetarian`, `Under 600 kcal`, `Low Sugar`).
- Inspect food details with base nutrition and customize food items (variants, bread/grain base, protein, sauces, toppings).
- See dynamic, instantaneous price and nutrition calculations that update in real time based on their selections (preserving missing macros without coercion to 0).
- Manage a live cart with unique configuration signatures for distinct items.
- Simulate the complete ordering process: Table locked Dine-in checkout, order placement, confirmation receipt, and 5-stage live kitchen status tracking.

> **Important**: This is a pure frontend prototype built with semantic HTML5, modern CSS3 (custom properties design system), and vanilla ES6+ JavaScript. It does not touch or overwrite the existing PHP MVC backend or MySQL database.

---

## 2. Directory Structure

```
prototype/
└── customer-ui/
    ├── index.html              # Entry point redirecting to menu.html
    ├── menu.html               # Main desktop (3-col grid) & mobile customer menu
    ├── food-details.html       # Standalone deep-linkable food customizer
    ├── cart.html               # Full cart breakdown with tax & fee calculations
    ├── checkout.html           # Dine-in (Table 12 locked) & Takeaway checkout
    ├── confirmation.html       # Order placed confirmation receipt (#HB1025)
    ├── tracking.html           # 5-stage interactive kitchen timeline stepper
    │
    ├── assets/
    │   ├── css/
    │   │   ├── variables.css   # Design tokens (#2E7D32, #1B5E20, #EAF5EA, etc.)
    │   │   ├── reset.css       # Standard normalization and resets
    │   │   ├── layout.css      # Header, 2-column layout grid, mobile bottom nav
    │   │   ├── components.css  # Buttons, search, chips, quantity, radio/check cards
    │   │   ├── customer.css    # Food cards (desktop vertical & mobile horizontal)
    │   │   ├── food-details.css# 2-panel desktop modal & full-screen mobile sheet
    │   │   ├── cart.css        # Desktop dark cart card (#122A16), mobile floating pill
    │   │   ├── checkout.css    # Checkout form, order type toggle, payment mock
    │   │   └── responsive.css  # Fluid breakpoints (320px to 1600px)
    │   │
    │   ├── js/
    │   │   ├── data.js         # Single source of truth (Greenhouse Kitchen catalog)
    │   │   ├── pricing.js      # Unit price, customizations delta, cart totals
    │   │   ├── nutrition.js    # Macro recalculations (preserves nulls, hides zeros)
    │   │   ├── state.js        # Reactive observable store with localStorage
    │   │   ├── menu.js         # Menu filtering & rendering controller
    │   │   ├── search.js       # Live search engine across all item attributes
    │   │   ├── filters.js      # Quick filter & category chips controller
    │   │   ├── food-details.js # Standalone food details page controller
    │   │   ├── cart.js         # Full cart page controller
    │   │   ├── checkout.js     # Checkout form controller
    │   │   ├── app.js          # Master page bootstrapper
    │   │   └── components/
    │   │       ├── restaurant-header.js   # Identity & Table 12 badge
    │   │       ├── search-bar.js          # Sleek search bar with clear button
    │   │       ├── filter-chip.js         # Quick filter scrollable chips
    │   │       ├── category-chip.js       # Restaurant category tabs
    │   │       ├── food-card.js           # Reusable food card component
    │   │       ├── food-detail-modal.js   # 2-panel modal with options & validation
    │   │       ├── cart-item.js           # Individual item renderer
    │   │       ├── cart-preview.js        # Desktop dark sidebar card & mobile pill
    │   │       ├── nutrition-summary.js   # Macro summary widget
    │   │       ├── quantity-control.js    # Reusable quantity steppers
    │   │       ├── toast.js               # Toast notifications
    │   │       ├── loading-state.js       # Loading skeletons
    │   │       └── empty-state.js         # Zero-results state
    │   │
    │   └── images/
    │       └── foods/          # Local SVG fallback vector artwork
    └── README.md
```

---

## 3. How to Open and Run

No build step, Node.js, or bundler is required.
You can open the files directly or serve them via any HTTP server:

1. **Option A: Via Local Server (e.g. PHP)**
   Run:
   ```powershell
   cd "d:\NEW healthy bite"
   # Access via browser:
   http://localhost:8000/prototype/customer-ui/menu.html
   ```

2. **Option B: Direct File Access**
   Double-click `prototype/customer-ui/menu.html` or `index.html` in Windows Explorer to open directly in Google Chrome, Edge, or Firefox.

---

## 4. Demo Restaurant & Menu Details
- **Restaurant**: Greenhouse Kitchen
- **Branch**: Indiranagar · Bengaluru
- **Table Session**: Table 12 (Dine-in locked)
- **Tagline**: *Fresh food. Clear choices.*
- **Categories**:
  1. Main Meals
  2. Bowls
  3. Wraps
  4. Coffee
  5. Beverages
  6. Desserts

### Food Items Catalog (`assets/js/data.js`):
1. **Paneer Protein Bowl** (₹249 | 520 kcal | 35g protein | Veg)
2. **Chicken Rice Bowl** (₹279 | 580 kcal | 42g protein | Non-Veg)
3. **Grilled Paneer Wrap** (₹219 | 460 kcal | 25g protein | Veg)
4. **Iced Protein Coffee** (₹165 | 140 kcal | 15g protein | 95mg caffeine | Veg)
5. **Banana Protein Smoothie** (₹195 | 320 kcal | 24g protein | Veg)
6. **Chocolate Protein Brownie** (₹140 | 230 kcal | 14g protein | Veg)
7. **Protein Ice Cream** (₹169 | 180 kcal | 20g protein | Veg)

---

## 5. Design Tokens (Figma Exact Match)
- **Primary Green**: `#2E7D32`
- **Dark Green**: `#1B5E20`
- **Deep Forest (Cart Container & Modal Footer)**: `#122A16`
- **Light Green Accent**: `#EAF5EA`
- **Accent Orange**: `#F59E0B`
- **Main Text**: `#1F2937`
- **Secondary Text**: `#6B7280`
- **Background**: `#FAFAF8`
- **Card**: `#FFFFFF`
- **Border**: `#E5E7EB`
- **Typography**: Inter (with Poppins / Plus Jakarta Sans fallback)

---

## 6. Interactive Features
1. **Live Search**: Filters across dish names, descriptions, ingredients, and dietary tags on every keystroke. Includes clear button.
2. **Quick Filters**: Filters items dynamically based on actual item metadata (`High Protein` for >= 20g protein, `Vegetarian` for veg type, `Under 600 kcal`, `Low Sugar` for <= 6g sugar).
3. **Restaurant Category Tabs**: Shows only items belonging to selected category, or all grouped by category.
4. **Desktop Modal / Mobile Sheet**:
   - 2-panel desktop dialog (`#EAF5EA` left overview + white right options).
   - Food-scoped variants (e.g. Regular vs Large/Double).
   - Scoped customizations with required/optional indicators and quantity limit enforcement.
   - Live recalculation of price: `(base + variant + customizations) * quantity`.
   - Live recalculation of macros: `(base_macro + variant_macro + customizations_macro) * quantity`.
   - Required selection validation: disables Add to Cart button until satisfied.
5. **Cart System**:
   - Desktop dark container (`#122A16`) with live subtotal and item counter.
   - Mobile floating pill bar with item count and price.
   - Items keep their distinct configurations (e.g. Paneer Bowl with Extra Paneer vs Avocado).
   - Increments, decrements, item deletion, and full cart clear.
   - Persisted across reloads using `localStorage`.
6. **Order Placement & Tracking**:
   - Table 12 context visually locked for dine-in.
   - Simulated payment options (UPI, Card, Cash).
   - Order confirmation receipt with token `#HB1025`.
   - 5-stage kitchen status stepper (`Placed` → `Accepted` → `Preparing` → `Ready` → `Completed`) with interactive simulator controls.

---

## 7. Simulated vs Future PHP/MySQL Backend
| Feature | In this Prototype (Simulated) | Later Connected to PHP/MySQL |
| :--- | :--- | :--- |
| **Catalog Data** | Loaded from `assets/js/data.js` | Fetched from `GET /api/menu?restaurant_id=1` |
| **Table Session** | Static Table 12 context | Read from QR code token `/menu?qr_token=...` |
| **Cart Storage** | `localStorage` | Session-backed & synced cart via REST API |
| **Order Placement** | JavaScript generated ID | `POST /api/orders` inserting into `orders` table |
| **Kitchen Tracking** | Interactive simulator buttons | Live Server-Sent Events (SSE) or WebSocket from KDS |
