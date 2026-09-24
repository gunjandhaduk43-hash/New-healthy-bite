<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Sales & Macro Analytics</h1>
        <p class="hb-page-subtitle">Executive revenue, order and nutrition intelligence.</p>
    </div>
    <div class="hb-action-group">
        <button type="button" class="hb-btn hb-btn-primary" onclick="window.print()">
            Export Report
        </button>
        <button type="button" class="hb-btn hb-btn-outline" onclick="window.print()">
            Download CSV
        </button>
        <button type="button" class="hb-btn hb-btn-outline" onclick="window.print()">
            Download PDF
        </button>
    </div>
</div>

<!-- Time Filter Pills -->
<div style="display: flex; gap: 8px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 4px;">
    <button type="button" class="hb-btn hb-btn-outline hb-btn-sm" style="background:#fff; border-radius: 9999px; padding: 6px 16px;">Today</button>
    <button type="button" class="hb-btn hb-btn-outline hb-btn-sm" style="background:#fff; border-radius: 9999px; padding: 6px 16px;">This Week</button>
    <button type="button" class="hb-btn hb-btn-primary hb-btn-sm" style="border-radius: 9999px; padding: 6px 16px;">This Month</button>
    <button type="button" class="hb-btn hb-btn-outline hb-btn-sm" style="background:#fff; border-radius: 9999px; padding: 6px 16px;">Custom Date Range</button>
</div>

<?php
    $allTimeRev      = (float)($todayStats['all_time_revenue'] ?? $todayStats['total_revenue'] ?? 0);
    $allTimeOrders   = (int)($todayStats['all_time_orders'] ?? $todayStats['total_orders'] ?? 0);
    $aov             = $allTimeOrders > 0 ? ($allTimeRev / $allTimeOrders) : 0;
    $activeOrders    = (int)($todayStats['active_orders'] ?? 0);
    $completedOrders = (int)($todayStats['completed_orders'] ?? 0);
    $todayRev        = (float)($todayStats['total_revenue'] ?? 0);
?>

<!-- 6 Top KPI Cards -->
<div class="hb-grid-6" style="margin-bottom: 24px;">
    <!-- 1. Total Sales -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Revenue</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= format_price($allTimeRev) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">All-time gross</div>
    </div>

    <!-- 2. Net Sales -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Net Sales</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= format_price($allTimeRev * 0.95) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">After 5% taxes</div>
    </div>

    <!-- 3. Total Orders -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Orders</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= number_format($allTimeOrders) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Verified orders</div>
    </div>

    <!-- 4. Average Order Value -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Average Order Value</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= format_price($aov) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Per order average</div>
    </div>

    <!-- 5. Active Orders -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Active Orders</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= number_format($activeOrders) ?></div>
        <div class="hb-stat-sub" style="color: #f59e0b; font-weight: 600;">In live kitchen queue</div>
    </div>

    <!-- 6. Completed Orders -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Completed Orders</span>
            <span class="hb-stat-icon-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                    <polyline points="16 7 22 7 22 13"></polyline>
                </svg>
            </span>
        </div>
        <div class="hb-stat-value"><?= number_format($completedOrders) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Fulfilled successfully</div>
    </div>
</div>

<!-- 2 Middle Charts -->
<div class="hb-grid-2" style="margin-bottom: 24px;">
    <!-- Revenue Trends Chart -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Revenue Trends · Daily / Weekly / Monthly</h3>
            <span class="hb-chart-badge"><?= format_price($allTimeRev) ?> Gross</span>
        </div>
        <div class="hb-chart-canvas">
            <svg viewBox="0 0 500 180" width="100%" height="180" preserveAspectRatio="none">
                <path d="M 40 150 L 40 140 C 40 140, 100 150, 140 150 C 180 150, 180 125, 230 115 C 280 105, 310 130, 360 120 C 410 110, 430 95, 470 70" 
                      fill="none" stroke="#2E7D32" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 40 70 L 40 150 L 470 150" fill="none" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <!-- Order Volume Chart -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Order Volume</h3>
            <span class="hb-chart-badge"><?= number_format($allTimeOrders) ?> Total Orders</span>
        </div>
        <div class="hb-chart-canvas" style="display: flex; align-items: flex-end; justify-content: space-between; height: 180px; padding: 20px 30px 10px 30px;">
            <div style="width: 44px; height: 35px; background: #EAF5EA; border-radius: 6px;"></div>
            <div style="width: 44px; height: 60px; background: #EAF5EA; border-radius: 6px;"></div>
            <div style="width: 44px; height: 45px; background: #EAF5EA; border-radius: 6px;"></div>
            <div style="width: 44px; height: 90px; background: #EAF5EA; border-radius: 6px;"></div>
            <div style="width: 44px; height: 55px; background: #EAF5EA; border-radius: 6px;"></div>
            <div style="width: 44px; height: 140px; background: #2E7D32; border-radius: 6px;"></div>
            <div style="width: 44px; height: 75px; background: #EAF5EA; border-radius: 6px;"></div>
        </div>
    </div>
</div>

<!-- 6 Macro / Nutrition KPI Cards (Including Sugar) -->
<div class="hb-grid-6" style="margin-bottom: 24px;">
    <!-- 1. Total Calories -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Calories</span>
            <span class="hb-stat-icon-badge">🔥</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_calories'] ?? 0)) ?> kcal
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Dishes served</div>
    </div>

    <!-- 2. Total Protein -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Protein</span>
            <span class="hb-stat-icon-badge">🥩</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_protein'] ?? 0), 1) ?> g
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Lean nutrition</div>
    </div>

    <!-- 3. Total Carbs -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Carbs</span>
            <span class="hb-stat-icon-badge">🌾</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_carbs'] ?? 0), 1) ?> g
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Complex carbs</div>
    </div>

    <!-- 4. Total Fat -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Fat</span>
            <span class="hb-stat-icon-badge">🥑</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_fat'] ?? 0), 1) ?> g
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Healthy fats</div>
    </div>

    <!-- 5. Total Sugar Sold -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Sugar Sold</span>
            <span class="hb-stat-icon-badge">🍬</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_sugar'] ?? 0), 1) ?> g
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Tracked sugars</div>
    </div>

    <!-- 6. Average Per Order -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Avg Per Order</span>
            <span class="hb-stat-icon-badge">📊</span>
        </div>
        <div class="hb-stat-value" style="font-size: 14px; line-height: 1.4; margin: 6px 0;">
            <?= round((float)($macroStats['avg_calories'] ?? 0)) ?> kcal · <?= round((float)($macroStats['avg_sugar'] ?? 0), 1) ?>g sugar
        </div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Avg order macros</div>
    </div>
</div>

<!-- Bottom Payment Method Table -->
<div class="hb-table-card-container">
    <table class="hb-table">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Payment Count</th>
                <th>Total</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($paymentStats)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:24px; color:#64748b;">
                        No settled payments recorded yet. Orders will appear here as payments complete.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($paymentStats as $p): ?>
                    <?php 
                        $methodName = strtoupper($p['payment_method'] ?? 'UPI');
                        $countVal = number_format((int)($p['count'] ?? 0));
                        $totalVal = '₹' . number_format((float)($p['total'] ?? 0), 2);
                        $pctVal = number_format((float)($p['percentage'] ?? 0), 1) . '%';
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--color-gray-900);"><?= e($methodName) ?></td>
                        <td style="color: var(--color-gray-700);"><?= $countVal ?></td>
                        <td style="color: var(--color-gray-900); font-weight: 600;"><?= $totalVal ?></td>
                        <td style="color: var(--color-gray-600);"><?= $pctVal ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
