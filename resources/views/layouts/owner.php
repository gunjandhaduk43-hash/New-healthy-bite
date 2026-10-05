<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\Csrf::getToken() ?>">
    <title><?= e($title ?? 'Restaurant Owner Portal — Healthy Bite') ?></title>
    
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="<?= asset('css/variables.css') ?>?v=2.0">
    <link rel="stylesheet" href="<?= asset('css/reset.css') ?>?v=2.0">
    <link rel="stylesheet" href="<?= asset('css/components.css') ?>?v=2.0">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>?v=2.0">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-icon" style="padding:0; overflow:hidden; background:transparent;">
                    <img src="<?= asset('images/logo.png') ?>" alt="Healthy Bite Logo" style="width:36px; height:36px; border-radius:50%; object-fit:cover; display:block;">
                </div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-title">Healthy Bite</span>
                    <span class="sidebar-brand-subtitle">Good Food. Better You.</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/dashboard') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="bi bi-grid-fill"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/orders') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'orders' ? 'active' : '' ?>">
                            <i class="bi bi-receipt"></i>
                            <span>Live Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/kitchen-orders') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'kitchen_orders' ? 'active' : '' ?>">
                            <i class="bi bi-fire"></i>
                            <span>Kitchen Live Orders</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/menu') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'menu' ? 'active' : '' ?>">
                            <i class="bi bi-egg-fried"></i>
                            <span>Menu & Foods</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/tables') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'tables' ? 'active' : '' ?>">
                            <i class="bi bi-qr-code-scan"></i>
                            <span>Tables & QR</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/reviews') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'reviews' ? 'active' : '' ?>">
                            <i class="bi bi-star-fill"></i>
                            <span>Customer Reviews</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/staff') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'staff' ? 'active' : '' ?>">
                            <i class="bi bi-people-fill"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/analytics') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'analytics' ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Sales & Analytics</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/live-menu') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'live_menu' ? 'active' : '' ?>">
                            <i class="bi bi-eye-fill"></i>
                            <span>Live Menu</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-divider"></div>

                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/profile') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'profile' ? 'active' : '' ?>">
                            <i class="bi bi-shop"></i>
                            <span>Restaurant Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/settings') ?>" class="sidebar-link <?= ($activeNav ?? '') === 'settings' ? 'active' : '' ?>">
                            <i class="bi bi-gear-fill"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="<?= url('/owner/logout') ?>" class="sidebar-link">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Dashboard Area -->
        <div class="dashboard-main">
            <!-- Topbar -->
            <header class="dashboard-topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="mobile-nav-toggle" aria-label="Toggle navigation">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="topbar-restaurant-info">
                        <span class="topbar-restaurant-name"><?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?></span>
                        <span class="topbar-restaurant-sub">Restaurant Owner • <?= e($restaurant['city'] ?? 'Bengaluru') ?></span>
                    </div>
                </div>

                <div class="topbar-right-actions">
                    <button class="topbar-icon-btn" title="Notifications" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                    </button>
                    <div class="topbar-user-badge">
                        <div class="topbar-avatar">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Avatar" onerror="this.style.display='none';this.parentNode.innerText='<?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>'">
                        </div>
                        <span class="topbar-user-name"><?= e($restaurant['name'] ?? $user['name'] ?? 'Greenhouse Kitchen') ?></span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="dashboard-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="<?= asset('js/dashboard.js') ?>"></script>
</body>
</html>
