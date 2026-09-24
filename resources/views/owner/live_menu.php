<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Live Menu</h1>
        <p class="hb-page-subtitle">Preview the customer-facing digital menu with real-time ordering simulation.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
        <span id="cart-counter-badge" style="background: #EAF5EA; color: #1B5E20; padding: 7px 16px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid #C8E6C9;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <span id="cart-item-count">0 items (₹0)</span>
        </span>
        <a href="<?= url('/menu') ?>" target="_blank" class="hb-btn hb-btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
            <span>Open Customer Menu</span>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        </a>
    </div>
</div>

<!-- Hero Banner matching Figma screen -->
<div class="hb-hero-banner" style="background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.7) 100%), url('https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=1600&auto=format&fit=crop&q=80') center/cover no-repeat; border-radius: 16px; min-height: 170px; display: flex; align-items: flex-end; padding: 24px; margin-bottom: 24px;">
    <div>
        <span class="hb-badge hb-badge-success" style="font-size: 11px; padding: 3px 10px; margin-bottom: 8px; display: inline-block; background: rgba(255,255,255,0.9); color: #1B5E20; font-weight: 700;">
            ● Live Digital Ordering Active
        </span>
        <h2 style="font-size: 26px; font-weight: 800; color: #FFFFFF; margin: 0 0 4px 0;">
            <?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?>
        </h2>
        <p style="font-size: 14px; font-weight: 500; color: rgba(255, 255, 255, 0.9); margin: 0;">
            Healthy Gourmet Dining · <?= e($restaurant['city'] ?? 'Bengaluru') ?> · Indiranagar Branch
        </p>
    </div>
</div>

<!-- Category Chips Filter -->
<div style="display: flex; gap: 10px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 6px;" id="categoryFilterBar">
    <button type="button" class="hb-btn hb-btn-primary hb-btn-sm cat-filter-btn active" onclick="filterLiveCategory('all', this)" style="border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
        All Items
    </button>
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
            <?php 
                $catSlug = strtolower(preg_replace('/[^a-z0-9]/', '', $cat['name']));
            ?>
            <button type="button" class="hb-btn hb-btn-outline hb-btn-sm cat-filter-btn" onclick="filterLiveCategory('<?= $catSlug ?>', this)" style="background:#fff; border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
                <?= e($cat['name']) ?>
            </button>
        <?php endforeach; ?>
    <?php else: ?>
        <button type="button" class="hb-btn hb-btn-outline hb-btn-sm cat-filter-btn" onclick="filterLiveCategory('bowls', this)" style="background:#fff; border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
            Protein Bowls
        </button>
        <button type="button" class="hb-btn hb-btn-outline hb-btn-sm cat-filter-btn" onclick="filterLiveCategory('wraps', this)" style="background:#fff; border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
            Wraps
        </button>
        <button type="button" class="hb-btn hb-btn-outline hb-btn-sm cat-filter-btn" onclick="filterLiveCategory('salads', this)" style="background:#fff; border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
            Salads
        </button>
        <button type="button" class="hb-btn hb-btn-outline hb-btn-sm cat-filter-btn" onclick="filterLiveCategory('beverages', this)" style="background:#fff; border-radius: 9999px; padding: 7px 20px; font-weight: 600;">
            Beverages
        </button>
    <?php endif; ?>
</div>

<!-- 4-Column Food Cards Grid with Real Gourmet Photography -->
<div class="hb-grid-4" id="liveFoodGrid" style="margin-bottom: 32px; gap: 20px;">
    <?php foreach ($foods as $f): ?>
        <?php 
            $catSlug = strtolower(preg_replace('/[^a-z0-9]/', '', $f['category_name'] ?? 'bowls'));
            $foodType = strtolower($f['food_type'] ?? 'vegetarian');
            $isVeg = ($foodType === 'vegetarian' || $foodType === 'vegan');

            // High resolution gourmet photography resolver
            $rawImg = (string)($f['image'] ?? '');
            $imageSrc = foodImageUrl($rawImg);
            $price = (float)($f['base_price'] ?? 249);
            $protein = (int)($f['protein'] ?? 24);
            $calories = (int)($f['calories'] ?? 420);
        ?>
        <div class="hb-food-card live-food-card" data-category="<?= $catSlug ?>" style="background: #FFFFFF; border: 1px solid var(--color-gray-200); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s;">
            <!-- Food Image -->
            <div style="height: 180px; background: #F3F4F6; overflow: hidden; position: relative;">
                <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= e($f['name']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                
                <!-- Macro Overlay Badge -->
                <div style="position: absolute; top: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(4px); color: #fff; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                    <?= $calories ?> kcal
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 16px; display: flex; flex-direction: column; flex: 1;">
                <!-- Tags -->
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                    <?php if ($isVeg): ?>
                        <span class="hb-badge hb-badge-success" style="font-size: 11px; padding: 2px 8px; display: inline-flex; align-items: center; gap: 4px;">
                            <span style="width:6px; height:6px; border-radius:50%; background:#16a34a;"></span> Vegetarian
                        </span>
                    <?php else: ?>
                        <span class="hb-badge hb-badge-warning" style="font-size: 11px; padding: 2px 8px; display: inline-flex; align-items: center; gap: 4px;">
                            <span style="width:6px; height:6px; border-radius:50%; background:#d97706;"></span> High Protein
                        </span>
                    <?php endif; ?>
                    <span style="font-size: 11px; font-weight: 600; color: #166534; background: #EAF5EA; padding: 2px 8px; border-radius: 4px;">
                        <?= $protein ?>g Protein
                    </span>
                </div>

                <!-- Title -->
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 6px 0; line-height: 1.3;">
                    <?= e($f['name']) ?>
                </h3>

                <!-- Subtitle / Allergens -->
                <p style="font-size: 12px; color: var(--color-gray-500); margin: 0 0 16px 0; line-height: 1.4;">
                    <?= e($f['description'] ?? ($f['ingredients'] ?? 'Prepared fresh with whole organic ingredients.')) ?>
                </p>

                <!-- Bottom Row: Price & Add to Cart -->
                <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #F3F4F6;">
                    <span style="font-size: 18px; font-weight: 800; color: var(--color-gray-900);">
                        ₹<?= number_format($price, 0) ?>
                    </span>
                    <button type="button" class="hb-btn hb-btn-primary hb-btn-sm add-cart-btn" 
                            onclick="addToCartPreview(<?= json_encode($f['name']) ?>, <?= (float)$price ?>, this)" 
                            style="border-radius: 8px; padding: 7px 16px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add to Cart</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Dynamic Empty State (Displays only when filter has no matches) -->
<div id="liveMenuEmptyState" style="display: none; text-align: center; padding: 60px 20px; background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 14px; margin-bottom: 32px;">
    <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #64748B;">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
    </div>
    <h3 style="font-size: 16px; font-weight: 700; color: #1E293B; margin: 0 0 6px 0;">No dishes found in this category</h3>
    <p style="font-size: 13px; color: #64748B; margin: 0 0 16px 0;">Try selecting a different category chip or reset to view all menu items.</p>
    <button type="button" class="hb-btn hb-btn-primary" onclick="filterLiveCategory('all', document.querySelector('.cat-filter-btn'))" style="padding: 8px 20px; border-radius: 8px;">
        Show All Dishes
    </button>
</div>

<!-- Cart Summary Toast / Drawer -->
<div id="cartDrawer" style="position: fixed; bottom: 24px; right: 24px; background: #1B5E20; color: #fff; padding: 14px 22px; border-radius: 12px; box-shadow: 0 12px 32px rgba(0,0,0,0.25); display: none; align-items: center; gap: 16px; z-index: 99999; animation: slideInUp 0.3s ease;">
    <div>
        <div style="font-size: 14px; font-weight: 700;" id="cartToastTitle">Added to cart!</div>
        <div style="font-size: 12px; color: rgba(255,255,255,0.85);" id="cartToastSubtitle">0 items in order</div>
    </div>
    <a href="<?= url('/menu') ?>" target="_blank" style="background: #FFFFFF; color: #1B5E20; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
        View Menu &rarr;
    </a>
</div>

<script>
let liveCart = {
    items: [],
    totalPrice: 0
};

function filterLiveCategory(category, btn) {
    document.querySelectorAll('.cat-filter-btn').forEach(b => {
        b.classList.remove('hb-btn-primary', 'active');
        b.classList.add('hb-btn-outline');
        b.style.background = '#fff';
        b.style.color = 'var(--color-gray-700)';
    });
    btn.classList.remove('hb-btn-outline');
    btn.classList.add('hb-btn-primary', 'active');
    btn.style.background = 'var(--color-primary)';
    btn.style.color = '#fff';

    const cards = document.querySelectorAll('.live-food-card');
    let visibleCount = 0;
    cards.forEach(card => {
        const cardCat = card.getAttribute('data-category');
        if (category === 'all' || cardCat === category || cardCat.includes(category) || category.includes(cardCat)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const emptyBox = document.getElementById('liveMenuEmptyState');
    if (emptyBox) {
        emptyBox.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function addToCartPreview(dishName, price, btn) {
    liveCart.items.push({ name: dishName, price: price });
    liveCart.totalPrice += price;

    const countEl = document.getElementById('cart-item-count');
    if (countEl) {
        countEl.textContent = `${liveCart.items.length} items (₹${liveCart.totalPrice.toFixed(0)})`;
    }

    // Button feedback animation
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Added!';
    btn.style.background = '#15803d';
    setTimeout(() => {
        btn.innerHTML = originalContent;
        btn.style.background = 'var(--color-primary)';
    }, 1200);

    // Toast feedback
    const toast = document.getElementById('cartDrawer');
    const title = document.getElementById('cartToastTitle');
    const sub = document.getElementById('cartToastSubtitle');
    if (toast && title && sub) {
        title.textContent = `Added "${dishName}"`;
        sub.textContent = `${liveCart.items.length} items · Total ₹${liveCart.totalPrice.toFixed(0)}`;
        toast.style.display = 'flex';
        clearTimeout(window._cartToastTimer);
        window._cartToastTimer = setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }
}
</script>
