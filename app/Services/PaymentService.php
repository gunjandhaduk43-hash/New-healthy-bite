<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\OrderRepository;
use App\Core\Database;

class PaymentService
{
    private PaymentRepository $paymentRepo;
    private OrderRepository $orderRepo;

    public function __construct()
    {
        $this->paymentRepo = new PaymentRepository();
        $this->orderRepo = new OrderRepository();
    }

    public function simulatePayment(int $orderId, string $method): array
    {
        $validMethods = ['cash', 'upi', 'card'];
        if (!in_array($method, $validMethods, true)) {
            throw new \InvalidArgumentException("Invalid payment method: {$method}");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, total_amount, payment_status, order_status, order_number FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            throw new \InvalidArgumentException("Order #{$orderId} not found.");
        }

        if ($order['order_status'] === 'cancelled') {
            throw new \InvalidArgumentException("Cannot process payment for a cancelled order.");
        }

        if ($order['payment_status'] === 'completed') {
            throw new \InvalidArgumentException("Order #{$order['order_number']} has already been paid.");
        }

        $reference = 'TXN-' . strtoupper($method) . '-' . strtoupper(bin2hex(random_bytes(6)));
        $now = date('Y-m-d H:i:s');

        $paymentId = $this->paymentRepo->createPayment([
            'order_id'              => $orderId,
            'payment_method'        => $method,
            'transaction_reference' => $reference,
            'amount'                => $order['total_amount'],
            'status'                => 'completed',
            'paid_at'               => $now,
        ]);

        // Advance order payment status and order status to accepted
        $stmt = $db->prepare("
            UPDATE orders 
            SET payment_status = 'completed', order_status = 'accepted', updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $orderId]);

        return [
            'payment_id'            => $paymentId,
            'order_number'          => $order['order_number'],
            'payment_method'        => $method,
            'transaction_reference' => $reference,
            'amount'                => (float)$order['total_amount'],
            'status'                => 'completed',
            'paid_at'               => $now,
        ];
    }
}
