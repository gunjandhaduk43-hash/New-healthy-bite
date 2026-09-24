<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\AdminMiddleware;
use App\Repositories\RestaurantRepository;

class RestaurantController extends Controller
{
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $adminUser = AdminMiddleware::handle();

        $restaurants = $this->restaurantRepo->getAll(100);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('admin/restaurants', [
            'title'        => 'Registered Restaurants — Healthy Bite Platform Control Center',
            'activeNav'    => 'restaurants',
            'user'         => $adminUser,
            'restaurants'  => $restaurants,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'admin');
    }

    public function inspect(string|int $id): void
    {
        $adminUser = AdminMiddleware::handle();
        $restaurantId = (int)$id;

        $restaurant = $this->restaurantRepo->getInspectionDetails($restaurantId);
        if (!$restaurant) {
            Session::setFlash('error', 'Restaurant not found for inspection.');
            Response::redirect('/admin/restaurants');
        }

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('admin/inspect', [
            'title'        => 'Restaurant Portal Inspection — Healthy Bite Platform Control Center',
            'activeNav'    => 'inspect',
            'user'         => $adminUser,
            'restaurant'   => $restaurant,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'admin');
    }

    public function inspectDefault(): void
    {
        $this->inspect(1);
    }

    public function updateStatus(string|int $id): void
    {
        AdminMiddleware::handle();

        $restaurantId = (int)$id;
        $status       = (string)Request::get('status', '');

        $validStatuses = ['pending', 'approved', 'suspended'];
        if (!in_array($status, $validStatuses, true)) {
            Session::setFlash('error', 'Invalid restaurant status specified.');
            Response::redirect('/admin/restaurants');
        }

        $this->restaurantRepo->updateStatus($restaurantId, $status);

        Session::setFlash('success', "Restaurant #{$restaurantId} status updated to " . ucfirst($status) . ".");
        Response::redirect('/admin/restaurants');
    }
}
