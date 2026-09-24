/**
 * Healthy Bite — Filters Controller
 */

const FiltersController = {
  init() {
    FilterChipComponent.render("quick-filters-container", (filterId) => {
      // Handled reactively via AppState
    });

    CategoryChipComponent.render("category-nav-container", (catId) => {
      // Handled reactively via AppState
    });
  }
};
