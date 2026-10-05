/**
 * HEALTHY BITE — Professional Food Customization Modal Controller
 * Matches Reference UI Design (2-Panel Desktop + Mobile Sheet)
 */

const FoodDetails = {
    modalEl: null,
    isLoading: false,
    editingCartIndex: null,

    init() {
        this.modalEl = document.getElementById('foodDetailModal');
        if (!this.modalEl) return;

        // Close on backdrop click (outside the modal box)
        this.modalEl.addEventListener('click', (e) => {
            if (e.target === this.modalEl) {
                this.close();
            }
        });

        // Close button click
        const closeBtn = document.getElementById('modalCloseBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modalEl.classList.contains('active')) {
                this.close();
            }
        });

        // Quantity Steppers
        const qtyDec = document.getElementById('modalQtyDec');
        const qtyInc = document.getElementById('modalQtyInc');

        if (qtyDec) {
            qtyDec.addEventListener('click', () => {
                if (State.modalQuantity > 1) {
                    State.modalQuantity--;
                    this.updateCalculations();
                }
            });
        }

        if (qtyInc) {
            qtyInc.addEventListener('click', () => {
                if (State.modalQuantity < 20) {
                    State.modalQuantity++;
                    this.updateCalculations();
                }
            });
        }

        // Add to Cart button
        const addBtn = document.getElementById('modalAddToCartBtn');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                this.addToCart();
            });
        }
    },

    async open(foodId, editingCartIndex = null) {
        if (this.isLoading) return;
        this.isLoading = true;
        this.editingCartIndex = (editingCartIndex !== null && editingCartIndex !== undefined) ? editingCartIndex : null;

        try {
            const restaurantId = (State.restaurant && State.restaurant.id) ? State.restaurant.id : 1;
            const resp = await Api.fetchFoodDetails(foodId, restaurantId);
            const food = resp.data;

            if (!food) {
                throw new Error('Food details not found');
            }

            State.selectedFood = food;

            if (this.editingCartIndex !== null && State.cart && State.cart[this.editingCartIndex]) {
                const cartItem = State.cart[this.editingCartIndex];

                // 1. Pre-select variant
                if (cartItem.variant_id && food.variants && food.variants.length > 0) {
                    State.selectedVariant = food.variants.find(v => Number(v.id) === Number(cartItem.variant_id)) || food.variants[0];
                } else if (food.variants && food.variants.length > 0) {
                    State.selectedVariant = food.variants[0];
                } else {
                    State.selectedVariant = null;
                }

                // 2. Pre-select customizations from cart item
                State.selectedCustomizations = [];
                const groups = food.customization_groups || {};
                const allAvailableOptions = [];
                Object.keys(groups).forEach(gName => {
                    (groups[gName] || []).forEach(opt => {
                        allAvailableOptions.push({ ...opt, group_name: gName });
                    });
                });

                const savedCusts = cartItem.customizations || [];
                savedCusts.forEach(sc => {
                    const matched = allAvailableOptions.find(o => Number(o.id) === Number(sc.id));
                    if (matched) {
                        State.selectedCustomizations.push({
                            id: matched.id,
                            name: matched.name,
                            group_name: matched.group_name,
                            price_adjustment: parseFloat(matched.price_adjustment) || 0,
                            calories_adjustment: matched.calories_adjustment !== null ? parseInt(matched.calories_adjustment) : null,
                            protein_adjustment: matched.protein_adjustment !== null ? parseFloat(matched.protein_adjustment) : null,
                            carbs_adjustment: matched.carbs_adjustment !== null ? parseFloat(matched.carbs_adjustment) : null,
                            fat_adjustment: matched.fat_adjustment !== null ? parseFloat(matched.fat_adjustment) : null,
                            sugar_adjustment: (matched.sugar_adjustment !== null && matched.sugar_adjustment !== undefined) ? parseFloat(matched.sugar_adjustment) : null,
                            caffeine_adjustment: (matched.caffeine_adjustment !== null && matched.caffeine_adjustment !== undefined) ? parseFloat(matched.caffeine_adjustment) : null,
                            quantity: sc.quantity || 1
                        });
                    } else {
                        State.selectedCustomizations.push({ ...sc });
                    }
                });

                // Auto-fill any missing required single-select groups
                Object.keys(groups).forEach(gName => {
                    const options = groups[gName] || [];
                    const isReq = options.some(o => parseInt(o.is_required) === 1 || parseInt(o.min_quantity) >= 1);
                    const maxQty = Math.max(...options.map(o => parseInt(o.max_quantity) || 1));
                    const alreadySelected = State.selectedCustomizations.some(c => c.group_name === gName);
                    if (isReq && !alreadySelected && options.length > 0 && maxQty === 1) {
                        const defaultOpt = options[0];
                        State.selectedCustomizations.push({
                            id: defaultOpt.id,
                            name: defaultOpt.name,
                            group_name: gName,
                            price_adjustment: parseFloat(defaultOpt.price_adjustment) || 0,
                            calories_adjustment: defaultOpt.calories_adjustment !== null ? parseInt(defaultOpt.calories_adjustment) : null,
                            protein_adjustment: defaultOpt.protein_adjustment !== null ? parseFloat(defaultOpt.protein_adjustment) : null,
                            carbs_adjustment: defaultOpt.carbs_adjustment !== null ? parseFloat(defaultOpt.carbs_adjustment) : null,
                            fat_adjustment: defaultOpt.fat_adjustment !== null ? parseFloat(defaultOpt.fat_adjustment) : null,
                            sugar_adjustment: (defaultOpt.sugar_adjustment !== null && defaultOpt.sugar_adjustment !== undefined) ? parseFloat(defaultOpt.sugar_adjustment) : null,
                            caffeine_adjustment: (defaultOpt.caffeine_adjustment !== null && defaultOpt.caffeine_adjustment !== undefined) ? parseFloat(defaultOpt.caffeine_adjustment) : null,
                            quantity: 1
                        });
                    }
                });

                // 3. Pre-select quantity
                State.modalQuantity = Math.max(1, parseInt(cartItem.quantity) || 1);

                // 4. Pre-fill special instructions
                State.specialInstructions = cartItem.special_instructions || '';
                const instrEl = document.getElementById('modalSpecialInstructions');
                if (instrEl) instrEl.value = State.specialInstructions;

            } else {
                State.selectedVariant = (food.variants && food.variants.length > 0) ? food.variants[0] : null;
                State.selectedCustomizations = [];
                State.modalQuantity = 1;
                State.specialInstructions = '';

                // Reset textarea
                const instrEl = document.getElementById('modalSpecialInstructions');
                if (instrEl) instrEl.value = '';

                // Auto-select default options for single-choice / required groups
                this.initializeDefaultCustomizations(food);
            }

            // Render modal structure
            this.renderLeftPanel(food);
            this.renderRightPanel(food);
            this.updateCalculations();

            // When editing, customize title
            if (this.editingCartIndex !== null) {
                const titleEl = document.getElementById('modalCustomTitle');
                if (titleEl) {
                    titleEl.innerHTML = `<i class="bi bi-pencil-square" style="color:var(--primary-green); margin-right:6px;"></i> Edit ${window.Utils ? Utils.escapeHtml(food.name) : food.name}`;
                }
            }

            // Display modal
            this.modalEl.classList.add('active');
            document.body.style.overflow = 'hidden';

        } catch (e) {
            console.error('Failed to load food details', e);
            const msg = e.message || 'Could not load food details';
            if (window.showToast) {
                window.showToast(msg, 'error', 'bi-exclamation-triangle-fill');
            } else if (window.Utils) {
                Utils.showToast(msg, 'error');
            }
        } finally {
            this.isLoading = false;
        }
    },

    close() {
        if (!this.modalEl) return;
        this.modalEl.classList.remove('active');
        document.body.style.overflow = '';
        State.selectedFood = null;
        const wasEditing = this.editingCartIndex !== null;
        this.editingCartIndex = null;
        if (wasEditing && window.Cart) {
            Cart.open();
        }
    },

    /**
     * Pre-select the first option for required single-select customization groups
     */
    initializeDefaultCustomizations(food) {
        const groups = food.customization_groups || {};
        Object.keys(groups).forEach(groupName => {
            const options = groups[groupName];
            if (!options || options.length === 0) return;

            const isRequired = options.some(o => parseInt(o.is_required) === 1 || parseInt(o.min_quantity) >= 1);
            const maxQty = Math.max(...options.map(o => parseInt(o.max_quantity) || 1));

            // If required single-select group, auto-select first item
            if (isRequired && maxQty === 1) {
                const defaultOpt = options[0];
                State.selectedCustomizations.push({
                    id: defaultOpt.id,
                    name: defaultOpt.name,
                    group_name: groupName,
                    price_adjustment: parseFloat(defaultOpt.price_adjustment) || 0,
                    calories_adjustment: defaultOpt.calories_adjustment !== null ? parseInt(defaultOpt.calories_adjustment) : null,
                    protein_adjustment: defaultOpt.protein_adjustment !== null ? parseFloat(defaultOpt.protein_adjustment) : null,
                    carbs_adjustment: defaultOpt.carbs_adjustment !== null ? parseFloat(defaultOpt.carbs_adjustment) : null,
                    fat_adjustment: defaultOpt.fat_adjustment !== null ? parseFloat(defaultOpt.fat_adjustment) : null,
                    sugar_adjustment: defaultOpt.sugar_adjustment !== null && defaultOpt.sugar_adjustment !== undefined ? parseFloat(defaultOpt.sugar_adjustment) : null,
                    caffeine_adjustment: defaultOpt.caffeine_adjustment !== null && defaultOpt.caffeine_adjustment !== undefined ? parseFloat(defaultOpt.caffeine_adjustment) : null,
                    quantity: 1
                });
            }
        });
    },

    /**
     * Render Left Panel: Food Image, Badges, Details, Ingredients, Allergens, Base Nutrition
     */
    renderLeftPanel(food) {
        // 1. Dietary & Feature Badges
        const badgeEl = document.getElementById('modalDietBadge');
        if (badgeEl) {
            const badges = [];
            if (food.protein !== null && parseFloat(food.protein) >= 25) {
                badges.push('HIGH PROTEIN');
            }
            const typeStr = (food.food_type || '').toLowerCase();
            if (typeStr === 'vegetarian' || typeStr === 'veg') {
                badges.push('VEGETARIAN');
            } else if (typeStr === 'vegan') {
                badges.push('VEGAN');
            } else if (typeStr === 'non_vegetarian' || typeStr === 'non-veg') {
                badges.push('NON-VEG');
            } else if (food.category_name) {
                badges.push(food.category_name.toUpperCase());
            }

            badgeEl.textContent = badges.length > 0 ? badges.join(' · ') : (food.category_name || 'HEALTHY BITE');
        }

        // 2. Real Food Image
        const imgEl = document.getElementById('modalFoodImg');
        if (imgEl) {
            let imgSrc = '/assets/images/foods/placeholder-dish.svg';
            if (food.image) {
                if (food.image.startsWith('http://') || food.image.startsWith('https://')) {
                    imgSrc = food.image;
                } else if (food.image.startsWith('/assets/')) {
                    imgSrc = food.image;
                } else if (food.image.startsWith('assets/')) {
                    imgSrc = '/' + food.image;
                } else {
                    imgSrc = '/assets/images/foods/' + food.image.replace(/^\//, '');
                }
            }
            imgEl.src = imgSrc;
            imgEl.alt = food.name;
            imgEl.onerror = () => { imgEl.src = '/assets/images/foods/placeholder-dish.svg'; };
        }

        // 3. Title & Description
        const titleEl = document.getElementById('modalFoodTitle');
        if (titleEl) titleEl.textContent = food.name;

        const descEl = document.getElementById('modalFoodDesc');
        if (descEl) descEl.textContent = food.description || 'Specially crafted with wholesome ingredients for high nutrition and exceptional flavor.';

        // 4. Ingredients & Allergens
        const ingEl = document.getElementById('modalFoodIngredients');
        const allEl = document.getElementById('modalFoodAllergens');
        const ingSec = document.getElementById('modalIngredientsSection');

        if (food.ingredients || food.allergens) {
            if (ingSec) ingSec.style.display = 'block';
            if (ingEl) {
                ingEl.textContent = food.ingredients || 'Fresh daily ingredients prepared transparently in our clean kitchen.';
                ingEl.style.display = 'block';
            }
            if (allEl) {
                if (food.allergens) {
                    allEl.textContent = food.allergens.startsWith('Contains') ? food.allergens : `Contains: ${food.allergens}`;
                    allEl.style.display = 'block';
                } else {
                    allEl.style.display = 'none';
                }
            }
        } else {
            if (ingSec) ingSec.style.display = 'none';
        }

        // 5. Base Nutrition Breakdown (6 Metrics)
        const calEl = document.getElementById('metricBaseCalories');
        const protEl = document.getElementById('metricBaseProtein');
        const carbsEl = document.getElementById('metricBaseCarbs');
        const fatEl = document.getElementById('metricBaseFat');
        const sugarEl = document.getElementById('metricBaseSugar');
        const caffCard = document.getElementById('cardBaseCaffeine');
        const caffEl = document.getElementById('metricBaseCaffeine');

        if (calEl) calEl.textContent = food.calories !== null ? `${Math.round(food.calories)} kcal` : '—';
        if (protEl) protEl.textContent = food.protein !== null ? `${parseFloat(food.protein)} g` : '—';
        if (carbsEl) carbsEl.textContent = food.carbs !== null ? `${parseFloat(food.carbs)} g` : '—';
        if (fatEl) fatEl.textContent = food.fat !== null ? `${parseFloat(food.fat)} g` : '—';
        if (sugarEl) sugarEl.textContent = food.sugar !== null && food.sugar !== undefined ? `${parseFloat(food.sugar)} g` : '—';

        // Check if item has caffeine or is in beverages category
        const catNameLower = (food.category_name || '').toLowerCase();
        const isBeverage = catNameLower.includes('beverage') || catNameLower.includes('drink');
        const hasCaffeine = food.caffeine !== null && food.caffeine !== undefined;

        if (caffCard && caffEl) {
            if (hasCaffeine && parseFloat(food.caffeine) > 0) {
                caffCard.style.display = 'block';
                caffEl.textContent = `${Math.round(parseFloat(food.caffeine))} mg`;
            } else if (isBeverage) {
                caffCard.style.display = 'block';
                caffEl.textContent = `0 mg`;
            } else {
                caffCard.style.display = 'none';
            }
        }

        const noteEl = document.getElementById('modalServingSizeNote');
        if (noteEl) {
            const serving = food.serving_size ? `Serving size ${food.serving_size}` : 'Standard portion';
            noteEl.textContent = `Nutritional values are approximate · ${serving}`;
        }
    },

    /**
     * Render Right Panel: Dynamic title, Variants, Customization groups, Options
     */
    renderRightPanel(food) {
        // Dynamic title
        const titleEl = document.getElementById('modalCustomTitle');
        if (titleEl) {
            const catName = (food.category_name || '').toLowerCase();
            const foodNameLower = food.name.toLowerCase();
            if (catName.includes('bowl') || foodNameLower.includes('bowl')) {
                titleEl.textContent = 'Build your bowl';
            } else if (foodNameLower.includes('pizza')) {
                titleEl.textContent = 'Customize your pizza';
            } else if (foodNameLower.includes('burger')) {
                titleEl.textContent = 'Build your burger';
            } else if (catName.includes('wrap') || foodNameLower.includes('wrap')) {
                titleEl.textContent = 'Customize your wrap';
            } else if (catName.includes('soup') || foodNameLower.includes('soup')) {
                titleEl.textContent = 'Customize your soup';
            } else if (catName.includes('beverage') || foodNameLower.includes('coffee') || foodNameLower.includes('smoothie')) {
                titleEl.textContent = 'Customize your drink';
            } else {
                titleEl.textContent = 'Customize your meal';
            }
        }

        // 1. Render Variants (Portion / Size)
        const variantsContainer = document.getElementById('modalVariantsContainer');
        variantsContainer.innerHTML = '';

        if (food.variants && food.variants.length > 0) {
            let variantTitle = 'Portion Size';
            const catNameLower = (food.category_name || '').toLowerCase();
            if (catNameLower.includes('beverage') || catNameLower.includes('drink')) {
                variantTitle = 'Cup / Bottle Size';
            } else if (catNameLower.includes('salad')) {
                variantTitle = 'Salad Portion';
            } else if (catNameLower.includes('soup')) {
                variantTitle = 'Bowl Size';
            } else if (catNameLower.includes('dessert')) {
                variantTitle = 'Serving Size';
            } else if (food.name.toLowerCase().includes('pizza')) {
                variantTitle = 'Pizza Size';
            }

            const sectionDiv = document.createElement('div');
            sectionDiv.className = 'custom-group-section';
            sectionDiv.innerHTML = `
                <div class="group-header-row">
                    <span class="group-header-title">${Utils.escapeHtml(variantTitle)}</span>
                    <span class="group-badge required">Required</span>
                </div>
                <div class="options-list" id="variantOptionsList"></div>
            `;
            variantsContainer.appendChild(sectionDiv);

            const listEl = sectionDiv.querySelector('#variantOptionsList');
            food.variants.forEach((v, index) => {
                const isSelected = State.selectedVariant ? Number(State.selectedVariant.id) === Number(v.id) : index === 0;
                const card = document.createElement('div');
                card.className = `option-card ${isSelected ? 'selected' : ''}`;
                
                const adjPrice = parseFloat(v.price_adjustment) || 0;
                const priceTagHtml = adjPrice > 0 
                    ? `<span class="option-price-tag">+₹${Math.round(adjPrice)}</span>` 
                    : `<span class="option-price-tag included">Included</span>`;

                let macroTagHtml = '';
                const vCaff = parseFloat(v.caffeine_adjustment) || 0;
                const vProt = parseFloat(v.protein_adjustment) || 0;
                const vCal = parseInt(v.calories_adjustment) || 0;

                if (vCaff > 0) {
                    macroTagHtml = `<span class="option-macro-tag" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff;">+${Math.round(vCaff)}mg caffeine</span>`;
                } else if (vProt > 0) {
                    macroTagHtml = `<span class="option-macro-tag">+${Math.round(vProt)}g protein</span>`;
                } else if (vCal > 0 && adjPrice > 0) {
                    macroTagHtml = `<span class="option-macro-tag">+${vCal} kcal</span>`;
                }

                card.innerHTML = `
                    <div class="option-left-wrap">
                        <span class="option-radio-dot"></span>
                        <span class="option-label-text">${Utils.escapeHtml(v.name)}</span>
                    </div>
                    <div class="option-right-wrap">
                        ${priceTagHtml}
                        ${macroTagHtml}
                    </div>
                `;

                card.addEventListener('click', () => {
                    State.selectedVariant = v;
                    listEl.querySelectorAll('.option-card').forEach(el => el.classList.remove('selected'));
                    card.classList.add('selected');
                    this.updateCalculations();
                });

                listEl.appendChild(card);
            });
        }

        // 2. Render Customization Groups
        const customContainer = document.getElementById('modalCustomizationsContainer');
        customContainer.innerHTML = '';

        const groups = food.customization_groups || {};
        const groupKeys = Object.keys(groups);

        groupKeys.forEach(groupName => {
            const options = groups[groupName];
            if (!options || options.length === 0) return;

            const isRequired = options.some(o => parseInt(o.is_required) === 1 || parseInt(o.min_quantity) >= 1);
            const maxQty = Math.max(...options.map(o => parseInt(o.max_quantity) || 1));
            const isSingleSelect = maxQty === 1;

            const groupDiv = document.createElement('div');
            groupDiv.className = 'custom-group-section';

            const badgeText = isRequired 
                ? 'Required' 
                : (maxQty > 1 ? `Optional · max ${maxQty}` : 'Optional');

            const badgeClass = isRequired ? 'required' : 'optional';

            groupDiv.innerHTML = `
                <div class="group-header-row">
                    <span class="group-header-title">${Utils.escapeHtml(groupName)}</span>
                    <span class="group-badge ${badgeClass}">${badgeText}</span>
                </div>
                <div class="options-list" data-group="${Utils.escapeHtml(groupName)}"></div>
            `;
            customContainer.appendChild(groupDiv);

            const listEl = groupDiv.querySelector('.options-list');

            options.forEach(c => {
                const isSelected = State.selectedCustomizations.some(x => Number(x.id) === Number(c.id));
                const card = document.createElement('div');
                card.className = `option-card ${isSelected ? 'selected' : ''}`;
                card.setAttribute('data-option-id', c.id);

                const adjPrice = parseFloat(c.price_adjustment) || 0;
                const priceTagHtml = adjPrice > 0 
                    ? `<span class="option-price-tag">+₹${Math.round(adjPrice)}</span>` 
                    : `<span class="option-price-tag included">Included</span>`;

                let macroTagHtml = '';
                const cCaff = parseFloat(c.caffeine_adjustment) || 0;
                const cProt = parseFloat(c.protein_adjustment) || 0;
                const cCal = parseInt(c.calories_adjustment) || 0;

                if (cCaff > 0) {
                    macroTagHtml = `<span class="option-macro-tag" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff;">+${Math.round(cCaff)}mg caffeine</span>`;
                } else if (cCaff < 0) {
                    macroTagHtml = `<span class="option-macro-tag" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">${Math.round(cCaff)}mg caffeine</span>`;
                } else if (cProt > 0) {
                    macroTagHtml = `<span class="option-macro-tag">+${Math.round(cProt)}g protein</span>`;
                } else if (cCal > 0 && adjPrice > 0) {
                    macroTagHtml = `<span class="option-macro-tag">+${cCal} kcal</span>`;
                }

                if (isSingleSelect) {
                    card.innerHTML = `
                        <div class="option-left-wrap">
                            <span class="option-radio-dot"></span>
                            <span class="option-label-text">${Utils.escapeHtml(c.name)}</span>
                        </div>
                        <div class="option-right-wrap">
                            ${priceTagHtml}
                            ${macroTagHtml}
                        </div>
                    `;

                    card.addEventListener('click', () => {
                        // Remove other selected options from this same group
                        State.selectedCustomizations = State.selectedCustomizations.filter(x => x.group_name !== groupName);
                        
                        // Select this option
                        State.selectedCustomizations.push({
                            id: c.id,
                            name: c.name,
                            group_name: groupName,
                            price_adjustment: parseFloat(c.price_adjustment) || 0,
                            calories_adjustment: c.calories_adjustment !== null ? parseInt(c.calories_adjustment) : null,
                            protein_adjustment: c.protein_adjustment !== null ? parseFloat(c.protein_adjustment) : null,
                            carbs_adjustment: c.carbs_adjustment !== null ? parseFloat(c.carbs_adjustment) : null,
                            fat_adjustment: c.fat_adjustment !== null ? parseFloat(c.fat_adjustment) : null,
                            sugar_adjustment: c.sugar_adjustment !== null && c.sugar_adjustment !== undefined ? parseFloat(c.sugar_adjustment) : null,
                            caffeine_adjustment: c.caffeine_adjustment !== null && c.caffeine_adjustment !== undefined ? parseFloat(c.caffeine_adjustment) : null,
                            quantity: 1
                        });

                        listEl.querySelectorAll('.option-card').forEach(el => el.classList.remove('selected'));
                        card.classList.add('selected');
                        this.updateCalculations();
                    });

                } else {
                    // Multi-select Checkbox
                    card.innerHTML = `
                        <div class="option-left-wrap">
                            <span class="option-check-box"><i class="bi bi-check-lg"></i></span>
                            <span class="option-label-text">${Utils.escapeHtml(c.name)}</span>
                        </div>
                        <div class="option-right-wrap">
                            ${priceTagHtml}
                            ${macroTagHtml}
                        </div>
                    `;

                    card.addEventListener('click', () => {
                        const existingIdx = State.selectedCustomizations.findIndex(x => Number(x.id) === Number(c.id));

                        if (existingIdx > -1) {
                            // Uncheck
                            State.selectedCustomizations.splice(existingIdx, 1);
                            card.classList.remove('selected');
                        } else {
                            // Check max limit for this group
                            const currentGroupSelections = State.selectedCustomizations.filter(x => x.group_name === groupName);
                            if (currentGroupSelections.length >= maxQty) {
                                if (window.showToast) {
                                    window.showToast(`Maximum ${maxQty} options allowed for ${groupName}`, 'warning', 'bi-info-circle-fill');
                                } else if (window.Utils) {
                                    Utils.showToast(`Maximum ${maxQty} options allowed for ${groupName}`);
                                }
                                return;
                            }

                            // Add selection
                            State.selectedCustomizations.push({
                                id: c.id,
                                name: c.name,
                                group_name: groupName,
                                price_adjustment: parseFloat(c.price_adjustment) || 0,
                                calories_adjustment: c.calories_adjustment !== null ? parseInt(c.calories_adjustment) : null,
                                protein_adjustment: c.protein_adjustment !== null ? parseFloat(c.protein_adjustment) : null,
                                carbs_adjustment: c.carbs_adjustment !== null ? parseFloat(c.carbs_adjustment) : null,
                                fat_adjustment: c.fat_adjustment !== null ? parseFloat(c.fat_adjustment) : null,
                                sugar_adjustment: c.sugar_adjustment !== null && c.sugar_adjustment !== undefined ? parseFloat(c.sugar_adjustment) : null,
                                caffeine_adjustment: c.caffeine_adjustment !== null && c.caffeine_adjustment !== undefined ? parseFloat(c.caffeine_adjustment) : null,
                                quantity: 1
                            });
                            card.classList.add('selected');
                        }

                        this.updateCalculations();
                    });
                }

                listEl.appendChild(card);
            });
        });
    },

    /**
     * Validate that all required customization groups have at least one selection
     */
    validateRequiredSelections() {
        if (!State.selectedFood) return true;

        const groups = State.selectedFood.customization_groups || {};
        for (const groupName of Object.keys(groups)) {
            const options = groups[groupName];
            const isRequired = options.some(o => parseInt(o.is_required) === 1 || parseInt(o.min_quantity) >= 1);
            if (isRequired) {
                const hasSelected = State.selectedCustomizations.some(x => x.group_name === groupName);
                if (!hasSelected) {
                    return false;
                }
            }
        }
        return true;
    },

    /**
     * Recalculate and update the Bottom Summary Panel (Nutrition cards, Delta pills, Total price, Add to Cart)
     */
    updateCalculations() {
        if (!State.selectedFood) return;

        const food = State.selectedFood;
        const variant = State.selectedVariant;
        const customizations = State.selectedCustomizations || [];
        const quantity = Math.max(1, State.modalQuantity || 1);

        // 1. Calculate Unit Price & Line Total
        const unitPrice = Pricing.calculateItemPrice(food.base_price, variant, customizations);
        const lineTotal = Pricing.calculateLineTotal(unitPrice, quantity);

        // Update Stepper Display
        const qtyEl = document.getElementById('modalQtyDisplay');
        if (qtyEl) qtyEl.textContent = quantity;

        // Update Summary Price & Header Label
        const priceEl = document.getElementById('modalSummaryPrice');
        if (priceEl) priceEl.textContent = `₹${Math.round(lineTotal)}`;

        const summaryHeaderEl = document.getElementById('summaryHeaderLabel');
        const catNameLower = (food.category_name || '').toLowerCase();
        const isBeverage = catNameLower.includes('beverage') || catNameLower.includes('drink');
        
        if (summaryHeaderEl) {
            const itemType = isBeverage ? 'drink' : (catNameLower.includes('bowl') ? 'bowl' : (catNameLower.includes('salad') ? 'salad' : 'item'));
            summaryHeaderEl.textContent = `Updated nutrition (for ${quantity} ${quantity > 1 ? itemType + 's' : itemType})`;
        }

        // 2. Calculate Live Nutrition
        const itemNutrition = Nutrition.calculateItemNutrition(food, variant, customizations);
        const scaledNutrition = Nutrition.scaleForQuantity(itemNutrition, quantity);

        // Base single item values for delta calculation
        const baseCalories = food.calories !== null ? parseInt(food.calories) * quantity : null;
        const baseProtein = food.protein !== null ? parseFloat(food.protein) * quantity : null;
        const baseCarbs = food.carbs !== null ? parseFloat(food.carbs) * quantity : null;
        const baseFat = food.fat !== null ? parseFloat(food.fat) * quantity : null;
        const baseSugar = food.sugar !== null && food.sugar !== undefined ? parseFloat(food.sugar) * quantity : null;
        const baseCaffeine = (food.caffeine !== null && food.caffeine !== undefined) ? parseFloat(food.caffeine) * quantity : null;

        // Render Live Values & Delta Badges (6 Metrics)
        this.renderMetricBox('Calories', scaledNutrition.calories, baseCalories, 'kcal');
        this.renderMetricBox('Protein', scaledNutrition.protein, baseProtein, 'g');
        this.renderMetricBox('Carbs', scaledNutrition.carbs, baseCarbs, 'g');
        this.renderMetricBox('Fat', scaledNutrition.fat, baseFat, 'g');
        this.renderMetricBox('Sugar', scaledNutrition.sugar, baseSugar, 'g');

        const boxCaff = document.getElementById('boxLiveCaffeine');
        const hasCaffeine = food.caffeine !== null && food.caffeine !== undefined;

        if (boxCaff) {
            if ((hasCaffeine && parseFloat(food.caffeine) > 0) || (scaledNutrition.caffeine !== null && scaledNutrition.caffeine > 0) || isBeverage) {
                boxCaff.style.display = 'flex';
                this.renderMetricBox('Caffeine', scaledNutrition.caffeine !== null ? Math.round(scaledNutrition.caffeine) : 0, baseCaffeine, 'mg');
            } else {
                boxCaff.style.display = 'none';
            }
        }

        const liveQtyLabel = document.getElementById('modalLiveQtyLabel');
        if (liveQtyLabel) {
            const itemWord = isBeverage ? (quantity > 1 ? 'drinks' : 'drink') : (quantity > 1 ? 'items' : 'item');
            liveQtyLabel.textContent = `${quantity} ${itemWord} · Live Per Order Totals`;
        }

        // 3. Validation & Add to Cart Button state
        const isValid = this.validateRequiredSelections();
        const addBtn = document.getElementById('modalAddToCartBtn');
        const addBtnText = document.getElementById('modalAddToCartText');

        if (addBtn && addBtnText) {
            if (isValid) {
                addBtn.disabled = false;
                if (this.editingCartIndex !== null) {
                    addBtnText.textContent = `Update in Cart · ₹${Math.round(lineTotal)}`;
                } else {
                    addBtnText.textContent = `Add to cart · ₹${Math.round(lineTotal)}`;
                }
            } else {
                addBtn.disabled = true;
                addBtnText.textContent = 'Please make required selections';
            }
        }
    },

    /**
     * Render a single live metric card with optional green delta pill
     */
    renderMetricBox(metricName, liveVal, baseVal, unit) {
        const valEl = document.getElementById(`metricLive${metricName}`);
        const deltaEl = document.getElementById(`delta${metricName}`);

        if (valEl) {
            valEl.textContent = liveVal !== null ? `${liveVal} ${unit}` : 'Not available';
        }

        if (deltaEl) {
            if (liveVal !== null && baseVal !== null) {
                const diff = Math.round((liveVal - baseVal) * 10) / 10;
                if (diff > 0) {
                    deltaEl.textContent = `+${diff}`;
                    deltaEl.style.display = 'inline-block';
                } else if (diff < 0) {
                    deltaEl.textContent = `${diff}`;
                    deltaEl.style.display = 'inline-block';
                } else {
                    deltaEl.style.display = 'none';
                }
            } else {
                deltaEl.style.display = 'none';
            }
        }
    },

    /**
     * Add or Update customized item in Cart using existing Cart service
     */
    addToCart() {
        if (!State.selectedFood) return;

        if (!this.validateRequiredSelections()) {
            if (window.showToast) {
                window.showToast('Please select all required options', 'warning');
            } else if (window.Utils) {
                Utils.showToast('Please select all required options');
            }
            return;
        }

        const food = State.selectedFood;
        const variant = State.selectedVariant;
        const customizations = [...State.selectedCustomizations];
        const quantity = Math.max(1, State.modalQuantity || 1);

        const instructionsInput = document.getElementById('modalSpecialInstructions');
        const specialInstructions = instructionsInput ? instructionsInput.value.trim() : '';

        const unitPrice = Pricing.calculateItemPrice(food.base_price, variant, customizations);
        const lineTotal = Pricing.calculateLineTotal(unitPrice, quantity);
        const itemNutrition = Nutrition.calculateItemNutrition(food, variant, customizations);

        const itemData = {
            restaurant_id: (State.restaurant && State.restaurant.id) ? State.restaurant.id : 1,
            branch_id: (State.branch && State.branch.id) ? State.branch.id : 1,
            food_id: food.id,
            food_name: food.name,
            food_image: food.image,
            image: food.image,
            base_price: food.base_price,
            variant_id: variant ? variant.id : null,
            variant_name: variant ? variant.name : null,
            variant_price: variant ? variant.price_adjustment : 0,
            customizations: customizations.map(c => ({
                id: c.id,
                name: c.name,
                price_adjustment: c.price_adjustment,
                sugar_adjustment: c.sugar_adjustment,
                caffeine_adjustment: c.caffeine_adjustment,
                quantity: c.quantity || 1
            })),
            special_instructions: specialInstructions,
            quantity: quantity,
            unit_price: unitPrice,
            line_total: lineTotal,
            nutrition: itemNutrition
        };

        if (this.editingCartIndex !== null && State.cart && State.cart[this.editingCartIndex]) {
            Cart.updateItem(this.editingCartIndex, itemData);
            this.editingCartIndex = null;
            this.close();
            if (window.Cart) {
                Cart.open();
            }
            const toastMsg = `Updated ${food.name} in cart!`;
            if (window.showToast) {
                window.showToast(toastMsg, 'success', 'bi-check-circle-fill');
            } else if (window.Utils) {
                Utils.showToast(toastMsg);
            }
        } else {
            Cart.addItem(itemData);
            this.close();
            const toastMsg = `${food.name} added to cart!`;
            if (window.showToast) {
                window.showToast(toastMsg, 'success', 'bi-check-circle-fill');
            } else if (window.Utils) {
                Utils.showToast(toastMsg);
            }
        }
    }
};

window.FoodDetails = FoodDetails;
