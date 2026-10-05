<!-- ============================================================
     HEALTHY BITE — Professional Food Customization Modal
     Reference Design: 2-Panel Desktop + Mobile Sheet
     ============================================================ -->
<div class="modal-backdrop" id="foodDetailModal" role="dialog" aria-modal="true" aria-labelledby="modalFoodTitle">
    <div class="food-custom-modal" role="document">
        
        <!-- Top Close Button for Mobile & Global Accessibility -->
        <button type="button" class="modal-close-icon-btn" id="modalCloseBtn" aria-label="Close dialog">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="modal-main-grid">
            <!-- ====================================================
                 LEFT PANEL: Food Image & Meal Information
                 ==================================================== -->
            <div class="custom-left-panel">
                <!-- Top Badge & Favorite Button Row -->
                <div class="left-panel-header">
                    <div class="dietary-badges-row" id="modalDietaryBadges">
                        <span class="diet-pill-badge" id="modalDietBadge">HIGH PROTEIN · VEGETARIAN</span>
                    </div>
                    <button type="button" class="modal-fav-btn" id="modalFavBtn" aria-label="Add to favorites" onclick="this.classList.toggle('active')">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>

                <!-- Real Food Image -->
                <div class="modal-food-media">
                    <img 
                        id="modalFoodImg" 
                        class="modal-food-img" 
                        src="" 
                        alt="Selected Meal" 
                        loading="eager"
                        onerror="this.src='/assets/images/foods/placeholder-dish.svg'"
                    >
                </div>

                <!-- Food Title & Description -->
                <h2 class="modal-food-title" id="modalFoodTitle"></h2>
                <p class="modal-food-desc" id="modalFoodDesc"></p>

                <!-- Ingredients & Allergens Block -->
                <div class="modal-ingredients-section" id="modalIngredientsSection">
                    <div class="section-subheading">
                        <svg class="leaf-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                        </svg>
                        <span>Ingredients &amp; allergens</span>
                    </div>
                    <p class="ingredients-content" id="modalFoodIngredients"></p>
                    <p class="allergens-content" id="modalFoodAllergens" style="display:none;"></p>
                </div>

                <!-- Base Nutrition Cards (5 Metrics) -->
                <div class="base-nutrition-section">
                    <div class="base-nutrition-grid">
                        <div class="base-metric-card">
                            <span class="base-metric-val" id="metricBaseCalories">—</span>
                            <span class="base-metric-lbl">Calories</span>
                        </div>
                        <div class="base-metric-card">
                            <span class="base-metric-val" id="metricBaseProtein">—</span>
                            <span class="base-metric-lbl">Protein</span>
                        </div>
                        <div class="base-metric-card">
                            <span class="base-metric-val" id="metricBaseCarbs">—</span>
                            <span class="base-metric-lbl">Carbs</span>
                        </div>
                        <div class="base-metric-card" id="cardBaseFat">
                            <span class="base-metric-val" id="metricBaseFat">—</span>
                            <span class="base-metric-lbl">Fat</span>
                        </div>
                        <div class="base-metric-card" id="cardBaseSugar">
                            <span class="base-metric-val" id="metricBaseSugar">—</span>
                            <span class="base-metric-lbl">Sugar</span>
                        </div>
                        <div class="base-metric-card" id="cardBaseCaffeine" style="display:none;">
                            <span class="base-metric-val" id="metricBaseCaffeine">—</span>
                            <span class="base-metric-lbl">Caffeine</span>
                        </div>
                    </div>
                    <div class="nutrition-footnote" id="modalServingSizeNote">
                        Nutritional values are approximate · Serving size 420 g
                    </div>
                </div>
            </div>

            <!-- ====================================================
                 RIGHT PANEL: Customization Options & Controls
                 ==================================================== -->
            <div class="custom-right-panel">
                <div class="right-panel-header">
                    <div>
                        <h3 class="custom-header-title" id="modalCustomTitle">Build your bowl</h3>
                        <p class="custom-header-subtitle">Required choices are marked · Nutrition updates with selections</p>
                    </div>
                </div>

                <!-- Scrollable Customization Controls -->
                <div class="custom-options-scroll" id="customOptionsScroll">
                    
                    <!-- Variants (Portion / Size) -->
                    <div id="modalVariantsContainer"></div>

                    <!-- Customizations Grid (Base, Protein, Sauce, Toppings, etc.) -->
                    <div class="customizations-grid" id="modalCustomizationsContainer"></div>

                    <!-- Special Instructions -->
                    <div class="special-instructions-section">
                        <label for="modalSpecialInstructions" class="special-instructions-label">Special instructions</label>
                        <textarea 
                            id="modalSpecialInstructions" 
                            class="special-instructions-textarea" 
                            rows="2" 
                            placeholder="E.g. less spice, extra sauce, no onion..."
                            maxlength="250"
                        ></textarea>
                    </div>

                    <!-- Quantity Stepper Row -->
                    <div class="quantity-row">
                        <span class="quantity-label">Quantity</span>
                        <div class="quantity-stepper">
                            <button type="button" class="qty-btn" id="modalQtyDec" aria-label="Decrease quantity">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="qty-display" id="modalQtyDisplay">1</span>
                            <button type="button" class="qty-btn" id="modalQtyInc" aria-label="Increase quantity">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ====================================================
                     BOTTOM SUMMARY: Dark Green Live Nutrition & Add to Cart
                     ==================================================== -->
                <div class="custom-bottom-summary">
                    <div class="summary-top-row">
                        <span class="summary-title" id="summaryHeaderLabel">Updated nutrition (for 1 bowl)</span>
                        <span class="summary-total-price" id="modalSummaryPrice">₹249</span>
                    </div>

                    <!-- 4 Live Nutrition Cards with Delta Badges -->
                    <div class="live-metrics-grid">
                        <div class="live-metric-box">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveCalories">520 kcal</span>
                                <span class="delta-badge" id="deltaCalories" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Calories</span>
                        </div>

                        <div class="live-metric-box">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveProtein">38 g</span>
                                <span class="delta-badge" id="deltaProtein" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Protein</span>
                        </div>

                        <div class="live-metric-box">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveCarbs">48 g</span>
                                <span class="delta-badge" id="deltaCarbs" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Carbs</span>
                        </div>

                        <div class="live-metric-box">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveFat">18 g</span>
                                <span class="delta-badge" id="deltaFat" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Fat</span>
                        </div>

                        <div class="live-metric-box">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveSugar">0 g</span>
                                <span class="delta-badge" id="deltaSugar" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Sugar</span>
                        </div>

                        <div class="live-metric-box" id="boxLiveCaffeine" style="display:none;">
                            <div class="live-metric-header">
                                <span class="live-val" id="metricLiveCaffeine">0 mg</span>
                                <span class="delta-badge" id="deltaCaffeine" style="display:none;">+0</span>
                            </div>
                            <span class="live-lbl">Caffeine</span>
                        </div>
                    </div>

                    <div class="summary-footnote" id="modalLiveScaleNote" style="font-size: 11px; color: rgba(255,255,255,0.75); margin-bottom: 10px; text-align: center;">
                        <i class="bi bi-info-circle"></i> Nutrition &amp; caffeine scaled for <span id="modalLiveQtyLabel" style="font-weight:700; color:#fff;">1 item</span>
                    </div>

                    <!-- Full-Width Add to Cart Button -->
                    <button type="button" class="btn-modal-add-cart" id="modalAddToCartBtn">
                        <span id="modalAddToCartText">Add to cart · ₹249</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
