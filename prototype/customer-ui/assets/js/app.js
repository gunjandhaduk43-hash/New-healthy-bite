/**
 * Healthy Bite — Master Customer App Bootstrapper
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Header
  RestaurantHeaderComponent.render("site-header-container");

  // Initialize Search & Filter Controls if present on page
  if (document.getElementById("search-bar-container")) {
    SearchController.init();
  }

  if (document.getElementById("quick-filters-container")) {
    FiltersController.init();
  }

  // Initialize Main Menu Controller if menu container exists
  if (document.getElementById("menu-sections-container")) {
    MenuController.init();
  }

  // Initialize Cart Previews
  if (document.getElementById("sidebar-cart-container")) {
    CartPreviewComponent.renderSidebar();
  }
  if (document.getElementById("mobile-floating-cart-container")) {
    CartPreviewComponent.renderMobileFloating();
  }
  CartPreviewComponent.initListeners();

  // Initialize Toast Engine
  ToastComponent.init();
});
