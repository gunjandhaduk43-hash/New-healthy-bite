<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class QrService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Validates a QR token and loads the verified restaurant, branch, and table context.
     * Returns null if token is invalid, inactive, or expired.
     */
    public function resolveToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                qr.id AS qr_token_id, qr.token, qr.status AS qr_status, qr.expires_at,
                t.id AS table_id, t.table_number, t.status AS table_status,
                b.id AS branch_id, b.name AS branch_name, b.address AS branch_address,
                r.id AS restaurant_id, r.name AS restaurant_name, r.slug AS restaurant_slug, r.status AS restaurant_status
            FROM qr_tokens qr
            JOIN restaurant_tables t ON qr.table_id = t.id
            JOIN branches b ON t.branch_id = b.id
            JOIN restaurants r ON b.restaurant_id = r.id
            WHERE qr.token = :token
              AND qr.status = 'active'
              AND (qr.expires_at IS NULL OR qr.expires_at > NOW())
              AND r.status = 'approved'
              AND b.status = 'active'
            LIMIT 1
        ");

        $stmt->execute(['token' => trim($token)]);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
