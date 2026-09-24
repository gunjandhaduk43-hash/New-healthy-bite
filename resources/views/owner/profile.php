<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Restaurant Profile</h1>
        <p class="hb-page-subtitle">Manage your restaurant identity, brand info, and location details.</p>
    </div>
</div>

<div style="max-width: 800px;">
    <div class="hb-table-card-container" style="padding: 28px;">
        <form action="<?= url('/owner/profile/update') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>

            <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid var(--color-gray-200);">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #EAF5EA; color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; border: 2px solid var(--color-gray-200);">
                    <?= strtoupper(substr($restaurant['name'] ?? 'G', 0, 1)) ?>
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 4px 0;">
                        <?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?>
                    </h3>
                    <div style="display: flex; gap: 8px;">
                        <span class="hb-badge hb-badge-success"><?= ucfirst(e($restaurant['status'] ?? 'approved')) ?></span>
                        <span class="hb-badge" style="background:#F3F4F6; color:var(--color-gray-700);">Multi-branch</span>
                    </div>
                </div>
            </div>

            <div class="hb-form-row">
                <div class="hb-form-group">
                    <label class="hb-form-label">Restaurant Name *</label>
                    <input type="text" name="name" class="hb-form-control" value="<?= e($restaurant['name'] ?? '') ?>" required>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">City *</label>
                    <input type="text" name="city" class="hb-form-control" value="<?= e($restaurant['city'] ?? 'Bengaluru') ?>" required>
                </div>
            </div>

            <div class="hb-form-row">
                <div class="hb-form-group">
                    <label class="hb-form-label">Contact Phone</label>
                    <input type="text" name="phone" class="hb-form-control" value="<?= e($restaurant['phone'] ?? '+91 98765 43210') ?>">
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Official Email</label>
                    <input type="email" name="email" class="hb-form-control" value="<?= e($restaurant['email'] ?? 'aarav@greenhouse.in') ?>">
                </div>
            </div>

            <div class="hb-form-group">
                <label class="hb-form-label">Full Street Address</label>
                <input type="text" name="address" class="hb-form-control" value="<?= e($restaurant['address'] ?? '100 Feet Road, Indiranagar') ?>">
            </div>

            <div class="hb-form-group">
                <label class="hb-form-label">About / Description</label>
                <textarea name="description" class="hb-form-control" rows="3" placeholder="Tell guests about your wholesome meals and culinary mission..."><?= e($restaurant['description'] ?? 'Wholesome organic dining with transparent nutrition and macro tracking.') ?></textarea>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                <button type="submit" class="hb-btn hb-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
