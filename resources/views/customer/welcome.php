<div class="welcome-page-container">
    <!-- Hero Section -->
    <section class="welcome-hero-section">
        <div class="welcome-hero-content">
            <div class="welcome-hero-left">
                <div class="welcome-brand-badge">
                    <img src="<?= asset('images/logo.png') ?>" alt="Healthy Bite" class="welcome-badge-logo">
                    <span>Healthy Bite &bull; Macro-Accurate Clean Dining</span>
                </div>

                <h1 class="welcome-hero-heading">
                    Good Food. Better You.<br>
                    <span class="text-gradient">Macro-Accurate Dining.</span>
                </h1>

                <p class="welcome-hero-subheading">
                    Welcome to <strong><?= e($restaurant) ?></strong> (<?= e($branch) ?>). 
                    Enjoy chef-crafted wholesome bowls, lean proteins, and cold-pressed refreshments—all with 100% transparent calories, protein, and zero hidden sugars.
                </p>

                <div class="welcome-hero-actions">
                    <a href="<?= url('/menu') ?>" class="btn-welcome-hero primary" id="welcomeExploreMenuBtn">
                        <i class="bi bi-book-half"></i>
                        <span>Explore Digital Menu</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                    <a href="#table-picker" class="btn-welcome-hero secondary" id="welcomePickTableBtn">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Pick Table & Order</span>
                    </a>

                    <button type="button" class="btn-welcome-hero live-track" id="welcomeLiveKitchenBtn" onclick="return handleLiveKitchenClick(event)">
                        <i class="bi bi-fire"></i>
                        <span>Live Kitchen</span>
                    </button>
                </div>

                <div class="welcome-metrics-strip">
                    <div class="welcome-metric-item">
                        <div class="metric-icon-box"><i class="bi bi-stopwatch-fill"></i></div>
                        <div>
                            <div class="metric-value">10–15 Mins</div>
                            <div class="metric-label">Average Kitchen Prep</div>
                        </div>
                    </div>
                    <div class="welcome-metric-item">
                        <div class="metric-icon-box"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <div class="metric-value">100% Tracked</div>
                            <div class="metric-label">Calories & Protein</div>
                        </div>
                    </div>
                    <div class="welcome-metric-item">
                        <div class="metric-icon-box"><i class="bi bi-star-fill"></i></div>
                        <div>
                            <div class="metric-value">4.9 / 5.0</div>
                            <div class="metric-label">Diner Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="welcome-hero-right">
                <div class="welcome-hero-card">
                    <div class="hero-image-wrapper">
                        <img src="<?= asset('images/hero_dish.jpg') ?>" alt="Healthy Bite Featured Platter" class="hero-showcase-img">
                        <div class="hero-glow-overlay"></div>
                    </div>

                    <!-- Floating Glass Badges -->
                    <div class="hero-floating-pill top-pill">
                        <i class="bi bi-lightning-charge-fill" style="color: #f59e0b;"></i>
                        <span>52.5g Protein &bull; Low Carb</span>
                    </div>

                    <div class="hero-floating-pill bottom-pill">
                        <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                        <span>Live Kitchen Ready &bull; Made Fresh</span>
                    </div>

                    <div class="hero-floating-price">
                        <span class="price-from">From</span>
                        <span class="price-val">₹279</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dine-In Table Quick-Select Section -->
    <section class="welcome-section welcome-tables-section" id="table-picker">
        <div class="welcome-section-header">
            <span class="section-tag-badge"><i class="bi bi-shop"></i> Contactless Dine-In</span>
            <h2 class="welcome-section-title">Dining In At Greenhouse Kitchen?</h2>
            <p class="welcome-section-desc">
                Select your table number below to open your personalized contactless digital menu. Your order will be sent instantly to the kitchen display!
            </p>
        </div>

        <div class="welcome-table-grid">
            <?php if (!empty($tables)): ?>
                <?php foreach ($tables as $tbl): ?>
                    <?php 
                        $tblNum = (string)$tbl['table_number'];
                        $displayTbl = stripos($tblNum, 'table') === 0 ? $tblNum : 'Table ' . $tblNum;
                    ?>
                    <a href="<?= url('/table/' . urlencode($tblNum)) ?>" class="welcome-table-card" id="table-chip-<?= e($tblNum) ?>">
                        <div class="table-card-icon">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <div class="table-card-title"><?= e($displayTbl) ?></div>
                        <div class="table-card-status">
                            <span class="status-indicator-dot"></span>
                            <span>Order Here</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="table-empty-notice">
                    <a href="<?= url('/menu') ?>" class="btn-welcome-hero primary">Open Digital Menu</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="welcome-takeaway-hint">
            <span>Taking away or ordering for pick up?</span>
            <a href="<?= url('/menu') ?>" class="inline-menu-link">Open Standard Digital Menu &rarr;</a>
        </div>
    </section>

    <!-- Core Experience Pillars -->
    <section class="welcome-section welcome-features-section">
        <div class="welcome-section-header">
            <span class="section-tag-badge"><i class="bi bi-stars"></i> The Healthy Bite Standard</span>
            <h2 class="welcome-section-title">Why Dine With Healthy Bite?</h2>
            <p class="welcome-section-desc">
                We re-engineered digital restaurant dining to be completely transparent, personalized, and effortless.
            </p>
        </div>

        <div class="welcome-pillars-grid">
            <div class="welcome-pillar-card">
                <div class="pillar-icon-wrapper" style="background: rgba(46, 125, 50, 0.1); color: #2e7d32;">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <h3 class="pillar-title">Macro Transparency</h3>
                <p class="pillar-desc">
                    Every dish lists verified calories, protein, carbs, healthy fats, and sugar. No hidden surprises or guessing games.
                </p>
            </div>

            <div class="welcome-pillar-card">
                <div class="pillar-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                    <i class="bi bi-sliders"></i>
                </div>
                <h3 class="pillar-title">Deep Customization</h3>
                <p class="pillar-desc">
                    Swap your carb bases, pick zero-sugar dressings, adjust portion sizes, and add extra protein scoops tailored to your diet.
                </p>
            </div>

            <div class="welcome-pillar-card">
                <div class="pillar-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                    <i class="bi bi-fire"></i>
                </div>
                <h3 class="pillar-title">Live Kitchen Tracker</h3>
                <p class="pillar-desc">
                    Follow your food through our digital kitchen pipeline in real-time from ticket acceptance to cooking and plating.
                </p>
            </div>

            <div class="welcome-pillar-card">
                <div class="pillar-icon-wrapper" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <i class="bi bi-phone-flip"></i>
                </div>
                <h3 class="pillar-title">100% Contactless</h3>
                <p class="pillar-desc">
                    Scan your table QR code, browse high-res dish photos, customize toppings, and order directly from your mobile device.
                </p>
            </div>
        </div>
    </section>

    <!-- Featured Chef Specials -->
    <?php if (!empty($featuredFoods)): ?>
    <section class="welcome-section welcome-featured-section">
        <div class="welcome-section-header-row">
            <div>
                <span class="section-tag-badge"><i class="bi bi-award-fill"></i> Chef's Selection</span>
                <h2 class="welcome-section-title">Popular Wholesome Dishes</h2>
                <p class="welcome-section-desc">Hand-crafted recipes built for peak nutrition and incredible flavor.</p>
            </div>
            <a href="<?= url('/menu') ?>" class="view-all-link">
                <span>View Full Menu</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="welcome-dishes-grid">
            <?php foreach ($featuredFoods as $food): ?>
                <?php 
                    $foodType = strtolower($food['food_type'] ?? 'veg');
                    $isVeg = in_array($foodType, ['veg', 'vegetarian']);
                ?>
                <div class="welcome-dish-card">
                    <div class="dish-card-media">
                        <img 
                            src="<?= foodImageUrl($food['image']) ?>" 
                            alt="<?= e($food['name']) ?>" 
                            class="dish-img"
                            loading="lazy"
                            onerror="this.src='<?= asset('images/foods/placeholder-dish.svg') ?>'"
                        >
                        <span class="dish-badge-type <?= $isVeg ? 'veg' : 'non-veg' ?>">
                            <?= $isVeg ? '🌱 Veg' : '🍗 Non-Veg' ?>
                        </span>
                        <?php if (!empty($food['category_name'])): ?>
                            <span class="dish-badge-cat"><?= e($food['category_name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="dish-card-body">
                        <h4 class="dish-title"><?= e($food['name']) ?></h4>
                        <p class="dish-desc"><?= e($food['description'] ?? 'Wholesome nutritious meal prepared fresh.') ?></p>
                        
                        <div class="dish-macros-bar">
                            <span class="macro-tag cal"><i class="bi bi-fire"></i> <?= (int)$food['calories'] ?> kcal</span>
                            <span class="macro-tag pro"><i class="bi bi-lightning-fill"></i> <?= (float)$food['protein'] ?>g P</span>
                            <span class="macro-tag sug"><i class="bi bi-heart-pulse"></i> <?= (float)$food['sugar'] ?>g Sug</span>
                        </div>

                        <div class="dish-card-footer">
                            <div class="dish-price">₹<?= number_format((float)$food['base_price'], 2) ?></div>
                            <a href="<?= url('/menu#food-' . $food['id']) ?>" class="btn-dish-order" title="Order <?= e($food['name']) ?>">
                                <span>Order</span>
                                <i class="bi bi-plus-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Menu Categories Quick Navigation -->
    <?php if (!empty($categories)): ?>
    <section class="welcome-section welcome-categories-section">
        <div class="welcome-section-header">
            <span class="section-tag-badge"><i class="bi bi-grid-fill"></i> Categories</span>
            <h2 class="welcome-section-title">Explore By Food Category</h2>
            <p class="welcome-section-desc">Whatever your health goals, we have nutrition-rich options ready for you.</p>
        </div>

        <div class="welcome-categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/menu') ?>" class="welcome-category-pill">
                    <span class="cat-pill-icon"><i class="bi bi-check2-circle"></i></span>
                    <span class="cat-pill-name"><?= e($cat['name']) ?></span>
                    <i class="bi bi-chevron-right cat-pill-arrow"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Location & Opening Hours Banner -->
    <section class="welcome-section welcome-location-section">
        <div class="welcome-location-card">
            <div class="location-details">
                <span class="section-tag-badge"><i class="bi bi-geo-alt-fill"></i> Visit Us In Person</span>
                <h3 class="location-title"><?= e($restaurant) ?> &bull; <?= e($branch) ?></h3>
                <p class="location-address">
                    100ft Road, Indiranagar, Bengaluru, Karnataka &bull; Opposite Metro Pillar 140
                </p>
                <div class="location-features">
                    <span><i class="bi bi-clock-fill"></i> Open Daily: 10:00 AM – 11:00 PM</span>
                    <span><i class="bi bi-wifi"></i> Free Guest Wi-Fi</span>
                    <span><i class="bi bi-qr-code"></i> Instant QR Dining</span>
                </div>
            </div>
            <div class="location-actions">
                <a href="<?= url('/menu') ?>" class="btn-welcome-hero primary">
                    <span>Start Order Now</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
</div>
