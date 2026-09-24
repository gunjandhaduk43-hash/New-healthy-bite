/**
 * Healthy Bite — Quick Filter Chips Component
 * Horizontal scrollable bar with active/inactive states
 */

const FilterChipComponent = {
  render(containerId = "quick-filters-container", onFilterChange = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const filters = HEALTHY_BITE_DATA.quick_filters;
    const currentFilter = AppState.getActiveFilter();

    container.innerHTML = `
      <div class="filters-scroll-bar" role="tablist" aria-label="Quick Filters">
        ${filters.map(filter => `
          <button 
            type="button" 
            class="filter-chip ${filter.id === currentFilter ? 'active' : ''}" 
            data-filter-id="${filter.id}"
            role="tab"
            aria-selected="${filter.id === currentFilter ? 'true' : 'false'}"
          >
            <i class="bi ${filter.icon}"></i>
            <span>${filter.label}</span>
          </button>
        `).join("")}
      </div>
    `;

    container.querySelectorAll(".filter-chip").forEach(chip => {
      chip.addEventListener("click", () => {
        const filterId = chip.dataset.filterId;
        AppState.setFilter(filterId);

        // Update active class immediately
        container.querySelectorAll(".filter-chip").forEach(c => {
          c.classList.remove("active");
          c.setAttribute("aria-selected", "false");
        });
        chip.classList.add("active");
        chip.setAttribute("aria-selected", "true");

        if (typeof onFilterChange === "function") {
          onFilterChange(filterId);
        }
      });
    });
  }
};
