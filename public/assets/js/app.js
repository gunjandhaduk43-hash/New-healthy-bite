/**
 * HEALTHY BITE — Main Application Bootstrapper
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Cart
    if (window.Cart) {
        Cart.init();
    }

    // 2. Initialize Menu & Food Details Modal if on Menu Page
    if (window.Menu) {
        Menu.init();
    }
    if (window.FoodDetails) {
        FoodDetails.init();
    }

    // 3. Initialize Checkout if on Checkout Page
    if (window.Checkout) {
        Checkout.init();
    }

    console.log('[HealthyBite] Frontend runtime initialized.');
});
