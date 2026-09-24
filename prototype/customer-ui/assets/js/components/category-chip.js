/**
 * Healthy Bite — Category Navigation Component
 * Tabs for restaurant-specific categories
 */

const CategoryChipComponent = {
  render(containerId = "category-nav-container", onCategoryChange = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const categories = HEALTHY_BITE_DATA.categories;
    const currentCategoryId = AppState.getActiveCategory();

    container.innerHTML = `
      <nav class="categories-nav" role="tablist" aria-label="Restaurant Categories">
        <button 
          type="button" 
          class="category-tab ${currentCategoryId === null ? 'active' : ''}" 
          data-category-id="all"
          role="tab"
          aria-selected="${currentCategoryId === null ? 'true' : 'false'}"
        >
          All Categories
        </button>
        ${categories.map(cat => `
          <button 
            type="button" 
            class="category-tab ${currentCategoryId === cat.id ? 'active' : ''}" 
            data-category-id="${cat.id}"
            role="tab"
            aria-selected="${currentCategoryId === cat.id ? 'true' : 'false'}"
          >
            ${cat.name}
          </button>
        `).join("")}
      </nav>
    `;

    container.querySelectorAll(".category-tab").forEach(tab => {
      tab.addEventListener("click", () => {
        const catIdStr = tab.dataset.categoryId;
        const catId = catIdStr === "all" ? null : parseInt(catIdStr, 10);
        AppState.setCategory(catId);

        container.querySelectorAll(".category-tab").forEach(t => {
          t.classList.remove("active");
          t.setAttribute("aria-selected", "false");
        });
        tab.classList.add("active");
        tab.setAttribute("aria-selected", "true");

        if (typeof onCategoryChange === "function") {
          onCategoryChange(catId);
        }
      });
    });
  }
};
