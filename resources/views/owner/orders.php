<?php if (!empty($flashSuccess)): ?>
    <div class="alert-banner success" style="margin-bottom: 20px; padding: 12px 16px; background: #EAF5EA; color: #166534; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= e($flashSuccess) ?></span>
    </div>
<?php endif; ?>

<!-- Page Header Row -->
<div class="page-header-row" style="margin-bottom: 16px;">
    <div>
        <h1 class="page-greeting">Live Orders Monitor</h1>
        <p class="page-subtitle">Complete real-time order register with customer details and item snapshots.</p>
    </div>
    <div class="header-action-group">
        <a href="<?= url('/owner/kitchen-orders') ?>" class="btn-hb btn-hb-outline-green" style="text-decoration:none;">
            <i class="bi bi-fire"></i> Open Kitchen Board
        </a>
        <button onclick="window.location.reload()" class="btn-hb btn-hb-primary" title="Refresh Live Orders">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<?php
    $statusCounts = [
        'placed' => 0,
        'accepted' => 0,
        'preparing' => 0,
        'ready' => 0,
        'completed' => 0,
        'cancelled' => 0,
    ];
    if (!empty($orders)) {
        foreach ($orders as $o) {
            $st = strtolower($o['order_status'] ?? '');
            if (isset($statusCounts[$st])) {
                $statusCounts[$st]++;
            }
        }
    }
?>

<!-- Status Filter Pills Bar (Prominent at top) -->
<div class="status-legend-bar" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; padding: 10px 16px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px;">
    <button type="button" class="legend-pill-btn active" onclick="filterByStatusLegend('all', this)" style="padding:6px 14px; border-radius:9999px; border:1px solid #166534; background:#166534; color:#fff; font-size:12.5px; font-weight:700; cursor:pointer;">
        All (<?= count($orders) ?>)
    </button>
    <button type="button" class="status-pill placed legend-pill-btn" onclick="filterByStatusLegend('placed', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Placed (<?= $statusCounts['placed'] ?>)
    </button>
    <button type="button" class="status-pill accepted legend-pill-btn" onclick="filterByStatusLegend('accepted', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Accepted (<?= $statusCounts['accepted'] ?>)
    </button>
    <button type="button" class="status-pill preparing legend-pill-btn" onclick="filterByStatusLegend('preparing', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Preparing (<?= $statusCounts['preparing'] ?>)
    </button>
    <button type="button" class="status-pill ready legend-pill-btn" onclick="filterByStatusLegend('ready', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Ready (<?= $statusCounts['ready'] ?>)
    </button>
    <button type="button" class="status-pill completed legend-pill-btn" onclick="filterByStatusLegend('completed', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Completed (<?= $statusCounts['completed'] ?>)
    </button>
    <button type="button" class="status-pill cancelled legend-pill-btn" onclick="filterByStatusLegend('cancelled', this)" style="cursor: pointer; border: 1px solid transparent; font-weight: 600;">
        Cancelled (<?= $statusCounts['cancelled'] ?>)
    </button>
</div>

<!-- Filter & Search Bar Card -->
<div class="filter-bar-card" style="margin-bottom: 16px;">
    <div class="search-input-wrap">
        <i class="bi bi-search"></i>
        <input type="text" class="search-input" id="orderSearchInput" placeholder="Search by order #, customer name, or table...">
    </div>

    <select class="filter-select" id="orderTypeFilter">
        <option value="all">All Order Types</option>
        <option value="dine_in">Dine-In</option>
        <option value="takeaway">Takeaway</option>
    </select>

    <div style="font-size:12.5px; color:#6B7280; margin-left:auto; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-info-circle"></i> Click any row to view full items breakdown
    </div>
</div>

<!-- Live Orders Table -->
<div class="table-container-card" style="overflow-x: auto;">
    <table class="dash-table" id="live-orders-table" style="min-width: 760px;">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Order Type</th>
                <th>Table</th>
                <th>Items</th>
                <th>Total Paid</th>
                <th>Status</th>
                <th>Placed At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr class="clickable-order-row" onclick="viewOrderDetails(<?= (int)$order['id'] ?>)" style="cursor: pointer; transition: background 0.15s;" title="Click to view full order breakdown" data-status="<?= strtolower($order['order_status']) ?>" data-type="<?= strtolower($order['order_type']) ?>" data-search="<?= strtolower(e($order['order_number'] . ' ' . ($order['customer_name'] ?? '') . ' ' . ($order['table_number'] ?? ''))) ?>">
                        <td style="font-weight:700; color:#111827;">
                            <span style="color:var(--brand-primary, #166534); font-family:monospace; font-size:14px;">#<?= e($order['order_number']) ?></span>
                        </td>
                        <td style="font-weight:600; color:#1f2937;"><?= e($order['customer_name'] ?? 'Guest') ?></td>
                        <td>
                            <?php if ($order['order_type'] === 'dine_in'): ?>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-weight:500; font-size:13px; color:#1e293b;">
                                    <i class="bi bi-cup-hot"></i> Dine-In
                                </span>
                            <?php else: ?>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-weight:500; font-size:13px; color:#0284c7;">
                                    <i class="bi bi-bag"></i> Takeaway
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($order['table_number'])): ?>
                                <span style="background:#f1f5f9; color:#1e293b; font-weight:700; font-size:12px; padding:3px 8px; border-radius:6px;">
                                    <?= e($order['table_number']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)($order['item_count'] ?? 2) ?> Items</td>
                        <td style="font-weight:700; color:#111827;">₹<?= number_format((float)$order['total_amount'], 0) ?></td>
                        <td>
                            <span class="status-pill <?= e($order['order_status']) ?>">
                                <?= ucfirst(e($order['order_status'])) ?>
                            </span>
                        </td>
                        <td style="color:#64748b; font-size:12.5px;"><?= date('h:i A', strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:48px; color:#6B7280;">
                        <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                        No active orders at this moment. Incoming orders will appear here automatically.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="hb-modal-overlay hidden" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
    <div class="hb-modal-dialog" style="background: #FFFFFF; border-radius: 16px; width: 560px; max-width: 90vw; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: scaleIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #E5E7EB;">
            <div>
                <h3 id="modalOrderNum" style="font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 2px 0;">Order #</h3>
                <p id="modalCustomerInfo" style="font-size: 12px; color: #6B7280; margin: 0;">Customer Details</p>
            </div>
            <button type="button" onclick="closeOrderModal()" style="background: #F3F4F6; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #4B5563;">✕</button>
        </div>

        <div id="modalOrderLoading" style="text-align: center; padding: 30px; color: #6B7280;">Loading order details...</div>

        <div id="modalOrderContent" style="display: none;">
            <!-- Order Meta Badges -->
            <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                <span id="modalOrderStatus" class="status-pill">Status</span>
                <span id="modalOrderType" class="status-pill" style="background:#F3F4F6; color:#374151;">Dine-In</span>
                <span id="modalOrderTable" class="status-pill" style="background:#EAF5EA; color:#1B5E20;">Table</span>
            </div>

            <!-- Items Breakdown Table -->
            <div style="margin-bottom: 16px; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden;">
                <div style="background: #F9FAFB; padding: 8px 14px; font-size: 12px; font-weight: 700; color: #374151; display: flex; justify-content: space-between;">
                    <span>Item</span>
                    <span>Qty · Price</span>
                </div>
                <div id="modalItemsList" style="padding: 10px 14px; display: flex; flex-direction: column; gap: 10px; font-size: 13px; max-height: 200px; overflow-y: auto;">
                    <!-- Items inserted by JS -->
                </div>
            </div>

            <!-- Total Breakdown -->
            <div style="background: #FAFAF8; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px; color: #4B5563;">
                    <span>Subtotal:</span>
                    <span id="modalSubtotal">₹0</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #4B5563;">
                    <span>Tax & Charges:</span>
                    <span id="modalTax">₹0</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 15px; color: #111827; padding-top: 6px; border-top: 1px solid #E5E7EB;">
                    <span>Total Amount:</span>
                    <span id="modalTotal">₹0</span>
                </div>
            </div>

            <!-- Kitchen Action Link -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="<?= url('/owner/kitchen-orders') ?>" class="btn-hb btn-hb-primary" style="padding: 8px 18px; border-radius: 8px; font-size: 13px; text-decoration: none;">
                    Open Kitchen Kanban &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let currentLegendStatus = 'all';

function filterByStatusLegend(status, btn) {
    currentLegendStatus = status;
    document.querySelectorAll('.legend-pill-btn').forEach(b => {
        b.style.borderColor = 'transparent';
        b.style.background = '';
        b.style.color = '';
    });

    if (btn) {
        btn.style.borderColor = '#111827';
        btn.style.boxShadow = '0 0 0 2px rgba(0,0,0,0.15)';
    }

    applyOrdersFilter();
}

function applyOrdersFilter() {
    const searchInput = document.getElementById('orderSearchInput');
    const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const typeFilter = document.getElementById('orderTypeFilter');
    const typeVal = typeFilter ? typeFilter.value.toLowerCase().trim() : 'all';

    const rows = document.querySelectorAll('.clickable-order-row');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status') || '';
        const rowType = row.getAttribute('data-type') || '';
        const rowSearch = row.getAttribute('data-search') || '';

        const matchesStatus = (currentLegendStatus === 'all') || (rowStatus === currentLegendStatus);
        const matchesType = (typeVal === 'all') || (rowType === typeVal);
        const matchesSearch = !searchVal || rowSearch.includes(searchVal);

        row.style.display = (matchesStatus && matchesType && matchesSearch) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('orderSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', applyOrdersFilter);
    }
    const typeFilter = document.getElementById('orderTypeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', applyOrdersFilter);
    }
});

function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const loading = document.getElementById('modalOrderLoading');
    const content = document.getElementById('modalOrderContent');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';

    fetch(`<?= url('/owner/orders/') ?>${orderId}/details`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success || !data.order) {
            loading.textContent = 'Could not load order details.';
            return;
        }
        const o = data.order;
        document.getElementById('modalOrderNum').textContent = 'Order #' + o.order_number;
        document.getElementById('modalCustomerInfo').textContent = `${o.customer_name || 'Guest'} · ${o.customer_mobile || 'No phone'} · Placed at ${o.created_at}`;
        
        const statusEl = document.getElementById('modalOrderStatus');
        statusEl.className = 'status-pill ' + (o.order_status || 'placed');
        statusEl.textContent = (o.order_status || 'Placed').toUpperCase();

        document.getElementById('modalOrderType').textContent = o.order_type === 'dine_in' ? 'Dine-In' : 'Takeaway';
        document.getElementById('modalOrderTable').textContent = o.table_number ? o.table_number : 'Takeaway Counter';

        const listEl = document.getElementById('modalItemsList');
        listEl.innerHTML = '';
        if (o.items && o.items.length > 0) {
            o.items.forEach(it => {
                const row = document.createElement('div');
                row.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding: 4px 0; border-bottom: 1px dashed #f1f5f9;';
                row.innerHTML = `
                    <div>
                        <strong style="color:#0f172a;">${it.food_name_snapshot}</strong>
                        ${it.calories ? `<span style="font-size:11px; color:#64748b;"> · ${it.calories} kcal · ${it.protein}g protein</span>` : ''}
                    </div>
                    <span style="font-weight:700; color:#0f172a;">${it.quantity}× ₹${Number(it.unit_price).toFixed(0)}</span>
                `;
                listEl.appendChild(row);
            });
        } else {
            listEl.innerHTML = '<div style="color:#6B7280;">No item snapshots recorded.</div>';
        }

        document.getElementById('modalSubtotal').textContent = '₹' + Number(o.subtotal || o.total_amount).toFixed(0);
        document.getElementById('modalTax').textContent = '₹' + Number(o.tax || 0).toFixed(0);
        document.getElementById('modalTotal').textContent = '₹' + Number(o.total_amount).toFixed(0);

        loading.style.display = 'none';
        content.style.display = 'block';
    })
    .catch(err => {
        loading.textContent = 'Failed to load order details.';
    });
}

function closeOrderModal() {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) modal.style.display = 'none';
}
</script>
