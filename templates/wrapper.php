<?php
/**
 * WP Product Filter Wrapper Template
 *
 * @var $args
 *
 * @package WP_Product_Filter
 */

use WPProductFilter\Loader;

?>
<div id="wp-product-filter" class="wp-product-filter">
    <?php Loader::load_template('products', true, $args); ?>
</div>