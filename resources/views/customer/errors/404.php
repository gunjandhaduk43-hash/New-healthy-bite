<div class="error-page">
    <div class="error-code">404</div>
    <h2 class="error-title">Page Not Found</h2>
    <p class="error-desc">The requested page <code><?= e($path ?? '') ?></code> does not exist.</p>
    <a href="<?= url('/') ?>" class="btn btn-primary">Back to Home</a>
</div>
