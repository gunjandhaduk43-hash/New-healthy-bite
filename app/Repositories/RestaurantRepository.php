<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class RestaurantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, slug, logo, cover_image, description, phone, email, address, city, state, status
            FROM restaurants
            WHERE id = :id AND status = 'approved'
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, slug, logo, cover_image, description, phone, email, address, city, state, status
            FROM restaurants
            WHERE slug = :slug AND status = 'approved'
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBranch(int $restaurantId, ?int $branchId = null): ?array
    {
        if ($branchId !== null) {
            $stmt = $this->db->prepare("
                SELECT id, restaurant_id, name, address, city, state, phone, status
                FROM branches
                WHERE id = :branch_id AND restaurant_id = :restaurant_id AND status = 'active'
                LIMIT 1
            ");
            $stmt->execute(['branch_id' => $branchId, 'restaurant_id' => $restaurantId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT id, restaurant_id, name, address, city, state, phone, status
                FROM branches
                WHERE restaurant_id = :restaurant_id AND status = 'active'
                ORDER BY id ASC
                LIMIT 1
            ");
            $stmt->execute(['restaurant_id' => $restaurantId]);
        }

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAnyById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, owner_user_id, name, slug, logo, cover_image, description, phone, email, address, city, state, status, created_at
            FROM restaurants
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAll(int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id, r.owner_user_id, r.name, r.slug, r.logo, r.phone, r.email, r.city, r.status, r.created_at,
                u.name AS owner_name, u.email AS owner_email,
                (SELECT COUNT(*) FROM branches b WHERE b.restaurant_id = r.id) AS branch_count,
                (SELECT COUNT(*) FROM orders o WHERE o.restaurant_id = r.id) AS order_count,
                (SELECT COALESCE(SUM(o.total_amount), 0) FROM orders o WHERE o.restaurant_id = r.id) AS total_revenue
            FROM restaurants r
            LEFT JOIN users u ON r.owner_user_id = u.id
            ORDER BY r.id ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE restaurants SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM restaurants");
        return (int)$stmt->fetchColumn();
    }

    public function updateDetails(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        $allowed = ['name', 'description', 'phone', 'email', 'address', 'city', 'state', 'logo', 'cover_image'];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE restaurants SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getPlatformOverviewStats(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total_restaurants,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS active_restaurants,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_approvals,
                (SELECT COUNT(*) FROM orders) AS total_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders) AS total_gmv,
                (SELECT COUNT(*) FROM users) AS registered_accounts
            FROM restaurants
        ");
        return $stmt->fetch() ?: [
            'total_restaurants'    => 0,
            'active_restaurants'   => 0,
            'pending_approvals'    => 0,
            'total_orders'         => 0,
            'total_gmv'            => 0,
            'registered_accounts'  => 0
        ];
    }

    public function getInspectionDetails(int $id): ?array
    {
        $restaurant = $this->findAnyById($id);
        if (!$restaurant) return null;

        $stmtBranches = $this->db->prepare("
            SELECT b.*, (SELECT COUNT(*) FROM restaurant_tables t WHERE t.branch_id = b.id) AS table_count
            FROM branches b
            WHERE b.restaurant_id = :id
        ");
        $stmtBranches->execute([':id' => $id]);
        $restaurant['branches_list'] = $stmtBranches->fetchAll();
        $restaurant['branches_count'] = count($restaurant['branches_list']);

        $stmtTables = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_tables,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_tables
            FROM restaurant_tables
            WHERE restaurant_id = :id
        ");
        $stmtTables->execute([':id' => $id]);
        $restaurant['table_stats'] = $stmtTables->fetch() ?: ['total_tables' => 0, 'available_tables' => 0];

        $stmtMenu = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_items,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) AS available_items,
                COUNT(DISTINCT category_id) AS category_count
            FROM food_items
            WHERE restaurant_id = :id
        ");
        $stmtMenu->execute([':id' => $id]);
        $restaurant['menu_stats'] = $stmtMenu->fetch() ?: ['total_items' => 0, 'available_items' => 0, 'category_count' => 0];

        $stmtSales = $this->db->prepare("
            SELECT 
                COALESCE(SUM(total_amount), 0) AS monthly_sales,
                COUNT(*) AS total_orders
            FROM orders
            WHERE restaurant_id = :id
        ");
        $stmtSales->execute([':id' => $id]);
        $restaurant['sales_stats'] = $stmtSales->fetch() ?: ['monthly_sales' => 0, 'total_orders' => 0];

        $stmtReviews = $this->db->prepare("
            SELECT 
                COALESCE(AVG(rating), 4.8) AS avg_rating,
                COUNT(*) AS total_reviews
            FROM reviews
            WHERE restaurant_id = :id
        ");
        $stmtReviews->execute([':id' => $id]);
        $restaurant['review_stats'] = $stmtReviews->fetch() ?: ['avg_rating' => 4.8, 'total_reviews' => 0];

        return $restaurant;
    }
}
