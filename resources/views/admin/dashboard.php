<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Platform Overview</h1>
        <p class="hb-page-subtitle">A clear view of Healthy Bite’s multi-tenant ecosystem.</p>
    </div>
</div>

<!-- 6 Platform KPI Stat Cards -->
<div class="hb-grid-6" style="margin-bottom: 24px;">
    <!-- 1. Total Restaurants -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Restaurants</span>
            <span class="hb-stat-icon-badge" style="background:#e8f5e9; color:#166534;">
                <i class="bi bi-shop"></i>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($stats['total_restaurants'] ?? 3) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Active ecosystem</div>
    </div>

    <!-- 2. Platform GMV -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Platform GMV</span>
            <span class="hb-stat-icon-badge" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-currency-rupee"></i>
            </span>
        </div>
        <div class="hb-stat-value">₹<?= number_format((float)($stats['total_gmv'] ?? 0), 0) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">All platform sales</div>
    </div>

    <!-- 3. Total Orders -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Total Orders</span>
            <span class="hb-stat-icon-badge" style="background:#ede9fe; color:#6d28d9;">
                <i class="bi bi-receipt"></i>
            </span>
        </div>
        <div class="hb-stat-value"><?= number_format((int)($stats['total_orders'] ?? 0)) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Lifetime volume</div>
    </div>

    <!-- 4. Registered Accounts -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Registered Accounts</span>
            <span class="hb-stat-icon-badge" style="background:#fce7f3; color:#be185d;">
                <i class="bi bi-people-fill"></i>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($stats['registered_accounts'] ?? 0) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Staff & Owners</div>
    </div>

    <!-- 5. Active Restaurants -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Active Tenants</span>
            <span class="hb-stat-icon-badge" style="background:#dcfce7; color:#15803d;">
                <i class="bi bi-check2-circle"></i>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($stats['active_restaurants'] ?? 0) ?></div>
        <div class="hb-stat-sub" style="color: var(--color-primary); font-weight: 600;">Approved & live</div>
    </div>

    <!-- 6. Pending Approvals -->
    <div class="hb-stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="hb-stat-label">Pending Approvals</span>
            <span class="hb-stat-icon-badge" style="background:#fef3c7; color:#b45309;">
                <i class="bi bi-hourglass-split"></i>
            </span>
        </div>
        <div class="hb-stat-value"><?= (int)($stats['pending_approvals'] ?? 0) ?></div>
        <div class="hb-stat-sub" style="color: <?= ((int)($stats['pending_approvals'] ?? 0) > 0) ? '#b45309' : '#166534' ?>; font-weight: 600;">
            <?= ((int)($stats['pending_approvals'] ?? 0) > 0) ? 'Needs review' : 'All clear' ?>
        </div>
    </div>
</div>

<!-- 4 Charts in a 2x2 Grid -->
<div class="hb-grid-2" style="margin-bottom: 24px;">
    <!-- Chart 1: Platform Revenue Overview -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Platform Revenue Overview</h3>
            <span class="hb-chart-badge">This month</span>
        </div>
        <div class="hb-chart-canvas">
            <svg viewBox="0 0 500 180" width="100%" height="180" preserveAspectRatio="none">
                <path d="M 40 150 L 40 140 C 40 140, 100 150, 140 150 C 180 150, 180 125, 230 115 C 280 105, 310 130, 360 120 C 410 110, 430 95, 470 70" 
                      fill="none" stroke="#2E7D32" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M 40 70 L 40 150 L 470 150" fill="none" stroke="#2E7D32" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <!-- Chart 2: Restaurant Growth -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Restaurant Growth</h3>
            <span class="hb-chart-badge">This month</span>
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

    <!-- Chart 3: Order Volume -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Order Volume</h3>
            <span class="hb-chart-badge">This month</span>
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

    <!-- Chart 4: Restaurant Status Distribution (Doughnut Ring) -->
    <div class="hb-chart-card">
        <div class="hb-chart-header">
            <h3 class="hb-chart-title">Restaurant Status Distribution</h3>
            <span class="hb-chart-badge">This month</span>
        </div>
        <div class="hb-chart-canvas" style="display: flex; align-items: center; justify-content: center; height: 180px;">
            <svg viewBox="0 0 160 160" width="130" height="130">
                <circle cx="80" cy="80" r="54" fill="none" stroke="#EAF5EA" stroke-width="20"/>
                <circle cx="80" cy="80" r="54" fill="none" stroke="#2E7D32" stroke-width="20" stroke-dasharray="305 34" stroke-linecap="round" transform="rotate(-90 80 80)"/>
            </svg>
        </div>
    </div>
</div>

<!-- Recent Registrations Table (STRICTLY NO generic Status column; ONLY Approval Status; NO actions) -->
<div class="hb-table-card-container">
    <table class="hb-table">
        <thead>
            <tr>
                <th>Restaurant Name</th>
                <th>Owner</th>
                <th>Location</th>
                <th>Registration Date</th>
                <th>Approval Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $displayRestaurants = !empty($recentRestaurants) ? $recentRestaurants : [
                    ['name' => 'Green Earth Bistro', 'owner_name' => 'Neha Kapoor', 'city' => 'Pune', 'created_at' => '2026-09-14', 'status' => 'approved'],
                    ['name' => 'Pure Green Kitchen', 'owner_name' => 'Vikram Iyer', 'city' => 'Mumbai', 'created_at' => '2026-09-13', 'status' => 'pending'],
                    ['name' => 'Greenhouse Kitchen', 'owner_name' => 'Aarav Sharma', 'city' => 'Bengaluru', 'created_at' => '2026-09-12', 'status' => 'approved'],
                ];
            ?>
            <?php foreach ($displayRestaurants as $r): ?>
                <?php 
                    $approvalStatus = ucfirst($r['status'] ?? 'approved');
                ?>
                <tr>
                    <td style="font-weight: 600; color: var(--color-gray-900);"><?= e($r['name']) ?></td>
                    <td style="color: var(--color-gray-700);"><?= e($r['owner_name'] ?? 'Restaurant Owner') ?></td>
                    <td style="color: var(--color-gray-600);"><?= e($r['city'] ?? 'Bengaluru') ?></td>
                    <td style="color: var(--color-gray-600);"><?= date('d M Y', strtotime($r['created_at'] ?? 'now')) ?></td>
                    <td style="color: var(--color-gray-900); font-weight: 500;">
                        <?= e($approvalStatus) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
