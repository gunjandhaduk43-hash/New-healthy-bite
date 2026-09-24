/**
 * Healthy Bite — Food Details Controller
 * Supports standalone food details page (food-details.html?id=101) as well as modal interactions
 */

const FoodDetailsPageController = {
  init() {
    const params = new URLSearchParams(window.location.search);
    const foodId = parseInt(params.get("id"), 10) || 101;
    const food = HEALTHY_BITE_DATA.food_items.find(f => f.id === foodId) || HEALTHY_BITE_DATA.food_items[0];

    const container = document.getElementById("standalone-food-detail-container");
    if (!container) return;

    FoodDetailModalComponent.open(food);
  }
};
