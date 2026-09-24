<div class="app-container">
    <div class="welcome-card">
        <div class="welcome-badge">
            <i class="bi bi-patch-check-fill"></i> Foundation Phase Active
        </div>
        <h1 class="welcome-title"><?= e($restaurant) ?></h1>
        <p class="welcome-subtitle"><i class="bi bi-geo-alt"></i> <?= e($branch) ?></p>
        <p class="welcome-desc">
            Welcome to the digital dining experience. Fresh food, clear choices, and protein-rich meals made for everyday eating.
        </p>
        <div class="welcome-actions">
            <a href="<?= url('/menu') ?>" class="btn btn-primary">
                <i class="bi bi-journal-text"></i> Explore Digital Menu
            </a>
            <a href="<?= url('/api/health') ?>" class="btn btn-outline" target="_blank">
                <i class="bi bi-cpu"></i> System Health API
            </a>
        </div>
    </div>
</div>
