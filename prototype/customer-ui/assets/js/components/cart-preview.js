/**
 * Healthy Bite — Cart Preview Component
 * Desktop dark sidebar cart card (#122A16) & Mobile sticky floating pill
 */

const CartPreviewComponent = {
  renderSidebar(containerId = "sidebar-cart-container") {
    const container = document.getElementById(containerId);
    if (!container) return;

    const cart = AppState.getCart();
    const summary = PricingEngine.calculateCartSummary(cart);

    if (cart.length === 0) {
      container.innerHTML = `
        <div class="sidebar-cart-card">
          <div class="sidebar-cart-header">
            <div class="sidebar-cart-title">
              <span>Your Cart</span>
              <span class="sidebar-cart-count">0</span>
            </div>
          </div>
          <div class="sidebar-cart-empty">
            <i class="bi bi-bag"></i>
            <p>Your cart is empty.<br>Add some fresh, healthy dishes!</p>
          </div>
        </div>
      `;
      return;
    }

    container.innerHTML = `
      <div class="sidebar-cart-card">
        <div class="sidebar-cart-header">
          <div class="sidebar-cart-title">
            <span>Your Cart</span>
            <span class="sidebar-cart-count">${summary.totalItemsCount}</span>
          </div>
          <button type="button" class="sidebar-cart-clear" id="btn-clear-cart">
            Clear all
          </button>
        </div>

        <div class="sidebar-cart-items">
          ${cart.map(item => CartItemComponent.renderSidebarItem(item)).join("")}
        </div>

        <div class="sidebar-cart-footer">
          <div class="sidebar-subtotal-row">
            <span class="sidebar-subtotal-label">Subtotal</span>
            <span class="sidebar-subtotal-val">₹${summary.subtotal}</span>
          </div>
          <a href="cart.html" class="sidebar-cart-btn">
            <span>Proceed to Cart</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    `;

    // Clear cart listener
    const clearBtn = container.querySelector("#btn-clear-cart");
    if (clearBtn) {
      clearBtn.addEventListener("click", () => {
        AppState.clearCart();
        ToastComponent.show("Cart cleared", "info", "bi-trash");
      });
    }

    // Item quantity buttons
    container.querySelectorAll(".btn-cart-qty-minus").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        AppState.updateCartQuantity(id, -1);
      });
    });

    container.querySelectorAll(".btn-cart-qty-plus").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        AppState.updateCartQuantity(id, 1);
      });
    });
  },

  renderMobileFloating(containerId = "mobile-floating-cart-container") {
    const container = document.getElementById(containerId);
    if (!container) return;

    const cart = AppState.getCart();
    const summary = PricingEngine.calculateCartSummary(cart);

    if (cart.length === 0) {
      container.innerHTML = "";
      return;
    }

    container.innerHTML = `
      <div class="mobile-floating-cart visible" id="mobile-floating-cart-bar" role="button" tabindex="0" onclick="window.location.href='cart.html'">
        <div class="mobile-cart-left">
          <span class="mobile-cart-count-badge">${summary.totalItemsCount}</span>
          <span class="mobile-cart-label">View Cart</span>
        </div>
        <div class="mobile-cart-right">
          <span class="mobile-cart-price">₹${summary.subtotal}</span>
          <i class="bi bi-arrow-right mobile-cart-arrow"></i>
        </div>
      </div>
    `;
  },

  initListeners() {
    AppState.subscribe((state, changedKey) => {
      if (changedKey === "cart") {
        this.renderSidebar();
        this.renderMobileFloating();
      }
    });
  }
};
