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

        const stageConfigs = {
            placed: {
                badgeClass: 'badge-placed',
                text: 'ORDER PLACED',
                progress: '10%',
                title: 'Order Queued & Received',
                desc: 'Your order ticket has been received by the kitchen queue. Line chefs are assigning your ticket.',
                eta: '~15-20 Mins',
                icon: 'bi-hourglass-split'
            },
            accepted: {
                badgeClass: 'badge-accepted',
                text: 'KITCHEN ACCEPTED',
                progress: '35%',
                title: 'Ticket Accepted — Line Assembling',
                desc: 'The kitchen confirmed your ticket. Fresh, high-protein ingredients are being selected and measured.',
                eta: '~12-16 Mins',
                icon: 'bi-card-checklist'
            },
            preparing: {
                badgeClass: 'badge-preparing',
                text: 'CHEFS COOKING',
                progress: '62%',
                title: 'Chefs Firing Up Your Dishes',
                desc: 'Our line chefs are actively cooking, seasoning, and garnishing your wholesome meal.',
                eta: '~6-10 Mins',
                icon: 'bi-fire'
            },
            ready: {
                badgeClass: 'badge-ready',
                text: 'READY FOR YOU',
                progress: '88%',
                title: 'Fresh & Hot — Ready for Pickup / Table!',
                desc: 'Your meal is freshly plated and ready for pickup at the counter, or being brought right to your table.',
                eta: 'Ready Now!',
                icon: 'bi-bell'
            },
            completed: {
                badgeClass: 'badge-completed',
                text: 'SERVED & ENJOYED',
                progress: '100%',
                title: 'Order Completed — Enjoy Your Meal!',
                desc: 'Your meal has been served. Thank you for dining with Healthy Bite — fuel your body with wholesome nutrition!',
                eta: 'Completed',
                icon: 'bi-emoji-smile'
            },
            cancelled: {
                badgeClass: 'badge-cancelled',
                text: 'ORDER CANCELLED',
                progress: '0%',
                title: 'Order Cancelled',
                desc: 'This order was cancelled. Please speak with restaurant staff if you need assistance.',
                eta: 'Cancelled',
                icon: 'bi-x-octagon'
            }
        };

        const config = stageConfigs[status] || stageConfigs.placed;

        if (statusText) {
            statusText.textContent = config.text;
        }

        if (statusBadge) {
            statusBadge.className = `order-status-badge ${config.badgeClass}`;
        }

        // Update progress bar line
        const progressFill = document.getElementById('trackingProgressFill');
        if (progressFill) {
            progressFill.style.width = config.progress;
        }

        // Update 5-stage timeline steps
        const steps = ['placed', 'accepted', 'preparing', 'ready', 'completed'];
        const currentIdx = steps.indexOf(status);

        steps.forEach((step, idx) => {
            const el = document.getElementById(`step-${step}`);
            if (!el) return;

            el.classList.remove('active', 'completed');
            const dotEl = el.querySelector('.step-dot i');

            if (idx < currentIdx) {
                el.classList.add('completed');
                if (dotEl) dotEl.className = 'bi bi-check-lg';
            } else if (idx === currentIdx) {
                el.classList.add('active');
                if (dotEl) {
                    const activeIcons = {
                        placed: 'bi-clipboard-check',
                        accepted: 'bi-check2-circle',
                        preparing: 'bi-fire',
                        ready: 'bi-bell-fill',
                        completed: 'bi-bag-check-fill'
                    };
                    dotEl.className = `bi ${activeIcons[step] || 'bi-circle-fill'}`;
                }
            } else {
                if (dotEl) {
                    const defaultIcons = {
                        placed: 'bi-clipboard-check',
                        accepted: 'bi-check2-circle',
                        preparing: 'bi-fire',
                        ready: 'bi-bell-fill',
                        completed: 'bi-bag-check-fill'
                    };
                    dotEl.className = `bi ${defaultIcons[step] || 'bi-circle'}`;
                }
            }
        });

        // Update Kitchen Hero Banner
        const heroTitle = document.getElementById('kitchenHeroTitle');
        if (heroTitle) heroTitle.textContent = config.title;

        const kitchenDesc = document.getElementById('kitchenStatusDesc');
        if (kitchenDesc) kitchenDesc.textContent = config.desc;

        const etaText = document.getElementById('kitchenEtaText');
        if (etaText) etaText.textContent = config.eta;

        const heroIcon = document.getElementById('kitchenHeroIcon');
        if (heroIcon) heroIcon.className = `bi ${config.icon}`;
    }
};

window.OrderTracker = OrderTracker;
