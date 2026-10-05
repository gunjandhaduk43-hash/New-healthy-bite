/**
 * HEALTHY BITE — Checkout Controller
 */

const Checkout = {
    orderType: 'dine_in',
    selectedPaymentMethod: 'upi',

    init() {
        State.loadCartFromStorage();

        if (!State.cart || State.cart.length === 0) {
            const urlParams = new URLSearchParams(window.location.search);
            const token = State.qrToken || urlParams.get('token');
            const returnUrl = token ? ('/menu?token=' + encodeURIComponent(token)) : '/menu';
            window.location.href = returnUrl;
            return;
        }

        this.setupOrderTypeSwitch();
        this.setupPaymentMethodSelection();
        this.renderCheckoutSummary();
        this.setupFormSubmit();
    },

    setupOrderTypeSwitch() {
        const dineInBtn = document.getElementById('switchDineIn');
        const takeawayBtn = document.getElementById('switchTakeaway');
        const tableGroup = document.getElementById('tableInfoGroup');
        const takeawayNote = document.getElementById('takeawayInfoNote');

        if (dineInBtn && takeawayBtn) {
            dineInBtn.addEventListener('click', () => {
                dineInBtn.classList.add('active');
                takeawayBtn.classList.remove('active');
                this.orderType = 'dine_in';
                if (tableGroup) tableGroup.style.display = 'block';
                if (takeawayNote) takeawayNote.style.display = 'none';
            });

            takeawayBtn.addEventListener('click', () => {
                takeawayBtn.classList.add('active');
                dineInBtn.classList.remove('active');
                this.orderType = 'takeaway';
                if (tableGroup) tableGroup.style.display = 'none';
                if (takeawayNote) takeawayNote.style.display = 'flex';
            });
        }
    },

    setupPaymentMethodSelection() {
        const cards = document.querySelectorAll('.payment-method-card');
        cards.forEach(card => {
            card.addEventListener('click', () => {
                cards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                this.selectedPaymentMethod = card.getAttribute('data-method') || 'upi';
            });
        });
    },

    renderCheckoutSummary() {
        const itemsList = document.getElementById('checkoutSummaryItems');
        if (!itemsList) return;

        itemsList.innerHTML = '';
        let totalCheckoutCalories = 0;
        let totalCheckoutProtein = 0;
        let totalCheckoutSugar = 0;
        let hasAnyCal = false;
        let hasAnyProtein = false;
        let hasAnySugar = false;

        State.cart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'summary-item-row';
            let detail = item.food_name;
            if (item.variant_name) detail += ` (${item.variant_name})`;

            let nutDetail = '';
            if (item.nutrition) {
                const parts = [];
                const qty = Math.max(1, parseInt(item.quantity) || 1);
                if (item.nutrition.calories !== null && item.nutrition.calories !== undefined) {
                    parts.push(`${item.nutrition.calories} kcal`);
                    totalCheckoutCalories += (parseFloat(item.nutrition.calories) * qty);
                    hasAnyCal = true;
                }
                if (item.nutrition.protein !== null && item.nutrition.protein !== undefined) {
                    parts.push(`${item.nutrition.protein}g protein`);
                    totalCheckoutProtein += (parseFloat(item.nutrition.protein) * qty);
                    hasAnyProtein = true;
                }
                if (item.nutrition.sugar !== null && item.nutrition.sugar !== undefined) {
                    parts.push(`${item.nutrition.sugar}g sugar`);
                    totalCheckoutSugar += (parseFloat(item.nutrition.sugar) * qty);
                    hasAnySugar = true;
                }
                if (item.nutrition.caffeine !== null && item.nutrition.caffeine !== undefined && parseFloat(item.nutrition.caffeine) > 0) {
                    parts.push(`${item.nutrition.caffeine}mg caffeine`);
                }
                if (parts.length > 0) {
                    nutDetail = parts.join(' · ');
                }
            }

            row.innerHTML = `
                <div>
                    <div><strong>${item.quantity}x</strong> ${Utils.escapeHtml(detail)}</div>
                    ${item.customizations && item.customizations.length > 0 ? `<small style="color:var(--text-muted); display:block; margin:2px 0;">+ ${item.customizations.map(c => c.name).join(', ')}</small>` : ''}
                    ${nutDetail ? `<div style="font-size:11px;color:#047857;font-weight:600;">${nutDetail} per item</div>` : ''}
                </div>
                <div><strong>${Utils.formatCurrency(item.line_total)}</strong></div>
            `;
            itemsList.appendChild(row);
        });

        const totals = Pricing.calculateCartTotals(State.cart);
        const subtotalEl = document.getElementById('checkoutSubtotal');
        const taxEl = document.getElementById('checkoutTax');
        const totalEl = document.getElementById('checkoutTotal');
        const totalCalEl = document.getElementById('checkoutTotalCalories');
        const totalProtEl = document.getElementById('checkoutTotalProtein');
        const totalSugarEl = document.getElementById('checkoutTotalSugar');
        const submitBtnText = document.getElementById('btnSubmitOrderText');

        if (totalCalEl) {
            totalCalEl.textContent = hasAnyCal ? `${Math.round(totalCheckoutCalories)} kcal` : '—';
        }
        if (totalProtEl) {
            totalProtEl.textContent = hasAnyProtein ? `${Math.round(totalCheckoutProtein * 10) / 10} g` : '—';
        }
        if (totalSugarEl) {
            totalSugarEl.textContent = hasAnySugar ? `${Math.round(totalCheckoutSugar * 10) / 10} g` : '0 g';
        }
        if (subtotalEl) subtotalEl.textContent = Utils.formatCurrency(totals.subtotal);
        if (taxEl) taxEl.textContent = Utils.formatCurrency(totals.tax);
        if (totalEl) totalEl.textContent = Utils.formatCurrency(totals.total_amount);
        if (submitBtnText) submitBtnText.textContent = `Place Order — ${Utils.formatCurrency(totals.total_amount)}`;
    },

    setupFormSubmit() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const notes = document.getElementById('orderNotes').value.trim();
            const submitBtn = document.getElementById('btnSubmitOrder');

            if (!name) {
                Utils.showToast('Please enter your name', 'error');
                return;
            }

            if (!State.cart || State.cart.length === 0) {
                Utils.showToast('Your cart is empty', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Placing Order...';

            const tableSelect = document.getElementById('selectedTableId');
            let selectedTableId = null;
            if (this.orderType === 'dine_in') {
                if (tableSelect && tableSelect.value) {
                    selectedTableId = parseInt(tableSelect.value);
                } else if (State.table && State.table.id) {
                    selectedTableId = State.table.id;
                } else {
                    selectedTableId = 4;
                }
            }

            try {
                const urlParams = new URLSearchParams(window.location.search);
                const token = State.qrToken || urlParams.get('token') || '';

                const payload = {
                    restaurant_id: (State.restaurant && State.restaurant.id) ? State.restaurant.id : 1,
                    branch_id: (State.branch && State.branch.id) ? State.branch.id : 1,
                    table_id: selectedTableId,
                    qr_token: token,
                    order_type: this.orderType,
                    customer_name: name,
                    customer_mobile: phone,
                    notes: notes,
                    items: State.cart.map(i => ({
                        food_id: parseInt(i.food_id),
                        variant_id: i.variant_id ? parseInt(i.variant_id) : null,
                        customizations: (i.customizations || []).map(c => ({
                            id: parseInt(c.id),
                            quantity: parseInt(c.quantity) || 1
                        })),
                        quantity: Math.max(1, parseInt(i.quantity) || 1)
                    }))
                };

                const resp = await Api.submitOrder(payload);
                const order = resp.data;

                // Simulate instant payment
                await Api.simulatePayment(order.id, this.selectedPaymentMethod);

                // Save active order number in localStorage for instant 1-tap Live Kitchen access
                if (order && order.order_number) {
                    localStorage.setItem('active_order_number', order.order_number);
                    localStorage.setItem('active_order_id', order.id);
                }

                // Clear cart from storage
                Cart.clear();

                // Redirect to Confirmation
                window.location.href = `/menu/confirmation/${order.order_number}`;
            } catch (err) {
                Utils.showToast(err.message || 'Failed to place order', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Try Again';
            }
        });
    }
};

window.Checkout = Checkout;
