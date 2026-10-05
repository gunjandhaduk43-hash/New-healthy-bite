/**
 * HEALTHY BITE — Cart Drawer & Cart Logic
 */

const Cart = {
    drawerEl: null,

    init() {
        this.drawerEl = document.getElementById('cartDrawerBackdrop');

        // Drawer Close Button
        const closeBtn = document.getElementById('cartCloseBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }

        // Drawer Backdrop Click
        if (this.drawerEl) {
            this.drawerEl.addEventListener('click', (e) => {
                if (e.target === this.drawerEl) {
                    this.close();
                }
            });
        }

        // Header Cart Trigger
        const headerTrigger = document.getElementById('cartTrigger');
        if (headerTrigger) {
            headerTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            });
        }

        // Floating Cart Bar (Mobile)
        const floatingBar = document.getElementById('floatingCartBar');
        if (floatingBar) {
            floatingBar.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            });
        }

        // Checkout Button in Drawer
        const checkoutBtn = document.getElementById('btnProceedCheckout');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.proceedToCheckout();
            });
        }

        // Clear Cart Button in Sidebar
        const sidebarClearBtn = document.getElementById('sidebarClearCartBtn');
        if (sidebarClearBtn) {
            sidebarClearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.clear();
                if (window.showToast) {
                    window.showToast('Order cleared', 'info', 'bi-trash');
                } else if (window.Utils) {
                    Utils.showToast('Order cleared');
                }
            });
        }

        // View Cart Button in Sidebar
        const sidebarViewCartBtn = document.getElementById('sidebarViewCartBtn');
        if (sidebarViewCartBtn) {
            sidebarViewCartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.proceedToCheckout();
            });
        }
        document.querySelectorAll('.btn-sidebar-viewcart').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.proceedToCheckout();
            });
        });

        // Subscribe to state updates
        State.subscribe('cartUpdated', () => {
            this.render();
        });

        // Load existing cart from storage and perform initial render
        State.loadCartFromStorage();
        this.render();
    },

    open() {
        if (!this.drawerEl) {
            this.proceedToCheckout();
            return;
        }
        this.drawerEl.classList.add('active');
        document.body.style.overflow = 'hidden';
        this.render();
    },

    close() {
        if (!this.drawerEl) return;
        this.drawerEl.classList.remove('active');
        document.body.style.overflow = '';
    },

    addItem(newItem) {
        if (!State.cart) State.cart = [];

        newItem.quantity = Math.max(1, parseInt(newItem.quantity) || 1);
        newItem.unit_price = parseFloat(newItem.unit_price) || 0;
        newItem.line_total = window.Pricing 
            ? Pricing.calculateLineTotal(newItem.unit_price, newItem.quantity)
            : (newItem.unit_price * newItem.quantity);

        // Find if identical configuration exists
        const existingIndex = State.cart.findIndex(item => {
            if (item.food_id !== newItem.food_id) return false;
            if (item.variant_id !== newItem.variant_id) return false;
            if (item.restaurant_id !== newItem.restaurant_id) return false;
            if (item.branch_id !== newItem.branch_id) return false;

            if ((item.special_instructions || '') !== (newItem.special_instructions || '')) return false;

            // Check customizations equality
            const itemCust = item.customizations || [];
            const newCust = newItem.customizations || [];
            if (itemCust.length !== newCust.length) return false;
            const itemCustIds = itemCust.map(c => `${c.id}:${c.quantity || 1}`).sort().join(',');
            const newCustIds = newCust.map(c => `${c.id}:${c.quantity || 1}`).sort().join(',');
            return itemCustIds === newCustIds;
        });

        if (existingIndex > -1) {
            State.cart[existingIndex].quantity += newItem.quantity;
            State.cart[existingIndex].line_total = window.Pricing
                ? Pricing.calculateLineTotal(State.cart[existingIndex].unit_price, State.cart[existingIndex].quantity)
                : (State.cart[existingIndex].unit_price * State.cart[existingIndex].quantity);
        } else {
            State.cart.push(newItem);
        }

        State.saveCartToStorage();
        this.render();
    },

    updateQuantity(index, delta) {
        if (!State.cart || !State.cart[index]) return;

        State.cart[index].quantity += delta;
        if (State.cart[index].quantity <= 0) {
            State.cart.splice(index, 1);
        } else {
            State.cart[index].line_total = window.Pricing
                ? Pricing.calculateLineTotal(State.cart[index].unit_price, State.cart[index].quantity)
                : (State.cart[index].unit_price * State.cart[index].quantity);
        }

        State.saveCartToStorage();
        this.render();
    },

    removeItem(index) {
        if (State.cart && State.cart[index]) {
            State.cart.splice(index, 1);
            State.saveCartToStorage();
            this.render();
            if (window.Utils) {
                Utils.showToast('Item removed from cart');
            }
        }
    },

    editItem(index) {
        if (!State.cart || !State.cart[index]) return;
        const item = State.cart[index];
        if (window.FoodDetails) {
            FoodDetails.open(item.food_id, index);
        }
    },

    updateItem(index, updatedItem) {
        if (!State.cart || !State.cart[index]) return;

        updatedItem.quantity = Math.max(1, parseInt(updatedItem.quantity) || 1);
        updatedItem.unit_price = parseFloat(updatedItem.unit_price) || 0;
        updatedItem.line_total = window.Pricing 
            ? Pricing.calculateLineTotal(updatedItem.unit_price, updatedItem.quantity)
            : (updatedItem.unit_price * updatedItem.quantity);

        State.cart[index] = updatedItem;
        State.saveCartToStorage();
        this.render();
    },

    clear() {
        State.cart = [];
        State.saveCartToStorage();
        this.render();
    },

    proceedToCheckout() {
        if (!State.cart || State.cart.length === 0) {
            if (window.Utils) {
                Utils.showToast('Your cart is empty', 'error');
            } else {
                alert('Your cart is empty');
            }
            return;
        }

        let checkoutUrl = '/menu/checkout';
        const params = new URLSearchParams();
        
        // Preserve QR Token
        const urlParams = new URLSearchParams(window.location.search);
        const currentToken = State.qrToken || urlParams.get('token');
        if (currentToken) {
            params.set('token', currentToken);
        } else {
            const restaurantId = (State.restaurant && State.restaurant.id) ? State.restaurant.id : (urlParams.get('restaurant_id') || 1);
            params.set('restaurant_id', restaurantId);
        }

        const queryString = params.toString();
        if (queryString) {
            checkoutUrl += '?' + queryString;
        }

        window.location.href = checkoutUrl;
    },

    render() {
        const cartItems = State.cart || [];
        const totalItemsCount = cartItems.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
        const totals = window.Pricing 
            ? Pricing.calculateCartTotals(cartItems) 
            : { subtotal: 0, tax: 0, service_charge: 0, total_amount: 0 };

        // 1. Update Header Cart Badges
        const headerBadge = document.getElementById('cartCount');
        if (headerBadge) {
            headerBadge.textContent = totalItemsCount;
            headerBadge.style.display = totalItemsCount > 0 ? 'inline-flex' : 'none';
        }

        // 2. Update Mobile Floating Cart Bar
        const floatingBar = document.getElementById('floatingCartBar');
        if (floatingBar) {
            if (totalItemsCount > 0) {
                floatingBar.style.display = 'flex';
                const floatingBadge = document.getElementById('floatingCartBadge');
                const floatingTotal = document.getElementById('floatingCartTotal');
                if (floatingBadge) floatingBadge.textContent = `${totalItemsCount} item${totalItemsCount > 1 ? 's' : ''}`;
                if (floatingTotal) floatingTotal.textContent = window.Utils ? Utils.formatCurrency(totals.total_amount) : ('₹' + totals.total_amount);
            } else {
                floatingBar.style.display = 'none';
            }
        }

        // 3. Render Right Sidebar Widget (Menu Page Desktop)
        const sidebarItems = document.getElementById('sidebarOrderItems');
        const sidebarCount = document.getElementById('sidebarOrderCount');
        const sidebarFooter = document.getElementById('sidebarOrderFooter');
        const sidebarTotalItemsLabel = document.getElementById('sidebarTotalItemsLabel');
        const sidebarTotalAmount = document.getElementById('sidebarTotalAmount');

        if (sidebarCount) {
            sidebarCount.textContent = totalItemsCount;
        }

        if (sidebarItems) {
            if (cartItems.length === 0) {
                sidebarItems.innerHTML = `
                    <div class="order-widget-empty" id="sidebarOrderEmpty">
                        <i class="bi bi-basket3"></i>
                        <p>Your table order is empty.<br>Choose delicious meals from the menu.</p>
                    </div>
                `;
                if (sidebarFooter) sidebarFooter.style.display = 'none';
            } else {
                if (sidebarFooter) sidebarFooter.style.display = 'block';
                if (sidebarTotalItemsLabel) sidebarTotalItemsLabel.textContent = totalItemsCount;
                if (sidebarTotalAmount) {
                    sidebarTotalAmount.textContent = window.Utils ? Utils.formatCurrency(totals.total_amount) : ('₹' + totals.total_amount);
                }

                sidebarItems.innerHTML = '';
                cartItems.forEach((item, index) => {
                    const itemCard = document.createElement('div');
                    itemCard.className = 'order-item-card';

                    let metaText = '';
                    if (item.variant_name) {
                        metaText += item.variant_name;
                    }
                    if (item.customizations && item.customizations.length > 0) {
                        const cText = item.customizations.map(c => c.name).join(', ');
                        metaText += (metaText ? ' • ' : '') + cText;
                    }
                    if (item.special_instructions) {
                        metaText += (metaText ? ' • Note: ' : 'Note: ') + item.special_instructions;
                    }

                    let macroText = '';
                    if (item.nutrition && item.nutrition.calories !== null && item.nutrition.calories !== undefined) {
                        macroText = `<span class="macro-chip" style="font-size:10px;padding:1px 5px;"><i class="bi bi-fire" style="color:#f59e0b;"></i> ${item.nutrition.calories} kcal</span>`;
                        if (item.nutrition.protein !== null && item.nutrition.protein !== undefined) {
                            macroText += `<span class="macro-chip macro-highlight" style="font-size:10px;padding:1px 5px;">${item.nutrition.protein}g P</span>`;
                        }
                        if (item.nutrition.sugar !== null && item.nutrition.sugar !== undefined) {
                            macroText += `<span class="macro-chip" style="font-size:10px;padding:1px 5px;background:#fef3c7;color:#92400e;border-color:#fde68a;">${item.nutrition.sugar}g Sugar</span>`;
                        }
                        if (item.nutrition.caffeine !== null && item.nutrition.caffeine !== undefined && parseFloat(item.nutrition.caffeine) > 0) {
                            macroText += `<span class="macro-chip" style="font-size:10px;padding:1px 5px;background:#f3e8ff;color:#6b21a8;border-color:#e9d5ff;"><i class="bi bi-cup-hot"></i> ${item.nutrition.caffeine}mg Caff</span>`;
                        }
                    }

                    const rawImg = item.image || item.food_image || 'placeholder-dish.svg';
                    const imgSrc = (rawImg.startsWith('http://') || rawImg.startsWith('https://')) 
                        ? rawImg 
                        : (rawImg.startsWith('/assets/') ? rawImg : (rawImg.startsWith('assets/') ? ('/' + rawImg) : ('/assets/images/foods/' + rawImg.replace(/^\//, ''))));

                    const linePriceStr = window.Utils ? Utils.formatCurrency(item.line_total) : ('₹' + item.line_total);
                    const foodNameEscaped = window.Utils ? Utils.escapeHtml(item.food_name) : item.food_name;

                    itemCard.innerHTML = `
                        <img src="${imgSrc}" class="order-item-img" style="width:42px;height:42px;min-width:42px;max-width:42px;object-fit:cover;border-radius:8px;flex-shrink:0;display:block;" alt="${foodNameEscaped}" onerror="this.src='/assets/images/foods/placeholder-dish.svg'">
                        <div class="order-item-info">
                            <div class="order-item-title-row">
                                <span class="order-item-title">${foodNameEscaped}</span>
                                <span class="order-item-price">${linePriceStr}</span>
                            </div>
                            ${metaText ? `<div class="order-item-meta">${window.Utils ? Utils.escapeHtml(metaText) : metaText}</div>` : ''}
                            ${macroText ? `<div class="order-item-macros">${macroText}</div>` : ''}
                        </div>
                        <div class="order-item-stepper">
                            <button type="button" class="order-item-edit-btn" onclick="Cart.editItem(${index})" title="Edit customizations" aria-label="Edit item"><i class="bi bi-pencil-square"></i></button>
                            <button type="button" class="order-stepper-btn" onclick="Cart.updateQuantity(${index}, -1)" aria-label="Decrease quantity">-</button>
                            <span class="order-stepper-val">${item.quantity}</span>
                            <button type="button" class="order-stepper-btn" onclick="Cart.updateQuantity(${index}, 1)" aria-label="Increase quantity">+</button>
                            <button type="button" class="order-item-del" onclick="Cart.removeItem(${index})" title="Remove item" aria-label="Remove item"><i class="bi bi-trash3"></i></button>
                        </div>
                    `;
                    sidebarItems.appendChild(itemCard);
                });
            }
        }

        // Calculate Cart Totals: Calories, Protein, and Sugar (Multiplied by quantity for each item)
        let totalCartCalories = 0;
        let totalCartProtein = 0;
        let totalCartSugar = 0;
        let hasAnyCalData = false;
        let hasAnyProtData = false;
        let hasAnySugarData = false;

        cartItems.forEach(item => {
            const qty = Math.max(1, parseInt(item.quantity) || 1);
            if (item.nutrition) {
                if (item.nutrition.calories !== null && item.nutrition.calories !== undefined) {
                    totalCartCalories += (parseFloat(item.nutrition.calories) * qty);
                    hasAnyCalData = true;
                }
                if (item.nutrition.protein !== null && item.nutrition.protein !== undefined) {
                    totalCartProtein += (parseFloat(item.nutrition.protein) * qty);
                    hasAnyProtData = true;
                }
                if (item.nutrition.sugar !== null && item.nutrition.sugar !== undefined) {
                    totalCartSugar += (parseFloat(item.nutrition.sugar) * qty);
                    hasAnySugarData = true;
                }
            }
        });

        const calDisplayStr = hasAnyCalData ? `${Math.round(totalCartCalories)} kcal` : '—';
        const protDisplayStr = hasAnyProtData ? `${Math.round(totalCartProtein * 10) / 10} g` : '—';
        const sugarDisplayStr = hasAnySugarData ? `${Math.round(totalCartSugar * 10) / 10} g` : '0 g';

        // Update Drawer elements
        const drawerCalEl = document.getElementById('cartTotalCalories');
        if (drawerCalEl) drawerCalEl.textContent = calDisplayStr;

        const drawerProtEl = document.getElementById('cartTotalProtein');
        if (drawerProtEl) drawerProtEl.textContent = protDisplayStr;

        const drawerSugarEl = document.getElementById('cartTotalSugar');
        if (drawerSugarEl) drawerSugarEl.textContent = sugarDisplayStr;

        // Update Desktop Sidebar elements
        const sidebarCalEl = document.getElementById('sidebarTotalCalories');
        if (sidebarCalEl) sidebarCalEl.textContent = calDisplayStr;

        const sidebarProtEl = document.getElementById('sidebarTotalProtein');
        if (sidebarProtEl) sidebarProtEl.textContent = protDisplayStr;

        const sidebarSugarEl = document.getElementById('sidebarTotalSugar');
        if (sidebarSugarEl) sidebarSugarEl.textContent = sugarDisplayStr;

        // 4. Render Drawer Elements (if present)
        const cartItemsList = document.getElementById('cartItemsList');
        const emptyState = document.getElementById('cartEmptyState');
        const cartFooter = document.getElementById('cartFooter');
        const subtotalEl = document.getElementById('cartSubtotal');
        const taxEl = document.getElementById('cartTax');
        const totalEl = document.getElementById('cartTotal');

        if (cartItemsList) {
            if (cartItems.length === 0) {
                cartItemsList.innerHTML = '';
                if (emptyState) emptyState.style.display = 'block';
                if (cartFooter) cartFooter.style.display = 'none';
            } else {
                if (emptyState) emptyState.style.display = 'none';
                if (cartFooter) cartFooter.style.display = 'block';

                cartItemsList.innerHTML = '';
                cartItems.forEach((item, index) => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'cart-item';

                    let customText = '';
                    if (item.variant_name) {
                        customText += `<div>Size: ${window.Utils ? Utils.escapeHtml(item.variant_name) : item.variant_name}</div>`;
                    }
                    if (item.customizations && item.customizations.length > 0) {
                        const cNames = item.customizations.map(c => c.name).join(', ');
                        customText += `<div>Add-ons: ${window.Utils ? Utils.escapeHtml(cNames) : cNames}</div>`;
                    }

                    let nutText = '';
                    if (item.nutrition && item.nutrition.calories !== null && item.nutrition.calories !== undefined) {
                        nutText = `${item.nutrition.calories} kcal · ${item.nutrition.protein}g protein`;
                        if (item.nutrition.sugar !== null && item.nutrition.sugar !== undefined) {
                            nutText += ` · ${item.nutrition.sugar}g sugar`;
                        }
                        if (item.nutrition.caffeine !== null && item.nutrition.caffeine !== undefined && parseFloat(item.nutrition.caffeine) > 0) {
                            nutText += ` · ${item.nutrition.caffeine}mg caffeine`;
                        }
                    }

                    const foodNameEscaped = window.Utils ? Utils.escapeHtml(item.food_name) : item.food_name;
                    const linePriceStr = window.Utils ? Utils.formatCurrency(item.line_total) : ('₹' + item.line_total);

                    itemDiv.innerHTML = `
                        <div class="cart-item-top">
                            <span class="cart-item-name">${foodNameEscaped}</span>
                            <span class="cart-item-price">${linePriceStr}</span>
                        </div>
                        ${customText ? `<div class="cart-item-meta">${customText}</div>` : ''}
                        <div class="cart-item-bottom">
                            <span class="cart-item-nutrition">${nutText}</span>
                            <div class="cart-item-actions">
                                <button type="button" class="btn-edit-cart-item" onclick="Cart.editItem(${index})" title="Edit customizations & portion" aria-label="Edit item">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit</span>
                                </button>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="Cart.updateQuantity(${index}, -1)" aria-label="Decrease quantity">-</button>
                                    <span class="qty-display">${item.quantity}</span>
                                    <button class="qty-btn" onclick="Cart.updateQuantity(${index}, 1)" aria-label="Increase quantity">+</button>
                                </div>
                                <button class="btn-remove-item" onclick="Cart.removeItem(${index})" title="Remove item" aria-label="Remove item">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    cartItemsList.appendChild(itemDiv);
                });

                if (subtotalEl) subtotalEl.textContent = window.Utils ? Utils.formatCurrency(totals.subtotal) : ('₹' + totals.subtotal);
                if (taxEl) taxEl.textContent = window.Utils ? Utils.formatCurrency(totals.tax) : ('₹' + totals.tax);
                if (totalEl) totalEl.textContent = window.Utils ? Utils.formatCurrency(totals.total_amount) : ('₹' + totals.total_amount);
            }

            // Render Complete-the-Meal Recommendations (Prompt Requirement #25)
            this.renderCompleteTheMeal(cartItems);
        }
    },

    renderCompleteTheMeal(cartItems) {
        const suggestionsBox = document.getElementById('cartSuggestionsBox');
        const suggestionsRow = document.getElementById('cartSuggestionsRow');
        if (!suggestionsBox || !suggestionsRow) return;

        if (!cartItems || cartItems.length === 0) {
            suggestionsBox.style.display = 'none';
            return;
        }

        // Curated pairing catalog matching Healthy Bite's real database items
        const pairings = [
            {
                food_id: 4,
                name: 'Iced Protein Coffee',
                price: 165,
                category: 'beverages',
                image: 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=400&auto=format&fit=crop&q=80',
                calories: 140,
                protein: 15,
                sugar: 4.0
            },
            {
                food_id: 47,
                name: 'Greek Yogurt Parfait with Berries',
                price: 169,
                category: 'desserts',
                image: 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&auto=format&fit=crop&q=80',
                calories: 190,
                protein: 16,
                sugar: 8.0
            },
            {
                food_id: 42,
                name: 'Raw Coconut Water with Chia Seeds',
                price: 129,
                category: 'beverages',
                image: 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=400&auto=format&fit=crop&q=80',
                calories: 85,
                protein: 3,
                sugar: 8.0
            },
            {
                food_id: 39,
                name: 'Air-Fried Sweet Potato Fries',
                price: 169,
                category: 'appetizers',
                image: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=400&auto=format&fit=crop&q=80',
                calories: 220,
                protein: 3,
                sugar: 8.0
            }
        ];

        // Filter out items already in cart
        const cartFoodNames = cartItems.map(i => (i.food_name || '').toLowerCase());
        const availablePairings = pairings.filter(p => !cartFoodNames.includes(p.name.toLowerCase()));

        if (availablePairings.length === 0) {
            suggestionsBox.style.display = 'none';
            return;
        }

        suggestionsBox.style.display = 'block';
        suggestionsRow.innerHTML = '';

        availablePairings.forEach(p => {
            const chip = document.createElement('div');
            chip.className = 'suggestion-chip-card';
            chip.innerHTML = `
                <div class="suggestion-chip-top">
                    <img src="${p.image}" class="suggestion-chip-img" alt="${p.name}" onerror="this.src='/assets/images/foods/placeholder-dish.svg'">
                    <span class="suggestion-chip-name" title="${p.name}">${p.name}</span>
                </div>
                <div class="suggestion-chip-bottom">
                    <span class="suggestion-chip-price">₹${p.price}</span>
                    <button type="button" class="btn-add-suggestion" onclick="Cart.addQuickPairing(${p.food_id}, '${p.name.replace(/'/g, "\\'")}', ${p.price}, '${p.image}', ${p.calories}, ${p.protein}, ${p.sugar})">
                        + Add
                    </button>
                </div>
            `;
            suggestionsRow.appendChild(chip);
        });
    },

    addQuickPairing(id, name, price, image, calories, protein, sugar) {
        if (window.FoodDetails) {
            FoodDetails.open(id);
            return;
        }

        const item = {
            food_id: id,
            food_name: name,
            variant_id: null,
            variant_name: null,
            unit_price: price,
            quantity: 1,
            line_total: price,
            image: image,
            restaurant_id: (State.restaurant && State.restaurant.id) ? State.restaurant.id : 1,
            branch_id: (State.branch && State.branch.id) ? State.branch.id : 1,
            customizations: [],
            special_instructions: '',
            nutrition: {
                calories: calories,
                protein: protein,
                carbs: null,
                fat: null,
                sugar: sugar !== undefined ? sugar : null
            }
        };

        this.addItem(item);
        if (window.showToast) {
            window.showToast(`Added ${name} to your order!`, 'success', 'bi-bag-plus-fill');
        } else if (window.Utils) {
            Utils.showToast(`Added ${name} to your order!`);
        }
    }
};

window.Cart = Cart;
