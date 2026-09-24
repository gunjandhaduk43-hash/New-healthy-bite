/**
 * HEALTHY BITE — Centralized Reactive State Store
 */

window.State = window.State || {};
const State = window.State;

// Provide default values if not pre-populated by PHP
State.restaurant = State.restaurant || { id: 1, name: 'Healthy Bite' };
State.branch = State.branch || { id: 1, name: 'Indiranagar' };
State.table = State.table || { id: 4, table_number: '12' };
State.qrToken = State.qrToken || '';
State.categories = State.categories || [];
State.foods = State.foods || [];

// Filters & Search
State.selectedCategory = State.selectedCategory || 'all';
State.activeFoodType = State.activeFoodType || 'all';
State.searchTerm = State.searchTerm || '';

// Food Details Modal Selection
State.selectedFood = null;
State.selectedVariant = null;
State.selectedCustomizations = [];
State.modalQuantity = 1;

// Cart
State.cart = State.cart || [];

// Listeners for reactivity
State.listeners = State.listeners || {};

State.subscribe = function(event, callback) {
    if (!this.listeners[event]) {
        this.listeners[event] = [];
    }
    this.listeners[event].push(callback);
};

State.notify = function(event, data) {
    if (this.listeners[event]) {
        this.listeners[event].forEach(cb => {
            try {
                cb(data);
            } catch (err) {
                console.error('[State] Error in event listener for ' + event, err);
            }
        });
    }
};

// Cart persistence with LocalStorage
State.loadCartFromStorage = function() {
    try {
        const raw = localStorage.getItem('hb_cart');
        if (raw) {
            this.cart = JSON.parse(raw);
            if (!Array.isArray(this.cart)) {
                this.cart = [];
            }
            this.notify('cartUpdated', this.cart);
        }
    } catch (e) {
        console.warn('Failed to load cart from localStorage', e);
        this.cart = [];
    }
};

State.saveCartToStorage = function() {
    try {
        localStorage.setItem('hb_cart', JSON.stringify(this.cart || []));
        this.notify('cartUpdated', this.cart);
    } catch (e) {
        console.warn('Failed to save cart to localStorage', e);
    }
};
