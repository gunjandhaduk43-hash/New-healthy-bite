<?php
declare(strict_types=1);

$allUrls = [
    1 => ['name' => 'Paneer Protein Bowl', 'url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop&q=80'],
    2 => ['name' => 'Chicken Rice Bowl', 'url' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=800&auto=format&fit=crop&q=80'],
    3 => ['name' => 'Grilled Paneer Wrap', 'url' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=800&auto=format&fit=crop&q=80'],
    4 => ['name' => 'Iced Protein Coffee', 'url' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=800&auto=format&fit=crop&q=80'],
    5 => ['name' => 'Banana Protein Smoothie', 'url' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&auto=format&fit=crop&q=80'],
    6 => ['name' => 'Chocolate Protein Brownie', 'url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&auto=format&fit=crop&q=80'],
    7 => ['name' => 'Protein Ice Cream', 'url' => 'https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=800&auto=format&fit=crop&q=80'],
    12 => ['name' => 'Grilled Herb Chicken Platter', 'url' => 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=800&auto=format&fit=crop&q=80'],
    13 => ['name' => 'Paneer Steak with Quinoa & Greens', 'url' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=800&auto=format&fit=crop&q=80'],
    14 => ['name' => 'Smoked Salmon with Steamed Asparagus', 'url' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&auto=format&fit=crop&q=80'],
    15 => ['name' => 'Tofu & Broccoli Stir-Fry Meal', 'url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800&auto=format&fit=crop&q=80'],
    16 => ['name' => 'Rosemary Garlic Chicken Breast', 'url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800&auto=format&fit=crop&q=80'],
    17 => ['name' => 'Grilled Chicken Bowl', 'url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&auto=format&fit=crop&q=80'],
    18 => ['name' => 'Salmon Power Bowl', 'url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&auto=format&fit=crop&q=80'],
    19 => ['name' => 'Quinoa Buddha Bowl', 'url' => 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?w=800&auto=format&fit=crop&q=80'],
    20 => ['name' => 'Chicken Teriyaki Bowl', 'url' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=800&auto=format&fit=crop&q=80'],
    21 => ['name' => 'Paneer Tikka Bowl', 'url' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800&auto=format&fit=crop&q=80'],
    22 => ['name' => 'Smoked Chicken Breast Wrap', 'url' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&auto=format&fit=crop&q=80'],
    23 => ['name' => 'Spiced Falafel & Hummus Wrap', 'url' => 'https://images.unsplash.com/photo-1540914124281-342587941389?w=800&auto=format&fit=crop&q=80'],
    24 => ['name' => 'Zesty Tofu & Avocado Wrap', 'url' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800&auto=format&fit=crop&q=80'],
    25 => ['name' => 'Egg White & Spinach Protein Wrap', 'url' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=800&auto=format&fit=crop&q=80'],
    26 => ['name' => 'Mediterranean Greek Feta Salad', 'url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&auto=format&fit=crop&q=80'],
    27 => ['name' => 'Warm Grilled Chicken Caesar Salad', 'url' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=800&auto=format&fit=crop&q=80'],
    28 => ['name' => 'Avocado & Edamame Crunch Salad', 'url' => 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=800&auto=format&fit=crop&q=80'],
    29 => ['name' => 'Roasted Beetroot & Goat Cheese Salad', 'url' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=800&auto=format&fit=crop&q=80'],
    30 => ['name' => 'Sprouted Moong & Pomegranate Salad', 'url' => 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800&auto=format&fit=crop&q=80'],
    31 => ['name' => 'Hearty Minestrone with Beans', 'url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&auto=format&fit=crop&q=80'],
    32 => ['name' => 'Clear Chicken & Herb Broth', 'url' => 'https://images.unsplash.com/photo-1608897013039-887f21d8c804?w=800&auto=format&fit=crop&q=80'],
    33 => ['name' => 'Cream of Roasted Mushroom (Light)', 'url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&auto=format&fit=crop&q=80'],
    34 => ['name' => 'Broccoli & Almond Protein Soup', 'url' => 'https://images.unsplash.com/photo-1576186726115-4d51596775d1?w=800&auto=format&fit=crop&q=80'],
    35 => ['name' => 'Spiced Tomato Basil Soup', 'url' => 'https://images.unsplash.com/photo-1594756202469-9ff9799b2e4e?w=800&auto=format&fit=crop&q=80'],
    36 => ['name' => 'Crispy Baked Tofu Bites', 'url' => 'https://images.unsplash.com/photo-1546069901-d5bfd2cbfb1f?w=800&auto=format&fit=crop&q=80'],
    37 => ['name' => 'Grilled Chicken Skewers', 'url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&auto=format&fit=crop&q=80'],
    38 => ['name' => 'Edamame with Himalayan Pink Salt', 'url' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&auto=format&fit=crop&q=80'],
    39 => ['name' => 'Air-Fried Sweet Potato Fries', 'url' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=800&auto=format&fit=crop&q=80'],
    40 => ['name' => 'Spiced Cottage Cheese Skewers', 'url' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800&auto=format&fit=crop&q=80'],
    41 => ['name' => 'Green Detox Cold-Pressed Juice', 'url' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&auto=format&fit=crop&q=80'],
    42 => ['name' => 'Raw Coconut Water with Chia Seeds', 'url' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=800&auto=format&fit=crop&q=80'],
    43 => ['name' => 'Berry Antioxidant Blast Shake', 'url' => 'https://images.unsplash.com/photo-1553530979-7ee52a2670c4?w=800&auto=format&fit=crop&q=80'],
    44 => ['name' => 'Protein Ice Cream (Vanilla/Choco)', 'url' => 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=800&auto=format&fit=crop&q=80'],
    45 => ['name' => 'Chia Seed Pudding with Mango Puree', 'url' => 'https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=800&auto=format&fit=crop&q=80'],
    46 => ['name' => 'Baked Oat & Apple Crumble (No Sugar)', 'url' => 'https://images.unsplash.com/photo-1568571780765-9276ac8b75a2?w=800&auto=format&fit=crop&q=80'],
    47 => ['name' => 'Greek Yogurt Parfait with Berries', 'url' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&auto=format&fit=crop&q=80'],
    48 => ['name' => 'Artisan Sourdough Protein Pizza', 'url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&auto=format&fit=crop&q=80'],
    49 => ['name' => 'Grilled Lean Protein Burger', 'url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&auto=format&fit=crop&q=80'],
];

$photoIds = [];
$duplicates = [];
foreach ($allUrls as $id => $item) {
    preg_match('/photo-[0-9a-f-]+/', $item['url'], $m);
    $pid = $m[0] ?? $item['url'];
    if (isset($photoIds[$pid])) {
        $duplicates[] = "Duplicate photo ID {$pid} on food #$id and #{$photoIds[$pid]}";
    }
    $photoIds[$pid] = $id;
}

echo "Total items: " . count($allUrls) . "\n";
echo "Total unique photo IDs: " . count($photoIds) . "\n";
if (!empty($duplicates)) {
    echo "ERROR: " . implode("\n", $duplicates) . "\n";
    exit(1);
}

echo "Checking HTTP status codes for all 45 URLs...\n";
$failed = 0;
foreach ($allUrls as $id => $item) {
    $ch = curl_init($item['url']);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($code !== 200 && $code !== 301 && $code !== 302) {
        echo "FAILED #$id {$item['name']}: HTTP $code ({$item['url']})\n";
        $failed++;
    }
}

if ($failed === 0) {
    echo "ALL 45 URLS VERIFIED: HTTP 200 OK & 100% DISTINCT!\n";
} else {
    echo "$failed URLs failed.\n";
}
