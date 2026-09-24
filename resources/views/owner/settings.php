<!-- Header Section -->
<div class="hb-header-row">
    <div>
        <h1 class="hb-page-title">Settings</h1>
        <p class="hb-page-subtitle">Configure notification preferences, operational alerts, and account security.</p>
    </div>
</div>

<div style="max-width: 800px; display: flex; flex-direction: column; gap: 20px;">
    <!-- Order Settings Card -->
    <div class="hb-table-card-container" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 16px 0;">
            Operational Preferences
        </h3>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-gray-900);">Automatic Kitchen Sound Alerts</div>
                    <div style="font-size: 12px; color: var(--color-gray-500);">Chime immediately when a new contactless order is placed</div>
                </div>
                <input type="checkbox" checked style="accent-color: var(--color-primary); width: 18px; height: 18px;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-gray-200); padding-top: 16px;">
                <div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-gray-900);">Live Order Auto-Accept</div>
                    <div style="font-size: 12px; color: var(--color-gray-500);">Automatically advance orders to Accepted status during rush hours</div>
                </div>
                <input type="checkbox" style="accent-color: var(--color-primary); width: 18px; height: 18px;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-gray-200); padding-top: 16px;">
                <div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-gray-900);">Customer Nutrition Breakdown Display</div>
                    <div style="font-size: 12px; color: var(--color-gray-500);">Show macro percentages on printed receipts and live menu cards</div>
                </div>
                <input type="checkbox" checked style="accent-color: var(--color-primary); width: 18px; height: 18px;">
            </div>
        </div>
    </div>

    <!-- Security & Password -->
    <div class="hb-table-card-container" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-gray-900); margin: 0 0 16px 0;">
            Account Security
        </h3>
        <form action="<?= url('/owner/settings/password') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>
            <div class="hb-form-group">
                <label class="hb-form-label">Current Password *</label>
                <input type="password" name="current_password" class="hb-form-control" placeholder="••••••••" required>
            </div>
            <div class="hb-form-row">
                <div class="hb-form-group">
                    <label class="hb-form-label">New Password *</label>
                    <input type="password" name="new_password" class="hb-form-control" placeholder="Minimum 6 characters" required>
                </div>
                <div class="hb-form-group">
                    <label class="hb-form-label">Confirm New Password *</label>
                    <input type="password" name="confirm_password" class="hb-form-control" placeholder="Re-enter new password" required>
                </div>
            </div>
            <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                <button type="submit" class="hb-btn hb-btn-primary">Update Security</button>
            </div>
        </form>
    </div>
</div>
