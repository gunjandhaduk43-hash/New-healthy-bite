<div class="error-page">
    <div class="error-code">500</div>
    <h2 class="error-title">Internal Server Error</h2>
    <p class="error-desc">Something went wrong on our servers. Our team has been notified.</p>
    <?php if (isset($exception) && $exception): ?>
        <pre class="error-debug"><?= e($exception->getMessage()) . "\n" . e($exception->getFile() . ':' . $exception->getLine()) ?></pre>
    <?php endif; ?>
    <a href="<?= url('/') ?>" class="btn btn-primary">Return Safely Home</a>
</div>
