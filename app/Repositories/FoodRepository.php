<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class FoodRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByRestaurantId(int $restaurantId, array $filters = []): array
    {
        $sql = "
            SELECT 
                f.id, f.restaurant_id, f.category_id, f.name, f.slug, f.description, f.image,
                f.ingredients, f.allergens, f.food_type, f.base_price,
                f.calories, f.protein, f.carbs, f.fat, f.fiber, f.sugar, f.sodium, f.caffeine,
                f.serving_size, f.is_featured, f.is_popular, f.is_available,
                c.name AS category_name, c.slug AS category_slug
            FROM food_items f
            JOIN categories c ON f.category_id = c.id
            WHERE f.restaurant_id = :restaurant_id
              AND f.is_available = 1
              AND c.status = 'active'
        ";

        $params = ['restaurant_id' => $restaurantId];

        if (!empty($filters['category_id'])) {
            $sql .= " AND f.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['food_type']) && $filters['food_type'] !== 'all') {
            $sql .= " AND f.food_type = :food_type";
            $params['food_type'] = $filters['food_type'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                f.name LIKE :search1 
                OR f.description LIKE :search2 
                OR f.ingredients LIKE :search3
                OR c.name LIKE :search4
            )";
            $searchVal = '%' . trim((string)$filters['search']) . '%';
            $params['search1'] = $searchVal;
            $params['search2'] = $searchVal;
            $params['search3'] = $searchVal;
            $params['search4'] = $searchVal;
        }

        $sql .= " ORDER BY c.sort_order ASC, f.is_featured DESC, f.is_popular DESC, f.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByIdAndRestaurant(int $foodId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                f.id, f.restaurant_id, f.category_id, f.name, f.slug, f.description, f.image,
                f.ingredients, f.allergens, f.food_type, f.base_price,
                f.calories, f.protein, f.carbs, f.fat, f.fiber, f.sugar, f.sodium, f.caffeine,
                f.serving_size, f.is_featured, f.is_popular, f.is_available,
                c.name AS category_name, c.slug AS category_slug
            FROM food_items f
            JOIN categories c ON f.category_id = c.id
            WHERE f.id = :food_id AND f.restaurant_id = :restaurant_id
            LIMIT 1
        ");
        $stmt->execute(['food_id' => $foodId, 'restaurant_id' => $restaurantId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getVariants(int $foodId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                id, food_item_id, name, description, price_adjustment,
                calories_adjustment, protein_adjustment, carbs_adjustment, fat_adjustment,
                fiber_adjustment, sugar_adjustment, sodium_adjustment, caffeine_adjustment,
                is_required, is_available, sort_order
            FROM food_variants
            WHERE food_item_id = :food_item_id AND is_available = 1
            ORDER BY sort_order ASC, price_adjustment ASC
        ");
        $stmt->execute(['food_item_id' => $foodId]);
        return $stmt->fetchAll();
    }

    public function getCustomizations(int $foodId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                id, food_item_id, name, description, group_name, price_adjustment,
                calories_adjustment, protein_adjustment, carbs_adjustment, fat_adjustment,
                fiber_adjustment, sugar_adjustment, sodium_adjustment, caffeine_adjustment,
                is_required, min_quantity, max_quantity, is_available, sort_order
            FROM food_customizations
            WHERE food_item_id = :food_item_id AND is_available = 1
            ORDER BY group_name ASC, sort_order ASC, price_adjustment ASC
        ");
        $stmt->execute(['food_item_id' => $foodId]);
        return $stmt->fetchAll();
    }

    public function getAllByRestaurantId(int $restaurantId, array $filters = []): array
    {
        $sql = "
            SELECT 
                f.id, f.restaurant_id, f.category_id, f.name, f.slug, f.description, f.image,
                f.ingredients, f.allergens, f.food_type, f.base_price,
                f.calories, f.protein, f.carbs, f.fat, f.fiber, f.sugar, f.sodium, f.caffeine,
                f.serving_size, f.is_featured, f.is_popular, f.is_available,
                c.name AS category_name, c.slug AS category_slug
            FROM food_items f
            JOIN categories c ON f.category_id = c.id
            WHERE f.restaurant_id = :restaurant_id
        ";

        $params = ['restaurant_id' => $restaurantId];

        if (!empty($filters['category_id'])) {
            $sql .= " AND f.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['food_type']) && $filters['food_type'] !== 'all') {
            $sql .= " AND f.food_type = :food_type";
            $params['food_type'] = $filters['food_type'];
        }

        if (isset($filters['is_available']) && $filters['is_available'] !== '' && $filters['is_available'] !== 'all') {
            $sql .= " AND f.is_available = :is_available";
            $params['is_available'] = (int)$filters['is_available'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (
                f.name LIKE :search1 
                OR f.description LIKE :search2 
                OR c.name LIKE :search3
            )";
            $searchVal = '%' . trim((string)$filters['search']) . '%';
            $params['search1'] = $searchVal;
            $params['search2'] = $searchVal;
            $params['search3'] = $searchVal;
        }

        $sql .= " ORDER BY c.sort_order ASC, f.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function toggleAvailability(int $foodId, int $restaurantId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE food_items 
            SET is_available = CASE WHEN is_available = 1 THEN 0 ELSE 1 END 
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute(['id' => $foodId, 'restaurant_id' => $restaurantId]);
    }

    public function createFoodItem(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO food_items (
                restaurant_id, category_id, name, slug, description, image,
                ingredients, allergens, food_type, base_price,
                calories, protein, carbs, fat, fiber, sugar, sodium, caffeine,
                serving_size, is_featured, is_popular, is_available
            ) VALUES (
                :restaurant_id, :category_id, :name, :slug, :description, :image,
                :ingredients, :allergens, :food_type, :base_price,
                :calories, :protein, :carbs, :fat, :fiber, :sugar, :sodium, :caffeine,
                :serving_size, :is_featured, :is_popular, :is_available
            )
        ");

        $stmt->execute([
            'restaurant_id' => $data['restaurant_id'],
            'category_id'   => $data['category_id'],
            'name'          => $data['name'],
            'slug'          => $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            'description'   => $data['description'] ?? null,
            'image'         => !empty($data['image']) ? $data['image'] : '/assets/images/foods/placeholder-dish.svg',
            'ingredients'   => $data['ingredients'] ?? null,
            'allergens'     => $data['allergens'] ?? null,
            'food_type'     => $data['food_type'] ?? 'vegetarian',
            'base_price'    => $data['base_price'],
            'calories'      => $data['calories'] ?? null,
            'protein'       => $data['protein'] ?? null,
            'carbs'         => $data['carbs'] ?? null,
            'fat'           => $data['fat'] ?? null,
            'fiber'         => $data['fiber'] ?? null,
            'sugar'         => $data['sugar'] ?? null,
            'sodium'        => $data['sodium'] ?? null,
            'caffeine'      => $data['caffeine'] ?? null,
            'serving_size'  => $data['serving_size'] ?? null,
            'is_featured'   => $data['is_featured'] ?? 0,
            'is_popular'    => $data['is_popular'] ?? 0,
            'is_available'  => $data['is_available'] ?? 1,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function updateFoodItem(int $foodId, int $restaurantId, array $data): bool
    {
        $fields = [];
        $params = ['id' => $foodId, 'restaurant_id' => $restaurantId];

        $allowed = [
            'category_id', 'name', 'slug', 'description', 'image',
            'ingredients', 'allergens', 'food_type', 'base_price',
            'calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium', 'caffeine',
            'serving_size', 'is_featured', 'is_popular', 'is_available'
        ];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE food_items SET " . implode(', ', $fields) . " WHERE id = :id AND restaurant_id = :restaurant_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteFoodItem(int $foodId, int $restaurantId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM food_items WHERE id = :id AND restaurant_id = :restaurant_id");
        return $stmt->execute(['id' => $foodId, 'restaurant_id' => $restaurantId]);
    }

    public function countByRestaurant(int $restaurantId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM food_items WHERE restaurant_id = :restaurant_id");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        return (int)$stmt->fetchColumn();
    }
}
