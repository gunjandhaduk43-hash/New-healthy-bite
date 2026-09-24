<?php
declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    static $configs = [];
    [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

    if (!isset($configs[$file])) {
        $path = dirname(__DIR__, 2) . "/config/{$file}.php";
        $configs[$file] = file_exists($path) ? require $path : [];
    }

    return $item !== null ? ($configs[$file][$item] ?? $default) : $configs[$file];
}

function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

function asset(string $path = ''): string
{
    return '/assets/' . ltrim($path, '/');
}

function full_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    return $scheme . '://' . $host . '/' . ltrim($path, '/');
}
