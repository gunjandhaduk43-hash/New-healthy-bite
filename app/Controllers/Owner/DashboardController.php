<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\BranchRepository;
use App\Repositories\FoodRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;

class DashboardController extends Controller
{
    private RestaurantRepository $restaurantRepo;
    private OrderRepository $orderRepo;
    private FoodRepository $foodRepo;
    private BranchRepository $branchRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
        $this->orderRepo      = new OrderRepository();
        $this->foodRepo       = new FoodRepository();
        $this->branchRepo     = new BranchRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $stats      = $this->orderRepo->getTodayStats($restaurantId);
        $tableStats = $this->branchRepo->countTablesByRestaurant($restaurantId);
        $foodCount    = $this->foodRepo->countByRestaurant($restaurantId);
        $popularItems = $this->orderRepo->getPopularItems($restaurantId, 5);
        $recentOrders = $this->orderRepo->findByRestaurant($restaurantId, null, 8);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/dashboard', [
            'title'           => ($restaurant['name'] ?? 'Restaurant') . ' — Overview',
            'activeNav'       => 'dashboard',
            'user'            => $user,
            'restaurant'      => $restaurant,
            'stats'           => $stats,
            'tableStats'      => $tableStats,
            'foodCount'       => $foodCount,
            'popularItems'    => $popularItems,
            'recentOrders'    => $recentOrders,
            'flashSuccess'    => $flashSuccess,
            'flashError'      => $flashError,
        ], 'owner');
    }
}
