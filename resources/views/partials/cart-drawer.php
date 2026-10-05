<!-- Cart Drawer (Prompt Requirements #24 & #25) -->
<div class="cart-drawer-backdrop" id="cartDrawerBackdrop" role="dialog" aria-modal="true" aria-labelledby="cartDrawerTitle">
    <div class="cart-drawer">
        <div class="cart-header">
            <h3 class="cart-header-title" id="cartDrawerTitle">
                <i class="bi bi-bag-check-fill" style="color:var(--primary-green);"></i>
                <span>Your Table Order</span>
            </h3>
            <button type="button" class="cart-close-btn" id="cartCloseBtn" aria-label="Close cart drawer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="cart-body">
            <!-- Dynamic Cart Items List -->
            <div id="cartItemsList" class="cart-items-container"></div>

            <!-- Contextual Complete-The-Meal Recommendations (Prompt Requirement #25) -->
            <div class="cart-suggestions-box" id="cartSuggestionsBox" style="display:none;">
                <div class="suggestions-header">
                    <div class="suggestions-title">
                        <i class="bi bi-stars" style="color:var(--accent-amber);"></i>
                        <span>Complete Your Meal</span>
                    </div>
                    <span class="suggestions-sub">Chef recommended pairings</span>
                </div>
                <div class="suggestions-scroll-row" id="cartSuggestionsRow">
                    <!-- Populated dynamically by cart.js -->
                </div>
            </div>

            <!-- Empty Cart State -->
            <div class="cart-empty-state" id="cartEmptyState">
                <div class="cart-empty-icon"><i class="bi bi-basket3"></i></div>
                <h4>Your cart is empty</h4>
                <p>Explore our menu and add delicious protein-rich meals.</p>
                <button type="button" class="btn btn-primary btn-sm" onclick="Cart.close()">
                    Browse Menu
                </button>
            </div>
        </div>

        <div class="cart-footer" id="cartFooter" style="display:none;">
            <div class="cart-summary-breakdown">
                <div class="cart-nutrition-summary" id="cartNutritionRow">
                    <div class="cart-nutrition-header">
                        <span style="display:flex; align-items:center; gap:5px;">
                            <i class="bi bi-activity" style="color:var(--primary-green);"></i> Order Nutrition Totals:
                        </span>
                    </div>
                    <div class="cart-nutrition-chips">
                        <div class="cart-nutrition-chip chip-calories" title="Total Calories">
                            <span class="chip-label"><i class="bi bi-fire"></i> Calories</span>
                            <span class="chip-value" id="cartTotalCalories">0 kcal</span>
                        </div>
                        <div class="cart-nutrition-chip chip-protein" title="Total Protein">
                            <span class="chip-label"><i class="bi bi-shield-check"></i> Protein</span>
                            <span class="chip-value" id="cartTotalProtein">0 g</span>
                        </div>
                        <div class="cart-nutrition-chip chip-sugar" title="Total Sugar">
                            <span class="chip-label"><i class="bi bi-heart-pulse-fill"></i> Sugar</span>
                            <span class="chip-value" id="cartTotalSugar">0 g</span>
                        </div>
                    </div>
                </div>
                <div class="cart-summary-line">
                    <span class="summary-lbl">Subtotal</span>
                    <span class="summary-val" id="cartSubtotal">₹0</span>
                </div>
                <div class="cart-summary-line">
                    <span class="summary-lbl">GST (5%)</span>
                    <span class="summary-val" id="cartTax">₹0</span>
                </div>
                <div class="cart-summary-line total-line">
                    <span class="summary-lbl">Total Amount</span>
                    <span class="summary-val total-price" id="cartTotal">₹0</span>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-checkout" id="btnProceedCheckout">
                <span>Proceed to Checkout</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Floating Cart Bar for Mobile View -->
<div class="floating-cart-bar" id="floatingCartBar" style="display:none;" role="button" aria-label="View current cart">
    <div class="floating-cart-left">
        <span class="floating-cart-badge" id="floatingCartBadge">0</span>
        <span class="floating-cart-label">View Order</span>
    </div>
    <div class="floating-cart-right">
        <span class="floating-cart-total" id="floatingCartTotal">₹0</span>
        <i class="bi bi-chevron-right"></i>
    </div>
</div>
