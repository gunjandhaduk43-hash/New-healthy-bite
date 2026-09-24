<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\RestaurantRepository;

class ProfileController extends Controller
{
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant   = $this->restaurantRepo->findAnyById($restaurantId);
        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/profile', [
            'title'        => 'Restaurant Profile — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'profile',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function update(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $data = [
            'name'        => trim((string)Request::get('name', '')),
            'phone'       => trim((string)Request::get('phone', '')),
            'email'       => trim((string)Request::get('email', '')),
            'address'     => trim((string)Request::get('address', '')),
            'city'        => trim((string)Request::get('city', '')),
            'description' => trim((string)Request::get('description', ''))
        ];

        $this->restaurantRepo->updateDetails($restaurantId, $data);
        Session::setFlash('success', 'Restaurant profile details saved successfully.');
        Response::redirect('/owner/profile');
    }
}
