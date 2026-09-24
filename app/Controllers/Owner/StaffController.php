<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\RestaurantRepository;
use App\Repositories\StaffRepository;

class StaffController extends Controller
{
    private StaffRepository $staffRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->staffRepo      = new StaffRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $staff      = $this->staffRepo->findByRestaurant($restaurantId);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/staff', [
            'title'        => 'Staff Management — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'staff',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'staff'        => $staff,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function create(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $name     = trim((string)Request::get('name', ''));
        $email    = trim((string)Request::get('email', ''));
        $roleId   = (int)Request::get('role_id', 4);
        $password = trim((string)Request::get('password', 'Secret@123'));

        if (empty($name) || empty($email)) {
            Session::setFlash('error', 'Name and email are required.');
            Response::redirect('/owner/staff');
        }

        try {
            $this->staffRepo->createStaff($restaurantId, $name, $email, $roleId, $password);
            Session::setFlash('success', "Team member {$name} added successfully.");
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not add staff member. Email may already exist.');
        }

        Response::redirect('/owner/staff');
    }

    public function toggleStatus(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];
        $userId       = (int)$id;

        $this->staffRepo->toggleStatus($userId, $restaurantId);
        Session::setFlash('success', 'Staff status updated successfully.');
        Response::redirect('/owner/staff');
    }

    public function update(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];
        $userId       = (int)$id;

        $csrfToken = (string)Request::get('_csrf_token');
        if (!\App\Core\Csrf::validateToken($csrfToken)) {
            Session::setFlash('error', 'Security token expired. Please try again.');
            Response::redirect('/owner/staff');
        }

        $name     = trim((string)Request::get('name', ''));
        $roleId   = (int)Request::get('role_id', 4);
        $password = trim((string)Request::get('password', ''));

        if (empty($name)) {
            Session::setFlash('error', 'Staff name is required.');
            Response::redirect('/owner/staff');
        }

        try {
            $this->staffRepo->updateStaff($userId, $restaurantId, $name, $roleId, $password !== '' ? $password : null);
            Session::setFlash('success', "Team member {$name} updated successfully.");
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Could not update staff member.');
        }

        Response::redirect('/owner/staff');
    }
}

