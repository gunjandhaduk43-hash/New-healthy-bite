<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Sales & Macro Analytics</h1>
        <p class="hb-page-subtitle">Executive revenue, order, and nutrition intelligence for <?= e($restaurant['name'] ?? 'Healthy Bite') ?>.</p>
    </div>
    <div class="hb-action-group">
        <a href="/owner/analytics/export?type=pdf&period=<?= urlencode($period) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" target="_blank" class="hb-btn hb-btn-primary">
            Export Report
        </a>
        <a href="/owner/analytics/export?type=csv&period=<?= urlencode($period) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="hb-btn hb-btn-outline" download>
            Download CSV
        </a>
        <a href="/owner/analytics/export?type=pdf&period=<?= urlencode($period) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" target="_blank" class="hb-btn hb-btn-outline">
            Download PDF
        </a>
    </div>
</div>

<!-- Time Filter Pills -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
        <a href="/owner/analytics?period=today" class="hb-btn <?= $period === 'today' ? 'hb-btn-primary' : 'hb-btn-outline' ?> hb-btn-sm" style="border-radius: 9999px; padding: 6px 18px; text-decoration: none;">Today</a>
        <a href="/owner/analytics?period=week" class="hb-btn <?= $period === 'week' ? 'hb-btn-primary' : 'hb-btn-outline' ?> hb-btn-sm" style="border-radius: 9999px; padding: 6px 18px; text-decoration: none;">This Week</a>
        <a href="/owner/analytics?period=month" class="hb-btn <?= $period === 'month' ? 'hb-btn-primary' : 'hb-btn-outline' ?> hb-btn-sm" style="border-radius: 9999px; padding: 6px 18px; text-decoration: none;">This Month</a>
        <button type="button" onclick="const r = document.getElementById('customDateRow'); r.style.display = (r.style.display === 'none' ? 'flex' : 'none')" class="hb-btn <?= $period === 'custom' ? 'hb-btn-primary' : 'hb-btn-outline' ?> hb-btn-sm" style="border-radius: 9999px; padding: 6px 18px; cursor: pointer;">Custom Date Range</button>
    </div>
    <div style="font-size: 13px; color: #64748b; font-weight: 500;">
        Active Window: <strong style="color:#0f172a;"><?= e($startDate) ?></strong> to <strong style="color:#0f172a;"><?= e($endDate) ?></strong>
    </div>
</div>

<!-- Custom Date Range Form (collapsible) -->
<div id="customDateRow" style="display: <?= $period === 'custom' ? 'flex' : 'none' ?>; gap: 12px; align-items: flex-end; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; flex-wrap: wrap;">
    <form method="GET" action="/owner/analytics" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="period" value="custom">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Start Date</label>
            <input type="date" name="start_date" value="<?= e($startDate) ?>" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; font-family: inherit;" required>
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">End Date</label>
            <input type="date" name="end_date" value="<?= e($endDate) ?>" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; font-family: inherit;" required>
        </div>
        <button type="submit" class="hb-btn hb-btn-primary hb-btn-sm" style="height: 34px; padding: 0 16px;">Apply Filter</button>
    </form>
</div>

<?php
    $sumRev          = (float)($summary['total_revenue'] ?? 0);
    $sumNet          = (float)($summary['net_sales'] ?? ($sumRev * 0.95));
    $sumOrders       = (int)($summary['total_orders'] ?? 0);
    $sumAov          = (float)($summary['aov'] ?? ($sumOrders > 0 ? $sumRev / $sumOrders : 0));
    $activeOrders    = (int)($summary['active_orders'] ?? 0);
    $completedOrders = (int)($summary['completed_orders'] ?? 0);
    $periodTitle     = match($period) {
        'today'  => "Today's metrics",
        'week'   => "Last 7 days metrics",
        'month'  => "Last 30 days metrics",
        'custom' => "Selected window metrics",
        default  => "Current period"
    };
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
        <div class="hb-stat-value"><?= format_price($sumRev) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;"><?= $periodTitle ?></div>
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
        <div class="hb-stat-value"><?= format_price($sumNet) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Net before taxes</div>
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
        <div class="hb-stat-value"><?= number_format($sumOrders) ?></div>
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
        <div class="hb-stat-value"><?= format_price($sumAov) ?></div>
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
            <h3 class="hb-chart-title">Revenue Trends · Daily Breakdown</h3>
            <span class="hb-chart-badge"><?= format_price($sumRev) ?> Total</span>
        </div>
        <div class="hb-chart-canvas" style="position: relative; height: 180px; display: flex; align-items: center; justify-content: center;">
            <?php if (empty($trends)): ?>
                <div style="color: #94a3b8; font-size: 13px; text-align: center; padding: 20px;">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 8px auto; display: block; opacity: 0.5;">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    No revenue data recorded in this period yet.
                </div>
            <?php else: ?>
                <?php
                    $maxRev = 1.0;
                    foreach ($trends as $t) {
                        if ((float)$t['daily_revenue'] > $maxRev) $maxRev = (float)$t['daily_revenue'];
                    }
                    $count = count($trends);
                    $pts = [];
                    $areaPts = [];
                    $svgWidth = 500;
                    $svgHeight = 150;
                    $padX = 35;
                    $padY = 20;
                    $plotWidth = $svgWidth - ($padX * 2);
                    $plotHeight = $svgHeight - ($padY * 2);

                    foreach ($trends as $idx => $t) {
                        $x = $count > 1 ? $padX + ($idx / ($count - 1)) * $plotWidth : ($svgWidth / 2);
                        $yRatio = (float)$t['daily_revenue'] / $maxRev;
                        $y = ($svgHeight - $padY) - ($yRatio * $plotHeight);
                        $pts[] = round($x, 1) . ',' . round($y, 1);
                    }
                    $pathD = 'M ' . implode(' L ', $pts);
                    $firstPt = explode(',', $pts[0]);
                    $lastPt = explode(',', end($pts));
                    $areaD = $pathD . ' L ' . $lastPt[0] . ',' . ($svgHeight - $padY) . ' L ' . $firstPt[0] . ',' . ($svgHeight - $padY) . ' Z';
                ?>
                <svg viewBox="0 0 500 160" width="100%" height="160" preserveAspectRatio="none" style="overflow: visible;">
                    <defs>
                        <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2E7D32" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#2E7D32" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <!-- Area fill -->
                    <path d="<?= $areaD ?>" fill="url(#revGrad)" />
                    <!-- Baseline -->
                    <line x1="20" y1="<?= $svgHeight - $padY ?>" x2="480" y2="<?= $svgHeight - $padY ?>" stroke="#e2e8f0" stroke-width="1" />
                    <!-- Trend line -->
                    <path d="<?= $pathD ?>" fill="none" stroke="#2E7D32" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Data points -->
                    <?php foreach ($trends as $idx => $t): ?>
                        <?php 
                            $ptCoord = explode(',', $pts[$idx]);
                            $revFmt = format_price((float)$t['daily_revenue']);
                            $dtFmt = date('M d', strtotime($t['order_date']));
                        ?>
                        <circle cx="<?= $ptCoord[0] ?>" cy="<?= $ptCoord[1] ?>" r="4.5" fill="#ffffff" stroke="#2E7D32" stroke-width="2.5">
                            <title><?= $dtFmt ?>: <?= $revFmt ?> (<?= (int)$t['order_count'] ?> orders)</title>
                        </circle>
                    <?php endforeach; ?>
                </svg>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Volume Chart -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Order Volume</h3>
            <span class="hb-chart-badge"><?= number_format($sumOrders) ?> Total Orders</span>
        </div>
        <div class="hb-chart-canvas" style="display: flex; align-items: flex-end; justify-content: space-around; height: 180px; padding: 20px 20px 10px 20px;">
            <?php if (empty($trends)): ?>
                <div style="color: #94a3b8; font-size: 13px; text-align: center; margin: auto;">
                    No order activity in this period.
                </div>
            <?php else: ?>
                <?php
                    $maxOrders = 1;
                    foreach ($trends as $t) {
                        if ((int)$t['order_count'] > $maxOrders) $maxOrders = (int)$t['order_count'];
                    }
                    $recentTrends = array_slice($trends, -7); // Show up to 7 bars
                ?>
                <?php foreach ($recentTrends as $t): ?>
                    <?php 
                        $barHeight = max(16, round(((int)$t['order_count'] / $maxOrders) * 115));
                        $isHigh = (int)$t['order_count'] === $maxOrders;
                        $dtLabel = date('m/d', strtotime($t['order_date']));
                    ?>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                        <span style="font-size: 11px; font-weight: 700; color: <?= $isHigh ? 'var(--color-primary)' : '#64748b' ?>;"><?= (int)$t['order_count'] ?></span>
                        <div style="width: 32px; height: <?= $barHeight ?>px; background: <?= $isHigh ? 'var(--color-primary)' : '#EAF5EA' ?>; border-radius: 6px; transition: height 0.3s;" title="<?= date('M d', strtotime($t['order_date'])) ?>: <?= (int)$t['order_count'] ?> orders"></div>
                        <span style="font-size: 10px; color: #94a3b8;"><?= $dtLabel ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 6 Macro / Nutrition KPI Cards (Including Sugar and Caffeine) -->
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

    <!-- 6. Total Caffeine Served -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Caffeine</span>
            <span class="hb-stat-icon-badge">☕</span>
        </div>
        <div class="hb-stat-value" style="font-size: 19px;">
            <?= number_format((float)($macroStats['total_caffeine'] ?? 0)) ?> mg
        </div>
        <div class="hb-stat-sub" style="color: #6366f1; font-weight: 600;">Energy served</div>
    </div>
</div>

<!-- Top-Selling Food Items & Payment Breakdown Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- Top-Selling Food Items -->
    <div class="hb-chart-card" style="padding: 20px;">
        <div class="hb-chart-header" style="margin-bottom: 16px;">
            <h3 class="hb-chart-title">Top-Selling Dishes</h3>
            <span class="hb-chart-badge">Leaderboard</span>
        </div>
        <?php if (empty($topItems)): ?>
            <div style="color: #94a3b8; font-size: 13px; text-align: center; padding: 24px;">
                No dishes sold yet in this time window.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($topItems as $idx => $it): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f8fafc; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 13px; font-weight: 700; color: #94a3b8; width: 16px;">#<?= $idx + 1 ?></span>
                            <?php if (!empty($it['image'])): ?>
                                <img src="<?= e($it['image']) ?>" alt="<?= e($it['name']) ?>" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 36px; height: 36px; border-radius: 6px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 16px;">🥗</div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight: 600; font-size: 14px; color: #0f172a;"><?= e($it['name']) ?></div>
                                <div style="font-size: 12px; color: #64748b;"><?= (int)$it['total_sold'] ?> orders placed</div>
                            </div>
                        </div>
                        <div style="font-weight: 700; font-size: 14px; color: var(--color-primary);">
                            <?= format_price((float)$it['total_revenue']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="hb-chart-card" style="padding: 20px;">
        <div class="hb-chart-header" style="margin-bottom: 16px;">
            <h3 class="hb-chart-title">Payment Settlement Breakdown</h3>
            <span class="hb-chart-badge">Transactions</span>
        </div>
        <table class="hb-table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Count</th>
                    <th>Total</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paymentStats)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:#64748b;">
                            No settled payments recorded in this period yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paymentStats as $p): ?>
                        <?php 
                            $methodName = strtoupper($p['payment_method'] ?? 'UPI');
                            $countVal = number_format((int)($p['count'] ?? 0));
                            $totalVal = format_price((float)($p['total'] ?? 0));
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
</div>
