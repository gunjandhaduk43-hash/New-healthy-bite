<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CategoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByRestaurantId(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, restaurant_id, name, slug, description, image, sort_order
            FROM categories
            WHERE restaurant_id = :restaurant_id AND status = 'active'
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute(['restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function findById(int $categoryId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, restaurant_id, name, slug, description, image, sort_order
            FROM categories
            WHERE id = :id AND restaurant_id = :restaurant_id AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute(['id' => $categoryId, 'restaurant_id' => $restaurantId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function createCategory(int $restaurantId, array $data): int
    {
        $name = trim((string)($data['name'] ?? ''));
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if ($slug === '') {
            $slug = 'category-' . substr(bin2hex(random_bytes(3)), 0, 4);
        }

        // Check if slug exists in this restaurant and append suffix if needed
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE restaurant_id = :restaurant_id AND slug = :slug");
        $stmtCheck->execute(['restaurant_id' => $restaurantId, 'slug' => $slug]);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        $stmtSort = $this->db->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM categories WHERE restaurant_id = :restaurant_id");
        $stmtSort->execute(['restaurant_id' => $restaurantId]);
        $nextSort = (int)$stmtSort->fetchColumn();

        $stmt = $this->db->prepare("
            INSERT INTO categories (restaurant_id, name, slug, description, image, sort_order, status)
            VALUES (:restaurant_id, :name, :slug, :description, :image, :sort_order, 'active')
        ");
        $stmt->execute([
            'restaurant_id' => $restaurantId,
            'name'          => $name,
            'slug'          => $slug,
            'description'   => $data['description'] ?? null,
            'image'         => $data['image'] ?? '/assets/images/foods/placeholder-dish.svg',
            'sort_order'    => $data['sort_order'] ?? $nextSort,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function deleteCategory(int $categoryId, int $restaurantId): bool
    {
        // Check if there are active food items in this category
        $stmtFoods = $this->db->prepare("
            SELECT COUNT(*) FROM food_items 
            WHERE category_id = :category_id AND restaurant_id = :restaurant_id AND is_available = 1
        ");
        $stmtFoods->execute(['category_id' => $categoryId, 'restaurant_id' => $restaurantId]);
        $foodCount = (int)$stmtFoods->fetchColumn();

        if ($foodCount > 0) {
            // Cannot delete category that has active food items
            throw new \InvalidArgumentException("Cannot delete category with {$foodCount} active food items. Reassign or remove the items first.");
        }

        $stmt = $this->db->prepare("
            UPDATE categories 
            SET status = 'inactive' 
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute(['id' => $categoryId, 'restaurant_id' => $restaurantId]);
    }
}

