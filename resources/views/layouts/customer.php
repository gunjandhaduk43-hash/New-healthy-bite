<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2E7D32">
    <title><?= e($title ?? config('app.name', 'Healthy Bite — Digital Restaurant Menu')) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- CSS Design Tokens & Modules -->
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/reset.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/layout.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/customer.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/food-details.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/cart.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/checkout.css') ?>?v=3.3">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>?v=3.3">
</head>
<body class="customer-body">
    <!-- Header: Restaurant Identity & Quick Actions -->
    <header class="site-header">
        <div class="app-container header-inner">
            <!-- Left: Brand Logo & Restaurant Context -->
            <a href="<?= url('/menu') ?>" class="brand-section" aria-label="Healthy Bite Menu">
                <div class="restaurant-logo-circle">
                    <img src="<?= asset('images/logo.png') ?>" alt="Healthy Bite Logo" class="restaurant-brand-logo">
                </div>
                <div class="brand-info">
                    <span class="brand-name">Healthy Bite</span>
                    <span class="brand-meta"><?= !empty($branch['name']) ? e($branch['name']) : 'Fresh Food · Clear Choices' ?></span>
                </div>
            </a>

            <!-- Center: Navigation (Desktop Only) -->
            <nav class="header-nav" aria-label="Main Navigation">
                <a href="<?= url('/menu') ?>" class="nav-link">Menu</a>
                <a href="<?= url('/menu/live-kitchen') ?>" class="nav-link" id="navLiveKitchenLink" onclick="return handleLiveKitchenClick(event)">
                    <i class="bi bi-fire" style="color:var(--accent-amber); margin-right:3px;"></i> Live Kitchen
                </a>
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
                        <img src="<?= asset('images/logo.png') ?>" alt="Healthy Bite Logo" style="width:26px; height:26px; border-radius:50%; object-fit:cover;">
                    </div>
                    <span>Healthy Bite</span>
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

    <script>
    function handleLiveKitchenClick(e) {
        if (e) e.preventDefault();
        const activeOrderNum = localStorage.getItem('active_order_number');
        if (activeOrderNum) {
            window.location.href = `/menu/tracking/${activeOrderNum}`;
            return false;
        }

        const tableId = (window.State && window.State.table && window.State.table.id) ? window.State.table.id : 4;
        const restaurantId = (window.State && window.State.restaurant && window.State.restaurant.id) ? window.State.restaurant.id : 1;
        fetch(`/api/orders/table-latest?table_id=${tableId}&restaurant_id=${restaurantId}`)
            .then(r => r.json())
            .then(json => {
                if (json && json.data && json.data.order_number) {
                    localStorage.setItem('active_order_number', json.data.order_number);
                    window.location.href = `/menu/tracking/${json.data.order_number}`;
                } else {
                    if (window.showToast) {
                        window.showToast('No active orders right now. Order delicious meals to track live!', 'info', 'bi-fire');
                    } else if (window.Utils) {
                        Utils.showToast('No active orders right now. Order delicious meals to track live!', 'info');
                    } else {
                        alert('No active orders right now. Order delicious meals to track live!');
                    }
                }
            })
            .catch(() => {
                window.location.href = '/menu';
            });
        return false;
    }
    window.handleLiveKitchenClick = handleLiveKitchenClick;
    </script>
</body>
</html>
