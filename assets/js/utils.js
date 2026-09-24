/**
 * HEALTHY BITE — Frontend Utilities & Toast Notification Engine
 */

const Utils = {
    formatCurrency(amount) {
        const val = parseFloat(amount) || 0;
        return '₹' + Math.round(val);
    },

    formatCurrencyDecimal(amount) {
        const val = parseFloat(amount) || 0;
        return '₹' + val.toFixed(2);
    },

    formatNutrition(val, unit = 'g') {
        if (val === null || val === undefined) {
            return null;
        }
        const num = parseFloat(val);
        return (Number.isInteger(num) ? num.toString() : num.toFixed(1)) + unit;
    },

    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    debounce(func, wait = 250) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    },

    showToast(message, type = 'success', iconClass = null) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast-item ${type}`;

        let icon = '';
        if (iconClass) {
            icon = `<i class="bi ${iconClass}"></i>`;
        } else if (type === 'success') {
            icon = '<i class="bi bi-check-circle-fill" style="color: #4ade80;"></i>';
        } else if (type === 'error') {
            icon = '<i class="bi bi-exclamation-circle-fill" style="color: #f87171;"></i>';
        } else {
            icon = '<i class="bi bi-info-circle-fill" style="color: #60a5fa;"></i>';
        }

        toast.innerHTML = `${icon} <span>${Utils.escapeHtml(message)}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(12px)';
            setTimeout(() => toast.remove(), 250);
        }, 3200);
    }
};

window.Utils = Utils;
window.showToast = function(message, type = 'success', iconClass = null) {
    Utils.showToast(message, type, iconClass);
};
