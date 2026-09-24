<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\MenuService;

class FoodController extends Controller
{
    private MenuService $menuService;

    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function show(string $id): void
    {
        $foodId = (int)$id;
        $restaurantId = (int)Request::getQuery('restaurant_id', (string)config('app.default_restaurant_id', 1));

        $food = $this->menuService->getFoodDetails($foodId, $restaurantId);
        if (!$food) {
            $this->json(['error' => 'Food item not found'], 404);
            return;
        }

        $this->json(['data' => $food]);
    }

    public function variants(string $id): void
    {
        $foodId = (int)$id;
        $restaurantId = (int)Request::getQuery('restaurant_id', (string)config('app.default_restaurant_id', 1));

        $food = $this->menuService->getFoodDetails($foodId, $restaurantId);
        if (!$food) {
            $this->json(['error' => 'Food item not found'], 404);
            return;
        }

        $this->json(['data' => $food['variants']]);
    }

    public function customizations(string $id): void
    {
        $foodId = (int)$id;
        $restaurantId = (int)Request::getQuery('restaurant_id', (string)config('app.default_restaurant_id', 1));

        $food = $this->menuService->getFoodDetails($foodId, $restaurantId);
        if (!$food) {
            $this->json(['error' => 'Food item not found'], 404);
            return;
        }

        $this->json([
            'data'   => $food['customizations'],
            'groups' => $food['customization_groups']
        ]);
    }
}
