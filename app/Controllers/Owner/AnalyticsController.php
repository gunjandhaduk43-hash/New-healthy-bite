<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;

class AnalyticsController extends Controller
{
    private OrderRepository $orderRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->orderRepo      = new OrderRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant   = $this->restaurantRepo->findAnyById($restaurantId);
        $todayStats   = $this->orderRepo->getTodayStats($restaurantId);
        $macroStats   = $this->orderRepo->getMacroStats($restaurantId);
        $paymentStats = $this->orderRepo->getPaymentStats($restaurantId);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/analytics', [
            'title'        => 'Sales & Macro Analytics — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'analytics',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'todayStats'   => $todayStats,
            'macroStats'   => $macroStats,
            'paymentStats' => $paymentStats,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }
}
