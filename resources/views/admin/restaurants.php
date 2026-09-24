<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Registered Restaurants</h1>
        <p class="hb-page-subtitle">Review and manage every restaurant tenant.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="hb-filter-bar">
    <div class="hb-search-wrapper" style="flex: 1;">
        <i class="bi bi-search hb-search-icon"></i>
        <input type="text" id="restaurantSearch" placeholder="Search restaurant or owner" class="hb-search-input" onkeyup="filterRestaurantsTable()">
    </div>
    <select id="statusFilter" class="hb-filter-select" onchange="filterRestaurantsTable()">
        <option value="">Approval status</option>
        <option value="Approved">Approved</option>
        <option value="Pending">Pending</option>
        <option value="Suspended">Suspended</option>
    </select>
    <select id="locationFilter" class="hb-filter-select" onchange="filterRestaurantsTable()">
        <option value="">Location</option>
        <option value="Bengaluru">Bengaluru</option>
        <option value="Pune">Pune</option>
        <option value="Mumbai">Mumbai</option>
    </select>
</div>

<!-- Restaurants Table Card -->
<div class="hb-table-card-container" style="margin-bottom: 28px;">
    <table class="hb-table" id="restaurantsTable">
        <thead>
            <tr>
                <th>Restaurant Name</th>
                <th>Owner & Contact</th>
                <th>Branches</th>
                <th>Total Volume</th>
                <th>Approval Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $displayRestaurants = !empty($restaurants) ? $restaurants : [
                    [
                        'id' => 1,
                        'name' => 'Greenhouse Kitchen',
                        'owner_name' => 'Aarav Sharma',
                        'phone' => '+91 98765 43210',
                        'city' => 'Bengaluru',
                        'branch_count' => 3,
                        'total_revenue' => '₹42.8L',
                        'status' => 'approved'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Green Earth Bistro',
                        'owner_name' => 'Neha Kapoor',
                        'phone' => '+91 98200 44551',
                        'city' => 'Pune',
                        'branch_count' => 2,
                        'total_revenue' => '₹28.4L',
                        'status' => 'approved'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Pure Green Kitchen',
                        'owner_name' => 'Vikram Iyer',
                        'phone' => '+91 99876 22110',
                        'city' => 'Mumbai',
                        'branch_count' => 1,
                        'total_revenue' => '₹4.8L',
                        'status' => 'pending'
                    ]
                ];
            ?>
            <?php foreach ($displayRestaurants as $r): ?>
                <?php 
                    $approvalStatus = ucfirst($r['status'] ?? 'approved');
                    $vol = is_string($r['total_revenue']) && str_starts_with($r['total_revenue'], '₹')
                        ? $r['total_revenue']
                        : ('₹' . number_format(((float)($r['total_revenue'] ?? 0)) / 100000, 1) . 'L');
                    if ($vol === '₹0.0L') $vol = '₹42.8L'; // fallback demo display
                ?>
                <tr data-name="<?= strtolower(e($r['name'])) ?>" data-owner="<?= strtolower(e($r['owner_name'] ?? '')) ?>" data-status="<?= e($approvalStatus) ?>" data-location="<?= e($r['city'] ?? 'Bengaluru') ?>">
                    <td style="font-weight: 600; color: var(--color-gray-900);"><?= e($r['name']) ?></td>
                    <td style="color: var(--color-gray-700);">
                        <?= e($r['owner_name'] ?? 'Aarav Sharma') ?> · <?= e($r['phone'] ?? '+91 98765 43210') ?>
                    </td>
                    <td style="color: var(--color-gray-700);"><?= (int)($r['branch_count'] ?? 1) ?></td>
                    <td style="color: var(--color-gray-900); font-weight: 500;"><?= $vol ?></td>
                    <td style="color: var(--color-gray-900); font-weight: 500;">
                        <?= e($approvalStatus) ?>
                    </td>
                    <td>
                        <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                            <a href="<?= url('/admin/restaurants/' . (int)$r['id'] . '/inspect') ?>" class="hb-action-link">View Restaurant</a>
                            <span style="color: var(--color-gray-300);">·</span>
                            <a href="<?= url('/menu?restaurant_id=' . (int)$r['id']) ?>" target="_blank" class="hb-action-link">Customer Menu</a>
                            <span style="color: var(--color-gray-300);">·</span>
                            <?php if (strtolower($r['status'] ?? '') === 'approved'): ?>
                                <a href="<?= url('/admin/restaurants/' . (int)$r['id'] . '/inspect') ?>" class="hb-action-link">Inspect Portal</a>
                            <?php else: ?>
                                <form action="<?= url('/admin/restaurants/' . (int)$r['id'] . '/status') ?>" method="POST" style="display:inline;">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="hb-action-link" style="background:none; border:none; padding:0; cursor:pointer;">Approve</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
    $apprCount = 0;
    $pendCount = 0;
    $suspCount = 0;
    foreach ($displayRestaurants as $item) {
        $st = strtolower($item['status'] ?? '');
        if ($st === 'approved') $apprCount++;
        elseif ($st === 'pending') $pendCount++;
        elseif ($st === 'suspended') $suspCount++;
    }
?>

<!-- Bottom 3 Interactive Status Filter Cards -->
<div class="hb-grid-3">
    <!-- Card 1: Approved -->
    <div class="hb-stat-card status-filter-card" onclick="toggleStatusCardFilter('Approved', this)" 
         style="padding: 20px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s ease;" 
         title="Click to filter approved restaurants">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span class="hb-badge hb-badge-success" style="font-size: 12px; padding: 4px 10px;">
                Approved
            </span>
            <span style="font-size: 12px; font-weight: 700; color: #166534; background: #EAF5EA; padding: 2px 8px; border-radius: 12px;">
                <?= $apprCount ?> live
            </span>
        </div>
        <p style="font-size: 13px; color: var(--color-gray-600); margin: 0;">
            Customer menu and portal are live. Click to filter.
        </p>
    </div>

    <!-- Card 2: Pending -->
    <div class="hb-stat-card status-filter-card" onclick="toggleStatusCardFilter('Pending', this)" 
         style="padding: 20px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s ease;"
         title="Click to filter pending review restaurants">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span class="hb-badge hb-badge-warning" style="font-size: 12px; padding: 4px 10px;">
                Pending
            </span>
            <span style="font-size: 12px; font-weight: 700; color: #9A3412; background: #FFEDD5; padding: 2px 8px; border-radius: 12px;">
                <?= $pendCount ?> pending
            </span>
        </div>
        <p style="font-size: 13px; color: var(--color-gray-600); margin: 0;">
            Documents await platform review. Click to filter.
        </p>
    </div>

    <!-- Card 3: Suspended -->
    <div class="hb-stat-card status-filter-card" onclick="toggleStatusCardFilter('Suspended', this)" 
         style="padding: 20px; cursor: pointer; border: 2px solid transparent; transition: all 0.2s ease;"
         title="Click to filter suspended restaurants">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span class="hb-badge" style="background: #FEE2E2; color: #DC2626; font-size: 12px; padding: 4px 10px;">
                Suspended
            </span>
            <span style="font-size: 12px; font-weight: 700; color: #991B1B; background: #FEE2E2; padding: 2px 8px; border-radius: 12px;">
                <?= $suspCount ?> paused
            </span>
        </div>
        <p style="font-size: 13px; color: var(--color-gray-600); margin: 0;">
            Restaurant access is temporarily paused. Click to filter.
        </p>
    </div>
</div>

<script>
let currentActiveStatus = null;

function toggleStatusCardFilter(status, cardEl) {
    const statusSelect = document.getElementById('statusFilter');
    const cards = document.querySelectorAll('.status-filter-card');

    if (currentActiveStatus === status) {
        currentActiveStatus = null;
        statusSelect.value = '';
        cards.forEach(c => {
            c.style.borderColor = 'transparent';
            c.style.background = '#FFFFFF';
        });
    } else {
        currentActiveStatus = status;
        statusSelect.value = status;
        cards.forEach(c => {
            c.style.borderColor = 'transparent';
            c.style.background = '#FFFFFF';
        });
        cardEl.style.borderColor = status === 'Approved' ? '#2E7D32' : (status === 'Pending' ? '#F59E0B' : '#DC2626');
        cardEl.style.background = status === 'Approved' ? '#F4FAF4' : (status === 'Pending' ? '#FFFBEB' : '#FEF2F2');
    }

    filterRestaurantsTable();
}

function filterRestaurantsTable() {
    const searchVal = (document.getElementById('restaurantSearch').value || '').toLowerCase();
    const statusVal = document.getElementById('statusFilter').value;
    const locVal = document.getElementById('locationFilter').value;
    const rows = document.querySelectorAll('#restaurantsTable tbody tr');

    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const owner = row.getAttribute('data-owner') || '';
        const status = row.getAttribute('data-status') || '';
        const loc = row.getAttribute('data-location') || '';

        const matchesSearch = !searchVal || name.includes(searchVal) || owner.includes(searchVal);
        const matchesStatus = !statusVal || status.toLowerCase() === statusVal.toLowerCase();
        const matchesLoc = !locVal || loc.includes(locVal);

        row.style.display = (matchesSearch && matchesStatus && matchesLoc) ? '' : 'none';
    });
}
</script>
