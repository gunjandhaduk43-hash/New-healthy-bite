<?php if (!empty($flashSuccess)): ?>
    <div class="alert-banner success" style="margin-bottom: 20px; padding: 12px 16px; background: #EAF5EA; color: #166534; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= e($flashSuccess) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($flashError)): ?>
    <div class="alert-banner error" style="margin-bottom: 20px; padding: 12px 16px; background: #FEE2E2; color: #DC2626; border-radius: 12px; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 500;">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span><?= e($flashError) ?></span>
    </div>
<?php endif; ?>

<!-- Page Header Row -->
<div class="page-header-row">
    <div>
        <h1 class="page-greeting">Menu & Foods</h1>
        <p class="page-subtitle">Configure menu catalog, dietary tags, dynamic macros, and stock availability.</p>
    </div>
    <div class="header-action-group">
        <button type="button" onclick="openHbModal('categoriesModal')" class="btn-hb btn-hb-secondary">
            <i class="bi bi-folder2"></i> Categories (<?= count($categories) ?>)
        </button>
        <a href="<?= url('/owner/live-menu') ?>" class="btn-hb btn-hb-outline-green" style="text-decoration:none;">
            <i class="bi bi-eye"></i> View Live Customer Menu
        </a>
        <button type="button" onclick="openAddFoodModal()" class="btn-hb btn-hb-primary">
            <i class="bi bi-plus-lg"></i> Add New Dish
        </button>
    </div>
</div>

<!-- Filter Bar Card -->
<div class="filter-bar-card">
    <div class="search-input-wrap">
        <i class="bi bi-search"></i>
        <input type="text" class="search-input" placeholder="Search dishes by name or ingredient..." data-table-search="#foods-table">
    </div>

    <select class="filter-select" data-table-filter="#foods-table" data-col-index="1">
        <option value="all">All Categories (<?= count($categories) ?>)</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= strtolower(e($c['name'])) ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <select class="filter-select" data-table-filter="#foods-table" data-col-index="5">
        <option value="all">Availability: All</option>
        <option value="available">Available in Stock</option>
        <option value="unavailable">Sold Out / Unavailable</option>
    </select>
</div>

<!-- Menu & Foods Table -->
<div class="table-container-card" style="overflow-x: auto;">
    <table class="dash-table" id="foods-table" style="min-width: 820px;">
        <thead>
            <tr>
                <th>Dish Details</th>
                <th>Category</th>
                <th>Base Price</th>
                <th>Calories</th>
                <th>Protein</th>
                <th>Sugar</th>
                <th>Stock Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($foods)): ?>
                <?php foreach ($foods as $f): ?>
                    <tr>
                        <td style="font-weight:600; color:#111827;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <img src="<?= e(foodImageUrl($f['image'] ?? '')) ?>" alt="<?= e($f['name']) ?>" onerror="this.src='<?= asset('images/foods/placeholder-dish.svg') ?>'" style="width:44px; height:44px; border-radius:8px; object-fit:cover; flex-shrink:0; background:#f1f5f9; border:1px solid #e2e8f0;">
                                <div>
                                    <div style="font-size:14px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                                        <?php if (($f['food_type'] ?? '') === 'vegetarian'): ?>
                                            <span class="dietary-badge veg" style="padding:1px 6px; font-size:10px;">VEG</span>
                                        <?php elseif (($f['food_type'] ?? '') === 'vegan'): ?>
                                            <span class="dietary-badge vegan" style="padding:1px 6px; font-size:10px;">VEGAN</span>
                                        <?php else: ?>
                                            <span class="dietary-badge non-veg" style="padding:1px 6px; font-size:10px;">NON-VEG</span>
                                        <?php endif; ?>
                                        <span><?= e($f['name']) ?></span>
                                    </div>
                                    <?php if (!empty($f['allergens'])): ?>
                                        <div style="font-size:11px; color:#94a3b8; margin-top:2px;">
                                            Allergens: <?= e($f['allergens']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:13px; font-weight:600; color:#475569; background:#f1f5f9; padding:3px 10px; border-radius:6px;">
                                <?= e($f['category_name'] ?? 'Mains') ?>
                            </span>
                        </td>
                        <td style="font-weight:700; color:#0f172a; font-size:14px;">₹<?= number_format((float)$f['base_price'], 0) ?></td>
                        <td>
                            <?php if ($f['calories'] !== null): ?>
                                <span style="font-weight:600; color:#0369a1; font-size:13px;"><?= e((string)$f['calories']) ?> kcal</span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($f['protein'] !== null): ?>
                                <span style="font-weight:600; color:#166534; font-size:13px;"><?= e((string)$f['protein']) ?>g</span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($f['sugar'] !== null): ?>
                                <span style="font-weight:600; color:#b45309; font-size:13px;"><?= e((string)$f['sugar']) ?>g</span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?= url('/owner/menu/' . $f['id'] . '/toggle') ?>" method="POST" style="display:inline;">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;" title="Click to toggle availability">
                                    <span class="status-pill <?= !empty($f['is_available']) ? 'available' : 'suspended' ?>" style="cursor:pointer;">
                                        <?= !empty($f['is_available']) ? '● In Stock' : '○ Unavailable' ?>
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="table-action-links">
                                <button type="button" onclick='openEditFoodModal(<?= htmlspecialchars(json_encode($f), ENT_QUOTES, "UTF-8") ?>)' style="color:var(--brand-primary, #166534); font-weight:700; background:none; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <span style="color:#cbd5e1;">•</span>
                                <form action="<?= url('/owner/menu/' . $f['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove <?= addslashes(e($f['name'])) ?> from the menu?');">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" style="background:none; border:none; padding:0; color:#dc2626; cursor:pointer; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:48px; color:#6B7280;">No menu dishes configured yet. Click "+ Add New Dish" to register dishes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add / Edit Food Modal with Clean Tabbed Layout -->
<div id="add-food-modal" class="hb-modal-overlay hidden">
    <div class="hb-modal-dialog" style="max-width: 640px;">
        <div class="hb-modal-header">
            <h2 class="hb-modal-title" id="foodModalTitle">Add New Dish</h2>
            <button type="button" class="hb-modal-close" onclick="closeHbModal('add-food-modal')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Visual Form Tabs -->
        <div style="display:flex; border-bottom:1px solid #e2e8f0; margin-bottom:18px; gap:8px;">
            <button type="button" id="tabBtn-general" class="btn-hb btn-hb-sm active" onclick="switchFoodModalTab('general')" style="border-radius:6px 6px 0 0; background:#f1f5f9; color:#0f172a; font-weight:700; border-bottom:2px solid var(--brand-primary, #166534);">
                1. General Info
            </button>
            <button type="button" id="tabBtn-dietary" class="btn-hb btn-hb-sm" onclick="switchFoodModalTab('dietary')" style="border-radius:6px 6px 0 0; background:transparent; color:#64748b; font-weight:600;">
                2. Dietary & Allergens
            </button>
            <button type="button" id="tabBtn-macros" class="btn-hb btn-hb-sm" onclick="switchFoodModalTab('macros')" style="border-radius:6px 6px 0 0; background:transparent; color:#64748b; font-weight:600;">
                3. Nutrition & Macros
            </button>
        </div>

        <form id="foodModalForm" action="<?= url('/owner/menu/create') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>

            <!-- Tab 1: General Info -->
            <div id="tabContent-general">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="form-label">Dish Name *</label>
                        <input type="text" id="food_name" name="name" class="form-control-hb" placeholder="e.g. Avocado Quinoa Power Bowl" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Category *</label>
                        <select id="food_cat" name="category_id" class="form-control-hb" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="form-label">Base Price (₹) *</label>
                        <input type="number" step="0.01" id="food_price" name="base_price" class="form-control-hb" placeholder="245" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Dish Image URL</label>
                        <input type="text" id="food_image" name="image" class="form-control-hb" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>

                <div class="form-field" style="margin-bottom:14px;">
                    <label class="form-label">Description</label>
                    <textarea id="food_desc" name="description" class="form-control-hb" rows="2" placeholder="Wholesome organic greens, ripe hass avocado, toasted seeds..."></textarea>
                </div>
            </div>

            <!-- Tab 2: Dietary & Allergens -->
            <div id="tabContent-dietary" style="display:none;">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="form-label">Dietary Classification *</label>
                        <select id="food_diet" name="food_type" class="form-control-hb">
                            <option value="vegetarian">Vegetarian (Veg)</option>
                            <option value="non_vegetarian">Non-Vegetarian</option>
                            <option value="vegan">100% Vegan</option>
                            <option value="jain">Jain Friendly</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label class="form-label">Allergens</label>
                        <input type="text" id="food_allergens" name="allergens" class="form-control-hb" placeholder="e.g. Tree Nuts, Dairy, Gluten">
                    </div>
                </div>

                <div class="form-field" style="margin-bottom:14px;">
                    <label class="form-label">Ingredients</label>
                    <input type="text" id="food_ingredients" name="ingredients" class="form-control-hb" placeholder="Quinoa, Avocado, Organic Spinach, Olive Oil, Chia Seeds">
                </div>
            </div>

            <!-- Tab 3: Nutrition & Macros -->
            <div id="tabContent-macros" style="display:none;">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="form-label">Calories (kcal)</label>
                        <input type="number" id="food_calories" name="calories" class="form-control-hb" placeholder="380">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Protein (g)</label>
                        <input type="number" step="0.1" id="food_protein" name="protein" class="form-control-hb" placeholder="18.5">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="form-label">Carbs (g)</label>
                        <input type="number" step="0.1" id="food_carbs" name="carbs" class="form-control-hb" placeholder="42.0">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Fat (g)</label>
                        <input type="number" step="0.1" id="food_fat" name="fat" class="form-control-hb" placeholder="12.0">
                    </div>
                </div>

                <div class="form-field" style="margin-top:10px;">
                    <label class="form-label">Sugar (g)</label>
                    <input type="number" step="0.1" min="0" id="food_sugar" name="sugar" class="form-control-hb" placeholder="4.5">
                    <span style="font-size:11px; color:#64748b;">Numeric value in grams. Leave empty if unmeasured (NULL).</span>
                </div>
            </div>

            <div class="hb-modal-actions">
                <button type="button" onclick="closeHbModal('add-food-modal')" class="btn-hb btn-hb-secondary">
                    Cancel
                </button>
                <button type="submit" id="foodSubmitBtn" class="btn-hb btn-hb-primary">
                    Save Dish
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Category Management -->
<div class="hb-modal-overlay hidden" id="categoriesModal">
    <div class="hb-modal-dialog" style="max-width: 580px;">
        <div class="hb-modal-header">
            <h2 class="hb-modal-title">Menu Categories</h2>
            <button type="button" class="hb-modal-close" onclick="closeHbModal('categoriesModal')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div style="padding: 20px;">
            <h3 style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Active Categories</h3>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #475569;">Name</th>
                            <th style="padding: 8px 12px; text-align: left; font-weight: 600; color: #475569;">Slug</th>
                            <th style="padding: 8px 12px; text-align: right; font-weight: 600; color: #475569;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 12px; font-weight: 600; color: #0f172a;"><?= e($cat['name']) ?></td>
                                <td style="padding: 8px 12px; color: #64748b; font-family: monospace;"><?= e($cat['slug']) ?></td>
                                <td style="padding: 8px 12px; text-align: right;">
                                    <form action="<?= url('/owner/categories/' . (int)$cat['id'] . '/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this category?');">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="btn-hb btn-hb-sm" style="color:#dc2626; background:none; border:none; padding:2px 6px; cursor:pointer;" title="Remove Category">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h3 style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Add New Category</h3>
            <form action="<?= url('/owner/categories/create') ?>" method="POST">
                <?= \App\Core\Csrf::field() ?>
                <div class="form-field" style="margin-bottom: 12px;">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control-hb" placeholder="e.g. Smoothies, Keto Bowls, Desserts" required>
                </div>
                <div class="form-field" style="margin-bottom: 16px;">
                    <label class="form-label">Description (Optional)</label>
                    <input type="text" name="description" class="form-control-hb" placeholder="Short description of items in this category">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="closeHbModal('categoriesModal')" class="btn-hb btn-hb-secondary">Close</button>
                    <button type="submit" class="btn-hb btn-hb-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchFoodModalTab(tabKey) {
    ['general', 'dietary', 'macros'].forEach(key => {
        const content = document.getElementById('tabContent-' + key);
        const btn = document.getElementById('tabBtn-' + key);
        if (content && btn) {
            if (key === tabKey) {
                content.style.display = 'block';
                btn.style.background = '#f1f5f9';
                btn.style.color = '#0f172a';
                btn.style.borderBottom = '2px solid var(--brand-primary, #166534)';
            } else {
                content.style.display = 'none';
                btn.style.background = 'transparent';
                btn.style.color = '#64748b';
                btn.style.borderBottom = 'none';
            }
        }
    });
}

function openAddFoodModal() {
    const form = document.getElementById('foodModalForm');
    form.action = '<?= url('/owner/menu/create') ?>';
    document.getElementById('foodModalTitle').textContent = 'Add New Dish';
    document.getElementById('foodSubmitBtn').textContent = 'Save Dish';
    
    document.getElementById('food_name').value = '';
    document.getElementById('food_desc').value = '';
    document.getElementById('food_price').value = '';
    document.getElementById('food_image').value = '';
    document.getElementById('food_diet').value = 'vegetarian';
    document.getElementById('food_allergens').value = '';
    document.getElementById('food_calories').value = '';
    document.getElementById('food_protein').value = '';
    document.getElementById('food_carbs').value = '';
    document.getElementById('food_fat').value = '';
    document.getElementById('food_sugar').value = '';
    document.getElementById('food_ingredients').value = '';
    
    switchFoodModalTab('general');
    openHbModal('add-food-modal');
}

function openEditFoodModal(food) {
    const form = document.getElementById('foodModalForm');
    form.action = '<?= url('/owner/menu/') ?>' + food.id + '/edit';
    document.getElementById('foodModalTitle').textContent = 'Edit Dish — ' + food.name;
    document.getElementById('foodSubmitBtn').textContent = 'Update Dish';
    
    document.getElementById('food_name').value = food.name || '';
    document.getElementById('food_desc').value = food.description || '';
    if (food.category_id) {
        document.getElementById('food_cat').value = food.category_id;
    }
    document.getElementById('food_price').value = food.base_price || '';
    document.getElementById('food_image').value = food.image || '';
    if (food.food_type) {
        document.getElementById('food_diet').value = food.food_type;
    }
    document.getElementById('food_allergens').value = food.allergens || '';
    document.getElementById('food_calories').value = food.calories || '';
    document.getElementById('food_protein').value = food.protein || '';
    document.getElementById('food_carbs').value = food.carbs || '';
    document.getElementById('food_fat').value = food.fat || '';
    document.getElementById('food_sugar').value = (food.sugar !== null && food.sugar !== undefined) ? food.sugar : '';
    document.getElementById('food_ingredients').value = food.ingredients || '';
    
    switchFoodModalTab('general');
    openHbModal('add-food-modal');
}
</script>
