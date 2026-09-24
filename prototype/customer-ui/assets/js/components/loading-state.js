/**
 * Healthy Bite — Loading Skeleton / State Component
 */

const LoadingStateComponent = {
  render() {
    const wrapper = document.createElement("div");
    wrapper.className = "loading-skeleton-container";
    wrapper.innerHTML = `
      <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 40px; color: var(--color-secondary-text);">
        <div class="spinner-border text-success" role="status" style="width: 2.5rem; height: 2.5rem; margin-bottom: 12px;"></div>
        <p style="font-size: var(--font-size-sm);">Loading fresh menu items...</p>
      </div>
    `;
    return wrapper;
  }
};
