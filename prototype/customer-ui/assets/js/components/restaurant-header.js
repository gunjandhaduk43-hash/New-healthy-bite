/**
 * Healthy Bite — Restaurant Header Component
 * Renders Greenhouse Kitchen brand header with branch and locked Table 12 context
 */

const RestaurantHeaderComponent = {
  render(containerId = "site-header-container") {
    const container = document.getElementById(containerId);
    if (!container) return;

    const restaurant = HEALTHY_BITE_DATA.restaurant;
    const cartItems = AppState.getCart();
    const cartSummary = PricingEngine.calculateCartSummary(cartItems);

    container.innerHTML = `
      <header class="site-header">
        <div class="app-container">
          <div class="header-inner">
            <a href="menu.html" class="brand-section">
              <div class="restaurant-logo-circle">
                <i class="bi bi-flower2"></i>
              </div>
              <div class="brand-info">
                <span class="brand-name">${restaurant.name}</span>
                <span class="brand-meta">
                  <span>${restaurant.branch.name} · ${restaurant.branch.city}</span>
                </span>
              </div>
            </a>

            <div class="header-actions">
              <div class="table-badge" title="Dine-in Order Session">
                <span class="dot"></span>
                <span>Table ${restaurant.current_table}</span>
              </div>

              <a href="cart.html" class="header-btn" title="View Cart" id="header-cart-btn">
                <i class="bi bi-bag"></i>
                <span class="header-cart-badge" id="header-cart-count" style="${cartSummary.totalItemsCount > 0 ? '' : 'display:none;'}">
                  ${cartSummary.totalItemsCount}
                </span>
              </a>
            </div>
          </div>
        </div>
      </header>
    `;

    // Subscribe to cart changes to update badge
    AppState.subscribe((state, changedKey) => {
      if (changedKey === "cart") {
        const badge = document.getElementById("header-cart-count");
        if (badge) {
          const summary = PricingEngine.calculateCartSummary(state.cart);
          if (summary.totalItemsCount > 0) {
            badge.style.display = "flex";
            badge.textContent = summary.totalItemsCount;
          } else {
            badge.style.display = "none";
          }
        }
      }
    });
  }
};
