/**
 * HEALTHY BITE — Live Order Tracking & Review Controller
 */

const OrderTracker = {
    orderNumber: '',
    pollInterval: null,
    selectedRating: 5,

    init(orderNumber) {
        this.orderNumber = orderNumber;
        this.bindEvents();
        this.startPolling();
    },

    bindEvents() {
        // Star rating clicks
        const stars = document.querySelectorAll('#starRatingContainer .star-item');
        const label = document.getElementById('starRatingLabel');
        const ratingLabels = {
            1: '1.0 Poor',
            2: '2.0 Fair',
            3: '3.0 Good',
            4: '4.0 Very Good',
            5: '5.0 Excellent'
        };

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const val = parseInt(star.getAttribute('data-val'), 10);
                this.selectedRating = val;
                this.renderStars(val);
                if (label) label.textContent = ratingLabels[val] || `${val}.0`;
            });

            star.addEventListener('mouseenter', () => {
                const val = parseInt(star.getAttribute('data-val'), 10);
                this.renderStars(val);
            });
        });

        const starContainer = document.getElementById('starRatingContainer');
        if (starContainer) {
            starContainer.addEventListener('mouseleave', () => {
                this.renderStars(this.selectedRating);
            });
        }

        // Submit review button
        const submitBtn = document.getElementById('btnSubmitReview');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => this.submitReview());
        }
    },

    renderStars(val) {
        const stars = document.querySelectorAll('#starRatingContainer .star-item');
        stars.forEach(s => {
            const sVal = parseInt(s.getAttribute('data-val'), 10);
            if (sVal <= val) {
                s.className = 'bi bi-star-fill star-item active';
                s.style.color = '#f59e0b';
            } else {
                s.className = 'bi bi-star star-item';
                s.style.color = '#cbd5e1';
            }
        });
    },

    async submitReview() {
        const submitBtn = document.getElementById('btnSubmitReview');
        const commentInput = document.getElementById('reviewComment');
        const comment = commentInput ? commentInput.value.trim() : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:inline-block; width:12px; height:12px; border:2px solid currentColor; border-right-color:transparent; border-radius:50%; animation:spin 0.6s linear infinite; margin-right:6px;"></span> Submitting...';
        }

        try {
            await Api.submitReview(this.orderNumber, this.selectedRating, comment);
            
            const promptBox = document.getElementById('reviewPrompt');
            const successBox = document.getElementById('reviewSuccessState');
            if (promptBox && successBox) {
                promptBox.style.display = 'none';
                successBox.style.display = 'block';
            }
            if (typeof Utils !== 'undefined' && Utils.showToast) {
                Utils.showToast('Thank you for your feedback! ⭐', 'success');
            }
        } catch (err) {
            if (typeof Utils !== 'undefined' && Utils.showToast) {
                Utils.showToast(err.message || 'Failed to submit review.', 'danger');
            } else {
                alert(err.message || 'Failed to submit review.');
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Review';
            }
        }
    },

    startPolling() {
        // Poll every 4 seconds for live status updates
        this.pollInterval = setInterval(async () => {
            try {
                const resp = await Api.getOrderStatus(this.orderNumber);
                const order = resp.data;
                this.updateUI(order);

                if (order.order_status === 'completed' || order.order_status === 'cancelled') {
                    clearInterval(this.pollInterval);
                }
            } catch (e) {
                console.warn('Tracking poll error', e);
            }
        }, 4000);
    },

    updateUI(order) {
        const status = (order.order_status || '').toLowerCase();
        const statusBadge = document.getElementById('orderStatusBadge');
        const statusText = document.getElementById('orderStatusText');
        if (statusText) {
            statusText.textContent = status.toUpperCase();
        }

        if (statusBadge) {
            statusBadge.className = 'badge';
            if (status === 'placed') statusBadge.classList.add('badge-warning');
            else if (status === 'accepted' || status === 'preparing') statusBadge.classList.add('badge-info');
            else if (status === 'ready') statusBadge.classList.add('badge-success');
            else if (status === 'completed') statusBadge.classList.add('badge-neutral');
            else if (status === 'cancelled') statusBadge.classList.add('badge-danger');
            else statusBadge.classList.add('badge-neutral');
        }

        const steps = ['placed', 'accepted', 'preparing', 'ready', 'completed'];
        const currentIdx = steps.indexOf(status);

        steps.forEach((step, idx) => {
            const el = document.getElementById(`step-${step}`);
            if (!el) return;

            el.classList.remove('active', 'completed');
            if (idx < currentIdx) {
                el.classList.add('completed');
            } else if (idx === currentIdx) {
                el.classList.add('active');
            }
        });

        const kitchenDesc = document.getElementById('kitchenStatusDesc');
        if (kitchenDesc) {
            if (status === 'placed') {
                kitchenDesc.textContent = 'Your order is securely queued and waiting for kitchen staff confirmation.';
            } else if (status === 'accepted') {
                kitchenDesc.textContent = 'The kitchen accepted your ticket and ingredients are being assembled!';
            } else if (status === 'preparing') {
                kitchenDesc.textContent = 'Our chefs are firing up fresh, wholesome ingredients for your dishes.';
            } else if (status === 'ready') {
                kitchenDesc.textContent = 'Fresh and hot! Your food is ready for service / pickup at the counter.';
            } else if (status === 'completed') {
                kitchenDesc.textContent = 'Order finished. Enjoy your meal and thank you for dining with Healthy Bite!';
            }
        }
    }
};

window.OrderTracker = OrderTracker;
