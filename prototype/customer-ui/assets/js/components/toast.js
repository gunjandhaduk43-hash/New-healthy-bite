/**
 * Healthy Bite — Toast Notification Component
 */

const ToastComponent = {
  _container: null,

  init() {
    if (!this._container) {
      this._container = document.createElement("div");
      this._container.className = "toast-container";
      this._container.setAttribute("aria-live", "polite");
      document.body.appendChild(this._container);
    }
  },

  show(message, type = "success", icon = "bi-check-circle-fill") {
    this.init();

    const toast = document.createElement("div");
    toast.className = "toast";
    toast.innerHTML = `
      <i class="bi ${icon} toast-icon"></i>
      <span>${message}</span>
    `;

    this._container.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
      toast.classList.add("show");
    });

    // Auto dismiss
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 2800);
  }
};
