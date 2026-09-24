<div class="app-container" style="max-width: 680px; margin: 0 auto; padding: var(--space-4) var(--space-3);">
    <div style="margin-bottom: var(--space-4);">
        <a href="<?= url('/menu') ?>" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:8px; padding: 8px 16px; border-radius: var(--radius-full); font-size: 13px;">
            <i class="bi bi-arrow-left"></i> Back to Menu
        </a>
    </div>

    <!-- Main Status Card -->
    <div class="checkout-card" style="padding: var(--space-6); background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); margin-bottom: var(--space-6);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: var(--space-4); flex-wrap:wrap; gap: 12px;">
            <div>
                <div style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                    <i class="bi bi-geo-alt"></i> <?= e($order['restaurant_name']) ?>
                    <?php if (!empty($order['table_number'])): ?>
                        <span>• Table <?= e($order['table_number']) ?></span>
                    <?php else: ?>
                        <span>• <?= ucfirst(e($order['order_type'] ?? 'dine_in')) ?></span>
                    <?php endif; ?>
                </div>
                <h1 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin:0;">
                    Order #<span style="font-family:monospace;"><?= e($order['order_number']) ?></span>
                </h1>
            </div>

            <?php
            $badgeClass = 'badge-info';
            $status = strtolower($order['order_status']);
            if ($status === 'placed') $badgeClass = 'badge-warning';
            elseif ($status === 'accepted' || $status === 'preparing') $badgeClass = 'badge-info';
            elseif ($status === 'ready') $badgeClass = 'badge-success';
            elseif ($status === 'completed') $badgeClass = 'badge-neutral';
            elseif ($status === 'cancelled') $badgeClass = 'badge-danger';
            ?>
            <span id="orderStatusBadge" class="badge <?= $badgeClass ?>" style="font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: var(--radius-full); text-transform: uppercase; letter-spacing: 0.05em; display:inline-flex; align-items:center; gap:6px;">
                <span class="status-pulse-dot" style="width:8px; height:8px; border-radius:50%; background:currentColor; display:inline-block;"></span>
                <span id="orderStatusText"><?= htmlspecialchars(strtoupper($order['order_status'])) ?></span>
            </span>
        </div>

        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: var(--space-6);">
            Placed on <?= date('M d, Y • h:i A', strtotime($order['created_at'] ?? 'now')) ?>. Real-time updates sync automatically.
        </p>

        <!-- Visual 5-Stage Stepper -->
        <?php
        $statuses = ['placed', 'accepted', 'preparing', 'ready', 'completed'];
        $currentIdx = array_search($status, $statuses, true);
        if ($currentIdx === false) $currentIdx = 0;
        ?>

        <div class="tracking-stepper" style="margin: var(--space-6) 0;">
            <div class="step-item <?= $currentIdx >= 0 ? ($currentIdx == 0 ? 'active' : 'completed') : '' ?>" id="step-placed">
                <div class="step-dot"><i class="bi bi-clipboard-check"></i></div>
                <span class="step-label">Placed</span>
            </div>

            <div class="step-item <?= $currentIdx >= 1 ? ($currentIdx == 1 ? 'active' : 'completed') : '' ?>" id="step-accepted">
                <div class="step-dot"><i class="bi bi-check2-circle"></i></div>
                <span class="step-label">Accepted</span>
            </div>

            <div class="step-item <?= $currentIdx >= 2 ? ($currentIdx == 2 ? 'active' : 'completed') : '' ?>" id="step-preparing">
                <div class="step-dot"><i class="bi bi-fire"></i></div>
                <span class="step-label">Preparing</span>
            </div>

            <div class="step-item <?= $currentIdx >= 3 ? ($currentIdx == 3 ? 'active' : 'completed') : '' ?>" id="step-ready">
                <div class="step-dot"><i class="bi bi-bell"></i></div>
                <span class="step-label">Ready</span>
            </div>

            <div class="step-item <?= $currentIdx >= 4 ? 'completed' : '' ?>" id="step-completed">
                <div class="step-dot"><i class="bi bi-check-lg"></i></div>
                <span class="step-label">Completed</span>
            </div>
        </div>

        <!-- Kitchen Status Box -->
        <div id="kitchenUpdateBox" style="background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: var(--space-4); margin-bottom: var(--space-6); display:flex; align-items:center; gap: 14px;">
            <div style="width:40px; height:40px; border-radius:var(--radius-full); background:var(--brand-primary-subtle); color:var(--brand-primary); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                <i class="bi bi-info-circle"></i>
            </div>
            <div>
                <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">Kitchen Progress Update</div>
                <p id="kitchenStatusDesc" style="font-size: 13px; color: var(--text-secondary); margin: 0; line-height: 1.4;">
                    <?php if ($status === 'placed'): ?>
                        Your order is securely queued and waiting for kitchen staff confirmation.
                    <?php elseif ($status === 'accepted'): ?>
                        The kitchen accepted your ticket and ingredients are being assembled!
                    <?php elseif ($status === 'preparing'): ?>
                        Our chefs are firing up fresh, wholesome ingredients for your dishes.
                    <?php elseif ($status === 'ready'): ?>
                        Fresh and hot! Your food is ready for service / pickup at the counter.
                    <?php elseif ($status === 'completed'): ?>
                        Order finished. Enjoy your meal and thank you for dining with Healthy Bite!
                    <?php else: ?>
                        Order is currently in progress.
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div style="margin-bottom: var(--space-6);">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: var(--space-3); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-receipt" style="color:var(--brand-primary);"></i> Order Items (<?= count($order['items']) ?>)
            </h3>
            <div style="border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; background: var(--bg-surface);">
                <?php foreach ($order['items'] as $item): ?>
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:flex-start; gap: 12px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: var(--text-primary);">
                                <span style="color:var(--brand-primary); margin-right:4px;"><?= (int)$item['quantity'] ?>×</span>
                                <?= e($item['food_name_snapshot']) ?>
                            </div>
                            <?php if (!empty($item['variant_name_snapshot'])): ?>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                    Size / Variant: <?= e($item['variant_name_snapshot']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['customizations'])): ?>
                                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px; display:flex; flex-wrap:wrap; gap:4px;">
                                    <?php foreach ($item['customizations'] as $cust): ?>
                                        <span class="dietary-badge" style="background:#f1f5f9; color:#475569; font-size:11px; padding:2px 8px;">
                                            + <?= e($cust['customization_name_snapshot'] ?? $cust['name'] ?? '') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); text-align:right; white-space:nowrap;">
                            <?= formatCurrency((float)$item['total_price']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div style="padding: 14px 16px; background: var(--bg-surface-elevated); display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size: 14px; font-weight: 700; color: var(--text-primary);">Total Paid</span>
                    <span style="font-size: 17px; font-weight: 800; color: var(--brand-primary);"><?= formatCurrency((float)$order['total_amount']) ?></span>
                </div>
            </div>
        </div>

        <!-- Interactive Customer Review Card (Prompt #29) -->
        <div id="customerReviewSection" class="review-feedback-card" style="border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: var(--space-5); background: #f8fafc; margin-bottom: var(--space-5);">
            <div id="reviewPrompt">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom: var(--space-2);">
                    <div style="width:36px; height:36px; border-radius:50%; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:16px;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin:0; font-size:15px; font-weight:700; color:var(--text-primary);">How is your dining experience?</h4>
                        <p style="margin:0; font-size:12px; color:var(--text-secondary);">Your rating helps our kitchen and service improve.</p>
                    </div>
                </div>

                <!-- Star Selection -->
                <div class="star-rating-selector" style="display:flex; gap:8px; margin: var(--space-3) 0; font-size: 26px; cursor:pointer;" id="starRatingContainer">
                    <i class="bi bi-star-fill star-item active" data-val="1" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="2" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="3" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="4" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <i class="bi bi-star-fill star-item active" data-val="5" style="color:#f59e0b; transition:transform 0.15s;"></i>
                    <span id="starRatingLabel" style="font-size:14px; font-weight:700; color:var(--text-primary); margin-left:8px; align-self:center;">5.0 Excellent</span>
                </div>

                <div class="form-group" style="margin-bottom: var(--space-3);">
                    <textarea id="reviewComment" class="form-control" rows="2" placeholder="Tell us about the taste, presentation, or service (optional)..." style="font-size: 13px; resize:vertical;"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="button" id="btnSubmitReview" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px; border-radius: var(--radius-full); font-weight: 700;">
                        <i class="bi bi-send"></i> Submit Review
                    </button>
                </div>
            </div>

            <!-- Review Success State -->
            <div id="reviewSuccessState" style="display:none; text-align:center; padding: var(--space-4) var(--space-2);">
                <div style="width:44px; height:44px; border-radius:50%; background:var(--brand-primary-subtle); color:var(--brand-primary); display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:8px;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <h4 style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">Thank you for your review!</h4>
                <p style="font-size:13px; color:var(--text-secondary); margin:0;">Our kitchen and management appreciate your valuable feedback.</p>
            </div>
        </div>

        <!-- Action Footer -->
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= url('/menu') ?>" class="btn btn-primary" style="padding: 10px 24px; border-radius: var(--radius-full); font-weight: 700;">
                <i class="bi bi-plus-circle"></i> Order More Food
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline" style="padding: 10px 20px; border-radius: var(--radius-full); font-weight: 600;">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
        </div>
    </div>
</div>

<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/order.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        OrderTracker.init(<?= json_encode($order['order_number']) ?>);
    });
</script>
