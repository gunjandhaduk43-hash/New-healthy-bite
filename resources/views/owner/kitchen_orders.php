<?php if (!empty($flashSuccess)): ?>
    <div class="alert-banner success" style="margin-bottom: 20px; padding: 12px 16px; background: #EAF5EA; color: #166534; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= e($flashSuccess) ?></span>
    </div>
<?php endif; ?>

<!-- Page Header Row -->
<div class="page-header-row" style="margin-bottom: 20px;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <h1 class="page-greeting" style="margin:0;">Kitchen Live Orders</h1>
            <span class="status-pulse-dot" style="width:10px; height:10px; border-radius:50%; background:#16a34a; display:inline-block; box-shadow:0 0 0 3px rgba(22,163,74,0.2);" title="Live syncing"></span>
        </div>
        <p class="page-subtitle">Real-time ticket board for chef and prep stations. Tickets update live.</p>
    </div>
    <div class="header-action-group">
        <div style="display:flex; align-items:center; gap:8px; background:#fff; padding:6px 12px; border-radius:10px; border:1px solid #E5E7EB; font-size:12px; color:#4B5563;">
            <i class="bi bi-arrow-repeat" style="color:#166534;"></i>
            <span>Auto-sync: <strong>Active (8s)</strong></span>
        </div>
        <button onclick="window.location.reload()" class="btn-hb btn-hb-primary" title="Refresh Kitchen Orders">
            <i class="bi bi-arrow-clockwise"></i> Refresh Board
        </button>
    </div>
</div>

<!-- 5-Column Kanban Board -->
<div class="kitchen-kanban-board" style="display: grid; grid-template-columns: repeat(5, minmax(240px, 1fr)); gap: 16px; align-items: flex-start; overflow-x: auto; padding-bottom: 16px;">
    <?php
    $columns = [
        ['key' => 'placed',    'title' => 'New Orders', 'badgeClass' => 'blue',   'nextStatus' => 'accepted',  'btnLabel' => 'Accept Ticket', 'btnClass' => 'background:#2563eb; color:#fff;'],
        ['key' => 'accepted',  'title' => 'Accepted',   'badgeClass' => 'purple', 'nextStatus' => 'preparing', 'btnLabel' => 'Start Prep',     'btnClass' => 'background:#7e22ce; color:#fff;'],
        ['key' => 'preparing', 'title' => 'In Prep',     'badgeClass' => 'orange', 'nextStatus' => 'ready',     'btnLabel' => 'Mark as Ready',  'btnClass' => 'background:#d97706; color:#fff;'],
        ['key' => 'ready',     'title' => 'Ready to Serve', 'badgeClass' => 'green',  'nextStatus' => 'completed', 'btnLabel' => 'Complete Order', 'btnClass' => 'background:#166534; color:#fff;'],
        ['key' => 'completed', 'title' => 'Completed',  'badgeClass' => 'gray',   'nextStatus' => null,        'btnLabel' => null,           'btnClass' => '']
    ];
    ?>

    <?php foreach ($columns as $col): ?>
        <?php $colOrders = $kanban[$col['key']] ?? []; ?>
        <div class="kitchen-kanban-col" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:12px; min-height:480px; display:flex; flex-direction:column; gap:12px;">
            <!-- Column Header -->
            <div class="kanban-col-header" style="display:flex; justify-content:space-between; align-items:center; padding:4px 6px; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">
                <div style="font-size:14px; font-weight:800; color:#1e293b; text-transform:uppercase; letter-spacing:0.04em;">
                    <?= e($col['title']) ?>
                </div>
                <span class="kanban-col-count <?= $col['badgeClass'] ?>" style="font-size:12px; font-weight:700; padding:2px 10px; border-radius:9999px;">
                    <?= count($colOrders) ?>
                </span>
            </div>

            <!-- Orders in this Status -->
            <?php if (!empty($colOrders)): ?>
                <?php foreach ($colOrders as $order): ?>
                    <div class="kanban-order-card" id="order-card-<?= (int)$order['id'] ?>" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.04); display:flex; flex-direction:column; gap:10px; transition:box-shadow 0.15s ease;">
                        
                        <!-- Top Row: Table context & Elapsed time -->
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:6px;">
                            <?php if (!empty($order['table_number'])): ?>
                                <span style="background:#1e293b; color:#ffffff; font-weight:800; font-size:12px; padding:3px 10px; border-radius:6px; letter-spacing:0.05em; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="bi bi-qr-code"></i> <?= strtoupper(e($order['table_number'])) ?>
                                </span>
                            <?php else: ?>
                                <span style="background:#0284c7; color:#ffffff; font-weight:800; font-size:12px; padding:3px 10px; border-radius:6px; letter-spacing:0.05em;">
                                    TAKEAWAY
                                </span>
                            <?php endif; ?>

                            <span style="font-size:11px; font-weight:600; color:#64748b; display:inline-flex; align-items:center; gap:4px;">
                                <i class="bi bi-clock"></i> <?= date('h:i A', strtotime($order['created_at'])) ?>
                            </span>
                        </div>

                        <!-- Order identifier -->
                        <div style="display:flex; justify-content:space-between; align-items:baseline;">
                            <span style="font-size:15px; font-weight:800; color:#0f172a; font-family:monospace;">
                                #<?= e($order['order_number']) ?>
                            </span>
                            <span style="font-size:12px; color:#64748b; font-weight:500;">
                                <?= e($order['customer_name'] ?? 'Guest') ?>
                            </span>
                        </div>

                        <!-- Itemized Lines with Customizations -->
                        <div class="kanban-item-list" style="border-top:1px dashed #e2e8f0; border-bottom:1px dashed #e2e8f0; padding:8px 0; margin:2px 0; display:flex; flex-direction:column; gap:8px;">
                            <?php if (!empty($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="kanban-item-row" style="line-height:1.3;">
                                        <div style="font-size:13.5px; font-weight:700; color:#0f172a; display:flex; align-items:flex-start; gap:6px;">
                                            <span style="background:#f1f5f9; color:#1e293b; padding:1px 6px; border-radius:4px; font-size:12px; font-weight:800; flex-shrink:0;">
                                                <?= (int)$item['quantity'] ?>×
                                            </span>
                                            <span><?= e($item['food_name_snapshot']) ?></span>
                                        </div>
                                        <?php if (!empty($item['variant_name_snapshot'])): ?>
                                            <div style="font-size:11px; color:#64748b; margin-left:26px; margin-top:1px;">
                                                Size: <?= e($item['variant_name_snapshot']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($item['customizations'])): ?>
                                            <div style="font-size:11px; color:#b45309; font-weight:600; margin-left:26px; margin-top:2px;">
                                                <?= implode(' • ', array_map(fn($c) => '+ ' . e($c['customization_name_snapshot'] ?? $c['name'] ?? ''), $item['customizations'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="font-size:12px; color:#94a3b8; font-style:italic;">
                                    Standard preparation
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($order['notes'])): ?>
                            <div class="kanban-special-notes" style="background:#fef3c7; color:#92400e; padding:6px 10px; border-radius:8px; font-size:11.5px; font-weight:600; display:flex; align-items:center; gap:6px;">
                                <i class="bi bi-chat-left-dots-fill"></i>
                                <span>Note: <?= e($order['notes']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Bottom Row: Price & 1-Tap Action Button -->
                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:2px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#64748b;">
                                <span>Total Paid</span>
                                <strong style="font-size:14px; color:#0f172a;">₹<?= number_format((float)$order['total_amount'], 0) ?></strong>
                            </div>

                            <?php if (!empty($col['nextStatus'])): ?>
                                <button type="button" 
                                        onclick="advanceOrderStatus(<?= (int)$order['id'] ?>, '<?= $col['nextStatus'] ?>', this)" 
                                        class="btn-hb" 
                                        style="width:100%; <?= $col['btnClass'] ?> font-weight:700; padding:10px 14px; border-radius:8px; font-size:13px; cursor:pointer; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1); transition:transform 0.1s ease;">
                                    <?= e($col['btnLabel']) ?> &rarr;
                                </button>
                            <?php else: ?>
                                <div style="text-align:center; padding:6px; font-size:12px; color:#166534; font-weight:700; background:#dcfce7; border-radius:6px;">
                                    <i class="bi bi-check2"></i> Finished
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="background:#FFFFFF; border:1px dashed #cbd5e1; border-radius:12px; padding:32px 16px; text-align:center; color:#94a3b8; font-size:12px; margin:auto 0;">
                    <i class="bi bi-check2-circle" style="font-size:24px; display:block; margin-bottom:6px; color:#cbd5e1;"></i>
                    No <?= strtolower($col['title']) ?> tickets
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
async function advanceOrderStatus(orderId, nextStatus, btn) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:12px; height:12px; display:inline-block; border:2px solid #fff; border-right-color:transparent; border-radius:50%; animation:hb-spin .6s linear infinite; margin-right:6px;"></span> Updating...';
    
    try {
        const res = await fetch(`/owner/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: nextStatus })
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(`Order status updated to ${nextStatus.charAt(0).toUpperCase() + nextStatus.slice(1)}!`, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 400);
        } else {
            throw new Error(data.error || 'Failed to update order status');
        }
    } catch (err) {
        showToast(err.message || 'Error updating order status', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = `background:${type === 'error' ? '#EF4444' : '#166534'}; color:#FFFFFF; padding:12px 18px; border-radius:10px; font-size:13.5px; font-weight:500; box-shadow:0 10px 25px -5px rgba(0,0,0,0.2); display:flex; align-items:center; gap:8px; animation:slideIn 0.25s ease-out; pointer-events:auto;`;
    toast.innerHTML = `<i class="bi ${type === 'error' ? 'bi-exclamation-circle' : 'bi-check-circle-fill'}"></i> <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Auto-sync poll every 8 seconds
setInterval(() => {
    // Only background sync if modal or forms are not active
    const activeModal = document.querySelector('.hb-modal-overlay:not(.hidden)');
    if (!activeModal) {
        window.location.reload();
    }
}, 8000);
</script>
