/**
 * Healthy Bite — Nutrition Summary Component
 * Displays nutrition breakdowns with proper units and zero-hiding
 */

const NutritionSummaryComponent = {
  render(nutritionData) {
    if (!nutritionData) return "";

    const displayMacros = NutritionEngine.getDisplayableMacros(nutritionData);

    return `
      <div class="nutrition-summary-card">
        <h4 style="font-size: var(--font-size-sm); font-weight: var(--font-weight-bold); margin-bottom: 8px; color: var(--color-dark-green);">
          Nutritional Analysis
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 8px;">
          ${displayMacros.map(m => `
            <div style="background: var(--color-background); border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 6px 10px;">
              <div style="font-size: 10px; color: var(--color-secondary-text); text-transform: uppercase;">${m.label}</div>
              <div style="font-size: var(--font-size-sm); font-weight: var(--font-weight-bold); color: var(--color-main-text);">${m.formatted}</div>
            </div>
          `).join("")}
        </div>
      </div>
    `;
  }
};
