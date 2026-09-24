<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\BranchRepository;

class OrderService
{
    private OrderRepository $orderRepo;
    private RestaurantRepository $restaurantRepo;
    private BranchRepository $branchRepo;
    private CartService $cartService;
    private QrService $qrService;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->restaurantRepo = new RestaurantRepository();
        $this->branchRepo = new BranchRepository();
        $this->cartService = new CartService();
        $this->qrService = new QrService();
    }

    public function placeOrder(array $payload): array
    {
        // 1. Resolve & Lock Restaurant, Branch & Table Context
        $qrToken = trim((string)($payload['qr_token'] ?? ''));
        $restaurantId = (int)($payload['restaurant_id'] ?? config('app.default_restaurant_id', 1));
        $branchId = (int)($payload['branch_id'] ?? 1);
        $tableId = !empty($payload['table_id']) ? (int)$payload['table_id'] : null;

        if ($qrToken !== '') {
            $qrContext = $this->qrService->resolveToken($qrToken);
            if (!$qrContext) {
                throw new \InvalidArgumentException('The provided QR code token is invalid, inactive, or expired.');
            }
            // Strict server override: ignore any client values and use verified QR context
            $restaurantId = (int)$qrContext['restaurant_id'];
            $branchId = (int)$qrContext['branch_id'];
            $tableId = (int)$qrContext['table_id'];
        }

        $restaurant = $this->restaurantRepo->findById($restaurantId);
        if (!$restaurant) {
            throw new \InvalidArgumentException('Restaurant not found or inactive.');
        }

        $branch = $this->restaurantRepo->getBranch($restaurantId, $branchId);
        if (!$branch) {
            throw new \InvalidArgumentException('Restaurant branch not found.');
        }

        $orderType = in_array($payload['order_type'] ?? '', ['dine_in', 'takeaway'], true)
            ? $payload['order_type']
            : 'dine_in';

        if ($orderType === 'dine_in' && $tableId === null) {
            throw new \InvalidArgumentException('A dining table must be selected for Dine-In orders.');
        }

        // 2. Validate Customer Details
        $customerName = trim((string)($payload['customer_name'] ?? 'Guest Customer'));
        if ($customerName === '') {
            $customerName = 'Guest Customer';
        }
        $customerMobile = !empty($payload['customer_mobile']) ? trim((string)$payload['customer_mobile']) : null;
        $customerEmail = !empty($payload['customer_email']) ? trim((string)$payload['customer_email']) : null;

        $customerId = $this->orderRepo->createCustomer($customerName, $customerMobile, $customerEmail);

        // 3. Re-validate and recalculate entire cart strictly on backend
        $rawItems = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        $validatedCart = $this->cartService->validateCartItems($rawItems, $restaurantId);

        $pricing = $validatedCart['pricing'];
        $items = $validatedCart['items'];

        // 4. Generate Unique Order Number
        $orderNumber = 'HB-' . strtoupper(dechex((int)(microtime(true) * 1000))) . '-' . rand(100, 999);

        // 5. Create Order
        $orderId = $this->orderRepo->createOrder([
            'order_number'   => $orderNumber,
            'restaurant_id'  => $restaurantId,
            'branch_id'      => $branchId,
            'table_id'       => $orderType === 'dine_in' ? $tableId : null,
            'customer_id'    => $customerId,
            'order_type'     => $orderType,
            'subtotal'       => $pricing['subtotal'],
            'tax'            => $pricing['tax'],
            'service_charge' => $pricing['service_charge'],
            'total_amount'   => $pricing['total_amount'],
            'payment_status' => 'pending',
            'order_status'   => 'placed',
            'notes'          => !empty($payload['notes']) ? trim((string)$payload['notes']) : null,
        ]);

        // 6. Freeze Items and Customization Snapshots
        foreach ($items as $item) {
            $food = $item['food'];
            $variant = $item['variant'];
            $nutrition = $item['nutrition'];

            $orderItemId = $this->orderRepo->addOrderItem([
                'order_id'               => $orderId,
                'food_item_id'           => $food['id'],
                'food_name_snapshot'     => $food['name'],
                'base_price_snapshot'    => $food['base_price'],
                'variant_name_snapshot'  => $variant ? $variant['name'] : null,
                'variant_price_snapshot' => $variant ? $variant['price_adjustment'] : 0.00,
                'quantity'               => $item['quantity'],
                'unit_price'             => $item['unit_price'],
                'total_price'            => $item['line_total'],
                'calories'               => $nutrition['calories'] ?? null,
                'protein'                => $nutrition['protein'] ?? null,
                'carbs'                  => $nutrition['carbs'] ?? null,
                'fat'                    => $nutrition['fat'] ?? null,
                'fiber'                  => $nutrition['fiber'] ?? null,
                'sugar'                  => $nutrition['sugar'] ?? null,
                'sodium'                 => $nutrition['sodium'] ?? null,
                'caffeine'               => $nutrition['caffeine'] ?? null,
            ]);

            foreach ($item['customizations'] as $custom) {
                $this->orderRepo->addOrderItemCustomization([
                    'order_item_id'               => $orderItemId,
                    'customization_id'            => $custom['id'],
                    'customization_name_snapshot' => $custom['name'],
                    'quantity'                    => $custom['quantity'],
                    'price_adjustment'            => $custom['price_adjustment'],
                    'calories_adjustment'         => $custom['calories_adjustment'] ?? null,
                    'protein_adjustment'          => $custom['protein_adjustment'] ?? null,
                    'carbs_adjustment'            => $custom['carbs_adjustment'] ?? null,
                    'fat_adjustment'              => $custom['fat_adjustment'] ?? null,
                    'fiber_adjustment'            => $custom['fiber_adjustment'] ?? null,
                    'sugar_adjustment'            => $custom['sugar_adjustment'] ?? null,
                    'sodium_adjustment'           => $custom['sodium_adjustment'] ?? null,
                    'caffeine_adjustment'         => $custom['caffeine_adjustment'] ?? null,
                ]);
            }
        }

        return $this->orderRepo->findByOrderNumber($orderNumber);
    }

    public function trackOrder(string $orderNumber): ?array
    {
        return $this->orderRepo->findByOrderNumber(trim($orderNumber));
    }
}
