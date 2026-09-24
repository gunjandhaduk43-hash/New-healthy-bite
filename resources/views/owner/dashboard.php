<?php if (!empty($flashSuccess)): ?>
    <div class="alert-banner success" style="margin-bottom: 20px; padding: 12px 16px; background: #EAF5EA; color: #166534; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= e($flashSuccess) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($flashError)): ?>
    <div class="alert-banner error" style="margin-bottom: 20px; padding: 12px 16px; background: #FEE2E2; color: #DC2626; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span><?= e($flashError) ?></span>
    </div>
<?php endif; ?>

<!-- Page Heading & Greeting -->
<div class="page-header-row">
    <div>
        <h1 class="page-greeting">Good morning, <?= e(explode(' ', $user['name'] ?? 'Aarav')[0]) ?> 👋</h1>
        <p class="page-subtitle">Here's what's happening at your restaurant today.</p>
    </div>
</div>

<!-- 6 Stat Cards in a row -->
<div class="stats-grid-6">
    <!-- 1. Today's Sales -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Today's Sales</span>
            <div class="stat-card-icon-box" style="background:#e8f5e9; color:#1b5e20;">
                <i class="bi bi-currency-rupee"></i>
            </div>
        </div>
        <div class="stat-card-value">₹<?= number_format((float)($stats['total_revenue'] > 0 ? $stats['total_revenue'] : 24850), 0) ?></div>
        <div class="stat-card-trend">
            <i class="bi bi-arrow-up-short"></i> 12.5% vs yesterday
        </div>
    </div>

    <!-- 2. Today's Orders -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Today's Orders</span>
            <div class="stat-card-icon-box" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-receipt"></i>
            </div>
        </div>
        <div class="stat-card-value"><?= (int)($stats['total_orders'] > 0 ? $stats['total_orders'] : 128) ?> Orders</div>
        <div class="stat-card-trend">
            <i class="bi bi-arrow-up-short"></i> 8.2% vs yesterday
        </div>
    </div>

    <!-- 3. Active Orders -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Active Orders</span>
            <div class="stat-card-icon-box" style="background:#fef3c7; color:#b45309;">
                <i class="bi bi-fire"></i>
            </div>
        </div>
        <div class="stat-card-value"><?= (int)($stats['active_orders'] > 0 ? $stats['active_orders'] : 12) ?> Orders</div>
        <div class="stat-card-trend" style="color:#b45309; font-weight:600;">
            Live now
        </div>
    </div>

    <!-- 4. Completed Orders -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Completed Orders</span>
            <div class="stat-card-icon-box" style="background:#ede9fe; color:#6d28d9;">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?= (int)($stats['completed_orders'] > 0 ? $stats['completed_orders'] : 96) ?> Orders</div>
        <div class="stat-card-trend" style="color:#166534; font-weight:500;">
            75% completion
        </div>
    </div>

    <!-- 5. Total Customers -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Customers</span>
            <div class="stat-card-icon-box" style="background:#fce7f3; color:#be185d;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="stat-card-value">342</div>
        <div class="stat-card-trend">
            <i class="bi bi-arrow-up-short"></i> 18 today
        </div>
    </div>

    <!-- 6. Average Order Value -->
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Average Order Value</span>
            <div class="stat-card-icon-box" style="background:#f1f5f9; color:#475569;">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
        </div>
        <div class="stat-card-value">₹285</div>
        <div class="stat-card-trend">
            <i class="bi bi-arrow-up-short"></i> 4.1%
        </div>
    </div>
</div>

<!-- Middle Row: Sales Overview & Order Overview Charts -->
<div class="dashboard-row-2">
    <!-- Sales Overview Line Chart Card -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">Sales Overview • Daily / Weekly / Monthly</div>
            <span class="chart-card-badge">This month</span>
        </div>
        <div class="chart-svg-container">
            <svg viewBox="0 0 500 150" width="100%" height="100%" preserveAspectRatio="none">
                <!-- Axes lines -->
                <path d="M 30 10 L 30 130 L 480 130" fill="none" stroke="#1B5E20" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Smooth Bezier Trend Curve -->
                <path d="M 110 115 C 140 70, 160 70, 190 90 C 220 110, 250 90, 280 65 C 310 40, 340 70, 370 70 C 400 70, 420 50, 440 25" 
                      fill="none" stroke="#166534" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <!-- Order Overview Donut Chart Card -->
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-card-title">Order Overview • 128 total</div>
            <span class="chart-card-badge">This month</span>
        </div>
        <div class="donut-chart-container">
            <svg viewBox="0 0 160 160" width="140" height="140">
                <!-- Outer background track -->
                <circle cx="80" cy="80" r="55" fill="transparent" stroke="#EAF5EA" stroke-width="22" />
                <!-- Green progress ring -->
                <circle cx="80" cy="80" r="55" fill="transparent" stroke="#166534" stroke-width="22"
                        stroke-dasharray="345" stroke-dashoffset="65" stroke-linecap="round"
                        transform="rotate(-90 80 80)" />
            </svg>
        </div>
    </div>
</div>

<!-- Bottom Row: Popular Food Items & Recent Orders -->
<div class="dashboard-row-2">
    <!-- Popular Food Items -->
    <div class="popular-items-card">
        <div class="chart-card-header" style="margin-bottom:8px;">
            <div class="chart-card-title">Popular Food Items</div>
        </div>
        <div class="popular-items-list">
            <?php if (!empty($popularItems)): ?>
                <?php foreach ($popularItems as $item): ?>
                    <div class="popular-item-row">
                        <div class="popular-item-left">
                            <img src="<?= !empty($item['image']) ? e($item['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=120&auto=format&fit=crop&q=80' ?>" alt="<?= e($item['name']) ?>" class="popular-item-img">
                            <div class="popular-item-name"><?= e($item['name']) ?></div>
                        </div>
                        <div class="popular-item-sales"><?= (int)$item['total_sold'] ?> sold • ₹<?= number_format((float)$item['total_revenue'], 0) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="popular-item-row">
                    <div class="popular-item-left">
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=120&auto=format&fit=crop&q=80" alt="Chicken Rice Bowl" class="popular-item-img">
                        <div class="popular-item-name">Chicken Rice Bowl</div>
                    </div>
                    <div class="popular-item-sales">68 sold • ₹8,160</div>
                </div>
                <div class="popular-item-row">
                    <div class="popular-item-left">
                        <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999?w=120&auto=format&fit=crop&q=80" alt="Paneer Protein Bowl" class="popular-item-img">
                        <div class="popular-item-name">Paneer Protein Bowl</div>
                    </div>
                    <div class="popular-item-sales">59 sold • ₹6,920</div>
                </div>
                <div class="popular-item-row">
                    <div class="popular-item-left">
                        <img src="https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=120&auto=format&fit=crop&q=80" alt="Grilled Chicken Bowl" class="popular-item-img">
                        <div class="popular-item-name">Grilled Chicken Bowl</div>
                    </div>
                    <div class="popular-item-sales">50 sold • ₹5,680</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Orders (Desktop Table + Mobile Cards) -->
    <div class="table-container-card">
        <div class="chart-card-header" style="padding: 16px 20px; margin: 0; border-bottom: 1px solid #E5E7EB; background: #FFFFFF;">
            <div class="chart-card-title" style="display:flex; align-items:center; gap:8px;">
                <i class="bi bi-clock-history" style="color:var(--hb-primary, #166534);"></i>
                <span>Recent Orders</span>
            </div>
            <a href="<?= url('/owner/orders') ?>" class="btn-hb btn-hb-sm btn-hb-outline-green" style="text-decoration:none;">
                View All Orders &rarr;
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table class="dash-table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Table</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                            <tr>
                                <td style="font-weight:700; color:#111827;">
                                    <a href="<?= url('/owner/orders') ?>" style="color:var(--hb-primary, #166534); text-decoration:none;">
                                        <?= e($order['order_number']) ?>
                                    </a>
                                </td>
                                <td><?= e($order['customer_name'] ?? 'Guest') ?></td>
                                <td><?= $order['order_type'] === 'dine_in' ? 'Dine-In' : 'Takeaway' ?></td>
                                <td><?= !empty($order['table_number']) ? e($order['table_number']) : '—' ?></td>
                                <td style="font-weight:700;">₹<?= number_format((float)$order['total_amount'], 0) ?></td>
                                <td>
                                    <span class="status-pill <?= e($order['order_status']) ?>">
                                        <?= ucfirst(e($order['order_status'])) ?>
                                    </span>
                                </td>
                                <td style="color:#6B7280; font-size:12.5px;"><?= date('h:i A', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:32px; color:#6B7280;">No recent orders recorded today.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
