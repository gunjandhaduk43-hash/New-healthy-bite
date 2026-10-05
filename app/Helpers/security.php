<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    return \App\Core\Csrf::getToken();
}

function csrf_field(): string
{
    return \App\Core\Csrf::field();
}

function sanitize(?string $value): string
{
    return trim(strip_tags((string)($value ?? '')));
}

