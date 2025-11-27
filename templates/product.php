<?php
/**
 * WP Product Filter Items Template
 *
 * @package WP_Product_Filter
 */

use WPProductFilter\Loader;

$product = $args['product'] ?? null;
$product_id = $args['product_id'] ?? 0;
if (empty($product) || 0 === $product_id) {
    return;
}

$name = $product['name'] ?? __('Duck');

$first_combination = null;
foreach ($product['combinations'] as $combination) {
    if ($combination['stock'] > 0 || $combination['available'] === true) {
        $first_combination = $combination;
        break;
    }
}

if (empty($first_combination)) {
    return;
}

$image = $first_combination['image'] ?: '';
$price = $first_combination['price'] ?? 0;
$stock = $first_combination['stock'] ?? 0;
?>
<div class="wp-product-filter__product" data-id="<?php echo esc_attr($product_id); ?>">
    <div class="wp-product-filter__product-header">
        <div class="wp-product-filter__product-name"><?php echo esc_html($name); ?></div>
        <div class="wp-product-filter__product-stock">
            <?php _e('In Stock:'); ?><?php echo esc_html($stock); ?>
        </div>
        <button class="wp-product-filter__product-price"
                data-price="<?php echo esc_attr($price); ?>"><?php echo esc_html($price); ?><?php _e('RUB'); ?></button>
    </div>
    <div class="wp-product-filter__product-body">
        <div class="wp-product-filter__product-image">
            <img src="<?php echo esc_url($image); ?>"
                 alt="<?php echo esc_attr($name); ?>"/>
        </div>

        <?php Loader::load_template('filter', true, array('product' => $product)); ?>
    </div>
</div>
