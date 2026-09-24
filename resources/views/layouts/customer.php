<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2E7D32">
    <title><?= e($title ?? config('app.name', 'Healthy Bite — Digital Restaurant Menu')) ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CSS Design Tokens & Modules -->
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/reset.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/layout.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/customer.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/food-details.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/cart.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/checkout.css') ?>?v=3.2">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>?v=3.2">
</head>
<body class="customer-body">
    <!-- Header: Restaurant Identity & Quick Actions -->
    <header class="site-header">
        <div class="app-container header-inner">
            <!-- Left: Brand Logo & Restaurant Context -->
            <a href="<?= url('/menu') ?>" class="brand-section" aria-label="Healthy Bite Menu">
                <div class="restaurant-logo-circle">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div class="brand-info">
                    <span class="brand-name"><?= e($restaurant['name'] ?? 'Healthy Bite') ?></span>
                    <span class="brand-meta"><?= !empty($branch['name']) ? e($branch['name']) : 'Fresh Food · Clear Choices' ?></span>
                </div>
            </a>

            <!-- Center: Navigation (Desktop Only) -->
            <nav class="header-nav" aria-label="Main Navigation">
                <a href="<?= url('/menu') ?>" class="nav-link active">Menu</a>
                <a href="#live-kitchen-widget" class="nav-link">Live Kitchen</a>
                <a href="#about-section" class="nav-link">About</a>
            </nav>

            <!-- Right: Table Session Badge & Cart Trigger -->
            <div class="header-actions">
                <?php if (!empty($tableContext['table_number'])): ?>
                    <div class="table-badge" id="headerTableBadge" title="Table Session Active">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Table <?= e($tableContext['table_number']) ?></span>
                    </div>
                <?php else: ?>
                    <div class="table-badge dine-in-badge" id="headerTableBadge" title="Contactless Dining Active">
                        <i class="bi bi-cup-hot"></i>
                        <span>Dine-In / Pick up</span>
                    </div>
                <?php endif; ?>

                <button type="button" class="btn-cart-trigger" id="cartTrigger" aria-label="View Cart" title="View Cart">
                    <i class="bi bi-bag"></i>
                    <span class="cart-count-badge" id="cartCount" style="display:none;">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container" aria-live="polite"></div>

    <!-- Main View Content -->
    <main class="app-main" id="main-content">
        <?= $content ?>
    </main>

    <!-- Clean Modern Restaurant Footer -->
    <footer class="site-footer" id="about-section">
        <div class="app-container footer-inner">
            <div class="footer-left">
                <div class="footer-logo">
                    <div class="footer-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                        </svg>
                    </div>
                    <span><?= e($restaurant['name'] ?? 'Healthy Bite') ?></span>
                </div>
                <span class="footer-tagline">100% Macro Accurate · Crafted Fresh Daily</span>
            </div>

            <div class="footer-center">
                <span>Indiranagar, Bengaluru</span>
                <span class="dot-sep">•</span>
                <span>Open Daily: 10:00 AM – 11:00 PM</span>
                <span class="dot-sep">•</span>
                <a href="<?= url('/owner/login') ?>" class="partner-link">Partner Login</a>
            </div>

            <div class="footer-right">
                <div class="social-links" aria-label="Social links">
                    <span class="follow-label">Follow</span>
                    <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
