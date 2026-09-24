/**
 * HEALTHY BITE — Client-side Price Calculations
 * (Mirrors backend PricingService)
 */

const Pricing = {
    calculateItemPrice(basePrice, selectedVariant = null, selectedCustomizations = []) {
        let price = parseFloat(basePrice) || 0;

        if (selectedVariant && selectedVariant.price_adjustment) {
            price += parseFloat(selectedVariant.price_adjustment) || 0;
        }

        selectedCustomizations.forEach(c => {
            const adj = parseFloat(c.price_adjustment) || 0;
            const qty = parseInt(c.quantity) || 1;
            price += (adj * qty);
        });

        return Math.round(price * 100) / 100;
    },

    calculateLineTotal(unitPrice, quantity = 1) {
        const qty = Math.max(1, parseInt(quantity) || 1);
        return Math.round(unitPrice * qty * 100) / 100;
    },

    calculateCartTotals(cartItems, taxRate = 0.05, serviceCharge = 0.00) {
        let subtotal = 0;
        cartItems.forEach(item => {
            subtotal += parseFloat(item.line_total) || 0;
        });

        subtotal = Math.round(subtotal * 100) / 100;
        const tax = Math.round(subtotal * taxRate * 100) / 100;
        const service = Math.round(serviceCharge * 100) / 100;
        const total = Math.round((subtotal + tax + service) * 100) / 100;

        return {
            subtotal,
            tax,
            service_charge: service,
            total_amount: total
        };
    }
};

window.Pricing = Pricing;
