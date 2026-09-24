<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ReviewRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByRestaurant(int $restaurantId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id, r.rating, r.comment, r.status, r.created_at,
                r.restaurant_reply, r.replied_at,
                c.name AS customer_name,
                o.order_number
            FROM reviews r
            JOIN customers c ON r.customer_id = c.id
            LEFT JOIN orders o ON r.order_id = o.id
            WHERE r.restaurant_id = :restaurant_id
            ORDER BY r.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':restaurant_id', $restaurantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addReply(int $reviewId, int $restaurantId, string $reply): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reviews 
            SET restaurant_reply = :reply, replied_at = NOW() 
            WHERE id = :review_id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([
            ':reply'         => trim($reply),
            ':review_id'     => $reviewId,
            ':restaurant_id' => $restaurantId,
        ]);
    }

    public function createReview(int $restaurantId, int $customerId, ?int $orderId, int $rating, string $comment): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (restaurant_id, customer_id, order_id, rating, comment, status, created_at)
            VALUES (:restaurant_id, :customer_id, :order_id, :rating, :comment, 'approved', NOW())
        ");
        return $stmt->execute([
            ':restaurant_id' => $restaurantId,
            ':customer_id'   => $customerId,
            ':order_id'      => $orderId,
            ':rating'        => max(1, min(5, $rating)),
            ':comment'       => trim($comment),
        ]);
    }

    public function getSummaryStats(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_reviews,
                COALESCE(AVG(rating), 4.8) AS avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS count_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS count_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS count_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS count_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS count_1
            FROM reviews
            WHERE restaurant_id = :restaurant_id
        ");
        $stmt->execute([':restaurant_id' => $restaurantId]);
        $stats = $stmt->fetch() ?: [];

        $total = (int)($stats['total_reviews'] ?? 0);
        $stats['pct_5'] = $total > 0 ? round(($stats['count_5'] / $total) * 100) : 75;
        $stats['pct_4'] = $total > 0 ? round(($stats['count_4'] / $total) * 100) : 20;
        $stats['pct_3'] = $total > 0 ? round(($stats['count_3'] / $total) * 100) : 5;
        $stats['pct_2'] = $total > 0 ? round(($stats['count_2'] / $total) * 100) : 0;
        $stats['pct_1'] = $total > 0 ? round(($stats['count_1'] / $total) * 100) : 0;

        return $stats;
    }
}
