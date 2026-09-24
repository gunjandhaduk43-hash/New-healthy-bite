/**
 * Healthy Bite — Food Detail Modal & Customizer Component
 * Implements 2-panel desktop & full-screen mobile sheet matching Figma.
 * Handles dynamic price & nutrition math with strictly enforced food-scoped options.
 */

const FoodDetailModalComponent = {
  _modalBackdrop: null,
  _currentFood: null,
  _selectedVariant: null,
  _selectedCustomizations: [], // array of { id, name, group_id, price_adjustment, nutrition_adjustment, quantity }
  _quantity: 1,

  init() {
    if (!this._modalBackdrop) {
      this._modalBackdrop = document.createElement("div");
      this._modalBackdrop.className = "modal-backdrop";
      this._modalBackdrop.id = "food-detail-modal-backdrop";
      this._modalBackdrop.setAttribute("role", "dialog");
      this._modalBackdrop.setAttribute("aria-modal", "true");
      document.body.appendChild(this._modalBackdrop);

      // Close on backdrop click (outside modal)
      this._modalBackdrop.addEventListener("click", (e) => {
        if (e.target === this._modalBackdrop) {
          this.close();
        }
      });

      // Close on Escape key
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && this._modalBackdrop.classList.contains("open")) {
          this.close();
        }
      });
    }
  },

  open(foodItem) {
    this.init();
    this._currentFood = foodItem;
    this._quantity = 1;

    // Set default variant
    const defaultVariant = (foodItem.variants || []).find(v => v.is_default) || (foodItem.variants || [])[0] || null;
    this._selectedVariant = defaultVariant;

    // Pre-select default options in required single groups
    this._selectedCustomizations = [];
    (foodItem.customizations || []).forEach(group => {
      if (group.type === "single" && group.required) {
        const defaultOption = group.options.find(o => o.is_default) || group.options[0];
        if (defaultOption) {
          this._selectedCustomizations.push({
            id: defaultOption.id,
            group_id: group.id,
            name: defaultOption.name,
            price_adjustment: defaultOption.price_adjustment || 0,
            nutrition_adjustment: defaultOption.nutrition_adjustment || {},
            quantity: 1
          });
        }
      }
    });

    this.render();
    this._modalBackdrop.classList.add("open");
    document.body.style.overflow = "hidden"; // Prevent background scroll
  },

  close() {
    if (this._modalBackdrop) {
      this._modalBackdrop.classList.remove("open");
      document.body.style.overflow = "";
    }
  },

  validateRequiredSelections() {
    if (!this._currentFood || !this._currentFood.customizations) return true;

    for (const group of this._currentFood.customizations) {
      if (group.required) {
        const count = this._selectedCustomizations.filter(c => c.group_id === group.id).length;
        if (count < (group.min_quantity || 1)) {
          return false;
        }
      }
    }
    return true;
  },

  render() {
    const food = this._currentFood;
    if (!food) return;

    const isVeg = food.food_type === "vegetarian";

    // Calculate dynamic price & macros
    const unitPrice = PricingEngine.calculateUnitPrice(food, this._selectedVariant, this._selectedCustomizations);
    const totalPrice = PricingEngine.calculateItemTotal(food, this._selectedVariant, this._selectedCustomizations, this._quantity);
    const dynamicNutrition = NutritionEngine.calculateItemNutrition(food, this._selectedVariant, this._selectedCustomizations, this._quantity);

    const isValid = this.validateRequiredSelections();

    // Base Nutrition Grid on Left Panel
    const baseNutritionItems = [
      { label: "Calories", val: food.nutrition.calories ? `${food.nutrition.calories} kcal` : null },
      { label: "Protein", val: food.nutrition.protein ? `${food.nutrition.protein}g` : null },
      { label: "Carbs", val: food.nutrition.carbs ? `${food.nutrition.carbs}g` : null },
      { label: "Fats", val: food.nutrition.fat ? `${food.nutrition.fat}g` : null },
      { label: "Fiber", val: food.nutrition.fiber ? `${food.nutrition.fiber}g` : null },
      { label: "Sugar", val: food.nutrition.sugar ? `${food.nutrition.sugar}g` : null },
      { label: "Caffeine", val: food.nutrition.caffeine ? `${food.nutrition.caffeine}mg` : null }
    ].filter(item => item.val !== null);

    // Variants HTML
    let variantsHtml = "";
    if (food.variants && food.variants.length > 0) {
      variantsHtml = `
        <div class="customization-section">
          <div class="customization-header">
            <div class="group-title-wrapper">
              <h4 class="group-title">Portion / Size</h4>
              <span class="group-badge required">Required</span>
            </div>
            <span class="group-rule-text">Select 1 option</span>
          </div>
          <div class="options-subgrid">
            ${food.variants.map(v => {
              const isSelected = this._selectedVariant && this._selectedVariant.id === v.id;
              const deltaStr = v.price_adjustment > 0 ? `+₹${v.price_adjustment}` : 'Included';
              return `
                <div class="option-card ${isSelected ? 'selected' : ''}" data-variant-id="${v.id}">
                  <div class="option-card-left">
                    <span class="option-card-radio"></span>
                    <span class="option-card-name">${v.name}</span>
                  </div>
                  <span class="option-card-delta">${deltaStr}</span>
                </div>
              `;
            }).join("")}
          </div>
        </div>
      `;
    }

    // Customization Groups HTML
    let customizationsHtml = "";
    if (food.customizations && food.customizations.length > 0) {
      customizationsHtml = food.customizations.map(group => {
        const isRequired = !!group.required;
        const ruleText = group.type === "single" 
          ? "Select 1 option" 
          : `Select up to ${group.max_quantity || 3} options`;

        return `
          <div class="customization-section" data-group-id="${group.id}">
            <div class="customization-header">
              <div class="group-title-wrapper">
                <h4 class="group-title">${group.group_name}</h4>
                <span class="group-badge ${isRequired ? 'required' : 'optional'}">
                  ${isRequired ? 'Required' : 'Optional'}
                </span>
              </div>
              <span class="group-rule-text">${ruleText}</span>
            </div>
            <div class="options-subgrid">
              ${group.options.map(opt => {
                const isSelected = this._selectedCustomizations.some(c => c.id === opt.id);
                const deltaStr = opt.price_adjustment > 0 ? `+₹${opt.price_adjustment}` : 'Free';
                return `
                  <div class="option-card ${isSelected ? 'selected' : ''}" data-group-id="${group.id}" data-option-id="${opt.id}">
                    <div class="option-card-left">
                      <span class="${group.type === 'single' ? 'option-card-radio' : 'option-card-check'}">
                        ${group.type !== 'single' && isSelected ? '<i class="bi bi-check" style="font-size:12px;"></i>' : ''}
                      </span>
                      <span class="option-card-name">${opt.name}</span>
                    </div>
                    <span class="option-card-delta">${deltaStr}</span>
                  </div>
                `;
              }).join("")}
            </div>
          </div>
        `;
      }).join("");
    }

    // Bottom Action Bar Nutrition Strip
    const liveMacros = [];
    if (dynamicNutrition.calories !== null && dynamicNutrition.calories !== undefined) {
      liveMacros.push({ label: "Calories", val: `${dynamicNutrition.calories} kcal`, highlight: false });
    }
    if (dynamicNutrition.protein !== null && dynamicNutrition.protein !== undefined) {
      liveMacros.push({ label: "Protein", val: `${dynamicNutrition.protein}g`, highlight: true });
    }
    if (dynamicNutrition.carbs !== null && dynamicNutrition.carbs !== undefined) {
      liveMacros.push({ label: "Carbs", val: `${dynamicNutrition.carbs}g`, highlight: false });
    }
    if (dynamicNutrition.fat !== null && dynamicNutrition.fat !== undefined) {
      liveMacros.push({ label: "Fat", val: `${dynamicNutrition.fat}g`, highlight: false });
    }

    const liveNutritionHtml = liveMacros.map(m => `
      <div class="live-macro-item">
        <span class="live-macro-label">${m.label}</span>
        <span class="live-macro-val ${m.highlight ? 'highlight' : ''}">${m.val}</span>
      </div>
    `).join("");

    this._modalBackdrop.innerHTML = `
      <div class="food-detail-modal" role="document">
        <button type="button" class="modal-close-btn" id="modal-close-btn" aria-label="Close dialog">
          <i class="bi bi-x"></i>
        </button>

        <div class="modal-content-grid">
          <!-- LEFT PANEL: Light Green Theme -->
          <div class="detail-left-panel">
            <div class="detail-image-wrapper">
              <img 
                src="${food.image}" 
                alt="${food.name}" 
                onerror="this.onerror=null; this.src='${food.fallback_image}';"
              />
            </div>

            <div class="detail-badge-row">
              <span class="food-type-icon ${isVeg ? 'veg' : 'non-veg'}"></span>
              ${(food.dietary_tags || []).map(t => `<span class="tag-badge tag-${t.toLowerCase().replace(/\s+/g, '-')}">${t}</span>`).join("")}
            </div>

            <h2 class="detail-title">${food.name}</h2>
            <p class="detail-description">${food.description}</p>

            ${food.ingredients ? `
              <div class="detail-ingredients">
                <strong>Ingredients:</strong> ${food.ingredients}
              </div>
            ` : ''}

            ${food.allergens ? `
              <div class="detail-allergens">
                <strong>Allergens:</strong> ${food.allergens}
              </div>
            ` : ''}

            <div class="detail-base-nutrition">
              <div class="base-nutrition-title">Base Nutrition Breakdown</div>
              <div class="base-nutrition-grid">
                ${baseNutritionItems.map(item => `
                  <div class="base-nutrition-item">
                    <span class="label">${item.label}</span>
                    <span class="val">${item.val}</span>
                  </div>
                `).join("")}
              </div>
            </div>
          </div>

          <!-- RIGHT PANEL: White, Options & Customizations -->
          <div class="detail-right-panel">
            ${variantsHtml}
            ${customizationsHtml}
          </div>
        </div>

        <!-- STICKY BOTTOM ACTION BAR: Dark #122A16 -->
        <div class="detail-sticky-footer">
          <div class="live-nutrition-strip">
            ${liveNutritionHtml}
          </div>

          <div class="detail-action-controls">
            <div class="modal-qty-control">
              <button type="button" class="modal-qty-btn" id="modal-qty-minus" aria-label="Decrease quantity">
                <i class="bi bi-dash"></i>
              </button>
              <span class="modal-qty-display" id="modal-qty-val">${this._quantity}</span>
              <button type="button" class="modal-qty-btn" id="modal-qty-plus" aria-label="Increase quantity">
                <i class="bi bi-plus"></i>
              </button>
            </div>

            <button 
              type="button" 
              class="btn-add-modal" 
              id="modal-add-to-cart-btn" 
              ${isValid ? '' : 'disabled'}
            >
              <span>Add to Cart</span>
              <span>•</span>
              <span>₹${totalPrice}</span>
            </button>
          </div>
        </div>
      </div>
    `;

    this.attachModalEvents();
  },

  attachModalEvents() {
    const food = this._currentFood;
    if (!food) return;

    // Close Button
    const closeBtn = this._modalBackdrop.querySelector("#modal-close-btn");
    if (closeBtn) closeBtn.addEventListener("click", () => this.close());

    // Quantity Controls
    const qtyMinus = this._modalBackdrop.querySelector("#modal-qty-minus");
    const qtyPlus = this._modalBackdrop.querySelector("#modal-qty-plus");

    if (qtyMinus) {
      qtyMinus.addEventListener("click", () => {
        if (this._quantity > 1) {
          this._quantity -= 1;
          this.render();
        }
      });
    }

    if (qtyPlus) {
      qtyPlus.addEventListener("click", () => {
        this._quantity += 1;
        this.render();
      });
    }

    // Variant Selection
    this._modalBackdrop.querySelectorAll(".option-card[data-variant-id]").forEach(card => {
      card.addEventListener("click", () => {
        const variantId = parseInt(card.dataset.variantId, 10);
        const variant = (food.variants || []).find(v => v.id === variantId);
        if (variant) {
          this._selectedVariant = variant;
          this.render();
        }
      });
    });

    // Customization Selection
    this._modalBackdrop.querySelectorAll(".option-card[data-option-id]").forEach(card => {
      card.addEventListener("click", () => {
        const groupId = parseInt(card.dataset.groupId, 10);
        const optionId = parseInt(card.dataset.optionId, 10);
        const group = (food.customizations || []).find(g => g.id === groupId);
        if (!group) return;

        const option = group.options.find(o => o.id === optionId);
        if (!option) return;

        if (group.type === "single") {
          // Remove previous selection from this group
          this._selectedCustomizations = this._selectedCustomizations.filter(c => c.group_id !== groupId);
          // Add newly selected option
          this._selectedCustomizations.push({
            id: option.id,
            group_id: groupId,
            name: option.name,
            price_adjustment: option.price_adjustment || 0,
            nutrition_adjustment: option.nutrition_adjustment || {},
            quantity: 1
          });
        } else {
          // Multi group (checkbox)
          const existingIndex = this._selectedCustomizations.findIndex(c => c.id === optionId);
          if (existingIndex > -1) {
            // Deselect
            this._selectedCustomizations.splice(existingIndex, 1);
          } else {
            // Check max limit
            const currentCount = this._selectedCustomizations.filter(c => c.group_id === groupId).length;
            if (group.max_quantity && currentCount >= group.max_quantity) {
              ToastComponent.show(`Maximum ${group.max_quantity} options allowed for ${group.group_name}`, "warning", "bi-exclamation-triangle");
              return;
            }
            this._selectedCustomizations.push({
              id: option.id,
              group_id: groupId,
              name: option.name,
              price_adjustment: option.price_adjustment || 0,
              nutrition_adjustment: option.nutrition_adjustment || {},
              quantity: 1
            });
          }
        }

        this.render();
      });
    });

    // Add To Cart CTA
    const addBtn = this._modalBackdrop.querySelector("#modal-add-to-cart-btn");
    if (addBtn) {
      addBtn.addEventListener("click", () => {
        if (!this.validateRequiredSelections()) {
          ToastComponent.show("Please complete all required selections", "warning", "bi-exclamation-circle");
          return;
        }

        AppState.addToCart(
          this._currentFood,
          this._selectedVariant,
          [...this._selectedCustomizations],
          this._quantity
        );

        ToastComponent.show(`Added ${this._quantity}x ${this._currentFood.name} to cart!`, "success", "bi-bag-check-fill");
        this.close();
      });
    }
  }
};
