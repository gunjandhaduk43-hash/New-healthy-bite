<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__, 2));
spl_autoload_register(function ($c) {
    $p = 'App\\';
    $f = HB_ROOT . '/app/' . str_replace('\\', '/', substr($c, strlen($p))) . '.php';
    if (file_exists($f)) require_once $f;
});
require_once HB_ROOT . '/config/constants.php';
\App\Core\Env::load(HB_ROOT . '/.env');

$db = \App\Core\Database::getConnection();

echo "Starting Item-Specific Customization and Caffeine Configuration...\n";

// 1. Ensure Beverages have signature Coffee and Tea items if not already present
$existingColdBrew = $db->query("SELECT id FROM food_items WHERE name LIKE '%Cold Brew%' LIMIT 1")->fetch();
if (!$existingColdBrew) {
    $db->prepare("
        INSERT INTO food_items (
            restaurant_id, category_id, name, slug, description, image, ingredients, allergens, food_type,
            base_price, calories, protein, carbs, fat, fiber, sugar, sodium, caffeine, serving_size, is_featured, is_popular, is_available
        ) VALUES (
            1, 3, 'Artisan Single-Origin Cold Brew', 'artisan-single-origin-cold-brew',
            'Slow-steeped for 18 hours using single-origin Chikmagalur Arabica beans. Exceptionally smooth, bold, and naturally energizing.',
            'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=800&auto=format&fit=crop&q=80',
            'Single-Origin Arabica Cold Brew, Alkaline Mineral Water, Orange Peel Zest',
            NULL, 'vegan', 165.00,
            15, 0.50, 2.00, 0.00, 0.00, 0.00, 10.00, 140.00, '350 ml', 1, 1, 1
        )
    ")->execute();
    echo "Added 'Artisan Single-Origin Cold Brew'\n";
}

$existingGreenTea = $db->query("SELECT id FROM food_items WHERE name LIKE '%Green Tea%' LIMIT 1")->fetch();
if (!$existingGreenTea) {
    $db->prepare("
        INSERT INTO food_items (
            restaurant_id, category_id, name, slug, description, image, ingredients, allergens, food_type,
            base_price, calories, protein, carbs, fat, fiber, sugar, sodium, caffeine, serving_size, is_featured, is_popular, is_available
        ) VALUES (
            1, 3, 'Organic Himalayan Green Tea', 'organic-himalayan-green-tea',
            'High-altitude spring-harvested whole green tea leaves infused with fresh ginger, lemongrass, and natural mint.',
            'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800&auto=format&fit=crop&q=80',
            'Organic Green Tea Leaves, Fresh Lemongrass, Ginger Root, Spearmint',
            NULL, 'vegan', 135.00,
            5, 0.20, 1.00, 0.00, 0.00, 0.00, 5.00, 35.00, '350 ml', 1, 0, 1
        )
    ")->execute();
    echo "Added 'Organic Himalayan Green Tea'\n";
}

// 2. Set accurate baseline caffeine and nutrition across all beverage items
$caffeineUpdates = [
    // Iced Protein Coffee: 95mg base
    'Iced Protein Coffee' => ['caffeine' => 95.0, 'allergens' => 'Dairy', 'food_type' => 'vegetarian'],
    // Cold Brew: 140mg base
    'Artisan Single-Origin Cold Brew' => ['caffeine' => 140.0, 'allergens' => null, 'food_type' => 'vegan'],
    // Green Tea: 35mg base
    'Organic Himalayan Green Tea' => ['caffeine' => 35.0, 'allergens' => null, 'food_type' => 'vegan'],
    // Naturally caffeine-free drinks: 0.0mg
    'Banana Protein Smoothie' => ['caffeine' => 0.0, 'allergens' => 'Dairy', 'food_type' => 'vegetarian'],
    'Green Detox Cold-Pressed Juice' => ['caffeine' => 0.0, 'allergens' => null, 'food_type' => 'vegan'],
    'Raw Coconut Water with Chia Seeds' => ['caffeine' => 0.0, 'allergens' => null, 'food_type' => 'vegan'],
    'Berry Antioxidant Blast Shake' => ['caffeine' => 0.0, 'allergens' => 'Dairy, Tree Nuts', 'food_type' => 'vegetarian'],
];

foreach ($caffeineUpdates as $foodName => $meta) {
    $stmt = $db->prepare("UPDATE food_items SET caffeine = :caffeine, allergens = :allergens, food_type = :food_type WHERE name = :name");
    $stmt->execute([
        'caffeine' => $meta['caffeine'],
        'allergens' => $meta['allergens'],
        'food_type' => $meta['food_type'],
        'name' => $foodName
    ]);
}

// For all non-beverage categories, set caffeine to 0.00 (naturally caffeine-free)
$db->exec("UPDATE food_items SET caffeine = 0.00 WHERE category_id != 3");

echo "Caffeine baselines updated.\n";

// 3. Clear out old generic variants and customizations so we can seed item-specific ones
// Disable foreign key checks during data refresh
$db->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->exec("DELETE FROM food_variants");
$db->exec("DELETE FROM food_customizations");
echo "Cleared old generic variants and customizations.\n";

// Helper functions for seeding
function addVariant(PDO $db, int $foodId, string $name, float $priceAdj = 0.0, ?int $calAdj = null, ?float $protAdj = null, ?float $carbsAdj = null, ?float $fatAdj = null, ?float $sugarAdj = null, ?float $caffAdj = null, int $sort = 0): void {
    $stmt = $db->prepare("
        INSERT INTO food_variants (
            food_item_id, name, price_adjustment, calories_adjustment, protein_adjustment,
            carbs_adjustment, fat_adjustment, sugar_adjustment, caffeine_adjustment,
            is_required, is_available, sort_order
        ) VALUES (
            :food_id, :name, :price, :cal, :prot, :carbs, :fat, :sugar, :caff, 1, 1, :sort
        )
    ");
    $stmt->execute([
        'food_id' => $foodId,
        'name' => $name,
        'price' => $priceAdj,
        'cal' => $calAdj,
        'prot' => $protAdj,
        'carbs' => $carbsAdj,
        'fat' => $fatAdj,
        'sugar' => $sugarAdj,
        'caff' => $caffAdj,
        'sort' => $sort
    ]);
}

function addCustom(PDO $db, int $foodId, string $group, string $name, float $priceAdj = 0.0, ?int $calAdj = null, ?float $protAdj = null, ?float $carbsAdj = null, ?float $fatAdj = null, ?float $sugarAdj = null, ?float $caffAdj = null, int $isReq = 0, int $minQ = 0, int $maxQ = 1, int $sort = 0): void {
    $stmt = $db->prepare("
        INSERT INTO food_customizations (
            food_item_id, group_name, name, price_adjustment, calories_adjustment, protein_adjustment,
            carbs_adjustment, fat_adjustment, sugar_adjustment, caffeine_adjustment,
            is_required, min_quantity, max_quantity, is_available, sort_order
        ) VALUES (
            :food_id, :group, :name, :price, :cal, :prot, :carbs, :fat, :sugar, :caff,
            :is_req, :min_q, :max_q, 1, :sort
        )
    ");
    $stmt->execute([
        'food_id' => $foodId,
        'group' => $group,
        'name' => $name,
        'price' => $priceAdj,
        'cal' => $calAdj,
        'prot' => $protAdj,
        'carbs' => $carbsAdj,
        'fat' => $fatAdj,
        'sugar' => $sugarAdj,
        'caff' => $caffAdj,
        'is_req' => $isReq,
        'min_q' => $minQ,
        'max_q' => $maxQ,
        'sort' => $sort
    ]);
}

// 4. Fetch all active foods
$allFoods = $db->query("
    SELECT f.id, f.name, f.category_id, c.slug as cat_slug, f.food_type
    FROM food_items f
    JOIN categories c ON f.category_id = c.id
    ORDER BY f.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($allFoods as $food) {
    $id = (int)$food['id'];
    $name = $food['name'];
    $slug = $food['cat_slug'];
    $type = $food['food_type'];

    // -------------------------------------------------------------
    // CATEGORY: BEVERAGES
    // -------------------------------------------------------------
    if ($slug === 'beverages') {
        if (str_contains($name, 'Coffee')) {
            // Iced Protein Coffee (95mg base caffeine)
            addVariant($db, $id, 'Regular (350ml Single Shot)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Grande (450ml Double Shot)', 40.0, 50, 5.0, 2.0, 1.0, 0, 65.0, 2);

            // Group: Milk Choice (Required)
            addCustom($db, $id, '1. Milk Choice', 'Skimmed Milk', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Milk Choice', 'Barista Oat Milk', 30.0, 25, 1.0, 4.0, 1.5, 1.0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Milk Choice', 'Almond Milk', 30.0, -15, -0.5, -2.0, 0.5, 0, 0, 1, 1, 1, 3);

            // Group: Espresso & Caffeine Boost
            addCustom($db, $id, '2. Espresso & Caffeine Boost', 'Extra Espresso Shot', 35.0, 5, 0.5, 1.0, 0, 0, 65.0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Espresso & Caffeine Boost', 'Decaf Single Extraction', 20.0, 0, 0, 0, 0, 0, -90.0, 0, 0, 1, 2);

            // Group: Sweetness (Required)
            addCustom($db, $id, '3. Sweetness Level', 'Unsweetened (0g sugar)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Sweetness Level', 'Natural Stevia Drop', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '3. Sweetness Level', 'Organic Raw Honey', 15.0, 25, 0, 6.0, 0, 6.0, 0, 1, 1, 1, 3);

            // Group: Ice Level (Required)
            addCustom($db, $id, '4. Ice Level', 'Regular Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '4. Ice Level', 'Less Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '4. Ice Level', 'No Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3);

            // Group: Protein Booster
            addCustom($db, $id, '5. Protein Booster', 'Extra Half Scoop Whey Isolate', 35.0, 55, 12.0, 1.0, 0.5, 0, 0, 0, 0, 1, 1);
            addCustom($db, $id, '5. Protein Booster', 'Chia Seeds Infusion', 20.0, 30, 1.5, 2.5, 1.5, 0, 0, 0, 0, 1, 2);

        } elseif (str_contains($name, 'Cold Brew')) {
            // Artisan Single-Origin Cold Brew (140mg base caffeine)
            addVariant($db, $id, 'Classic Glass (300ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Grande Tumbler (450ml)', 40.0, 10, 0.5, 1.0, 0, 0, 60.0, 2);

            // Group: Serving Style
            addCustom($db, $id, '1. Serving Style', 'Over Crystal Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Serving Style', 'Nitrogen Cold Brew (Ultra Smooth)', 25.0, 5, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Serving Style', 'Chilled No Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3);

            // Group: Milk Splash
            addCustom($db, $id, '2. Milk Splash', 'Pure Black (Zero Dairy)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Milk Splash', 'Splash of Barista Oat Milk', 25.0, 20, 0.5, 3.0, 1.0, 0.8, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Milk Splash', 'Splash of Almond Milk', 25.0, 15, 0.5, 1.0, 1.0, 0.2, 0, 1, 1, 1, 3);

            // Group: Caffeine Boost
            addCustom($db, $id, '3. Espresso Boost', 'Extra Cold Brew Concentrate Shot', 35.0, 10, 0.5, 1.5, 0, 0, 70.0, 0, 0, 1, 1);

            // Group: Sweetener
            addCustom($db, $id, '4. Sweetener', 'Unsweetened', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '4. Sweetener', 'Organic Stevia Drops', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '4. Sweetener', 'Sugar-Free Vanilla Syrup', 15.0, 5, 0, 1.0, 0, 0, 0, 1, 1, 1, 3);

        } elseif (str_contains($name, 'Green Tea')) {
            // Organic Himalayan Green Tea (35mg base caffeine)
            addVariant($db, $id, 'Standard Steeping Pot (350ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Large Infusion Flask (500ml)', 35.0, 5, 0, 1.0, 0, 0, 15.0, 2);

            // Group: Tea Strength (Affects caffeine!)
            addCustom($db, $id, '1. Tea Strength', 'Mild Leaf Infusion', 0.0, 0, 0, 0, 0, 0, -10.0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Tea Strength', 'Classic Balanced Steeping', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Tea Strength', 'Rich & Strong Green Infusion', 0.0, 0, 0, 0, 0, 0, 15.0, 1, 1, 1, 3);

            // Group: Fresh Herbs & Lemon
            addCustom($db, $id, '2. Herbal Botanicals', 'Fresh Squeezed Lemon & Mint', 0.0, 5, 0.1, 1.0, 0, 0.5, 0, 0, 0, 2, 1);
            addCustom($db, $id, '2. Herbal Botanicals', 'Crushed Himalayan Ginger Root', 15.0, 5, 0.2, 1.0, 0, 0.2, 0, 0, 0, 2, 2);
            addCustom($db, $id, '2. Herbal Botanicals', 'Cardamom & Lemongrass Pods', 15.0, 5, 0.1, 1.0, 0, 0.1, 0, 0, 0, 2, 3);

            // Group: Sweetener
            addCustom($db, $id, '3. Sweetener', 'Pure Unsweetened', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Sweetener', 'Wild Forest Raw Honey', 15.0, 25, 0, 6.0, 0, 6.0, 0, 1, 1, 1, 2);

        } elseif (str_contains($name, 'Banana Protein') || str_contains($name, 'Smoothie')) {
            // Banana Protein Smoothie (0mg caffeine)
            addVariant($db, $id, 'Regular Glass (350ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Large Power Shake (500ml)', 50.0, 120, 10.0, 16.0, 2.0, 8.0, 0, 2);

            // Group: Milk Base
            addCustom($db, $id, '1. Milk Base', 'Greek Yogurt & Toned Milk', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Milk Base', 'Creamy Barista Oat Milk', 30.0, 20, -1.0, 5.0, 1.0, 2.0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Milk Base', 'Pure Unsweetened Almond Milk', 30.0, -25, -1.0, -3.0, 0.5, 0, 0, 1, 1, 1, 3);

            // Group: Protein Boost
            addCustom($db, $id, '2. Protein Boost', 'Extra Scoop Whey Isolate (+18g P)', 45.0, 80, 18.0, 1.0, 0.5, 0.5, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Protein Boost', 'Organic Plant Pea Protein (+16g P)', 45.0, 75, 16.0, 1.5, 0.5, 0, 0, 0, 0, 1, 2);

            // Group: Nut Butters
            addCustom($db, $id, '3. Nut & Seed Add-on', 'Roasted Peanut Butter Scoop', 30.0, 90, 4.0, 3.0, 8.0, 1.0, 0, 0, 0, 2, 1);
            addCustom($db, $id, '3. Nut & Seed Add-on', 'Creamy Almond Butter Scoop', 40.0, 95, 4.0, 3.0, 8.5, 1.0, 0, 0, 0, 2, 2);
            addCustom($db, $id, '3. Nut & Seed Add-on', 'Organic Chia Seeds', 20.0, 30, 1.5, 2.0, 2.0, 0, 0, 0, 0, 2, 3);

            // Group: Sweetener
            addCustom($db, $id, '4. Sweetener', 'Natural Banana Sweetness (No Added Sugar)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '4. Sweetener', 'Natural Stevia Drop', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '4. Sweetener', 'Organic Raw Honey', 15.0, 25, 0, 6.0, 0, 6.0, 0, 1, 1, 1, 3);

        } elseif (str_contains($name, 'Juice') || str_contains($name, 'Green Detox')) {
            // Green Detox Cold-Pressed Juice (0mg caffeine)
            addVariant($db, $id, 'Fresh Press Bottle (300ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Cleanse Bottle (500ml)', 50.0, 50, 1.5, 10.0, 0, 6.0, 0, 2);

            // Group: Chilling & Temperature
            addCustom($db, $id, '1. Temperature', 'Chilled with Ice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Temperature', 'Chilled No Ice (Pure Juice)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);

            // Group: Botanicals & Add-ons
            addCustom($db, $id, '2. Botanical Infusions', 'Fresh Cold-Pressed Ginger Shot', 20.0, 10, 0.3, 2.0, 0, 0.4, 0, 0, 0, 2, 1);
            addCustom($db, $id, '2. Botanical Infusions', 'Extra Lemon & Fresh Mint', 15.0, 5, 0.2, 1.0, 0, 0.3, 0, 0, 0, 2, 2);
            addCustom($db, $id, '2. Botanical Infusions', 'Pinch of Himalayan Pink Rock Salt', 0.0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 3);
            addCustom($db, $id, '2. Botanical Infusions', 'Organic Soaked Chia Seeds', 25.0, 30, 1.5, 2.0, 2.0, 0, 0, 0, 0, 2, 4);

        } elseif (str_contains($name, 'Coconut Water')) {
            // Raw Coconut Water with Chia Seeds (0mg caffeine)
            addVariant($db, $id, 'Standard Glass (300ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Large Carafe (500ml)', 40.0, 45, 1.5, 9.0, 0.5, 4.0, 0, 2);

            // Group: Serving Temperature
            addCustom($db, $id, '1. Serving Style', 'Freshly Chilled', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Serving Style', 'Natural Room Temperature', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);

            // Group: Citrus & Herbs
            addCustom($db, $id, '2. Refreshment Infusions', 'Fresh Key Lime Splash', 10.0, 5, 0.1, 1.0, 0, 0.3, 0, 0, 0, 2, 1);
            addCustom($db, $id, '2. Refreshment Infusions', 'Crushed Fresh Mint Leaves', 0.0, 2, 0.1, 0.5, 0, 0.1, 0, 0, 0, 2, 2);
            addCustom($db, $id, '2. Refreshment Infusions', 'Pinch of Kala Namak (Black Salt)', 0.0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 3);
            addCustom($db, $id, '2. Refreshment Infusions', 'Double Soaked Chia Seeds (+2g P)', 20.0, 30, 2.0, 2.0, 2.0, 0, 0, 0, 0, 2, 4);

        } elseif (str_contains($name, 'Berry Antioxidant') || str_contains($name, 'Shake')) {
            // Berry Antioxidant Blast Shake (0mg caffeine)
            addVariant($db, $id, 'Regular (350ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Grande (500ml)', 50.0, 110, 9.0, 14.0, 1.5, 6.0, 0, 2);

            // Group: Milk Choice
            addCustom($db, $id, '1. Milk Choice', 'Low-Fat Cow Milk', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Milk Choice', 'Barista Oat Milk', 30.0, 25, 0.5, 4.0, 1.5, 1.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Milk Choice', 'Pure Almond Milk', 30.0, -15, -0.5, -2.0, 0.5, 0, 0, 1, 1, 1, 3);

            // Group: Protein & Berries
            addCustom($db, $id, '2. Nutrition Boost', 'Extra Scoop Vanilla Whey Isolate (+18g P)', 45.0, 80, 18.0, 1.0, 0.5, 0.5, 0, 0, 0, 2, 1);
            addCustom($db, $id, '2. Nutrition Boost', 'Extra Wild Blueberries & Raspberries', 35.0, 30, 0.5, 7.0, 0.2, 4.0, 0, 0, 0, 2, 2);
            addCustom($db, $id, '2. Nutrition Boost', 'Organic Soaked Chia Seeds', 20.0, 30, 1.5, 2.0, 2.0, 0, 0, 0, 0, 2, 3);

            // Group: Sweetener
            addCustom($db, $id, '3. Sweetener', 'Naturally Sweet (No Added Sugar)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Sweetener', 'Natural Stevia Drops', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '3. Sweetener', 'Pure Raw Honey', 15.0, 25, 0, 6.0, 0, 6.0, 0, 1, 1, 1, 3);
        }

    // -------------------------------------------------------------
    // CATEGORY: BOWLS
    // -------------------------------------------------------------
    } elseif ($slug === 'bowls') {
        addVariant($db, $id, 'Standard Portion', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Power / Large Bowl', 60.0, 150, 12.0, 18.0, 4.0, 1.0, 0, 2);

        // Group 1: Base Grain
        addCustom($db, $id, '1. Base Grain', 'Steamed Brown Rice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Base Grain', 'Organic Tricolor Quinoa', 30.0, 40, 3.0, 8.0, 1.0, 0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '1. Base Grain', 'Riced Cauliflower (Low-Carb)', 35.0, -110, 2.0, -20.0, -1.0, 0, 0, 1, 1, 1, 3);
        addCustom($db, $id, '1. Base Grain', 'Fresh Salad Greens Base', 0.0, -80, 1.0, -16.0, -1.0, 0, 0, 1, 1, 1, 4);

        // Group 2: Protein Add-on (Dish specific!)
        if (str_contains($name, 'Chicken')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Grilled Herb Chicken Breast (80g)', 65.0, 110, 18.0, 0.5, 3.0, 0, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Protein Boost', 'Soft-Boiled Farm Egg', 25.0, 70, 6.0, 0.5, 5.0, 0, 0, 0, 0, 1, 2);
        } elseif (str_contains($name, 'Salmon')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Pan-Seared Salmon Fillet (70g)', 95.0, 140, 16.0, 0, 8.0, 0, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Protein Boost', 'Soft-Boiled Farm Egg', 25.0, 70, 6.0, 0.5, 5.0, 0, 0, 0, 0, 1, 2);
        } elseif (str_contains($name, 'Paneer')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Tandoori Grilled Paneer (70g)', 45.0, 90, 9.0, 2.0, 6.0, 0, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Protein Boost', 'Crispy Spiced Tofu Cubes (60g)', 40.0, 70, 7.0, 2.0, 4.0, 0, 0, 0, 0, 1, 2);
        } else { // Buddha bowl / vegan
            addCustom($db, $id, '2. Protein Boost', 'Crispy Golden Tofu Cubes (80g)', 40.0, 75, 8.0, 3.0, 4.0, 0, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Protein Boost', 'Steamed Edamame Beans (60g)', 35.0, 60, 6.0, 4.0, 2.5, 0, 0, 0, 0, 1, 2);
        }

        // Group 3: Sauce / Dressing
        addCustom($db, $id, '3. Signature Dressing', 'Mint Greek Yogurt Dressing', 0.0, 25, 2.0, 2.0, 1.0, 1.0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '3. Signature Dressing', 'Citrus Herb Vinaigrette', 0.0, 35, 0.2, 1.5, 3.5, 0.5, 0, 1, 1, 1, 2);
        addCustom($db, $id, '3. Signature Dressing', 'Spicy Green Coriander Chutney', 0.0, 15, 0.5, 2.0, 0.2, 0.5, 0, 1, 1, 1, 3);
        addCustom($db, $id, '3. Signature Dressing', 'Toasted Sesame Tahini', 20.0, 45, 1.5, 2.0, 4.0, 0.2, 0, 1, 1, 1, 4);

        // Group 4: Gourmet Toppings & Crunch (Max 3)
        addCustom($db, $id, '4. Toppings & Crunch', 'Toasted Pumpkin & Chia Seeds', 25.0, 40, 2.0, 1.5, 3.5, 0, 0, 0, 0, 3, 1);
        addCustom($db, $id, '4. Toppings & Crunch', 'Fresh Hass Avocado Slices', 50.0, 75, 1.0, 3.5, 7.0, 0.5, 0, 0, 0, 3, 2);
        addCustom($db, $id, '4. Toppings & Crunch', 'Sautéed Garlic Herb Mushrooms', 35.0, 30, 1.5, 2.5, 1.5, 0.5, 0, 0, 0, 3, 3);
        addCustom($db, $id, '4. Toppings & Crunch', 'Pickled Ruby Beetroot & Cucumbers', 15.0, 15, 0.5, 3.0, 0, 1.5, 0, 0, 0, 3, 4);
        addCustom($db, $id, '4. Toppings & Crunch', 'Crumbled Artisanal Feta', 35.0, 45, 3.5, 0.5, 3.5, 0.2, 0, 0, 0, 3, 5);

    // -------------------------------------------------------------
    // CATEGORY: WRAPS
    // -------------------------------------------------------------
    } elseif ($slug === 'wraps') {
        addVariant($db, $id, 'Single Wrap', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Wrap + Farm Salad Combo', 70.0, 90, 4.0, 8.0, 4.5, 2.0, 0, 2);

        // Group 1: Tortilla Base
        addCustom($db, $id, '1. Tortilla Choice', 'Whole Wheat Hand-Rolled Roti', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Tortilla Choice', 'Spinach & Garden Herb Tortilla', 15.0, 10, 1.0, 2.0, 0, 0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '1. Tortilla Choice', 'Crisp Romaine Lettuce Wrap (Low-Carb)', 0.0, -120, -1.0, -24.0, -1.0, 0, 0, 1, 1, 1, 3);
        addCustom($db, $id, '1. Tortilla Choice', 'Multigrain Seeded Flatbread', 20.0, 25, 3.0, 2.0, 1.0, 0, 0, 1, 1, 1, 4);

        // Group 2: Protein Boost (Dish specific!)
        if (str_contains($name, 'Chicken')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Smoked Chicken Breast (60g)', 60.0, 100, 16.0, 0, 3.0, 0, 0, 0, 0, 1, 1);
        } elseif (str_contains($name, 'Paneer')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Grilled Cottage Cheese (50g)', 45.0, 85, 8.0, 2.0, 5.5, 0, 0, 0, 0, 1, 1);
        } elseif (str_contains($name, 'Egg')) {
            addCustom($db, $id, '2. Protein Boost', 'Double Farm Egg Whites', 35.0, 45, 10.0, 0.5, 0.2, 0, 0, 0, 0, 1, 1);
        } elseif (str_contains($name, 'Falafel')) {
            addCustom($db, $id, '2. Protein Boost', 'Extra Baked Herb Falafel Patty', 35.0, 75, 4.0, 9.0, 3.0, 0.5, 0, 0, 0, 1, 1);
        } else { // Tofu
            addCustom($db, $id, '2. Protein Boost', 'Extra Griddled Spiced Tofu', 40.0, 60, 7.0, 2.0, 3.0, 0, 0, 0, 0, 1, 1);
        }

        // Group 3: Sauce / Spread
        addCustom($db, $id, '3. Gourmet Spread', 'Roasted Garlic Chickpea Hummus', 0.0, 40, 2.0, 4.0, 2.5, 0.5, 0, 1, 1, 1, 1);
        addCustom($db, $id, '3. Gourmet Spread', 'Chipotle Greek Yogurt Sauce', 0.0, 30, 2.5, 1.5, 1.5, 1.0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '3. Gourmet Spread', 'Fresh Sweet Basil Pesto', 25.0, 55, 1.5, 1.0, 5.5, 0, 0, 1, 1, 1, 3);
        addCustom($db, $id, '3. Gourmet Spread', 'Mint Coriander Yogurt Chutney', 0.0, 15, 1.0, 1.5, 0.5, 0.5, 0, 1, 1, 1, 4);

        // Group 4: Add-ins & Crunch
        addCustom($db, $id, '4. Wrap Add-ins', 'Caramelized Sweet Bell Peppers', 20.0, 20, 0.5, 4.0, 0.5, 2.0, 0, 0, 0, 3, 1);
        addCustom($db, $id, '4. Wrap Add-ins', 'Fresh Hass Avocado Slices', 45.0, 60, 1.0, 3.0, 5.5, 0.5, 0, 0, 0, 3, 2);
        addCustom($db, $id, '4. Wrap Add-ins', 'Pickled Jalapeño Rings', 10.0, 5, 0.2, 1.0, 0, 0.2, 0, 0, 0, 3, 3);
        addCustom($db, $id, '4. Wrap Add-ins', 'Crisp Baby Arugula & Microgreens', 15.0, 10, 0.8, 1.0, 0.1, 0, 0, 0, 0, 3, 4);

    // -------------------------------------------------------------
    // CATEGORY: SALADS
    // -------------------------------------------------------------
    } elseif ($slug === 'salads') {
        addVariant($db, $id, 'Standard Salad Bowl', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Large / Sharing Bowl', 65.0, 120, 8.0, 10.0, 6.0, 3.0, 0, 2);

        // Group 1: Dressing Style
        addCustom($db, $id, '1. Dressing Style', 'Tossed In Fresh', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Dressing Style', 'Served on the Side', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '1. Dressing Style', 'Light Dressing (Half Pour)', 0.0, -35, 0, -1.0, -3.5, -0.5, 0, 1, 1, 1, 3);

        // Group 2: Choice of Dressing
        addCustom($db, $id, '2. Choice of Dressing', 'Lemon Oregano Cold-Pressed Olive Oil', 0.0, 60, 0, 1.0, 6.5, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '2. Choice of Dressing', 'Greek Yogurt Parmesan Caesar', 0.0, 45, 3.0, 2.0, 3.0, 1.0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '2. Choice of Dressing', 'Aged Modena Balsamic Reduction', 0.0, 35, 0.2, 7.0, 0.2, 6.0, 0, 1, 1, 1, 3);
        addCustom($db, $id, '2. Choice of Dressing', 'Honey Citrus Dijon Vinaigrette', 0.0, 40, 0.5, 4.0, 2.5, 3.0, 0, 1, 1, 1, 4);

        // Group 3: Protein Add-on
        addCustom($db, $id, '3. Protein Add-on', 'Sliced Grilled Herb Chicken (80g)', 70.0, 120, 20.0, 0.5, 3.0, 0, 0, 0, 0, 1, 1);
        addCustom($db, $id, '3. Protein Add-on', 'Golden Pan-Seared Tofu (80g)', 45.0, 70, 8.0, 2.0, 3.5, 0, 0, 0, 0, 1, 2);
        addCustom($db, $id, '3. Protein Add-on', 'Authentic Greek Feta Crumbles', 40.0, 65, 5.0, 1.0, 5.0, 0.5, 0, 0, 0, 1, 3);
        addCustom($db, $id, '3. Protein Add-on', 'Soft-Boiled Farm Egg', 25.0, 70, 6.0, 0.5, 5.0, 0, 0, 0, 0, 1, 4);

        // Group 4: Toppings & Seeds (Max 3)
        addCustom($db, $id, '4. Seeds & Crunch', 'Toasted California Walnut Halves', 35.0, 65, 1.5, 1.5, 6.5, 0.5, 0, 0, 0, 3, 1);
        addCustom($db, $id, '4. Seeds & Crunch', 'Roasted Pumpkin & Sunflower Seeds', 25.0, 45, 2.0, 1.5, 4.0, 0, 0, 0, 0, 3, 2);
        addCustom($db, $id, '4. Seeds & Crunch', 'Artisan Whole Wheat Herb Croutons', 20.0, 35, 1.0, 6.5, 0.5, 0.5, 0, 0, 0, 3, 3);
        addCustom($db, $id, '4. Seeds & Crunch', 'Ruby Pomegranate Pearls', 25.0, 25, 0.5, 5.5, 0.2, 4.0, 0, 0, 0, 3, 4);
        addCustom($db, $id, '4. Seeds & Crunch', 'Fresh Hass Avocado Chunks', 50.0, 70, 1.0, 3.5, 6.5, 0.5, 0, 0, 0, 3, 5);

    // -------------------------------------------------------------
    // CATEGORY: SOUPS
    // -------------------------------------------------------------
    } elseif ($slug === 'soups') {
        addVariant($db, $id, 'Regular Cup (280ml)', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Hearty Large Bowl (420ml)', 45.0, 60, 4.0, 7.0, 1.5, 1.0, 0, 2);

        // Group 1: Bread & Accompaniment
        addCustom($db, $id, '1. Bread Accompaniment', 'Warm Sourdough Toast Slice', 0.0, 75, 3.0, 14.0, 0.8, 0.5, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Bread Accompaniment', 'Whole Wheat Garlic Toast', 20.0, 85, 3.0, 13.0, 2.5, 0.5, 0, 1, 1, 1, 2);
        addCustom($db, $id, '1. Bread Accompaniment', 'Crispy Oven-Baked Herb Croutons', 0.0, 45, 1.5, 8.0, 1.0, 0.5, 0, 1, 1, 1, 3);
        addCustom($db, $id, '1. Bread Accompaniment', 'No Bread (Low Carb)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 4);

        // Group 2: Soup Boost
        if (str_contains($name, 'Chicken')) {
            addCustom($db, $id, '2. Soup Enrichment', 'Extra Shredded Chicken Breast (50g)', 45.0, 65, 12.0, 0, 1.5, 0, 0, 0, 0, 1, 1);
        } else {
            addCustom($db, $id, '2. Soup Enrichment', 'Cannellini White Beans (+6g P)', 30.0, 50, 4.0, 8.0, 0.5, 0.5, 0, 0, 0, 1, 1);
            addCustom($db, $id, '2. Soup Enrichment', 'Sautéed Wild Mushroom Medley', 35.0, 30, 1.5, 2.0, 1.5, 0.5, 0, 0, 0, 1, 2);
        }

        // Group 3: Seasoning & Herb Finishes
        addCustom($db, $id, '3. Gourmet Finishes', 'Extra Virgin Olive Oil Swirl', 20.0, 40, 0, 0, 4.5, 0, 0, 0, 0, 2, 1);
        addCustom($db, $id, '3. Gourmet Finishes', 'Cracked Malabar Peppercorn & Lemon', 0.0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2);
        addCustom($db, $id, '3. Gourmet Finishes', 'Toasted Almond Flakes', 25.0, 35, 1.2, 1.0, 3.0, 0.2, 0, 0, 0, 2, 3);
        addCustom($db, $id, '3. Gourmet Finishes', 'Fresh Italian Sweet Basil Chiffonade', 0.0, 2, 0.1, 0.3, 0, 0, 0, 0, 0, 2, 4);

    // -------------------------------------------------------------
    // CATEGORY: APPETIZERS
    // -------------------------------------------------------------
    } elseif ($slug === 'appetizers') {
        addVariant($db, $id, 'Standard Plate', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Sharing / Double Plate', 80.0, 140, 16.0, 6.0, 5.0, 1.0, 0, 2);

        // Group 1: Spice & Heat Level
        addCustom($db, $id, '1. Spice Level', 'Mild Herb Seasoned', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Spice Level', 'Medium Spiced', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '1. Spice Level', 'Fiery Roasted Chili Heat', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3);

        // Group 2: Accompaniment Dip
        addCustom($db, $id, '2. Accompaniment Dip', 'Garlic Hung Curd Dip', 0.0, 35, 2.5, 1.5, 2.0, 0.5, 0, 1, 1, 1, 1);
        addCustom($db, $id, '2. Accompaniment Dip', 'Sweet Chili Tamari Glaze', 0.0, 30, 0.5, 6.0, 0.2, 4.0, 0, 1, 1, 1, 2);
        addCustom($db, $id, '2. Accompaniment Dip', 'Mint Coriander Yogurt Chutney', 0.0, 25, 1.5, 1.5, 1.0, 0.5, 0, 1, 1, 1, 3);
        addCustom($db, $id, '2. Accompaniment Dip', 'Roasted Sesame Tahini Dip', 20.0, 45, 1.5, 2.0, 4.0, 0.2, 0, 1, 1, 1, 4);

        // Group 3: Finishing Toppings
        addCustom($db, $id, '3. Finishing Garnish', 'Toasted Sesame & Charred Scallions', 20.0, 25, 1.0, 1.0, 2.0, 0.2, 0, 0, 0, 2, 1);
        addCustom($db, $id, '3. Finishing Garnish', 'Crumbled Greek Feta Sprinkle', 35.0, 45, 3.5, 0.5, 3.5, 0.2, 0, 0, 0, 2, 2);
        addCustom($db, $id, '3. Finishing Garnish', 'Charred Lemon Wedge & Chaat Dust', 0.0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 3);

    // -------------------------------------------------------------
    // CATEGORY: DESSERTS
    // -------------------------------------------------------------
    } elseif ($slug === 'desserts') {
        addVariant($db, $id, 'Single Serving', 0.0, 0, 0, 0, 0, 0, 0, 1);
        addVariant($db, $id, 'Sharing Portion', 55.0, 110, 8.0, 14.0, 4.0, 5.0, 0, 2);

        // Group 1: Serving Style / Temperature
        addCustom($db, $id, '1. Serving Style', 'Gently Warmed (Fresh from Oven)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
        addCustom($db, $id, '1. Serving Style', 'Served Chilled (Dense Texture)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);

        // Group 2: Gourmet Drizzle
        addCustom($db, $id, '2. Gourmet Drizzle', 'Zero-Cal Sugar-Free Dark Chocolate Drizzle', 20.0, 20, 0.5, 3.0, 0.8, 0, 0, 0, 0, 1, 1);
        addCustom($db, $id, '2. Gourmet Drizzle', 'Wildberry Berry Compote', 30.0, 25, 0.2, 6.0, 0.1, 3.5, 0, 0, 0, 1, 2);
        addCustom($db, $id, '2. Gourmet Drizzle', 'Raw Himalayan Honey Drops', 20.0, 30, 0, 7.5, 0, 7.0, 0, 0, 0, 1, 3);

        // Group 3: Nut & Seed Crunch (Max 2)
        addCustom($db, $id, '3. Crunchy Toppings', 'Roasted Almond Flakes', 25.0, 45, 1.8, 1.5, 4.0, 0.3, 0, 0, 0, 2, 1);
        addCustom($db, $id, '3. Crunchy Toppings', 'Toasted Pistachio Crumbs', 30.0, 50, 2.0, 2.0, 4.2, 0.5, 0, 0, 0, 2, 2);
        addCustom($db, $id, '3. Crunchy Toppings', 'Buckwheat Granola Crunch', 25.0, 40, 1.5, 6.0, 1.2, 1.0, 0, 0, 0, 2, 3);

        // Group 4: A la Mode
        addCustom($db, $id, '4. A la Mode', 'Scoop of High-Protein Vanilla Ice Cream', 50.0, 80, 10.0, 8.0, 2.0, 3.0, 0, 0, 0, 1, 1);

    // -------------------------------------------------------------
    // CATEGORY: MAIN MEALS
    // -------------------------------------------------------------
    } elseif ($slug === 'main-meals') {
        if (str_contains($name, 'Pizza')) {
            addVariant($db, $id, 'Personal 8-inch', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Sharing 11-inch', 90.0, 280, 16.0, 34.0, 8.0, 2.0, 0, 2);

            // Group 1: Crust Type
            addCustom($db, $id, '1. Artisan Crust', 'Slow-Fermented Sourdough Crust', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Artisan Crust', 'High-Protein Whole Wheat Crust', 35.0, 20, 6.0, 2.0, 0.5, 0, 0, 1, 1, 1, 2);

            // Group 2: Sauce & Cheese
            addCustom($db, $id, '2. Sauce & Cheese', 'San Marzano Tomato & Light Mozzarella', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Sauce & Cheese', 'Basil Cashew Pesto & Buffalo Mozzarella', 45.0, 60, 4.0, 2.0, 5.0, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Sauce & Cheese', 'Vegan Nut Cheese (Dairy-Free)', 40.0, 20, 2.0, 3.0, 2.0, 0, 0, 1, 1, 1, 3);

            // Group 3: Gourmet Toppings (Max 3)
            addCustom($db, $id, '3. Gourmet Toppings', 'Charred Kalamata Olives', 25.0, 30, 0.5, 1.0, 3.0, 0, 0, 0, 0, 3, 1);
            addCustom($db, $id, '3. Gourmet Toppings', 'Roasted Portobello Mushrooms', 35.0, 25, 1.5, 2.0, 1.0, 0.5, 0, 0, 0, 3, 2);
            addCustom($db, $id, '3. Gourmet Toppings', 'Grilled Bell Peppers & Sweet Corn', 0.0, 15, 0.5, 3.0, 0.2, 1.0, 0, 0, 0, 3, 3);
            addCustom($db, $id, '3. Gourmet Toppings', 'Sliced Pickled Jalapeños', 0.0, 5, 0.2, 1.0, 0, 0.2, 0, 0, 0, 3, 4);
            addCustom($db, $id, '3. Gourmet Toppings', 'Extra Herb Paneer Cubes (+7g P)', 45.0, 70, 7.0, 1.5, 4.5, 0.5, 0, 0, 0, 3, 5);

        } elseif (str_contains($name, 'Burger')) {
            addVariant($db, $id, 'Single Patty Burger', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Double Protein Patty', 85.0, 170, 28.0, 1.0, 6.0, 0, 0, 2);

            // Group 1: Bun Choice
            addCustom($db, $id, '1. Bun Choice', 'Whole Wheat Brioche Bun', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Bun Choice', 'Multigrain Seeded Bun', 20.0, 15, 2.5, 1.0, 1.0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Bun Choice', 'Crisp Iceberg Lettuce Wrap (Low-Carb)', 0.0, -130, -1.0, -24.0, -1.5, 0, 0, 1, 1, 1, 3);

            // Group 2: Sauce Choice
            addCustom($db, $id, '2. Sauce Choice', 'Chipotle Greek Yogurt Spread', 0.0, 30, 2.0, 1.5, 1.5, 0.8, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Sauce Choice', 'Mint Coriander Light Mayo', 0.0, 25, 0.5, 1.0, 2.2, 0.4, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Sauce Choice', 'Whole Grain Dijon Mustard', 0.0, 15, 0.5, 1.0, 0.5, 0.2, 0, 1, 1, 1, 3);

            // Group 3: Gourmet Add-ons (Max 3)
            addCustom($db, $id, '3. Gourmet Add-ons', 'Low-Fat Aged Cheddar Slice', 30.0, 50, 5.0, 0.5, 3.5, 0, 0, 0, 0, 3, 1);
            addCustom($db, $id, '3. Gourmet Add-ons', 'Caramelized Sweet Onions', 15.0, 20, 0.5, 4.0, 0.5, 2.5, 0, 0, 0, 3, 2);
            addCustom($db, $id, '3. Gourmet Add-ons', 'Dill Pickles & Crisp Lettuce', 0.0, 5, 0.2, 1.0, 0, 0.3, 0, 0, 0, 3, 3);
            addCustom($db, $id, '3. Gourmet Add-ons', 'Fresh Hass Avocado Slices', 45.0, 65, 1.0, 3.0, 6.0, 0.5, 0, 0, 0, 3, 4);

        } elseif (str_contains($name, 'Chicken Platter') || str_contains($name, 'Chicken Breast')) {
            addVariant($db, $id, 'Standard Platter (180g)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Power Meal (260g Double Breast)', 85.0, 180, 32.0, 1.0, 4.5, 0, 0, 2);

            // Group 1: Spice & Herb Glaze
            addCustom($db, $id, '1. Spice Marinade', 'Mild Mediterranean Herb Marinated', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Spice Marinade', 'Cracked Malabar Peppercorn & Garlic', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Spice Marinade', 'Fiery Roasted Peri-Peri Spiced', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3);

            // Group 2: Choice of Side Carbs
            addCustom($db, $id, '2. Side Choice', 'Steamed Brown Basmati Rice', 0.0, 140, 3.5, 28.0, 1.2, 0.5, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Side Choice', 'Organic Tricolor Quinoa Pilaf', 35.0, 120, 4.5, 22.0, 2.0, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Side Choice', 'Roasted Rosemary Baby Potatoes', 30.0, 110, 2.5, 22.0, 1.8, 1.0, 0, 1, 1, 1, 3);
            addCustom($db, $id, '2. Side Choice', 'Double Charred Garden Vegetables (Low-Carb)', 0.0, -80, 2.0, -18.0, -0.5, 0, 0, 1, 1, 1, 4);

            // Group 3: Signature Sauce
            addCustom($db, $id, '3. Signature Sauce', 'Rosemary Garlic Reduction Jus', 0.0, 40, 1.0, 3.0, 2.5, 0.5, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Signature Sauce', 'Smoked Tomato BBQ Relish', 0.0, 45, 0.8, 7.0, 0.5, 4.0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '3. Signature Sauce', 'Creamy Greek Yogurt Dijonnaise', 0.0, 35, 2.0, 2.0, 2.0, 1.0, 0, 1, 1, 1, 3);

            // Group 4: Gourmet Extras (Max 2)
            addCustom($db, $id, '4. Gourmet Sides', 'Sautéed Garlic Portobello Mushrooms', 35.0, 30, 1.5, 2.0, 1.5, 0.5, 0, 0, 0, 2, 1);
            addCustom($db, $id, '4. Gourmet Sides', 'Fresh Hass Avocado Half', 50.0, 80, 1.2, 4.0, 7.5, 0.5, 0, 0, 0, 2, 2);
            addCustom($db, $id, '4. Gourmet Sides', 'Steamed Broccoli & Zucchini Medley', 30.0, 25, 2.0, 4.0, 0.5, 1.5, 0, 0, 0, 2, 3);

        } elseif (str_contains($name, 'Paneer Steak')) {
            addVariant($db, $id, 'Standard Steak (160g Paneer)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'Power Steak (240g Large Portion)', 70.0, 160, 18.0, 3.0, 11.0, 1.0, 0, 2);

            // Group 1: Spice Marinade
            addCustom($db, $id, '1. Spice Level', 'Herb & Extra Virgin Olive Oil Grilled', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Spice Level', 'Degi Mirch & Kasuri Methi Tandoori', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Spice Level', 'Crushed Peppercorn & Lemon Glazed', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3);

            // Group 2: Side Grain / Base
            addCustom($db, $id, '2. Side Choice', 'Steamed Quinoa & Wilted Spinach', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Side Choice', 'Brown Basmati Herb Pilaf', 20.0, 130, 3.5, 26.0, 1.2, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Side Choice', 'Mashed Spiced Sweet Potatoes', 30.0, 110, 2.0, 24.0, 1.0, 3.5, 0, 1, 1, 1, 3);
            addCustom($db, $id, '2. Side Choice', 'Charred Broccoli & Asparagus Medley', 35.0, 40, 3.0, 5.0, 1.0, 1.5, 0, 1, 1, 1, 4);

            // Group 3: Gourmet Glaze
            addCustom($db, $id, '3. Gourmet Glaze', 'Aged Balsamic Reduction', 0.0, 30, 0.2, 6.0, 0.2, 5.0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Gourmet Glaze', 'Creamy Herb Cashew Pesto', 30.0, 60, 2.0, 2.0, 5.5, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '3. Gourmet Glaze', 'Tangy Tomato Basil Relish', 0.0, 25, 0.8, 4.0, 0.5, 2.0, 0, 1, 1, 1, 3);

            // Group 4: Protein & Topping Add-on (Max 2)
            addCustom($db, $id, '4. Extra Add-on', 'Extra Grilled Paneer Slice (60g)', 45.0, 90, 8.0, 1.5, 6.5, 0.5, 0, 0, 0, 2, 1);
            addCustom($db, $id, '4. Extra Add-on', 'Toasted Pine Nuts & Seeds', 35.0, 50, 1.8, 1.5, 4.5, 0.5, 0, 0, 0, 2, 2);
            addCustom($db, $id, '4. Extra Add-on', 'Sautéed Garlic Button Mushrooms', 35.0, 30, 1.5, 2.0, 1.5, 0.5, 0, 0, 0, 2, 3);

        } elseif (str_contains($name, 'Salmon')) {
            addVariant($db, $id, 'Standard Fillet (150g)', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'King Cut Fillet (220g)', 110.0, 170, 24.0, 0, 8.5, 0, 0, 2);

            // Group 1: Doneness
            addCustom($db, $id, '1. Doneness', 'Pan-Seared Medium (Juicy)', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Doneness', 'Crisp Skin Well-Done', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2);

            // Group 2: Side Choice
            addCustom($db, $id, '2. Side Choice', 'Steamed Asparagus & Baby Greens', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Side Choice', 'Organic Tricolor Quinoa', 30.0, 120, 4.0, 22.0, 2.0, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Side Choice', 'Herb Roasted Baby Potatoes', 30.0, 110, 2.5, 22.0, 1.8, 1.0, 0, 1, 1, 1, 3);

            // Group 3: Sauce Choice
            addCustom($db, $id, '3. Sauce Choice', 'Lemon Dill Greek Yogurt Dip', 0.0, 30, 2.0, 2.0, 1.5, 1.0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '3. Sauce Choice', 'Kalamata Caper Olive Tapenade', 25.0, 40, 0.5, 1.5, 4.0, 0, 0, 1, 1, 1, 2);

            // Group 4: Finishing Touch (Max 2)
            addCustom($db, $id, '4. Finishing Touch', 'Charred Lime & Sea Salt Flakes', 0.0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 1);
            addCustom($db, $id, '4. Finishing Touch', 'Fresh Hass Avocado Slices', 50.0, 75, 1.0, 3.5, 7.0, 0.5, 0, 0, 0, 2, 2);

        } elseif (str_contains($name, 'Tofu & Broccoli')) {
            addVariant($db, $id, 'Standard Sauté Bowl', 0.0, 0, 0, 0, 0, 0, 0, 1);
            addVariant($db, $id, 'High-Protein Bowl', 60.0, 120, 14.0, 8.0, 4.0, 1.5, 0, 2);

            // Group 1: Stir-Fry Sauce
            addCustom($db, $id, '1. Stir-Fry Glaze', 'Ginger Garlic Tamari Sauce', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '1. Stir-Fry Glaze', 'Spicy Szechuan Peppercorn Sauce', 0.0, 10, 0.5, 2.0, 0.2, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '1. Stir-Fry Glaze', 'Toasted Sesame Teriyaki Reduction', 15.0, 25, 0.5, 5.0, 0.5, 3.5, 0, 1, 1, 1, 3);

            // Group 2: Base Grain
            addCustom($db, $id, '2. Base Grain', 'Brown Jasmine Steamed Rice', 0.0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1);
            addCustom($db, $id, '2. Base Grain', 'Soba Buckwheat Noodles', 30.0, 30, 2.0, 6.0, 0.5, 0.5, 0, 1, 1, 1, 2);
            addCustom($db, $id, '2. Base Grain', 'Riced Cauliflower (Low-Carb)', 35.0, -100, 1.5, -18.0, -1.0, 0, 0, 1, 1, 1, 3);

            // Group 3: Extra Add-ons (Max 2)
            addCustom($db, $id, '3. Extra Add-ons', 'Extra Crispy Non-GMO Tofu Cubes', 40.0, 70, 8.0, 2.0, 3.5, 0, 0, 0, 0, 2, 1);
            addCustom($db, $id, '3. Extra Add-ons', 'Steamed Edamame Beans (+5g P)', 30.0, 50, 5.0, 3.5, 2.0, 0, 0, 0, 0, 2, 2);
            addCustom($db, $id, '3. Extra Add-ons', 'Toasted White & Black Sesame Seeds', 15.0, 25, 1.0, 1.0, 2.0, 0, 0, 0, 0, 2, 3);
        }
    }
}

$db->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "All item-specific variants and customizations seeded successfully!\n";
$varCount = $db->query('SELECT COUNT(*) FROM food_variants')->fetchColumn();
$custCount = $db->query('SELECT COUNT(*) FROM food_customizations')->fetchColumn();
echo "New Totals: {$varCount} variants, {$custCount} customizations across all dishes.\n";
