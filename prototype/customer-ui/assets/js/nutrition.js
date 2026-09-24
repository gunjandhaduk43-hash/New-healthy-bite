/**
 * Healthy Bite — Nutrition Engine
 * Calculates dynamic macros while strictly preserving null/undefined values.
 * NEVER displays 0g or fake zeros for missing nutrition data!
 */

const NutritionEngine = {
  // Recognized nutrition fields and their standard unit of measurement
  FIELDS: [
    { key: 'calories', label: 'Calories', unit: 'kcal', precision: 0, isMacro: true },
    { key: 'protein', label: 'Protein', unit: 'g', precision: 0, isMacro: true },
    { key: 'carbs', label: 'Carbs', unit: 'g', precision: 0, isMacro: true },
    { key: 'fat', label: 'Fat', unit: 'g', precision: 0, isMacro: true },
    { key: 'fiber', label: 'Fiber', unit: 'g', precision: 0, isMacro: false },
    { key: 'sugar', label: 'Sugar', unit: 'g', precision: 0, isMacro: false },
    { key: 'sodium', label: 'Sodium', unit: 'mg', precision: 0, isMacro: false },
    { key: 'caffeine', label: 'Caffeine', unit: 'mg', precision: 0, isMacro: false }
  ],

  /**
   * Recalculates nutrition for an item configuration and quantity.
   * If a base macro is null or undefined, the result MUST remain null!
   */
  calculateItemNutrition(foodItem, selectedVariant, selectedCustomizations = [], quantity = 1) {
    if (!foodItem || !foodItem.nutrition) return {};

    const qty = Math.max(1, parseInt(quantity, 10) || 1);
    const result = {};

    this.FIELDS.forEach(({ key }) => {
      const baseVal = foodItem.nutrition[key];
      
      // If base value does not exist (null or undefined), it stays null!
      if (baseVal === null || baseVal === undefined) {
        result[key] = null;
        return;
      }

      let totalVal = Number(baseVal);

      // Add variant adjustment if available
      if (selectedVariant && selectedVariant.nutrition_adjustment) {
        const variantVal = selectedVariant.nutrition_adjustment[key];
        if (typeof variantVal === 'number') {
          totalVal += variantVal;
        }
      }

      // Add customization adjustments
      if (Array.isArray(selectedCustomizations)) {
        selectedCustomizations.forEach(item => {
          if (item && item.nutrition_adjustment) {
            const customVal = item.nutrition_adjustment[key];
            if (typeof customVal === 'number') {
              const itemQty = Number(item.quantity) || 1;
              totalVal += customVal * itemQty;
            }
          }
        });
      }

      // Multiply by total item quantity
      result[key] = Math.max(0, Math.round(totalVal * qty));
    });

    return result;
  },

  /**
   * Formats a single nutrition value with its appropriate unit.
   * Returns empty string if value is null or undefined.
   */
  formatMacro(key, value) {
    if (value === null || value === undefined) return null;
    const field = this.FIELDS.find(f => f.key === key);
    if (!field) return `${value}`;
    return `${value}${field.unit ? ' ' + field.unit : ''}`;
  },

  /**
   * Generates display pills for existing nutrition fields.
   * Skips null/undefined fields entirely.
   */
  getDisplayableMacros(nutritionObj) {
    if (!nutritionObj) return [];
    
    return this.FIELDS
      .filter(({ key }) => nutritionObj[key] !== null && nutritionObj[key] !== undefined)
      .map(({ key, label, unit, isMacro }) => ({
        key,
        label,
        value: nutritionObj[key],
        formatted: `${nutritionObj[key]}${unit === 'kcal' ? ' kcal' : unit}`,
        isMacro
      }));
  }
};
