<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function createCustomer(string $name, ?string $mobile = null, ?string $email = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO customers (name, mobile, email)
            VALUES (:name, :mobile, :email)
        ");
        $stmt->execute([
            'name'   => $name,
            'mobile' => $mobile,
            'email'  => $email,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function createOrder(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (
                order_number, restaurant_id, branch_id, table_id, customer_id,
                order_type, subtotal, tax, service_charge, total_amount,
                payment_status, order_status, notes
            ) VALUES (
                :order_number, :restaurant_id, :branch_id, :table_id, :customer_id,
                :order_type, :subtotal, :tax, :service_charge, :total_amount,
                :payment_status, :order_status, :notes
            )
        ");

        $stmt->execute([
            'order_number'   => $data['order_number'],
            'restaurant_id'  => $data['restaurant_id'],
            'branch_id'      => $data['branch_id'],
            'table_id'       => $data['table_id'] ?? null,
            'customer_id'    => $data['customer_id'],
            'order_type'     => $data['order_type'],
            'subtotal'       => $data['subtotal'],
            'tax'            => $data['tax'],
            'service_charge' => $data['service_charge'] ?? 0.00,
            'total_amount'   => $data['total_amount'],
            'payment_status' => $data['payment_status'] ?? 'pending',
            'order_status'   => $data['order_status'] ?? 'placed',
            'notes'          => $data['notes'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function addOrderItem(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO order_items (
                order_id, food_item_id, food_name_snapshot, base_price_snapshot,
                variant_name_snapshot, variant_price_snapshot, quantity, unit_price, total_price,
                calories, protein, carbs, fat, fiber, sugar, sodium, caffeine
            ) VALUES (
                :order_id, :food_item_id, :food_name_snapshot, :base_price_snapshot,
                :variant_name_snapshot, :variant_price_snapshot, :quantity, :unit_price, :total_price,
                :calories, :protein, :carbs, :fat, :fiber, :sugar, :sodium, :caffeine
            )
        ");

        $stmt->execute([
            'order_id'               => $data['order_id'],
            'food_item_id'           => $data['food_item_id'],
            'food_name_snapshot'     => $data['food_name_snapshot'],
            'base_price_snapshot'    => $data['base_price_snapshot'],
            'variant_name_snapshot'  => $data['variant_name_snapshot'] ?? null,
            'variant_price_snapshot' => $data['variant_price_snapshot'] ?? 0.00,
            'quantity'               => $data['quantity'],
            'unit_price'             => $data['unit_price'],
            'total_price'            => $data['total_price'],
            'calories'               => $data['calories'] ?? null,
            'protein'                => $data['protein'] ?? null,
            'carbs'                  => $data['carbs'] ?? null,
            'fat'                    => $data['fat'] ?? null,
            'fiber'                  => $data['fiber'] ?? null,
            'sugar'                  => $data['sugar'] ?? null,
            'sodium'                 => $data['sodium'] ?? null,
            'caffeine'               => $data['caffeine'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function addOrderItemCustomization(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO order_item_customizations (
                order_item_id, customization_id, customization_name_snapshot,
                quantity, price_adjustment, calories_adjustment, protein_adjustment,
                carbs_adjustment, fat_adjustment, fiber_adjustment, sugar_adjustment,
                sodium_adjustment, caffeine_adjustment
            ) VALUES (
                :order_item_id, :customization_id, :customization_name_snapshot,
                :quantity, :price_adjustment, :calories_adjustment, :protein_adjustment,
                :carbs_adjustment, :fat_adjustment, :fiber_adjustment, :sugar_adjustment,
                :sodium_adjustment, :caffeine_adjustment
            )
        ");

        $stmt->execute([
            'order_item_id'               => $data['order_item_id'],
            'customization_id'            => $data['customization_id'],
            'customization_name_snapshot' => $data['customization_name_snapshot'],
            'quantity'                    => $data['quantity'],
            'price_adjustment'            => $data['price_adjustment'],
            'calories_adjustment'         => $data['calories_adjustment'] ?? null,
            'protein_adjustment'          => $data['protein_adjustment'] ?? null,
            'carbs_adjustment'            => $data['carbs_adjustment'] ?? null,
            'fat_adjustment'              => $data['fat_adjustment'] ?? null,
            'fiber_adjustment'            => $data['fiber_adjustment'] ?? null,
            'sugar_adjustment'            => $data['sugar_adjustment'] ?? null,
            'sodium_adjustment'           => $data['sodium_adjustment'] ?? null,
            'caffeine_adjustment'         => $data['caffeine_adjustment'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findByOrderNumber(string $orderNumber): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.restaurant_id, o.branch_id, o.table_id, o.customer_id,
                o.order_type, o.subtotal, o.tax, o.service_charge, o.total_amount,
                o.payment_status, o.order_status, o.notes, o.created_at,
                r.name AS restaurant_name, r.slug AS restaurant_slug,
                b.name AS branch_name,
                t.table_number,
                c.name AS customer_name, c.mobile AS customer_mobile, c.email AS customer_email
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            JOIN branches b ON o.branch_id = b.id
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.order_number = :order_number
            LIMIT 1
        ");
        $stmt->execute(['order_number' => $orderNumber]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Fetch order items
        $stmt = $this->db->prepare("
            SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC
        ");
        $stmt->execute(['order_id' => $order['id']]);
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $stmtCustom = $this->db->prepare("
                SELECT * FROM order_item_customizations WHERE order_item_id = :item_id ORDER BY id ASC
            ");
            $stmtCustom->execute(['item_id' => $item['id']]);
            $item['customizations'] = $stmtCustom->fetchAll();
        }

        $order['items'] = $items;
        return $order;
    }

    public function findWithItemsById(int $orderId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.order_type, o.subtotal, o.tax, o.service_charge, o.total_amount,
                o.payment_status, o.order_status, o.notes, o.created_at,
                t.table_number,
                c.name AS customer_name, c.mobile AS customer_mobile
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.id = :order_id AND o.restaurant_id = :restaurant_id
            LIMIT 1
        ");
        $stmt->execute(['order_id' => $orderId, 'restaurant_id' => $restaurantId]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }

        $stmtItems = $this->db->prepare("
            SELECT id, food_name_snapshot, quantity, unit_price, total_price, calories, protein
            FROM order_items 
            WHERE order_id = :order_id 
            ORDER BY id ASC
        ");
        $stmtItems->execute(['order_id' => $orderId]);
        $order['items'] = $stmtItems->fetchAll();

        return $order;
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE orders SET order_status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $orderId]);
    }

    public function getTodayStats(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue,
                SUM(CASE WHEN order_status IN ('placed', 'accepted', 'preparing', 'ready') THEN 1 ELSE 0 END) AS active_orders,
                SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) AS completed_orders
            FROM orders
            WHERE restaurant_id = :restaurant_id
              AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        $today = $stmt->fetch() ?: [];

        // All-time stats as fallback/summary
        $stmtAll = $this->db->prepare("
            SELECT 
                COUNT(*) AS all_time_orders,
                COALESCE(SUM(total_amount), 0) AS all_time_revenue
            FROM orders
            WHERE restaurant_id = :restaurant_id
        ");
        $stmtAll->execute(['restaurant_id' => $restaurantId]);
        $allTime = $stmtAll->fetch() ?: [];
        $totalOrders = (int)($allTime['all_time_orders'] ?? 0);
        $totalRevenue = (float)($allTime['all_time_revenue'] ?? 0);
        $allTime['aov'] = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0.00;

        return array_merge($today, $allTime);
    }

    public function findByRestaurant(int $restaurantId, ?string $status = null, int $limit = 100): array
    {
        $sql = "
            SELECT 
                o.id, o.order_number, o.restaurant_id, o.branch_id, o.table_id, o.customer_id,
                o.order_type, o.subtotal, o.tax, o.service_charge, o.total_amount,
                o.payment_status, o.order_status, o.notes, o.created_at,
                t.table_number,
                c.name AS customer_name, c.mobile AS customer_mobile,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
                (SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.food_name_snapshot) SEPARATOR ', ') 
                 FROM order_items oi WHERE oi.order_id = o.id) AS items_summary
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.restaurant_id = :restaurant_id
        ";

        $params = ['restaurant_id' => $restaurantId];

        if ($status && $status !== 'all') {
            $sql .= " AND o.order_status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByIdAndRestaurant(int $orderId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.restaurant_id, o.branch_id, o.table_id, o.customer_id,
                o.order_type, o.subtotal, o.tax, o.service_charge, o.total_amount,
                o.payment_status, o.order_status, o.notes, o.created_at,
                r.name AS restaurant_name,
                b.name AS branch_name,
                t.table_number,
                c.name AS customer_name, c.mobile AS customer_mobile, c.email AS customer_email
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            JOIN branches b ON o.branch_id = b.id
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.id = :order_id AND o.restaurant_id = :restaurant_id
            LIMIT 1
        ");
        $stmt->execute(['order_id' => $orderId, 'restaurant_id' => $restaurantId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $stmtItems = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC");
        $stmtItems->execute(['order_id' => $orderId]);
        $items = $stmtItems->fetchAll();

        foreach ($items as &$item) {
            $stmtCust = $this->db->prepare("SELECT * FROM order_item_customizations WHERE order_item_id = :item_id");
            $stmtCust->execute(['item_id' => $item['id']]);
            $item['customizations'] = $stmtCust->fetchAll();
        }

        $order['items'] = $items;
        return $order;
    }

    public function updateStatusForRestaurant(int $orderId, int $restaurantId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET order_status = :status 
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([
            'status'        => $status,
            'id'            => $orderId,
            'restaurant_id' => $restaurantId
        ]);
    }

    public function updatePaymentStatusForRestaurant(int $orderId, int $restaurantId, string $paymentStatus): bool
    {
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET payment_status = :payment_status 
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([
            'payment_status' => $paymentStatus,
            'id'             => $orderId,
            'restaurant_id'  => $restaurantId
        ]);
    }

    public function getPlatformStats(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue,
                SUM(CASE WHEN order_status IN ('placed', 'accepted', 'preparing', 'ready') THEN 1 ELSE 0 END) AS active_orders,
                SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) AS completed_orders
            FROM orders
        ");
        return $stmt->fetch() ?: [];
    }

    public function getAllOrders(int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.restaurant_id, o.branch_id, o.table_id, o.customer_id,
                o.order_type, o.subtotal, o.tax, o.service_charge, o.total_amount,
                o.payment_status, o.order_status, o.created_at,
                r.name AS restaurant_name,
                c.name AS customer_name, c.mobile AS customer_mobile
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            JOIN customers c ON o.customer_id = c.id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findLatestActiveByTable(int $tableId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT o.id, o.order_number, o.order_status, o.total_amount, o.created_at, o.payment_status
            FROM orders o
            WHERE o.table_id = :table_id 
              AND o.restaurant_id = :restaurant_id
              AND o.order_status IN ('placed', 'accepted', 'preparing', 'ready')
            ORDER BY o.created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['table_id' => $tableId, 'restaurant_id' => $restaurantId]);
        $order = $stmt->fetch();
        if (!$order) {
            $stmtPast = $this->db->prepare("
                SELECT o.id, o.order_number, o.order_status, o.total_amount, o.created_at, o.payment_status
                FROM orders o
                WHERE o.table_id = :table_id 
                  AND o.restaurant_id = :restaurant_id
                  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 45 MINUTE)
                ORDER BY o.created_at DESC
                LIMIT 1
            ");
            $stmtPast->execute(['table_id' => $tableId, 'restaurant_id' => $restaurantId]);
            $order = $stmtPast->fetch();
            if (!$order) {
                return null;
            }
        }

        $stmtItems = $this->db->prepare("SELECT food_name_snapshot, quantity, unit_price, total_price FROM order_items WHERE order_id = :order_id");
        $stmtItems->execute(['order_id' => $order['id']]);
        $order['items'] = $stmtItems->fetchAll();

        return $order;
    }

    public function getPopularItems(int $restaurantId, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                oi.food_item_id,
                oi.food_name_snapshot AS name,
                fi.image,
                SUM(oi.quantity) AS total_sold,
                SUM(oi.total_price) AS total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            LEFT JOIN food_items fi ON oi.food_item_id = fi.id
            WHERE o.restaurant_id = :restaurant_id
            GROUP BY oi.food_item_id, oi.food_name_snapshot, fi.image
            ORDER BY total_sold DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':restaurant_id', $restaurantId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getKitchenOrders(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.order_type, o.subtotal, o.tax, o.total_amount,
                o.order_status, o.payment_status, o.notes, o.created_at,
                t.table_number,
                c.name AS customer_name
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.restaurant_id = :restaurant_id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $stmtItems = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
            $stmtItems->execute(['order_id' => $order['id']]);
            $items = $stmtItems->fetchAll();

            foreach ($items as &$item) {
                $stmtCustom = $this->db->prepare("SELECT * FROM order_item_customizations WHERE order_item_id = :item_id");
                $stmtCustom->execute(['item_id' => $item['id']]);
                $item['customizations'] = $stmtCustom->fetchAll();
            }
            $order['items'] = $items;
        }

        return $orders;
    }

    public function getMacroStats(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(oi.calories * oi.quantity), 0) AS total_calories,
                COALESCE(SUM(oi.protein * oi.quantity), 0) AS total_protein,
                COALESCE(SUM(oi.carbs * oi.quantity), 0) AS total_carbs,
                COALESCE(SUM(oi.fat * oi.quantity), 0) AS total_fat,
                COALESCE(SUM(oi.sugar * oi.quantity), 0) AS total_sugar,
                COALESCE(AVG(oi.calories), 0) AS avg_calories,
                COALESCE(AVG(oi.protein), 0) AS avg_protein,
                COALESCE(AVG(oi.sugar), 0) AS avg_sugar
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.restaurant_id = :restaurant_id
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        return $stmt->fetch() ?: [
            'total_calories' => 0,
            'total_protein'  => 0,
            'total_carbs'    => 0,
            'total_fat'      => 0,
            'total_sugar'    => 0,
            'avg_calories'   => 0,
            'avg_protein'    => 0,
            'avg_sugar'      => 0
        ];
    }

    public function getPaymentStats(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(p.payment_method, 'upi') AS payment_method,
                COUNT(*) AS count,
                COALESCE(SUM(p.amount), 0) AS total
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            WHERE o.restaurant_id = :restaurant_id
            GROUP BY p.payment_method
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        $rows = $stmt->fetchAll();

        // Calculate total amount across all payments for percentage
        $grandTotal = array_sum(array_column($rows, 'total'));
        foreach ($rows as &$r) {
            $r['percentage'] = $grandTotal > 0 ? round(($r['total'] / $grandTotal) * 100, 1) : 0;
        }
        return $rows;
    }

    public function getFilteredAnalytics(int $restaurantId, string $period = 'month', ?string $startDate = null, ?string $endDate = null): array
    {
        $now = new \DateTimeImmutable();
        if ($period === 'today') {
            $start = $now->format('Y-m-d 00:00:00');
            $end   = $now->format('Y-m-d 23:59:59');
        } elseif ($period === 'week') {
            $start = $now->modify('-6 days')->format('Y-m-d 00:00:00');
            $end   = $now->format('Y-m-d 23:59:59');
        } elseif ($period === 'custom' && !empty($startDate) && !empty($endDate)) {
            $s = \DateTimeImmutable::createFromFormat('Y-m-d', substr(trim($startDate), 0, 10));
            $e = \DateTimeImmutable::createFromFormat('Y-m-d', substr(trim($endDate), 0, 10));
            $start = $s ? $s->format('Y-m-d 00:00:00') : $now->modify('-29 days')->format('Y-m-d 00:00:00');
            $end   = $e ? $e->format('Y-m-d 23:59:59') : $now->format('Y-m-d 23:59:59');
            if ($start > $end) {
                $tmp = $start; $start = $end; $end = $tmp;
            }
        } else {
            $period = 'month';
            $start  = $now->modify('-29 days')->format('Y-m-d 00:00:00');
            $end    = $now->format('Y-m-d 23:59:59');
        }

        // Summary KPI
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_orders,
                COALESCE(SUM(total_amount), 0) AS total_revenue,
                COALESCE(SUM(subtotal), 0) AS net_sales,
                SUM(CASE WHEN order_status IN ('placed', 'accepted', 'preparing', 'ready') THEN 1 ELSE 0 END) AS active_orders,
                SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) AS completed_orders
            FROM orders
            WHERE restaurant_id = :restaurant_id
              AND created_at BETWEEN :start_date AND :end_date
        ");
        $stmt->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $summary = $stmt->fetch() ?: [];
        $totalOrders = (int)($summary['total_orders'] ?? 0);
        $totalRevenue = (float)($summary['total_revenue'] ?? 0);
        $summary['aov'] = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0.00;

        // Macro & Nutrition Query
        $stmtMacro = $this->db->prepare("
            SELECT 
                COALESCE(SUM(oi.calories * oi.quantity), 0) AS total_calories,
                COALESCE(SUM(oi.protein * oi.quantity), 0) AS total_protein,
                COALESCE(SUM(oi.carbs * oi.quantity), 0) AS total_carbs,
                COALESCE(SUM(oi.fat * oi.quantity), 0) AS total_fat,
                COALESCE(SUM(oi.sugar * oi.quantity), 0) AS total_sugar,
                COALESCE(SUM(oi.caffeine * oi.quantity), 0) AS total_caffeine,
                COALESCE(AVG(oi.calories), 0) AS avg_calories,
                COALESCE(AVG(oi.protein), 0) AS avg_protein,
                COALESCE(AVG(oi.sugar), 0) AS avg_sugar,
                COALESCE(AVG(oi.caffeine), 0) AS avg_caffeine
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.restaurant_id = :restaurant_id
              AND o.created_at BETWEEN :start_date AND :end_date
        ");
        $stmtMacro->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $macroStats = $stmtMacro->fetch() ?: [
            'total_calories' => 0, 'total_protein' => 0, 'total_carbs' => 0,
            'total_fat' => 0, 'total_sugar' => 0, 'total_caffeine' => 0,
            'avg_calories' => 0, 'avg_protein' => 0, 'avg_sugar' => 0, 'avg_caffeine' => 0
        ];

        // Payment stats
        $stmtPayment = $this->db->prepare("
            SELECT 
                COALESCE(p.payment_method, 'UPI') AS payment_method,
                COUNT(DISTINCT o.id) AS count,
                COALESCE(SUM(o.total_amount), 0) AS total
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id
            WHERE o.restaurant_id = :restaurant_id
              AND o.created_at BETWEEN :start_date AND :end_date
            GROUP BY payment_method
        ");
        $stmtPayment->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $paymentRows = $stmtPayment->fetchAll();
        $grandPaymentTotal = array_sum(array_column($paymentRows, 'total'));
        foreach ($paymentRows as &$pr) {
            $pr['percentage'] = $grandPaymentTotal > 0 ? round(($pr['total'] / $grandPaymentTotal) * 100, 1) : 0;
        }

        // Daily trends
        $stmtTrend = $this->db->prepare("
            SELECT 
                DATE(created_at) AS order_date,
                COUNT(*) AS order_count,
                COALESCE(SUM(total_amount), 0) AS daily_revenue
            FROM orders
            WHERE restaurant_id = :restaurant_id
              AND created_at BETWEEN :start_date AND :end_date
            GROUP BY DATE(created_at)
            ORDER BY order_date ASC
        ");
        $stmtTrend->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $trends = $stmtTrend->fetchAll();

        // Top-selling items
        $stmtTop = $this->db->prepare("
            SELECT 
                oi.food_item_id,
                oi.food_name_snapshot AS name,
                fi.image,
                SUM(oi.quantity) AS total_sold,
                SUM(oi.total_price) AS total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            LEFT JOIN food_items fi ON oi.food_item_id = fi.id
            WHERE o.restaurant_id = :restaurant_id
              AND o.created_at BETWEEN :start_date AND :end_date
            GROUP BY oi.food_item_id, oi.food_name_snapshot, fi.image
            ORDER BY total_sold DESC
            LIMIT 5
        ");
        $stmtTop->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $topItems = $stmtTop->fetchAll();

        // Orders list for export and detail inspection
        $stmtOrders = $this->db->prepare("
            SELECT 
                o.id, o.order_number, o.created_at, o.order_type, o.order_status, o.payment_status,
                o.subtotal, o.tax, o.service_charge, o.total_amount,
                t.table_number, c.name AS customer_name, c.mobile AS customer_mobile,
                (SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.food_name_snapshot) SEPARATOR ', ')
                 FROM order_items oi WHERE oi.order_id = o.id) AS items_summary
            FROM orders o
            LEFT JOIN restaurant_tables t ON o.table_id = t.id
            JOIN customers c ON o.customer_id = c.id
            WHERE o.restaurant_id = :restaurant_id
              AND o.created_at BETWEEN :start_date AND :end_date
            ORDER BY o.created_at DESC
        ");
        $stmtOrders->execute([
            'restaurant_id' => $restaurantId,
            'start_date'    => $start,
            'end_date'      => $end
        ]);
        $ordersList = $stmtOrders->fetchAll();

        return [
            'period'        => $period,
            'start_date'    => substr($start, 0, 10),
            'end_date'      => substr($end, 0, 10),
            'summary'       => $summary,
            'macro_stats'   => $macroStats,
            'payment_stats' => $paymentRows,
            'trends'        => $trends,
            'top_items'     => $topItems,
            'orders'        => $ordersList
        ];
    }
}
