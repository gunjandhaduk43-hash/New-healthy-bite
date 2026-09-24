<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function simulate(): void
    {
        $body = Request::getBody();
        $orderId = (int)($body['order_id'] ?? 0);
        $method = (string)($body['payment_method'] ?? 'cash');

        try {
            $result = $this->paymentService->simulatePayment($orderId, $method);
            $this->json([
                'status'  => 'success',
                'message' => 'Payment simulated successfully.',
                'data'    => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
