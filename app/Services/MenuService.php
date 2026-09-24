<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\RestaurantRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FoodRepository;

class MenuService
{
    private RestaurantRepository $restaurantRepo;
    private CategoryRepository $categoryRepo;
    private FoodRepository $foodRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->foodRepo = new FoodRepository();
    }

    public function getRestaurant(int $restaurantId): ?array
    {
        return $this->restaurantRepo->findById($restaurantId);
    }

    public function getRestaurantBySlug(string $slug): ?array
    {
        return $this->restaurantRepo->findBySlug($slug);
    }

    public function getCategories(int $restaurantId): array
    {
        return $this->categoryRepo->getByRestaurantId($restaurantId);
    }

    public function getFoods(int $restaurantId, array $filters = []): array
    {
        return $this->foodRepo->getByRestaurantId($restaurantId, $filters);
    }

    public function getFoodDetails(int $foodId, int $restaurantId): ?array
    {
        $food = $this->foodRepo->getByIdAndRestaurant($foodId, $restaurantId);
        if (!$food) {
            return null;
        }

        $food['variants'] = $this->foodRepo->getVariants($foodId);
        $customizations = $this->foodRepo->getCustomizations($foodId);

        // Group customizations by group_name
        $grouped = [];
        foreach ($customizations as $c) {
            $group = $c['group_name'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $c;
        }

        $food['customization_groups'] = $grouped;
        $food['customizations'] = $customizations;

        return $food;
    }
}
