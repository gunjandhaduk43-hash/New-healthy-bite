<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class RestaurantMiddleware
{
    public static function handle(): array
    {
        if (!Auth::check()) {
            Response::redirect('/owner/login');
        }

        $user = Auth::user();
        $allowedRoles = [ROLE_RESTAURANT_OWNER, ROLE_MANAGER, ROLE_STAFF, ROLE_SUPER_ADMIN];

        if (!in_array((int)($user['role_id'] ?? 0), $allowedRoles, true)) {
            http_response_code(403);
            die("Access Denied: Restaurant management permissions required.");
        }

        $restaurantId = $user['restaurant_id'] ?? null;
        if ($restaurantId === null && (int)$user['role_id'] === ROLE_SUPER_ADMIN) {
            $restaurantId = 1; // Default to first restaurant for super admin impersonation
        }

        if (!$restaurantId) {
            http_response_code(403);
            die("No restaurant associated with this user account.");
        }

        return [
            'user'          => $user,
            'restaurant_id' => (int)$restaurantId,
        ];
    }
}
