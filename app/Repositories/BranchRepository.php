<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class BranchRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getTables(int $branchId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, restaurant_id, branch_id, table_number, status
            FROM restaurant_tables
            WHERE branch_id = :branch_id
            ORDER BY table_number ASC
        ");
        $stmt->execute(['branch_id' => $branchId]);
        return $stmt->fetchAll();
    }

    public function getTableById(int $tableId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, restaurant_id, branch_id, table_number, status
            FROM restaurant_tables
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $tableId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBranchesByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, restaurant_id, name, address, city, state, phone, status
            FROM branches
            WHERE restaurant_id = :restaurant_id
            ORDER BY id ASC
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function getTablesWithQr(int $restaurantId, ?int $branchId = null): array
    {
        $sql = "
            SELECT 
                t.id, t.restaurant_id, t.branch_id, t.table_number, t.status,
                b.name AS branch_name,
                q.token AS qr_token, q.status AS qr_status,
                (SELECT COUNT(*) FROM orders o WHERE o.table_id = t.id AND o.order_status IN ('placed', 'accepted', 'preparing', 'ready')) AS active_orders_count
            FROM restaurant_tables t
            JOIN branches b ON t.branch_id = b.id
            LEFT JOIN qr_tokens q ON q.table_id = t.id AND q.status = 'active'
            WHERE t.restaurant_id = :restaurant_id
        ";

        $params = ['restaurant_id' => $restaurantId];
        if ($branchId !== null) {
            $sql .= " AND t.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY b.id ASC, CAST(REGEXP_SUBSTR(t.table_number, '[0-9]+') AS UNSIGNED) ASC, t.table_number ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateTableStatus(int $tableId, int $restaurantId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE restaurant_tables 
            SET status = :status 
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([
            'status'        => $status,
            'id'            => $tableId,
            'restaurant_id' => $restaurantId,
        ]);
    }

    public function createTable(int $restaurantId, int $branchId, string $tableNumber): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO restaurant_tables (restaurant_id, branch_id, table_number, status)
            VALUES (:restaurant_id, :branch_id, :table_number, 'available')
        ");
        $stmt->execute([
            'restaurant_id' => $restaurantId,
            'branch_id'     => $branchId,
            'table_number'  => $tableNumber,
        ]);
        $tableId = (int)$this->db->lastInsertId();

        $this->ensureQrTokenForTable($restaurantId, $branchId, $tableId);
        return $tableId;
    }

    public function ensureQrTokenForTable(int $restaurantId, int $branchId, int $tableId): string
    {
        $stmt = $this->db->prepare("
            SELECT token FROM qr_tokens 
            WHERE table_id = :table_id AND status = 'active' 
            LIMIT 1
        ");
        $stmt->execute(['table_id' => $tableId]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (string)$existing;
        }

        $token = 'hb_token_' . bin2hex(random_bytes(16));
        $insert = $this->db->prepare("
            INSERT INTO qr_tokens (restaurant_id, branch_id, table_id, token, status)
            VALUES (:restaurant_id, :branch_id, :table_id, :token, 'active')
        ");
        $insert->execute([
            'restaurant_id' => $restaurantId,
            'branch_id'     => $branchId,
            'table_id'      => $tableId,
            'token'         => $token,
        ]);

        return $token;
    }

    public function countTablesByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_tables,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_tables,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) AS occupied_tables,
                SUM(CASE WHEN status = 'cleaning' THEN 1 ELSE 0 END) AS cleaning_tables,
                SUM(CASE WHEN status = 'out_of_service' THEN 1 ELSE 0 END) AS out_of_service_tables
            FROM restaurant_tables
            WHERE restaurant_id = :restaurant_id
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        return $stmt->fetch() ?: [
            'total_tables'          => 0,
            'available_tables'      => 0,
            'occupied_tables'       => 0,
            'cleaning_tables'       => 0,
            'out_of_service_tables' => 0
        ];
    }

    public function getFirstAvailableTable(int $restaurantId, ?int $branchId = null): ?array
    {
        $sql = "
            SELECT 
                t.id, t.restaurant_id, t.branch_id, t.table_number, t.status,
                q.token AS qr_token
            FROM restaurant_tables t
            LEFT JOIN qr_tokens q ON q.table_id = t.id AND q.status = 'active'
            WHERE t.restaurant_id = :restaurant_id
        ";
        $params = ['restaurant_id' => $restaurantId];

        if ($branchId !== null && $branchId > 0) {
            $sql .= " AND t.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY (t.status = 'available') DESC, t.id ASC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findTableByNumber(int $restaurantId, string $tableNumber): ?array
    {
        $cleanNumber = trim($tableNumber);
        $withPrefix = str_starts_with(strtolower($cleanNumber), 'table ') ? $cleanNumber : ('Table ' . $cleanNumber);
        $withoutPrefix = preg_replace('/^table\s+/i', '', $cleanNumber);

        $stmt = $this->db->prepare("
            SELECT 
                t.id, t.restaurant_id, t.branch_id, t.table_number, t.status,
                q.token AS qr_token
            FROM restaurant_tables t
            LEFT JOIN qr_tokens q ON q.table_id = t.id AND q.status = 'active'
            WHERE t.restaurant_id = :restaurant_id
              AND (
                  t.table_number = :exact 
                  OR t.table_number = :with_prefix 
                  OR t.table_number = :without_prefix
              )
            LIMIT 1
        ");
        $stmt->execute([
            'restaurant_id'   => $restaurantId,
            'exact'           => $cleanNumber,
            'with_prefix'     => $withPrefix,
            'without_prefix'  => $withoutPrefix
        ]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}

