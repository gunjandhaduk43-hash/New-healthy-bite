<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\MenuService;
use App\Services\QrService;

class RestaurantController extends Controller
{
    private MenuService $menuService;
    private QrService $qrService;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->qrService = new QrService();
    }

    public function getRestaurant(): void
    {
        $restaurantId = $this->resolveRestaurantId();
        $restaurant = $this->menuService->getRestaurant($restaurantId);

        if (!$restaurant) {
            $this->json(['error' => 'Restaurant not found'], 404);
            return;
        }

        $this->json(['data' => $restaurant]);
    }

    public function getCategories(): void
    {
        $restaurantId = $this->resolveRestaurantId();
        $categories = $this->menuService->getCategories($restaurantId);

        $this->json(['data' => $categories]);
    }

    public function getFoods(): void
    {
        $restaurantId = $this->resolveRestaurantId();
        $filters = [
            'category_id' => Request::getQuery('category_id'),
            'food_type'   => Request::getQuery('food_type'),
            'search'      => Request::getQuery('search'),
        ];

        $foods = $this->menuService->getFoods($restaurantId, $filters);
        $this->json(['data' => $foods]);
    }

    private function resolveRestaurantId(): int
    {
        $qrToken = Request::getQuery('token');
        if ($qrToken) {
            $context = $this->qrService->resolveToken((string)$qrToken);
            if ($context) {
                return (int)$context['restaurant_id'];
            }
        }

        return (int)Request::getQuery('restaurant_id', (string)config('app.default_restaurant_id', 1));
    }
}
