/**
 * Healthy Bite — Cart Page Controller (cart.html)
 */

const CartPageController = {
  itemsContainerId: "cart-items-container",
  summaryContainerId: "cart-summary-container",

  init() {
    this.render();

    AppState.subscribe((state, changedKey) => {
      if (changedKey === "cart") {
        this.render();
      }
    });
  },

  render() {
    const itemsContainer = document.getElementById(this.itemsContainerId);
    const summaryContainer = document.getElementById(this.summaryContainerId);
    if (!itemsContainer || !summaryContainer) return;

    const cart = AppState.getCart();
    const summary = PricingEngine.calculateCartSummary(cart);

    if (cart.length === 0) {
      itemsContainer.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">
            <i class="bi bi-bag-x"></i>
          </div>
          <h3 class="empty-state-title">Your cart is empty</h3>
          <p class="empty-state-desc">Explore our delicious, nutrition-aware menu and add items to your table order.</p>
          <a href="menu.html" class="btn btn-primary">
            <i class="bi bi-book"></i>
            <span>Browse Full Menu</span>
          </a>
        </div>
      `;

      summaryContainer.innerHTML = `
        <div class="order-summary-card" style="opacity: 0.7;">
          <h3 class="summary-title">Order Summary</h3>
          <p style="font-size: var(--font-size-sm); color: var(--color-secondary-text); margin-bottom: var(--space-4);">No items in cart</p>
          <div class="summary-line total-line">
            <span>Total</span>
            <span class="val">₹0</span>
          </div>
          <button type="button" class="btn btn-primary btn-block" disabled>Proceed to Checkout</button>
        </div>
      `;
      return;
    }

    // Render items list
    itemsContainer.innerHTML = `
      <div class="cart-items-card">
        <div class="cart-items-header">
          <h2 style="font-size: var(--font-size-lg); font-weight: var(--font-weight-bold);">
            Items in Order (${summary.totalItemsCount})
          </h2>
          <button type="button" class="sidebar-cart-clear" id="btn-cart-page-clear">
            Clear all items
          </button>
        </div>
        <div class="cart-items-list">
          ${cart.map(item => CartItemComponent.renderFullCartRow(item)).join("")}
        </div>
        <div style="margin-top: var(--space-4); display: flex; justify-content: space-between; align-items: center;">
          <a href="menu.html" class="btn btn-outline btn-sm">
            <i class="bi bi-arrow-left"></i>
            <span>Add More Dishes</span>
          </a>
        </div>
      </div>
    `;

    // Render Order Summary
    summaryContainer.innerHTML = `
      <div class="order-summary-card">
        <h3 class="summary-title">Order Summary</h3>
        
        <div class="table-context-box">
          <div style="display:flex; align-items:center; gap: 8px;">
            <i class="bi bi-geo-alt-fill" style="color: var(--color-primary-green);"></i>
            <span>Dining at Table 12</span>
          </div>
          <span style="font-size: 11px; color: var(--color-primary-green); background: #ffffff; padding: 2px 6px; border-radius: 4px;">Dine-In</span>
        </div>

        <div class="summary-line">
          <span>Items Subtotal</span>
          <span>₹${summary.subtotal}</span>
        </div>

        <div class="summary-line">
          <span>Taxes & GST (5%)</span>
          <span>₹${summary.gstTax}</span>
        </div>

        <div class="summary-line">
          <span>Restaurant Service Fee</span>
          <span>₹${summary.platformFee}</span>
        </div>

        <div class="summary-line total-line">
          <span>Final Payable</span>
          <span class="val">₹${summary.finalTotal}</span>
        </div>

        <button type="button" class="btn btn-primary btn-block btn-lg" id="btn-proceed-checkout">
          <span>Proceed to Checkout</span>
          <i class="bi bi-arrow-right"></i>
        </button>

        <p style="font-size: 11px; color: var(--color-secondary-text); text-align: center; margin-top: 12px;">
          <i class="bi bi-shield-check"></i> Clean, verified food crafted fresh to order.
        </p>
      </div>
    `;

    // Event listeners
    const clearBtn = itemsContainer.querySelector("#btn-cart-page-clear");
    if (clearBtn) {
      clearBtn.addEventListener("click", () => {
        AppState.clearCart();
        ToastComponent.show("All items removed from cart", "info", "bi-trash");
      });
    }

    itemsContainer.querySelectorAll(".btn-full-qty-minus").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        AppState.updateCartQuantity(id, -1);
      });
    });

    itemsContainer.querySelectorAll(".btn-full-qty-plus").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        AppState.updateCartQuantity(id, 1);
      });
    });

    itemsContainer.querySelectorAll(".btn-cart-item-delete").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        AppState.removeCartItem(id);
        ToastComponent.show("Item removed", "info", "bi-trash");
      });
    });

    const checkoutBtn = summaryContainer.querySelector("#btn-proceed-checkout");
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", () => {
        window.location.href = "checkout.html";
      });
    }
  }
};
