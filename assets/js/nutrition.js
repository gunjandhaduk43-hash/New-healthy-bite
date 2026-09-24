/**
 * HEALTHY BITE — Client-side Dynamic Nutrition Calculations
 * (Strict NULL representation - never converts NULL to 0)
 */

const Nutrition = {
    fields: ['calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium', 'caffeine'],

    calculateItemNutrition(baseFood, selectedVariant = null, selectedCustomizations = []) {
        const result = {};

        this.fields.forEach(field => {
            const adjKey = field + '_adjustment';
            let val = baseFood[field] !== null && baseFood[field] !== undefined ? parseFloat(baseFood[field]) : null;

            if (selectedVariant && selectedVariant[adjKey] !== null && selectedVariant[adjKey] !== undefined) {
                val = (val === null ? 0 : val) + parseFloat(selectedVariant[adjKey]);
            }

            selectedCustomizations.forEach(c => {
                if (c[adjKey] !== null && c[adjKey] !== undefined) {
                    const qty = parseInt(c.quantity) || 1;
                    val = (val === null ? 0 : val) + (parseFloat(c[adjKey]) * qty);
                }
            });

            if (val !== null) {
                result[field] = field === 'calories' ? Math.round(val) : Math.round(val * 10) / 10;
            } else {
                result[field] = null;
            }
        });

        return result;
    },

    scaleForQuantity(nutrition, quantity = 1) {
        const scaled = {};
        const qty = Math.max(1, parseInt(quantity) || 1);

        this.fields.forEach(field => {
            if (nutrition[field] !== null && nutrition[field] !== undefined) {
                const val = nutrition[field] * qty;
                scaled[field] = field === 'calories' ? Math.round(val) : Math.round(val * 10) / 10;
            } else {
                scaled[field] = null;
            }
        });

        return scaled;
    }
};

window.Nutrition = Nutrition;
