<div class="app-container checkout-container">
    <div class="checkout-back-bar">
        <a href="<?= url('/menu') ?>" class="btn-back-link">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Menu</span>
        </a>
    </div>

    <div class="checkout-header-row">
        <h1 class="checkout-page-title">Review &amp; Checkout</h1>
        <span class="checkout-step-badge">Final Step · Contactless Dining</span>
    </div>

    <div class="checkout-grid">
        <!-- Left Column: Dining Option, Contact Details & Payment -->
        <div class="checkout-form-column">
            <form id="checkoutForm" novalidate>
                <!-- 1. Dining Option Card -->
                <div class="checkout-card">
                    <h2 class="card-title">
                        <i class="bi bi-shop" style="color:var(--primary-green);"></i>
                        <span>Dining Option</span>
                    </h2>

                    <div class="segmented-switch order-type-switch" role="tablist">
                        <button type="button" class="switch-btn active" id="switchDineIn" role="tab" aria-selected="true">
                            <i class="bi bi-cup-hot"></i>
                            <span>Dine-In</span>
                        </button>
                        <button type="button" class="switch-btn" id="switchTakeaway" role="tab" aria-selected="false">
                            <i class="bi bi-bag"></i>
                            <span>Takeaway / Pick up</span>
                        </button>
                    </div>

                    <!-- Table Context (Dine-In only) -->
                    <div id="tableInfoGroup" class="form-group table-info-group">
                        <label class="form-label" for="selectedTableId">Table Assignment</label>
                        <?php if (!empty($tableContext)): ?>
                            <div class="table-session-locked-badge">
                                <i class="bi bi-qr-code-scan"></i>
                                <span>Table <?= e($tableContext['table_number']) ?> (Verified Session)</span>
                            </div>
                        <?php else: ?>
                            <select id="selectedTableId" class="form-select">
                                <?php if (!empty($availableTables)): ?>
                                    <?php foreach ($availableTables as $tbl): ?>
                                        <option value="<?= (int)$tbl['id'] ?>">
                                            <?= e($tbl['table_number']) ?> · <?= ucfirst(e($tbl['status'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Table 1 · Available</option>
                                <?php endif; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <!-- Takeaway Note (Takeaway only) -->
                    <div id="takeawayInfoNote" class="takeaway-notice" style="display:none;">
                        <i class="bi bi-box2-heart" style="color:var(--primary-green); font-size:18px;"></i>
                        <span>Your order will be freshly packed with eco-friendly biodegradable containers for pickup.</span>
                    </div>
                </div>

                <!-- 2. Customer Contact Details -->
                <div class="checkout-card">
                    <h2 class="card-title">
                        <i class="bi bi-person" style="color:var(--primary-green);"></i>
                        <span>Guest Details</span>
                    </h2>

                    <div class="form-group">
                        <label class="form-label" for="customerName">Full Name *</label>
                        <input type="text" id="customerName" class="form-input" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                        <span class="form-hint">For chef callout and table order confirmation</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customerPhone">Mobile Number (Optional)</label>
                        <input type="tel" id="customerPhone" class="form-input" placeholder="e.g. 98765 43210" autocomplete="tel">
                        <span class="form-hint">Receive live SMS alerts when food is ready</span>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="orderNotes">Cooking Preferences / Instructions</label>
                        <textarea id="orderNotes" class="form-textarea" rows="2" placeholder="e.g. Dressing on side, less spicy, no cutlery..."></textarea>
                    </div>
                </div>

                <!-- 3. Payment Method -->
                <div class="checkout-card">
                    <h2 class="card-title">
                        <i class="bi bi-credit-card-2-front" style="color:var(--primary-green);"></i>
                        <span>Payment Method</span>
                    </h2>

                    <div class="payment-methods-grid">
                        <div class="payment-method-card selected" data-method="upi" tabindex="0" role="radio" aria-checked="true">
                            <div class="payment-icon"><i class="bi bi-qr-code"></i></div>
                            <div class="payment-name">Instant UPI</div>
                            <span class="payment-sub">GPay / PhonePe / Paytm</span>
                        </div>
                        <div class="payment-method-card" data-method="card" tabindex="0" role="radio" aria-checked="false">
                            <div class="payment-icon"><i class="bi bi-credit-card-2-front"></i></div>
                            <div class="payment-name">Card / POS</div>
                            <span class="payment-sub">Tap at table / counter</span>
                        </div>
                        <div class="payment-method-card" data-method="cash" tabindex="0" role="radio" aria-checked="false">
                            <div class="payment-icon"><i class="bi bi-cash-stack"></i></div>
                            <div class="payment-name">Pay at Counter</div>
                            <span class="payment-sub">Cash or split payment</span>
                        </div>
                    </div>

                    <div class="payment-security-note">
                        <i class="bi bi-shield-check" style="color:var(--color-success);"></i>
                        <span>Zero-commission direct restaurant ordering. 100% encrypted &amp; verified.</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-submit-order" id="btnSubmitOrder">
                    <i class="bi bi-lock-fill"></i>
                    <span id="btnSubmitOrderText">Place Order</span>
                </button>
            </form>
        </div>

        <!-- Right Column: Sticky Order Summary -->
        <div class="checkout-summary-column">
            <div class="checkout-card summary-card">
                <h2 class="card-title">
                    <i class="bi bi-receipt" style="color:var(--primary-green);"></i>
                    <span>Order Summary</span>
                </h2>

                <div id="checkoutSummaryItems" class="summary-items-list">
                    <!-- Populated dynamically by checkout.js -->
                </div>

                <div class="checkout-totals-breakdown">
                    <div class="cart-summary-line nutrition-line" id="checkoutNutritionLine" style="background:#f8fafc; padding:6px 10px; border-radius:6px; margin-bottom:8px; font-size:12px; display:flex; justify-content:space-between;">
                        <span style="display:flex; align-items:center; gap:5px; color:#475569; font-weight:600;"><i class="bi bi-heart-pulse-fill" style="color:var(--primary-green);"></i> Total Sugar:</span>
                        <span id="checkoutTotalSugar" style="font-weight:700; color:#b45309;">0 g</span>
                    </div>
                    <div class="cart-summary-line">
                        <span>Items Subtotal</span>
                        <span id="checkoutSubtotal" class="summary-val">₹0.00</span>
                    </div>
                    <div class="cart-summary-line">
                        <span>GST (5%)</span>
                        <span id="checkoutTax" class="summary-val">₹0.00</span>
                    </div>
                    <div class="cart-summary-line total-line">
                        <span>Grand Total</span>
                        <span id="checkoutTotal" class="summary-val total-highlight">₹0.00</span>
                    </div>
                </div>

                <div class="summary-security-banner">
                    <i class="bi bi-shield-check" style="color:var(--color-success); font-size:16px;"></i>
                    <span>Your order is directly transmitted to the kitchen queue upon submission.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Embed State Context for JS -->
<script>
    window.State = window.State || {};
    window.State.restaurant = <?= json_encode($restaurant, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.branch = <?= json_encode($branch, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.table = <?= json_encode($tableContext, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.State.qrToken = <?= json_encode($qrToken, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>

<script src="<?= asset('js/state.js') ?>"></script>
<script src="<?= asset('js/utils.js') ?>"></script>
<script src="<?= asset('js/api.js') ?>"></script>
<script src="<?= asset('js/pricing.js') ?>"></script>
<script src="<?= asset('js/cart.js') ?>"></script>
<script src="<?= asset('js/checkout.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
