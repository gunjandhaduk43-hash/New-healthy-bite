<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\FoodRepository;

class CartService
{
    private FoodRepository $foodRepo;
    private PricingService $pricingService;
    private NutritionService $nutritionService;

    public function __construct()
    {
        $this->foodRepo = new FoodRepository();
        $this->pricingService = new PricingService();
        $this->nutritionService = new NutritionService();
    }

    /**
     * Validates and recalculates cart items against database records.
     * Throws \InvalidArgumentException on invalid or tampered data.
     */
    public function validateCartItems(array $rawCartItems, int $restaurantId): array
    {
        if (empty($rawCartItems)) {
            throw new \InvalidArgumentException('Cart is empty.');
        }

        $validatedItems = [];
        $lineTotals = [];
        $itemsNutrition = [];

        foreach ($rawCartItems as $index => $item) {
            $foodId = (int)($item['food_id'] ?? 0);
            $quantityInput = $item['quantity'] ?? 1;
            if (!is_scalar($quantityInput) || filter_var($quantityInput, FILTER_VALIDATE_INT) === false) {
                throw new \InvalidArgumentException('Item quantity must be a positive whole number.');
            }
            $quantity = (int)$quantityInput;
            if ($quantity < 1 || $quantity > 2147483647) {
                throw new \InvalidArgumentException('Item quantity must be a positive whole number within the supported range.');
            }

            // 1. Validate food item
            $food = $this->foodRepo->getByIdAndRestaurant($foodId, $restaurantId);
            if (!$food || !(bool)$food['is_available']) {
                throw new \InvalidArgumentException("Food item #{$foodId} is unavailable or does not belong to this restaurant.");
            }

            // 2. Validate variant if selected
            $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;
            $selectedVariant = null;

            if ($variantId !== null) {
                $availableVariants = $this->foodRepo->getVariants($foodId);
                foreach ($availableVariants as $v) {
                    if ((int)$v['id'] === $variantId) {
                        $selectedVariant = $v;
                        break;
                    }
                }

                if (!$selectedVariant) {
                    throw new \InvalidArgumentException("Selected variant #{$variantId} does not belong to {$food['name']}.");
                }
            }

            // 3. Validate customizations
            $customizationsInput = is_array($item['customizations'] ?? null) ? $item['customizations'] : [];
            $availableCustomizations = $this->foodRepo->getCustomizations($foodId);
            $availableCustomMap = [];
            foreach ($availableCustomizations as $c) {
                $availableCustomMap[(int)$c['id']] = $c;
            }

            $validatedCustomizations = [];
            $selectedCustomizationIds = [];
            $selectedByGroup = [];
            foreach ($customizationsInput as $cIn) {
                if (!is_array($cIn)) {
                    throw new \InvalidArgumentException('Cart contains an invalid customization.');
                }
                $cId = (int)($cIn['id'] ?? 0);
                $cQtyInput = $cIn['quantity'] ?? 1;
                if (!is_scalar($cQtyInput) || filter_var($cQtyInput, FILTER_VALIDATE_INT) === false || (int)$cQtyInput < 1) {
                    throw new \InvalidArgumentException('Customization quantity must be a positive whole number.');
                }
                $cQty = (int)$cQtyInput;

                if (!isset($availableCustomMap[$cId])) {
                    throw new \InvalidArgumentException("Customization #{$cId} does not belong to {$food['name']}.");
                }

                $cDef = $availableCustomMap[$cId];
                if (!(bool)$cDef['is_available']) {
                    throw new \InvalidArgumentException("Customization '{$cDef['name']}' is currently unavailable.");
                }

                if ($cQty > (int)$cDef['max_quantity']) {
                    throw new \InvalidArgumentException("Customization '{$cDef['name']}' exceeds max allowed quantity of {$cDef['max_quantity']}.");
                }

                if (isset($selectedCustomizationIds[$cId])) {
                    throw new \InvalidArgumentException("Customization '{$cDef['name']}' was selected more than once.");
                }
                $selectedCustomizationIds[$cId] = true;
                $groupName = (string)$cDef['group_name'];
                $selectedByGroup[$groupName] = ($selectedByGroup[$groupName] ?? 0) + $cQty;

                $cDef['quantity'] = $cQty;
                $validatedCustomizations[] = $cDef;
            }

            $requiredByGroup = [];
            foreach ($availableCustomizations as $customization) {
                $groupName = (string)$customization['group_name'];
                if ((bool)$customization['is_required'] || (int)$customization['min_quantity'] > 0) {
                    $requiredByGroup[$groupName] = max(
                        $requiredByGroup[$groupName] ?? 0,
                        (int)$customization['min_quantity'],
                        (bool)$customization['is_required'] ? 1 : 0
                    );
                }
            }
            foreach ($requiredByGroup as $groupName => $minQuantity) {
                if (($selectedByGroup[$groupName] ?? 0) < $minQuantity) {
                    throw new \InvalidArgumentException("Select at least {$minQuantity} option(s) from '{$groupName}'.");
                }
            }

            // 4. Calculate verified price
            $unitPrice = $this->pricingService->calculateSingleItemPrice(
                (float)$food['base_price'],
                $selectedVariant,
                $validatedCustomizations
            );

            $lineTotal = $this->pricingService->calculateLineTotal($unitPrice, $quantity);
            $lineTotals[] = $lineTotal;

            // 5. Calculate verified nutrition
            $itemNutrition = $this->nutritionService->calculateItemNutrition(
                $food,
                $selectedVariant,
                $validatedCustomizations
            );
            $scaledNutrition = $this->nutritionService->scaleNutritionForQuantity($itemNutrition, $quantity);
            $itemsNutrition[] = $scaledNutrition;

            $validatedItems[] = [
                'food'           => $food,
                'variant'        => $selectedVariant,
                'customizations' => $validatedCustomizations,
                'quantity'       => $quantity,
                'unit_price'     => $unitPrice,
                'line_total'     => $lineTotal,
                'nutrition'      => $itemNutrition,
                'scaled_nutrition' => $scaledNutrition,
            ];
        }

        $orderTotals = $this->pricingService->calculateOrderTotals(
            $lineTotals,
            (float)config('app.tax_rate', 0.05),
            (float)config('app.service_charge', 0.00)
        );

        $totalNutrition = $this->nutritionService->aggregateCartNutrition($itemsNutrition);

        return [
            'items'           => $validatedItems,
            'pricing'         => $orderTotals,
            'total_nutrition' => $totalNutrition,
        ];
    }
}
