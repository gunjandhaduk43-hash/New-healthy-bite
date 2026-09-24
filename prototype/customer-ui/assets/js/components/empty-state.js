/**
 * Healthy Bite — Empty State Component
 */

const EmptyStateComponent = {
  render({
    icon = "bi-search",
    title = "No food found",
    description = "We couldn't find any dishes matching your criteria. Try adjusting your filters or search terms.",
    actionText = "Reset Filters",
    onAction = null
  }) {
    const wrapper = document.createElement("div");
    wrapper.className = "empty-state";
    wrapper.innerHTML = `
      <div class="empty-state-icon">
        <i class="bi ${icon}"></i>
      </div>
      <h3 class="empty-state-title">${title}</h3>
      <p class="empty-state-desc">${description}</p>
      ${actionText ? `<button type="button" class="btn btn-secondary btn-sm empty-state-btn">${actionText}</button>` : ''}
    `;

    if (actionText && typeof onAction === "function") {
      const btn = wrapper.querySelector(".empty-state-btn");
      if (btn) {
        btn.addEventListener("click", onAction);
      }
    }

    return wrapper;
  }
};
