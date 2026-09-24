/**
 * Healthy Bite — Food Card Component
 * Desktop vertical 3-column & Mobile horizontal card
 */

const FoodCardComponent = {
  render(foodItem) {
    if (!foodItem) return "";

    const isFav = AppState.isFavorite(foodItem.id);
    const isVeg = foodItem.food_type === "vegetarian";

    // Macro display: show calories and protein if available
    const macros = [];
    if (foodItem.nutrition) {
      if (foodItem.nutrition.calories) {
        macros.push({ label: `${foodItem.nutrition.calories} kcal`, highlight: false });
      }
      if (foodItem.nutrition.protein) {
        macros.push({ label: `${foodItem.nutrition.protein}g protein`, highlight: true });
      }
      if (foodItem.nutrition.carbs && macros.length < 3) {
        macros.push({ label: `${foodItem.nutrition.carbs}g carbs`, highlight: false });
      }
    }

    const tagsHtml = (foodItem.dietary_tags || []).slice(0, 2).map(tag => {
      const slug = tag.toLowerCase().replace(/\s+/g, '-');
      return `<span class="tag-badge tag-${slug}">${tag}</span>`;
    }).join("");

    const macrosHtml = macros.map(m => `
      <span class="macro-chip ${m.highlight ? 'macro-highlight' : ''}">
        ${m.label}
      </span>
    `).join("");

    return `
      <article class="food-card" data-food-id="${foodItem.id}" tabindex="0" role="button" aria-label="View details for ${foodItem.name}">
        <div class="food-card-media">
          <img 
            src="${foodItem.image}" 
            alt="${foodItem.name}" 
            class="food-card-img" 
            loading="lazy"
            onerror="this.onerror=null; this.src='${foodItem.fallback_image}';"
          />
          <div class="card-top-badges">
            <span class="food-type-icon ${isVeg ? 'veg' : 'non-veg'}" title="${isVeg ? 'Vegetarian' : 'Non-Vegetarian'}"></span>
            ${tagsHtml}
          </div>
          <button 
            type="button" 
            class="food-card-fav-btn ${isFav ? 'active' : ''}" 
            data-fav-id="${foodItem.id}"
            title="${isFav ? 'Remove from favorites' : 'Add to favorites'}"
            aria-label="${isFav ? 'Remove from favorites' : 'Add to favorites'}"
          >
            <i class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i>
          </button>
        </div>

        <div class="food-card-body">
          <div class="food-card-header">
            <div>
              <h3 class="food-card-name">${foodItem.name}</h3>
            </div>
            <span class="food-type-icon ${isVeg ? 'veg' : 'non-veg'}" title="${isVeg ? 'Vegetarian' : 'Non-Vegetarian'}" style="display: none; /* Shown on mobile */"></span>
          </div>

          <p class="food-card-desc">${foodItem.description}</p>

          ${macrosHtml ? `<div class="food-card-nutrition">${macrosHtml}</div>` : ''}

          <div class="food-card-footer">
            <div class="food-card-price">
              <span class="currency">₹</span><span>${foodItem.base_price}</span>
            </div>
            <div class="food-card-action">
              <button type="button" class="btn btn-add btn-trigger-modal" data-food-id="${foodItem.id}">
                Add <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
        </div>
      </article>
    `;
  },

  /**
   * Attaches event handlers for card clicks, favourite toggle, and add modal trigger
   */
  attachCardEvents(container) {
    if (!container) return;

    // Favorite click
    container.querySelectorAll(".food-card-fav-btn").forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const id = parseInt(btn.dataset.favId, 10);
        AppState.toggleFavorite(id);
        const active = AppState.isFavorite(id);
        btn.classList.toggle("active", active);
        const icon = btn.querySelector("i");
        if (icon) {
          icon.className = `bi ${active ? 'bi-heart-fill' : 'bi-heart'}`;
        }
        ToastComponent.show(active ? "Added to favorites" : "Removed from favorites", "info", "bi-heart");
      });
    });

    // Food card click or Add button click opens detail modal
    container.querySelectorAll(".food-card").forEach(card => {
      card.addEventListener("click", (e) => {
        // Prevent trigger if clicking on fav button
        if (e.target.closest(".food-card-fav-btn")) return;
        const id = parseInt(card.dataset.foodId, 10);
        const item = HEALTHY_BITE_DATA.food_items.find(f => f.id === id);
        if (item) {
          FoodDetailModalComponent.open(item);
        }
      });

      card.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          const id = parseInt(card.dataset.foodId, 10);
          const item = HEALTHY_BITE_DATA.food_items.find(f => f.id === id);
          if (item) {
            FoodDetailModalComponent.open(item);
          }
        }
      });
    });
  }
};
