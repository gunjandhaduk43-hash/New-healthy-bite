/**
 * Healthy Bite — Reusable Quantity Stepper Component
 */

const QuantityControlComponent = {
  render(currentQty = 1, min = 1, max = 20) {
    return `
      <div class="quantity-control" data-min="${min}" data-max="${max}">
        <button type="button" class="qty-btn btn-qty-decr" aria-label="Decrease" ${currentQty <= min ? 'disabled' : ''}>
          <i class="bi bi-dash"></i>
        </button>
        <span class="qty-display">${currentQty}</span>
        <button type="button" class="qty-btn btn-qty-incr" aria-label="Increase" ${currentQty >= max ? 'disabled' : ''}>
          <i class="bi bi-plus"></i>
        </button>
      </div>
    `;
  }
};
