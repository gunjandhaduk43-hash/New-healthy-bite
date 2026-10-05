<?php
declare(strict_types=1);

function formatCurrency(float|int|string|null $amount): string
{
    return '₹' . number_format((float)($amount ?? 0), 2);
}

function format_price(float|int|string|null $amount): string
{
    return formatCurrency($amount);
}

function formatNutrition(?float $value, string $unit = 'g'): ?string
{
    if ($value === null) {
        return null;
    }
    $formatted = (float)$value == (int)$value ? (string)(int)$value : number_format($value, 1);
    return $formatted . $unit;
}

