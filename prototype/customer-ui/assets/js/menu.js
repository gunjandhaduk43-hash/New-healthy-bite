/**
 * Healthy Bite — Menu Controller
 * Orchestrates category filtering, search queries, quick filters, and food card grid rendering.
 */

const MenuController = {
  containerId: "menu-sections-container",

  init() {
    this.render();

    // Subscribe to state changes (category, filter, search)
    AppState.subscribe((state, changedKey) => {
      if (["activeCategoryId", "activeFilterId", "searchQuery"].includes(changedKey)) {
        this.render();
      }
    });
  },

  /**
   * Filters the food list according to category, search, and quick filters.
   */
  getFilteredItems() {
    let items = HEALTHY_BITE_DATA.food_items;
    const catId = AppState.getActiveCategory();
    const filterId = AppState.getActiveFilter();
    const query = AppState.getSearchQuery().toLowerCase();

    // 1. Category Filter
    if (catId !== null) {
      items = items.filter(f => f.category_id === catId);
    }

    // 2. Quick Filter
    if (filterId === "high-protein") {
      items = items.filter(f => f.nutrition && f.nutrition.protein >= 20);
    } else if (filterId === "vegetarian") {
      items = items.filter(f => f.food_type === "vegetarian");
    } else if (filterId === "under-600-kcal") {
      items = items.filter(f => f.nutrition && f.nutrition.calories !== null && f.nutrition.calories < 600);
    } else if (filterId === "low-sugar") {
      items = items.filter(f => f.nutrition && f.nutrition.sugar !== null && f.nutrition.sugar <= 6);
    }

    // 3. Search Query Filter
    if (query) {
      items = items.filter(f => {
        const nameMatch = f.name.toLowerCase().includes(query);
        const descMatch = (f.description || "").toLowerCase().includes(query);
        const ingMatch = (f.ingredients || "").toLowerCase().includes(query);
        const catMatch = (f.category_name || "").toLowerCase().includes(query);
        const tagsMatch = (f.dietary_tags || []).some(t => t.toLowerCase().includes(query));
        return nameMatch || descMatch || ingMatch || catMatch || tagsMatch;
      });
    }

    return items;
  },

  render() {
    const container = document.getElementById(this.containerId);
    if (!container) return;

    const items = this.getFilteredItems();

    // Zero items state
    if (items.length === 0) {
      container.innerHTML = "";
      const emptyState = EmptyStateComponent.render({
        icon: "bi-search",
        title: "No food found",
        description: "We couldn't find any dishes matching your search or filters. Try clearing filters to see all delicious options.",
        actionText: "Reset All Filters",
        onAction: () => {
          AppState.setCategory(null);
          AppState.setFilter("all");
          AppState.setSearchQuery("");
          const searchInput = document.getElementById("global-search-input");
          if (searchInput) searchInput.value = "";
          // Re-render components
          CategoryChipComponent.render();
          FilterChipComponent.render();
        }
      });
      container.appendChild(emptyState);
      return;
    }

    const activeCatId = AppState.getActiveCategory();

    // If a specific category is selected, render single section
    if (activeCatId !== null) {
      const currentCat = HEALTHY_BITE_DATA.categories.find(c => c.id === activeCatId);
      const catName = currentCat ? currentCat.name : "Menu Items";

      container.innerHTML = `
        <section class="menu-section">
          <div class="section-header">
            <h2 class="section-title">${catName}</h2>
            <span class="section-count">${items.length} ${items.length === 1 ? 'item' : 'items'}</span>
          </div>
          <div class="food-grid">
            ${items.map(f => FoodCardComponent.render(f)).join("")}
          </div>
        </section>
      `;
    } else {
      // Group by categories
      const categories = HEALTHY_BITE_DATA.categories;
      let sectionsHtml = "";

      categories.forEach(cat => {
        const catItems = items.filter(f => f.category_id === cat.id);
        if (catItems.length > 0) {
          sectionsHtml += `
            <section class="menu-section">
              <div class="section-header">
                <h2 class="section-title">${cat.name}</h2>
                <span class="section-count">${catItems.length} ${catItems.length === 1 ? 'item' : 'items'}</span>
              </div>
              <div class="food-grid">
                ${catItems.map(f => FoodCardComponent.render(f)).join("")}
              </div>
            </section>
          `;
        }
      });

      container.innerHTML = sectionsHtml || `
        <div class="food-grid">
          ${items.map(f => FoodCardComponent.render(f)).join("")}
        </div>
      `;
    }

    // Attach card event listeners (click, fav, add)
    FoodCardComponent.attachCardEvents(container);
  }
};
