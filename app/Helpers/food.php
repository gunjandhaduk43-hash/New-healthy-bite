<?php
declare(strict_types=1);

function foodTypeBadge(string $type): string
{
    return match (strtolower($type)) {
        'vegetarian'     => '<span class="diet-badge badge-veg"><i class="bi bi-circle-fill"></i> Veg</span>',
        'non_vegetarian' => '<span class="diet-badge badge-nonveg"><i class="bi bi-triangle-fill"></i> Non-Veg</span>',
        'vegan'          => '<span class="diet-badge badge-vegan"><i class="bi bi-flower1"></i> Vegan</span>',
        'jain'           => '<span class="diet-badge badge-jain"><i class="bi bi-shield-check"></i> Jain</span>',
        default          => '<span class="diet-badge badge-other">' . e($type) . '</span>',
    };
}

function foodImageUrl(?string $image): string
{
    if (empty($image)) {
        return asset('images/foods/placeholder-dish.svg');
    }
    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
        return $image;
    }
    if (str_starts_with($image, '/assets/')) {
        return $image;
    }
    if (str_starts_with($image, 'assets/')) {
        return '/' . $image;
    }
    return asset('images/foods/' . ltrim($image, '/'));
}

