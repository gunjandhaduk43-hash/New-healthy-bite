<?php
declare(strict_types=1);

$ctx = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nAccept: text/html\r\n",
        'timeout' => 5
    ]
]);

$testPhotos = [
    '1576186726115-4d51596775d1' => 'Broccoli & Almond Protein Soup (Current)',
    '1599488615731-7e5c2823ff28' => 'Spiced Cottage Cheese Skewers (Current)',
    '1553530979-7ee52a2670c4' => 'Berry Antioxidant Blast Shake (Current)',
    '1631452180519-c014fe946bc7' => 'Paneer Steak with Quinoa & Greens',
    '1467003909585-2f8a72700288' => 'Smoked Salmon with Steamed Asparagus',
    '1512058564366-18510be2db19' => 'Tofu & Broccoli Stir-Fry Meal',
    '1604908176997-125f25cc6f3d' => 'Rosemary Garlic Chicken Breast',
    '1546069901-d5bfd2cbfb1f' => 'Crispy Baked Tofu Bites',
    '1555939594-58d7cb561ad1' => 'Grilled Chicken Skewers',
    '1559847844-5315695dadae' => 'Edamame with Himalayan Pink Salt',
    '1573080496219-bb080dd4f877' => 'Air-Fried Sweet Potato Fries',
    '1610970881699-44a5587cabec' => 'Green Detox Cold-Pressed Juice',
    '1525385133512-2f3bdd039054' => 'Raw Coconut Water with Chia Seeds',
    '1497034825429-c343d7c6a68f' => 'Protein Ice Cream (Vanilla/Choco)',
    '1511690743698-d9d85f2fbf38' => 'Chia Seed Pudding with Mango Puree',
    '1568571780765-9276ac8b75a2' => 'Baked Oat & Apple Crumble (No Sugar)',
    '1488477181946-6428a0291777' => 'Greek Yogurt Parfait with Berries',
    '1565299624946-b28f40a0ae38' => 'Artisan Sourdough Protein Pizza',
    '1568901346375-23c9450c58cd' => 'Grilled Lean Protein Burger'
];

foreach ($testPhotos as $photoId => $label) {
    $url = "https://unsplash.com/photos/" . $photoId;
    $html = @file_get_contents($url, false, $ctx);
    if ($html) {
        if (preg_match('/<title>(.*?)<\/title>/s', $html, $m)) {
            $title = html_entity_decode(trim($m[1]));
            echo "[$photoId] $label\n  => Title: $title\n";
        } else {
            echo "[$photoId] $label => Loaded, no title\n";
        }
    } else {
        echo "[$photoId] $label => Failed to fetch\n";
    }
}
