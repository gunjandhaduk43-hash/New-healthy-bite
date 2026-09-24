<?php
/**
 * HEALTHY BITE — Restaurant Information Banner
 * Clean, compact, operational hierarchy (Prompt Requirements #13 & #14)
 */
?>
<section class="restaurant-hero-bar" aria-label="Restaurant Information">
    <div class="hero-bar-inner">
        <div class="restaurant-main-meta">
            <div class="restaurant-headline-row">
                <h1 class="restaurant-title"><?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?></h1>
                <span class="status-live-indicator">
                    <span class="live-dot"></span> Open Now
                </span>
            </div>
            
            <div class="restaurant-attributes-row">
                <span class="attribute-item rating-pill">
                    <i class="bi bi-star-fill"></i> 4.8 <span class="rating-count">(120+ reviews)</span>
                </span>
                <span class="attribute-sep">•</span>
                <span class="attribute-item">
                    <i class="bi bi-geo-alt-fill"></i> <?= e($branch['name'] ?? 'Indiranagar') ?>, <?= e($restaurant['city'] ?? 'Bengaluru') ?>
                </span>
                <span class="attribute-sep">•</span>
                <span class="attribute-item macro-guarantee">
                    <i class="bi bi-shield-check"></i> 100% Macro Accurate
                </span>
            </div>
        </div>

        <div class="restaurant-cuisine-tags">
            <span class="cuisine-tag"><i class="bi bi-flower1"></i> Organic Bowls</span>
            <span class="cuisine-tag"><i class="bi bi-fire"></i> High Protein</span>
            <span class="cuisine-tag"><i class="bi bi-patch-check"></i> Clean Ingredients</span>
        </div>
    </div>
</section>
