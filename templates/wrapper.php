<?php
/**
 * WP Product Filter Wrapper Template
 *
 * @package WP_Product_Filter
 */

use WPProductFilter\Loader;

?>
<div id="wp-product-filter" class="wp-product-filter">
    <?php
    Loader::load_template('filter', true);
    Loader::load_template('items', true);
    ?>
</div>