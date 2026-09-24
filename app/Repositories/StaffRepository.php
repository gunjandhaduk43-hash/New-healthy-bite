<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class StaffRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, u.role_id, u.name, u.email, u.status, u.created_at,
                r.name AS role_name, r.slug AS role_slug
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.restaurant_id = :restaurant_id
            ORDER BY u.id ASC
        ");
        $stmt->execute([':restaurant_id' => $restaurantId]);
        return $stmt->fetchAll();
    }

    public function createStaff(int $restaurantId, string $name, string $email, int $roleId, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO users (role_id, restaurant_id, name, email, password, status)
            VALUES (:role_id, :restaurant_id, :name, :email, :password, 'active')
        ");
        $stmt->execute([
            ':role_id'       => $roleId,
            ':restaurant_id' => $restaurantId,
            ':name'          => $name,
            ':email'         => $email,
            ':password'      => $hash
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function toggleStatus(int $userId, int $restaurantId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET status = IF(status = 'active', 'inactive', 'active')
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([':id' => $userId, ':restaurant_id' => $restaurantId]);
    }

    public function findById(int $userId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, u.role_id, u.status, r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id AND u.restaurant_id = :restaurant_id
            LIMIT 1
        ");
        $stmt->execute([':id' => $userId, ':restaurant_id' => $restaurantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStaff(int $userId, int $restaurantId, string $name, int $roleId, ?string $password = null): bool
    {
        if ($password !== null && trim($password) !== '') {
            $hash = password_hash(trim($password), PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("
                UPDATE users 
                SET name = :name, role_id = :role_id, password = :password
                WHERE id = :id AND restaurant_id = :restaurant_id
            ");
            return $stmt->execute([
                ':id'            => $userId,
                ':restaurant_id' => $restaurantId,
                ':name'          => $name,
                ':role_id'       => $roleId,
                ':password'      => $hash
            ]);
        }

        $stmt = $this->db->prepare("
            UPDATE users 
            SET name = :name, role_id = :role_id
            WHERE id = :id AND restaurant_id = :restaurant_id
        ");
        return $stmt->execute([
            ':id'            => $userId,
            ':restaurant_id' => $restaurantId,
            ':name'          => $name,
            ':role_id'       => $roleId
        ]);
    }
}

