/**
 * Healthy Bite — Application State Management
 * Reactive observable store with localStorage persistence for cart items.
 */

const AppState = {
  _state: {
    cart: [],
    activeCategoryId: null, // null means all or first category
    activeFilterId: "all",
    searchQuery: "",
    favorites: [],
    currentOrder: null
  },

  _listeners: [],

  init() {
    // Load cart from localStorage
    try {
      const savedCart = localStorage.getItem("hb_prototype_cart");
      if (savedCart) {
        this._state.cart = JSON.parse(savedCart);
      }
    } catch (e) {
      console.warn("Could not load cart from storage:", e);
    }

    // Load favorites from localStorage
    try {
      const savedFavs = localStorage.getItem("hb_prototype_favorites");
      if (savedFavs) {
        this._state.favorites = JSON.parse(savedFavs);
      }
    } catch (e) {
      console.warn("Could not load favorites:", e);
    }
  },

  subscribe(listener) {
    if (typeof listener === "function") {
      this._listeners.push(listener);
    }
    return () => {
      this._listeners = this._listeners.filter(l => l !== listener);
    };
  },

  _notify(changedKey) {
    this._listeners.forEach(fn => fn(this._state, changedKey));
  },

  _persistCart() {
    try {
      localStorage.setItem("hb_prototype_cart", JSON.stringify(this._state.cart));
    } catch (e) {
      console.warn("Could not persist cart:", e);
    }
  },

  _persistFavs() {
    try {
      localStorage.setItem("hb_prototype_favorites", JSON.stringify(this._state.favorites));
    } catch (e) {
      console.warn("Could not persist favorites:", e);
    }
  },

  // Getters
  getCart() {
    return this._state.cart;
  },

  getActiveCategory() {
    return this._state.activeCategoryId;
  },

  getActiveFilter() {
    return this._state.activeFilterId;
  },

  getSearchQuery() {
    return this._state.searchQuery;
  },

  isFavorite(foodId) {
    return this._state.favorites.includes(foodId);
  },

  toggleFavorite(foodId) {
    if (this.isFavorite(foodId)) {
      this._state.favorites = this._state.favorites.filter(id => id !== foodId);
    } else {
      this._state.favorites.push(foodId);
    }
    this._persistFavs();
    this._notify("favorites");
  },

  setCategory(catId) {
    this._state.activeCategoryId = catId;
    this._notify("activeCategoryId");
  },

  setFilter(filterId) {
    this._state.activeFilterId = filterId;
    this._notify("activeFilterId");
  },

  setSearchQuery(query) {
    this._state.searchQuery = (query || "").trim();
    this._notify("searchQuery");
  },

  /**
   * Generates a deterministic signature for a configured cart item
   * so distinct configurations are kept distinct as requested.
   */
  _generateItemSignature(foodItem, selectedVariant, selectedCustomizations = []) {
    const foodId = foodItem.id;
    const variantId = selectedVariant ? selectedVariant.id : "default";
    const customIds = selectedCustomizations
      .map(c => `${c.id}:${c.quantity || 1}`)
      .sort()
      .join("|");
    return `${foodId}-${variantId}-${customIds}`;
  },

  /**
   * Adds configured item to cart. If exact signature exists, increments quantity.
   */
  addToCart(foodItem, selectedVariant, selectedCustomizations = [], quantity = 1) {
    const signature = this._generateItemSignature(foodItem, selectedVariant, selectedCustomizations);
    const existingIndex = this._state.cart.findIndex(item => item.signature === signature);

    if (existingIndex > -1) {
      this._state.cart[existingIndex].quantity += quantity;
    } else {
      const cartItemId = "ci_" + Date.now() + "_" + Math.random().toString(36).substr(2, 4);
      this._state.cart.push({
        id: cartItemId,
        signature,
        food_item: foodItem,
        selected_variant: selectedVariant,
        selected_customizations: selectedCustomizations,
        quantity: Math.max(1, quantity)
      });
    }

    this._persistCart();
    this._notify("cart");
  },

  /**
   * Updates quantity of a cart item. If quantity <= 0, removes the item.
   */
  updateCartQuantity(cartItemId, delta) {
    const item = this._state.cart.find(ci => ci.id === cartItemId);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
      this._state.cart = this._state.cart.filter(ci => ci.id !== cartItemId);
    }

    this._persistCart();
    this._notify("cart");
  },

  removeCartItem(cartItemId) {
    this._state.cart = this._state.cart.filter(ci => ci.id !== cartItemId);
    this._persistCart();
    this._notify("cart");
  },

  clearCart() {
    this._state.cart = [];
    this._persistCart();
    this._notify("cart");
  },

  setCurrentOrder(orderData) {
    this._state.currentOrder = orderData;
    try {
      localStorage.setItem("hb_prototype_current_order", JSON.stringify(orderData));
    } catch (e) {}
    this._notify("currentOrder");
  },

  getCurrentOrder() {
    if (!this._state.currentOrder) {
      try {
        const saved = localStorage.getItem("hb_prototype_current_order");
        if (saved) this._state.currentOrder = JSON.parse(saved);
      } catch (e) {}
    }
    return this._state.currentOrder;
  }
};

// Initialize state
AppState.init();
