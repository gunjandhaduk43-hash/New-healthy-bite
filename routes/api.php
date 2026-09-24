<?php
declare(strict_types=1);

use App\Core\Response;
use App\Controllers\RestaurantController;
use App\Controllers\FoodController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\PaymentController;

/** @var \App\Core\Router $router */

$router->get('/api/health', function() {
    Response::json([
        'status'    => 'ok',
        'app'       => 'Healthy Bite',
        'database'  => 'connected',
        'timestamp' => date('c'),
    ]);
});

// Restaurant & Menu Data
$router->get('/api/restaurant', [RestaurantController::class, 'getRestaurant']);
$router->get('/api/categories', [RestaurantController::class, 'getCategories']);
$router->get('/api/foods', [RestaurantController::class, 'getFoods']);
$router->get('/api/foods/{id}', [FoodController::class, 'show']);
$router->get('/api/foods/{id}/variants', [FoodController::class, 'variants']);
$router->get('/api/foods/{id}/customizations', [FoodController::class, 'customizations']);

// Cart, Orders & Payments
$router->post('/api/cart/validate', [CartController::class, 'validateCart']);
$router->post('/api/orders', [OrderController::class, 'createOrder']);
$router->get('/api/orders/table-latest', [OrderController::class, 'getLatestTableOrder']);
$router->get('/api/orders/{orderNumber}', [OrderController::class, 'getOrder']);
$router->post('/api/payment/simulate', [PaymentController::class, 'simulate']);
$router->post('/api/reviews', [OrderController::class, 'submitReview']);

