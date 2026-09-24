<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    private const USER_SESSION_KEY = '_auth_user';

    public static function check(): bool
    {
        Session::start();
        return Session::has(self::USER_SESSION_KEY);
    }

    public static function user(): ?array
    {
        Session::start();
        return Session::get(self::USER_SESSION_KEY);
    }

    public static function id(): ?int
    {
        $user = self::user();
        return isset($user['id']) ? (int)$user['id'] : null;
    }

    public static function login(array $user): void
    {
        Session::start();
        Session::regenerate();
        Session::set(self::USER_SESSION_KEY, [
            'id'            => $user['id'],
            'role_id'       => $user['role_id'],
            'restaurant_id' => $user['restaurant_id'] ?? null,
            'name'          => $user['name'],
            'email'         => $user['email'],
        ]);
    }

    public static function logout(): void
    {
        Session::start();
        Session::remove(self::USER_SESSION_KEY);
        Session::regenerate();
    }
}
