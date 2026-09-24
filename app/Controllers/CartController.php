<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\CartService;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    public function validateCart(): void
    {
        $body = Request::getBody();
        $restaurantId = (int)($body['restaurant_id'] ?? config('app.default_restaurant_id', 1));
        $items = is_array($body['items'] ?? null) ? $body['items'] : [];

        try {
            $validated = $this->cartService->validateCartItems($items, $restaurantId);
            $this->json([
                'status'  => 'success',
                'message' => 'Cart is valid and up to date.',
                'data'    => $validated,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
