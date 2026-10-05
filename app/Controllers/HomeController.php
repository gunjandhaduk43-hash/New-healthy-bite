<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\CategoryRepository;
use App\Repositories\FoodRepository;
use App\Repositories\RestaurantRepository;

class HomeController extends Controller
{
    public function index(): void
    {
        $restaurantId = (int)config('app.default_restaurant_id', 1);

        $restaurantRepo = new RestaurantRepository();
        $restaurant = $restaurantRepo->findById($restaurantId);
        $branch = $restaurantRepo->getBranch($restaurantId);

        $foodRepo = new FoodRepository();
        $allFoods = $foodRepo->getByRestaurantId($restaurantId);

        // Curate 6 top featured or popular dishes
        $featuredFoods = array_slice(array_filter($allFoods, fn($f) => !empty($f['is_featured']) || !empty($f['is_popular'])), 0, 6);
        if (empty($featuredFoods)) {
            $featuredFoods = array_slice($allFoods, 0, 6);
        }

        $categoryRepo = new CategoryRepository();
        $rawCategories = $categoryRepo->getByRestaurantId($restaurantId);
        $categories = array_values(array_filter($rawCategories, function ($c) {
            return stripos($c['name'], 'test') === false;
        }));

        // Fetch tables for direct quick-order selection
        $db = Database::getConnection();
        $tablesStmt = $db->prepare("
            SELECT id, table_number, status 
            FROM restaurant_tables 
            WHERE restaurant_id = :restaurant_id 
            ORDER BY CAST(table_number AS UNSIGNED) ASC, table_number ASC 
            LIMIT 12
        ");
        $tablesStmt->execute(['restaurant_id' => $restaurantId]);
        $tables = $tablesStmt->fetchAll();

        echo $this->render('customer/welcome', [
            'appName'       => config('app.name', 'Healthy Bite'),
            'restaurant'    => $restaurant ? $restaurant['name'] : 'Greenhouse Kitchen',
            'restaurantObj' => $restaurant,
            'branch'        => $branch ? $branch['name'] : 'Indiranagar, Bengaluru',
            'branchObj'     => $branch,
            'featuredFoods' => $featuredFoods,
            'categories'    => $categories,
            'tables'        => $tables,
            'title'         => 'Healthy Bite — Fresh Food & Macro-Accurate Dining'
        ]);
    }
}
