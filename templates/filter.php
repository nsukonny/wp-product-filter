<?php
/**
 * WP Product Filter Template
 *
 * @package WP_Product_Filter
 *
 */

use WPProductFilter\Product;

$product = $args['product'] ?? null;
if (empty($product)) {
    return;
}

$compatibility = $product['compatibility'] ?? array();
if (empty($compatibility)) {
    return;
}

$colors = array(
        'red' => '#ff0000c4',
        'yellow' => '#ffff00ad',
        'green' => '#008000c9',
        'blue' => '#1b7ab7c9',
);
$sizes = array('S', 'M', 'L');
$default_color = array();
?>
<div class="product-filter">
    <div class="product-filter__colors">
        <span class="product-filter-title"><?php _e('Pick a colour'); ?></span>
        <?php
        foreach ($colors as $color => $fill) {
            if (empty($default_color) && !empty($compatibility[$color])) {
                $default_color = $color;
            }

            $is_disabled = Product::is_disabled($product, $color);

            $selected_class = empty($selected_class) && !$is_disabled && (!empty($default_color) && $color === $default_color) ? 'selected' : '';
            ?>
            <button
                    class="product-filter__colors__color-btn <?php echo esc_attr($selected_class); ?>"
                    data-type="color"
                    data-color="<?php echo esc_attr($color); ?>"
                    <?php if ($is_disabled){ ?>disabled="disabled"<?php } ?>
                    style="background:<?php echo esc_attr($fill); ?>;"></button>
        <?php } ?>
    </div>

    <div class="product-filter__sizes">
        <span class="product-filter-title"><?php _e('Pick a size'); ?></span>
        <?php
        $selected_class = '';
        foreach ($sizes as $size) {
            $is_disabled = Product::is_disabled($product, $default_color, $size);
            $selected_class = empty($selected_class) && !$is_disabled && in_array($size, $compatibility[$default_color]) ? 'selected' : '';
            ?>
            <button
                    class="product-filter__sizes__size-btn <?php echo esc_attr($selected_class); ?>"
                    data-type="size"
                    data-size="<?php echo esc_attr($size); ?>"
                    <?php if ($is_disabled){ ?>disabled="disabled"<?php } ?>
            ><?php echo esc_attr($size); ?></button>
        <?php } ?>
    </div>
</div>
