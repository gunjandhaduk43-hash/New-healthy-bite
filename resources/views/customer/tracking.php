<?php
$status = strtolower($order['order_status'] ?? 'placed');
$tableRaw = (string)($order['table_number'] ?? '');
$tableClean = preg_replace('/^table\s*/i', '', trim($tableRaw));

// Comprehensive Stage Metadata
$stageMeta = [
    'placed' => [
        'index' => 0,
        'badge' => 'badge-placed',
        'badge_text' => 'Order Placed',
        'icon' => 'bi-clipboard-check',
        'hero_icon' => 'bi-hourglass-split',
        'hero_title' => 'Order Queued & Received',
        'hero_desc' => 'Your order ticket has been received by the kitchen queue. Line chefs are assigning your ticket.',
        'eta' => '~15-20 Mins',
        'progress' => '10%',
    ],
    'accepted' => [
        'index' => 1,
        'badge' => 'badge-accepted',
        'badge_text' => 'Kitchen Accepted',
        'icon' => 'bi-check2-circle',
        'hero_icon' => 'bi-card-checklist',
        'hero_title' => 'Ticket Accepted — Line Assembling',
        'hero_desc' => 'The kitchen confirmed your ticket. Fresh, high-protein ingredients are being selected and measured.',
        'eta' => '~12-16 Mins',
        'progress' => '35%',
    ],
    'preparing' => [
        'index' => 2,
        'badge' => 'badge-preparing',
        'badge_text' => 'Chefs Cooking',
        'icon' => 'bi-fire',
        'hero_icon' => 'bi-fire',
        'hero_title' => 'Chefs Firing Up Your Dishes',
        'hero_desc' => 'Our line chefs are actively cooking, seasoning, and garnishing your wholesome meal.',
        'eta' => '~6-10 Mins',
        'progress' => '62%',
    ],
    'ready' => [
        'index' => 3,
        'badge' => 'badge-ready',
        'badge_text' => 'Ready for You',
        'icon' => 'bi-bell-fill',
        'hero_icon' => 'bi-bell',
        'hero_title' => 'Fresh & Hot — Ready for Pickup / Table!',
        'hero_desc' => 'Your meal is freshly plated and ready for pickup at the counter, or being brought right to your table.',
        'eta' => 'Ready Now!',
        'progress' => '88%',
    ],
    'completed' => [
        'index' => 4,
        'badge' => 'badge-completed',
        'badge_text' => 'Served & Enjoyed',
        'icon' => 'bi-check-circle-fill',
        'hero_icon' => 'bi-emoji-smile',
        'hero_title' => 'Order Completed — Enjoy Your Meal!',
        'hero_desc' => 'Your meal has been served. Thank you for dining with Healthy Bite — fuel your body with wholesome nutrition!',
        'eta' => 'Completed',
        'progress' => '100%',
    ],
    'cancelled' => [
        'index' => 0,
        'badge' => 'badge-cancelled',
        'badge_text' => 'Order Cancelled',
        'icon' => 'bi-x-circle',
        'hero_icon' => 'bi-x-octagon',
        'hero_title' => 'Order Cancelled',
        'hero_desc' => 'This order was cancelled. Please speak with restaurant staff if you need assistance.',
        'eta' => 'Cancelled',
        'progress' => '0%',
    ],
];

$currentStage = $stageMeta[$status] ?? $stageMeta['placed'];
$currentIdx = $currentStage['index'];
?>

<div class="app-container" style="max-width: 680px; margin: 0 auto; padding: var(--space-4) var(--space-3);">
    <!-- Top Navigation & Live Telemetry Pill -->
    <div class="tracking-header-nav">
        <a href="<?= url('/menu') ?>" class="btn-track-secondary" style="padding: 7px 16px; font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Menu
        </a>
        <div class="tracking-live-telemetry-badge" title="Active WebSocket / Live HTTP Poller Connection">
            <span class="live-pulse-dot"></span>
            <span>Live Kitchen Sync</span>
        </div>
    </div>

    <!-- Main Live Tracking Card -->
    <div class="tracking-main-card">
        <!-- Order Location & Identification -->
        <div class="order-meta-header">
            <div>
                <div class="order-location-capsules">
                    <span class="order-loc-pill">
                        <i class="bi bi-geo-alt-fill" style="color:var(--primary-green);"></i> <?= e($order['restaurant_name']) ?>
                    </span>
                    <?php if (!empty($tableClean)): ?>
                        <span class="order-loc-pill table-pill">
                            <i class="bi bi-table"></i> Table <?= e($tableClean) ?>
                        </span>
                    <?php else: ?>
                        <span class="order-loc-pill">
                            <i class="bi bi-bag-check"></i> <?= ucfirst(str_replace('_', ' ', e($order['order_type'] ?? 'Dine-In'))) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="order-title-wrap">
                    <h1 class="order-number-title">
                        Order #<?= e($order['order_number']) ?>
                    </h1>
                    <button type="button" class="order-copy-pill" onclick="copyOrderNumber('<?= e($order['order_number']) ?>', this)" title="Copy Order Number">
                        <i class="bi bi-copy"></i> Copy
                    </button>
                </div>
            </div>

            <!-- Dynamic Live Status Badge -->
            <span id="orderStatusBadge" class="order-status-badge <?= $currentStage['badge'] ?>">
                <span class="status-badge-dot"></span>
                <span id="orderStatusText"><?= htmlspecialchars(strtoupper($currentStage['badge_text'])) ?></span>
            </span>
        </div>

        <p class="order-timestamp-text">
            Placed on <?= date('M d, Y • h:i A', strtotime($order['created_at'] ?? 'now')) ?>. Real-time updates sync automatically.
        </p>

        <!-- Fluid 5-Stage Stepper (Zero Cut-offs on Mobile) -->
        <div class="tracking-stepper-container">
            <div class="stepper-line-track">
                <div class="stepper-line-fill" id="trackingProgressFill" style="width: <?= $currentStage['progress'] ?>;"></div>
            </div>

            <div class="tracking-stepper" role="progressbar" aria-valuenow="<?= $currentIdx + 1 ?>" aria-valuemin="1" aria-valuemax="5">
                <div class="step-item <?= $currentIdx >= 0 ? ($currentIdx == 0 ? 'active' : 'completed') : '' ?>" id="step-placed">
                    <div class="step-dot"><i class="bi <?= $currentIdx > 0 ? 'bi-check-lg' : 'bi-clipboard-check' ?>"></i></div>
                    <span class="step-label">Placed</span>
                </div>

                <div class="step-item <?= $currentIdx >= 1 ? ($currentIdx == 1 ? 'active' : 'completed') : '' ?>" id="step-accepted">
                    <div class="step-dot"><i class="bi <?= $currentIdx > 1 ? 'bi-check-lg' : 'bi-check2-circle' ?>"></i></div>
                    <span class="step-label">Accepted</span>
                </div>

                <div class="step-item <?= $currentIdx >= 2 ? ($currentIdx == 2 ? 'active' : 'completed') : '' ?>" id="step-preparing">
                    <div class="step-dot"><i class="bi <?= $currentIdx > 2 ? 'bi-check-lg' : 'bi-fire' ?>"></i></div>
                    <span class="step-label">Preparing</span>
                </div>

                <div class="step-item <?= $currentIdx >= 3 ? ($currentIdx == 3 ? 'active' : 'completed') : '' ?>" id="step-ready">
                    <div class="step-dot"><i class="bi <?= $currentIdx > 3 ? 'bi-check-lg' : 'bi-bell-fill' ?>"></i></div>
                    <span class="step-label">Ready</span>
                </div>

                <div class="step-item <?= $currentIdx >= 4 ? 'completed' : '' ?>" id="step-completed">
                    <div class="step-dot"><i class="bi bi-bag-check-fill"></i></div>
                    <span class="step-label">Completed</span>
                </div>
            </div>
        </div>

        <!-- Dynamic Kitchen Hero Progress Banner -->
        <div class="kitchen-hero-card" id="kitchenUpdateBox">
            <div class="kitchen-hero-icon-wrap" id="kitchenHeroIconWrap">
                <i class="bi <?= $currentStage['hero_icon'] ?>" id="kitchenHeroIcon"></i>
            </div>
            <div class="kitchen-hero-content">
                <div class="kitchen-hero-title-row">
                    <h2 class="kitchen-hero-title" id="kitchenHeroTitle"><?= $currentStage['hero_title'] ?></h2>
                    <span class="kitchen-eta-pill" id="kitchenEtaPill">
                        <i class="bi bi-clock-history"></i> <span id="kitchenEtaText"><?= $currentStage['eta'] ?></span>
                    </span>
                </div>
                <p class="kitchen-hero-desc" id="kitchenStatusDesc"><?= $currentStage['hero_desc'] ?></p>
                <div class="kitchen-sync-meta">
                    <i class="bi bi-arrow-repeat spin-slow" style="color:var(--primary-green);"></i> Polling live every 4s • Synced with Kitchen Display System
                </div>
            </div>
        </div>

        <!-- Order Items Breakdown Ticket -->
        <div class="tracking-items-section">
            <div class="items-section-header">
                <h3 class="items-section-title">
                    <i class="bi bi-receipt" style="color:var(--primary-green);"></i> Order Items (<?= count($order['items']) ?>)
                </h3>
                <span style="font-size:12px; font-weight:700; color:#166534; background:#e8f5e9; padding:2px 8px; border-radius:6px;">
                    Verified Ticket
                </span>
            </div>

            <div class="order-ticket-card">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="tracking-item-row">
                        <div class="item-left-details">
                            <div class="item-title-line">
                                <span class="item-qty-pill"><?= (int)$item['quantity'] ?>×</span>
                                <span><?= e($item['food_name_snapshot']) ?></span>
                            </div>

                            <?php if (!empty($item['variant_name_snapshot'])): ?>
                                <div class="item-variant-label">
                                    Size / Portion: <?= e($item['variant_name_snapshot']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['customizations'])): ?>
                                <div class="item-customs-wrap">
                                    <?php foreach ($item['customizations'] as $cust): ?>
                                        <span class="item-custom-tag">
                                            + <?= e($cust['customization_name_snapshot'] ?? $cust['name'] ?? '') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['calories'])): ?>
                                <div class="item-macros-tag">
                                    <i class="bi bi-fire" style="color:#f59e0b;"></i> <?= (int)$item['calories'] ?> kcal · <?= (float)$item['protein'] ?>g protein
                                    <?php if ($item['sugar'] !== null): ?> · <?= (float)$item['sugar'] ?>g sugar<?php endif; ?>
                                    <?php if (!empty($item['caffeine']) && (float)$item['caffeine'] > 0): ?> · <i class="bi bi-cup-hot" style="color:#7e22ce;"></i> <?= (int)$item['caffeine'] ?>mg caffeine<?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="item-price-val">
                            <?= formatCurrency((float)$item['total_price']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="order-totals-bar">
                    <div class="totals-label-wrap">
                        <span class="totals-label">Total Amount Paid</span>
                        <span class="totals-paid-badge">
                            <i class="bi bi-shield-check"></i> Paid Online · Kitchen Confirmed
                        </span>
                    </div>
                    <span class="totals-amount-val"><?= formatCurrency((float)$order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <!-- Interactive Customer Review Card -->
        <div id="customerReviewSection" class="review-feedback-card">
            <div id="reviewPrompt">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom: 10px;">
                    <div style="width:38px; height:38px; border-radius:50%; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin:0; font-size:15px; font-weight:800; color:#0f172a;">How is your dining experience?</h4>
                        <p style="margin:0; font-size:12px; color:#64748b;">Your rating helps our kitchen and service team improve.</p>
                    </div>
                </div>

                <!-- Star Selection -->
                <div class="star-rating-selector" style="display:flex; gap:8px; margin: 12px 0 14px; font-size: 26px; cursor:pointer;" id="starRatingContainer">
                    <i class="bi bi-star-fill star-item active" data-val="1" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="2" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="3" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="4" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="5" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <span id="starRatingLabel" style="font-size:13.5px; font-weight:700; color:#0f172a; margin-left:8px; align-self:center;">5.0 Excellent</span>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <textarea id="reviewComment" class="form-control" rows="2" placeholder="Tell us about the taste, presentation, or service (optional)..." style="font-size: 13px; resize:vertical; border:1.5px solid #e2e8f0; border-radius:12px; padding:10px 14px;"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="button" id="btnSubmitReview" class="btn-track-primary" style="padding: 9px 22px; font-size: 13.5px;">
                        <i class="bi bi-send"></i> Submit Review
                    </button>
                </div>
            </div>

            <!-- Review Success State -->
            <div id="reviewSuccessState" style="display:none; text-align:center; padding: 16px 8px;">
                <div style="width:44px; height:44px; border-radius:50%; background:#e8f5e9; color:#166534; display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:8px;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <h4 style="font-size:15px; font-weight:800; color:#0f172a; margin-bottom:4px;">Thank you for your review!</h4>
                <p style="font-size:13px; color:#64748b; margin:0;">Our kitchen and management appreciate your valuable feedback.</p>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="tracking-actions-footer">
            <a href="<?= url('/menu') ?>" class="btn-track-primary">
                <i class="bi bi-plus-circle"></i> Order More Food
            </a>
            <button type="button" onclick="window.print()" class="btn-track-secondary">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
        </div>
    </div>
</div>

<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/order.js') ?>"></script>
<script>
    function copyOrderNumber(num, btn) {
        if (!navigator.clipboard) return;
        navigator.clipboard.writeText(num).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg" style="color:#16a34a;"></i> Copied!';
            setTimeout(() => { btn.innerHTML = original; }, 2000);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        try {
            localStorage.setItem('active_order_number', <?= json_encode($order['order_number']) ?>);
            localStorage.setItem('active_order_id', <?= json_encode($order['id'] ?? 0) ?>);
        } catch(e) {}
        OrderTracker.init(<?= json_encode($order['order_number']) ?>);
    });
</script>
