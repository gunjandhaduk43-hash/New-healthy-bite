    <!-- 1. Restaurant Compact Info Bar (Prompt Requirements #13 & #14) -->
    <?php require dirname(__DIR__) . '/partials/restaurant-header.php'; ?>

    <?php if (!empty($tokenError)): ?>
        <div style="margin: 12px 16px; padding: 12px 16px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; display: flex; align-items: center; gap: 10px; font-size: 13px; color: #92400e;">
            <i class="bi bi-exclamation-triangle-fill" style="color: #f59e0b; font-size: 16px; flex-shrink: 0;"></i>
            <span><?= e($tokenError) ?></span>
        </div>
    <?php endif; ?>

    <!-- 2. Search & Filter Bar (Prompt Requirement #15) -->
    <div class="search-section" role="search">
        <div class="search-input-wrapper">
            <i class="bi bi-search search-icon" aria-hidden="true"></i>
            <input 
                type="search" 
                id="foodSearchInput" 
                class="search-input" 
                placeholder="Search meals, macros, or ingredients (e.g., chicken, quinoa, salad)..."
                autocomplete="off"
                aria-label="Search food menu"
            >
            <button type="button" class="search-clear-btn" id="searchClearBtn" aria-label="Clear search" style="display:none;">
                <i class="bi bi-x-circle-fill"></i>
            </button>
        </div>
    </div>

    <!-- 3. Category Navigation Pills with Scroll-Snap (Prompt Requirement #16) -->
    <nav class="category-pills-nav" id="categoryPillsNav" aria-label="Food Categories">
        <?php 
        $catIcons = [
            'main-meals' => 'bi-fire',
            'bowls'      => 'bi-egg-fried',
            'wraps'      => 'bi-disc',
            'salads'     => 'bi-flower1',
            'soups'      => 'bi-cup-hot',
            'appetizers' => 'bi-grid-fill',
            'beverages'  => 'bi-cup-straw',
            'desserts'   => 'bi-cake2'
        ];
        ?>
        <button 
            type="button" 
            class="category-pill-btn active" 
            data-category-id="all" 
            onclick="filterByCategory('all', this)"
        >
            <i class="bi bi-grid-fill"></i>
            <span>All Items</span>
        </button>
        <?php foreach ($categories as $cat): ?>
            <?php 
                $slug = $cat['slug'] ?? strtolower(preg_replace('/[^a-z0-9]/', '', $cat['name']));
                $icon = $catIcons[$slug] ?? 'bi-tag-fill';
            ?>
            <button 
                type="button"
                class="category-pill-btn" 
                data-category-id="<?= (int)$cat['id'] ?>" 
                data-slug="<?= e($slug) ?>"
                onclick="filterByCategory(<?= (int)$cat['id'] ?>, this)"
            >
                <i class="bi <?= $icon ?>"></i>
                <span><?= e($cat['name']) ?></span>
            </button>
        <?php endforeach; ?>
    </nav>

    <!-- 4. Main 2-Column Responsive Layout -->
    <div class="main-layout-grid">
        <!-- Left Column: Menu Sections & Food Cards Grid -->
        <div class="menu-content-col" id="menuContentCol">
            <?php
            // Group foods by category
            $groupedFoods = [];
            foreach ($foods as $food) {
                $cId = (int)$food['category_id'];
                if (!isset($groupedFoods[$cId])) {
                    $groupedFoods[$cId] = [
                        'id'          => $cId,
                        'name'        => $food['category_name'],
                        'description' => $food['category_description'] ?? 'Wholesome and delicious meals to fuel your day.',
                        'items'       => []
                    ];
                }
                $groupedFoods[$cId]['items'][] = $food;
            }
            ?>

            <?php foreach ($groupedFoods as $cId => $group): ?>
                <section class="menu-section" data-section-category-id="<?= $cId ?>" id="cat-section-<?= $cId ?>">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title"><?= e($group['name']) ?></h2>
                            <p class="section-desc"><?= e($group['description']) ?></p>
                        </div>
                        <span class="section-count"><?= count($group['items']) ?> items</span>
                    </div>

                    <div class="food-grid">
                        <?php foreach ($group['items'] as $food): ?>
                            <?php 
                                $foodType = strtolower($food['food_type'] ?? 'veg');
                                $isVeg = ($foodType === 'veg' || $foodType === 'vegetarian');
                                $isVegan = ($foodType === 'vegan');
                                $isNonVeg = ($foodType === 'non_veg' || $foodType === 'non-vegetarian' || $foodType === 'nonveg');
                                $isJain = ($foodType === 'jain');
                            ?>
                            <article 
                                class="food-card" 
                                data-food-id="<?= (int)$food['id'] ?>"
                                data-category-id="<?= (int)$food['category_id'] ?>"
                                data-food-name="<?= e($food['name']) ?>"
                                data-food-desc="<?= e($food['description'] ?? '') ?>"
                                data-food-ingredients="<?= e($food['ingredients'] ?? '') ?>"
                                data-base-price="<?= (float)$food['base_price'] ?>"
                                data-food-type="<?= e($foodType) ?>"
                                onclick="openFoodDetails(<?= (int)$food['id'] ?>)"
                                tabindex="0"
                                role="button"
                                aria-label="View details for <?= e($food['name']) ?>"
                            >
                                <!-- Media Container with 16:10 Gourmet Aspect Ratio -->
                                <div class="food-card-media">
                                    <img 
                                        src="<?= foodImageUrl($food['image']) ?>" 
                                        alt="<?= e($food['name']) ?>" 
                                        class="food-card-img" 
                                        loading="lazy"
                                        onerror="this.src='<?= asset('images/foods/placeholder-dish.svg') ?>'"
                                    >
                                    
                                    <!-- Badges: Dietary & Highlights (Prompt Requirement #18) -->
                                    <div class="card-top-badges">
                                        <?php if ($isVeg): ?>
                                            <span class="diet-badge veg" title="Vegetarian">
                                                <span class="diet-symbol diet-symbol-veg"></span>
                                                <span>Veg</span>
                                            </span>
                                        <?php elseif ($isVegan): ?>
                                            <span class="diet-badge vegan" title="Vegan">
                                                <i class="bi bi-flower1"></i>
                                                <span>Vegan</span>
                                            </span>
                                        <?php elseif ($isNonVeg): ?>
                                            <span class="diet-badge nonveg" title="Non-Vegetarian">
                                                <span class="diet-symbol diet-symbol-nonveg"></span>
                                                <span>Non-Veg</span>
                                            </span>
                                        <?php elseif ($isJain): ?>
                                            <span class="diet-badge jain" title="Jain Friendly">
                                                <i class="bi bi-shield"></i>
                                                <span>Jain</span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($food['protein']) && (float)$food['protein'] >= 25): ?>
                                            <span class="macro-highlight-badge">
                                                <i class="bi bi-lightning-charge-fill"></i> High Protein
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <button 
                                        type="button" 
                                        class="food-card-fav-btn" 
                                        aria-label="Save as favorite" 
                                        onclick="event.stopPropagation(); this.classList.toggle('active');"
                                    >
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>

                                <!-- Body: Name, Description, Allergens, Nutrition -->
                                <div class="food-card-body">
                                    <div class="food-card-header">
                                        <h3 class="food-card-name"><?= e($food['name']) ?></h3>
                                    </div>
                                    <p class="food-card-desc"><?= e($food['description'] ?? 'Wholesome ingredients prepared fresh to order.') ?></p>

                                    <!-- Allergen Tag (Prompt Requirement #19) -->
                                    <?php if (!empty($food['allergens'])): ?>
                                        <div class="food-card-allergens">
                                            <span class="allergen-notice-badge">
                                                <i class="bi bi-exclamation-triangle"></i> <?= e($food['allergens']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Nutrition Macro Chips (Prompt Requirement #23 & Sugar Enhancement) -->
                                    <div class="food-card-nutrition">
                                        <?php if ($food['calories'] !== null): ?>
                                            <span class="macro-chip"><i class="bi bi-fire" style="color:var(--accent-amber);"></i> <?= (int)$food['calories'] ?> kcal</span>
                                        <?php endif; ?>
                                        <?php if ($food['protein'] !== null): ?>
                                            <span class="macro-chip macro-highlight"><?= (float)$food['protein'] ?>g protein</span>
                                        <?php endif; ?>
                                        <?php if ($food['carbs'] !== null): ?>
                                            <span class="macro-chip"><?= (float)$food['carbs'] ?>g carbs</span>
                                        <?php endif; ?>
                                        <?php if ($food['sugar'] !== null): ?>
                                            <span class="macro-chip macro-sugar" style="background:#fef3c7; color:#92400e; border-color:#fde68a; font-weight:600;"><i class="bi bi-droplet-half" style="color:#d97706;"></i> <?= (float)$food['sugar'] ?>g sugar</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Footer: Price & Add Action -->
                                    <div class="food-card-footer">
                                        <div class="food-card-price">
                                            <span class="currency">₹</span><span class="price-value"><?= (int)$food['base_price'] ?></span>
                                        </div>
                                        <button 
                                            type="button" 
                                            class="btn-add-food" 
                                            aria-label="Add <?= e($food['name']) ?> to order"
                                            onclick="event.stopPropagation(); openFoodDetails(<?= (int)$food['id'] ?>)"
                                        >
                                            <span>Add</span>
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

            <!-- Empty Search State (Prompt Requirement #15) -->
            <div id="menuEmptyState" class="empty-state-card" style="display:none;">
                <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                <h3 class="empty-state-title">No dishes found</h3>
                <p class="empty-state-desc">We couldn't find any dishes matching your search. Try searching for a different item or browse our categories.</p>
                <button type="button" class="btn btn-secondary btn-sm" onclick="clearSearch()">
                    View All Items
                </button>
            </div>
        </div>

        <!-- Right Column: Sticky Sidebar Order & Live Kitchen Status -->
        <aside class="cart-sidebar-col" aria-label="Order summary and live kitchen status">
            <!-- Widget 1: Your Order (Cart) -->
            <div class="sidebar-widget order-widget" id="sidebarOrderWidget">
                <div class="order-widget-header">
                    <div class="order-widget-title">
                        <i class="bi bi-bag-check-fill" style="color:var(--primary-green);"></i>
                        <span>Your Order (<span id="sidebarOrderCount">0</span>)</span>
                    </div>
                    <button type="button" class="order-widget-clear" id="sidebarClearCartBtn" title="Clear current cart">Clear</button>
                </div>
                
                <div class="order-widget-items" id="sidebarOrderItems">
                    <!-- Populated dynamically by cart.js -->
                    <div class="order-widget-empty" id="sidebarOrderEmpty">
                        <div class="empty-bag-icon"><i class="bi bi-basket3"></i></div>
                        <p class="empty-bag-text">Your table order is empty.<br>Choose wholesome meals from the menu.</p>
                    </div>
                </div>

                <div class="order-widget-footer" id="sidebarOrderFooter" style="display:none;">
                    <div class="order-nutrition-row" id="sidebarOrderNutritionRow" style="display:flex; justify-content:space-between; font-size:12px; color:#475569; margin-bottom:8px; background:#f8fafc; padding:6px 10px; border-radius:6px;">
                        <span style="display:flex; align-items:center; gap:5px; font-weight:600;"><i class="bi bi-heart-pulse-fill" style="color:var(--primary-green);"></i> Total Sugar:</span>
                        <span id="sidebarTotalSugar" style="font-weight:700; color:#b45309;">0 g</span>
                    </div>
                    <div class="order-total-row">
                        <span class="total-label">Subtotal (<span id="sidebarTotalItemsLabel">0</span> items)</span>
                        <span class="total-amount" id="sidebarTotalAmount">₹0</span>
                    </div>
                    <a href="<?= url('/menu/checkout') ?>" class="btn-sidebar-viewcart" id="sidebarViewCartBtn" onclick="if(window.Cart){ Cart.proceedToCheckout(); return false; }">
                        <span>Review &amp; Checkout</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Widget 2: Live Kitchen Status (Prompt Requirement #32 & #33) -->
            <div class="sidebar-widget live-kitchen-widget" id="live-kitchen-widget">
                <div class="kitchen-widget-header">
                    <div class="kitchen-widget-title">
                        <i class="bi bi-fire" style="color:var(--accent-amber);"></i>
                        <span>Kitchen Live Tracker</span>
                    </div>
                    <span class="badge-live-pulse"><span class="pulse-dot"></span> Live</span>
                </div>
                
                <div class="kitchen-stepper" id="liveKitchenStepper">
                    <div class="stepper-step completed" id="step-placed">
                        <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                        <div class="step-content">
                            <div class="step-title-row">
                                <span class="step-title">Order Placed</span>
                                <span class="step-time" id="time-placed">—</span>
                            </div>
                            <div class="step-desc">Order received by table session.</div>
                        </div>
                    </div>

                    <div class="stepper-step completed" id="step-accepted">
                        <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                        <div class="step-content">
                            <div class="step-title-row">
                                <span class="step-title">Accepted</span>
                                <span class="step-time" id="time-accepted">—</span>
                            </div>
                            <div class="step-desc">Kitchen confirmed ticket.</div>
                        </div>
                    </div>

                    <div class="stepper-step active" id="step-preparing">
                        <div class="step-icon"><i class="bi bi-fire"></i></div>
                        <div class="step-content">
                            <div class="step-title-row">
                                <span class="step-title">Preparing</span>
                                <span class="step-time" id="time-preparing">In Progress</span>
                            </div>
                            <div class="step-desc">Fresh meals on the grill.</div>
                        </div>
                    </div>

                    <div class="stepper-step pending" id="step-ready">
                        <div class="step-icon"><i class="bi bi-bell"></i></div>
                        <div class="step-content">
                            <div class="step-title-row">
                                <span class="step-title">Ready for Service</span>
                            </div>
                            <div class="step-desc">Ready to serve at table.</div>
                        </div>
                    </div>

                    <div class="stepper-step pending" id="step-completed">
                        <div class="step-icon"><i class="bi bi-trophy"></i></div>
                        <div class="step-content">
                            <div class="step-title-row">
                                <span class="step-title">Completed</span>
                            </div>
                            <div class="step-desc">Enjoy your wholesome meal!</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget 3: Fresh Dining Promise -->
            <div class="sidebar-widget quote-widget">
                <div class="quote-leaf-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div class="quote-text-box">
                    <div class="quote-main">Pure Food. Real Macros.</div>
                    <div class="quote-sub">No artificial additives. Locally sourced and weighed to precision.</div>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Food Customization Bottom Sheet / Modal (Prompt Requirement #20, #21, #22, #23) -->
<?php require dirname(__DIR__) . '/partials/food-detail.php'; ?>

<!-- Cart Drawer & Complete-The-Meal Suggestions (Prompt Requirement #24 & #25) -->
<?php require dirname(__DIR__) . '/partials/cart-drawer.php'; ?>

<!-- Embed State Context for JS (Preserving Backend Context) -->
<script>
    window.State = window.State || {};
    window.State.restaurant = <?= json_encode($restaurant, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.branch = <?= json_encode($branch, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.table = <?= json_encode($tableContext, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.qrToken = <?= json_encode($qrToken, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>

<!-- Scripts -->
<script src="<?= asset('js/state.js') ?>"></script>
<script src="<?= asset('js/utils.js') ?>"></script>
<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/pricing.js') ?>"></script>
<script src="<?= asset('js/nutrition.js') ?>"></script>
<script src="<?= asset('js/food-details.js') ?>"></script>
<script src="<?= asset('js/cart.js') ?>"></script>
<script src="<?= asset('js/live-kitchen.js') ?>"></script>
<script src="<?= asset('js/menu.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
