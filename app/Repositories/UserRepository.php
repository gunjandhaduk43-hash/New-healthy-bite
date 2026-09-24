<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $normalized = strtolower(trim($email));
        $aliases = [
            'admin@healthybite.com'       => 'mira@healthybite.in',
            'admin@healthybite.in'        => 'mira@healthybite.in',
            'admin'                       => 'mira@healthybite.in',
            'mira@healthybite.com'        => 'mira@healthybite.in',
            'owner@greenhousekitchen.com' => 'aarav@greenhouse.in',
            'owner@greenhouse.in'         => 'aarav@greenhouse.in',
            'owner'                       => 'aarav@greenhouse.in',
            'aarav@greenhousekitchen.com' => 'aarav@greenhouse.in',
        ];

        if (isset($aliases[$normalized])) {
            $normalized = $aliases[$normalized];
        }

        $stmt = $this->db->prepare("
            SELECT 
                u.id, u.role_id, u.restaurant_id, u.name, u.email, u.password, u.status,
                r.slug AS role_slug, r.name AS role_name,
                rest.name AS restaurant_name, rest.slug AS restaurant_slug, rest.status AS restaurant_status
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN restaurants rest ON u.restaurant_id = rest.id
            WHERE LOWER(u.email) = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => $normalized]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, u.role_id, u.restaurant_id, u.name, u.email, u.status,
                r.slug AS role_slug, r.name AS role_name,
                rest.name AS restaurant_name, rest.slug AS restaurant_slug
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN restaurants rest ON u.restaurant_id = rest.id
            WHERE u.id = :id
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
                u.id, u.role_id, u.restaurant_id, u.name, u.email, u.status, u.created_at,
                r.slug AS role_slug, r.name AS role_name,
                rest.name AS restaurant_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN restaurants rest ON u.restaurant_id = rest.id
            ORDER BY u.id ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $userId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $userId]);
    }

    public function updateRole(int $userId, int $roleId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role_id = :role_id WHERE id = :id");
        return $stmt->execute(['role_id' => $roleId, 'id' => $userId]);
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }
}
