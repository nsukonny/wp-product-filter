<?php
/**
 * WP Product Filter Template
 *
 * @package WP_Product_Filter
 */
?>
<div class="wp-product-filter__items">
    <form method="post">
        <?php wp_nonce_field('product_variants_nonce'); ?>
        
        <div class="product-variants__colors">
            <button class="color-btn" data-color="red" style="background:red;"></button>
            <button class="color-btn" data-color="yellow" style="background:yellow;"></button>
            <button class="color-btn" data-color="green" style="background:green;"></button>
        </div>

        <div class="product-variants__sizes">
            <button class="size-btn" data-size="S">S</button>
            <button class="size-btn" data-size="M">M</button>
            <button class="size-btn" data-size="L">L</button>
        </div>

        <div class="product-variants__info">
            <div class="product-price"><span class="js-price"></span> ₽</div>
            <div class="product-stock js-stock"></div>
            <div class="product-message js-message"></div>
        </div>

        <div class="product-image">
            <img class="js-image" src="" alt="">
        </div>
    </form>
</div>
