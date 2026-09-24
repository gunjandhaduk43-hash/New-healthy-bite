<?php
declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\MenuController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/menu/welcome', [HomeController::class, 'index']);
$router->get('/menu', [MenuController::class, 'index']);
$router->get('/table/{tableNumber}', [MenuController::class, 'resolveTableNumber']);
$router->get('/t/{token}', [MenuController::class, 'resolveQr']);
$router->get('/qr/{token}', [MenuController::class, 'resolveQr']);
$router->get('/cart', fn() => \App\Core\Response::redirect('/menu/checkout'));
$router->get('/menu/cart', fn() => \App\Core\Response::redirect('/menu/checkout'));
$router->get('/menu/checkout', [CheckoutController::class, 'index']);
$router->get('/menu/confirmation/{orderNumber}', [OrderController::class, 'confirmation']);
$router->get('/menu/tracking/{orderNumber}', [OrderController::class, 'tracking']);

// -------------------------------------------------------------
// Restaurant Owner Portal Routes (Matching Figma Design System)
// -------------------------------------------------------------
$router->get('/owner', fn() => \App\Core\Response::redirect('/owner/dashboard'));
$router->get('/owner/login', [\App\Controllers\Owner\AuthController::class, 'loginForm']);
$router->post('/owner/login', [\App\Controllers\Owner\AuthController::class, 'login']);
$router->get('/owner/logout', [\App\Controllers\Owner\AuthController::class, 'logout']);

// 1. Overview (Monitoring only, no action column)
$router->get('/owner/dashboard', [\App\Controllers\Owner\DashboardController::class, 'index']);

// 2. Live Orders (Monitoring only, no status updates)
$router->get('/owner/orders', [\App\Controllers\Owner\OrderController::class, 'index']);
$router->get('/owner/orders/{id}/details', [\App\Controllers\Owner\OrderController::class, 'details']);

// 3. Kitchen Live Orders (Operational Kanban board status transitions)
$router->get('/owner/kitchen-orders', [\App\Controllers\Owner\OrderController::class, 'kitchenOrders']);
$router->post('/owner/orders/{id}/status', [\App\Controllers\Owner\OrderController::class, 'updateStatus']);
$router->post('/owner/orders/{id}/payment', [\App\Controllers\Owner\OrderController::class, 'updatePayment']);

// 4. Menu & Foods
$router->get('/owner/menu', [\App\Controllers\Owner\MenuController::class, 'index']);
$router->post('/owner/menu/create', [\App\Controllers\Owner\MenuController::class, 'create']);
$router->post('/owner/menu/{id}/edit', [\App\Controllers\Owner\MenuController::class, 'update']);
$router->post('/owner/menu/{id}/toggle', [\App\Controllers\Owner\MenuController::class, 'toggleAvailability']);
$router->post('/owner/menu/{id}/delete', [\App\Controllers\Owner\MenuController::class, 'delete']);
$router->post('/owner/categories/create', [\App\Controllers\Owner\MenuController::class, 'createCategory']);
$router->post('/owner/categories/{id}/delete', [\App\Controllers\Owner\MenuController::class, 'deleteCategory']);

// 5. Tables & QR
$router->get('/owner/tables', [\App\Controllers\Owner\TableController::class, 'index']);
$router->post('/owner/tables/create', [\App\Controllers\Owner\TableController::class, 'create']);
$router->post('/owner/tables/{id}/status', [\App\Controllers\Owner\TableController::class, 'updateStatus']);

// 6. Customer Reviews
$router->get('/owner/reviews', [\App\Controllers\Owner\ReviewController::class, 'index']);
$router->post('/owner/reviews/{id}/respond', [\App\Controllers\Owner\ReviewController::class, 'respond']);

// 7. Staff Management
$router->get('/owner/staff', [\App\Controllers\Owner\StaffController::class, 'index']);
$router->post('/owner/staff/create', [\App\Controllers\Owner\StaffController::class, 'create']);
$router->post('/owner/staff/{id}/edit', [\App\Controllers\Owner\StaffController::class, 'update']);
$router->post('/owner/staff/{id}/toggle', [\App\Controllers\Owner\StaffController::class, 'toggleStatus']);

// 8. Sales & Macro Analytics
$router->get('/owner/analytics', [\App\Controllers\Owner\AnalyticsController::class, 'index']);

// 9. Live Menu Preview
$router->get('/owner/live-menu', [\App\Controllers\Owner\MenuController::class, 'livePreview']);

// Profile & Settings
$router->get('/owner/profile', [\App\Controllers\Owner\ProfileController::class, 'index']);
$router->post('/owner/profile', [\App\Controllers\Owner\ProfileController::class, 'update']);
$router->get('/owner/settings', [\App\Controllers\Owner\SettingsController::class, 'index']);
$router->post('/owner/settings/password', [\App\Controllers\Owner\SettingsController::class, 'updatePassword']);


// -------------------------------------------------------------
// Platform Administrator Portal Routes (NO Platform Orders)
// -------------------------------------------------------------
$router->get('/admin', fn() => \App\Core\Response::redirect('/admin/dashboard'));
$router->get('/admin/login', [\App\Controllers\Admin\AuthController::class, 'loginForm']);
$router->post('/admin/login', [\App\Controllers\Admin\AuthController::class, 'login']);
$router->get('/admin/logout', [\App\Controllers\Admin\AuthController::class, 'logout']);

// 1. Platform Overview (NO generic status column)
$router->get('/admin/dashboard', [\App\Controllers\Admin\DashboardController::class, 'index']);

// 2. Registered Restaurants
$router->get('/admin/restaurants', [\App\Controllers\Admin\RestaurantController::class, 'index']);
$router->post('/admin/restaurants/{id}/status', [\App\Controllers\Admin\RestaurantController::class, 'updateStatus']);

// 3. Restaurant Portal Inspection
$router->get('/admin/portal-inspect', [\App\Controllers\Admin\RestaurantController::class, 'inspectDefault']);
$router->get('/admin/restaurants/{id}/inspect', [\App\Controllers\Admin\RestaurantController::class, 'inspect']);

// 4. Users & Access
$router->get('/admin/users', [\App\Controllers\Admin\UserController::class, 'index']);
$router->post('/admin/users/create', [\App\Controllers\Admin\UserController::class, 'create']);
$router->post('/admin/users/{id}/status', [\App\Controllers\Admin\UserController::class, 'updateStatus']);

// NOTE: Platform Orders (/admin/orders) has been REMOVED completely per specification.
