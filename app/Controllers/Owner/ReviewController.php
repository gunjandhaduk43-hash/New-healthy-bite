<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\RestaurantRepository;
use App\Repositories\ReviewRepository;

class ReviewController extends Controller
{
    private ReviewRepository $reviewRepo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->reviewRepo     = new ReviewRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $reviews    = $this->reviewRepo->findByRestaurant($restaurantId);
        $summary    = $this->reviewRepo->getSummaryStats($restaurantId);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/reviews', [
            'title'        => 'Customer Reviews — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'reviews',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'reviews'      => $reviews,
            'summary'      => $summary,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function respond(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = (int)$authContext['restaurant_id'];
        $reviewId     = (int)$id;

        $response = trim((string)Request::get('reply', ''));

        if ($response === '') {
            if (Request::isJson()) {
                Response::json(['success' => false, 'error' => 'Reply cannot be empty.'], 422);
            }
            Session::setFlash('error', 'Please enter a response before submitting.');
            Response::redirect('/owner/reviews');
        }

        $saved = $this->reviewRepo->addReply($reviewId, $restaurantId, $response);

        if (Request::isJson()) {
            Response::json([
                'success' => $saved,
                'review_id' => $reviewId,
                'reply' => $response,
                'replied_at' => date('Y-m-d H:i:s'),
                'message' => 'Your thoughtful response has been recorded.'
            ]);
        }

        Session::setFlash('success', 'Your thoughtful response has been recorded.');
        Response::redirect('/owner/reviews');
    }
}
