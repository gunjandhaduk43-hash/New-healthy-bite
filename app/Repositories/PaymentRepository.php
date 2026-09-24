<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class PaymentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function createPayment(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO payments (
                order_id, payment_method, transaction_reference, amount, status, paid_at
            ) VALUES (
                :order_id, :payment_method, :transaction_reference, :amount, :status, :paid_at
            )
        ");

        $stmt->execute([
            'order_id'              => $data['order_id'],
            'payment_method'        => $data['payment_method'],
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'amount'                => $data['amount'],
            'status'                => $data['status'] ?? 'pending',
            'paid_at'               => $data['paid_at'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findByOrderId(int $orderId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, order_id, payment_method, transaction_reference, amount, status, paid_at, created_at
            FROM payments
            WHERE order_id = :order_id
            LIMIT 1
        ");
        $stmt->execute(['order_id' => $orderId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
