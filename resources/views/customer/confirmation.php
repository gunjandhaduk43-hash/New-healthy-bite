<div class="app-container confirmation-container">
    <div class="confirmation-card">
        <!-- Animated Success Icon -->
        <div class="success-icon-wrapper">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <span class="confirmation-badge">
            <i class="bi bi-patch-check-fill"></i> Order Confirmed
        </span>

        <h1 class="confirmation-title">Thank you, <?= e($order['customer_name']) ?>!</h1>
        <p class="confirmation-subtitle">
            Your fresh, protein-rich meal ticket has been received by the kitchen at <strong><?= e($order['restaurant_name']) ?></strong>.
        </p>

        <!-- Order Metadata Grid -->
        <div class="order-receipt-card">
            <div class="receipt-row">
                <span class="receipt-label">Order Reference</span>
                <span class="receipt-val order-num-badge">#<?= e($order['order_number']) ?></span>
            </div>

            <div class="receipt-row">
                <span class="receipt-label">Dining Mode</span>
                <span class="receipt-val">
                    <?= $order['order_type'] === 'dine_in' ? '<i class="bi bi-cup-hot"></i> Dine-In' : '<i class="bi bi-bag"></i> Takeaway / Pickup' ?>
                </span>
            </div>

            <?php if (!empty($order['table_number'])): ?>
                <div class="receipt-row">
                    <span class="receipt-label">Table Number</span>
                    <span class="receipt-val table-val"><?= (stripos((string)$order['table_number'], 'table') === 0) ? e($order['table_number']) : 'Table ' . e($order['table_number']) ?></span>
                </div>
            <?php endif; ?>

            <div class="receipt-row">
                <span class="receipt-label">Payment Status</span>
                <span class="receipt-val payment-val">
                    <i class="bi bi-shield-check"></i> Paid (<?= strtoupper($order['payment_status'] ?? 'COMPLETED') ?>)
                </span>
            </div>

            <div class="receipt-row total-row">
                <span class="receipt-label">Total Amount Paid</span>
                <span class="receipt-val total-amount-val"><?= formatCurrency((float)$order['total_amount']) ?></span>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div class="receipt-items-section">
            <h2 class="receipt-section-title">
                <i class="bi bi-bag-check" style="color:var(--primary-green);"></i>
                <span>Dishes in this Order (<?= count($order['items']) ?>)</span>
            </h2>
            <div class="receipt-items-list">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="receipt-item-row">
                        <div class="item-left">
                            <div class="item-title">
                                <span class="item-qty"><?= (int)$item['quantity'] ?>×</span>
                                <strong><?= e($item['food_name_snapshot']) ?></strong>
                                <?php if (!empty($item['variant_name_snapshot'])): ?>
                                    <span class="item-variant">(<?= e($item['variant_name_snapshot']) ?>)</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($item['customizations'])): ?>
                                <div class="item-addons">
                                    + <?= implode(', ', array_map(fn($c) => e($c['customization_name_snapshot']), $item['customizations'])) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($item['calories'] !== null): ?>
                                <div class="item-macros">
                                    <i class="bi bi-fire"></i> <?= (int)$item['calories'] ?> kcal · <?= (float)$item['protein'] ?>g protein
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="item-price"><?= formatCurrency((float)$item['total_price']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="confirmation-actions">
            <a href="<?= url('/menu/tracking/' . $order['order_number']) ?>" class="btn btn-primary btn-track-live">
                <i class="bi bi-clock-history"></i>
                <span>Track Live Preparation</span>
            </a>
            <a href="<?= url('/menu') ?>" class="btn btn-outline btn-back-menu">
                <span>Browse Menu</span>
            </a>
        </div>
    </div>
</div>
