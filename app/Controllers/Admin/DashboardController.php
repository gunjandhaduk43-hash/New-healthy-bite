<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Middleware\AdminMiddleware;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\UserRepository;

class DashboardController extends Controller
{
    private RestaurantRepository $restaurantRepo;
    private OrderRepository $orderRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
        $this->orderRepo      = new OrderRepository();
        $this->userRepo       = new UserRepository();
    }

    public function index(): void
    {
        $adminUser = AdminMiddleware::handle();

        $platformStats     = $this->restaurantRepo->getPlatformOverviewStats();
        $recentRestaurants = $this->restaurantRepo->getAll(10);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('admin/dashboard', [
            'title'             => 'Platform Overview — Healthy Bite Platform Control Center',
            'activeNav'         => 'dashboard',
            'user'              => $adminUser,
            'stats'             => $platformStats,
            'recentRestaurants' => $recentRestaurants,
            'flashSuccess'      => $flashSuccess,
            'flashError'        => $flashError,
        ], 'admin');
    }
}
