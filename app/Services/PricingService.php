<?php
declare(strict_types=1);

namespace App\Services;

class PricingService
{
    /**
     * Calculate single configured item price:
     * base_price + variant_adjustment + SUM(customization_adjustment * customization_quantity)
     */
    public function calculateSingleItemPrice(float $basePrice, ?array $variant = null, array $customizations = []): float
    {
        $price = $basePrice;

        if ($variant && isset($variant['price_adjustment'])) {
            $price += (float)$variant['price_adjustment'];
        }

        foreach ($customizations as $custom) {
            $adjustment = (float)($custom['price_adjustment'] ?? 0.00);
            $qty = (int)($custom['quantity'] ?? 1);
            $price += ($adjustment * $qty);
        }

        return round($price, 2);
    }

    /**
     * Calculate item line total:
     * single_item_price * food_quantity
     */
    public function calculateLineTotal(float $singleItemPrice, int $foodQuantity): float
    {
        return round($singleItemPrice * max(1, $foodQuantity), 2);
    }

    /**
     * Calculate full order totals:
     * subtotal + tax + service_charge
     */
    public function calculateOrderTotals(array $lineTotals, float $taxRate = 0.05, float $serviceCharge = 0.00): array
    {
        $subtotal = 0.00;
        foreach ($lineTotals as $total) {
            $subtotal += (float)$total;
        }
        $subtotal = round($subtotal, 2);

        $tax = round($subtotal * $taxRate, 2);
        $serviceCharge = round($serviceCharge, 2);
        $totalAmount = round($subtotal + $tax + $serviceCharge, 2);

        return [
            'subtotal'       => $subtotal,
            'tax'            => $tax,
            'service_charge' => $serviceCharge,
            'total_amount'   => $totalAmount,
        ];
    }
}
