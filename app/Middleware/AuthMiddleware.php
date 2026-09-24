<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class AuthMiddleware
{
    public static function handle(?int $requiredRole = null): void
    {
        if (!Auth::check()) {
            Response::redirect('/owner/login');
        }

        if ($requiredRole !== null) {
            $user = Auth::user();
            if ((int)($user['role_id'] ?? 0) !== $requiredRole) {
                http_response_code(403);
                die("Access Denied: Insufficient permissions.");
            }
        }
    }
}
