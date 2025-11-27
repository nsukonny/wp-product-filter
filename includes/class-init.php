<?php
/**
 * Init all plugin
 *
 * @since 1.0.0
 *
 * @package WPProductFilter
 *
 * @domain  wp-product-filter
 */

namespace WPProductFilter;

use Singleton;

defined('ABSPATH') || exit;

class Init
{

    use Singleton;

    /**
     * Init core of the plugin
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function init(): void
    {
        add_action('init_wp_product_filter', [Shortcodes::class, 'instance']);
        add_action('init_wp_product_filter', [Ajax::class, 'instance']);

        do_action('init_wp_product_filter');
    }
}
