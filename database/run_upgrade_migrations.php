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
    if (file_exists($file)) require_once $file;
});

require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

\App\Core\Env::load(HB_ROOT . '/.env');

$pdo = \App\Core\Database::getConnection();

echo "Starting migration & seed upgrade...\n";

// 1. Setup All 8 Categories
$categories = [
    ['name' => 'Main Meals', 'slug' => 'main-meals', 'description' => 'Wholesome and delicious meals to fuel your day', 'sort_order' => 1],
    ['name' => 'Bowls', 'slug' => 'bowls', 'description' => 'Nutrient-packed warm and cold signature protein bowls', 'sort_order' => 2],
    ['name' => 'Wraps', 'slug' => 'wraps', 'description' => 'Whole-wheat high-protein roll-ups', 'sort_order' => 3],
    ['name' => 'Salads', 'slug' => 'salads', 'description' => 'Fresh organic greens and antioxidant rich salads', 'sort_order' => 4],
    ['name' => 'Soups', 'slug' => 'soups', 'description' => 'Hearty protein-infused broths and vegetable soups', 'sort_order' => 5],
    ['name' => 'Appetizers', 'slug' => 'appetizers', 'description' => 'Light, guilt-free starters and grilled small plates', 'sort_order' => 6],
    ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Clean functional protein shakes and cold-pressed juices', 'sort_order' => 7],
    ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Guilt-free unrefined sweet treats', 'sort_order' => 8],
];

$catMap = [];
$catStmt = $pdo->prepare("SELECT id FROM categories WHERE restaurant_id = 1 AND slug = ?");
$insertCatStmt = $pdo->prepare("INSERT INTO categories (restaurant_id, name, slug, description, sort_order, status) VALUES (1, ?, ?, ?, ?, 'active')");
$updateCatStmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, sort_order = ? WHERE id = ?");

foreach ($categories as $cat) {
    $catStmt->execute([$cat['slug']]);
    $existing = $catStmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        $updateCatStmt->execute([$cat['name'], $cat['description'], $cat['sort_order'], $existing['id']]);
        $catMap[$cat['slug']] = (int)$existing['id'];
    } else {
        $insertCatStmt->execute([$cat['name'], $cat['slug'], $cat['description'], $cat['sort_order']]);
        $catMap[$cat['slug']] = (int)$pdo->lastInsertId();
    }
}
echo "✓ Categories synchronized (Total: " . count($catMap) . ").\n";

// 3. Define 5+ items for every category
$foodCatalog = [
    // Main Meals (At least 5)
    [
        'category_slug' => 'main-meals',
        'name' => 'Grilled Herb Chicken Platter',
        'slug' => 'grilled-herb-chicken-platter',
        'description' => 'Tender herb-marinated chicken breast served with steamed brown rice and roasted garden vegetables.',
        'ingredients' => 'Chicken Breast, Brown Rice, Broccoli, Zucchini, Bell Peppers, Olive Oil, Fresh Rosemary',
        'allergens' => null,
        'food_type' => 'non_veg',
        'base_price' => 289.00,
        'calories' => 540,
        'protein' => 44.0,
        'carbs' => 32.0,
        'fat' => 16.0,
        'fiber' => 6.0,
        'sugar' => 4.0,
        'sodium' => 450.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'main-meals',
        'name' => 'Paneer Steak with Quinoa & Greens',
        'slug' => 'paneer-steak-quinoa-greens',
        'description' => 'Thick-cut grilled cottage cheese steak accompanied by organic quinoa, wilted spinach, and balsamic glaze.',
        'ingredients' => 'Paneer, Quinoa, Baby Spinach, Cherry Tomatoes, Olive Oil, Balsamic Reduction',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 269.00,
        'calories' => 510,
        'protein' => 32.0,
        'carbs' => 36.0,
        'fat' => 20.0,
        'fiber' => 8.0,
        'sugar' => 5.0,
        'sodium' => 410.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'main-meals',
        'name' => 'Smoked Salmon with Steamed Asparagus',
        'slug' => 'smoked-salmon-steamed-asparagus',
        'description' => 'Wild Atlantic salmon fillet seared with dill lemon butter, served alongside fresh asparagus spears and baby potatoes.',
        'ingredients' => 'Atlantic Salmon, Tender Asparagus, Baby Potatoes, Dill, Lemon, Cold Pressed Olive Oil',
        'allergens' => 'Fish',
        'food_type' => 'non_veg',
        'base_price' => 349.00,
        'calories' => 490,
        'protein' => 42.0,
        'carbs' => 14.0,
        'fat' => 18.0,
        'fiber' => 4.0,
        'sugar' => 2.0,
        'sodium' => 480.0,
        'caffeine' => null,
        'dietary_badge' => 'Chef\'s Special',
        'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'main-meals',
        'name' => 'Tofu & Broccoli Stir-Fry Meal',
        'slug' => 'tofu-broccoli-stir-fry-meal',
        'description' => 'Crisp organic tofu cubes tossed in a light ginger-tamari glaze with crunchy florets and red quinoa.',
        'ingredients' => 'Organic Firm Tofu, Broccoli, Snap Peas, Red Quinoa, Ginger, Tamari, Sesame Seeds',
        'allergens' => 'Soy, Sesame',
        'food_type' => 'vegan',
        'base_price' => 239.00,
        'calories' => 390,
        'protein' => 26.0,
        'carbs' => 42.0,
        'fat' => 12.0,
        'fiber' => 7.0,
        'sugar' => 4.0,
        'sodium' => 380.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'main-meals',
        'name' => 'Rosemary Garlic Chicken Breast',
        'slug' => 'rosemary-garlic-chicken-breast',
        'description' => 'Slow-grilled lean chicken breast seasoned with mountain rosemary, crushed garlic, and steamed baby greens.',
        'ingredients' => 'Skinless Chicken Breast, Garlic, Rosemary, Olive Oil, French Beans, Carrots',
        'allergens' => null,
        'food_type' => 'non_veg',
        'base_price' => 279.00,
        'calories' => 460,
        'protein' => 48.0,
        'carbs' => 18.0,
        'fat' => 12.0,
        'fiber' => 4.0,
        'sugar' => 2.0,
        'sodium' => 420.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Bowls (Matching reference image items!)
    [
        'category_slug' => 'bowls',
        'name' => 'Grilled Chicken Bowl',
        'slug' => 'grilled-chicken-bowl',
        'description' => 'Lean grilled chicken, brown rice, fresh veggies, house sauce.',
        'ingredients' => 'Lean Chicken, Brown Rice, Broccoli, Carrots, House Herb Sauce',
        'allergens' => null,
        'food_type' => 'non_veg',
        'base_price' => 249.00,
        'calories' => 520,
        'protein' => 42.0,
        'carbs' => 38.0,
        'fat' => 14.0,
        'fiber' => 6.0,
        'sugar' => 3.0,
        'sodium' => 460.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'bowls',
        'name' => 'Paneer Protein Bowl',
        'slug' => 'paneer-protein-bowl',
        'description' => 'Grilled paneer, quinoa, mixed greens, tahini dressing.',
        'ingredients' => 'Grilled Paneer, Quinoa, Mixed Greens, Edamame, Tahini Dressing',
        'allergens' => 'Dairy, Sesame',
        'food_type' => 'veg',
        'base_price' => 229.00,
        'calories' => 480,
        'protein' => 28.0,
        'carbs' => 45.0,
        'fat' => 16.0,
        'fiber' => 8.0,
        'sugar' => 5.0,
        'sodium' => 420.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'bowls',
        'name' => 'Salmon Power Bowl',
        'slug' => 'salmon-power-bowl',
        'description' => 'Grilled salmon, brown rice, avocado, steamed vegetables.',
        'ingredients' => 'Atlantic Salmon, Brown Rice, Hass Avocado, Steamed Veggies, Sesame',
        'allergens' => 'Fish, Sesame',
        'food_type' => 'non_veg',
        'base_price' => 299.00,
        'calories' => 560,
        'protein' => 40.0,
        'carbs' => 42.0,
        'fat' => 18.0,
        'fiber' => 5.0,
        'sugar' => 3.0,
        'sodium' => 490.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'bowls',
        'name' => 'Quinoa Buddha Bowl',
        'slug' => 'quinoa-buddha-bowl',
        'description' => 'Roasted veggies, quinoa, chickpeas, hummus.',
        'ingredients' => 'Tricolor Quinoa, Spiced Chickpeas, Roasted Pumpkin, Beetroot Hummus',
        'allergens' => 'Sesame',
        'food_type' => 'vegan',
        'base_price' => 199.00,
        'calories' => 430,
        'protein' => 18.0,
        'carbs' => 55.0,
        'fat' => 12.0,
        'fiber' => 9.0,
        'sugar' => 4.0,
        'sodium' => 370.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'bowls',
        'name' => 'Chicken Teriyaki Bowl',
        'slug' => 'chicken-teriyaki-bowl',
        'description' => 'Teriyaki chicken, steamed rice, broccoli, sesame.',
        'ingredients' => 'Chicken Breast, Steamed Jasmine Rice, Broccoli, Low-Sugar Teriyaki Sauce',
        'allergens' => 'Soy, Sesame',
        'food_type' => 'non_veg',
        'base_price' => 249.00,
        'calories' => 510,
        'protein' => 38.0,
        'carbs' => 60.0,
        'fat' => 10.0,
        'fiber' => 4.0,
        'sugar' => 8.0,
        'sodium' => 520.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'bowls',
        'name' => 'Paneer Tikka Bowl',
        'slug' => 'paneer-tikka-bowl',
        'description' => 'Tandoori paneer, brown rice, salad, mint sauce.',
        'ingredients' => 'Tandoori Marinated Paneer, Brown Rice, Cucumber Salad, Greek Yogurt Mint Dip',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 239.00,
        'calories' => 470,
        'protein' => 26.0,
        'carbs' => 50.0,
        'fat' => 15.0,
        'fiber' => 7.0,
        'sugar' => 4.0,
        'sodium' => 430.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Wraps (At least 5)
    [
        'category_slug' => 'wraps',
        'name' => 'Grilled Paneer Wrap',
        'slug' => 'grilled-paneer-wrap',
        'description' => 'Whole wheat tortilla stuffed with grilled paneer, crunchy peppers, and mint curd dressing.',
        'ingredients' => 'Whole Wheat Tortilla, Grilled Paneer, Bell Peppers, Romaine, Mint Yogurt Sauce',
        'allergens' => 'Gluten, Dairy',
        'food_type' => 'veg',
        'base_price' => 219.00,
        'calories' => 410,
        'protein' => 22.0,
        'carbs' => 35.0,
        'fat' => 16.0,
        'fiber' => 6.0,
        'sugar' => 3.0,
        'sodium' => 380.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'wraps',
        'name' => 'Smoked Chicken Breast Wrap',
        'slug' => 'smoked-chicken-breast-wrap',
        'description' => 'Succulent lean smoked chicken breast, shredded greens, tomatoes, and light garlic spread in multigrain flatbread.',
        'ingredients' => 'Multigrain Wrap, Smoked Chicken, Lettuce, Tomatoes, Light Garlic Yogurt Dip',
        'allergens' => 'Gluten, Dairy',
        'food_type' => 'non_veg',
        'base_price' => 249.00,
        'calories' => 450,
        'protein' => 36.0,
        'carbs' => 38.0,
        'fat' => 12.0,
        'fiber' => 5.0,
        'sugar' => 2.0,
        'sodium' => 440.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'wraps',
        'name' => 'Spiced Falafel & Hummus Wrap',
        'slug' => 'spiced-falafel-hummus-wrap',
        'description' => 'Baked chickpea falafel patties with authentic tahini hummus, pickled cucumbers, and crisp iceberg.',
        'ingredients' => 'Whole Wheat Flatbread, Baked Falafel, Tahini Hummus, Pickled Cucumbers, Tomatoes',
        'allergens' => 'Gluten, Sesame',
        'food_type' => 'vegan',
        'base_price' => 199.00,
        'calories' => 420,
        'protein' => 16.0,
        'carbs' => 52.0,
        'fat' => 14.0,
        'fiber' => 8.0,
        'sugar' => 4.0,
        'sodium' => 390.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540914124281-342587941389?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'wraps',
        'name' => 'Zesty Tofu & Avocado Wrap',
        'slug' => 'zesty-tofu-avocado-wrap',
        'description' => 'Seared lemon herb tofu, creamy avocado slices, shredded purple cabbage, and chipotle lime drizzle.',
        'ingredients' => 'Seeded Tortilla, Tofu, Hass Avocado, Red Cabbage, Cilantro Lime Vinaigrette',
        'allergens' => 'Gluten, Soy',
        'food_type' => 'vegan',
        'base_price' => 229.00,
        'calories' => 380,
        'protein' => 20.0,
        'carbs' => 34.0,
        'fat' => 16.0,
        'fiber' => 7.0,
        'sugar' => 3.0,
        'sodium' => 360.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'wraps',
        'name' => 'Egg White & Spinach Protein Wrap',
        'slug' => 'egg-white-spinach-protein-wrap',
        'description' => 'Fluffy scrambled farm egg whites, baby spinach, roasted mushrooms, and feta cheese in warm whole wheat wrap.',
        'ingredients' => 'Whole Wheat Wrap, Free Range Egg Whites, Spinach, Mushrooms, Crumbled Feta',
        'allergens' => 'Gluten, Egg, Dairy',
        'food_type' => 'non_veg',
        'base_price' => 189.00,
        'calories' => 340,
        'protein' => 28.0,
        'carbs' => 32.0,
        'fat' => 8.0,
        'fiber' => 4.0,
        'sugar' => 2.0,
        'sodium' => 390.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Salads (At least 5)
    [
        'category_slug' => 'salads',
        'name' => 'Mediterranean Greek Feta Salad',
        'slug' => 'mediterranean-greek-feta-salad',
        'description' => 'Crunchy Persian cucumbers, Kalamata olives, juicy tomatoes, red onions, and Greek feta tossed in oregano dressing.',
        'ingredients' => 'Cucumbers, Cherry Tomatoes, Kalamata Olives, Greek Feta, Extra Virgin Olive Oil',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 219.00,
        'calories' => 280,
        'protein' => 12.0,
        'carbs' => 16.0,
        'fat' => 18.0,
        'fiber' => 5.0,
        'sugar' => 4.0,
        'sodium' => 460.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'salads',
        'name' => 'Warm Grilled Chicken Caesar Salad',
        'slug' => 'warm-grilled-chicken-caesar-salad',
        'description' => 'Crispy romaine hearts topped with sliced grilled chicken breast, whole grain croutons, and yogurt parmesan dressing.',
        'ingredients' => 'Romaine Lettuce, Chicken Breast, Whole Grain Croutons, Shaved Parmesan, Greek Yogurt Caesar Dressing',
        'allergens' => 'Dairy, Gluten',
        'food_type' => 'non_veg',
        'base_price' => 269.00,
        'calories' => 390,
        'protein' => 38.0,
        'carbs' => 12.0,
        'fat' => 16.0,
        'fiber' => 4.0,
        'sugar' => 2.0,
        'sodium' => 480.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'salads',
        'name' => 'Avocado & Edamame Crunch Salad',
        'slug' => 'avocado-edamame-crunch-salad',
        'description' => 'Diced buttery Hass avocado, steamed edamame beans, baby spinach, pumpkin seeds, and sesame citrus vinaigrette.',
        'ingredients' => 'Avocado, Edamame, Baby Spinach, Pumpkin Seeds, Orange Citrus Vinaigrette',
        'allergens' => 'Soy, Sesame',
        'food_type' => 'vegan',
        'base_price' => 249.00,
        'calories' => 340,
        'protein' => 15.0,
        'carbs' => 22.0,
        'fat' => 20.0,
        'fiber' => 9.0,
        'sugar' => 3.0,
        'sodium' => 310.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'salads',
        'name' => 'Roasted Beetroot & Goat Cheese Salad',
        'slug' => 'roasted-beetroot-goat-cheese-salad',
        'description' => 'Sweet oven-roasted beets, peppery arugula leaves, toasted walnuts, and crumbled artisanal goat cheese.',
        'ingredients' => 'Roasted Beetroot, Wild Arugula, Goat Cheese, California Walnuts, Honey Mustard Vinaigrette',
        'allergens' => 'Dairy, Tree Nuts',
        'food_type' => 'veg',
        'base_price' => 239.00,
        'calories' => 290,
        'protein' => 11.0,
        'carbs' => 26.0,
        'fat' => 14.0,
        'fiber' => 6.0,
        'sugar' => 12.0,
        'sodium' => 340.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'salads',
        'name' => 'Sprouted Moong & Pomegranate Salad',
        'slug' => 'sprouted-moong-pomegranate-salad',
        'description' => 'Living sprouted green mung beans, sweet ruby pomegranate pearls, diced cucumber, chaat spices, and fresh lemon.',
        'ingredients' => 'Sprouted Mung, Pomegranate, Cucumbers, Fresh Mint, Lemon Juice, Himalayan Salt',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 179.00,
        'calories' => 220,
        'protein' => 14.0,
        'carbs' => 38.0,
        'fat' => 3.0,
        'fiber' => 8.0,
        'sugar' => 6.0,
        'sodium' => 280.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Soups (At least 5)
    [
        'category_slug' => 'soups',
        'name' => 'Hearty Minestrone with Beans',
        'slug' => 'hearty-minestrone-with-beans',
        'description' => 'Italian country-style soup packed with slow-simmered tomatoes, cannellini white beans, zucchini, and aromatic herbs.',
        'ingredients' => 'San Marzano Tomatoes, Cannellini Beans, Zucchini, Carrots, Celery, Thyme',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 169.00,
        'calories' => 180,
        'protein' => 9.0,
        'carbs' => 28.0,
        'fat' => 3.0,
        'fiber' => 6.0,
        'sugar' => 4.0,
        'sodium' => 390.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'soups',
        'name' => 'Clear Chicken & Herb Broth',
        'slug' => 'clear-chicken-herb-broth',
        'description' => 'Warm nourishing bone broth made with free-range chicken, ginger, black peppercorns, scallions, and shredded chicken breast.',
        'ingredients' => 'Chicken Bone Broth, Shredded Chicken, Fresh Ginger, Scallions, Parsley',
        'allergens' => null,
        'food_type' => 'non_veg',
        'base_price' => 189.00,
        'calories' => 160,
        'protein' => 24.0,
        'carbs' => 6.0,
        'fat' => 3.0,
        'fiber' => 1.0,
        'sugar' => 1.0,
        'sodium' => 440.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'soups',
        'name' => 'Cream of Roasted Mushroom (Light)',
        'slug' => 'cream-of-roasted-mushroom-light',
        'description' => 'Button and shiitake wild mushrooms roasted to deep umami and pureed with cashew cream. Dairy free.',
        'ingredients' => 'Shiitake Mushrooms, Button Mushrooms, Cashew Cream, Vegetable Stock, Garlic, Chives',
        'allergens' => 'Tree Nuts',
        'food_type' => 'vegan',
        'base_price' => 179.00,
        'calories' => 150,
        'protein' => 6.0,
        'carbs' => 14.0,
        'fat' => 7.0,
        'fiber' => 3.0,
        'sugar' => 2.0,
        'sodium' => 360.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'soups',
        'name' => 'Broccoli & Almond Protein Soup',
        'slug' => 'broccoli-almond-protein-soup',
        'description' => 'Bright green velvety broccoli soup enriched with blanched almonds, pea protein, and crushed black pepper.',
        'ingredients' => 'Fresh Broccoli, Roasted Almonds, Pea Protein, Vegetable Broth, Black Pepper',
        'allergens' => 'Tree Nuts',
        'food_type' => 'vegan',
        'base_price' => 189.00,
        'calories' => 190,
        'protein' => 12.0,
        'carbs' => 16.0,
        'fat' => 8.0,
        'fiber' => 5.0,
        'sugar' => 3.0,
        'sodium' => 320.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'soups',
        'name' => 'Spiced Tomato Basil Soup',
        'slug' => 'spiced-tomato-basil-soup',
        'description' => 'Sweet roasted tomatoes slow simmered with Italian sweet basil leaves, roasted garlic, and a hint of smoked paprika.',
        'ingredients' => 'Roma Tomatoes, Fresh Sweet Basil, Garlic, Olive Oil, Vegetable Broth',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 159.00,
        'calories' => 130,
        'protein' => 4.0,
        'carbs' => 20.0,
        'fat' => 3.0,
        'fiber' => 4.0,
        'sugar' => 5.0,
        'sodium' => 370.0,
        'caffeine' => null,
        'dietary_badge' => 'Under 600 kcal',
        'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Appetizers (At least 5)
    [
        'category_slug' => 'appetizers',
        'name' => 'Crispy Baked Tofu Bites',
        'slug' => 'crispy-baked-tofu-bites',
        'description' => 'Oven-crisped organic tofu cubes served with house sweet chili tamari dip and spring onions.',
        'ingredients' => 'Non-GMO Tofu, Cornstarch, Tamari, Garlic Powder, Sweet Chili Dip',
        'allergens' => 'Soy',
        'food_type' => 'vegan',
        'base_price' => 179.00,
        'calories' => 210,
        'protein' => 16.0,
        'carbs' => 14.0,
        'fat' => 8.0,
        'fiber' => 3.0,
        'sugar' => 1.0,
        'sodium' => 340.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'appetizers',
        'name' => 'Grilled Chicken Skewers',
        'slug' => 'grilled-chicken-skewers',
        'description' => 'Tender cubes of chicken breast marinated in yogurt, cilantro, and spices, flame-grilled on skewers.',
        'ingredients' => 'Chicken Breast, Greek Yogurt, Fresh Cilantro, Cumin, Coriander',
        'allergens' => 'Dairy',
        'food_type' => 'non_veg',
        'base_price' => 229.00,
        'calories' => 290,
        'protein' => 36.0,
        'carbs' => 6.0,
        'fat' => 8.0,
        'fiber' => 1.0,
        'sugar' => 1.0,
        'sodium' => 410.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'appetizers',
        'name' => 'Edamame with Himalayan Pink Salt',
        'slug' => 'edamame-himalayan-pink-salt',
        'description' => 'Fresh young soybean pods steamed to tender perfection and sprinkled with coarsely ground rock salt.',
        'ingredients' => 'Green Soybean Pods, Himalayan Pink Rock Salt',
        'allergens' => 'Soy',
        'food_type' => 'vegan',
        'base_price' => 159.00,
        'calories' => 160,
        'protein' => 14.0,
        'carbs' => 11.0,
        'fat' => 5.0,
        'fiber' => 6.0,
        'sugar' => 2.0,
        'sodium' => 290.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'appetizers',
        'name' => 'Air-Fried Sweet Potato Fries',
        'slug' => 'air-fried-sweet-potato-fries',
        'description' => 'Crispy skin-on sweet potato batons air-fried with rosemary salt. Served with hung curd garlic dip.',
        'ingredients' => 'Sweet Potatoes, Olive Oil Spray, Sea Salt, Rosemary, Hung Curd Dip',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 169.00,
        'calories' => 220,
        'protein' => 3.0,
        'carbs' => 42.0,
        'fat' => 4.0,
        'fiber' => 6.0,
        'sugar' => 8.0,
        'sodium' => 310.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'appetizers',
        'name' => 'Spiced Cottage Cheese Skewers',
        'slug' => 'spiced-cottage-cheese-skewers',
        'description' => 'Firm cottage cheese cubes glazed with mustard oil, fenugreek, and tandoori spices, grilled on bamboo skewers.',
        'ingredients' => 'Paneer, Mustard Oil, Kasuri Methi, Degi Mirch, Bell Peppers',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 209.00,
        'calories' => 260,
        'protein' => 20.0,
        'carbs' => 8.0,
        'fat' => 14.0,
        'fiber' => 2.0,
        'sugar' => 2.0,
        'sodium' => 380.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Beverages (At least 5)
    [
        'category_slug' => 'beverages',
        'name' => 'Iced Protein Coffee',
        'slug' => 'iced-protein-coffee',
        'description' => 'Cold-brewed Arabica espresso blended with pure isolate whey protein, cold milk, and natural vanilla.',
        'ingredients' => 'Arabica Espresso, Whey Isolate, Low-Fat Milk, Pure Vanilla Extract',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 165.00,
        'calories' => 140,
        'protein' => 15.0,
        'carbs' => 8.0,
        'fat' => 5.0,
        'fiber' => 0.0,
        'sugar' => 4.0,
        'sodium' => 110.0,
        'caffeine' => 95.0,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'beverages',
        'name' => 'Banana Protein Smoothie',
        'slug' => 'banana-protein-smoothie',
        'description' => 'Thick creamy blend of Robusta bananas, Greek yogurt, chia seeds, and clean plant protein.',
        'ingredients' => 'Bananas, Greek Yogurt, Plant Protein, Chia Seeds, Ceylon Cinnamon',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 195.00,
        'calories' => 290,
        'protein' => 24.0,
        'carbs' => 36.0,
        'fat' => 4.0,
        'fiber' => 5.0,
        'sugar' => 18.0,
        'sodium' => 140.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'beverages',
        'name' => 'Green Detox Cold-Pressed Juice',
        'slug' => 'green-detox-cold-pressed-juice',
        'description' => 'Hydrating, raw cold-pressed mix of crisp cucumber, celery, granny smith apple, and lemon mint.',
        'ingredients' => 'Cucumber, Celery, Green Apple, Baby Spinach, Lemon, Fresh Mint',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 149.00,
        'calories' => 95,
        'protein' => 3.0,
        'carbs' => 20.0,
        'fat' => 0.0,
        'fiber' => 3.0,
        'sugar' => 11.0,
        'sodium' => 80.0,
        'caffeine' => null,
        'dietary_badge' => 'Under 600 kcal',
        'image' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'beverages',
        'name' => 'Raw Coconut Water with Chia Seeds',
        'slug' => 'raw-coconut-water-chia-seeds',
        'description' => 'Pure tender coconut water straight from coastal farms, infused with soaked organic chia seeds.',
        'ingredients' => 'Tender Coconut Water, Organic Chia Seeds',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 129.00,
        'calories' => 85,
        'protein' => 3.0,
        'carbs' => 16.0,
        'fat' => 1.0,
        'fiber' => 4.0,
        'sugar' => 8.0,
        'sodium' => 90.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'beverages',
        'name' => 'Berry Antioxidant Blast Shake',
        'slug' => 'berry-antioxidant-blast-shake',
        'description' => 'Wild blueberries, strawberries, almond milk, and whey protein isolate for post-workout recovery.',
        'ingredients' => 'Blueberries, Strawberries, Almond Milk, Whey Isolate, Stevia',
        'allergens' => 'Dairy, Tree Nuts',
        'food_type' => 'veg',
        'base_price' => 199.00,
        'calories' => 220,
        'protein' => 18.0,
        'carbs' => 28.0,
        'fat' => 3.0,
        'fiber' => 6.0,
        'sugar' => 12.0,
        'sodium' => 120.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],

    // Desserts (At least 5)
    [
        'category_slug' => 'desserts',
        'name' => 'Chocolate Protein Brownie',
        'slug' => 'chocolate-protein-brownie',
        'description' => 'Dense, decadent fudge brownie made with Dutch dark cocoa, almond flour, and whey protein. Refined sugar free.',
        'ingredients' => 'Almond Flour, Dark Cocoa, Whey Isolate, Stevia, Eggs, Coconut Oil',
        'allergens' => 'Nuts, Dairy, Egg',
        'food_type' => 'veg',
        'base_price' => 149.00,
        'calories' => 210,
        'protein' => 12.0,
        'carbs' => 20.0,
        'fat' => 9.0,
        'fiber' => 4.0,
        'sugar' => 8.0,
        'sodium' => 120.0,
        'caffeine' => null,
        'dietary_badge' => 'High Protein',
        'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'desserts',
        'name' => 'Protein Ice Cream (Vanilla/Choco)',
        'slug' => 'protein-ice-cream-vanilla-choco',
        'description' => 'Artisanal low-fat churned ice cream infused with whey isolate and organic vanilla pods.',
        'ingredients' => 'Low Fat Milk, Whey Isolate, Erythritol, Bourbon Vanilla Extract',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 180.00,
        'calories' => 180,
        'protein' => 20.0,
        'carbs' => 16.0,
        'fat' => 5.0,
        'fiber' => 2.0,
        'sugar' => 6.0,
        'sodium' => 90.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 1
    ],
    [
        'category_slug' => 'desserts',
        'name' => 'Chia Seed Pudding with Mango Puree',
        'slug' => 'chia-seed-pudding-mango-puree',
        'description' => 'Soaked black chia seeds in coconut almond milk layered with fresh seasonal Alphonso mango puree.',
        'ingredients' => 'Chia Seeds, Coconut Milk, Alphonso Mango, Cardamom, Stevia',
        'allergens' => null,
        'food_type' => 'vegan',
        'base_price' => 169.00,
        'calories' => 230,
        'protein' => 8.0,
        'carbs' => 26.0,
        'fat' => 10.0,
        'fiber' => 7.0,
        'sugar' => 9.0,
        'sodium' => 60.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'desserts',
        'name' => 'Baked Oat & Apple Crumble (No Sugar)',
        'slug' => 'baked-oat-apple-crumble-no-sugar',
        'description' => 'Warm spiced green apples baked with rolled oats, cinnamon, crushed almonds, and organic maple drops.',
        'ingredients' => 'Rolled Oats, Green Apples, Almond Flour, Cinnamon, Nutmeg, Maple Syrup',
        'allergens' => 'Tree Nuts, Gluten',
        'food_type' => 'vegan',
        'base_price' => 179.00,
        'calories' => 240,
        'protein' => 7.0,
        'carbs' => 38.0,
        'fat' => 6.0,
        'fiber' => 6.0,
        'sugar' => 10.0,
        'sodium' => 80.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegan',
        'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
    [
        'category_slug' => 'desserts',
        'name' => 'Greek Yogurt Parfait with Berries',
        'slug' => 'greek-yogurt-parfait-berries',
        'description' => 'Thick hung Greek yogurt layered with house-toasted buckwheat granola, fresh berries, and organic raw honey.',
        'ingredients' => 'Greek Yogurt, Buckwheat Granola, Raspberries, Blueberries, Raw Honey',
        'allergens' => 'Dairy',
        'food_type' => 'veg',
        'base_price' => 169.00,
        'calories' => 190,
        'protein' => 16.0,
        'carbs' => 22.0,
        'fat' => 3.0,
        'fiber' => 3.0,
        'sugar' => 8.0,
        'sodium' => 95.0,
        'caffeine' => null,
        'dietary_badge' => 'Vegetarian',
        'image' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=600&auto=format&fit=crop&q=80',
        'is_featured' => 0
    ],
];

// Insert/Update Foods
$foodCheckStmt = $pdo->prepare("SELECT id FROM food_items WHERE restaurant_id = 1 AND slug = ?");
$foodInsertStmt = $pdo->prepare("
    INSERT INTO food_items (
        restaurant_id, category_id, name, slug, description, ingredients, allergens,
        food_type, base_price, calories, protein, carbs, fat, fiber, sugar, sodium, caffeine,
        image, is_available, is_featured
    ) VALUES (
        1, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, 1, ?
    )
");

$foodUpdateStmt = $pdo->prepare("
    UPDATE food_items SET
        category_id = ?, name = ?, description = ?, ingredients = ?, allergens = ?,
        food_type = ?, base_price = ?, calories = ?, protein = ?, carbs = ?, fat = ?, fiber = ?, sugar = ?, sodium = ?, caffeine = ?,
        image = ?, is_available = 1, is_featured = ?
    WHERE id = ?
");

$insertedCount = 0;
$updatedCount = 0;

foreach ($foodCatalog as $item) {
    $catId = $catMap[$item['category_slug']] ?? null;
    if (!$catId) continue;

    $foodCheckStmt->execute([$item['slug']]);
    $existing = $foodCheckStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $foodUpdateStmt->execute([
            $catId, $item['name'], $item['description'], $item['ingredients'], $item['allergens'],
            $item['food_type'], $item['base_price'], $item['calories'], $item['protein'], $item['carbs'],
            $item['fat'], $item['fiber'], $item['sugar'], $item['sodium'], $item['caffeine'],
            $item['image'], $item['is_featured'], $existing['id']
        ]);
        $updatedCount++;
    } else {
        $foodInsertStmt->execute([
            $catId, $item['name'], $item['slug'], $item['description'], $item['ingredients'], $item['allergens'],
            $item['food_type'], $item['base_price'], $item['calories'], $item['protein'], $item['carbs'],
            $item['fat'], $item['fiber'], $item['sugar'], $item['sodium'], $item['caffeine'],
            $item['image'], $item['is_featured']
        ]);
        $foodId = (int)$pdo->lastInsertId();
        $insertedCount++;

        // Add standard variants (Regular, Large) for this food item
        $pdo->exec("INSERT INTO food_variants (food_item_id, name, price_adjustment, calories_adjustment, protein_adjustment, sort_order) VALUES
            ({$foodId}, 'Standard Portion', 0.00, 0, 0.0, 1),
            ({$foodId}, 'Large / Power Portion', 50.00, 110, 10.0, 2)
        ");

        // Add standard customizations (Extra Protein, Grain swap, etc.)
        $pdo->exec("INSERT INTO food_customizations (food_item_id, group_name, name, price_adjustment, calories_adjustment, protein_adjustment, is_required, min_quantity, max_quantity, sort_order) VALUES
            ({$foodId}, 'Portion Add-on', 'Extra Protein Scoop / Serving', 40.00, 90, 12.0, 0, 0, 2, 1),
            ({$foodId}, 'Portion Add-on', 'Avocado Slices', 50.00, 80, 1.5, 0, 0, 1, 2)
        ");
    }
}

echo "✓ Food catalog updated: {$insertedCount} inserted, {$updatedCount} updated.\n";

// Count per category verification
echo "\n=== VERIFICATION: FOOD ITEMS PER CATEGORY ===\n";
$report = $pdo->query("
    SELECT c.name, COUNT(f.id) as total_items
    FROM categories c
    LEFT JOIN food_items f ON f.category_id = c.id
    WHERE c.restaurant_id = 1
    GROUP BY c.id, c.name
    ORDER BY c.sort_order
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($report as $row) {
    echo "  - {$row['name']}: {$row['total_items']} items\n";
}

echo "Migration and seed upgrade finished successfully!\n";
