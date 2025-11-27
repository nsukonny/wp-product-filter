<?php
/**
 * WP Product Filter Items Template
 *
 * @package WP_Product_Filter
 */

use WPProductFilter\Loader;

$products = $args['products'] ?? array();
if (empty($products) || !is_array($products)) {
    return;
}
?>
<div class="wp-product-filter__items">
    <?php
    foreach ($products as $product_id => $product) {
        Loader::load_template(
                'product',
                true,
                array(
                        'product_id' => $product_id,
                        'product' => $product,
                )
        );
    }
    ?>
</div>