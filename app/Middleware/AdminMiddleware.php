<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class AdminMiddleware
{
    public static function handle(): array
    {
        if (!Auth::check()) {
            Response::redirect('/admin/login');
        }

        $user = Auth::user();
        if ((int)($user['role_id'] ?? 0) !== ROLE_SUPER_ADMIN) {
            http_response_code(403);
            die("Access Denied: Platform Administrator privileges required.");
        }

        return $user;
    }
}
