<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function setStatusCode(int $code): void
    {
        if (!headers_sent()) {
            http_response_code($code);
        }
    }

    public static function setHeader(string $name, string $value): void
    {
        if (!headers_sent()) {
            header("{$name}: {$value}");
        }
    }

    public static function json(array $data, int $statusCode = 200): void
    {
        self::setStatusCode($statusCode);
        self::setHeader('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $url, int $statusCode = 302): void
    {
        self::setStatusCode($statusCode);
        self::setHeader('Location', $url);
        exit;
    }
}
