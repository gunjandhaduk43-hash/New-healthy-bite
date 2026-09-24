-- ============================================================
-- HEALTHY BITE — Sample Seed Data
-- Restaurant: Greenhouse Kitchen (Indiranagar, Bengaluru)
-- ============================================================

USE `healthy_bite`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLES
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Super Admin', 'super_admin', 'Platform administrator with full privileges'),
(2, 'Restaurant Owner', 'restaurant_owner', 'Owner of a restaurant entity'),
(3, 'Manager', 'manager', 'Branch manager supervising table service and orders'),
(4, 'Staff', 'staff', 'Waitstaff and kitchen staff');

-- 2. RESTAURANTS (Greenhouse Kitchen)
INSERT INTO `restaurants` (
    `id`, `owner_user_id`, `name`, `slug`, `logo`, `cover_image`,
    `description`, `phone`, `email`, `address`, `city`, `state`, `status`
) VALUES (
    1, NULL, 'Greenhouse Kitchen', 'greenhouse-kitchen', 'greenhouse_logo.png', 'greenhouse_cover.jpg',
    'Fresh food, clear choices, and protein-rich meals made for everyday eating.',
    '+918025201234', 'hello@greenhousekitchen.com',
    '100 Feet Road, HAL 2nd Stage, Indiranagar', 'Bengaluru', 'Karnataka', 'approved'
);

-- 3. USERS (Password: Secret@123)
-- Hash generated via password_hash('Secret@123', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `role_id`, `restaurant_id`, `name`, `email`, `password`, `status`) VALUES
(1, 1, NULL, 'System Admin', 'admin@healthybite.com', '$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza', 'active'),
(2, 2, 1, 'Aarav Patel', 'owner@greenhousekitchen.com', '$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza', 'active'),
(3, 3, 1, 'Priya Nair', 'manager@greenhousekitchen.com', '$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza', 'active');

UPDATE `restaurants` SET `owner_user_id` = 2 WHERE `id` = 1;

-- 4. BRANCHES
INSERT INTO `branches` (`id`, `restaurant_id`, `name`, `address`, `city`, `state`, `phone`, `status`) VALUES
(1, 1, 'Greenhouse Kitchen – Indiranagar', '100 Feet Road, HAL 2nd Stage, Indiranagar', 'Bengaluru', 'Karnataka', '+918025201234', 'active');

-- 5. CATEGORIES
INSERT INTO `categories` (`id`, `restaurant_id`, `name`, `slug`, `description`, `image`, `sort_order`, `status`) VALUES
(1, 1, 'Bowls', 'bowls', 'Nutrient-packed warm and cold signature protein bowls', 'cat_bowls.jpg', 1, 'active'),
(2, 1, 'Wraps', 'wraps', 'Whole-wheat high-protein roll-ups', 'cat_wraps.jpg', 2, 'active'),
(3, 1, 'Beverages', 'beverages', 'Clean cold-pressed functional juices and shakes', 'cat_beverages.jpg', 3, 'active'),
(4, 1, 'Desserts', 'desserts', 'Guilt-free unrefined sweet treats', 'cat_desserts.jpg', 4, 'active');

-- 6. FOOD ITEMS
INSERT INTO `food_items` (
    `id`, `restaurant_id`, `category_id`, `name`, `slug`, `description`, `image`,
    `ingredients`, `allergens`, `food_type`, `base_price`,
    `calories`, `protein`, `carbs`, `fat`, `fiber`, `sugar`, `sodium`, `caffeine`,
    `serving_size`, `is_featured`, `is_popular`, `is_available`
) VALUES
-- 1. Paneer Protein Bowl
(1, 1, 1, 'Paneer Protein Bowl', 'paneer-protein-bowl',
 'Tandoori paneer, brown rice, fresh greens, cucumber, pickled onion and mint yogurt.',
 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&auto=format&fit=crop&q=80', 'Paneer, Brown Rice, Lettuce, Cucumber, Pickled Onion, Mint Yogurt, Fresh Herbs',
 'Contains milk. Prepared in a facility that also handles nuts, soy and gluten.', 'vegetarian', 249.00,
 520, 38.00, 48.00, 18.00, 8.00, 6.00, 420.00, NULL,
 '420 g', 1, 1, 1),

-- 2. Chicken Rice Bowl
(2, 1, 1, 'Chicken Rice Bowl', 'chicken-rice-bowl',
 'Grilled sous-vide chicken breast served with steamed brown rice, fresh steamed broccoli, cherry tomatoes and citrus herb dressing.',
 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop&q=80', 'Chicken Breast, Brown Rice, Steamed Broccoli, Cherry Tomatoes, Citrus Vinaigrette, Fresh Herbs',
 'Prepared in a facility that handles nuts, dairy, soy and gluten.', 'non_vegetarian', 279.00,
 560, 44.00, 50.00, 14.00, 6.00, 4.00, 510.00, NULL,
 '440 g', 1, 1, 1),

-- 3. Grilled Paneer Wrap
(3, 1, 2, 'Grilled Paneer Wrap', 'grilled-paneer-wrap',
 'High-protein paneer slices wrapped in whole-wheat roti with crisp bell peppers.',
 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=800&auto=format&fit=crop&q=80', 'Paneer, Whole Wheat Flatbread, Bell Peppers, Onions, Low-fat Yogurt Mint Sauce',
 'Gluten, Dairy', 'vegetarian', 219.00,
 410, 22.00, 35.00, 16.00, 6.00, 3.00, 380.00, NULL,
 '240g wrap', 0, 1, 1),

-- 4. Iced Protein Coffee
(4, 1, 3, 'Iced Protein Coffee', 'iced-protein-coffee',
 'Cold espresso shaken with whey protein isolate and unsweetened milk.',
 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=800&auto=format&fit=crop&q=80', 'Espresso, Whey Protein Isolate, Skimmed Milk, Ice',
 'Dairy', 'vegetarian', 165.00,
 140, 15.00, 8.00, 5.00, 0.00, 4.00, 110.00, 95.00,
 '350ml cup', 1, 1, 1),

-- 5. Banana Protein Smoothie
(5, 1, 3, 'Banana Protein Smoothie', 'banana-protein-smoothie',
 'Ripe bananas blended with whey protein, chia seeds, and almond milk.',
 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&auto=format&fit=crop&q=80', 'Banana, Whey Protein, Almond Milk, Chia Seeds',
 'Tree Nuts', 'vegetarian', 195.00,
 290, 24.00, 36.00, 4.00, 5.00, 18.00, 140.00, NULL,
 '400ml glass', 0, 0, 1),

-- 6. Chocolate Protein Brownie
(6, 1, 4, 'Chocolate Protein Brownie', 'chocolate-protein-brownie',
 'Fudge brownie baked with almond flour, cocoa, and isolate protein.',
 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&auto=format&fit=crop&q=80', 'Almond Flour, Dark Cocoa, Whey Protein, Stevia',
 'Tree Nuts, Dairy', 'vegetarian', 149.00,
 210, 12.00, 20.00, 9.00, 4.00, 8.00, 120.00, NULL,
 '85g square', 0, 1, 1),

-- 7. Protein Ice Cream
(7, 1, 4, 'Protein Ice Cream', 'protein-ice-cream',
 'Slow-churned low-fat protein ice cream sweetened with erythritol.',
 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=800&auto=format&fit=crop&q=80', 'Milk Protein, Skimmed Milk, Erythritol, Vanilla Extract',
 'Dairy', 'vegetarian', 180.00,
 180, 20.00, 16.00, 5.00, 2.00, 6.00, 90.00, NULL,
 '120g cup', 1, 0, 1),

-- 48. Artisan Sourdough Protein Pizza
(48, 1, 5, 'Artisan Sourdough Protein Pizza', 'artisan-sourdough-protein-pizza',
 'Hand-stretched slow fermented sourdough crust topped with San Marzano tomato reduction, light mozzarella, basil, and fresh vegetable medley.',
 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&auto=format&fit=crop&q=80', 'Sourdough Flour, San Marzano Tomatoes, Low-Fat Mozzarella, Bell Peppers, Olives, Fresh Basil, Extra Virgin Olive Oil',
 'Gluten, Dairy', 'vegetarian', 299.00,
 640, 32.00, 72.00, 16.00, 7.00, 4.00, 480.00, NULL,
 '350 g (8 inch)', 1, 1, 1),

-- 49. Grilled Lean Protein Burger
(49, 1, 5, 'Grilled Lean Protein Burger', 'grilled-lean-protein-burger',
 'Juicy grilled spiced chicken breast patty on a whole-wheat brioche bun with crisp lettuce, tomato, pickles, and light chipotle Greek yogurt spread.',
 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&auto=format&fit=crop&q=80', 'Whole-Wheat Brioche, Minced Chicken Breast, Crisp Lettuce, Ripe Tomato, Pickles, Greek Yogurt Chipotle Sauce',
 'Gluten, Dairy', 'non_vegetarian', 269.00,
 490, 36.00, 42.00, 14.00, 5.00, 5.00, 460.00, NULL,
 '280 g burger', 1, 1, 1);

-- 7. FOOD VARIANTS
INSERT INTO `food_variants` (
    `id`, `food_item_id`, `name`, `description`, `price_adjustment`,
    `calories_adjustment`, `protein_adjustment`, `carbs_adjustment`, `fat_adjustment`,
    `caffeine_adjustment`, `is_required`, `is_available`, `sort_order`
) VALUES
-- Pizza variants
(1, 48, '8-inch Personal (Standard)', 'Standard size for one', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1),
(2, 48, '11-inch Sharing (+₹120)', 'Extra slices to share', 120.00, 240, 16.00, 32.00, 8.00, NULL, 1, 1, 2),

-- Iced Protein Coffee variants
(3, 4, 'Regular (350ml Single Shot)', 'Standard single espresso shot', 0.00, 0, 0.00, 0.00, 0.00, 0.00, 1, 1, 1),
(4, 4, 'Grande (450ml Double Shot)', 'Double espresso shot with extra whey', 40.00, 50, 8.00, 3.00, 1.00, 45.00, 1, 1, 2),

-- Protein Ice Cream variants
(5, 7, 'Single Cup (120g)', 'Standard single cup', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1),
(6, 7, 'Double Waffle Cup (200g)', 'Double scoop portion in waffle bowl', 60.00, 110, 12.00, 8.00, 3.00, NULL, 1, 1, 2);

-- 8. FOOD CUSTOMIZATIONS
INSERT INTO `food_customizations` (
    `id`, `food_item_id`, `name`, `description`, `group_name`, `price_adjustment`,
    `calories_adjustment`, `protein_adjustment`, `carbs_adjustment`, `fat_adjustment`,
    `caffeine_adjustment`, `is_required`, `min_quantity`, `max_quantity`, `is_available`, `sort_order`
) VALUES
-- Paneer Protein Bowl customizations (Exact reference structure)
(1, 1, 'Brown rice', '', '1. Choose base', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1, 1, 1),
(2, 1, 'Quinoa', '', '1. Choose base', 30.00, 40, 4.00, 6.00, 1.00, NULL, 1, 1, 1, 1, 2),
(3, 1, 'Tandoori paneer · 100 g', '', '2. Choose protein', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1, 1, 1),
(4, 1, 'Extra paneer · 50 g', '', '2. Choose protein', 40.00, 90, 10.00, 1.00, 5.00, NULL, 1, 1, 1, 1, 2),
(5, 1, 'Mint yogurt', '', '3. Choose sauce', 0.00, 0, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, 1),
(6, 1, 'Spicy green chutney', '', '3. Choose sauce', 0.00, 0, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, 2),
(7, 1, 'Pickled onion', '', '4. Toppings & add-ons', 0.00, 10, 0.20, 2.00, 0.00, NULL, 0, 0, 4, 1, 1),
(8, 1, 'Avocado', '', '4. Toppings & add-ons', 50.00, 80, 1.00, 4.00, 7.00, NULL, 0, 0, 4, 1, 2),
(9, 1, 'Roasted seeds', '', '4. Toppings & add-ons', 25.00, 45, 3.00, 1.50, 3.50, NULL, 0, 0, 4, 1, 3),
(10, 1, 'Cherry tomatoes', '', '4. Toppings & add-ons', 0.00, 15, 0.80, 3.00, 0.20, NULL, 0, 0, 4, 1, 4),
(11, 1, 'Extra greens', '', '4. Toppings & add-ons', 20.00, 10, 1.00, 1.50, 0.10, NULL, 0, 0, 4, 1, 5),
(12, 1, 'Grilled mushrooms', '', '4. Toppings & add-ons', 40.00, 45, 3.00, 4.00, 1.50, NULL, 0, 0, 4, 1, 6),
-- Chocolate Protein Brownie customizations
(13, 6, 'Served Warm (Oven Heats)', 'Warmed up for gooey fudge center', '1. Serving Style', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1, 1, 1),
(14, 6, 'Served Chilled (Dense Fudge)', 'Chilled firm texture', '1. Serving Style', 0.00, 0, 0.00, 0.00, 0.00, NULL, 1, 1, 1, 1, 2),
(15, 6, 'Berry Compote', 'Fresh tart berry reduction', '2. Gourmet Toppings', 30.00, 25, 0.50, 6.00, 0.20, NULL, 0, 0, 3, 1, 1),
(16, 6, 'Roasted Almond Flakes', 'Crunchy toasted sliced almonds', '2. Gourmet Toppings', 25.00, 45, 2.00, 1.50, 3.50, NULL, 0, 0, 3, 1, 2),
(17, 6, 'Sugar-Free Dark Chocolate Drizzle', 'Rich antioxidant dark cocoa drizzle', '2. Gourmet Toppings', 20.00, 30, 0.50, 2.00, 2.00, NULL, 0, 0, 3, 1, 3),
(18, 6, 'Scoop of Protein Vanilla Ice Cream', 'High-protein low-sugar vanilla scoop', '3. Add-ons', 60.00, 90, 10.00, 8.00, 2.50, NULL, 0, 0, 1, 1, 1);


-- 9. RESTAURANT TABLES (Indiranagar branch)
INSERT INTO `restaurant_tables` (`id`, `restaurant_id`, `branch_id`, `table_number`, `status`) VALUES
(1, 1, 1, 'Table 1', 'available'),
(2, 1, 1, 'Table 2', 'available'),
(3, 1, 1, 'Table 3', 'occupied'),
(4, 1, 1, 'Table 12', 'available');

-- 10. QR TOKENS (Table 12 active token)
INSERT INTO `qr_tokens` (`id`, `restaurant_id`, `branch_id`, `table_id`, `token`, `status`, `expires_at`) VALUES
(1, 1, 1, 4, 'hb_qr_token_indiranagar_tbl12_7f8a9b', 'active', NULL);

-- 11. SAMPLE CUSTOMER
INSERT INTO `customers` (`id`, `name`, `mobile`, `email`) VALUES
(1, 'Rahul Sharma', '+919876543210', 'rahul.sharma@example.com');

SET FOREIGN_KEY_CHECKS = 1;
