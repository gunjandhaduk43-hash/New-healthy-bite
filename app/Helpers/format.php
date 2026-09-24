<?php
declare(strict_types=1);

function formatCurrency(float $amount): string
{
    return '₹' . number_format($amount, 2);
}

function formatNutrition(?float $value, string $unit = 'g'): ?string
{
    if ($value === null) {
        return null;
    }
    $formatted = (float)$value == (int)$value ? (string)(int)$value : number_format($value, 1);
    return $formatted . $unit;
}
