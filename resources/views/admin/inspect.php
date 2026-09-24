<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Restaurant Portal</h1>
        <p class="hb-page-subtitle">Inspect <?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?> without changing tenant ownership.</p>
    </div>
    <div>
        <a href="<?= url('/owner/dashboard') ?>" target="_blank" class="hb-btn hb-btn-primary">
            Inspect Portal
        </a>
    </div>
</div>

<!-- Hero Restaurant Card -->
<div class="hb-table-card-container" style="padding: 20px; display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
    <div style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; background: #EAF5EA; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
        <?php if (!empty($restaurant['cover_image']) && file_exists(__DIR__ . '/../../../public/assets/images/' . $restaurant['cover_image'])): ?>
            <img src="<?= url('/assets/images/' . $restaurant['cover_image']) ?>" alt="<?= e($restaurant['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <img src="<?= url('/assets/images/hero_dish.jpg') ?>" alt="<?= e($restaurant['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <?php endif; ?>
    </div>

    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--color-gray-900); margin: 0 0 4px 0;">
            <?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?>
        </h2>
        <p style="font-size: 13px; color: var(--color-gray-600); margin: 0 0 10px 0;">
            Healthy Food Restaurant · <?= e($restaurant['city'] ?? 'Bengaluru') ?> · Owner <?= e($restaurant['owner_name'] ?? 'Aarav Sharma') ?>
        </p>
        <div style="display: flex; gap: 8px;">
            <span class="hb-badge hb-badge-success" style="font-size: 11px; padding: 2px 8px;">
                Approved
            </span>
            <span class="hb-badge" style="background: #EAF5EA; color: #166534; font-size: 11px; padding: 2px 8px;">
                Restaurant active
            </span>
        </div>
    </div>
</div>

<!-- 5 Metric Cards -->
<div class="hb-grid-5" style="margin-bottom: 24px;">
    <!-- 1. Branches -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Branches</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($restaurant['branches_count'] ?? 3) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Bengaluru region</div>
    </div>

    <!-- 2. Tables -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Tables</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($restaurant['table_stats']['total_tables'] ?? 36) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">
            <?= (int)($restaurant['table_stats']['available_tables'] ?? 32) ?> available
        </div>
    </div>

    <!-- 3. Menu Items -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Menu Items</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($restaurant['menu_stats']['total_items'] ?? 68) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">
            <?= (int)($restaurant['menu_stats']['available_items'] ?? 62) ?> available
        </div>
    </div>

    <!-- 4. Monthly Sales -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Monthly Sales</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value">₹8.42L</div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">↑ 14.2%</div>
    </div>

    <!-- 5. Average Rating -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Average Rating</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value">4.8 ★</div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">
            <?= (int)($restaurant['review_stats']['total_reviews'] ?? 128) ?> reviews
        </div>
    </div>
</div>

<!-- Bottom 3 Cards: Branches | Menu & Tables | Performance Chart -->
<div class="hb-grid-3">
    <!-- Card 1: Branches -->
    <div class="hb-stat-card" style="padding: 24px;">
        <h3 style="font-size: 15px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 16px 0;">
            Branches
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
            <?php if (!empty($restaurant['branches_list'])): ?>
                <?php foreach ($restaurant['branches_list'] as $b): ?>
                    <li style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-gray-700);">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-gray-500);"></span>
                        <?= e($b['name']) ?> · <?= (int)($b['table_count'] ?? 12) ?> tables
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-gray-700);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-gray-500);"></span>
                    Indiranagar · 12 tables
                </li>
                <li style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-gray-700);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-gray-500);"></span>
                    Koramangala · 14 tables
                </li>
                <li style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-gray-700);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-gray-500);"></span>
                    Whitefield · 10 tables
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Card 2: Menu & Tables -->
    <div class="hb-stat-card" style="padding: 24px;">
        <h3 style="font-size: 15px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 16px 0;">
            Menu & Tables
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
            <li style="font-size: 13px; color: var(--color-gray-700);">
                <?= (int)($restaurant['menu_stats']['total_items'] ?? 45) ?> menu items · <?= (int)($restaurant['menu_stats']['category_count'] ?? 8) ?> categories
            </li>
            <li style="font-size: 13px; color: var(--color-gray-700);">
                <?= (int)($restaurant['table_stats']['total_tables'] ?? 12) ?> table QR codes generated
            </li>
            <li style="font-size: 13px; color: var(--color-gray-700);">
                <?= max(0, (int)($restaurant['menu_stats']['total_items'] ?? 0) - (int)($restaurant['menu_stats']['available_items'] ?? 0)) ?> items temporarily unavailable
            </li>
        </ul>
    </div>

    <!-- Card 3: Performance -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Performance</h3>
            <span class="hb-chart-badge">This month</span>
        </div>
        <div class="hb-chart-canvas">
            <svg viewBox="0 0 350 140" width="100%" height="140" preserveAspectRatio="none">
                <path d="M 30 110 L 30 100 C 30 100, 80 110, 110 110 C 140 110, 150 75, 190 70 C 230 65, 250 85, 280 80 C 310 75, 320 60, 340 50" 
                      fill="none" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/>
                <path d="M 30 50 L 30 110 L 340 110" fill="none" stroke="#2E7D32" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</div>
