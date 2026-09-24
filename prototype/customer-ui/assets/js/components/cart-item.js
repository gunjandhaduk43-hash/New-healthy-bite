/**
 * Healthy Bite — Cart Item Component
 * Renders individual cart item row with custom options and quantity steppers
 */

const CartItemComponent = {
  renderSidebarItem(cartItem) {
    const itemTotal = PricingEngine.calculateItemTotal(
      cartItem.food_item,
      cartItem.selected_variant,
      cartItem.selected_customizations,
      cartItem.quantity
    );

    const variantStr = cartItem.selected_variant ? cartItem.selected_variant.name : "";
    const customStr = (cartItem.selected_customizations || []).map(c => c.name).join(", ");
    const detailsStr = [variantStr, customStr].filter(Boolean).join(" · ");

    // Nutrition summary
    const dynamicNutrition = NutritionEngine.calculateItemNutrition(
      cartItem.food_item,
      cartItem.selected_variant,
      cartItem.selected_customizations,
      cartItem.quantity
    );

    return `
      <div class="sidebar-item" data-cart-item-id="${cartItem.id}">
        <div class="sidebar-item-info">
          <div class="sidebar-item-name">${cartItem.food_item.name}</div>
          ${detailsStr ? `<div class="sidebar-item-details">${detailsStr}</div>` : ''}
          ${dynamicNutrition.calories ? `<div class="sidebar-item-macros">${dynamicNutrition.calories} kcal · ${dynamicNutrition.protein}g protein</div>` : ''}
        </div>
        <div class="sidebar-item-actions">
          <div class="sidebar-item-price">₹${itemTotal}</div>
          <div class="sidebar-qty-control">
            <button type="button" class="sidebar-qty-btn btn-cart-qty-minus" data-id="${cartItem.id}" aria-label="Decrease">
              <i class="bi bi-dash"></i>
            </button>
            <span class="sidebar-qty-val">${cartItem.quantity}</span>
            <button type="button" class="sidebar-qty-btn btn-cart-qty-plus" data-id="${cartItem.id}" aria-label="Increase">
              <i class="bi bi-plus"></i>
            </button>
          </div>
        </div>
      </div>
    `;
  },

  renderFullCartRow(cartItem) {
    const itemTotal = PricingEngine.calculateItemTotal(
      cartItem.food_item,
      cartItem.selected_variant,
      cartItem.selected_customizations,
      cartItem.quantity
    );

    const variantStr = cartItem.selected_variant ? cartItem.selected_variant.name : "";
    const customStr = (cartItem.selected_customizations || []).map(c => c.name).join(", ");

    const dynamicNutrition = NutritionEngine.calculateItemNutrition(
      cartItem.food_item,
      cartItem.selected_variant,
      cartItem.selected_customizations,
      cartItem.quantity
    );

    return `
      <div class="cart-item-row" data-cart-item-id="${cartItem.id}">
        <img 
          src="${cartItem.food_item.image}" 
          alt="${cartItem.food_item.name}" 
          class="cart-item-img"
          onerror="this.onerror=null; this.src='${cartItem.food_item.fallback_image}';"
        />
        
        <div class="cart-item-meta">
          <h4 class="cart-item-name">${cartItem.food_item.name}</h4>
          ${variantStr ? `<div class="cart-item-variant">${variantStr}</div>` : ''}
          ${customStr ? `<div class="cart-item-customizations">${customStr}</div>` : ''}
          ${dynamicNutrition.calories ? `
            <div class="cart-item-nutrition-chip">
              <i class="bi bi-fire"></i>
              <span>${dynamicNutrition.calories} kcal · ${dynamicNutrition.protein}g protein</span>
            </div>
          ` : ''}
        </div>

        <div class="cart-item-controls">
          <div class="cart-item-total">₹${itemTotal}</div>
          <div style="display:flex; align-items:center; gap: 8px;">
            <div class="quantity-control">
              <button type="button" class="qty-btn btn-full-qty-minus" data-id="${cartItem.id}" aria-label="Decrease">
                <i class="bi bi-dash"></i>
              </button>
              <span class="qty-display">${cartItem.quantity}</span>
              <button type="button" class="qty-btn btn-full-qty-plus" data-id="${cartItem.id}" aria-label="Increase">
                <i class="bi bi-plus"></i>
              </button>
            </div>
            <button type="button" class="cart-item-delete btn-cart-item-delete" data-id="${cartItem.id}" title="Remove item" aria-label="Remove item">
              <i class="bi bi-trash3"></i>
            </button>
          </div>
        </div>
      </div>
    `;
  }
};
