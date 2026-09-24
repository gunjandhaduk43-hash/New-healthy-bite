<?php
declare(strict_types=1);

// Curated distinct high-quality food photo IDs from Unsplash matching each specific dish
$imageMapping = [
    // Bowls
    1 => [
        'name' => 'Paneer Protein Bowl',
        'url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Tofu/paneer protein bowl with greens and seeds'
    ],
    2 => [
        'name' => 'Chicken Rice Bowl',
        'url' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Healthy grilled chicken with steamed rice and vegetables'
    ],
    17 => [
        'name' => 'Grilled Chicken Bowl',
        'url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Fresh vibrant salad bowl with sliced grilled chicken breast'
    ],
    18 => [
        'name' => 'Salmon Power Bowl',
        'url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Fresh power bowl with pan-seared salmon and greens'
    ],
    19 => [
        'name' => 'Quinoa Buddha Bowl',
        'url' => 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Vibrant vegan quinoa buddha bowl with chickpeas and vegetables'
    ],
    20 => [
        'name' => 'Chicken Teriyaki Bowl',
        'url' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Glazed teriyaki chicken over steamed rice bowl with sesame seeds'
    ],
    21 => [
        'name' => 'Paneer Tikka Bowl',
        'url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Tandoori spiced paneer tikka cubes with mint chutney and grain bowl'
    ],

    // Wraps
    3 => [
        'name' => 'Grilled Paneer Wrap',
        'url' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Whole-wheat grilled roll with charred paneer and crisp lettuce'
    ],
    22 => [
        'name' => 'Smoked Chicken Breast Wrap',
        'url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Sliced smoked chicken breast tightly wrapped in tortilla'
    ],
    23 => [
        'name' => 'Spiced Falafel & Hummus Wrap',
        'url' => 'https://images.unsplash.com/photo-1540914124281-342587941389?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Crispy golden falafel with creamy hummus in flatbread'
    ],
    24 => [
        'name' => 'Zesty Tofu & Avocado Wrap',
        'url' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Firm seasoned tofu and sliced avocado wrapped in spinach tortilla'
    ],
    25 => [
        'name' => 'Egg White & Spinach Protein Wrap',
        'url' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Fluffy egg whites and sautéed spinach protein breakfast wrap'
    ],

    // Main Meals
    12 => [
        'name' => 'Grilled Herb Chicken Platter',
        'url' => 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Platter of char-grilled herb-crusted chicken with roasted baby potatoes'
    ],
    13 => [
        'name' => 'Paneer Steak with Quinoa & Greens',
        'url' => 'https://images.unsplash.com/photo-1505253758473-96b3015f240a?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Thick seared paneer cottage cheese steak served over quinoa bed'
    ],
    14 => [
        'name' => 'Smoked Salmon with Steamed Asparagus',
        'url' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Pan-roasted tender salmon fillet garnished with asparagus spears'
    ],
    15 => [
        'name' => 'Tofu & Broccoli Stir-Fry Meal',
        'url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Wok-tossed organic firm tofu with bright green broccoli florets'
    ],
    16 => [
        'name' => 'Rosemary Garlic Chicken Breast',
        'url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Juicy oven-roasted chicken breast infused with fresh rosemary springs'
    ],
    48 => [
        'name' => 'Artisan Sourdough Protein Pizza',
        'url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Neapolitan sourdough artisanal pizza with tomato, basil, mozzarella'
    ],
    49 => [
        'name' => 'Grilled Lean Protein Burger',
        'url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Gourmet lean protein burger with whole-wheat bun, lettuce, and pickles'
    ],

    // Salads
    26 => [
        'name' => 'Mediterranean Greek Feta Salad',
        'url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Traditional Greek salad with feta cubes, cucumbers, olives, cherry tomatoes'
    ],
    27 => [
        'name' => 'Warm Grilled Chicken Caesar Salad',
        'url' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Classic crisp romaine Caesar salad topped with warm sliced chicken'
    ],
    28 => [
        'name' => 'Avocado & Edamame Crunch Salad',
        'url' => 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Green salad loaded with sliced ripe avocado, shelled edamame, and crunchy greens'
    ],
    29 => [
        'name' => 'Roasted Beetroot & Goat Cheese Salad',
        'url' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Ruby red roasted beetroot wedges paired with crumbly goat cheese'
    ],
    30 => [
        'name' => 'Sprouted Moong & Pomegranate Salad',
        'url' => 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Nutritious sprouted green lentils tossed with ruby pomegranate jewels'
    ],

    // Soups
    31 => [
        'name' => 'Hearty Minestrone with Beans',
        'url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Rustic Italian tomato and white bean minestrone soup with herbs'
    ],
    32 => [
        'name' => 'Clear Chicken & Herb Broth',
        'url' => 'https://images.unsplash.com/photo-1608897013039-887f21d8c804?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Golden clear steaming chicken consommé with scallions and shredded meat'
    ],
    33 => [
        'name' => 'Cream of Roasted Mushroom (Light)',
        'url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Velvety earthy wild mushroom soup with roasted thyme'
    ],
    34 => [
        'name' => 'Broccoli & Almond Protein Soup',
        'url' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=800&auto=format&fit=crop&q=80', // we will check uniqueness
        'dish' => 'Vibrant bright green broccoli purée soup garnished with toasted sliced almonds'
    ],
    35 => [
        'name' => 'Spiced Tomato Basil Soup',
        'url' => 'https://images.unsplash.com/photo-1594756202469-9ff9799b2e4e?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Silky roasted ripe tomato soup crowned with fresh aromatic basil leaf'
    ],

    // Appetizers
    36 => [
        'name' => 'Crispy Baked Tofu Bites',
        'url' => 'https://images.unsplash.com/photo-1546069901-d5bfd2cbfb1f?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Golden crispy cube-cut baked tofu served with dipping bowl'
    ],
    37 => [
        'name' => 'Grilled Chicken Skewers',
        'url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Charred spiced chicken yakitori skewers with flame grill marks'
    ],
    38 => [
        'name' => 'Edamame with Himalayan Pink Salt',
        'url' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Steamed whole edamame soybean pods dusted with coarse pink salt'
    ],
    39 => [
        'name' => 'Air-Fried Sweet Potato Fries',
        'url' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Golden crispy air-fried sweet potato batons with herb garnish'
    ],
    40 => [
        'name' => 'Spiced Cottage Cheese Skewers',
        'url' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Tandoori grilled paneer skewers with bell peppers and onions'
    ],

    // Beverages
    4 => [
        'name' => 'Iced Protein Coffee',
        'url' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Chilled glass of layered iced latte espresso with frothy foam'
    ],
    5 => [
        'name' => 'Banana Protein Smoothie',
        'url' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Creamy banana protein shake in a glass topped with banana slice'
    ],
    41 => [
        'name' => 'Green Detox Cold-Pressed Juice',
        'url' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Fresh vibrant cold-pressed green juice with cucumber, mint, and apple'
    ],
    42 => [
        'name' => 'Raw Coconut Water with Chia Seeds',
        'url' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Clear refreshing coconut water served with soaked chia seeds'
    ],
    43 => [
        'name' => 'Berry Antioxidant Blast Shake',
        'url' => 'https://images.unsplash.com/photo-1553530979-7ee52a2670c4?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Deep purple wild berry antioxidant smoothie in tall glass with fresh blueberries'
    ],

    // Desserts
    6 => [
        'name' => 'Chocolate Protein Brownie',
        'url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Decadent fudge chocolate protein brownie square with walnuts'
    ],
    7 => [
        'name' => 'Protein Ice Cream',
        'url' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Scoop of healthy high-protein ice cream with dark cacao drizzle'
    ],
    44 => [
        'name' => 'Protein Ice Cream (Vanilla/Choco)',
        'url' => 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Dual vanilla and chocolate artisanal protein gelato scoops'
    ],
    45 => [
        'name' => 'Chia Seed Pudding with Mango Puree',
        'url' => 'https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Layered glass jar of soaked chia seed pudding topped with golden mango puree'
    ],
    46 => [
        'name' => 'Baked Oat & Apple Crumble (No Sugar)',
        'url' => 'https://images.unsplash.com/photo-1568571780765-9276ac8b75a2?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Oven-baked cinnamon spiced apple crumble with rolled oat crisp crust'
    ],
    47 => [
        'name' => 'Greek Yogurt Parfait with Berries',
        'url' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&auto=format&fit=crop&q=80',
        'dish' => 'Layered glass with thick Greek yogurt, homemade granola, and fresh raspberries'
    ]
];

// Check duplicate URLs
$urls = [];
$duplicates = [];
foreach ($imageMapping as $id => $item) {
    // Extract unsplash photo ID
    preg_match('/photo-[0-9a-f-]+/', $item['url'], $m);
    $photoId = $m[0] ?? $item['url'];
    if (isset($urls[$photoId])) {
        $duplicates[] = "Photo ID {$photoId} used in #{$id} ({$item['name']}) and #{$urls[$photoId]}";
    }
    $urls[$photoId] = $id;
}

echo "Total Mapped Foods: " . count($imageMapping) . "\n";
echo "Unique Unsplash Photos: " . count($urls) . "\n";
if (!empty($duplicates)) {
    echo "DUPLICATES FOUND:\n" . implode("\n", $duplicates) . "\n";
} else {
    echo "100% UNIQUE PHOTOS VERIFIED!\n";
}
