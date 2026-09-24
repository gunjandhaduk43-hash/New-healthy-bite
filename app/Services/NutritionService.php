<?php
declare(strict_types=1);

namespace App\Services;

class NutritionService
{
    private const FIELDS = [
        'calories', 'protein', 'carbs', 'fat',
        'fiber', 'sugar', 'sodium', 'caffeine'
    ];

    /**
     * Calculate item nutrition snapshot:
     * base nutrition + variant adjustment + SUM(customization adjustment * customization_qty)
     * Keeps NULL if data was never provided.
     */
    public function calculateItemNutrition(array $baseFood, ?array $variant = null, array $customizations = []): array
    {
        $calculated = [];

        foreach (self::FIELDS as $field) {
            $baseVal = isset($baseFood[$field]) && $baseFood[$field] !== null ? (float)$baseFood[$field] : null;
            $adjKey = $field . '_adjustment';

            $total = $baseVal;

            if ($variant && isset($variant[$adjKey]) && $variant[$adjKey] !== null) {
                $total = ($total ?? 0.0) + (float)$variant[$adjKey];
            }

            foreach ($customizations as $custom) {
                if (isset($custom[$adjKey]) && $custom[$adjKey] !== null) {
                    $qty = (int)($custom['quantity'] ?? 1);
                    $total = ($total ?? 0.0) + ((float)$custom[$adjKey] * $qty);
                }
            }

            if ($total !== null) {
                $calculated[$field] = $field === 'calories' ? (int)round($total) : round($total, 2);
            } else {
                $calculated[$field] = null;
            }
        }

        return $calculated;
    }

    /**
     * Multiply item nutrition by food quantity
     */
    public function scaleNutritionForQuantity(array $nutrition, int $quantity): array
    {
        $scaled = [];
        $qty = max(1, $quantity);

        foreach (self::FIELDS as $field) {
            if (isset($nutrition[$field]) && $nutrition[$field] !== null) {
                $val = (float)$nutrition[$field] * $qty;
                $scaled[$field] = $field === 'calories' ? (int)round($val) : round($val, 2);
            } else {
                $scaled[$field] = null;
            }
        }

        return $scaled;
    }

    /**
     * Aggregate total nutrition across multiple order items
     */
    public function aggregateCartNutrition(array $itemsNutrition): array
    {
        $totals = [];

        foreach (self::FIELDS as $field) {
            $sum = null;
            foreach ($itemsNutrition as $itemNutr) {
                if (isset($itemNutr[$field]) && $itemNutr[$field] !== null) {
                    $sum = ($sum ?? 0.0) + (float)$itemNutr[$field];
                }
            }

            if ($sum !== null) {
                $totals[$field] = $field === 'calories' ? (int)round($sum) : round($sum, 2);
            } else {
                $totals[$field] = null;
            }
        }

        return $totals;
    }
}
