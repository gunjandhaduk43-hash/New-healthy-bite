/**
 * Healthy Bite — Checkout Page Controller (checkout.html)
 */

const CheckoutController = {
  orderType: "dine-in", // 'dine-in' | 'takeaway'
  paymentMethod: "upi", // 'upi' | 'card' | 'cash'

  init() {
    this.render();
  },

  render() {
    const summaryContainer = document.getElementById("checkout-summary-container");
    if (!summaryContainer) return;

    const cart = AppState.getCart();
    if (cart.length === 0) {
      window.location.href = "cart.html";
      return;
    }

    const summary = PricingEngine.calculateCartSummary(cart);

    summaryContainer.innerHTML = `
      <div class="order-summary-card">
        <h3 class="summary-title">Order Breakdown</h3>
        
        <div style="margin-bottom: var(--space-4); max-height: 220px; overflow-y: auto;">
          ${cart.map(item => {
            const itemTotal = PricingEngine.calculateItemTotal(
              item.food_item,
              item.selected_variant,
              item.selected_customizations,
              item.quantity
            );
            return `
              <div style="display: flex; justify-content: space-between; font-size: var(--font-size-xs); margin-bottom: 8px;">
                <div>
                  <span style="font-weight: 600;">${item.quantity}x</span> ${item.food_item.name}
                  ${item.selected_variant ? `<span style="color: var(--color-secondary-text);">(${item.selected_variant.name})</span>` : ''}
                </div>
                <div style="font-weight: 600;">₹${itemTotal}</div>
              </div>
            `;
          }).join("")}
        </div>

        <div class="summary-line">
          <span>Subtotal</span>
          <span>₹${summary.subtotal}</span>
        </div>

        <div class="summary-line">
          <span>GST (5%)</span>
          <span>₹${summary.gstTax}</span>
        </div>

        <div class="summary-line">
          <span>Service Fee</span>
          <span>₹${summary.platformFee}</span>
        </div>

        <div class="summary-line total-line">
          <span>Amount to Pay</span>
          <span class="val">₹${summary.finalTotal}</span>
        </div>

        <button type="button" class="btn btn-primary btn-block btn-lg" id="btn-place-order">
          <i class="bi bi-check2-circle"></i>
          <span>Place Order • ₹${summary.finalTotal}</span>
        </button>
      </div>
    `;

    this.attachEvents(summary);
  },

  attachEvents(summary) {
    // Dine-in vs Takeaway toggle
    document.querySelectorAll(".order-type-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".order-type-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        this.orderType = btn.dataset.type;

        const tableBox = document.getElementById("table-context-callout");
        if (tableBox) {
          tableBox.style.display = this.orderType === "dine-in" ? "flex" : "none";
        }
      });
    });

    // Payment Method cards
    document.querySelectorAll(".payment-method-card").forEach(card => {
      card.addEventListener("click", () => {
        document.querySelectorAll(".payment-method-card").forEach(c => c.classList.remove("selected"));
        card.classList.add("selected");
        this.paymentMethod = card.dataset.method;
      });
    });

    // Place Order Button
    const placeOrderBtn = document.getElementById("btn-place-order");
    if (placeOrderBtn) {
      placeOrderBtn.addEventListener("click", () => {
        const nameInput = document.getElementById("customer-name");
        const phoneInput = document.getElementById("customer-phone");

        const name = nameInput ? nameInput.value.trim() : "";
        const phone = phoneInput ? phoneInput.value.trim() : "";

        if (!name) {
          ToastComponent.show("Please enter your name", "warning", "bi-exclamation-circle");
          if (nameInput) nameInput.focus();
          return;
        }

        if (!phone || phone.length < 10) {
          ToastComponent.show("Please enter a valid 10-digit mobile number", "warning", "bi-exclamation-circle");
          if (phoneInput) phoneInput.focus();
          return;
        }

        placeOrderBtn.disabled = true;
        placeOrderBtn.innerHTML = `
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="margin-right: 8px;"></span>
          <span>Placing your order...</span>
        `;

        setTimeout(() => {
          const orderNum = "HB" + (Math.floor(1000 + Math.random() * 9000));
          const orderData = {
            orderNumber: orderNum,
            customerName: name,
            customerPhone: phone,
            orderType: this.orderType,
            tableNumber: "12",
            paymentMethod: this.paymentMethod,
            items: [...AppState.getCart()],
            summary: summary,
            placedAt: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            status: "Placed"
          };

          AppState.setCurrentOrder(orderData);
          AppState.clearCart();

          window.location.href = `confirmation.html?order=${orderNum}`;
        }, 800);
      });
    }
  }
};
