<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    private const FLASH_KEY = '_flash_messages';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');

            session_start();
        }

        self::ageFlash();
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $key, mixed $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'value' => $message,
            'remove' => false
        ];
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? $default;
    }

    private static function ageFlash(): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            return;
        }

        foreach ($_SESSION[self::FLASH_KEY] as $key => $message) {
            if ($message['remove'] === true) {
                unset($_SESSION[self::FLASH_KEY][$key]);
            } else {
                $_SESSION[self::FLASH_KEY][$key]['remove'] = true;
            }
        }
    }

    public static function regenerate(): void
    {
        if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
