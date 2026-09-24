<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\CategoryRepository;
use App\Repositories\FoodRepository;
use App\Repositories\RestaurantRepository;

class MenuController extends Controller
{
    private FoodRepository $foodRepo;
    private CategoryRepository $categoryRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->foodRepo       = new FoodRepository();
        $this->categoryRepo   = new CategoryRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $categories = $this->categoryRepo->getByRestaurantId($restaurantId);

        $filters = [
            'category_id'  => Request::getQuery('category_id'),
            'food_type'    => Request::getQuery('food_type'),
            'is_available' => Request::getQuery('is_available'),
            'search'       => Request::getQuery('search'),
        ];

        $foods = $this->foodRepo->getAllByRestaurantId($restaurantId, $filters);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/menu', [
            'title'        => 'Menu & Foods — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'menu',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'categories'   => $categories,
            'foods'        => $foods,
            'filters'      => $filters,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function livePreview(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $categories = $this->categoryRepo->getByRestaurantId($restaurantId);
        $foods      = $this->foodRepo->getAllByRestaurantId($restaurantId, ['is_available' => 1]);

        echo $this->render('owner/live_menu', [
            'title'        => 'Live Menu Preview — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'live_menu',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'categories'   => $categories,
            'foods'        => $foods,
        ], 'owner');
    }

    public function toggleAvailability(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $foodId = (int)$id;
        $success = $this->foodRepo->toggleAvailability($foodId, $restaurantId);

        if (Request::isJson()) {
            Response::json(['success' => $success, 'food_id' => $foodId]);
        }

        Session::setFlash('success', 'Food item availability toggled.');
        Response::redirect('/owner/menu');
    }

    public function create(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $csrfToken = (string)Request::get('_csrf_token');
        if (!Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/menu');
        }

        $name = trim((string)Request::get('name', ''));
        $categoryId = (int)Request::get('category_id', 0);
        $basePrice = (float)Request::get('base_price', 0);
        $foodType = (string)Request::get('food_type', 'vegetarian');

        if ($name === '' || $categoryId <= 0 || $basePrice <= 0) {
            Session::setFlash('error', 'Please fill in food name, category, and a valid base price.');
            Response::redirect('/owner/menu');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $image = trim((string)Request::get('image', ''));
        if ($image === '') {
            $image = '/assets/images/foods/placeholder-dish.svg';
        }

        $this->foodRepo->createFoodItem([
            'restaurant_id' => $restaurantId,
            'category_id'   => $categoryId,
            'name'          => $name,
            'slug'          => $slug . '-' . substr(bin2hex(random_bytes(3)), 0, 4),
            'description'   => Request::get('description'),
            'image'         => $image,
            'ingredients'   => Request::get('ingredients'),
            'allergens'     => Request::get('allergens'),
            'food_type'     => $foodType,
            'base_price'    => $basePrice,
            'calories'      => Request::get('calories') !== '' ? (int)Request::get('calories') : null,
            'protein'       => Request::get('protein') !== '' ? (float)Request::get('protein') : null,
            'carbs'         => Request::get('carbs') !== '' ? (float)Request::get('carbs') : null,
            'fat'           => Request::get('fat') !== '' ? (float)Request::get('fat') : null,
            'fiber'         => Request::get('fiber') !== '' ? (float)Request::get('fiber') : null,
            'sugar'         => (Request::get('sugar') !== null && Request::get('sugar') !== '') ? max(0.0, (float)Request::get('sugar')) : null,
            'serving_size'  => Request::get('serving_size'),
            'is_available'  => 1,
            'is_featured'   => Request::get('is_featured') ? 1 : 0,
            'is_popular'    => Request::get('is_popular') ? 1 : 0,
        ]);

        Session::setFlash('success', "Dish '{$name}' created successfully.");
        Response::redirect('/owner/menu');
    }

    public function update(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $foodId = (int)$id;

        $name = trim((string)Request::get('name', ''));
        $categoryId = (int)Request::get('category_id', 0);
        $basePrice = (float)Request::get('base_price', 0);
        $foodType = (string)Request::get('food_type', 'vegetarian');

        if ($name === '' || $categoryId <= 0 || $basePrice <= 0) {
            Session::setFlash('error', 'Please fill in food name, category, and a valid base price.');
            Response::redirect('/owner/menu');
        }

        $data = [
            'category_id'  => $categoryId,
            'name'         => $name,
            'description'  => Request::get('description'),
            'food_type'    => $foodType,
            'base_price'   => $basePrice,
            'allergens'    => Request::get('allergens'),
            'ingredients'  => Request::get('ingredients'),
            'calories'     => Request::get('calories') !== '' ? (int)Request::get('calories') : null,
            'protein'      => Request::get('protein') !== '' ? (float)Request::get('protein') : null,
            'carbs'        => Request::get('carbs') !== '' ? (float)Request::get('carbs') : null,
            'fat'          => Request::get('fat') !== '' ? (float)Request::get('fat') : null,
            'sugar'        => (Request::get('sugar') !== null && Request::get('sugar') !== '') ? max(0.0, (float)Request::get('sugar')) : null,
        ];

        $image = trim((string)Request::get('image', ''));
        if ($image !== '') {
            $data['image'] = $image;
        }

        $this->foodRepo->updateFoodItem($foodId, $restaurantId, $data);

        Session::setFlash('success', "Dish '{$name}' updated successfully.");
        Response::redirect('/owner/menu');
    }

    public function delete(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $foodId = (int)$id;
        $this->foodRepo->deleteFoodItem($foodId, $restaurantId);

        Session::setFlash('success', 'Food item removed.');
        Response::redirect('/owner/menu');
    }

    public function createCategory(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $csrfToken = (string)Request::get('_csrf_token');
        if (!Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/menu');
        }

        $name = trim((string)Request::get('name', ''));
        $description = trim((string)Request::get('description', ''));

        if ($name === '') {
            Session::setFlash('error', 'Category name is required.');
            Response::redirect('/owner/menu');
        }

        try {
            $catId = $this->categoryRepo->createCategory($restaurantId, [
                'name'        => $name,
                'description' => $description !== '' ? $description : null,
                'image'       => '/assets/images/foods/placeholder-dish.svg',
            ]);
            Session::setFlash('success', "Category '{$name}' created successfully.");
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not create category.');
        }

        Response::redirect('/owner/menu');
    }

    public function deleteCategory(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];
        $categoryId   = (int)$id;

        $csrfToken = (string)Request::get('_csrf_token');
        if (!Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/menu');
        }

        try {
            $this->categoryRepo->deleteCategory($categoryId, $restaurantId);
            Session::setFlash('success', 'Category removed.');
        } catch (\InvalidArgumentException $e) {
            Session::setFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not remove category.');
        }

        Response::redirect('/owner/menu');
    }
}

