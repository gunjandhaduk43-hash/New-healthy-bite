/**
 * Healthy Bite — Pricing Engine
 * Computes exact item and cart totals dynamically
 */

const PricingEngine = {
  /**
   * Calculates the unit price of a configured food item.
   * Formula: base_price + variant_adjustment + sum(customization_option_adjustment * qty)
   */
  calculateUnitPrice(foodItem, selectedVariant, selectedCustomizations = []) {
    if (!foodItem) return 0;
    
    let unitPrice = Number(foodItem.base_price) || 0;

    // Add variant adjustment
    if (selectedVariant && typeof selectedVariant.price_adjustment === 'number') {
      unitPrice += selectedVariant.price_adjustment;
    }

    // Add customizations adjustments
    if (Array.isArray(selectedCustomizations)) {
      selectedCustomizations.forEach(item => {
        const adjustment = Number(item.price_adjustment) || 0;
        const qty = Number(item.quantity) || 1;
        unitPrice += adjustment * qty;
      });
    }

    return Math.max(0, unitPrice);
  },

  /**
   * Calculates total for a configured item given its quantity.
   * Formula: unit_price * quantity
   */
  calculateItemTotal(foodItem, selectedVariant, selectedCustomizations = [], quantity = 1) {
    const unitPrice = this.calculateUnitPrice(foodItem, selectedVariant, selectedCustomizations);
    const qty = Math.max(1, parseInt(quantity, 10) || 1);
    return unitPrice * qty;
  },

  /**
   * Computes subtotal, taxes (5% GST), platform fee (₹5), and final total for cart items.
   */
  calculateCartSummary(cartItems = []) {
    let subtotal = 0;
    let totalItemsCount = 0;

    cartItems.forEach(cartItem => {
      const itemTotal = this.calculateItemTotal(
        cartItem.food_item,
        cartItem.selected_variant,
        cartItem.selected_customizations,
        cartItem.quantity
      );
      subtotal += itemTotal;
      totalItemsCount += Number(cartItem.quantity) || 1;
    });

    const gstTax = subtotal > 0 ? Math.round(subtotal * 0.05) : 0; // 5% GST
    const platformFee = subtotal > 0 ? 5 : 0; // ₹5 platform fee
    const finalTotal = subtotal + gstTax + platformFee;

    return {
      subtotal,
      totalItemsCount,
      gstTax,
      platformFee,
      finalTotal
    };
  },

  /**
   * Currency formatter helper.
   */
  formatCurrency(amount, currency = "₹") {
    const num = Number(amount) || 0;
    return `${currency}${num.toLocaleString('en-IN')}`;
  }
};
