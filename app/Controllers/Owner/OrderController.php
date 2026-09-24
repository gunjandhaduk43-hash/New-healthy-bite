<?php
declare(strict_types=1);

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\RestaurantMiddleware;
use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;

class OrderController extends Controller
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

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $statusFilter = (string)Request::getQuery('status', 'all');
        $orders = $this->orderRepo->findByRestaurant($restaurantId, $statusFilter);

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/orders', [
            'title'        => 'Live Orders — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'orders',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'orders'       => $orders,
            'statusFilter' => $statusFilter,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function kitchenOrders(): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $user         = $authContext['user'];
        $restaurantId = $authContext['restaurant_id'];

        $restaurant = $this->restaurantRepo->findAnyById($restaurantId);
        $orders     = $this->orderRepo->getKitchenOrders($restaurantId);

        // Group by status for Kanban columns
        $kanban = [
            'placed'    => [],
            'accepted'  => [],
            'preparing' => [],
            'ready'     => [],
            'completed' => []
        ];

        foreach ($orders as $o) {
            $st = $o['order_status'];
            if (isset($kanban[$st])) {
                $kanban[$st][] = $o;
            }
        }

        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');

        echo $this->render('owner/kitchen_orders', [
            'title'        => 'Kitchen Live Orders — ' . ($restaurant['name'] ?? 'Dashboard'),
            'activeNav'    => 'kitchen_orders',
            'user'         => $user,
            'restaurant'   => $restaurant,
            'kanban'       => $kanban,
            'orders'       => $orders,
            'flashSuccess' => $flashSuccess,
            'flashError'   => $flashError,
        ], 'owner');
    }

    public function updateStatus(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $orderId = (int)$id;
        $newStatus = (string)Request::get('status', '');

        $validStatuses = ['placed', 'accepted', 'preparing', 'ready', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses, true)) {
            if (Request::isJson()) {
                Response::json(['error' => 'Invalid order status transition'], 400);
            }
            Session::setFlash('error', 'Invalid order status selected.');
            Response::redirect('/owner/orders');
        }

        $success = $this->orderRepo->updateStatusForRestaurant($orderId, $restaurantId, $newStatus);

        if (Request::isJson()) {
            Response::json([
                'success'  => $success,
                'order_id' => $orderId,
                'status'   => $newStatus
            ]);
        }

        Session::setFlash('success', "Order #{$orderId} status changed to " . ucfirst($newStatus) . ".");
        Response::redirect('/owner/orders');
    }

    public function updatePayment(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = $authContext['restaurant_id'];

        $orderId = (int)$id;
        $paymentStatus = (string)Request::get('payment_status', '');

        $validStatuses = ['pending', 'completed', 'failed'];
        if (!in_array($paymentStatus, $validStatuses, true)) {
            Session::setFlash('error', 'Invalid payment status selected.');
            Response::redirect('/owner/orders');
        }

        $this->orderRepo->updatePaymentStatusForRestaurant($orderId, $restaurantId, $paymentStatus);

        Session::setFlash('success', "Order #{$orderId} payment status updated to " . ucfirst($paymentStatus) . ".");
        Response::redirect('/owner/orders');
    }

    public function details(string|int $id): void
    {
        $authContext  = RestaurantMiddleware::handle();
        $restaurantId = (int)$authContext['restaurant_id'];
        $orderId      = (int)$id;

        $order = $this->orderRepo->findWithItemsById($orderId, $restaurantId);
        if (!$order) {
            Response::json(['success' => false, 'error' => 'Order not found.'], 404);
        }

        Response::json(['success' => true, 'order' => $order]);
    }
}
