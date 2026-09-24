<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function createOrder(): void
    {
        $payload = Request::getBody();

        try {
            $order = $this->orderService->placeOrder($payload);
            $this->json([
                'status'  => 'success',
                'message' => 'Order placed successfully.',
                'data'    => $order,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getOrder(string $orderNumber): void
    {
        $order = $this->orderService->trackOrder($orderNumber);
        if (!$order) {
            $this->json(['error' => 'Order not found'], 404);
            return;
        }

        $this->json(['data' => $order]);
    }

    public function confirmation(string $orderNumber): void
    {
        $order = $this->orderService->trackOrder($orderNumber);
        if (!$order) {
            echo $this->render('customer/errors/404', ['path' => "/menu/confirmation/{$orderNumber}"], 'minimal');
            return;
        }

        echo $this->render('customer/confirmation', [
            'order' => $order,
            'title' => "Order Confirmation #{$orderNumber} — Healthy Bite"
        ]);
    }

    public function tracking(string $orderNumber): void
    {
        $order = $this->orderService->trackOrder($orderNumber);
        if (!$order) {
            echo $this->render('customer/errors/404', ['path' => "/menu/tracking/{$orderNumber}"], 'minimal');
            return;
        }

        echo $this->render('customer/tracking', [
            'order' => $order,
            'title' => "Live Order Tracking #{$orderNumber} — Healthy Bite"
        ]);
    }

    public function getLatestTableOrder(): void
    {
        $tableId = (int)($_GET['table_id'] ?? 0);
        $restaurantId = (int)($_GET['restaurant_id'] ?? 1);

        if ($tableId <= 0) {
            $this->json(['status' => 'error', 'message' => 'table_id required'], 400);
            return;
        }

        $orderRepo = new \App\Repositories\OrderRepository();
        $order = $orderRepo->findLatestActiveByTable($tableId, $restaurantId);

        $this->json([
            'status' => 'success',
            'data'   => $order
        ]);
    }

    public function submitReview(): void
    {
        $payload = Request::getBody();
        $orderNumber = trim((string)($payload['order_number'] ?? ''));
        $rating = (int)($payload['rating'] ?? 5);
        $comment = trim((string)($payload['comment'] ?? ''));

        if ($orderNumber === '') {
            $this->json(['status' => 'error', 'message' => 'Order number is required'], 400);
            return;
        }

        $order = $this->orderService->trackOrder($orderNumber);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Order not found'], 404);
            return;
        }

        $reviewRepo = new \App\Repositories\ReviewRepository();
        $success = $reviewRepo->createReview(
            (int)$order['restaurant_id'],
            (int)$order['customer_id'],
            (int)$order['id'],
            $rating,
            $comment !== '' ? $comment : 'Verified dining experience.'
        );

        if ($success) {
            $this->json([
                'status'  => 'success',
                'message' => 'Thank you for your review!'
            ]);
        } else {
            $this->json([
                'status'  => 'error',
                'message' => 'Could not save review. Please try again.'
            ], 500);
        }
    }
}
